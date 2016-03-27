<?php
/**
 * @package   yii2-user
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */

use lowbase\user\models\User;
use lowbase\user\UserAsset;

$assets = UserAsset::register($this);

$name = $model->first_name;
if ($model->last_name) {
    $name .= ' ' . $model->last_name;
}
$this->title = $name;
?>
<div class="user-show">
    <div class="lb-user-module-profile-image">
        <?php
        if ($model->image) {
            echo "<img src='".$model->image."' class='thumbnail'>";
        } else {
            if ($model->sex === User::SEX_FEMALE) {
                echo "<img src='".$assets->baseUrl ."/image/female.png' class='thumbnail'>";
            } else {
                echo "<img src='".$assets->baseUrl ."/image/male.png' class='thumbnail'>";
            }
        }
        ?>
    </div>
    <b><?= $name ?></b>
</div>
