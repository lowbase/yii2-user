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
 * This is the model class for table "city".
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
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'lb_city';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['country_id', 'city', 'region'], 'required'],
            [['country_id', 'biggest_city'], 'integer'],
            [['city', 'state', 'region'], 'string', 'max' => 255],
            ['biggest_city', 'default', 'value' => 0],
            [['state'], 'default', 'value' => null],
            [['city', 'region', 'state'], 'filter', 'filter' => 'trim'],
        ];
    }

    /**
     * @inheritdoc
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
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['city_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCountry()
    {
        return $this->hasOne(Country::className(), ['id' => 'country_id']);
    }
    
    /**
     * Список регионов страны
     */
    public function Regions($country_id)
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
