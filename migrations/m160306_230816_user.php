<?php
/**
 * @package   yii2-user
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */

use lowbase\user\models\AuthItem;
use lowbase\user\models\User;
use yii\db\Schema;
use yii\db\Migration;

class m160306_230816_user extends Migration
{
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

    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        //Таблица страны country
        $this->createTable('{{%lb_country}}', [
            'id' => Schema::TYPE_PK,
            'name' => Schema::TYPE_STRING.'(255) NOT NULL',
            'currency_code' => Schema::TYPE_STRING.'(5) NOT NULL',
            'currency' => Schema::TYPE_STRING.' NULL DEFAULT NULL'
        ], $tableOptions);

        //Таблица города city
        $this->createTable('{{%lb_city}}', [
            'id' => Schema::TYPE_PK,
            'country_id' => Schema::TYPE_INTEGER.'(11) NOT NULL',
            'city' => Schema::TYPE_STRING.'(255) NOT NULL',
            'state' => Schema::TYPE_STRING.'(255) NULL DEFAULT NULL',
            'region' => Schema::TYPE_STRING.'(255) NOT NULL',
            'biggest_city' => Schema::TYPE_SMALLINT.' NOT NULL DEFAULT 0',
        ], $tableOptions);

        //Ключи и индексы
        $this->addForeignKey('city_country_id_fk', '{{%lb_city}}', 'country_id', '{{%lb_country}}', 'id', 'CASCADE', 'CASCADE');
        $this->createIndex('city_city_index', '{{%lb_city}}', 'city');

        //Таблица пользователей user
        $this->createTable('{{%lb_user}}', [
            'id' => Schema::TYPE_PK,
            'first_name' => Schema::TYPE_STRING . '(100) NOT NULL',
            'last_name' => Schema::TYPE_STRING . '(100) NULL DEFAULT NULL',
            'auth_key' => Schema::TYPE_STRING . '(32) NULL DEFAULT NULL',
            'password_hash' => Schema::TYPE_STRING . ' NULL DEFAULT NULL',
            'password_reset_token' => Schema::TYPE_STRING . ' NULL DEFAULT NULL',
            'email_confirm_token' => Schema::TYPE_STRING . ' NULL DEFAULT NULL',
            'email' => Schema::TYPE_STRING . '(100) NULL DEFAULT NULL',
            'image' => Schema::TYPE_STRING.' NULL DEFAULT NULL',
            'sex' => Schema::TYPE_SMALLINT.' NULL DEFAULT NULL',
            'birthday' => Schema::TYPE_DATE . ' NULL DEFAULT NULL',
            'phone' => Schema::TYPE_STRING . '(100) NULL DEFAULT NULL',
            'country_id' => Schema::TYPE_INTEGER . ' NULL DEFAULT NULL',
            'city_id' => Schema::TYPE_INTEGER . ' NULL DEFAULT NULL',
            'address' => Schema::TYPE_STRING . ' NULL DEFAULT NULL',
            'status' => Schema::TYPE_SMALLINT.' NOT NULL DEFAULT ' . User::STATUS_WAIT,
            'address' => Schema::TYPE_STRING . ' NULL DEFAULT NULL',
            'ip' => Schema::TYPE_STRING . '(20) NULL DEFAULT NULL',
            'created_at' => Schema::TYPE_DATETIME . ' NOT NULL',
            'updated_at' => Schema::TYPE_DATETIME . ' NULL DEFAULT NULL',
            'login_at' => Schema::TYPE_DATETIME . ' NULL DEFAULT NULL',
        ], $tableOptions);

