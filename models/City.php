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
 * Города
 *
 * @property integer $id
 * @property integer $country_id
 * @property string $city
 * @property string $state
 * @property string $region
 * @property integer $biggest_city
 */
class City extends \yii\db\ActiveRecord
{
    /**
     * Наименование таблицы
     * @return string
     */
    public static function tableName()
    {
        return 'lb_city';
    }

    /**
     * Правила валидации
     * @return array
     */
    public function rules()
    {
        return [
            [['country_id', 'city', 'region'], 'required'], // Обязательные для заполнения
            [['country_id', 'biggest_city'], 'integer'],    // Целочисленные значения
            [['city', 'state', 'region'], 'string', 'max' => 255],  //  Строковые значения (не более 255 символов)
            [['city', 'region', 'state'], 'filter', 'filter' => 'trim'], // Обрезаем строки по краям
            ['biggest_city', 'default', 'value' => 0],  // Значение по умолчанию = 0
            [['state'], 'default', 'value' => null],    // Значения по умолчанию = null
        ];
    }

    /**
     * Наименования полей аттрибутов
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('user', 'ID'),
            'country_id' => Yii::t('user', 'Страна'),
            'city' => Yii::t('user', 'Город'),
            'state' => Yii::t('user', 'Район'),
            'region' => Yii::t('user', 'Регион'),
            'biggest_city' => Yii::t('user', 'Большой город'),
        ];
    }

    /**
     * Пользователи из города
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['city_id' => 'id']);
    }

    /**
     * Страна текущего города
     * @return \yii\db\ActiveQuery
     */
    public function getCountry()
    {
        return $this->hasOne(Country::className(), ['id' => 'country_id']);
    }

    /**
     * Список регионов страны
     * @param $country_id - страны
     * @return array
     */
    public static function regions($country_id)
    {
        $region = [];
        $city = self::find()
            ->where(['country_id' => $country_id])
            ->groupBy(['region'])
            ->orderBy(['region' => SORT_ASC])
            ->all();
        if ($city) {
            foreach ($city as $c) {
                $region[$c->region] = $c->region;
            }
        }
        return $region;
    }
}
