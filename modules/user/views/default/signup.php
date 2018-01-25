<?php
/**
 * Created by PhpStorm.
 * User: BUNAKOV ILYA
 * Date: 18.01.2018
 * Time: 8:44
 */

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

/* @var $model \app\modules\user\models\SignupForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

$this->title = Yii::t('app', 'TITLE_SIGNUP');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-default-signup">
    <div class="container">
        <h1><?= Html::encode($this->title) ?></h1>

        <p><?=Yii::t('app','PLEASE_FILL_FOR_SIGNUP')?>:</p>

        <div class="row">
            <div class="col-lg-5">
                <?php $form = ActiveForm::begin(['id' => 'form-signup']); ?>

                <?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>

                <?= $form->field($model, 'email') ?>

                <?= $form->field($model, 'password')->passwordInput() ?>

                <?= $form->field($model, 'verifyCode')->widget(Captcha::className(), [
                    'captchaAction' => '/user/default/captcha',
                    'template' => '<div class="row"><div class="col-lg-3">{image}</div><div class="col-lg-6">{input}</div></div>',
                ]) ?>
                <div class="form-group">
                    <?= Html::submitButton(Yii::t('app','BUTTON_SIGNUP'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>