        //Индексы и ключи таблицы пользователей user
        $this->addForeignKey('user_country_id_fk', '{{%lb_user}}', 'country_id', '{{%lb_country}}', 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey('user_city_id_fk', '{{%lb_user}}', 'city_id', '{{%lb_city}}', 'id', 'SET NULL', 'CASCADE');
        $this->createIndex('user_name_index', '{{%lb_user}}', ['first_name', 'last_name']);
        $this->createIndex('user_email_index', '{{%lb_user}}', 'email');
        $this->createIndex('user_status_index', '{{%lb_user}}', 'status');

        //Предустановленные значения таблицы пользователей user
        $this->batchInsert('lb_user', [
            'id',
            'first_name',
            'last_name',
            'email',
            'auth_key',
            'password_hash',
            'status',
            'created_at',
            'updated_at'
        ], [
            [
                1,
                self::ADMIN_FIRST_NAME,
                self::ADMIN_LAST_NAME,
                self::ADMIN_EMAIL,
                Yii::$app->security->generateRandomString(),
                Yii::$app->security->generatePasswordHash(self::ADMIN_PASSWORD),
                User::STATUS_ACTIVE,
                date('Y-m-d H:i:s'),
                date('Y-m-d H:i:s')
            ],
            [
                2,
                self::MODERATOR_FIRST_NAME,
                self::MODERATOR_LAST_NAME,
                self::MODERATOR_EMAIL,
                Yii::$app->security->generateRandomString(),
                Yii::$app->security->generatePasswordHash(self::MODERATOR_PASSWORD),
                User::STATUS_ACTIVE,
                date('Y-m-d H:i:s'),
                date('Y-m-d H:i:s')
            ]
        ]);

        //Таблица авторизации пользователя user_oauth_key
        $this->createTable('{{%lb_user_oauth_key}}', [
            'id' => Schema::TYPE_PK,
            'user_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'provider_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'provider_user_id' => Schema::TYPE_STRING.'(255) NOT NULL',
            'page' => Schema::TYPE_STRING.'(255) NULL DEFAULT NULL'
        ], $tableOptions);

        //Индексы и ключи таблицы авторизации пользователя user_oauth_key
        $this->addForeignKey('user_oauth_key_user_id_fk', '{{%lb_user_oauth_key}}', 'user_id', '{{%lb_user}}', 'id', 'CASCADE', 'CASCADE');

        /**
         * Миграции RBAC
         */

        //Таблица правил auth_rule
        $this->createTable('{{%lb_auth_rule}}', [
            'name' => Schema::TYPE_STRING.'(64) NOT NULL',
            'data' => Schema::TYPE_TEXT,
            'created_at' => Schema::TYPE_INTEGER,
            'updated_at' => Schema::TYPE_INTEGER

        ], $tableOptions);

        //Индексы и ключи таблицы правил auth_rule
        $this->addPrimaryKey('auth_rule_pk', '{{%lb_auth_rule}}', 'name');

        //Предустановленные значения таблицы правил auth_rule
        $this->insert('lb_auth_rule', [
            'name' => 'AuthorRule',
            'data' => 'O:29:"lowbase\user\rules\AuthorRule":3:{s:4:"name";s:10:"AuthorRule";s:9:"createdAt";N;s:9:"updatedAt";N;}',
            'created_at' => time(),
            'updated_at' => time(),
        ]);

        //Таблица ролей и допусков auth_item
        $this->createTable('{{%lb_auth_item}}', [
            'name' => Schema::TYPE_STRING.'(64) NOT NULL',
            'type' => Schema::TYPE_INTEGER.' NOT NULL',
            'description' => Schema::TYPE_TEXT.' NOT NULL',
            'rule_name' => Schema::TYPE_STRING.'(64)',
            'data' => Schema::TYPE_TEXT,
            'created_at' => Schema::TYPE_INTEGER,
            'updated_at' => Schema::TYPE_INTEGER
        ], $tableOptions);

        //Индексы и ключи таблицы ролей и допусков auth_item
        $this->addPrimaryKey('auth_item_name_pk', '{{%lb_auth_item}}', 'name');
        $this->addForeignKey('auth_item_rule_name_fk', '{{%lb_auth_item}}', 'rule_name', '{{%lb_auth_rule}}',  'name', 'SET NULL', 'CASCADE');
        $this->createIndex('auth_item_type_index', '{{%lb_auth_item}}', 'type');

        //Предустановленные значения таблицы ролей и допусков auth_item
        $this->batchInsert('lb_auth_item', ['name', 'type', 'description', 'rule_name', 'created_at', 'updated_at'], [
            ['administrator', AuthItem::TYPE_ROLE, 'Администратор', NULL, time(), time()],
            ['moderator', AuthItem::TYPE_ROLE, 'Модератор', NULL, time(), time()],
            ['userUpdate', AuthItem::TYPE_PERMISSION, 'Редактирование пользователя', NULL, time(), time()],
            ['userDelete', AuthItem::TYPE_PERMISSION, 'Удаление пользователя', NULL, time(), time()],
            ['userManager', AuthItem::TYPE_PERMISSION, 'Менеджер пользователей', NULL, time(), time()],
            ['userView', AuthItem::TYPE_PERMISSION, 'Просмотр карточки пользователя', NULL, time(), time()],
            ['roleCreate', AuthItem::TYPE_PERMISSION, 'Создание роли / допуска', NULL, time(), time()],
            ['roleUpdate', AuthItem::TYPE_PERMISSION, 'Редактирование роли / допуска', NULL, time(), time()],
            ['roleDelete', AuthItem::TYPE_PERMISSION, 'Удаление роли / допуска', NULL, time(), time()],
            ['roleManager', AuthItem::TYPE_PERMISSION, 'Менеджер ролей / допусков', NULL, time(), time()],
            ['roleView', AuthItem::TYPE_PERMISSION, 'Просмотр роли / допуска', NULL, time(), time()],
            ['ruleCreate', AuthItem::TYPE_PERMISSION, 'Создание правил контроля доступа', NULL, time(), time()],
            ['ruleDelete', AuthItem::TYPE_PERMISSION, 'Удаление правил контроля доступа', NULL, time(), time()],
            ['ruleManager', AuthItem::TYPE_PERMISSION, 'Менеджер правил контроля доступа', NULL, time(), time()],
            ['ruleView', AuthItem::TYPE_PERMISSION, 'Просмотр правил контроля доступа', NULL, time(), time()],
            ['countryCreate', AuthItem::TYPE_PERMISSION, 'Создание страны', NULL, time(), time()],
            ['countryUpdate', AuthItem::TYPE_PERMISSION, 'Редактирование страны', NULL, time(), time()],
            ['countryDelete', AuthItem::TYPE_PERMISSION, 'Удаление страны', NULL, time(), time()],
            ['countryManager', AuthItem::TYPE_PERMISSION, 'Менеджер стран', NULL, time(), time()],
            ['countryView', AuthItem::TYPE_PERMISSION, 'Просмотр страны', NULL, time(), time()],
            ['cityCreate', AuthItem::TYPE_PERMISSION, 'Создание населенного пункта', NULL, time(), time()],
            ['cityUpdate', AuthItem::TYPE_PERMISSION, 'Редактирование населенного пункта', NULL, time(), time()],
            ['cityDelete', AuthItem::TYPE_PERMISSION, 'Удаление населенного пункта', NULL, time(), time()],
            ['cityManager', AuthItem::TYPE_PERMISSION, 'Менеджер населенных пунктов', NULL, time(), time()],
            ['cityView', AuthItem::TYPE_PERMISSION, 'Просмотр населенного пункта', NULL, time(), time()],
        ]);

        //Таблица разрешений auth_item_child
        $this->createTable('{{%lb_auth_item_child}}', [
            'parent' => Schema::TYPE_STRING.'(64) NOT NULL',
            'child' => Schema::TYPE_STRING.'(64) NOT NULL'
        ], $tableOptions);

        //Индексы и ключи таблицы разрешений auth_item_child
        $this->addPrimaryKey('auth_item_child_pk', '{{%lb_auth_item_child}}', array('parent', 'child'));
        $this->addForeignKey('auth_item_child_parent_fk', '{{%lb_auth_item_child}}', 'parent', '{{%lb_auth_item}}', 'name', 'CASCADE', 'CASCADE');
        $this->addForeignKey('auth_item_child_child_fk', '{{%lb_auth_item_child}}', 'child', '{{%lb_auth_item}}', 'name', 'CASCADE', 'CASCADE');

        //Предустановленные значения таблицы разрешений auth_item_child
        $this->batchInsert('lb_auth_item_child', ['parent', 'child'], [
            ['moderator', 'userManager'],
            ['moderator', 'userView'],
            ['moderator', 'roleManager'],
            ['moderator', 'roleView'],
            ['moderator', 'countryManager'],
            ['moderator', 'countryView'],
            ['moderator', 'cityManager'],
            ['moderator', 'cityView'],
            ['administrator', 'moderator'],
            ['administrator', 'userUpdate'],
            ['administrator', 'userDelete'],
            ['administrator', 'roleCreate'],
            ['administrator', 'roleUpdate'],
            ['administrator', 'roleDelete'],
            ['administrator', 'ruleCreate'],
            ['administrator', 'ruleDelete'],
            ['administrator', 'ruleView'],
            ['administrator', 'ruleManager'],
            ['administrator', 'countryCreate'],
            ['administrator', 'countryUpdate'],
            ['administrator', 'countryDelete'],
            ['administrator', 'cityCreate'],
            ['administrator', 'cityUpdate'],
            ['administrator', 'cityDelete'],
        ]);

        //Таблица связи ролей auth_assignment
        $this->createTable('{{%lb_auth_assignment}}', [
            'item_name' => Schema::TYPE_STRING.'(64) NOT NULL',
            'user_id' => Schema::TYPE_INTEGER.'(11) NOT NULL',
            'created_at' => Schema::TYPE_INTEGER,
            'updated_at' => Schema::TYPE_INTEGER
        ], $tableOptions);

        //Индексы и ключи таблицы связи ролей auth_assignment
        $this->addPrimaryKey('auth_assignment_pk', '{{%lb_auth_assignment}}', array('item_name', 'user_id'));
        $this->addForeignKey('auth_assignment_item_name_fk', '{{%lb_auth_assignment}}', 'item_name', '{{%lb_auth_item}}', 'name', 'CASCADE', 'CASCADE');
        $this->addForeignKey('auth_assignment_user_id_fk', '{{%lb_auth_assignment}}', 'user_id', '{{%lb_user}}', 'id', 'CASCADE', 'CASCADE');

        //Предустановленные значения таблицы связи ролей auth_assignment
        $this->batchInsert('lb_auth_assignment', ['item_name', 'user_id', 'created_at', 'updated_at'], [
            ['administrator', 1, time(), time()],
            ['moderator', 2, time(), time()],
        ]);
    }

    public function down()
    {
        $this->dropTable('{{%lb_auth_assignment}}');
        $this->dropTable('{{%lb_auth_item_child}}');
        $this->dropTable('{{%lb_auth_item}}');
        $this->dropTable('{{%lb_auth_rule}}');
        $this->dropTable('{{%lb_user_oauth_key}}');
        $this->dropTable('{{%lb_user}}');
        $this->dropTable('{{%lb_city}}');
        $this->dropTable('{{%lb_country}}');
    }
}
