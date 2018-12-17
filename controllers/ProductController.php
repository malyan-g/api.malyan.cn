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
 * 产品接口
 * Class ProductController
 * @package app\controllers
 */
class ProductController extends Controller
{
    /**
     * 商品列表
     * @return mixed
     */
    public function actionList()
    {
        $requestData = Yii::$app->request->get();
        $page = (int) ArrayHelper::getValue($requestData, 'page', 1);
        if($page > 0){
            $this->data['code'] = self::API_CODE_SUCCESS;
            $this->data['allPages'] = 3;
            $this->data['data'] = [
                [
                    'id' => 1,
                    'title' => 'LANCOME兰蔻小黑瓶精华肌底液',
                    'price' => '188.00',
                    'memberPrice' => '168.00',
                    'memberName' => '银卡专享',
                    'labelArray' => [
                        '纯天然',
                        '价格实惠',
                        '优质产品'
                    ],
                    'imgUrl' => 'http://mz.djmall.xmisp.cn/files/product/20161201/148057921620_middle.jpg'
                ],
                [
                    'id' => 2,
                    'title' => 'LANCOME兰蔻小黑瓶精华肌底液-1',
                    'price' => '188.00',
                    'memberPrice' => '168.00',
                    'memberName' => '银卡专享',
                    'labelArray' => [
                        '纯天然',
                        '价格实惠',
                        '优质产品'
                    ],
                    'imgUrl' => 'http://mz.djmall.xmisp.cn/files/product/20161201/148057922659_middle.jpg'
                ],
                [
                    'id' => 3,
                    'title' => 'LANCOME兰蔻小黑小黑瓶精华小黑瓶精华瓶精华肌底液-2',
                    'price' => '188.00',
                    'memberPrice' => '168.00',
                    'memberName' => '银卡专享',
                    'labelArray' => [
                        '纯天然',
                        '价格实惠',
                        '优质产品'
                    ],
                    'imgUrl' => 'http://mz.djmall.xmisp.cn/files/product/20161201/148057923813_middle.jpg'
                ],
                [
                    'id' => 4,
                    'title' => 'LANCOME兰蔻小黑瓶精华肌底液-3',
                    'price' => '188.00',
                    'memberPrice' => '168.00',
                    'memberName' => '银卡专享',
                    'labelArray' => [
                        '纯天然',
                        '价格实惠',
                        '优质产品'
                    ],
                    'imgUrl' => 'http://mz.djmall.xmisp.cn/files/product/20161201/148057924965_middle.jpg'
                ],
                [
                    'id' => 5,
                    'title' => 'LANCOME兰蔻小小黑瓶精华黑瓶精华肌底液-4',
                    'price' => '188.00',
                    'memberPrice' => '168.00',
                    'memberName' => '银卡专享',
                    'labelArray' => [
                        '纯天然',
                        '价格实惠',
                        '优质产品'
                    ],
                    'imgUrl' => 'http://mz.djmall.xmisp.cn/files/product/20161201/148057925958_middle.jpg'
                ],
                [
                    'id' => 6,
                    'title' => 'LANCOME兰蔻小黑瓶精华肌底液-5',
                    'price' => '188.00',
                    'memberPrice' => '168.00',
                    'memberName' => '银卡专享',
                    'labelArray' => [
                        '纯天然',
                        '价格实惠',
                        '优质产品'
                    ],
                    'imgUrl' => 'http://mz.djmall.xmisp.cn/files/product/20161201/148057923813_middle.jpg'
                ]
            ];
        }
        return $this->data;
    }

    /**
     * 产品详情
     * @return mixed
     */
    public function actionDetail()
    {
        $requestData = Yii::$app->request->get();
        $idArray = (int) ArrayHelper::getValue($requestData, 'id', []);
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