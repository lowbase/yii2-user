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

/**
 * Форма регистрации на сайте
 * Class SignupForm
 * @package lowbase\user\models\forms
 */
class SignupForm extends User
{
    public $password;   // Пароль
    public $captcha;    // Капча

    /**
     * Правила валидации
     * @return array
     */
    public function rules()
    {
        return [
            [['first_name', 'password', 'email', 'captcha'], 'required'],   // Обязательные поля
            ['email', 'unique', 'targetClass' => self::className(),
                'message' => Yii::t('user', 'Данный Email уже зарегистрирован.')],  // Электронная почта должна быть уникальна
            ['email', 'email'], // Электронная почта
            ['captcha', 'captcha', 'captchaAction' => '/lowbase-user/default/captcha'], // Проверка капчи
            [['password'], 'string', 'min' => 4],   // Пароль минимум 4 символа
            [['sex', 'country_id', 'city_id', 'status'], 'integer'],    // Целочисленные значения
            [['birthday', 'login_at'], 'safe'], // Безопасные аттрибуты
            [['first_name', 'last_name', 'email', 'phone'], 'string', 'max' => 100],    // Строка (максимум 100 символов)
            [['auth_key'], 'string', 'max' => 32],  // Строка (максимум 32 символа)
            [['ip'], 'string', 'max' => 20],    // Строка (максимуму 20 символов)
            [['password_hash', 'password_reset_token', 'email_confirm_token', 'image', 'address'], 'string', 'max' => 255], // Строка (максимум 255 символов)
            ['status', 'in', 'range' => array_keys(self::getStatusArray())], // Статус должен быть из списка статусов
            ['status', 'default', 'value' => self::STATUS_WAIT],    // Статус после регистрации "Ожидает подтверждения"
            ['sex', 'in', 'range' => array_keys(self::getSexArray())],  // Пол должен быть из гендерного списка
            [['first_name', 'last_name', 'email', 'phone', 'address'], 'filter', 'filter' => 'trim'],   // Обрезаем строки по краям
            [['last_name', 'password_reset_token', 'email_confirm_token',
                'image', 'sex', 'phone', 'country_id', 'city_id', 'address',
                'auth_key', 'password_hash', 'email', 'ip', 'login_at'], 'default', 'value' => null],   // По умолчанию значение = null
        ];
    }

    /**
     * Наименования дополнительных полей формы
     * @return array
     */
    public function attributeLabels()
    {
        $labels = parent::attributeLabels();
        $labels['password'] = Yii::t('user', 'Пароль');
        $labels['captcha'] = Yii::t('user', 'Защитный код');
        return $labels;
    }

    /**
     * Генерация ключа авторизации, токена подтверждения регистрации
     * и хеширование пароля перед сохранением
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->setPassword($this->password);
            $this->generateAuthKey();
            $this->generateEmailConfirmToken();
            return true;
        }
        return false;
    }

    /**
     * Отправка письма согласно шаблону "confirmEmail"
     * после регистрации
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        $view = '@vendor/lowbase/user/mail/confirmEmail';
        if (method_exists(\Yii::$app->controller->module, 'getCustomMailView')) {
           $view = \Yii::$app->controller->module->getCustomMailView('confirmEmail', $view);
        }
        Yii::$app->mailer->compose($view, ['model' => $this])
            ->setFrom([Yii::$app->params['adminEmail']])
            ->setTo($this->email)
            ->setSubject(Yii::t('user', 'Подтверждение регистрации на сайте'))
            ->send();
    }
}
