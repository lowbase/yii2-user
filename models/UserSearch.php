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
 * UserSearch represents the model behind the search form about `app\modules\user\models\User`.
 */
class UserSearch extends User
{
    public $id_from;
    public $id_till;
    public $created_at_from;
    public $created_at_till;
    public $login_at_from;
    public $login_at_till;
    public $birthday_from;
    public $birthday_till;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'sex', 'country_id', 'city_id', 'status', 'id_from', 'id_till'], 'integer'],
            [['first_name', 'last_name', 'auth_key', 'password_hash', 'password_reset_token',
                'email_confirm_token', 'email', 'image', 'birthday', 'phone', 'address',
                'created_at', 'updated_at', 'created_at_from', 'created_at_till',
                'birthday_from', 'birthday_till', 'login_at', 'ip', 'login_at_from', 'login_at_till'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

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
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = User::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize'=>50,
            ],
            'sort' => array(
                'defaultOrder' => ['created_at' => SORT_DESC],
            ),
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
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
            $this->created_at_from = $date_from->format('Y-m-d');
            $query->andFilterWhere(['>=', 'created_at', $this->created_at_from]);
            $this->created_at_from = $date_from->format('d.m.Y');
        }
        if ($this->created_at_till) {
            $date_till = new \DateTime($this->created_at_till);
            $this->created_at_till = $date_till->format('Y-m-d');
            $query->andFilterWhere(['<=', 'created_at', $this->created_at_till]);
            $this->created_at_till = $date_till->format('d.m.Y');
        }
        if ($this->login_at_from) {
            $date_from = new \DateTime($this->login_at_from);
            $this->login_at_from = $date_from->format('Y-m-d');
            $query->andFilterWhere(['>=', 'login_at', $this->login_at_from]);
            $this->login_at_from = $date_from->format('d.m.Y');
        }
        if ($this->login_at_till) {
            $date_till = new \DateTime($this->login_at_till);
            $this->login_at_till = $date_till->format('Y-m-d');
            $query->andFilterWhere(['<=', 'login_at', $this->login_at_till]);
            $this->login_at_till = $date_till->format('d.m.Y');
        }
        if ($this->birthday_from) {
            $birthday_from = new \DateTime($this->birthday_from);
            $this->birthday_from = $birthday_from->format('Y-m-d');
            $query->andFilterWhere(['>=', 'birthday', $this->birthday_from]);
            $this->birthday_from = $birthday_from->format('d.m.Y');
        }
        if ($this->birthday_till) {
            $birthday_till = new \DateTime($this->birthday_till);
            $this->birthday_till = $birthday_till->format('Y-m-d');
            $query->andFilterWhere(['<=', 'birthday', $this->birthday_till]);
            $this->birthday_till = $birthday_till->format('d.m.Y');
        }

        return $dataProvider;
    }
}
