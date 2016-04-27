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
 * Авторизация с помощью Twitter
 * Class Twitter
 * @package lowbase\user\components\oauth\
 */
class Twitter extends \yii\authclient\clients\Twitter
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
     * Получение аттрибутов
     * @return array
     * @throws \yii\base\Exception
     */
    protected function initUserAttributes()
    {
        $attributes = $this->api('account/verify_credentials.json', 'GET');

        $return_attributes = [
            'User' => [
                'email' => null,
                'first_name' => $attributes['name'],
                'photo' => '',
                'sex' => User::SEX_MALE,
            ],
            'provider_user_id' => $attributes['id'],
            'provider_id' => UserOauthKey::getAvailableClients()['twitter'],
            'page' => $attributes['screen_name'],
        ];

        return $return_attributes;
    }
}
