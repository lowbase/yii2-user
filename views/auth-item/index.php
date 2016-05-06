<?php
/**
 * @package   yii2-user
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */

use lowbase\user\models\AuthItem;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;
use lowbase\user\UserAsset;

/* @var $this yii\web\View */
/* @var $searchModel lowbase\user\models\AuthItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('user', 'Роли и допуски');
$this->params['breadcrumbs'][] = $this->title;
$assets = UserAsset::register($this);

?>
<div class="auth-item-index">

    <?php
    $gridColumns = [
        [
            'class' => 'kartik\grid\SerialColumn',
            'contentOptions' => ['class'=>'kartik-sheet-style'],
            'width' => '30px',
            'header' => '',
            'headerOptions' => ['class' => 'kartik-sheet-style']
        ],
        'name',
        [
            'attribute' => 'type',
            'vAlign' => 'middle',
            'format' => 'raw',
            'value' => function ($model) {
                switch ($model->type) {
                    case 1:
                        return '<span class="label label-primary">'. AuthItem::getTypes()[$model->type].'</span>';
                        break;
                    case 2:
                        return '<span class="label label-success">'. AuthItem::getTypes()[$model->type].'</span>';
                        break;
                }
                return false;
            },
            'filter' => AuthItem::getTypes()
        ],
        'description:ntext',
        'rule_name',
        'data:ntext',
//         'created_at',
//         'updated_at',
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
            'heading' => '<i class="glyphicon glyphicon-eye-close"></i> '.Yii::t('user', 'Роли и допуски'),
            'type' => GridView::TYPE_PRIMARY,
            'before' => Html::a('<span class="glyphicon glyphicon-plus"></span> '.Yii::t('user', 'Добавить'), [
                'auth-item/create'], ['class' => 'btn btn-success']),
            'after' => "<div class='text-right'><b>".Yii::t('user', 'Выбранные').":</b> ".
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
            url: "' . Url::to(['auth-item/multidelete']) . '",
            type:"POST",
            data:{keys: keys},
            success: function(data){
                location.reload();
            }
            });
        });
    ');
?>

