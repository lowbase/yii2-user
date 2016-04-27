<?php
/**
 * @package   yii2-user
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */

namespace lowbase\user\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * Связи между пользователями и ролями
 *
 * @property string $item_name
 * @property integer $user_id
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property User $user
 * @property AuthItem $itemName
 */
class AuthAssignment extends \yii\db\ActiveRecord
{
    /**
     * Наименование таблицы
     * @return string
     */
    public static function tableName()
    {
        return 'lb_auth_assignment';
    }

    /**
     * Автоматическое заполнение создания и редактирования
     * @return array
     */
    public function behaviors()
    {
        return [[
            'class' => TimestampBehavior::className(),
            'createdAtAttribute' => 'created_at',
            'updatedAtAttribute' => 'updated_at',
            'value' => time(),
        ]];
    }

    /**
     * Правила валдиации
     * @return array
     */
    public function rules()
    {
        return [
            [['item_name', 'user_id'], 'required'], // Обязательно для заполнения
            [['user_id', 'created_at', 'updated_at'], 'integer'],   // Целочисленные значения
            [['item_name'], 'string', 'max' => 64], // Строковые значения (максимум 64 символа)
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['item_name'], 'exist', 'skipOnError' => true, 'targetClass' => AuthItem::className(), 'targetAttribute' => ['item_name' => 'name']],
        ];
    }

    /**
     * Прользователь
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * Роль или допуск
     * @return \yii\db\ActiveQuery
     */
    public function getItemName()
    {
        return $this->hasOne(AuthItem::className(), ['name' => 'item_name']);
    }
}
