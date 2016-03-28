<?php
/**
 * @package   yii2-user
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\bootstrap\Modal;
use kartik\widgets\DatePicker;
use kartik\widgets\Select2;
use lowbase\user\models\Country;
use lowbase\user\models\User;
use yii\helpers\Url;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model lowbase\user\models\UserSearch */
/* @var $form yii\widgets\ActiveForm */

Modal::begin([
    'header' => '<h1 class="text-center">'.Yii::t('user', 'Поиск по параметрам').'</h1>',
    'toggleButton' => false,
    'id' => 'filter',
    'options' => [
        'tabindex' => false
    ],
]);
?>

<div class="user-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row">
        <div class="col-lg-3">
            <?= $form->field($model, 'id_from') ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'id_till') ?>
        </div>
        <div class="col-lg-6">
            <?= $form->field($model, 'sex')->dropDownList([''=>''] + User::getSexArray()) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <?= $form->field($model, 'first_name') ?>
        </div>
        <div class="col-lg-6">
            <?= $form->field($model, 'last_name') ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <?= $form->field($model, 'email') ?>
        </div>
        <div class="col-lg-6">
            <?= $form->field($model, 'phone') ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <?= $form->field($model, 'country_id')->widget(Select2::classname(), [
                'data' => Country::getAll(),
                'options' => [
                    'placeholder' => '',
                    'id' => 'country_id'
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>

        </div>
        <div class="col-lg-6">
            <?= $form->field($model, 'city_id')->widget(Select2::classname(), [
                'initValueText' => ($model->city_id && $model->city) ? $model->city->city .
                    ' (' . $model->city->state.", ".$model->city->region . ")": '',
                'options' => ['placeholder' => ''],
                'pluginOptions' => [
                    'allowClear' => true,
                    'minimumInputLength' => 3,
                    'ajax' => [
                        'url' => Url::to(['city/find']),
                        'dataType' => 'json',
                        'data' => new JsExpression('function(params) { return {q:params.term}; }')
                    ],
                    'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                    'templateResult' => new JsExpression('function(city) { return city.text; }'),
                    'templateSelection' => new JsExpression('function (city) { return city.text; }'),
                ],
            ]); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <?= $form->field($model, 'status')->dropDownList([''=>''] + User::getStatusArray()) ?>
        </div>
        <div class="col-lg-6">
            <?= $form->field($model, 'address') ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <?= $form->field($model, 'created_at_from')->widget(DatePicker::classname(), [
                    'options' => ['placeholder' => ''], 'type' => DatePicker::TYPE_COMPONENT_APPEND, 'pluginOptions' => [
                        'autoclose' => true,
                        'format' => 'dd.mm.yyyy'
                    ]
                ]); ?>
        </div>
        <div class="col-lg-6">
            <?= $form->field($model, 'created_at_till')
                ->widget(DatePicker::classname(), [
                    'options' => ['placeholder' => ''],
                    'type' => DatePicker::TYPE_COMPONENT_APPEND,
                    'pluginOptions' => [
                        'autoclose'=>true,
                        'format' => 'dd.mm.yyyy'
                    ]
                ]);  ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <?= $form->field($model, 'login_at_from')
                ->widget(DatePicker::classname(), [
                        'options' => ['placeholder' => ''],
                        'type' => DatePicker::TYPE_COMPONENT_APPEND,
                        'pluginOptions' => [
                            'autoclose'=>true,
                            'format' => 'dd.mm.yyyy'
                        ]
                    ]); ?>
        </div>
        <div class="col-lg-6">
            <?= $form->field($model, 'login_at_till')
                ->widget(DatePicker::classname(), [
                        'options' => ['placeholder' => ''],
                        'type' => DatePicker::TYPE_COMPONENT_APPEND,
                        'pluginOptions' => [
                            'autoclose'=>true,
                            'format' => 'dd.mm.yyyy'
                        ]
                    ]);  ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <?= $form->field($model, 'birthday_from')
                ->widget(DatePicker::classname(), [
                    'options' => ['placeholder' => ''],
                    'type' => DatePicker::TYPE_COMPONENT_APPEND,
                    'pluginOptions' => [
                        'autoclose'=>true,
                        'format' => 'dd.mm.yyyy'
                    ]
                ]);  ?>
        </div>
        <div class="col-lg-6">
            <?= $form->field($model, 'birthday_till')
                ->widget(DatePicker::classname(), [
                    'options' => ['placeholder' => ''],
                    'type' => DatePicker::TYPE_COMPONENT_APPEND,
                    'pluginOptions' => [
                        'autoclose'=>true,
                        'format' => 'dd.mm.yyyy'
                    ]
                ]);  ?>
        </div>
    </div>

    <div class="form-group row text-center">
        <div class="col-lg-12">
            <?= Html::submitButton('<i class="glyphicon glyphicon-search"></i> '.Yii::t('user', 'Найти'), ['class' => 'btn btn-primary btn-lg']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php Modal::end(); ?>
