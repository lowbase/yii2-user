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
 * This is the model class for table "auth_item".
 *
 * @property string $name
 * @property integer $type
 * @property string $description
 * @property string $rule_name
 * @property string $data
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property AuthAssignment[] $authAssignments
 * @property User[] $users
 * @property AuthRule $ruleName
 * @property AuthItemChild[] $authItemChildren
 * @property AuthItemChild[] $authItemChildren0
 * @property AuthItem[] $parents
 * @property AuthItem[] $children
 */
class AuthItem extends \yii\db\ActiveRecord
{
    const TYPE_ROLE = 1;
    const TYPE_PERMISSION = 2;

    public $children_array = [];
    public $user_array = [];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'lb_auth_item';
    }

    /**
     * @inheritdoc
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

    public static function getTypes()
    {
        return ['1' => Yii::t('user', 'Роль'), '2' =>  Yii::t('user', 'Допуск')];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['type', 'default', 'value' => 1],
            [['name', 'type', 'description'], 'required'],
            ['name', 'match', 'pattern' => '/^[a-zA-Z0-9_-]+$/','message' => Yii::t('user', 'Допустимы только латинские буквы и цифры.')],
            [['name', 'description'], 'unique'],
            [['type', 'created_at', 'updated_at'], 'integer'],
            [['description', 'data'], 'string'],
            [['name', 'rule_name'], 'string', 'max' => 64],
            [['description', 'rule_name', 'data',
                'created_at', 'updated_at'], 'default', 'value' => null],
            [['name', 'description'], 'filter', 'filter' => 'trim'],
            [['rule_name'], 'exist', 'skipOnError' => true, 'targetClass' => AuthRule::className(), 'targetAttribute' => ['rule_name' => 'name']],
            [['children_array', 'user_array'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('user', 'Название'),
            'type' => Yii::t('user', 'Тип'),
            'description' => Yii::t('user', 'Описание'),
            'rule_name' => Yii::t('user', 'Правило'),
            'data' => Yii::t('user', 'Данные'),
            'created_at' => Yii::t('user', 'Создана'),
            'updated_at' => Yii::t('user', 'Редактирована'),
            'children_array' =>  Yii::t('user', 'Обладает допусками'),
            'user_array' => Yii::t('user', 'Пользователи имеют'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthAssignments()
    {
        return $this->hasMany(AuthAssignment::className(), ['item_name' => 'name']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['id' => 'user_id'])->viaTable('lb_auth_assignment', ['item_name' => 'name']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRuleName()
    {
        return $this->hasOne(AuthRule::className(), ['name' => 'rule_name']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthItemChildren()
    {
        return $this->hasMany(AuthItemChild::className(), ['child' => 'name']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthItemChildren0()
    {
        return $this->hasMany(AuthItemChild::className(), ['parent' => 'name']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParents()
    {
        return $this->hasMany(AuthItem::className(), ['name' => 'parent'])->viaTable('lb_auth_item_child', ['child' => 'name']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChildren()
    {
        return $this->hasMany(AuthItem::className(), ['name' => 'child'])->viaTable('lb_auth_item_child', ['parent' => 'name']);
    }

    /**
     * Заполняет массив детей
     * @return int
     */
    public function fill()
    {
        if ($this->children) {
            foreach ($this->children as $child) {
                $this->children_array[$child->name] = $child->description;
            }
        }
        if ($this->users) {
            foreach ($this->users as $user) {
                $name = ($user->last_name) ? $user->first_name ." ".$user->last_name." (".$user->id.")" : $user->first_name . " (".$user->id.")";
                $this->user_array[$user->id] = $name;
            }
        }
    }

    /**
     * Список всех прав и ролей массивом
     * @param array $type
     * @return array
     */
    public static function getAll($type = [1,2])
    {
        $auth = [];
        $model = AuthItem::find()
            ->where(['type' => $type])
            ->all();
        if ($model) {
            foreach ($model as $m) {
                $auth[$m->name] = $m->description;
            }
        }
        return $auth;
    }

    /**
     * @param bool $insert
     * @param array $changedAttributes
     * @return bool
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        AuthItemChild::deleteAll(['parent' => $this->name]);
        if ($this->children_array) {
            foreach ($this->children_array as $child) {
                $authItemChild = new AuthItemChild();
                $authItemChild->parent = $this->name;
                $authItemChild->child = $child;
                $authItemChild->save();
            }
        }
        AuthAssignment::deleteAll(['item_name' => $this->name]);
        if ($this->user_array) {
            foreach ($this->user_array as $user) {
                $authAssignment = new AuthAssignment();
                $authAssignment->item_name = $this->name;
                $authAssignment->user_id = $user;
                $authAssignment->save();
            }
        }
        return true;
    }

}
