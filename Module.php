<?php
/**
 * @package   yii2-user
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */

namespace lowbase\user;

/**
 * Модуль пользователей и управления ими
 */
class Module extends \yii\base\Module
{

    public $controllerNamespace = 'lowbase\user\controllers';

    //Массив с собственными отображениями
    public $customViews = [];
    //Массив с собственными шаблонами писем
    public $customMailViews = [];
    //Action для капчи
    public $captchaAction = '/user/default/captcha';

    public $userPhotoPath = 'attach/user/images';

    /**
     * Инициализация
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::registerTranslations();
    }

    /**
     * Собственные отображения
     * Допустимые параметры:
     *
     * signup - регистрация
     * login - авторизация
     * profile - профиль
     * repass - восстановление пароля
     * show - просмотр пользователя
     *
     * @param $customView - отображение
     * @param $default - отображение по умолчанию
     * @return mixed
     */
    public function getCustomView($customView, $default)
    {
        if (isset($this->customViews[$customView])) {
            return $this->customViews[$customView];
        } else {
            return $default;
        }
    }
    
    /**
     * Собственные шаблоны писем
     * Допустимые параметры:
     *
     * confirmEmail - шаблон письма подтверждения Email
     * passwordResetToken - шаблон письма сброса пароля
     *
     * @param $customMailViews - отображение
     * @param $default - отображение по умолчанию
     * @return mixed
     */
    public function getCustomMailView($customView, $default)
    {
        if (isset($this->customMailViews[$customView])) {
            return $this->customMailViews[$customView];
        } else {
            return '@lowbase/user/mail/' . $default;
        }
    }

    /**
     * Подключаем сообщения перевода
     */
    public static function registerTranslations()
    {
        if (!isset(\Yii::$app->i18n->translations['user']) && !isset(\Yii::$app->i18n->translations['user/*'])) {
            \Yii::$app->i18n->translations['user'] = [
                'class' => 'yii\i18n\PhpMessageSource',
                'basePath' => '@lowbase/user/messages',
                'forceTranslation' => true,
                'fileMap' => [
                    'user' => 'user.php'
                ]
            ];
        }
    }
}
