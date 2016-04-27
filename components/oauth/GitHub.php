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
 * Авторизация через GitHub
 * Class GitHub
 * @package lowbase\user\components\oauth
 */
class GitHub extends \yii\authclient\clients\GitHub
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
     * Инициализация
     */
    public function init()
    {
        parent::init();
        if ($this->scope === null) {
            $this->scope = implode(' ', [
                'user',
                'user:email',
            ]);
        }
    }

    /**
     * Получение аттрибутов
     * @return array
     * @throws \yii\base\Exception
     */
    protected function initUserAttributes()
    {
        $attributes = $this->api('user', 'GET');
        $emails = $this->api('user/emails', 'GET');

        $verifiedEmail = '';

        foreach ($emails as $email) {
            if ($email['verified'] && $email['primary']) {
                $verifiedEmail = $email['email'];
            }
        }

        $return_attributes = [
            'User' => [
                'email' => $verifiedEmail,
                'first_name' => $attributes['login'],
                'photo' => $attributes['avatar_url'],
                'sex' => User::SEX_MALE
            ],
            'provider_user_id' => $attributes['id'],
            'provider_id' => UserOauthKey::getAvailableClients()['github'],
            'page' => $attributes['login'],
        ];

        return $return_attributes;
    }
}
