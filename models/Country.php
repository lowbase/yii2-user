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
 * Страны
 *
 * @property integer $id
 * @property string $name
 * @property string $currency_code
 * @property string $currency
 */
class Country extends \yii\db\ActiveRecord
{
    /**
     * Наименование таблицы
     * @return string
     */
    public static function tableName()
    {
        return 'lb_country';
    }

    /**
     * Правила валидации
     * @return array
     */
    public function rules()
    {
        return [
            [['name', 'currency_code'], 'required'],    // Обязательные для заполнения
            [['name', 'currency'], 'string', 'max' => 255], // Строки с количеством символов не более 255
            [['currency_code'], 'string', 'max' => 5],  // Строка с количеством символов не более 5
            [['name', 'currency_code', 'currency'], 'filter', 'filter' => 'trim'],  // Обрезание строк по бокам
            [['currency'], 'default', 'value' => null], // Значения по умолчанию = null
        ];
    }

    /**
     * Наименование полей аттрибутов
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('user', 'ID'),
            'name' => Yii::t('user', 'Название'),
            'currency_code' => Yii::t('user', 'Код валюты'),
            'currency' => Yii::t('user', 'Валюта'),
        ];
    }

    /**
     * Пользователи из этой страны
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['country_id' => 'id']);
    }

    /**
     * Список всех стран массивом [ID => Название страны]
     * @return array
     */
    public static function getAll()
    {
        $countries = [];
        $model = self::find()->all();
        if ($model) {
            foreach ($model as $m) {
                $countries[$m->id] = $m->name;
            }
        }

        return $countries;
    }
}
