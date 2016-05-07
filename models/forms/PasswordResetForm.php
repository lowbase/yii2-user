<?php
/**
 * @package   yii2-user
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */

namespace lowbase\user\models\forms;

use lowbase\user\models\User;
use yii\base\Model;

/**
 * Форма восстановления пароля
 * Class PasswordResetForm
 * @package lowbase\user\models\forms
 */
class PasswordResetForm extends Model
{
    public $email;      // Электронная почта
    public $password;   // Пароль
    public $captcha;    // Капча

    /**
     * Правила валидации
     * @return array
     */
    public function rules()
    {
        return [
            [['email', 'password', 'captcha'], 'required'], // Обязательны для заполнения
            [['password'], 'string', 'min' => 4],   // Пароль минимум 4 символа
            ['email', 'email'], // Электронная почта
            ['email', 'exist',
                'targetClass' => '\lowbase\user\models\User',
                'message' => \Yii::t('user', 'Пользователь с таким Email не зарегистрирован.')
            ],  // Значение электронной почты должно присутствовать в базе данных
            ['captcha', 'captcha', 'captchaAction' => 'lowbase-user/default/captcha'], // Проверка капчи
        ];
    }

    /**
     * Наименования полей формы
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'password' => \Yii::t('user', 'Новый пароль'),
            'email' => \Yii::t('user', 'Email'),
            'captcha' => \Yii::t('user', 'Защитный код'),
        ];
    }

    /**
     * Отправка сообщения с подтверждением
     * нового пароля
     * @return bool
     */
    public function sendEmail()
    {
        /* @var $user User */
        $user = User::findByEmail($this->email);

        if ($user) {
            if (!User::isPasswordResetTokenValid($user->password_reset_token)) {
                $user->generatePasswordResetToken();
            }
            if ($user->save()) {
                // Отправка по шаблону письма "passwordResetToken"
                $view = '@lowbase/user/mail/passwordResetToken';
                if (method_exists(\Yii::$app->controller->module, 'getCustomMailView')) {
                   $view = \Yii::$app->controller->module->getCustomMailView('passwordResetToken', $view);
                }
                return \Yii::$app->mailer->compose($view, [
                    'model' => $user,
                    'password' => $this->password
                ])
                    ->setFrom(\Yii::$app->params['adminEmail'])
                    ->setTo($this->email)
                    ->setSubject(\Yii::t('user', 'Сброс пароля на сайте'))
                    ->send();
            }
        }

        return false;
    }
}
