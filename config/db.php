<?php

return [
    'class' => 'yii\db\Connection',
    // 主库的配置
    'dsn' => 'mysql:host=' . $SYSTEM_CONFIG['SYSTEM_DB_HOST'] . ':' . $SYSTEM_CONFIG['SYSTEM_DB_PORT'] . ';dbname=' . $SYSTEM_CONFIG['SYSTEM_DB_NAME'],
    'username' => $SYSTEM_CONFIG['SYSTEM_DB_USER'],
    'password' => $SYSTEM_CONFIG['SYSTEM_DB_PASS'],
    'charset' => $SYSTEM_CONFIG['SYSTEM_DB_CHARSET'],
    'tablePrefix' => $SYSTEM_CONFIG['SYSTEM_DB_TABLE_PREFIX'],
    /*// 从库的通用配置
    'slaveConfig' => [
        'username' => $SYSTEM_CONFIG['SYSTEM_DB_USER_R'],
        'password' => $SYSTEM_CONFIG['SYSTEM_DB_PASS_R'],
        'attributes' => [
            // 使用一个更小的连接超时
            PDO::ATTR_TIMEOUT => 10,
        ],
    ],
    // 从库的配置列表
    'slaves' => [
        [
            'dsn' => 'mysql:host=' . $SYSTEM_CONFIG['SYSTEM_DB_HOST_R1'] . ':' . $SYSTEM_CONFIG['SYSTEM_DB_PORT_R'] . ';dbname=' . $SYSTEM_CONFIG['SYSTEM_DB_NAME_R']
        ],
        [
            'dsn' => 'mysql:host=' . $SYSTEM_CONFIG['SYSTEM_DB_HOST_R2'] . ':' . $SYSTEM_CONFIG['SYSTEM_DB_PORT_R'] . ';dbname=' . $SYSTEM_CONFIG['SYSTEM_DB_NAME_R']
        ]
    ],*/
];
