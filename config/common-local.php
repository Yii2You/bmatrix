<?php
/**
 * Created by PhpStorm.
 * User: BUNAKOV ILYA
 * Date: 17.01.2018
 * Time: 14:48
 */

return [
    'components' => [
        'db' => [
            'dsn' => 'mysql:host=localhost;dbname=yii2_bmatrix',
            'username' => 'root',
            'password' => '',
            'tablePrefix' => 'bmx_',
        ],
        'mailer' => [
            'useFileTransport' => true,
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
    ],
];