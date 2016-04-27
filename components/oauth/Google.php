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
 * Авторизация через Google plus
 * Class Google
 * @package lowbase\user\components\oauth
 */
class Google extends \yii\authclient\clients\GoogleOAuth
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
            'male' => User::SEX_MALE,
            'female' => User::SEX_FEMALE
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
                'profile',
                'email',
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
        $attributes = $this->api('people/me', 'GET');

        $return_attributes = [
            'User' => [
                'email' => $attributes['emails'][0]['value'],
                'first_name' => $attributes['name']['givenName'],
                'last_name' => $attributes['name']['familyName'],
                'photo' => str_replace('sz=50', 'sz=200', $attributes['image']['url']),
                'sex' => $this->normalizeSex()[$attributes['gender']]
            ],
            'provider_user_id' => $attributes['id'],
            'provider_id' => UserOauthKey::getAvailableClients()['google'],
            'page' => $attributes['id'],
        ];
        return $return_attributes;
    }
}
