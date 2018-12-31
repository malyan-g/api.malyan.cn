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
    /**
     * word转pdf
     * @param $wordPath
     * @param $pdfPath
     * @param $path
     */
    public static function word2pdf($wordPath, $pdfPath,$path)
    {
        if(file_exists($pdfPath)){
            unlink($pdfPath);
        }
        $cmd = 'libreoffice --headless --convert-to pdf:writer_pdf_Export ' . $wordPath . ' --outdir ' . $path;
        system($cmd);
    }

    /**
     * pdf转png
     * @param $pdf
     * @param $filePath
     * @param int $page
     * @return bool|string
     */
    public static function pdf2png($pdf , $filePath, $page=-1)
    {
        if(!extension_loaded('imagick'))
        {
            return false;
        }
        if(!file_exists($pdf))
        {
            return false;
        }
        $im = new \Imagick();
        $im->setResolution(120,120);
        $im->setCompressionQuality(100);
        if($page==-1) {
            $im->readImage($pdf);
        }else {
            $im->readImage($pdf . "[" . $page . "]");
        }
        $Return = '';
        foreach ($im as $Key => $Var){
            $Var->setImageFormat('png');
            if($Var->writeImage($filePath) == true){
                $Return = $filePath;
            }
        }
        return $Return;
    }
}