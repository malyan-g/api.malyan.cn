<?php
/**
 * Created by PhpStorm.
 * User: xywy
 * Date: 2017/3/25
 * Time: 13:56
 */

namespace app\components\helpers;

use yii\base\Object;

/**
 * 快递鸟接口
 * Class ExpressApiHelper
 * @package app\components\helpers
 */
class ExpressApiHelper extends Object
{
    /**
     * 用户ID
     */
    const APP_ID = '1436369';

    /**
     * 秘钥
     */
    const APP_KEY = '699945ff-d415-4229-bd37-d0de16dc5d34';

    /**
     * 查询
     * @param string $shipperCode
     * @param string $logisticCode
     * @return string
     * @throws \Exception
     */
    public static function query($shipperCode, $logisticCode)
    {
        $host = 'http://api.kdniao.com/Ebusiness/EbusinessOrderHandle.aspx';
        $requestData = json_encode([
            'OrderCode' => '',
            'ShipperCode' => $shipperCode,
            'LogisticCode' => $logisticCode,
        ]);
        $data = [
            'RequestData' => urlencode($requestData),
            'EBusinessID' => self::APP_ID,
            'RequestType' => '1002',
            'DataSign' => self::encrypt($requestData),
            'DataType' => '2',
        ];

        return HttpClientHelper::request($host, 'post', $data);
    }

    /**
     * 签名加密
     * @param $data
     * @return string
     */
    private static function encrypt($data)
    {
        return urlencode(base64_encode(md5($data . self::APP_KEY)));
    }
}