<?php
/**
 * @package   yii2-user
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */

use yii\helpers\Html;
use yii\widgets\DetailView;
use lowbase\user\UserAsset;

/* @var $this yii\web\View */
/* @var $model lowbase\user\models\AuthRule */

$this->title = Yii::t('user', 'Просмотр правила');
$this->params['breadcrumbs'][] = ['label' => Yii::t('user', 'Правила контроля доступа'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$assets = UserAsset::register($this);
?>
<div class="auth-rule-view">

    <div class="row">
        <div class="col-lg-12">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>
    </div>

    <p>
        <?= Html::a('<i class="glyphicon glyphicon-trash"></i> '.Yii::t('user', 'Удалить'), ['delete', 'id' => $model->name], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('user', 'Вы уверены, что хотите удалить правило?'),
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
            'name',
            'data:ntext',
            [
                'attribute' => 'created_at',
                'format' =>  ['date', 'dd.MM.Y HH:mm:ss'],
            ],
            [
                'attribute' => 'updated_at',
                'format' =>  ['date', 'dd.MM.Y HH:mm:ss'],
            ],
        ],
    ]) ?>

</div>
