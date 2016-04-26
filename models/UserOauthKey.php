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
 * Ключи авторизации пользователей
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
     * Название таблицы
     * @return string
     */
    public static function tableName()
    {
        return 'lb_user_oauth_key';
    }

    /**
     * Поддерживаемые социальные сети
     * @return array
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

    /**
     * Приставки для формирования
     * личных страниц пользователей в 
     * социальных сетях
     * @return array
     */
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
     * Правила валидации
     * @return array
     */
    public function rules()
    {
        return [
            [['user_id', 'provider_id'], 'integer'],    // Целочисленные значения
            [['provider_user_id', 'page'], 'string', 'max' => 255], // Строки с максимальной длинной 255 символов
            [['user_id', 'provider_id', 'provider_user_id'], 'required'],   // Обязательные поля для заполнения
            [['page'], 'default', 'value' => null], // Значение по умолчанию = null
        ];
    }

    /**
     * Наименование полей аттрибутов модели
     * @return array
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
     * Пользователь, которому принадлежит ключ
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * Возвращает количество активированных социальных сетей
     * @param $user_id - ID пользователя
     * @return int|string
     */
    public static function isOAuth($user_id)
    {
        return self::find()->where(['user_id' => $user_id])->count();
    }
 }
