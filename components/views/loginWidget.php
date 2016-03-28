<?php
/**
 * @package   yii2-user
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */

use yii\helpers\Html;
use lowbase\user\components\AuthChoice;
use yii\widgets\ActiveForm;
use lowbase\user\UserAsset;

/* @var $model \lowbase\user\models\forms\LoginForm */
/* @var $oauth boolean */

UserAsset::register($this);
?>

<div class="lb-user-module-login-widget">

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

    <div class="form-group">
        <?= Html::submitButton('<i class="glyphicon glyphicon-log-in"></i> '.Yii::t('user', 'Войти'), [
            'class' => 'btn btn-lg btn-primary',
            'name' => 'login-button']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <?php if ($oauth) { ?>

        <p class="hint-block"><?=Yii::t('user', 'Войти с помощью социальных сетей')?>:</p>

        <div class="text-center" style="text-align: center">
            <?= AuthChoice::widget([
                'baseAuthUrl' => ['/user/auth/index'],
            ]) ?>
        </div>

    <?php } ?>

</div>
