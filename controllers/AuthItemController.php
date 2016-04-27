<?php
/**
 * @package   yii2-user
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */

namespace lowbase\user\controllers;

use Yii;
use lowbase\user\models\AuthItem;
use lowbase\user\models\AuthItemSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;

/**
 * Роли и допуски
 * 
 * Абсолютные пути Views использованы, чтобы при наследовании
 * происходила связь с отображениями модуля родителя.
 * 
 * Class AuthItemController
 * @package lowbase\user\controllers
 */
class AuthItemController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'view', 'create', 'update', 'delete', 'multidelete'],
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['roleManager'],
                    ],
                    [
                        'actions' => ['view'],
                        'allow' => true,
                        'roles' => ['roleView'],
                    ],
                    [
                        'actions' => ['update'],
                        'allow' => true,
                        'roles' => ['roleUpdate'],
                    ],
                    [
                        'actions' => ['create'],
                        'allow' => true,
                        'roles' => ['roleCreate'],
                    ],
                    [
                        'actions' => ['delete', 'multidelete'],
                        'allow' => true,
                        'roles' => ['roleDelete'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Менеджер ролей / допусков
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new AuthItemSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        return $this->render('@vendor/lowbase/yii2-user/views/auth-item/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Отображение роли / допуска
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        return $this->render('@vendor/lowbase/yii2-user/views/auth-item/view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Создание роли / допуска
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new AuthItem();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $message = ($model->type == 1) ? Yii::t('user', 'Роль создана') : Yii::t('user', 'Допуск создан');
            Yii::$app->getSession()->setFlash('success', $message);
            return $this->redirect(['view', 'id' => $model->name]);
        } else {
            return $this->render('@vendor/lowbase/yii2-user/views/auth-item/create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Редактирование роли / допуска
     * @param $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->fill(); // Заполнение дочерних ролей / допусков и пользователей, владеющих ими

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $message = ($model->type == 1) ? Yii::t('user', 'Роль отредактирована') : Yii::t('user', 'Допуск отредактирован');
            Yii::$app->getSession()->setFlash('success', $message);
            return $this->redirect(['view', 'id' => $model->name]);
        } else {
            return $this->render('@vendor/lowbase/yii2-user/views/auth-item/update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Удаление роли / допуска
     * @param $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $message = ($model->type == 1) ? Yii::t('user', 'Роль удалена') : Yii::t('user', 'Допуск удален');
        $model->delete();
        Yii::$app->getSession()->setFlash('success', $message);

        return $this->redirect(['index']);
    }

    /**
     * Множественное удаление ролей / допусков
     * @return bool
     * @throws NotFoundHttpException
     */
    public function actionMultidelete()
    {
        $models = Yii::$app->request->post('keys');
        if ($models) {
            foreach ($models as $id) {
                $this->findModel($id)->delete();
            }
            Yii::$app->getSession()->setFlash('success', Yii::t('user', 'Роли / допуски удалены'));
        }
        return true;
    }

    /**
     * Поиск модели (роль / допуск) по ID
     * @param string $id
     * @return AuthItem the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AuthItem::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('user', 'Запрашиваемая страница не найдена.'));
        }
    }
}
