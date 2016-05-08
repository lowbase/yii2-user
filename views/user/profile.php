<?php
/**
 * @package   yii2-user
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */

use lowbase\user\components\AuthKeysManager;
use lowbase\user\models\Country;
use lowbase\user\models\User;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\widgets\DatePicker;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use lowbase\user\UserAsset;

$this->title = Yii::t('user', 'Мой профиль');
$this->params['breadcrumbs'][] = $this->title;
$assets = UserAsset::register($this);
?>

<?php $form = ActiveForm::begin([
    'id' => 'form-profile',
    'options' => [
        'class'=>'form',
        'enctype'=>'multipart/form-data'
    ],
    'fieldConfig' => [
        'template' => "{input}\n{hint}\n{error}"
    ]
    ]); ?>

<div class="profile row">
    <div class="col-lg-12">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <div class="col-lg-6">

        <?= $form->field($model, 'first_name')->textInput([
            'maxlength' => true,
            'placeholder' => $model->getAttributeLabel('first_name')
        ]) ?>

        <?= $form->field($model, 'last_name')->textInput([
            'maxlength' => true,
            'placeholder' => $model->getAttributeLabel('last_name')
        ]) ?>

        <?= $form->field($model, 'email')->textInput([
            'maxlength' => true,
            'placeholder' => $model->getAttributeLabel('email')
        ]) ?>

        <?= $form->field($model, 'phone')->textInput([
            'maxlength' => true,
            'placeholder' => $model->getAttributeLabel('phone')
        ]) ?>

        <?= $form->field($model, 'birthday')
            ->widget(DatePicker::classname(), [
                'options' => ['placeholder' => $model->getAttributeLabel('birthday')],
                'type' => DatePicker::TYPE_COMPONENT_APPEND,
                'pluginOptions' => [
                    'autoclose'=>true,
                    'format' => 'dd.mm.yyyy'
                ]
                ]); ?>

        <?= $form->field($model, 'country_id')->widget(Select2::classname(), [
            'data' => Country::getAll(),
            'options' => ['placeholder' => $model->getAttributeLabel('country_id')],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]); ?>

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
        ]); ?>

        <?= $form->field($model, 'address')->textInput([
            'maxlength' => true,
            'placeholder' => $model->getAttributeLabel('address')
        ]) ?>

        <?= $form->field($model, 'sex')->radioList([null => Yii::t('user', 'Не указан')] + User::getSexArray()) ?>

        <div class="form-group">
            <?= Html::submitButton('<i class="glyphicon glyphicon-ok"></i> '.Yii::t('user', 'Сохранить'), [
                'class' => 'btn btn-lg btn-primary',
                'name' => 'signup-button']) ?>
            <?= Html::a('<i class="glyphicon glyphicon-log-out"></i> '.Yii::t('user', 'Выход'), ['logout'], [
                'class' => 'btn btn-lg btn-default',
                'name' => 'signup-button']) ?>
        </div>

    </div>

    <div class="col-lg-6">

        <div class="lb-user-module-profile-image">
            <?php
            if ($model->image) {
                echo "<img src='".$model->image."' class='thumbnail'>";
                echo "<p>" . Html::a(Yii::t('user', 'Удалить фото'), ['remove']) . "</p>";
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

        <div class="lb-user-module-profile-keys">
            <?= AuthKeysManager::widget([
                'baseAuthUrl' => ['/lowbase-user/auth/index'],
            ]) ?>
        </div>

        <div class="lb-user-module-profile-password">
            <?= $form->field($model, 'password')->passwordInput([
                'maxlength' => true,
                'placeholder' => $model->getAttributeLabel('password'),
                'class' => 'form-control password'
            ]) ?>

            <a href="javascript:void(0)" class="change_password lnk"><?= Yii::t('user', 'Изменить пароль')?></a>

        </div>

    </div>
</div>

<?php ActiveForm::end();

$this->registerJs('
    $(document).ready(function(){
        $(".change_password").click(function(){
            $(".password").toggle();
            var display = $(".password").css("display");
            if (display=="none")
            {
                $(".password").val("");
                $(".change_password").text("'.Yii::t('user', 'Изменить пароль').'");
            }
            else
                $(".change_password").text("'.Yii::t('user', 'Отмена').'");
        });
    });
    ');

?>
