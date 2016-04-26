<?php
/**
 * @package   yii2-user
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */

namespace lowbase\user;

use yii\web\AssetBundle;

/**
 * Подключение CSS и JS
 */
class UserAsset extends AssetBundle
{
    public $sourcePath = '@lowbase/user/assets';

    public $css = [
        'css/lb-user-module.css'
    ];

    public $depends = [
        'yii\web\JqueryAsset'
    ];
}
