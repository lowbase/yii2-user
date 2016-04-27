<?php
/**
 * @package   yii2-user
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */

namespace lowbase\user\components;

use lowbase\user\models\UserOauthKey;
use yii\authclient\widgets\AuthChoice;
use yii\base\InvalidConfigException;
use Yii;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\authclient\ClientInterface;
use yii\authclient\widgets\AuthChoiceAsset;

/**
 * Панель прикрепления / открепления
 * ключей авторизации через соц. сети
 * Используется стандартный код класса.
 * Изменения касаются лишь тегов оформления
 * отображения
 *
 * Class AuthKeysManager
 * @package lowbase\user\components
 */
class AuthKeysManager extends AuthChoice
{
    public $options = [
        'class' => 'auth-clients row' // добавили класс row
    ];

    /**
     * Меняем ссылки на добавление и удаление ключей
     * @param ClientInterface $client external auth client instance.
     * @param string $text link text, if not set - default value will be generated.
     * @param array $htmlOptions link HTML options.
     * @throws InvalidConfigException on wrong configuration.
     */
    public function clientLink($client, $text = null, array $htmlOptions = [])
    {
        echo Html::beginTag('div', ['class' => 'col-xs-4']);
        $exists = UserOauthKey::findOne(['user_id' => Yii::$app->user->id, 'provider_id' => UserOauthKey::getAvailableClients()[$client->getId()]]);
        if ($exists) {
            $button = Html::a('<span class="glyphicon glyphicon-trash" aria-hidden="true"></span> <span class="hidden-xs">'.Yii::t('user', 'Удалить').'</span>', Url::toRoute(['auth/unbind', 'id' => $client->getId()]), ['class' => 'btn btn-danger btn-sm', 'onclick' => '$(this).off("click"); return true;']);
        } else {
            $viewOptions = $client->getViewOptions();
            if (isset($viewOptions['popupWidth'])) {
                $htmlOptions['data-popup-width'] = $viewOptions['popupWidth'];
            }
            if (isset($viewOptions['popupHeight'])) {
                $htmlOptions['data-popup-height'] = $viewOptions['popupHeight'];
            }

            $htmlOptions['class'] = 'btn btn-success btn-sm';
            $button = Html::a('<span class="glyphicon glyphicon-plus" aria-hidden="true"></span> <span class="hidden-xs">'.Yii::t('user', 'Добавить').'</span>', $this->createClientUrl($client), $htmlOptions);
        }
        echo Html::tag('span', $button, ['class' => 'auth-icon ' . $client->getName(), 'style' => 'padding-left: 40px; margin-bottom: 10px;']);
        echo Html::endTag('div');
    }

    /**
     * Меняем ul на div
     */
    protected function renderMainContent()
    {
        echo Html::beginTag('div', ['class' => '']);
        foreach ($this->getClients() as $externalService) {
            $this->clientLink($externalService);
        }
        echo Html::endTag('div');
    }
}
