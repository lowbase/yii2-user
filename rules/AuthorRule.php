<?php
/**
 * @package   yii2-user
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */

namespace lowbase\user\rules;

use yii\rbac\Rule;

/**
 * Class AuthorRule
 * Проверка на авторство
 * @package lowbase\user\rules
 */
class AuthorRule extends Rule
{
    public $name = 'AuthorRule';

    public function execute($user_id, $item, $params)
    {
        return isset($params['post']) ? $params['post']->createdBy == $user_id : false;
    }
}
