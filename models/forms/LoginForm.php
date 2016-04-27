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
    public $email;              // Электронная почта
    public $password;           // Пароль
    public $rememberMe = true;  // Запомнить меня

    private $_user = false;

    /**
     * Правила валидации
     * @return array
     */
    public function rules()
    {
        return [
            // И Email и пароль должны быть заполнены
            [['email', 'password'], 'required'],
            // Булево значение (галочка)
            ['rememberMe', 'boolean'],
            // Валидация пароля из метода "validatePassword"
            ['password', 'validatePassword'],
            // Электронная почта
            ['email', 'email'],
        ];
    }

    /**
     * Наименование полей формы
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
     * Проверка комбинации Email - Пароль
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
     * Авторизация
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
     * Получение модели пользователя
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
