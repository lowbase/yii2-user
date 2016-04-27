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
    public $password;   // Новый пароль

    /**
     * Правила валдиации
     * @return array
     */
    public function rules()
    {
        return [
            [['first_name'], 'required'],   // Обязательно для заполнения
            [['password'], 'passwordValidate', 'skipOnEmpty' => false], // Валидация пароля методом модели
            [['email'], 'emailValidate', 'skipOnEmpty' => false],   // Валидация электронной почты методом модели
            ['email', 'unique', 'targetClass' => self::className(),
                'message' => Yii::t('user', 'Данный Email уже зарегистрирован.')],  // Электронная почта должна быть уникальна
            ['email', 'email'], // Электронная почта
            [['password'], 'string', 'min' => 4],   // Пароль минимум 4 символа
            [['sex', 'country_id', 'city_id', 'status'], 'integer'],    // Целочисленные значения
            [['birthday', 'login_at'], 'safe'], // Безопасные аттрибуты
            [['first_name', 'last_name', 'email', 'phone'], 'string', 'max' => 100], // Строковые значения (максимум 100 символов)
            [['auth_key'], 'string', 'max' => 32],  // Строковое значение (максимум 32 символа)
            [['ip'], 'string', 'max' => 20],    // Строковое значение (максимум 20 симоволов)
            [['password_hash', 'password_reset_token', 'email_confirm_token', 'image', 'address'], 'string', 'max' => 255], // Строка (максимум 255 символов)
            ['status', 'in', 'range' => array_keys(self::getStatusArray())], // Статус должен быть из списка статусов
            ['sex', 'in', 'range' => array_keys(self::getSexArray())], // Пол должен быть из гендерного списка
            [['first_name', 'last_name', 'email', 'phone', 'address'], 'filter', 'filter' => 'trim'],   // Обрезание строк по краям
            [['last_name', 'password_reset_token', 'email_confirm_token',
                'image', 'sex', 'phone', 'country_id', 'city_id', 'address',
                'auth_key', 'password_hash', 'email', 'ip', 'login_at'], 'default', 'value' => null],   // По умолчанию = null
        ];
    }

    /**
     * Дополнительные поля формы
     * @return array
     */
    public function attributeLabels()
    {
        $labels = parent::attributeLabels();
        $labels['password'] = Yii::t('user', 'Новый пароль');
        return $labels;
    }

    /**
     * Генерация пароля и ключа авторизации,
     * преобразование дня рождения в необходимый
     * формат перед сохранением
     * 
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            // Если указан новый пароль
            if ($this->password) {
                $this->setPassword($this->password);
                $this->generateAuthKey();
            }
            // Преобразование дня рождения
            if ($this->birthday) {
                $date = new \DateTime($this->birthday);
                $this->birthday = $date->format('Y-m-d');
            }
            return true;
        }
        return false;
    }

    /**
     * Валидация пароля
     * 
     * Указывается обязательно при отсутствии
     * ключей авторизации соц. сетей
     */
    public function passwordValidate()
    {
        if ($this->password_hash === null && !$this->password && !UserOauthKey::isOAuth($this->id)) {
            $this->addError('password', Yii::t('user', 'Необходимо указать пароль.'));
        }
    }

    /**
     * Валидация электронной почты
     * 
     * Указывается обязательно при отсутствии
     * ключей авторизации соц. сетей
     */
    public function emailValidate()
    {
        if (!$this->email && !UserOauthKey::isOAuth($this->id)) {
            $this->addError('email', Yii::t('user', 'Необходимо указать Email.'));
        }
    }
}
