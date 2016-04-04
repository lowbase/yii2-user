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
    public $email;
    public $password;
    public $captcha;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email', 'password', 'captcha'], 'required'],
            [['password'], 'string', 'min' => 4],
            ['email', 'email'],
            ['email', 'exist',
                'targetClass' => '\lowbase\user\models\User',
                'message' => \Yii::t('user', 'Пользователь с таким Email не зарегистрирован.')
            ],
            ['captcha', 'captcha', 'captchaAction' => '/user/default/captcha'],
        ];
    }

    /**
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
        $user =  User::find()->where([
            'email' => $this->email,
        ])->one();

        if ($user) {
            if (!User::isPasswordResetTokenValid($user->password_reset_token)) {
                $user->generatePasswordResetToken();
            }
            if ($user->save()) {
                return \Yii::$app->mailer->compose(\Yii::$app->controller->module->getCustomMailView('passwordResetToken', 'passwordResetToken'), [
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
