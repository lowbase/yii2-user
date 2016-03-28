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
     * @var \lowbase\user\models\user
     */
    private $_user;

    /**
     * @param array $token
     * @param $password
     * @param array $config
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
     * @return bool|int|mixed
     */
    public function resetPassword()
    {
        $user = $this->_user;
        $user->setPassword($this->password);
        $user->removePasswordResetToken();

        return (($user->save())) ? $user->id : false;
    }
}
