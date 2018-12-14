<?php
/**
 * Created by PhpStorm.
 * User: xywy
 * Date: 2017/3/25
 * Time: 13:56
 */

namespace app\components\helpers;

use yii\base\Object;
use yii\httpclient\Client;
use yii\helpers\ArrayHelper;

/**
 * 小程序接口
 * Class WxApiHelper
 * @package app\components\helpers
 */
class WxApiHelper extends Object
{
    /**
     * AppID(小程序ID)
     */
    const API_APP_ID = 'wxe70daa96ac03e0a4';

    /**
     * AppSecret(小程序密钥)
     */
    const API_APP_SECRET = '34a1e70128e9d8840320ecf1084be50a';
    /**
     * 执行请求操作
     * @param string $path 请求的路径
     * @param string $method 请求的方式（get post delete put)
     * @param string $data 请求的数据内容
     * @return mixed
     */
    protected static function execute($path, $method = 'get', $data = '')
    {
        $header = [];
        if ($path != 'token') {
            $header = ['Authorization'=>'Bearer ' . static::getToken()];
        }
        return HttpClientHelper::request (static::URL,$method,$data,$header,Client::FORMAT_JSON);
    }

    /**
     * 获取用户登录的openid
     * @param $code
     * @return array|mixed
     */
    public static function getLoginInfo($code)
    {
        $url = 'https://api.weixin.qq.com/sns/jscode2session?appid=' .self::API_APP_ID . '&secret=' . self::API_APP_SECRET . '&js_code=' . $code . '&grant_type=authorization_code';
        $result = HttpClientHelper::request($url, 'get');
        return ArrayHelper::getValue($result, 'errcode') == 0 ? $result : [];
    }
}