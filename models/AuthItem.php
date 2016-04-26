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
 * Роли и допуски
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
    const TYPE_ROLE = 1;        // Роль
    const TYPE_PERMISSION = 2;  // Допуск

    public $children_array = [];    // Дочерние роли/допуски
    public $user_array = [];        // Пользователи, обаладающие ролью/допуском

    /**
     * Наименование таблицы
     * @return string
     */
    public static function tableName()
    {
        return 'lb_auth_item';
    }

    /**
     * Автозаполнение даты создания и редактирования
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
     * Типы записей (роль или допуск)
     * @return array
     */
    public static function getTypes()
    {
        return [
            '1' => Yii::t('user', 'Роль'),
            '2' =>  Yii::t('user', 'Допуск')
        ];
    }

    /**
     * Правила валидации
     * @return array
     */
    public function rules()
    {
        return [
            [['name', 'type', 'description'], 'required'],  // Обязательны для заполнения
            ['name', 'match', 'pattern' => '/^[a-zA-Z0-9_-]+$/','message' => Yii::t('user', 'Допустимы только латинские буквы и цифры.')], // Латинские буквы и цифры
            [['name', 'description'], 'unique'],    // Уникальные значения
            [['type', 'created_at', 'updated_at'], 'integer'],  // Целочисленные значения
            [['description', 'data'], 'string'],    // Строковые значения
            [['name', 'rule_name'], 'string', 'max' => 64], // Строковые значения (максимум 64 символов)
            [['rule_name'], 'exist', 'skipOnError' => true, 'targetClass' => AuthRule::className(), 'targetAttribute' => ['rule_name' => 'name']],
            [['children_array', 'user_array'], 'safe'], // Безопасные аттрибуты
            [['name', 'description'], 'filter', 'filter' => 'trim'],    // Обрезаем строки по краям
            [['description', 'rule_name', 'data',
                'created_at', 'updated_at'], 'default', 'value' => null],   // По умолучанию = null
            ['type', 'default', 'value' => self::TYPE_ROLE], // По умолчанию тип записи "Роль"
        ];
    }

    /**
     * Наименование полей аттрибутов
     * @return array
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
     * Связь название роли => пользователь
     * @return \yii\db\ActiveQuery
     */
    public function getAuthAssignments()
    {
        return $this->hasMany(AuthAssignment::className(), ['item_name' => 'name']);
    }

    /**
     * Пользователи, обладающие текущей ролью или допуском
     * @return $this
     */
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['id' => 'user_id'])->viaTable('lb_auth_assignment', ['item_name' => 'name']);
    }

    /**
     * Правило, которому принадлежит допуск
     * @return \yii\db\ActiveQuery
     */
    public function getRuleName()
    {
        return $this->hasOne(AuthRule::className(), ['name' => 'rule_name']);
    }

    /**
     * Дочерние роли или допуски по связи child
     * @return \yii\db\ActiveQuery
     */
    public function getAuthItemChildren()
    {
        return $this->hasMany(AuthItemChild::className(), ['child' => 'name']);
    }

    /**
     * Дочерние роли или допуски по связи parent
     * @return \yii\db\ActiveQuery
     */
    public function getAuthItemChildren0()
    {
        return $this->hasMany(AuthItemChild::className(), ['parent' => 'name']);
    }

    /**
     * Родительские роли или допуски
     * @return $this
     */
    public function getParents()
    {
        return $this->hasMany(AuthItem::className(), ['name' => 'parent'])->viaTable('lb_auth_item_child', ['child' => 'name']);
    }

    /**
     * Дочерние роли или допуски
     * @return \yii\db\ActiveQuery
     */
    public function getChildren()
    {
        return $this->hasMany(AuthItem::className(), ['name' => 'child'])->viaTable('lb_auth_item_child', ['parent' => 'name']);
    }

    /**
     * Заполнение массива дочерних ролей и допусков
     * Заполнение массива пользователей, обладающих ролью или допуском
     *
     * Выполняется, как правило, при инциализации модели
     */
    public function fill()
    {
        // Дочерние роли и допуски
        if ($this->children) {
            foreach ($this->children as $child) {
                $this->children_array[$child->name] = $child->description;
            }
        }
        // Пользователи
        if ($this->users) {
            foreach ($this->users as $user) {
                $name = ($user->last_name) ? $user->first_name ." ".$user->last_name : $user->first_name;
                $name .= " (" . $user->id . ")"; // добавляем ID к надписи
                $this->user_array[$user->id] = $name;
            }
        }
    }

    /**
     * Список всех допусков и ролей массивом
     * @param array $type - тип записи (1 - роль, 2 - допуск) по умолчанию оба
     * @return array
     */
    public static function getAll($type = [1,2])
    {
        $auths = [];
        $model = self::find()
            ->where(['type' => $type])
            ->all();
        if ($model) {
            foreach ($model as $m) {
                $auths[$m->name] = $m->description;
            }
        }
        return $auths;
    }

    /**
     * Записываем роли, допуски и их связи в
     * соотествующие таблицы после сохранения
     *
     * @param bool $insert
     * @param array $changedAttributes
     * @return bool
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        // Удаляем сначала прошлые связи с другими ролями и допусками
        AuthItemChild::deleteAll(['parent' => $this->name]);
        // Сохраняем новые связи
        if ($this->children_array) {
            foreach ($this->children_array as $child) {
                $authItemChild = new AuthItemChild();
                $authItemChild->parent = $this->name;
                $authItemChild->child = $child;
                $authItemChild->save();
            }
        }
        // Удаляем сначала прошлые связи с пользователями
        AuthAssignment::deleteAll(['item_name' => $this->name]);
        // Сохраняем новые связи
        if ($this->user_array) {
            foreach ($this->user_array as $user) {
                $authAssignment = new AuthAssignment();
                $authAssignment->item_name = $this->name;
                $authAssignment->user_id = $user;
                $authAssignment->save();
            }
        }
    }
}
