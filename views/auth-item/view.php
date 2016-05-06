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
/* @var $model lowbase\user\models\AuthItem */

$this->title = Yii::t('user', 'Просмотр') .' ' . (($model->type === 1) ? Yii::t('user', 'роли') : Yii::t('user', 'допуска'));
$this->params['breadcrumbs'][] = ['label' => Yii::t('user', 'Роли и допуски'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
UserAsset::register($this);
?>

<div class="auth-item-view">

    <div class="row">
        <div class="col-lg-12">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>
    </div>

    <p>
        <?= Html::a('<i class="glyphicon glyphicon-pencil"></i> '.Yii::t('user', 'Редактировать'), ['update', 'id' => $model->name], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="glyphicon glyphicon-trash"></i> '.Yii::t('user', 'Удалить'), ['delete', 'id' => $model->name], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('user', 'Вы уверены, что хотите удалить').' '.(($model->type === 1) ? Yii::t('user', 'роль') : Yii::t('user', 'допуск')).'?',
                'method' => 'post',
            ],
        ]) ?>
        <?= Html::a('<i class="glyphicon glyphicon-menu-left"></i> '.Yii::t('user', 'Отмена'), ['index'], [
            'class' => 'btn btn-default',
        ]) ?>
    </p>

    <?php
    $children = '';
    if ($model->children) {
        foreach ($model->children as $child) {
            $type = ($child->type == 1) ? 'label-primary' : 'label-success';
            $children .= Html::a('<span class="label '.$type.'">'.$child->description.'</span>', ['auth-item/view', 'id' => $child->name])." ";
        }
    }
    $users = '';
    if ($model->users) {
        foreach ($model->users as $user) {
            $name = ($user->last_name) ? $user->first_name ." ".$user->last_name." (".$user->id.")" : $user->first_name . " (".$user->id.")";
            $users .= Html::a('<span class="label label-success">'.$name.'</span>', ['user/view', 'id' => $user->id])." ";
        }
    }
    $parents = '';
    if ($model->parents) {
        foreach ($model->parents as $parent) {
            $type = ($parent->type == 1) ? 'label-primary' : 'label-success';
            $parents .= Html::a('<span class="label '.$type.'">'.$parent->description.'</span>', ['auth-item/view', 'id' => $parent->name])." ";
        }
    }
    ?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
            [
                'attribute' => 'type',
                'value' => ($model->type === 1) ? Yii::t('user', 'Роль') : Yii::t('user', 'Допуск'),
            ],
            'description:ntext',
            'rule_name',
            'data:ntext',
            [
                'attribute' => 'created_at',
                'format' =>  ['date', 'dd.MM.Y HH:mm:ss'],
            ],
            [
                'attribute' => 'updated_at',
                'format' =>  ['date', 'dd.MM.Y HH:mm:ss'],
            ],
            [
                'attribute' => Yii::t('user', 'Обладает допусками'),
                'format' => 'raw',
                'value' =>  ($children) ? $children : null,
            ],
            [
                'attribute' => Yii::t('user', 'Пользователи имеют'),
                'format' => 'raw',
                'value' =>  ($users) ? $users : null,
            ],
            [
                'attribute' => Yii::t('user', 'Роль / допуск имеют'),
                'format' => 'raw',
                'value' =>  ($parents) ? $parents : null,
            ],
        ],
    ]) ?>

</div>
