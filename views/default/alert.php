<?php
/**
 * @package   yii2-user
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */

use kartik\widgets\Alert;
?>

<div class="lb-user-module-alert">

<?php
$delay = 4000;

if (Yii::$app->session->hasFlash('error')) {
    echo Alert::widget([
        'type' => Alert::TYPE_DANGER,
        'title' => Yii::t('user', 'Ошибка'),
        'icon' => 'glyphicon glyphicon-remove-sign',
        'body' => Yii::$app->session->getFlash('error'),
        'showSeparator' => true,
        'delay' => $delay
    ]);
}
if (Yii::$app->session->hasFlash('success')) {
    echo Alert::widget([
        'type' => Alert::TYPE_SUCCESS,
        'title' => Yii::t('user', 'Результат'),
        'icon' => 'glyphicon glyphicon-ok-sign',
        'body' => Yii::$app->session->getFlash('success'),
        'showSeparator' => true,
        'delay' => $delay
    ]);
}
?>

</div>
