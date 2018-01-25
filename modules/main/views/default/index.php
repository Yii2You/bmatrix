<?php
/* @var $this yii\web\View */
$this->title = Yii::$app->name;
?>

<div class="main-default-index">
    <div class="body-content">
        <div class="row">
            <div class="jumbotron">
                <h1>B-Matrix CMS Yii2</h1>
                <img class="logo-yii" src="/img/B-Matrix.png">
                <img class="logo-yii" src="/img/yii.png">
                <p class="lead">Панель управления и инструменты на базе Yii2-фреймворка. Лёгкий CMS для быстрых
                    сайтов.</p>
                <p>
                    <a class="btn btn-lg btn-success" href="https://github.com/Yii2You/bmatrix">
                        <?= Yii::t('app', 'GET_STARTED_BMATRIX') ?>
                    </a>
                </p>
            </div>
        </div>
        <div id="features">
            <h2><?= Yii::t('app', 'FEATURES') ?></h2>
            <div class="feature">
                <i class="glyphicon glyphicon-dashboard"></i>
                <h3><?= Yii::t('app', 'FAST_ENGINE') ?></h3>
                <p><?= Yii::t('app', 'FAST_ENGINE_BLOCK') ?></p>
            </div>
            <div class="feature">
                <i class="glyphicon glyphicon-pencil"></i>
                <h3><?= Yii::t('app', 'LIVE_EDIT') ?></h3>
                <p><?= Yii::t('app', 'LIVE_EDIT_BLOCK') ?></p>
            </div>
            <div class="feature">
                <i class="glyphicon glyphicon-wrench"></i>
                <h3><?= Yii::t('app', 'EASY_SIMPLE') ?></h3>
                <p><?= Yii::t('app', 'EASY_SIMPLE_BLOCK') ?></p>
            </div>
            <div class="feature">
                <i class="glyphicon glyphicon-thumbs-up"></i>
                <h3><?= Yii::t('app', 'POWERED_BY_YII2') ?></h3>
                <p><?= Yii::t('app', 'POWERED_BY_YII2_BLOCK') ?></p>
            </div>
        </div>
    </div>
</div>