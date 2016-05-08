<?php
/**
 * @package   yii2-user
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */

namespace lowbase\user\controllers;

use lowbase\user\models\UserSearch;
use Yii;
use lowbase\user\models\forms\ProfileForm;
use lowbase\user\models\forms\PasswordResetForm;
use lowbase\user\models\forms\SignupForm;
use lowbase\user\models\User;
use lowbase\user\models\forms\LoginForm;
use lowbase\user\models\EmailConfirm;
use lowbase\user\models\ResetPassword;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\UploadedFile;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * Пользователи
 *
 * Абсолютные пути Views использованы, чтобы при наследовании
 * происходила связь с отображениями модуля родителя.
 *
 * Class UserController
 * @package lowbase\user\controllers
 */
class UserController extends Controller
{
    /**
     * Разделение ролей
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['login', 'signup', 'logout', 'confirm', 'reset', 'profile', 'remove', 'online', 'show',
                    'index', 'view', 'update', 'delete', 'rmv', 'multiactive', 'multiblock', 'multidelete'],
                'rules' => [
                    [
                        'actions' => ['login', 'signup', 'confirm', 'reset', 'show'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['login', 'signup', 'show', 'logout', 'profile', 'remove', 'online'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['userManager'],
                    ],
                    [
                        'actions' => ['view'],
                        'allow' => true,
                        'roles' => ['userView'],
                    ],
                    [
                        'actions' => ['update', 'rmv', 'multiactive', 'multiblock'],
                        'allow' => true,
                        'roles' => ['userUpdate'],
                    ],
                    [
                        'actions' => ['delete', 'multidelete'],
                        'allow' => true,
                        'roles' => ['userDelete'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Регистрация
     * @return string|\yii\web\Response
     */
    public function actionSignup()
    {
        // Уже авторизированных отправляем на домашнюю страницу
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success', Yii::t('user', 'Ссылка с подтверждением регистрации отправлена на Email.'));
            return $this->goBack(['signup']);
        }

