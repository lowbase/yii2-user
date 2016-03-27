<?php
/**
 * @package   yii2-user
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */

namespace lowbase\user\models\forms;

use lowbase\user\models\User;
use lowbase\user\models\UserOauthKey;
use Yii;

/**
 * Форма профиля (личного кабинета)
 * Class ProfileForm
 * @package app\modules\user\models\forms
 */
class ProfileForm extends User
{
    public $password;
    public $captcha;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['first_name'], 'required'],
            [['password'], 'passwordValidate', 'skipOnEmpty' => false],
            [['email'], 'emailValidate', 'skipOnEmpty' => false],
            ['email', 'unique', 'targetClass' => self::className(),
                'message' => Yii::t('user', 'Данный Email уже зарегистрирован.')],
            ['email', 'email'],
            [['password'], 'string', 'min' => 4],
            [['sex', 'country_id', 'city_id', 'status'], 'integer'],
            [['birthday', 'login_at'], 'safe'],
            [['first_name', 'last_name', 'email', 'phone'], 'string', 'max' => 100],
            [['auth_key'], 'string', 'max' => 32],
            [['ip'], 'string', 'max' => 20],
            [['password_hash', 'password_reset_token', 'email_confirm_token', 'image', 'address'], 'string', 'max' => 255],
            ['status', 'in', 'range' => array_keys(self::getStatusArray())],
            ['status', 'default', 'value' => self::STATUS_WAIT],
            ['sex', 'in', 'range' => array_keys(self::getSexArray())],
            [['first_name', 'last_name', 'email', 'phone', 'address'], 'filter', 'filter' => 'trim'],
            [['last_name', 'password_reset_token', 'email_confirm_token',
                'image', 'sex', 'phone', 'country_id', 'city_id', 'address',
                'auth_key', 'password_hash', 'email', 'ip', 'login_at'], 'default', 'value' => null],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        $labels = parent::attributeLabels();
        $labels['password'] = Yii::t('user', 'Новый пароль');
        return $labels;
    }

     /**
     * @param bool $insert
     * @param $changedAttributes
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->password) {
                $this->setPassword($this->password);
                $this->generateAuthKey();
            }
            if ($this->birthday) {
                $date = new \DateTime($this->birthday);
                $this->birthday = $date->format('Y-m-d');
            }
            return true;
        }
        return false;
    }

    /**
     * @param $attribute
     * @param $params
     */
    public function passwordValidate($attribute, $params)
    {
        if ($this->password_hash === null && !$this->password && !UserOauthKey::isOAuth($this->id)) {
            $this->addError('password', Yii::t('user', 'Необходимо указать пароль.'));
        }
    }

    /**
     * @param $attribute
     * @param $params
     */
    public function emailValidate($attribute, $params)
    {
        if (!$this->email && !UserOauthKey::isOAuth($this->id)) {
            $this->addError('email', Yii::t('user', 'Необходимо указать Email.'));
        }
    }
}
