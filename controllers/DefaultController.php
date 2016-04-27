<?php
/**
 * @package   yii2-user
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */

namespace lowbase\user\controllers;

use yii\web\Controller;

/**
 * Основной контроллер модуля
 * 
 * Class DefaultController
 * @package lowbase\user\controllers
 */
class DefaultController extends Controller
{
    /**
     * Настраиваем страницу ошибки и капчу
     * @return array
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'foreColor' => '3373751' //синий
            ],
        ];
    }
}
