<?php
/**
 * Created by PhpStorm.
 * User: xywy
 * Date: 2017/3/25
 * Time: 13:56
 */

namespace app\components\helpers;

use yii\base\Object;
use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;

/**
 * 发送短信接口
 * Class SendSmsHelper
 * @package app\components\helpers
 */
class SendSmsHelper extends Object
{
    /**
     * 阿里云AK
     */
    const ACCESS_KEY= 'LTAIwB72NQ7oQZsj';

    /**
     * 阿里云SK
     */
    const SECRET_KEY = '62nohL1lsuPDONDK3xUDy8WfM0ZRiX';

    /**
     * 签名名称
     */
    const  SIGN_NAME = '菜根堂';

    /**
     * 模板CODE
     */
    const TEMPLATE_CODE = 'SMS_157280381';

    /**
     * 发送
     * @param $mobile
     * @param $code
     * @return array
     */
    public static function sendCode($mobile, $code)
    {
        $data = [];
        AlibabaCloud::accessKeyClient(self::ACCESS_KEY,  self::SECRET_KEY)
            ->regionId('cn-hangzhou')
            ->asGlobalClient();
        try {
            $result = AlibabaCloud::rpcRequest()
                ->product('Dysmsapi')
                ->version('2017-05-25')
                ->action('SendSms')
                ->method('POST')
                ->options([
                    'query' => [
                        'RegionId' => 'cn-hangzhou',
                        'PhoneNumbers' => $mobile,
                        'SignName' => self::SIGN_NAME,
                        'TemplateCode' => self::TEMPLATE_CODE,
                        'TemplateParam' => json_encode(['code' => $code]),
                    ],
                ])
                ->request();
            $data = $result->toArray();
        } catch (ClientException $e) {
             // echo $e->getErrorMessage() . PHP_EOL;
        } catch (ServerException $e) {
            // echo $e->getErrorMessage() . PHP_EOL;
        }

        return $data;
    }
}