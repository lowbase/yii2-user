<?php
/**
 * @package   yii2-user
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */

use lowbase\user\models\Country;
use lowbase\user\models\User;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\DatePicker;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use yii\helpers\Url;
use lowbase\user\UserAsset;

/* @var $this yii\web\View */
/* @var $model lowbase\user\models\User */

$assets = UserAsset::register($this);

$this->title = Yii::t('user', 'Редактирование данных пользователя');
$this->params['breadcrumbs'][] = ['label' => Yii::t('user', 'Пользователи'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('user', 'Редактирование');
?>
<div class="user-update">

    <div class="row">
        <div class="col-lg-12">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>
    </div>

    <div class="user-form">

        <?php $form = ActiveForm::begin([
            'id' => 'form-update',
            'options' => [
                'class'=>'form',
                'enctype'=>'multipart/form-data'
            ],
        ]); ?>

        <div class="row">
            <div class="col-lg-12">
                <p>
                    <?= Html::submitButton('<i class="glyphicon glyphicon-floppy-disk"></i> '. Yii::t('user', 'Сохранить'), ['class' => 'btn btn-primary']) ?>
                    <?= Html::a('<i class="glyphicon glyphicon-trash"></i> '. Yii::t('user', 'Удалить'), ['delete', 'id' => $model->id], [
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => Yii::t('user', 'Вы уверены, что хотите удалить пользователя?'),
                            'method' => 'post',
                        ],
                    ]) ?>
                    <?= Html::a('<i class="glyphicon glyphicon-menu-left"></i> '. Yii::t('user', 'Отмена'), ['index'], [
                        'class' => 'btn btn-default',
                    ]) ?>
                </p>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <?= $form->field($model, 'first_name')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-lg-6">
                <?= $form->field($model, 'last_name')->textInput(['maxlength' => true]) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <?= $form->field($model, 'auth_key')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-lg-6">
                <?= $form->field($model, 'password_hash')->textInput(['maxlength' => true]) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <?= $form->field($model, 'password_reset_token')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-lg-6">
                <?= $form->field($model, 'email_confirm_token')->textInput(['maxlength' => true]) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <?= $form->field($model, 'password')->passwordInput() ?>
            </div>
            <div class="col-lg-6">
                <?= $form->field($model, 'birthday')
                    ->widget(DatePicker::classname(), [
                            'options' => ['placeholder' => $model->getAttributeLabel('birthday')],
                            'type' => DatePicker::TYPE_COMPONENT_APPEND,
                            'pluginOptions' => [
                                'autoclose'=>true,
                                'format' => 'dd.mm.yyyy'
                            ]
                        ]); ?>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-lg-6">
                <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <?= $form->field($model, 'country_id')->widget(Select2::classname(), [
                    'data' => Country::getAll(),
                    'options' => ['placeholder' => $model->getAttributeLabel('country_id')],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]); ?>
            </div>
            <div class="col-lg-6">
                <?= $form->field($model, 'city_id')->widget(Select2::classname(), [
                    'initValueText' => ($model->city_id && $model->city) ? $model->city->city .
                        ' (' . $model->city->state.", ".$model->city->region . ")": '',
                    'options' => ['placeholder' => $model->getAttributeLabel('city_id')],
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
                ]) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-lg-6">
                <?= $form->field($model, 'status')->dropDownList(User::getStatusArray()) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <div class="lb-user-module-profile-image">
                    <?php
                    if ($model->image) {
                        echo "<img src='/".$model->image."' class='thumbnail'>";
                        echo "<p>" . Html::a(Yii::t('user', 'Удалить фото'), ['rmv', 'id' => $model->id]) . "</p>";
                    } else {
                        if ($model->sex === User::SEX_FEMALE) {
                            echo "<img src='".$assets->baseUrl ."/image/female.png' class='thumbnail'>";
                        } else {
                            echo "<img src='".$assets->baseUrl ."/image/male.png' class='thumbnail'>";
                        }
                    }
                    ?>
                    <?= $form->field($model, 'photo')->fileInput([
                        'maxlength' => true,
                        'placeholder' => $model->getAttributeLabel('photo')
                    ]) ?>
                </div>
            </div>
            <div class="col-lg-6">
                <?= $form->field($model, 'sex')->radioList([null => Yii::t('user', 'Не указан')] + User::getSexArray()) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
