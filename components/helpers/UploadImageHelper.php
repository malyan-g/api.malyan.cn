<?php
/**
 * Created by PhpStorm.
 * User: xywy
 * Date: 2017/3/25
 * Time: 15:24
 */

namespace app\components\helpers;


use yii\httpclient\Client;

/**
 * 图片上传到XS3
 * Class UploadImageHelper
 * @package app\components\helpers
 */
class UploadImageHelper
{
    /**
     * @param $filename
     * @param $content
     * @param string $dir
     * @param bool $thumb
     * @param int $thumb_width
     * @param int $thumb_height
     * @return array|mixed
     */
    public static function upload($filename,$content,$dir='qrcode',$thumb = false,$thumb_width=100,$thumb_height=100)
    {
        $time = time();
        $key = "alke7983kjfdsklnme3$#2343**&^%";
        $url = 'http://api.imgupload.xywy.com/streamupload.php';
        $method = 'POST';
        $data=array(
            'timestamp'=>$time,
            'sign' =>md5($key.$time),
            'format'=>'base64',
            'dir'=>$dir,
            'content'=>$content,
            'filename'=>$filename,
            'thumb'=>$thumb?1:0,
            'width'=>$thumb_width,
            'heigth'=>$thumb_height,
        );
        return HttpClientHelper::request($url,$method,$data,[],Client::FORMAT_JSON);
    }
}