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
 * This is the model class for table "country".
 *
 * @property integer $id
 * @property string $name
 * @property string $currency_code
 * @property string $currency
 */
class Country extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'lb_country';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'currency_code'], 'required'],
            [['name', 'currency'], 'string', 'max' => 255],
            [['currency_code'], 'string', 'max' => 5],
            [['currency'], 'default', 'value' => null],
            [['name', 'currency_code', 'currency'], 'filter', 'filter' => 'trim'],
        ];
    }

    /**
     * @inheritdoc
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
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['country_id' => 'id']);
    }

    /**
     * Список всех стран массивом
     * @return array
     */
    public static function getAll()
    {
        $countries = [];
        $model = Country::find()->all();
        if ($model) {
            foreach ($model as $m) {
                $countries[$m->id] = $m->name;
            }
        }

        return $countries;
    }
}
