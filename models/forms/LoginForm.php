<?php
/**
 * @package   yii2-user
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */

namespace lowbase\user\models\forms;

use lowbase\user\models\User;
use Yii;
use yii\base\Model;

/**
 * Форма авторизации
 * Class LoginForm
 * @package lowbase\user\models\forms
 */
class LoginForm extends Model
{
    public $email;
    public $password;
    public $rememberMe = true;

    private $_user = false;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['email', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
            ['email', 'email'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'password' => Yii::t('user', 'Пароль'),
            'email' => Yii::t('user', 'Email'),
            'rememberMe' => Yii::t('user', 'Запомнить меня'),
        ];
    }

    /**
     * Проверка пароля
     * @param $attribute
     */
    public function validatePassword($attribute)
    {
        if (!$this->hasErrors()) {

            $user = $this->getUser();

            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, Yii::t('user', 'Неправильная введен Email или Пароль.'));
            } elseif ($user && $user->status == User::STATUS_WAIT) {
                $this->addError('email', Yii::t('user', 'Аккаунт не подтвержден. Проверьте Email.'));
            } elseif ($user && $user->status == User::STATUS_BLOCKED) {
                $this->addError('email', Yii::t('user', 'Аккаунт заблокирован. Свяжитель с администратором.'));
            }
        }
    }

    /**
     * Авторзиация
     * @return bool
     */
    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600*24*30 : 0);
        }
        return false;
    }

    /**
     * @return bool|null|static
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findOne(['email' => $this->email]);
        }

        return $this->_user;
    }
}
