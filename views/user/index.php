<?php
/**
 * @package   yii2-user
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */

use lowbase\user\models\User;
use yii\helpers\Html;
use kartik\date\DatePicker;
use kartik\grid\GridView;
use yii\helpers\Url;
use lowbase\user\UserAsset;

/* @var $this yii\web\View */
/* @var $searchModel lowbase\user\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('user', 'Менеджер пользователей');
$this->params['breadcrumbs'][] = $this->title;
UserAsset::register($this);
?>

<div class="user-index">

    <?= $this->render('_search', ['model' => $searchModel]);?>

    <?php
    $gridColumns = [
        [
            'class' => 'kartik\grid\SerialColumn',
            'contentOptions' => ['class'=>'kartik-sheet-style'],
            'width' => '30px',
            'header' => '',
            'headerOptions' => ['class' => 'kartik-sheet-style']
        ],
        [
            'attribute' => 'id',
            'width' => '70px',
        ],
        'first_name',
        'last_name',
        'email:email',
        [
            'attribute' => 'created_at',
            'format' =>  ['date', 'dd.MM.Y HH:mm:ss'],
            'width'=>'200px',
            'filter' => DatePicker::widget([
                'value'=> isset($_GET['UserSearch']['created_at'])?$_GET['UserSearch']['created_at']:'',
                'name' => 'UserSearch[created_at]',
                'type' => DatePicker::TYPE_COMPONENT_APPEND,
                'pluginOptions' => [
                    'format' => 'dd.mm.yyyy',
                    'todayHighlight' => true
                ]
            ])
        ],
        [
            'attribute' => 'status',
            'vAlign' => 'middle',
            'format' => 'raw',
            'value' => function ($model) {
                switch ($model->status) {
                    case User::STATUS_BLOCKED:
                        return '<span class="label label-danger">
                            <i class="glyphicon glyphicon-lock"></i> '.User::getStatusArray()[User::STATUS_BLOCKED].'</span>';
                        break;
                    case User::STATUS_WAIT:
                        return '<span class="label label-warning">
                            <i class="glyphicon glyphicon-hourglass"></i> '.User::getStatusArray()[User::STATUS_WAIT].'</span>';
                        break;
                    case User::STATUS_ACTIVE:
                        return '<span class="label label-success">
                            <i class="glyphicon glyphicon-ok"></i> '.User::getStatusArray()[User::STATUS_ACTIVE].'</span>';
                        break;
                }
                return false;
            },
            'filter' => User::getStatusArray(),
        ],
        [
            'template' => '{view} {update} {delete}',
            'class'=>'kartik\grid\ActionColumn',
        ],
        [
            'class'=>'kartik\grid\CheckboxColumn',
            'headerOptions'=>['class'=>'kartik-sheet-style'],
        ],
    ];

    echo GridView::widget([
        'layout'=>"{items}\n{summary}\n{pager}",
        'dataProvider'=> $dataProvider,
        'filterModel' => $searchModel,
        'columns' => $gridColumns,
        'containerOptions' => ['style'=>'overflow: auto'],
        'headerRowOptions' => ['class'=>'kartik-sheet-style'],
        'filterRowOptions' => ['class'=>'kartik-sheet-style'],
        'pjax' => false,
        'panel' => [
            'heading' => '<i class="glyphicon glyphicon-user"></i> '.Yii::t('user', 'Пользователи'),
            'type' => GridView::TYPE_PRIMARY,
            'before' => Html::button('<span class="glyphicon glyphicon-search"></span> '.Yii::t('user', 'Поиск'), [
                    'class' => 'filter btn btn-default',
                    'data-toggle' => 'modal',
                    'data-target' => '#filter',
                ]),
            'after' => "<div class='text-right'><b>".Yii::t('user', 'Выбранные').":</b> ".
                Html::button('<span class="glyphicon glyphicon-eye-open"></span> '.Yii::t('user', 'Активировать'), [
                    'class' => 'btn btn-default open-all'])." ".
                Html::button('<span class="glyphicon glyphicon-eye-close"></span> '.Yii::t('user', 'Заблокировать'), [
                    'class' => 'btn btn-default close-all'])." ".
                Html::button('<span class="glyphicon glyphicon-trash"></span> '.Yii::t('user', 'Удалить'), [
                    'class' => 'btn btn-danger delete-all']).
                "</div>"
        ],
        'export' => [
            'fontAwesome' => true
        ],
        'bordered' => true,
        'striped' => true,
        'condensed' => true,
        'persistResize' => false,
        'hover' => true,
        'responsive' => true,
    ]);
    ?>

</div>

<?php
$this->registerJs('
        $(".delete-all").click(function(){
        var keys = $(".grid-view").yiiGridView("getSelectedRows");
        $.ajax({
            url: "' . Url::to(['user/multidelete']) . '",
            type:"POST",
            data:{keys: keys},
            success: function(data){
                location.reload();
            }
            });
        });
        $(".open-all").click(function(){
        var keys = $(".grid-view").yiiGridView("getSelectedRows");
        $.ajax({
            url: "' . Url::to(['user/multiactive']) . '",
            type:"POST",
            data:{keys: keys},
            success: function(data){
                location.reload();
            }
            });
        });
        $(".close-all").click(function(){
        var keys = $(".grid-view").yiiGridView("getSelectedRows");
        $.ajax({
            url: "' . Url::to(['user/multiblock']) . '",
            type:"POST",
            data:{keys: keys},
            success: function(data){
                location.reload();
            }
            });
        });
    ');

