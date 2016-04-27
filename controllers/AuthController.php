<?php
/**
 * @package   yii2-user
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */

namespace lowbase\user\controllers;

use Yii;
use lowbase\user\models\User;
use lowbase\user\models\UserOauthKey;
use yii\web\Controller;

/**
 * Авторизация и регстрация через
 * соц. сети. Прикрепление и открепление
 * ключей авторизации
 * 
 * Class AuthController
 * @package lowbase\user\controllers
 */
class AuthController extends Controller
{
    /**
     * Сохранение адреса для возврата
     * @param \yii\base\Action $action
     * @return bool
     * @throws \yii\web\BadRequestHttpException
     */
    public function beforeAction($action)
    {
        if ($action->id == 'index' && Yii::$app->request->referrer !== null) {
            Yii::$app->session->set('returnUrl', Yii::$app->request->referrer);
        }
        return parent::beforeAction($action);
    }

    /**
     * Авторизация в социальной сети
     * @return array
     */
    public function actions()
    {
        return [
            'index' => [
                'class' => 'yii\authclient\AuthAction',
                'successCallback' => [$this, 'successCallback']
            ],
        ];
    }

    /**
     * Результат успешной авторизации с помощью социальной сети
     * @param $client - социальная сеть, через которую происходит авторизация
     * @return bool
     */
    public function successCallback($client)
    {
        $attributes = $client->userAttributes;

        $this->action->successUrl = Yii::$app->session->get('returnUrl');

        /** @var \lowbase\user\models\UserOauthKey $key */
        $key = UserOauthKey::findOne([
            'provider_id' => $attributes['provider_id'],
            'provider_user_id' => $attributes['provider_user_id']
        ]);

        if ($key) {
            // Ключ авторизации соц. сети найден в базе
            if (Yii::$app->user->isGuest) {
                // Авторзириуемся если Гость
                return Yii::$app->user->login($key->user, 3600 * 24 * 30);
            } else {
                // Запрщаем авторизацию если не свой ключ
                if ($key->user_id != Yii::$app->user->id) {
                    Yii::$app->session->setFlash('error', Yii::t('user', 'Данный ключ уже закреплен за другим пользователем сайта.'));
                    return true;
                }
            }
        } else {
            // Текущего ключа авторизации соц. сети нет в базе
            if (Yii::$app->user->isGuest) {
                $user = false;
                if ($attributes['User']['email'] != null) {
                    // Пытаемся найти пользователя в базе по почте из соц. сети
                    $user = User::findByEmail($attributes['User']['email']);
                }
                if (!$user) {
                    // Не найден пользователь с Email, создаем нового
                    $user = new User;
                    $user->load($attributes);
                    $user->validate();
                    // Сохранение изображения
                    if (file_get_contents($user->photo)) {
                        $user->photo = file_get_contents($user->photo);
                    }
                    return ($user->save() && $this->createKey($attributes, $user->id) && Yii::$app->user->login($user, 3600 * 24 * 30));
                } else {
                    // Найден Email. Добавляем ключ и авторизируемся
                    return ($this->createKey($attributes, $user->id) && Yii::$app->user->login($user, 3600 * 24 * 30));
                }

            } else {
                // Добавляем ключ для авторизированного пользователя
                $this->createKey($attributes, Yii::$app->user->id);
                Yii::$app->session->setFlash('success', Yii::t('user', 'Ключ входа успешно добавлен.'));
                return true;
            }
        }
        return true;
    }

    /**
     * Создание ключа авторизации соц. сети (привязывание)
     * @param $attributes - аттрибуты пользователя
     * @param $user_id - ID пользователя
     * @return bool
     */
    protected function createKey($attributes, $user_id)
    {
        $key = new UserOauthKey;
        $key->provider_id = $attributes['provider_id'];
        $key->provider_user_id = (string) $attributes['provider_user_id'];
        $key->page = (string) $attributes['page'];
        $key->user_id = $user_id;
        return $key->save();
    }

    /**
     * Удлаение ключа авторизации соц. сети (отвзяывание)
     * @param $id - ID ключа авторизации
     * @return \yii\web\Response
     */
    public function actionUnbind($id)
    {
        /** @var \lowbase\user\models\UserOauthKey $key */
        $key = UserOauthKey::findOne(['user_id' => Yii::$app->user->id, 'provider_id' => UserOauthKey::getAvailableClients()[$id]]);
        if (!$key) {
            Yii::$app->session->setFlash('error', Yii::t('user', 'Ключ не найден'));
        } else {
            /** @var \lowbase\user\models\User $user */
            $user = User::findOne($key->user_id);
            if ($user) {
                if (UserOauthKey::isOAuth($user->id)<=1 && $user->email === null) {
                    Yii::$app->session->setFlash('error', Yii::t('user', 'Нельзя отвязать единственную соц. сеть, не заполнив Email'));
                } elseif (UserOauthKey::isOAuth($user->id)<=1 && $user->password_hash === null) {
                    Yii::$app->session->setFlash('error', Yii::t('user', 'Нельзя отвязать единственную соц. сеть, не заполнив пароль'));
                } else {
                    $key->delete();
                    Yii::$app->session->setFlash('success', Yii::t('user', 'Ключ входа удален'));
                }
            }
        }
        return $this->redirect(Yii::$app->request->referrer);
    }
}
