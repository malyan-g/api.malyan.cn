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
        $requestData = Yii::$app->request->post();
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
        $requestData = Yii::$app->request->post();
        $id= (int) ArrayHelper::getValue($requestData, 'id', 1);
        $this->data['code'] = self::API_CODE_SUCCESS;
        $this->data['data'] = [
            'id' => $id,
            'title' => 'Apple iPhone X手机 苹果x 全网通4G 全面屏手机 银色 官方标配 256G',
            'price' => '188.00',
            'memberPrice' => '168.00',
            'memberName' => '银卡专享',
            'carouselData' => [
                "https://m.360buyimg.com/n12/jfs/t11317/108/1080677336/325163/f4c2a03a/59fd8b17Nbe2fcca3.jpg!q70.jpg",  "https://m.360buyimg.com/n12/jfs/t11575/282/348533702/60173/d75cd1cc/59edb8d6N688b420f.jpg!q70.jpg",  "https://m.360buyimg.com/n12/jfs/t11536/279/360605865/15194/442cab0b/59edb8d3N163a7608.jpg!q70.jpg",
                "https://m.360buyimg.com/n12/s750x750_jfs/t9733/126/2033941175/68120/a4eb4468/59edb8d6N37bea6f7.jpg!q70.jpg",
                "https://m.360buyimg.com/n12/s750x750_jfs/t10744/195/2053933852/71608/94425578/59edb8d6Ne28c70ff.jpg!q70.jpg"
            ],
            'detailData' => [
                "https://haitao.nosdn1.127.net/8b8f60cb94b148e485dd50934e35ecca1511959468798jal1mola10610.jpg?imageView&quality=98&crop=0_0_750_500&imageView&thumbnail=710x0&quality=85",
                "https://haitao.nosdn1.127.net/8b8f60cb94b148e485dd50934e35ecca1511959468798jal1mola10610.jpg?imageView&quality=98&crop=0_500_750_500&imageView&thumbnail=710x0&quality=85",
                "https://haitao.nosdn1.127.net/8b8f60cb94b148e485dd50934e35ecca1511959468798jal1mola10610.jpg?imageView&quality=98&crop=0_1000_750_500&imageView&thumbnail=710x0&quality=85",
                "https://haitao.nosdn1.127.net/8b8f60cb94b148e485dd50934e35ecca1511959468798jal1mola10610.jpg?imageView&quality=98&crop=0_1500_750_500&imageView&thumbnail=710x0&quality=85",
                "https://haitao.nosdn1.127.net/8b8f60cb94b148e485dd50934e35ecca1511959468798jal1mola10610.jpg?imageView&quality=98&crop=0_2000_750_500&imageView&thumbnail=710x0&quality=85",
                "https://haitao.nosdn1.127.net/8b8f60cb94b148e485dd50934e35ecca1511959468798jal1mola10610.jpg?imageView&quality=98&crop=0_2500_750_500&imageView&thumbnail=710x0&quality=85",
                "https://haitao.nosdn1.127.net/8b8f60cb94b148e485dd50934e35ecca1511959468798jal1mola10610.jpg?imageView&quality=98&crop=0_3000_750_500&imageView&thumbnail=710x0&quality=85",
                "https://haitao.nosdn1.127.net/8b8f60cb94b148e485dd50934e35ecca1511959468798jal1mola10610.jpg?imageView&quality=98&crop=0_3500_750_500&imageView&thumbnail=710x0&quality=85",
                "https://haitao.nosdn1.127.net/8b8f60cb94b148e485dd50934e35ecca1511959468798jal1mola10610.jpg?imageView&quality=98&crop=0_4000_750_500&imageView&thumbnail=710x0&quality=85",
                "https://haitao.nosdn1.127.net/8b8f60cb94b148e485dd50934e35ecca1511959468798jal1mola10610.jpg?imageView&quality=98&crop=0_4500_750_500&imageView&thumbnail=710x0&quality=85",
                "https://haitao.nosdn1.127.net/8b8f60cb94b148e485dd50934e35ecca1511959468798jal1mola10610.jpg?imageView&quality=98&crop=0_5000_750_500&imageView&thumbnail=710x0&quality=85",
                "https://haitao.nosdn1.127.net/8b8f60cb94b148e485dd50934e35ecca1511959468798jal1mola10610.jpg?imageView&quality=98&crop=0_5500_750_500&imageView&thumbnail=710x0&quality=85",
                "https://haitao.nosdn1.127.net/8b8f60cb94b148e485dd50934e35ecca1511959468798jal1mola10610.jpg?imageView&quality=98&crop=0_6000_750_500&imageView&thumbnail=710x0&quality=85",
                "https://haitao.nos.netease.com/a108a6ae73914a91b7e07b8cc0ad32ad1511959470997jal1mqad10611.jpg?imageView&quality=98&crop=0_7500_750_500&imageView&thumbnail=710x0&quality=85",
                "https://haitao.nos.netease.com/a108a6ae73914a91b7e07b8cc0ad32ad1511959470997jal1mqad10611.jpg?imageView&quality=98&crop=0_8000_750_500&imageView&thumbnail=710x0&quality=85",
                "https://haitao.nos.netease.com/a108a6ae73914a91b7e07b8cc0ad32ad1511959470997jal1mqad10611.jpg?imageView&quality=98&crop=0_8500_750_500&imageView&thumbnail=710x0&quality=85",
                "https://haitao.nos.netease.com/a108a6ae73914a91b7e07b8cc0ad32ad1511959470997jal1mqad10611.jpg?imageView&quality=98&crop=0_9000_750_500&imageView&thumbnail=710x0&quality=85",
                "https://haitao.nos.netease.com/a108a6ae73914a91b7e07b8cc0ad32ad1511959470997jal1mqad10611.jpg?imageView&quality=98&crop=0_9500_750_376&imageView&thumbnail=710x0&quality=85"
            ]
        ];
        return $this->data;
    }

    /**
     * 购物车产品详情
     * @return mixed
     */
    public function actionCartDetail()
    {
        $requestData = Yii::$app->request->post();
        $productData = ArrayHelper::getValue($requestData, 'productData');
        if($productData){
            $productArray =  explode(',', $productData);
            $this->data['code'] = self::API_CODE_SUCCESS;
            foreach ($productArray as $val) {
                $this->data['data'][$val] = [
                    'id' => $val,
                    'title' => 'LANCOME兰蔻小小黑瓶精华黑瓶精华肌底液',
                    'price' => '168.00',
                    'memberName' => '银卡专享',
                    'imgUrl' => 'http://mz.djmall.xmisp.cn/files/product/20161201/148057924965_middle.jpg'
                ];
            }
        }

        return $this->data;
    }
}