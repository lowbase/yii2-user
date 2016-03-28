<?php
/**
 * @package   yii2-user
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model lowbase\user\models\AuthRule */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="auth-rule-form">

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
                            'confirm' => Yii::t('user', 'Вы уверены, что хотите удалить правило?'),
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
            <?= $form->field($model, 'data')->textInput(['maxlength' => true]) ?>

        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
