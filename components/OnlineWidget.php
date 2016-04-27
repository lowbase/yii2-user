<?php
/**
 * @package   yii2-user
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */

namespace lowbase\user\components;

use yii\base\Widget;
use Yii;

/**
 * Виджет фиксирования пользователя Online
 * Устанавливается в те представления, где
 * необходимо фиксировать нахождение пользователя
 * с регулярностью $time
 * 
 * Class OnlineWidget
 * @package lowbase\user\components
 */
class OnlineWidget extends Widget
{
    public $time = 300; // 5 минут

    public function run()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->render('onlineWidget', ['time' => (int) $this->time * 1000]);
        } else {
            return true;
        }
    }
}
