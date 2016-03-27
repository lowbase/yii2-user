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
    public $password;
    public $captcha;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['first_name', 'password', 'email', 'captcha'], 'required'],
            ['email', 'unique', 'targetClass' => self::className(),
                'message' => Yii::t('user', 'Данный Email уже зарегистрирован.')],
            ['email', 'email'],
            ['captcha', 'captcha', 'captchaAction' => '/user/default/captcha'],
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
        $labels['password'] = Yii::t('user', 'Пароль');
        $labels['captcha'] = Yii::t('user', 'Защитный код');
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
            $this->setPassword($this->password);
            $this->generateAuthKey();
            $this->generateEmailConfirmToken();
            return true;
        }
        return false;
    }

    /**
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        Yii::$app->mailer->compose(\Yii::$app->controller->module->getCustomView('confirmEmail', 'confirmEmail'), ['model' => $this])
            ->setFrom([Yii::$app->params['adminEmail']])
            ->setTo($this->email)
            ->setSubject(Yii::t('user', 'Подтверждение регистрации на сайте'))
            ->send();
    }
}
