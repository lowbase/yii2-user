Модуль пользователей
====================

Yii2-user - независимый модуль из комплекта lowBase с панелью администрирования и полным функционалом возможностей.

* Авторизация и регистрация по Email (с подтверждением)
* Авторизация и регистрация через социальные сети
* Восстановление пароля через Email
* Максимально полный профиль пользователя
* Администрирование пользователей и расширенный поиск по параметрам
* Страны и города (База Вконтакте) и привязка профиля к ним
* Разделение ролей и управление ими с помощью системы допусков, наследования и правил
* Виджеты авторизации, фиксирования Online, привязка профиля к социальным сетям (для последующего входа)
* Поддержка мультиязычности
* Поддержка кастомных отображений и шаблонов писем без наследований

Установка
---------
```
php composer.phar require --prefer-dist lowbase/yii2-user "*"
```
или
```
"lowbase/yii2-user": "*"
```

Настройка конфигурационного файла
---------------------------------

```
//-----------------------
// Компонент пользователя
//-----------------------

'user' => [
    'identityClass' => 'app\modules\user\models\User',
    'enableAutoLogin' => true,
    'loginUrl' => ['/login'],
    'on afterLogin' => function($event) {
        app\modules\user\models\User::afterLogin($event->identity->id);
    }
],

//--------------------------------------------------------
// Компонент OAUTH для авторизации через социальные сети,
// где вмето ? указываем полученные после регистрации
// клиентский ID и секретный ключ.
// В комментария указаны ссылки для регистрации приложений
// в соответствующих социальных сетях.
//--------------------------------------------------------

'authClientCollection' => [
   'class' => 'yii\authclient\Collection',
   'clients' => [
       'vkontakte' => [
           // https://vk.com/editapp?act=create
           'class' => 'app\modules\user\components\oauth\VKontakte',
           'clientId' => '?',
           'clientSecret' => '?',
           'scope' => 'email'
       ],
       'google' => [
           // https://console.developers.google.com/project
           'class' => 'app\modules\user\components\oauth\Google',
           'clientId' => '?',
           'clientSecret' => '?',
       ],
       'twitter' => [
            // https://dev.twitter.com/apps/new
           'class' => 'app\modules\user\components\oauth\Twitter',
           'consumerKey' => '?',
           'consumerSecret' => '?',
       ],
       'facebook' => [
            // https://developers.facebook.com/apps
           'class' => 'app\modules\user\components\oauth\Facebook',
           'clientId' => '?',
           'clientSecret' => '?',
       ],
       'github' => [
            // https://github.com/settings/applications/new
           'class' => 'app\modules\user\components\oauth\GitHub',
           'clientId' => '?',
           'clientSecret' => '?',
           'scope' => 'user:email, user'
       ],
       'yandex' => [
            // https://oauth.yandex.ru/client/new
           'class' => 'app\modules\user\components\oauth\Yandex',
           'clientId' => '?',
           'clientSecret' => '?',
       ],
   ],
],

//---------------------------------------------
// Для реализации разделения прав пользователей
// с помощью коробочного модуля Yii2 RBAC.
//---------------------------------------------

'authManager' => [
   'class' => 'yii\rbac\DbManager',
],

//-------------------------------------------------
// Прописываем правила роутинга для соответствующих
// действий с модулем в приложении.
//-------------------------------------------------

'urlManager' => [
   'enablePrettyUrl' => true,
   'showScriptName' => false,
   'rules' => [
       //Взаимодействия с пользователем на сайте
       '<action:(login|signup|confirm|reset|profile|remove|online)>' => 'user/user/<action>',
       //Взаимодействия с пользователем в панели админстрирования
       'admin/user/<action:(index|update|delete|view|rmv|multidelete|multiactive|multiblock)>' => 'user/user/<action>',
       //Авторизация через социальные сети
       'auth/<authclient:[\w\-]+>' => 'user/auth/index',
       //Просмотр пользователя
       'user/<id:\d+>' => 'user/user/show',
       //Взаимодействия со странами в панели админстрирования
       'admin/country/<action:(index|create|update|delete|view|multidelete)>' => 'user/country/<action>',
       //Поиск населенного пункта (города)
       'city/find' => 'user/city/find',
       //Взаимодействия с городами в панели администрирования
       'admin/city/<action:(index|create|update|delete|view|multidelete)>' => 'user/city/<action>',
       //Работа с ролями и разделением прав доступа
       'admin/role/<action:(index|create|update|delete|view|multidelete)>' => 'user/auth-item/<action>',
       //Работа с правилами контроля доступа
       'admin/rule/<action:(index|create|update|delete|view|multidelete)>' => 'user/auth-rule/<action>',
   ],
],

//-----------------------
// Подключаем сами модули
//-----------------------

'modules' => [
   'gridview' =>  [
       'class' => '\kartik\grid\Module'
   ],
   'user' => [
       'class' => '\app\modules\user\Module',
   ],
],
```
Создание таблиц БД
------------------
Запускаем миграции командой:
```
yii migrate/up --migrationPath=@vendor/lowbase/yii2-users/migrations
```
Миграции создают необходимые таблицы и заполняют их предустановленными значениями
по минимуму. Изначально установлены 2 роли: Администратор и модератор. Если хотите
изменить эти данные, то следует скорректировать их в файле миграций перед запуском
```
//Администратор по умолчанию
const ADMIN_FIRST_NAME = 'Имя_администратора';
const ADMIN_LAST_NAME = 'Фамилия_администратора';
const ADMIN_EMAIL = 'admin@example.ru';
const ADMIN_PASSWORD = 'admin';

//Модератор по умолчанию
const MODERATOR_FIRST_NAME = 'Имя_модератора';
const MODERATOR_LAST_NAME = 'Фамилия_модератора';
const MODERATOR_EMAIL = 'moderator@example.ru';
const MODERATOR_PASSWORD = 'moderator';
```
Таблицы страны и города будут пустыми. Если хотите заполнить их записями из базы данных Вконтакте, то
для этого импортируйте в базу файл из папки с миграциями:
```
dump_city.sql
```

Пользовательское отображение страниц модуля
-------------------------------------------
```
'modules' => [
    'user' => [
        'class' => '\app\modules\user\Module',
        'customViews' => [
            // Меняем стандартное отображение профиля
            'login' => '@app/views/user/profile'
        ]
    ],
],
// Допустимые отображения для кастомизации:
//
// signup - регистрация
// login - авторизация
// profile - профиль
// alert - сообщения
// repass - восстановление пароля
// show - просмотр пользователя
// confirmEmail - шаблон письма подтверждения Email
// passwordResetToken - шаблон письма сброса пароля
```

Запуск виджетов
---------------
```
// Виджет авторизации (с отключеными иконками соц. сетей)
<?= LoginWidget::widget(['oauth' => false]) ?>

// Виджет проверяющий пользователя Online (с установкой времени опроса 300 секунд)
<?= OnlineWidget::widget(['time' => 300]) ?>

// Виджет авторизации через социальные сети
<?= AuthChoice::widget([['baseAuthUrl' => ['/user/auth/index']]]) ?>

// Виджет привязки социальных сетей к профилю
 <?= AuthKeysManager::widget(['baseAuthUrl' => ['/user/auth/index']]) ?>
```