        if (method_exists($this->module, 'getCustomView')) {
            return $this->render($this->module->getCustomView('signup', '@vendor/lowbase/yii2-user/views/user/signup'), [
                'model' => $model,
            ]);
        } else {
            return $this->render('@vendor/lowbase/yii2-user/views/user/signup', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Авторизация
     * @return string|\yii\web\Response
     */
    public function actionLogin()
    {
        // Уже авторизированных отправляем на домашнюю страницу
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goHome();
        }

        //Восстановление пароля
        $forget = new PasswordResetForm();
        if ($forget->load(Yii::$app->request->post()) && $forget->validate()) {
            if ($forget->sendEmail()) { // Отправлено подтверждение по Email
                Yii::$app->getSession()->setFlash('reset-success', Yii::t('user', 'Ссылка с активацией нового пароля отправлена на Email.'));
            }
            return $this->goBack(['login']);
        }
        
        if (method_exists($this->module, 'getCustomView')) {
            return $this->render($this->module->getCustomView('login', '@vendor/lowbase/yii2-user/views/user/login'), [
                'model' => $model,
                'forget' => $forget
            ]);
        } else {
            return $this->render('@vendor/lowbase/yii2-user/views/user/login', [
                'model' => $model,
                'forget' => $forget
            ]);
        }
    }

    /**
     * Деавторизация
     * @return \yii\web\Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }

    /**
     * Подтверждение аккаунта с помощью
     * электронной почты
     * @param $token - токен подтверждения, высылаемый почтой
     * @return \yii\web\Response
     * @throws BadRequestHttpException
     */
    public function actionConfirm($token)
    {
        try {
            $model = new EmailConfirm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        if ($user_id = $model->confirmEmail()) {
            // Авторизируемся при успешном подтверждении
            Yii::$app->user->login(User::findIdentity($user_id));
        }

        return $this->redirect(['/']);
    }

    /**
     * Сброс пароля через электронную почту
     * @param $token - токен сброса пароля, высылаемый почтой
     * @param $password - новый пароль
     * @return \yii\web\Response
     * @throws BadRequestHttpException
     */
    public function actionReset($token, $password)
    {
        try {
            $model = new ResetPassword($token, $password);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        if ($user_id = $model->resetPassword()) {
            // Авторизируемся при успешном сбросе пароля
            Yii::$app->user->login(User::findIdentity($user_id));
        }

        return $this->redirect(['/']);
    }

    /**
     * Профиль пользователя (личный кабинет)
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionProfile()
    {
        /** @var \lowbase\user\models\forms\ProfileForm $model */
        $model = ProfileForm::findOne(Yii::$app->user->id);
        if ($model === null) {
            throw new NotFoundHttpException(Yii::t('user', 'Запрошенная страница не найдена.'));
        }
        // Преобразуем дату в понятный формат
        if ($model->birthday) {
            $date = new \DateTime($model->birthday);
            $model->birthday = $date->format('d.m.Y');
        }
        if ($model->load(Yii::$app->request->post())) {
            // Получаем изображение, если оно есть
            $model->photo = UploadedFile::getInstance($model, 'photo');
            if ($model->save()) {
                Yii::$app->getSession()->setFlash('success', Yii::t('user', 'Данные профиля обновлены.'));
                return $this->redirect(['profile']);
            }
        }
        
        if (method_exists($this->module, 'getCustomView')) {
            return $this->render($this->module->getCustomView('profile', '@vendor/lowbase/yii2-user/views/user/profile'), [
                'model' => $model,
            ]);
        } else {
            return $this->render('@vendor/lowbase/yii2-user/views/user/profile', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Пользовательское открытое отображение
     * профиля пользователя
     *
     * @param $id - ID пользователя
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionShow($id)
    {
        $model = $this->findModel($id);

        if (method_exists($this->module, 'getCustomView')) {
            return $this->render($this->module->getCustomView('show', '@vendor/lowbase/yii2-user/views/user/show'), [
                'model' => $model,
            ]);
        } else {
            return $this->render('@vendor/lowbase/yii2-user/views/user/show', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Фиксирование пользователя Онлайн
     * Используется onlineWidget посредством
     * Ajax-запросов
     */
    public function actionOnline()
    {
        User::afterLogin((Yii::$app->request->post('id')));
    }

    /**
     * Удаление собственной аватарки
     * @return \yii\web\Response
     */
    public function actionRemove()
    {
        /** @var \lowbase\user\models\forms\ProfileForm $model */
        $model = ProfileForm::findOne(Yii::$app->user->id);
        if ($model !== null) {
            $model->removeImage();  // Удаление изображения
            Yii::$app->getSession()->setFlash('success', Yii::t('user', 'Изображение удалено.'));
        }

        return $this->redirect(['profile']);
    }

    /**
     * Административная часть
     * ----------------------
     */

    /**
     * Менеджер пользователей (список таблицей)
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('@vendor/lowbase/yii2-user/views/user/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Просмотр пользователя (карточки)
     * @param $id - ID пользователя
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        return $this->render('@vendor/lowbase/yii2-user/views/user/view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Редактирование пользователя в режиме
     * администрирования (по аналогии с личным кабинетом)
     * @param $id - ID пользователя
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        /** @var \lowbase\user\models\forms\ProfileForm $model */
        $model = ProfileForm::findOne($id);
        if ($model === null) {
            throw new NotFoundHttpException(Yii::t('user', 'Запрошенная страница не найдена.'));
        }
        // Преобразуем дату в понятный формат
        if ($model->birthday) {
            $date = new \DateTime($model->birthday);
            $model->birthday = $date->format('d.m.Y');
        }
        if ($model->load(Yii::$app->request->post())) {
            // Загружаем изображение, если оно есть
            $model->photo = UploadedFile::getInstance($model, 'photo');
            if ($model->save()) {
                Yii::$app->getSession()->setFlash('success', Yii::t('user', 'Данные профиля обновлены.'));
                return $this->redirect(['update', 'id' => $id]);
            }
        }

        return $this->render('@vendor/lowbase/yii2-user/views/user/update', [
            'model' => $model
        ]);
    }

    /**
     * Удаление пользователя
     * @param $id - ID пользователя
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        Yii::$app->getSession()->setFlash('success', Yii::t('user', 'Пользователь удален.'));

        return $this->redirect(['index']);
    }

    /**
     * Удаление аватарки пользователя по
     * его ID (чужой аватарки)
     * @param $id - ID пользователя
     * @return \yii\web\Response
     */
    public function actionRmv($id)
    {
        /** @var \lowbase\user\models\forms\ProfileForm $model */
        $model = ProfileForm::findOne($id);
        if ($model !== null) {
            $model->removeImage();
            Yii::$app->getSession()->setFlash('success', Yii::t('user', 'Изображение удалено.'));
        }

        return $this->redirect(['update', 'id' => $id]);
    }

    /**
     * Множественная активация пользователей
     * Перевод в статус STATUS_ACTIVE
     * @return bool
     * @throws NotFoundHttpException
     */
    public function actionMultiactive()
    {
        $models = Yii::$app->request->post('keys');
        if ($models) {
            foreach ($models as $id) {
                if ($id != Yii::$app->user->id) {
                    /** @var \lowbase\user\models\User $model */
                    $model = $this->findModel($id);
                    $model->status = User::STATUS_ACTIVE;
                    $model->save();
                }
            }
            Yii::$app->getSession()->setFlash('success', Yii::t('user', 'Пользователи активированы.'));
        }
        return true;
    }

    /**
     * Множественная блокировка пользователей
     * Перевод в статус STATUS_BLOCKED
     * @return bool
     * @throws NotFoundHttpException
     */
    public function actionMultiblock()
    {
        $models = Yii::$app->request->post('keys');
        if ($models) {
            foreach ($models as $id) {
                if ($id != Yii::$app->user->id) {
                    /** @var \lowbase\user\models\User $model */
                    $model = $this->findModel($id);
                    $model->status = User::STATUS_BLOCKED;
                    $model->save();
                }
            }
            Yii::$app->getSession()->setFlash('success', Yii::t('user', 'Пользователи заблокированы.'));
        }
        return true;
    }

    /**
     * Множественное удаление пользователей
     * @return bool
     * @throws NotFoundHttpException
     */
    public function actionMultidelete()
    {
        /** @var \lowbase\user\models\User $models */
        $models = Yii::$app->request->post('keys');
        if ($models) {
            foreach ($models as $id) {
                if ($id != Yii::$app->user->id) {
                    /** @var \lowbase\user\models\User $user */
                    $user = $this->findModel($id);
                    $user->removeImage(); // Удаление аватарки с сервера
                    $user->delete();
                }
            }
            Yii::$app->getSession()->setFlash('success', Yii::t('user', 'Пользователи удалены.'));
        }
        return true;
    }

    /**
     * Поиск пользователя по ID
     * @param $id - ID пользователя
     * @return null|static
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('user', 'Пользователь не найден.'));
        }
    }
}
