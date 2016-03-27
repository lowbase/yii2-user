<?php
/**
 * @package   yii2-user
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */

namespace lowbase\user\models;

use yii\base\InvalidParamException;
use yii\base\Model;
use Yii;

/**
 * Password reset
 */
class ResetPassword extends Model
{
    public $password;

    /**
     * @var \common\models\User
     */
    private $_user;

    /**
     * Creates a form model given a token.
     * @param  string                          $token
     * @param  array                           $config name-value pairs that will be used to initialize the object properties
     * @throws \yii\base\InvalidParamException if token is empty or not valid
     */
    public function __construct($token, $password, $config = [])
    {
        if (empty($token) || !is_string($token)) {
            throw new InvalidParamException(Yii::t('user', 'Ключ для восстановления пароля не найден.'));
        }
        if (empty($password) || !is_string($password)) {
            throw new InvalidParamException(Yii::t('user', 'Пароль не задан.'));
        }
        $this->_user = User::findByPasswordResetToken($token);
        $this->password = $password;
        if (!$this->_user) {
            throw new InvalidParamException(Yii::t('user', 'Неправильный ключ для восстановления пароля.'));
        }
        parent::__construct($config);
    }

    /**
     * Resets password.
     * @return boolean if password was reset.
     */
    public function resetPassword()
    {
        $user = $this->_user;
        $user->setPassword($this->password);
        $user->removePasswordResetToken();

        return (($user->save())) ? $user->id : false;
    }
}
