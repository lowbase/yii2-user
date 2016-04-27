<?php
/**
 * @package   yii2-user
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */

namespace lowbase\user\components\oauth;

use lowbase\user\models\User;
use lowbase\user\models\UserOauthKey;

/**
 * Авторизация с помощью Вконтакте
 * Class VKontakte
 * @package lowbase\user\components\oauth
 */
class VKontakte extends \yii\authclient\clients\VKontakte
{
    /**
     * Размеры Popap-окна
     * @return array
     */
    public function getViewOptions()
    {
        return [
            'popupWidth' => 900,
            'popupHeight' => 500
        ];
    }

    /**
     * Преобразование пола
     * @return array
     */
    public function normalizeSex()
    {
        return [
            '1' => User::SEX_FEMALE,
            '2' => User::SEX_MALE
        ];
    }

    /**
     * Получение аттрибутов
     * @return array
     * @throws \yii\base\Exception
     */
    protected function initUserAttributes()
    {
        $attributes = $this->api('users.get.json', 'GET', [
            'fields' => implode(',', [
                'uid',
                'first_name',
                'last_name',
                'photo_200',
                'sex'
            ]),
        ]);

        $attributes = array_shift($attributes['response']);

        $return_attributes = [
            'User' => [
                'email' => (isset($this->accessToken->params['email'])) ? $this->accessToken->params['email'] : null,
                'first_name' => $attributes['first_name'],
                'last_name' => $attributes['last_name'],
                'photo' => $attributes['photo_200'],
                'sex' => $this->normalizeSex()[$attributes['sex']]
            ],
            'provider_user_id' => $attributes['uid'],
            'provider_id' => UserOauthKey::getAvailableClients()['vkontakte'],
            'page' => $attributes['uid'],
        ];

        return $return_attributes;
    }
}
