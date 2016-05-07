<?php
/**
 * @package   yii2-user
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */

/* @var $this yii\web\View */

use yii\helpers\Html;
use lowbase\user\components\AuthChoice;
use yii\widgets\ActiveForm;
use lowbase\user\UserAsset;

$this->title = Yii::t('user', 'Вход на сайт');
$this->params['breadcrumbs'][] = $this->title;
UserAsset::register($this);
?>

<div class="site-login row" id="filter">

    <?php
        if (method_exists(\Yii::$app->controller->module, 'getCustomView')) {
            echo $this->render(\Yii::$app->controller->module->getCustomView('repass', '_pass'), [
                'model' => $forget,
            ]);
        } else {
            echo $this->render('_pass', [
                'model' => $forget,
            ]);
        }
    ?>
    
    <div class="col-lg-6">

        <h1><?= Html::encode($this->title) ?></h1>

        <?php $form = ActiveForm::begin([
            'id' => 'login-form',
            'fieldConfig' => [
                'template' => "{input}\n{hint}\n{error}"
            ],
        ]); ?>

        <?= $form->field($model, 'email')->textInput([
            'maxlength' => true,
            'placeholder' => $model->getAttributeLabel('email')
        ]); ?>

        <?= $form->field($model, 'password')->passwordInput([
            'maxlength' => true,
            'placeholder' => $model->getAttributeLabel('password')
        ]);?>

        <?= $form->field($model, 'rememberMe')->checkbox() ?>

        <p class="hint-block">
            <?=Yii::t('user', 'Если')?> <?=Html::a(Yii::t('user', 'регистрировались'), ['signup'])?>
            <?=Yii::t('user', 'ранее, но забыли пароль, нажмите')?>
            <?=Html::a(Yii::t('user', 'восстановить пароль'), ['#'], [
                'data-toggle' => 'modal',
                'data-target' => '#pass',
            ])?>.
        </p>

        <div class="form-group">
            <?= Html::submitButton('<i class="glyphicon glyphicon-log-in"></i> '.Yii::t('user', 'Войти'), [
                'class' => 'btn btn-lg btn-primary',
                'name' => 'login-button']) ?>
        </div>

        <?php ActiveForm::end(); ?>

        <p class="hint-block"><?=Yii::t('user', 'Войти с помощью социальных сетей')?>:</p>

        <div class="text-center" style="text-align: center">
            <?= AuthChoice::widget([
                'baseAuthUrl' => ['/lowbase-user/auth/index'],
            ]) ?>
        </div>
    </div>
    <div class="col-lg-6">
    </div>
</div>
