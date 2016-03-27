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

class AuthKeysManager extends AuthChoice
{
    public $options = [
        'class' => 'auth-clients row'
    ];

    /**
     * Outputs client auth link.
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
            $button = Html::a('<span class="glyphicon glyphicon-trash" aria-hidden="true"></span> <span class="hidden-xs">Удалить</span>', Url::toRoute(['auth/unbind', 'id' => $client->getId()]), ['class' => 'btn btn-danger btn-sm', 'onclick' => '$(this).off("click"); return true;']);
        } else {
            $viewOptions = $client->getViewOptions();
            if (isset($viewOptions['popupWidth'])) {
                $htmlOptions['data-popup-width'] = $viewOptions['popupWidth'];
            }
            if (isset($viewOptions['popupHeight'])) {
                $htmlOptions['data-popup-height'] = $viewOptions['popupHeight'];
            }

            $htmlOptions['class'] = 'btn btn-success btn-sm';
            $button = Html::a('<span class="glyphicon glyphicon-plus" aria-hidden="true"></span> <span class="hidden-xs">Добавить</span>', $this->createClientUrl($client), $htmlOptions);
        }
        echo Html::tag('span', $button, ['class' => 'auth-icon ' . $client->getName(), 'style' => 'padding-left: 40px; margin-bottom: 10px;']);
        echo Html::endTag('div');
    }

    /**
     * Composes client auth URL.
     * @param ClientInterface $provider external auth client instance.
     * @return string auth URL.
     */
    public function createClientUrl($provider)
    {
        $this->autoRender = false;
        $url = $this->getBaseAuthUrl();
        $url[$this->clientIdGetParamName] = $provider->getId();

        return Url::to($url);
    }

    /**
     * Renders the main content, which includes all external services links.
     */
    protected function renderMainContent()
    {
        echo Html::beginTag('div', ['class' => '']);
        foreach ($this->getClients() as $externalService) {
            $this->clientLink($externalService);
        }
        echo Html::endTag('div');
    }

    /**
     * Initializes the widget.
     */
    public function init()
    {
        $view = Yii::$app->getView();
        if ($this->popupMode) {
            AuthChoiceAsset::register($view);
            $view->registerJs("\$('#" . $this->getId() . "').authchoice();");
        } else {
            AuthChoiceStyleAsset::register($view);
        }
        $this->options['id'] = $this->getId();
        echo Html::beginTag('div', $this->options);
    }

    /**
     * Runs the widget.
     */
    public function run()
    {
        if ($this->autoRender) {
            $this->renderMainContent();
        }
        echo Html::endTag('div');
    }
}
