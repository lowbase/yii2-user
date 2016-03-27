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

class Facebook extends \yii\authclient\clients\Facebook
{
    /**
     * @inheritdoc
     */
    public function getViewOptions()
    {
        return [
            'popupWidth' => 900,
            'popupHeight' => 600
        ];
    }

    public function normalizeSex()
    {
        return [
            'male' => User::SEX_MALE,
            'female' => User::SEX_FEMALE
        ];
    }

    /**
     * @inheritdoc
     */
    protected function initUserAttributes()
    {
        $attributes = $this->api('me', 'GET', [
            'fields' => implode(',', [
                'id',
                'email',
                'first_name',
                'last_name',
                'picture.height(200).width(200)',
                'gender'
            ]),
        ]);

        $return_attributes = [
            'User' => [
                'email' => $attributes['email'],
                'first_name' => $attributes['first_name'],
                'last_name' => $attributes['last_name'],
                'photo' => $attributes['picture']['data']['url'],
                'sex' => $this->normalizeSex()[$attributes['gender']]
            ],
            'provider_user_id' => $attributes['id'],
            'provider_id' => UserOauthKey::getAvailableClients()['facebook'],
            'page' => $attributes['id'],
        ];

        return $return_attributes;
    }
}
