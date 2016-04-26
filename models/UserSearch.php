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
 * Поиск среди пользователей
 * Class UserSearch
 * @package lowbase\user\models
 */
class UserSearch extends User
{

    const COUNT = 50; // количество пользователей на одной странице

    public $id_from; // начало диапазона поиска по ID
    public $id_till; // конец диапазона поиска по ID
    public $created_at_from; // начало диапазона поиска по дате регистрации
    public $created_at_till; // конец диапазона поиска по дате регистрации
    public $login_at_from; // начало диапазона поиска по дате последней авторизации
    public $login_at_till; // конец диапазона поиска по дате последней авторизации
    public $birthday_from; // начало диапазона поиска по дню рождения
    public $birthday_till; // конец диапазона поиска по дню рождения

    /**
     * Правила валидации
     * @return array
     */
    public function rules()
    {
        return [
            [['id', 'sex', 'country_id', 'city_id', 'status', 'id_from', 'id_till'], 'integer'],    // Целочисленные значения
            [['first_name', 'last_name', 'auth_key', 'password_hash', 'password_reset_token',
                'email_confirm_token', 'email', 'image', 'birthday', 'phone', 'address',
                'created_at', 'updated_at', 'created_at_from', 'created_at_till',
                'birthday_from', 'birthday_till', 'login_at', 'ip', 'login_at_from', 'login_at_till'], 'safe'], // Безопасные аттрибуты
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
     * Наименования дополнительных полей
     * аттрибутов, присущих модели поиска
     * @return array
     */
    public function attributeLabels()
    {
        $label = parent::attributeLabels();
        $label['id_from'] = Yii::t('user', 'От Id');
        $label['id_till'] = Yii::t('user', 'До Id');
        $label['created_at_from'] = Yii::t('user', 'Зарегистрирован с');
        $label['created_at_till'] = Yii::t('user', 'Зарегистрирован до');
        $label['login_at_from'] = Yii::t('user', 'Авторизован с');
        $label['login_at_till'] = Yii::t('user', 'Авторизован до');
        $label['birthday_from'] = Yii::t('user', 'День рождения с');
        $label['birthday_till'] = Yii::t('user', 'День рождения до');
        return $label;
    }

    /**
     * Создает DataProvider на основе переданных данных
     * @param $params - параметры
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = User::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize'=> $this::COUNT,
            ],
            // Сортировка по умолчанию
            'sort' => array(
                'defaultOrder' => ['created_at' => SORT_DESC],
            ),
        ]);

        $this->load($params);

        // Если валидация не пройдена, то ничего не выводить
        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        // Фильтр данных
        $query->andFilterWhere([
            'id' => $this->id,
            'sex' => $this->sex,
            'birthday' => $this->birthday,
            'country_id' => $this->country_id,
            'city_id' => $this->city_id,
            'status' => $this->status,
            'updated_at' => $this->updated_at,
            'login_at' => $this->login_at,
        ]);

        if ($this->created_at) {
            $date = new \DateTime($this->created_at);
            $this->created_at = $date->format('Y-m-d');
        }

        $query->andFilterWhere(['like', 'first_name', $this->first_name])
            ->andFilterWhere(['like', 'last_name', $this->last_name])
            ->andFilterWhere(['like', 'auth_key', $this->auth_key])
            ->andFilterWhere(['like', 'password_hash', $this->password_hash])
            ->andFilterWhere(['like', 'password_reset_token', $this->password_reset_token])
            ->andFilterWhere(['like', 'email_confirm_token', $this->email_confirm_token])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'image', $this->image])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'created_at', $this->created_at])
            ->andFilterWhere(['like', 'address', $this->address]);

        if ($this->id_from) {
            $query->andFilterWhere(['>=', 'id', $this->id_from]);
        }
        if ($this->id_till) {
            $query->andFilterWhere(['<=', 'id', $this->id_till]);
        }
        if ($this->created_at_from) {
            $date_from = new \DateTime($this->created_at_from);
            $query->andFilterWhere(['>=', 'created_at', $date_from->format('Y-m-d')]);
        }
        if ($this->created_at_till) {
            $date_till = new \DateTime($this->created_at_till);
            $query->andFilterWhere(['<=', 'created_at', $date_till->format('Y-m-d')]);
        }
        if ($this->login_at_from) {
            $date_from = new \DateTime($this->login_at_from);
            $query->andFilterWhere(['>=', 'login_at', $date_from->format('Y-m-d')]);
        }
        if ($this->login_at_till) {
            $date_till = new \DateTime($this->login_at_till);
            $query->andFilterWhere(['<=', 'login_at', $date_till->format('Y-m-d')]);
        }
        if ($this->birthday_from) {
            $birthday_from = new \DateTime($this->birthday_from);
            $query->andFilterWhere(['>=', 'birthday', $birthday_from->format('Y-m-d')]);
        }
        if ($this->birthday_till) {
            $birthday_till = new \DateTime($this->birthday_till);
            $query->andFilterWhere(['<=', 'birthday', $birthday_till->format('Y-m-d')]);
        }

        return $dataProvider;
    }
}
