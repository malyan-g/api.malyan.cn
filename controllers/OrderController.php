<?php
/**
 * Created by PhpStorm.
 * User: M
 * Date: 17/7/5
 * Time: 下午5:36
 */

namespace app\controllers;

use YII;
use yii\helpers\ArrayHelper;

/**
 * 订单接口
 * Class OrderController
 * @package app\controllers
 */
class OrderController extends Controller
{
    /**
     * 商品列表
     * @return mixed
     */
    public function actionList()
    {
        $requestData = Yii::$app->request->get();
        $page = (int) ArrayHelper::getValue($requestData, 'page', 1);
        $status= (int) ArrayHelper::getValue($requestData, 'status', 0);
        if($page > 0) {
            $this->data['code'] = self::API_CODE_SUCCESS;
            $this->data['allPages'] = 1;
            if ($status != 3) {
            $this->data['data'] = [
                [
                    'title' => 'LANCOME兰蔻小黑瓶精华肌底液',
                    'imgUrl' => 'http://mz.djmall.xmisp.cn/files/product/20161201/148057921620_middle.jpg',
                    'totalNum' => 8,
                    'totalAmount' => 188 * 8,
                    'status' => $status
                ],
                [
                    'title' => 'LANCOME兰蔻小黑瓶精华肌底液-1',
                    'imgUrl' => 'http://mz.djmall.xmisp.cn/files/product/20161201/148057922659_middle.jpg',
                    'totalNum' => 4,
                    'totalAmount' => 188 * 4,
                    'status' => $status
                ],
                [
                    'title' => 'LANCOME兰蔻小黑瓶精华肌底液-2',
                    'imgUrl' => 'http://mz.djmall.xmisp.cn/files/product/20161201/148057923813_middle.jpg',
                    'totalNum' => 20,
                    'totalAmount' => 188 * 20,
                    'status' => $status
                ],
                [
                    'title' => 'LANCOME兰蔻小黑瓶精华肌底液-3',
                    'imgUrl' => 'http://mz.djmall.xmisp.cn/files/product/20161201/148057924965_middle.jpg',
                    'totalNum' => 2,
                    'totalAmount' => 188 * 2,
                    'status' => $status
                ],
                [
                    'title' => 'LANCOME兰蔻小黑瓶精华肌底液-4',
                    'imgUrl' => 'http://mz.djmall.xmisp.cn/files/product/20161201/148057925958_middle.jpg',
                    'totalNum' => 10,
                    'totalAmount' => 188 * 10,
                    'status' => $status
                ],
                [
                    'title' => 'LANCOME兰蔻小黑瓶精华肌底液-5',
                    'imgUrl' => 'http://mz.djmall.xmisp.cn/files/product/20161201/148057923813_middle.jpg',
                    'totalNum' => 1,
                    'totalAmount' => 188,
                    'status' => $status
                ]
            ];
             }else{
                $this->data['data']  = [];
            }
        }
        return $this->data;
    }

    /**
     * 产品详情
     * @return mixed
     */
    public function actionDetail()
    {
        $this->data['code'] = self::API_CODE_SUCCESS;
        $this->data['data'] = [
            'title' => 'LANCOME兰蔻小黑瓶精华肌底液',
            'detail' => '东西还可以，好评~,东西还可以，好评~东西还可以，好评~东西还可以，好评~东西还可以，好评~东西还可以，好评~东西还可以，好评~东西还可以，好评~',
            'carouselData' => [
                "http://mz.djmall.xmisp.cn/files/product/20161201/148057921620_middle.jpg",
                "http://mz.djmall.xmisp.cn/files/product/20161201/148057922659_middle.jpg",
                "http://mz.djmall.xmisp.cn/files/product/20161201/148057923813_middle.jpg",
                "http://mz.djmall.xmisp.cn/files/product/20161201/148057924965_middle.jpg",
                "http://mz.djmall.xmisp.cn/files/product/20161201/148057925958_middle.jpg"
            ],
            'imgData' => [
                "http://mz.djmall.xmisp.cn/files/product/20161201/148057921620_middle.jpg",
                "http://mz.djmall.xmisp.cn/files/product/20161201/148057922659_middle.jpg",
                "http://mz.djmall.xmisp.cn/files/product/20161201/148057923813_middle.jpg",
                "http://mz.djmall.xmisp.cn/files/product/20161201/148057924965_middle.jpg",
                "http://mz.djmall.xmisp.cn/files/product/20161201/148057925958_middle.jpg"
            ]
        ];
        return $this->data;
    }
}