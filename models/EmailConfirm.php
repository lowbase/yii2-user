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
 * Подтверждение электронной почты
 * Class EmailConfirm
 * @package lowbase\user\models
 */
class EmailConfirm extends Model
{
    /**
     * @var User
     */
    private $_user;

    /**
     * @param  string $token - токен
     * @param  array $config - параметры
     * @throws \yii\base\InvalidParamException - при пустом или неправильном токене
     */
    public function __construct($token, $config = [])
    {
        if (empty($token) || !is_string($token)) {
            throw new InvalidParamException(Yii::t('user', 'Отсутствует код подтверждения.'));
        }
        $this->_user = User::findByEmailConfirmToken($token);
        if (!$this->_user) {
            throw new InvalidParamException(Yii::t('user', 'Неверный токен.'));
        }
        parent::__construct($config);
    }

    /**
     * Подтверждение электронной почты
     * @return bool|int
     */
    public function confirmEmail()
    {
        $user = $this->_user;
        $user->status = User::STATUS_ACTIVE;
        $user->removeEmailConfirmToken();   // Удаление токена подтверждения электронной почты

        return (($user->save())) ? $user->id : false;
    }
}
