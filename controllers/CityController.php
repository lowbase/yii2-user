<?php
/**
 * @package   yii2-user
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */

namespace lowbase\user\controllers;

use lowbase\user\models\CitySearch;
use Yii;
use lowbase\user\models\City;
use yii\db\Query;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;

/**
 * Города
 * 
 * Абсолютные пути Views использованы, чтобы при наследовании
 * происходила связь с отображениями модуля родителя.
 * 
 * Class CityController
 * @package lowbase\user\controllers
 */
class CityController extends Controller
{
    /**
     * @inheritdoc
     */
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
                        'roles' => ['cityManager'],
                    ],
                    [
                        'actions' => ['view'],
                        'allow' => true,
                        'roles' => ['cityView'],
                    ],
                    [
                        'actions' => ['update'],
                        'allow' => true,
                        'roles' => ['cityUpdate'],
                    ],
                    [
                        'actions' => ['create'],
                        'allow' => true,
                        'roles' => ['cityCreate'],
                    ],
                    [
                        'actions' => ['delete', 'multidelete'],
                        'allow' => true,
                        'roles' => ['cityDelete'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Поиск населенного пункта по названию или ID
     * @param null $q - часть названия или целиком
     * @param null $id - ID населенного пункта ( в случае поиска по ID
     * @return array [ID => 'Город (Район, Регион)']
     */
    public function actionFind($q = null, $id = null)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        
        if (!is_null($q)) {
            $query = new Query();
            $query->select('id, city, state, region')
                ->from('lb_city')
                ->where(['like', 'city', $q])
                ->limit(1000);
            
            $command = $query->createCommand();
            $data = $command->queryAll();
            $format_data = [];
            
            foreach ($data as $d) {
                $format_data[] = [
                    'id' => $d['id'],
                    'text' => $d['city'] . " (" .
                        $d['state'] . ", " .
                        $d['region'] . ")"];
            }
            
            $out['results'] = array_values($format_data);
        } elseif ($id > 0) {
            $out['results'] = ['id' => $id, 'text' => City::find($id)->city];
        }
        
        return $out;
    }

    /**
     * Менеджер населенных пунктов (вывод таблицей)
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new CitySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('@vendor/lowbase/yii2-user/views/city/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Отображение населенного пункта
     * @param $id - ID населенного пункта
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        return $this->render('@vendor/lowbase/yii2-user/views/city/view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Создание населенного пункта
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new City();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success', Yii::t('user', 'Новый город создан.'));
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('@vendor/lowbase/yii2-user/views/city/create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Редактирование населенного пункта
     * @param $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success', Yii::t('user', 'Город отредактирован.'));
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('@vendor/lowbase/yii2-user/views/city/update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Удаление населенного пункта
     * @param $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        Yii::$app->getSession()->setFlash('success', Yii::t('user', 'Город удален.'));

        return $this->redirect(['index']);
    }

    /**
     * Множественное удаление населенных пунктов
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
            Yii::$app->getSession()->setFlash('success', Yii::t('user', 'Города удалены.'));
        }
        
        return true;
    }

    /**
     * Поиск модели (насленного пункта) по ID
     * @param integer $id
     * @return City the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = City::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('user', 'Запрашиваемая страница не найдена.'));
        }
    }
}
