<?php
/**
 * @package   yii2-user
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */

/* @var $this yii\web\View */
/* @var $model \lowbase\user\models\User */
/* @var $password string */

use yii\helpers\Html;

if (isset($model) && $model) {
    $confirmLink = Yii::$app->urlManager->createAbsoluteUrl(['reset',
        'token' => $model->password_reset_token,
        'password' => $password]);
?>

<p><?=Yii::t('user', 'Здравствуйте')?>, <?= Html::encode($model->first_name) ?>!</p>

<p><?=Yii::t('user', 'Сформирован запрос на установку следующего пароля на сайте')?>: <b><?= Html::encode($password) ?></b>.</p>

<p><?=Yii::t('user', 'Для его активации и авторизации по новому паролю проследуйте по ссылке')?>:

<?= Html::a(Html::encode($confirmLink), $confirmLink) ?>.
</p>

<p><?=Yii::t('user', 'Если Вы не подавали запрос на изменение пароля, то ни в коем случае не переходите по ссылке и просто удалите письмо.')?></p>
<?php
}
?>
