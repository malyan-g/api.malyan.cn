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
     * @return bool
     */
    public static function word2pdf($wordPath, $pdfPath,$path)
    {
        if(file_exists($pdfPath)){
            unlink($pdfPath);
        }
        $cmd = 'libreoffice --headless --convert-to pdf:writer_pdf_Export ' . $wordPath . ' --outdir ' . $path;
        try{
            ob_start();
            system($cmd);
            $result = ob_get_contents();
            ob_clean();
            ob_end_flush();
            return $result ? true : false;
        }catch (\Exception $e){
        }
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
        $im->BorderImage(new \ImagickPixel("#ffffff") , 8,8);
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