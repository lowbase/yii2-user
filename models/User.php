<?php
/**
 * @package   yii2-user
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */

namespace lowbase\user\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\base\NotSupportedException;
use yii\imagine\Image;
use yii\web\IdentityInterface;
use yii\db\ActiveRecord;

/**
 * Класс пользователей
 *
 * @property integer $id
 * @property string $first_name
 * @property string $last_name
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email_confirm_token
 * @property string $email
 * @property string $image
 * @property integer $sex
 * @property string $birthday
 * @property string $phone
 * @property integer $country_id
 * @property integer $city_id
 * @property string $address
 * @property integer $status
 * @property string $ip
 * @property string $created_at
 * @property string $updated_at
 * @property string $login_at
 *
 * @property AuthAssignment[] $authAssignments
 * @property AuthItem[] $itemNames
 * @property UserOauthKey[] $userOauthKeys
 */
class User extends ActiveRecord implements IdentityInterface
{
    // Статусы пользователя
    const STATUS_BLOCKED = 0;   // заблокирован
    const STATUS_ACTIVE = 1;    // активен
    const STATUS_WAIT = 2;      // ожидает подтверждения

    // Гендерные статусы
    const SEX_MALE = 1;     // мужчина
    const SEX_FEMALE = 2;   // женщина

    // Время действия токенов
    const EXPIRE = 3600;

    // Расширение сохраняемого файла изобрежния
    // Определение Mime не предусмотрено. Файлы
    // изобрежния в соц. сетях часто без расширения в
    // названиях
    const EXT = '.jpg';

    public $photo;  // аватар (само изображение)

    /**
     * @return string
     */
    public static function tableName()
    {
        return 'lb_user';
    }

    /**
     * Автозаполнение полей создание и редактирование
     * профиля
     * @return array
     */
    public function behaviors()
    {
        return [[
            'class' => TimestampBehavior::className(),
            'createdAtAttribute' => 'created_at',
            'updatedAtAttribute' => 'updated_at',
            'value' => date('Y-m-d H:i:s'),
        ]];
    }

    /**
     * Статусы пользователя
     * @return array
     */
    public static function getStatusArray()
    {
        return [
            self::STATUS_BLOCKED => Yii::t('user', 'Заблокирован'),
            self::STATUS_ACTIVE => Yii::t('user', 'Активен'),
            self::STATUS_WAIT =>  Yii::t('user', 'Не активен'),
        ];
    }

    /**
     * Гендерный список
     * @return array
     */
    public static function getSexArray()
    {
        return [
            self::SEX_MALE =>  Yii::t('user', 'Мужской'),
            self::SEX_FEMALE => Yii::t('user', 'Женский'),
        ];
    }

    /**
     * Правила валидации модели
     * @return array
     */
    public function rules()
    {
        return [
            [['first_name'], 'required'],   // Имя обязательно для заполнения
            [['sex', 'country_id', 'city_id', 'status'], 'integer'],    // Только целочисленные значения
            [['birthday', 'login_at'], 'safe'], // Безопасные аттрибуты (любые значения) - преобразуются автоматически
            [['first_name', 'last_name', 'email', 'phone'], 'string', 'max' => 100],    // Строки до 100 символов
            [['auth_key', 'ip'], 'string', 'max' => 32],  // Строка до 32 символов
            [['password_hash', 'password_reset_token', 'email_confirm_token', 'image', 'address'], 'string', 'max' => 255], // Строки до 255 символов
            ['status', 'in', 'range' => array_keys(self::getStatusArray())],    // Статус возможен только из списка статусов
            ['status', 'default', 'value' => self::STATUS_ACTIVE],  // По умолчанию статус "Активен"
            ['sex', 'in', 'range' => array_keys(self::getSexArray())],  // Пол только из гендерного списка
            ['email', 'email'], // Формат электронной почты
            ['email', 'unique', 'targetClass' => self::className(),
                'message' => Yii::t('user', 'Данный Email уже зарегистрирован.')],  // Уникальный почтовый ящик
            [['first_name', 'last_name', 'email', 'phone', 'address'], 'filter', 'filter' => 'trim'],   // Обрезаем строки по краям
            [['last_name', 'password_reset_token', 'email_confirm_token',
                'image', 'sex', 'phone', 'country_id', 'city_id', 'address',
                'auth_key', 'password_hash', 'email', 'ip', 'login_at'], 'default', 'value' => null],   // По умолчанию Null
            [['photo'], 'image',
                'minHeight' => 100,
                'skipOnEmpty' => true
            ],  // Изображение не менее 100 пикселей в высоту
        ];
    }

