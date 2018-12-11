<?php
/**
 * Created by PhpStorm.
 * User: M
 * Date: 17/10/26
 * Time: 上午9:53
 */

return [
    'enablePrettyUrl' => true,
    'showScriptName'  => false, //隐藏index.php
    //'suffix' => '.html', //后缀
    'rules' => [
        '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
    ]
];
