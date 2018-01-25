<?php
/**
 * Created by PhpStorm.
 * User: BUNAKOV ILYA
 * Date: 17.01.2018
 * Time: 23:13
 */

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user app\modules\user\models\User */

$confirmLink = Yii::$app->urlManager->createAbsoluteUrl(['user/default/email-confirm', 'token' => $user->email_confirm_token]);
?>

Здравствуйте, <?= Html::encode($user->username) ?>!

Для подтверждения адреса пройдите по ссылке:

<?= Html::a(Html::encode($confirmLink), $confirmLink) ?>

Если Вы не регистрировались у на нашем сайте, то просто удалите это письмо.