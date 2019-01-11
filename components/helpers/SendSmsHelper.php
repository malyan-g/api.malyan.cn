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
use crazyfd\qiniu\Qiniu;

/**
 * 发送短信接口
 * Class SendSmsHelper
 * @package app\components\helpers
 */
class SendSmsHelper extends Object
{
    /**
     * 七牛AK
     */
    const ACCESS_KEY= 'n-K0BplUXED8juHWjXm4oLbWx3UlppUraYwaDIgR';

    /**
     * 七牛SK
     */
    const SECRET_KEY = 'sET-cgQCg4A6om1zuXV9f0i2MTLptuo1IyW4HJPC';

    /**
     * 域名
     */
    const DOMAIN = 'http://img.malyan.cn';

    /**
     * @param $updateFile
     * @param $filename
     * @return array|mixed
     * @throws \Exception
     */
    public static function send($phone, $msg)
    {
        return false;
    }
}