<?php
/**
 * @package   yii2-user
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */

/* @var $this yii\web\View */
/* @var $model \lowbase\user\models\User */

use yii\helpers\Html;

if (isset($model) && $model) {
    $confirmLink = Yii::$app->urlManager->createAbsoluteUrl(['confirm', 'token' => $model->email_confirm_token]);
?>

<p><?=Yii::t('user', 'Здравствуйте')?>, <?= Html::encode($model->first_name) ?>!</p>

<p><?=Yii::t('user', 'Для подтверждения адреса и первичной авторизации пройдите по ссылке')?>:

<?= Html::a(Html::encode($confirmLink), $confirmLink) ?>.</p>

<p><?=Yii::t('user', 'Если Вы не регистрировались у на нашем сайте, то просто удалите это письмо.')?></p>

<?php
}
?>
