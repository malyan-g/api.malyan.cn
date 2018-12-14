<?php
/**
 * Created by PhpStorm.
 * User: M
 * Date: 17/10/27
 * Time: 下午5:40
 */

namespace app\components\helpers;

use yii\base\Object;

/**
 * Class ScHelper
 * @package app\components\helpers
 */
class ScHelper extends Object
{
    //密钥为24位16进制 向量为8
    protected static $desKey = '4d89g13j4j91j27c';
    protected static $desIv = 'f5e68737ead431bb';

    /**
     * 用户信息加密
     * @param $userArray
     * @return null
     */
    public static function encode($data)
    {
        if(is_array($data)){
            $encodeStr = openssl_encrypt(json_encode($data), 'AES-128-CBC', self::$desKey,false, self::$desIv);
            return urlencode($encodeStr);
        }
        return null;
    }

    /**
     * 用户信息解密
     * @param $str
     * @return null
     */
    public static function decode( $str )
    {
        if($str){
            $decodeStr = openssl_decrypt(urldecode($str), 'AES-128-CBC', self::$desKey,false, self::$desIv);
            return json_decode($decodeStr, TRUE);
        }
        return null;
    }
}
