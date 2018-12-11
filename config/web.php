<?php
require(__DIR__ . '/const.php');

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'defaultRoute' => 'index/index',
    'language'   => 'zh-CN',
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '2lp18Q7XzHsSkbOrNfE4jLzYXPUmypMg',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'identityCookie' => ['name' => '_identity-passport', 'httpOnly' => true],
            'enableAutoLogin' => true,
        ],
        // 错误配置
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        // 日志配置
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        // 数据库配置
        'db' => require(__DIR__ . '/db.php'),
        // Redis配置
        'redis' => require(__DIR__ . '/redis.php'),
        // 路由配置
        'urlManager' => require(__DIR__ . '/urlManager.php')
    ],
    'params' => require(__DIR__ . '/params.php'),
    'aliases' => [
        '@staticHost' => 'http://static.malyan.cn',
    ],
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
