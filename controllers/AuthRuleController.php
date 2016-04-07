<?php
/**
 * @package   yii2-user
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */

namespace lowbase\user\controllers;

use Yii;
use lowbase\user\models\AuthRule;
use lowbase\user\models\AuthRuleSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;

/**
 * AuthRuleController implements the CRUD actions for AuthRule model.
 * Абсолютные пути Views использованы, чтобы при наследовании
 * происходила связь с отображениями модуля родителя.
 */
class AuthRuleController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'view', 'create', 'delete', 'multidelete'],
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['ruleManager'],
                    ],
                    [
                        'actions' => ['view'],
                        'allow' => true,
                        'roles' => ['ruleView'],
                    ],
                    [
                        'actions' => ['create'],
                        'allow' => true,
                        'roles' => ['ruleCreate'],
                    ],
                    [
                        'actions' => ['delete', 'multidelete'],
                        'allow' => true,
                        'roles' => ['ruleDelete'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all AuthRule models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AuthRuleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single AuthRule model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('@vendor/lowbase/yii2-user/views/auth-rule/view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new AuthRule model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AuthRule();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success', Yii::t('user', 'Правило контроля доступа создано.'));
            return $this->redirect(['view', 'id' => $model->name]);
        } else {
            return $this->render('@vendor/lowbase/yii2-user/views/auth-rule/create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing AuthRule model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        Yii::$app->getSession()->setFlash('success', Yii::t('user', 'Правило контроля доступа удалено.'));

        return $this->redirect(['index']);
    }


    /**
     * Множественное удаление правил
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
            Yii::$app->getSession()->setFlash('success', Yii::t('user', 'Правила контроля доступа удалены.'));
        }
        return true;
    }

    /**
     * Finds the AuthRule model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return AuthRule the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AuthRule::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('user', 'Запрашиваемая страница не найдена.'));
        }
    }
}
