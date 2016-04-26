<?php
/**
 * @package   yii2-user
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */

namespace lowbase\user\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * Поиск по городам
 * Class CitySearch
 * @package lowbase\user\models
 */
class CitySearch extends City
{
    const COUNT = 50; // количество городов на одной странице

    /**
     * Правила валидации
     * @return array
     */
    public function rules()
    {
        return [
            [['id', 'country_id', 'biggest_city'], 'integer'],  // Целочисленные значения
            [['city', 'state', 'region'], 'safe'],  // Безопасные аттрибуты
        ];
    }

    /**
     * Сценарии
     * @return array
     */
    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * Создает DataProvider на основе переданных данных
     * @param $params - параметры
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = City::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize'=> $this::COUNT,
            ],
        ]);

        $this->load($params);

        // Если валидация не пройдена, то ничего не выводить
        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        // Фильтрация
        $query->andFilterWhere([
            'id' => $this->id,
            'country_id' => $this->country_id,
            'biggest_city' => $this->biggest_city,
        ]);

        $query->andFilterWhere(['like', 'city', $this->city])
            ->andFilterWhere(['like', 'state', $this->state])
            ->andFilterWhere(['like', 'region', $this->region]);

        return $dataProvider;
    }
}
