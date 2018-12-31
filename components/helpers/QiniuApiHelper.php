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
 * 七牛云上传接口
 * Class QiniuApiHelper
 * @package app\components\helpers
 */
class QiniuApiHelper extends Object
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
     * 空间
     */
    const BUCKET = 'malyan';

    /**
     * 地区
     */
    const ZONE= 'east_china';

    /**
     * @param $updateFile
     * @param $filename
     * @return array|mixed
     * @throws \Exception
     */
    public static function upload($updateFile , $filename)
    {
        $qiniu = new Qiniu(self::ACCESS_KEY, self::SECRET_KEY,self::DOMAIN, self::BUCKET, self::ZONE);
        return $qiniu->uploadFile($updateFile, $filename);
    }

    /**
     * 删除文件
     * @param $filename
     * @throws \Exception
     */
    public static function delete($filename)
    {
        $qiniu = new Qiniu(self::ACCESS_KEY, self::SECRET_KEY,self::DOMAIN, self::BUCKET, self::ZONE);
        return $qiniu->delete($filename);
    }
}