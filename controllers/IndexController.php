<?php
/**
 * Created by PhpStorm.
 * User: M
 * Date: 17/7/5
 * Time: 下午5:36
 */

namespace app\controllers;

/**
 * 首页接口
 * Class IndexController
 * @package app\controllers
 */
class IndexController extends Controller
{
    /**
     * 首页接口
     * @return array
     */
    public function actionIndex()
    {
        $this->data = [
            'code' => self::API_CODE_SUCCESS,
            'data' => [
                'bannerArray' => [
                    'http://img.malyan.cn/banner-1.jpg',
                    'http://img.malyan.cn/banner-2.jpg',
                    'http://img.malyan.cn/banner-3.jpg',
                    'http://img.malyan.cn/banner-4.jpg',
                    'http://img.malyan.cn/banner-5.jpg'
                ],
                'productArray' => [
                    'http://img.malyan.cn/WechatIMG18.jpeg',
                    'http://img.malyan.cn/WechatIMG10.jpeg'
                ],
                'recruitArray' => [
                    'http://img.malyan.cn/WechatIMG11.jpeg'
                ]
            ]
        ];
        return $this->data;
    }
}
