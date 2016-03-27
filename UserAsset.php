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
 * Widget asset bundle
 */
class UserAsset extends AssetBundle
{
	/**
	 * @inheritdoc
	 */
	public $sourcePath = '@lowbase/user/assets';

    public $css = [
        'css/lb-user-module.css'
    ];

	/**
	 * @inheritdoc
	 */
	public $depends = [
		'yii\web\JqueryAsset'
	];
}
