<?php
/**
 * Created by PhpStorm.
 * User: M
 * Date: 17/10/26
 * Time: 上午9:53
 */

return [
    //用于表明 urlManager 是否启用 URL 美化功能
    //默认不启用。但实际使用中，特别是产品环境，一般都会启用
    'enablePrettyUrl'       => true,
    //是否启用严格解析，如启用严格解析，要求当前请求应至少匹配1个路由规则，否则认为是无效路由。
    //这个选项仅在 enablePrettyUrl 启用后才有效。
    //如果开启，表示只有配置在 rules 里的规则才有效
    //由于项目会将一些 url 进行优化，所以这里需要设置为 true
    'enableStrictParsing'   => true,
    //指定是否在URL在保留入口脚本 index.php
    'showScriptName'        => false,
    // 后缀
    //'suffix' => '.html',
    'rules' => [
        //当然，如果自带的路由无法满足需求，可以自己增加规则
        '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
        [
            'class'         => 'yii\rest\UrlRule',
            'controller'    => ['v1/goods'],
            // 由于 resetful 风格规定 URL 保持格式一致并且始终使用复数形式
            // 所以如果你的 controller 是单数的名称比如 UserController
            // 设置 pluralize 为 true （默认为 true）的话，url 地址必须是 users 才可访问
            // 如果 pluralize 设置为 false, url 地址必须是 user 也可访问
            // 如果你的 controller 本身是复数名称 UsersController ，此参数没用，url 地址必须是 users
            'pluralize'     => false,
        ],
    ],
];
