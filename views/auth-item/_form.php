<?php
/**
 * @package   yii2-user
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */

use lowbase\user\models\AuthItem;
use lowbase\user\models\AuthRule;
use lowbase\user\models\User;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model lowbase\user\models\AuthItem */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="auth-item-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-lg-12">
            <p>
                <?= Html::submitButton('<i class="glyphicon glyphicon-floppy-disk"></i> '.Yii::t('user', 'Сохранить'), ['class' => 'btn btn-primary']) ?>
                <?php
                if (!$model->isNewRecord) {
                    Html::a('<i class="glyphicon glyphicon-trash"></i> '.Yii::t('user', 'Удалить'), ['delete', 'id' => $model->name], [
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => Yii::t('user', 'Вы уверены, что хотите удалить').' '. ($model->type === 1) ? Yii::t('user', 'роль') : Yii::t('user', 'допуск') .'?',
                            'method' => 'post',
                        ],
                    ]);
                }
                ?>
                <?= Html::a('<i class="glyphicon glyphicon-menu-left"></i> '.Yii::t('user', 'Отмена'), ['index'], [
                    'class' => 'btn btn-default',
                ]) ?>
            </p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-lg-6">
            <?= $form->field($model, 'type')->dropDownList(AuthItem::getTypes()) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>
        </div>
        <div class="col-lg-6">
            <?= $form->field($model, 'data')->textarea(['rows' => 6]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <?= $form->field($model, 'rule_name')->widget(Select2::classname(), [
                'data' => AuthRule::getAll(),
                'options' => ['placeholder' => ''],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
        </div>
        <div class="col-lg-6">
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <?= '<label class="control-label">'.Yii::t('user', 'Обладает допусками').'</label>' ?>
            <?= Select2::widget([
                'name' => 'AuthItem[children_array]',
                'value' => array_keys($model->children_array),
                'data' => AuthItem::getAll(),
                'options' => ['multiple' => true],
                'pluginOptions' => [
                    'tags' => true,
                ],
            ]);
            ?>
        </div>
        <div class="col-lg-6">
            <?= '<label class="control-label">'.Yii::t('user', 'Обладают пользователи').'</label>' ?>
            <?= Select2::widget([
                'name' => 'AuthItem[user_array]',
                'value' => array_keys($model->user_array),
                'data' => User::getAll(),
                'options' => ['multiple' => true],
                'pluginOptions' => [
                    'tags' => true,
                ],
            ]);
            ?>
        </div>
    </div>


    <?php ActiveForm::end(); ?>

</div>
