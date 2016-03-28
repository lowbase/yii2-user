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
 * This is the model class for table "user".
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
    //Статусы пользователя
    const STATUS_BLOCKED = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_WAIT = 2;

    //Гендерные статусы
    const SEX_MALE = 1;
    const SEX_FEMALE = 2;

    public $photo;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'lb_user';
    }

    /**
     * @inheritdoc
     *  Автозаполнение полей created_at и update_at
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
     */
    public static function getSexArray()
    {
        return [
            self::SEX_MALE =>  Yii::t('user', 'Мужской'),
            self::SEX_FEMALE => Yii::t('user', 'Женский'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['first_name'], 'required'],
            [['sex', 'country_id', 'city_id', 'status'], 'integer'],
            [['birthday', 'login_at'], 'safe'],
            [['first_name', 'last_name', 'email', 'phone'], 'string', 'max' => 100],
            [['auth_key'], 'string', 'max' => 32],
            [['ip'], 'string', 'max' => 32],
            [['password_hash', 'password_reset_token', 'email_confirm_token', 'image', 'address'], 'string', 'max' => 255],
            ['status', 'in', 'range' => array_keys(self::getStatusArray())],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['sex', 'in', 'range' => array_keys(self::getSexArray())],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => self::className(),
                'message' => Yii::t('user', 'Данный Email уже зарегистрирован.')],
            [['first_name', 'last_name', 'email', 'phone', 'address'], 'filter', 'filter' => 'trim'],
            [['last_name', 'password_reset_token', 'email_confirm_token',
                'image', 'sex', 'phone', 'country_id', 'city_id', 'address',
                'auth_key', 'password_hash', 'email', 'ip', 'login_at'], 'default', 'value' => null],
            [['photo'], 'image',
                'minHeight' => 100,
                'skipOnEmpty' => true
            ],
        ];
    }

    /**
     * @inheritdoc
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
     * @return \yii\db\ActiveQuery
     */
    public function getAuthAssignments()
    {
        return $this->hasMany(AuthAssignment::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItemNames()
    {
        return $this->hasMany(AuthItem::className(), ['name' => 'item_name'])->viaTable('lb_auth_assignment', ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKeys()
    {
        return $this->hasMany(UserOauthKey::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCountry()
    {
        return $this->hasOne(Country::className(), ['id' => 'country_id']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCity()
    {
        return $this->hasOne(City::className(), ['id' => 'city_id']);
    }

    /**
     * @param int|string $id
     * @return null|static
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    /**
     * @param $email
     * @return null|static
     */
    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email]);
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException(Yii::t('user', 'Поиск по токену не поддерживается.'));
    }

    /**
     * @inheritdoc
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * @inheritdoc
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * @inheritdoc
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }
        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * @inheritdoc
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }
        $expire = 3600;
        $parts = explode('_', $token);
        $timestamp = (int) end($parts);
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * @inheritdoc
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * @inheritdoc
     */
    public static function findByEmailConfirmToken($email_confirm_token)
    {
        return static::findOne(['email_confirm_token' => $email_confirm_token, 'status' => self::STATUS_WAIT]);
    }

    /**
     * @inheritdoc
     */
    public function generateEmailConfirmToken()
    {
        $this->email_confirm_token = Yii::$app->security->generateRandomString();
    }

    /**
     * @inheritdoc
     */
    public function removeEmailConfirmToken()
    {
        $this->email_confirm_token = null;
    }

     /**
     * @param bool $insert
     * @param array $changedAttributes
     * @return bool
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        $this->saveImage();
        return true;
    }

    /**
     * @param $id
     * @return bool
     * @throws \yii\db\Exception
     */
    public static function afterLogin($id)
    {
        $db = self::getDb();
        $db->createCommand()->update('lb_user', ['ip' => $_SERVER["REMOTE_ADDR"], 'login_at' => date('Y-m-d H:i:s')], ['id' => $id])->execute();
        return true;
    }

    /**
     * Сохранение фото
     * @return bool
     * @throws \yii\db\Exception
     */
    public function saveImage()
    {
        if ($this->photo) {
            $this->removeImage();
            $module = Yii::$app->controller->module;
            $ext = ".jpg";
            $name = time() . '-' . $this->id;
            $path = $module->userPhotoPath;
            $full_name = $path. '/' . $name . $ext;
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }
            if (is_object($this->photo)) {
                Image::thumbnail($this->photo->tempName, 200, 200)->save($full_name);
            } else {
                if (file_get_contents($this->photo)) {
                    $content = file_get_contents($this->photo);
                    file_put_contents($full_name, $content);
                }
            }
            $this->image = $full_name;
            $db = User::getDb();
            $db->createCommand()->update('lb_user', ['image' => $full_name], ['id' => $this->id])->execute();
        }
        return true;
    }

    /**
     * Удаление фото
     * @return bool
     * @throws \yii\db\Exception
     */
    public function removeImage()
    {
        if ($this->image) {
            if (file_exists($this->image)) {
                unlink($this->image);
            }
            if (!$this->isNewRecord) {
                $db = User::getDb();
                $db->createCommand()->update('lb_user', ['image' => null], ['id' => $this->id])->execute();
            }
        }
        return true;
    }

    /**
     * Список всех пользователей массивом
     * @param array $type
     * @return array
     */
    public static function getAll()
    {
        $user = [];
        $model = User::find()->all();
        if ($model) {
            foreach ($model as $m) {
                $name = ($m->last_name) ? $m->first_name ." ".$m->last_name." (".$m->id.")" : $m->first_name . " (".$m->id.")";
                $user[$m->id] = $name;
            }
        }
        return $user;
    }
}
