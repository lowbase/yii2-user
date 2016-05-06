<?php
/**
 * @package   yii2-user
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */

use lowbase\user\UserAsset;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model lowbase\user\models\Country */

$this->title = Yii::t('user', 'Редактирование страны');
$this->params['breadcrumbs'][] = ['label' => Yii::t('user', 'Страны'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('user', 'Редактирование');
$assets = UserAsset::register($this);

?>
<div class="country-update">

    <div class="row">
        <div class="col-lg-12">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
