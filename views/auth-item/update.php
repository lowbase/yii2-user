<?php
/**
 * @package   yii2-user
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */

use yii\helpers\Html;
use lowbase\user\UserAsset;

/* @var $this yii\web\View */
/* @var $model lowbase\user\models\AuthItem */

$this->title = Yii::t('user', 'Редактирование'). ' '.(($model->type === 1) ? Yii::t('user', 'роли'): Yii::t('user', 'допуска'));
$this->params['breadcrumbs'][] = ['label' => Yii::t('user', 'Роли и допуски'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->name]];
$this->params['breadcrumbs'][] = Yii::t('user', 'Редактирование');
$assets = UserAsset::register($this);

?>
<div class="auth-item-update">

    <div class="row">
        <div class="col-lg-12">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
