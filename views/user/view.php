<?php
/**
 * @package   yii2-user
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */

use lowbase\user\models\User;
use lowbase\user\models\UserOauthKey;
use yii\helpers\Html;
use yii\widgets\DetailView;
use lowbase\user\UserAsset;
use yii\authclient\widgets\AuthChoiceStyleAsset;

/* @var $this yii\web\View */
/* @var $model lowbase\user\models\User */

$this->title = Yii::t('user', 'Просмотр пользователя');
$this->params['breadcrumbs'][] = ['label' => Yii::t('user', 'Пользователи'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$assets = UserAsset::register($this);
AuthChoiceStyleAsset::register($this);
?>
<div class="user-view">

    <div class="row">
        <div class="col-lg-12">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>
    </div>

    <p>
        <?= Html::a('<i class="glyphicon glyphicon-pencil"></i> '.Yii::t('user', 'Редактировать'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="glyphicon glyphicon-trash"></i> '.Yii::t('user', 'Удалить'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('user', 'Вы уверены, что хотите удалить пользователя?'),
                'method' => 'post',
            ],
        ]) ?>
        <?= Html::a('<i class="glyphicon glyphicon-menu-left"></i> '.Yii::t('user', 'Отмена'), ['index'], [
            'class' => 'btn btn-default',
        ]) ?>
    </p>

    <?php
    $keys = '';
    if ($model->keys) {
        $keys .= "<div class='row'>";
        foreach ($model->keys as $key) {
            $services = array_flip(UserOauthKey::getAvailableClients());
            $keys .= "<div class='col-xs-1'><a href='" . UserOauthKey::getSites()[$key->provider_id] . $key->page . "'><span class='auth-icon " . $services[$key->provider_id] . "'></span></a></div>";
        }
        $keys .= "</div>";
    }
    $roles = '';
    if ($model->authAssignments) {
        foreach ($model->authAssignments as $role) {
            $type = ($role->itemName->type == 1) ? 'label-primary' : 'label-success';
            $roles .= Html::a('<span class="label ' . $type . '">' . $role->itemName->description . '</span>', ['auth-item/view', 'id' => $role->itemName->name]) . " ";
        }
    }
    ?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'first_name',
            'last_name',
            'email:email',
            [
                'attribute' => 'sex',
                'value' => ($model->sex)? User::getSexArray()[$model->sex]:null,
            ],
            [
                'attribute' => 'birthday',
                'format' =>  ['date', 'dd.MM.Y'],
            ],
            'phone',
            [
                'attribute' => 'country_id',
                'value' => (isset($model->country)) ? $model->country->name : null,
            ],
            [
                'attribute' => 'city_id',
                'value' => (isset($model->city)) ? $model->city->city . " (" .
                    $model->city->state . " " . $model->city->region . ")" : null,
            ],
            'address',
            [
                'attribute' => 'status',
                'value' => User::getStatusArray()[$model->status],
            ],
            'ip',
            [
                'attribute' => 'created_at',
                'format' =>  ['date', 'dd.MM.Y HH:mm:ss'],
            ],
            [
                'attribute' => 'updated_at',
                'format' =>  ['date', 'dd.MM.Y HH:mm:ss'],
            ],
            [
                'attribute' => 'login_at',
                'format' =>  ['date', 'dd.MM.Y HH:mm:ss'],
            ],
            [
                'attribute' => 'image',
                'format' => 'raw',
                'value' => ($model->image)?'<img src="/'.$model->image.'" class="thumbnail">':null
            ],
            [
                'attribute' => Yii::t('user', 'Социальные сети'),
                'format' => 'raw',
                'value' => ($keys) ? $keys : null,
            ],
            [
                'attribute' => Yii::t('user', 'Обладает ролями и допусками'),
                'format' => 'raw',
                'value' => ($roles) ? $roles : null,
            ],
        ],
    ]) ?>

</div>
