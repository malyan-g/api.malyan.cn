<?php
/**
 * Created by PhpStorm.
 * User: xywy
 * Date: 2017/3/25
 * Time: 15:28
 */

namespace app\components\helpers;

use Codeception\Command\Clean;
use yii\httpclient\Client;
use yii\base\Object;

class HttpClientHelper extends Object
{
    /**
     * @param $url
     * @param $method
     * @param string $data
     * @param array $headers
     * @param string $format
     * @return array|mixed
     */
    public static function request($url,$method,$data='',$headers=array(),$format='')
    {
        $request = ( new Client())->createRequest();
        $request->setMethod($method)->setUrl($url);
        if($headers){
            $request->setHeaders($headers);
        }
        if($data){
            $request->setData($data);
        }
        if($format){
            $request->setFormat($format);
        }
        $response = $request->send();
        return $response->getData();
    }
}