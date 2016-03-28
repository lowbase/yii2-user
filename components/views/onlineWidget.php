<?php
/**
 * @package   yii2-user
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */

use yii\helpers\Url;

/* @var $time integer */

$this->registerJs(
    'setInterval(function(){
        $.ajax({
            url: "' . Url::to(['user/user/online']) . '",
            type:"POST",
            data:{id: "'.\Yii::$app->user->id.'"},
            });
    },'.$time.');'
);
