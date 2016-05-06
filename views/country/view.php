<?php
/**
 * @package   yii2-user
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */

use lowbase\user\UserAsset;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model lowbase\user\models\Country */

$this->title = Yii::t('user', 'Просмотр страны');
$this->params['breadcrumbs'][] = ['label' =>  Yii::t('user', 'Страны'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$assets = UserAsset::register($this);

?>
<div class="country-view">

    <div class="row">
        <div class="col-lg-12">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>
    </div>

    <p>
        <?= Html::a('<i class="glyphicon glyphicon-pencil"></i> '.Yii::t('user', 'Редактировать'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="glyphicon glyphicon-trash"></i> '.Yii::t('user', 'Удалить'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('user', 'Вы уверены, что хотите удалить страну?'),
                'method' => 'post',
            ],
        ]) ?>
        <?= Html::a('<i class="glyphicon glyphicon-menu-left"></i> '.Yii::t('user', 'Отмена'), ['index'], [
            'class' => 'btn btn-default',
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'currency_code',
            'currency',
        ],
    ]) ?>

</div>
