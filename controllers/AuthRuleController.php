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
 * Правила допусков
 * 
 * Абсолютные пути Views использованы, чтобы при наследовании
 * происходила связь с отображениями модуля родителя.
 * 
 * Class AuthRuleController
 * @package lowbase\user\controllers
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
     * Менеджер правил (список таблицей)
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new AuthRuleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('@vendor/lowbase/yii2-user/views/auth-rule/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Отображение правила
     * @param $id - ID правила
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        return $this->render('@vendor/lowbase/yii2-user/views/auth-rule/view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Создание правила
     * @return string|\yii\web\Response
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
     * Удаление правила
     * @param $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
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
     * Поиск модели правил по ID
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