    /**
     * Наименования полей модели
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('user', 'ID'),
            'first_name' => Yii::t('user', 'Имя'),
            'last_name' => Yii::t('user', 'Фамилия'),
            'auth_key' => Yii::t('user', 'Ключ авторизации'),
            'password_hash' => Yii::t('user', 'Хеш пароля'),
            'password_reset_token' => Yii::t('user', 'Токен восстановления пароля'),
            'email_confirm_token' => Yii::t('user', 'Токен подтвердждения Email'),
            'email' =>  Yii::t('user', 'Email'),
            'image' => Yii::t('user', 'Фото'),
            'photo' => Yii::t('user', 'Фото'),
            'sex' => Yii::t('user', 'Пол'),
            'birthday' => Yii::t('user', 'Дата рождения'),
            'phone' => Yii::t('user', 'Телефон'),
            'country_id' => Yii::t('user', 'Страна'),
            'city_id' => Yii::t('user', 'Город'),
            'address' => Yii::t('user', 'Адрес'),
            'status' => Yii::t('user', 'Статус'),
            'ip' => Yii::t('user', 'IP'),
            'created_at' => Yii::t('user', 'Создан'),
            'updated_at' => Yii::t('user', 'Обновлен'),
            'login_at' => Yii::t('user', 'Авторизован'),
        ];
    }

    /**
     * Сязи пользователь => роль
     * @return \yii\db\ActiveQuery
     */
    public function getAuthAssignments()
    {
        return $this->hasMany(AuthAssignment::className(), ['user_id' => 'id']);
    }

    /**
     * Роли и допуски (разрешения)
     * @return \yii\db\ActiveQuery
     */
    public function getItemNames()
    {
        return $this->hasMany(AuthItem::className(), ['name' => 'item_name'])->viaTable('lb_auth_assignment', ['user_id' => 'id']);
    }

    /**
     * Ключи авторизации соц. сетей и страницы соц. сетей
     * @return \yii\db\ActiveQuery
     */
    public function getKeys()
    {
        return $this->hasMany(UserOauthKey::className(), ['user_id' => 'id']);
    }

    /**
     * Страна
     * @return \yii\db\ActiveQuery
     */
    public function getCountry()
    {
        return $this->hasOne(Country::className(), ['id' => 'country_id']);
    }


    /**
     * Город
     * @return \yii\db\ActiveQuery
     */
    public function getCity()
    {
        return $this->hasOne(City::className(), ['id' => 'city_id']);
    }

