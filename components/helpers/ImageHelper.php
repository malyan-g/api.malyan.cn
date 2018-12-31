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
class ImageHelper extends Object
{
    public static function word2pdf($wordPath, $path)
    {
        $cmd = 'export HOME=/tmp && libreoffice --headless --convert-to pdf:writer_pdf_Export ' . $wordPath . ' --outdir ' . $path;
        var_dump(system($cmd));
    }
}