<?php

namespace app\components\methods;

use Yii;
use yii\helpers\ArrayHelper;
use app\components\helpers\HttpClientHelper;

class ApiRequest
{
    /**
     * AppID(小程序ID)
     */
    const API_APP_ID = 'wxe70daa96ac03e0a4';

    /**
     * AppSecret(小程序密钥)
     */
    const API_APP_SECRET = '34a1e70128e9d8840320ecf1084be50a';

    const API_REQUEST_CODE_SUCCESS = 0;
    const API_TYPE_LOGIN_URL = 1;
    const PAY_SUCCESS = 'store_order_submit';//支付成功
    const API_TYPE_DOCTOR_INFO = 2; //医生详情


    /**
     * 接口配置
     * @param $api
     * @return mixed
     */
    protected static function apiConfig($api)
    {
        $config = [
            self::API_TYPE_LOGIN_URL => [
                'url' => 'https://api.weixin.qq.com/sns/jscode2session',
                'appid' => self::API_APP_ID,
                'secret' => self::API_APP_SECRET,
                'grant_type' => 'authorization_code'
            ],
        ];
        return $config[$api];
    }

    /**
     * 接口请求
     * @param $api
     * @param array $getData
     * @param array $postData
     * @param $method
     * @param $code
     * @param $returnAll
     * @return mixed
     */
    public static function request($api ,$getData = [], $postData = [], $method = 'post', $code = 10000, $returnAll = false)
    {
        $app_secret_key = "@I.D#AxFxR8o+o+*";
        $config = self::apiConfig($api);
        $data = [
            'api' => $config['api'],
            'source' => 'yyzs_ym_user',
            'os' => 'wap',
            'version' => $config['version'],
            'pro' => 'xywyf32l24WmcqquqqTdhXaWkg'
        ];
        $getData = array_merge($data, $getData);
        $param = '';
        foreach ($getData as $key=>$val){
            $param .= $key . '=' . $val .'&';
        }
        $sign = self::encrypt(array_merge($getData, $postData), $app_secret_key);
        $param .= 'sign=' . $sign;
        $url = Yii::$app->params['WWSURL'] . $config['url'] . '?' . $param;
        $result = HttpClientHelper::request($url, $method, $postData);
        if($returnAll){
            return $result;
        }
        return ArrayHelper::getValue($result, 'code') == $code ? ArrayHelper::getValue($result, 'data') : [];
    }

    /**
     * 登录js_code转openid
     * @param $code
     * @return array|mixed
     */
    public static function login($code)
    {
        $url = 'https://api.weixin.qq.com/sns/jscode2session?appid=' .self::API_APP_ID . '&secret=' . self::API_APP_SECRET . '&js_code=' . $code . 'grant_type&=authorization_code';
        $result = HttpClientHelper::request($url, 'get');
        return ArrayHelper::getValue($result, 'errcode') == 0 ? $result : [];
    }

    /**
     * 加密
     * @param $data
     * @param $key
     * @return string
     */
    public static function encrypt($data, $key)
    {
        if(!$data ||  !$key){
            return '';
        }
        ksort($data);
        reset($data);
        $signStr = "";
        foreach($data as $k => $v){
            if($k != 'sign' && $k != 'r' ){
                $signStr .= "&" . $k . "=" . trim($v);
            }
        }
        $sign =  md5(trim($signStr, "&") . $key);
        return $sign;
    }
}
