<?php
/**
 * Created by PhpStorm.
 * User: BUNAKOV ILYA
 * Date: 18.01.2018
 * Time: 8:43
 */

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

/* @var $model \app\modules\user\models\PasswordResetRequestForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = Yii::t('app', 'TITLE_RESET_PASSWORD_REQUEST');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-request-password-reset">
    <div class="container">
        <h1><?= Html::encode($this->title) ?></h1>

        <p><?= Yii::t('app', 'PLEASE_FILL_FOR_RESET_REQUEST') ?></p>

        <div class="row">
            <div class="col-lg-5">
                <?php $form = ActiveForm::begin(['id' => 'request-password-reset-form']); ?>

                <?= $form->field($model, 'email')->textInput(['autofocus' => true]) ?>

                <div class="form-group">
                    <?= Html::submitButton(Yii::t('app', 'BUTTON_SEND'), ['class' => 'btn btn-primary']) ?>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>