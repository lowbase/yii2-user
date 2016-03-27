<?php
/**
 * @package   yii2-user
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */

namespace lowbase\user\models;

use Yii;

/**
 * This is the model class for table "user_oauth_key".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $provider_id
 * @property string $provider_user_id
 * @property string $page
 *
 * @property User $user
 */
class UserOauthKey extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'lb_user_oauth_key';
    }

    /**
     * @inheritdoc
     */
    public static function getAvailableClients()
    {
        return [
            'vkontakte' => 1,
            'google' => 2,
            'facebook' => 3,
            'github' => 4,
            'yandex' => 7,
            'twitter' => 8
        ];
    }

    public static function getSites()
    {
        return [
            1 => '//vk.com/id',
            2 => '//plus.google.com/',
            3 => '//wwww.facebook.com/',
            4 => '//github.com/',
            7 => '',
            8 => '//twitter.com/'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'provider_id'], 'integer'],
            [['provider_user_id', 'page'], 'string', 'max' => 255],
            [['user_id', 'provider_id', 'provider_user_id'], 'required'],
            [['page'], 'default', 'value' => null],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('user', 'ID'),
            'user_id' =>  Yii::t('user', 'Пользователь'),
            'provider_id' => Yii::t('user', 'Провайдер'),
            'provider_user_id' => Yii::t('user', 'Ключ аутентификации'),
            'page' => Yii::t('user', 'Ключ Страница')
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @param $user_id
     * @return int|string
     */
    public static function isOAuth($user_id)
    {
        return UserOauthKey::find()->where(['user_id' => $user_id])->count();
    }
}
