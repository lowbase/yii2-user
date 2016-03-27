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
 * This is the model class for table "auth_rule".
 *
 * @property string $name
 * @property string $data
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property AuthItem[] $authItems
 */
class AuthRule extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'lb_auth_rule';
    }

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
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'unique'],
            [['data'], 'string'],
            [['name'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('user', 'Название'),
            'data' => Yii::t('user', 'Данные'),
            'created_at' => Yii::t('user', 'Созданы'),
            'updated_at' => Yii::t('user', 'Обновлены'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthItems()
    {
        return $this->hasMany(AuthItem::className(), ['rule_name' => 'name']);
    }

    public function beforeValidate()
    {
        if ($this->data) {
            if (class_exists($this->data)) {
                $class = new $this->data();
                $this->data = serialize($class);
            } else {
                $this->addError('data', Yii::t('user', 'Класс не найден'));
            }
        }
        return true;
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            return true;
        }
        return false;
    }

    /**
     * Список всех правил массивом
     * @param array $type
     * @return array
     */
    public static function getAll()
    {
        $rule = [];
        $model = AuthRule::find()
            ->all();
        if ($model) {
            foreach ($model as $m) {
                $rule[$m->name] = $m->name;
            }
        }
        return $rule;
    }
}