    /**
     * Поиск пользователя по Id
     * @param int|string $id - ID
     * @return null|static
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    /**
     * Поиск пользователя по Email
     * @param $email - электронная почта
     * @return null|static
     */
    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email]);
    }

    /**
     * Ключ авторизации
     * @return string
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * ID пользователя
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Проверка ключа авторизации
     * @param string $authKey - ключ авторизации
     * @return bool
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    /**
     * Поиск по токену доступа (не поддерживается)
     * @param mixed $token - токен
     * @param null $type - тип
     * @throws NotSupportedException - Исключение "Не подерживается"
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException(Yii::t('user', 'Поиск по токену не поддерживается.'));
    }

    /**
     * Проверка правильности пароля
     * @param $password - пароль
     * @return bool
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Генераия Хеша пароля
     * @param $password - пароль
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Поиск по токену восстановления паролья
     * Работает и для неактивированных пользователей
     * @param $token - токен восстановления пароля
     * @return null|static
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }
        return static::findOne([
            'password_reset_token' => $token
        ]);
    }

    /**
     * Генерация случайного авторизационного ключа
     * для пользователя
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Проверка токена восстановления пароля
     * согласно его давности, заданной константой EXPIRE
     * @param $token - токен восстановления пароля
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }
        $parts = explode('_', $token);
        $timestamp = (int) end($parts);
        return $timestamp + self::EXPIRE >= time();
    }

    /**
     * Генерация случайного токена
     * восстановления пароля
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Очищение токена восстановления пароля
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * Проверка токена подтверждения Email
     * @param $email_confirm_token - токен подтверждения электронной почты
     * @return null|static
     */
    public static function findByEmailConfirmToken($email_confirm_token)
    {
        return static::findOne(['email_confirm_token' => $email_confirm_token, 'status' => self::STATUS_WAIT]);
    }

    /**
     * Генерация случайного токена
     * подтверждения электронной почты
     */
    public function generateEmailConfirmToken()
    {
        $this->email_confirm_token = Yii::$app->security->generateRandomString();
    }

    /**
     * Очищение токена подтверждения почты
     */
    public function removeEmailConfirmToken()
    {
        $this->email_confirm_token = null;
    }

    /**
     * @param bool $insert
     * @param array $changedAttributes
     * Сохраняем изображения после сохранения
     * данных пользователя
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        $this->saveImage();
    }

    /**
     * Действия, выполняющиеся после авторизации.
     * Сохранение IP адреса и даты авторизации.
     *
     * Для активации текущего обновления необходимо
     * повесить текущую функцию на событие 'on afterLogin'
     * компонента user в конфигурационном файле.
     * @param $id - ID пользователя
     */
    public static function afterLogin($id)
    {
        $db = self::getDb();
        $db->createCommand()->update('lb_user', [
            'ip' => $_SERVER["REMOTE_ADDR"],
            'login_at' => date('Y-m-d H:i:s')
        ], ['id' => $id])->execute();
    }

    /**
     * Сохранение изображения (аватара)
     * пользвоателя
     */
    public function saveImage()
    {
        if ($this->photo) {
            $this->removeImage();   // Сначала удаляем старое изображение
            $module = Yii::$app->controller->module;
            $path = $module->userPhotoPath; // Путь для сохранения аватаров
            $name = time() . '-' . $this->id; // Название файла
            $this->image = $path. '/' . $name . $this::EXT;   // Путь файла и название
            if (!file_exists($path)) {
                mkdir($path, 0777, true);   // Создаем директорию при отсутствии
            }
            Image::thumbnail($this->photo->tempName, 200, 200)
                ->save($this->image);   // Сохраняем изображение в формате 200x200 пикселей
            $this::getDb()
                ->createCommand()
                ->update($this->tableName(), ['image' => $this->image], ['id' => $this->id])
                ->execute();
        }
    }

    /**
     * Удаляем изображение при его наличии
     */
    public function removeImage()
    {
        if ($this->image) {
            // Если файл существует
            if (file_exists($this->image)) {
                unlink($this->image);
            }
            // Не регистрация пользователя
            if (!$this->isNewRecord) {
                $this::getDb()
                    ->createCommand()
                    ->update($this::tableName(), ['image' => null], ['id' => $this->id])
                    ->execute();
            }
        }
    }

    /**
     * Список всех пользователей
     * @param bool $show_id - показывать ID пользователя
     * @return array - [id => Имя Фамилия (ID)]
     */
    public static function getAll($show_id = false)
    {
        $users = [];
        $model = self::find()->all();
        if ($model) {
            foreach ($model as $m) {
                $name = ($m->last_name) ? $m->first_name . " " . $m->last_name : $m->first_name;
                if ($show_id) {
                    $name .= " (".$m->id.")";
                }
                $users[$m->id] = $name;
            }
        }

        return $users;
    }
}
