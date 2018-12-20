<?php
/**
 * Created by PhpStorm.
 * User: M
 * Date: 17/7/5
 * Time: 下午5:36
 */

namespace app\controllers;

use YII;
use app\models\Product;
use yii\helpers\ArrayHelper;
use app\models\ProductPrice;
use app\models\ProductAttach;

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
            // 查询
            $query = Product::find();
            // 会员查会员价格
            $isMember = $this->userInfo['is_member'] ? true : false;
            if($isMember){
                $memberId = $this->userInfo['member_id'];
                $query = $query
                    ->select([Product::tableName() . '.id', 'name', 'image', 'label',  'price', 'member_price', 'describe'])
                    ->leftJoin(ProductPrice::tableName(), ProductPrice::tableName() . '.product_id=' . Product::tableName() . '.id and ' . ProductPrice::tableName() . '.member_id=' . $memberId);
            }else{
                $query = $query->select(['id', 'name', 'image', 'label',  'price']);
            }
            // 上架产品
            $query = $query->where(['status' => Product::NORMAL_STATUS]);
            $allPages = (int) ceil($query->count()/6);

            if($page <= $allPages){
                $pageSize = 6;
                $list = $query->offset(($page - 1) * $pageSize)
                    ->limit($pageSize)
                    ->asArray()
                    ->all();

                $data = [
                    'code' => self::API_CODE_SUCCESS,
                    'msg' => self::API_CODE_SUCCESS_MSG,
                    'allPages' => $allPages,
                ];

                foreach($list as $key => $val){
                    $data['data'][$key] = [
                        'id' => $val['id'],
                        'title' => $val['name'],
                        'price' => $val['price'],
                        'labelArray' => empty($val['label']) ?  [] : explode(',', $val['label']),
                        'imgUrl' => $val['image']
                    ];
                    if($isMember){
                        $data['data'][$key]['isMember'] =  !is_null($val['member_price']);
                        $data['data'][$key]['memberPrice'] = $val['member_price'] ? $val['member_price'] : 0;
                        $data['data'][$key]['memberName'] = $val['describe'] ? $val['describe'] : '';
                    }else{
                        $data['data'][$key]['isMember'] =  false;
                        $data['data'][$key]['memberPrice'] = 0;
                        $data['data'][$key]['memberName'] = '';
                    }
                }
                $this->data = $data;
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
        $requestData = Yii::$app->request->post();
        $id = (int) ArrayHelper::getValue($requestData, 'id');
        if($id > 0){
            $query = Product::find();
            // 会员查会员价格
            $isMember = $this->userInfo['is_member'] ? true : false;
            if($isMember){
                $memberId = $this->userInfo['member_id'];
                $query = $query
                    ->select([Product::tableName() . '.id', 'name', 'image',  'price', 'member_price', ProductPrice::tableName() . '.describe as memberName', 'banner',  ProductAttach::tableName() . '.describe'])
                    ->leftJoin(ProductPrice::tableName(), ProductPrice::tableName() . '.product_id=' . Product::tableName() . '.id and ' . ProductPrice::tableName() . '.member_id=' . $memberId);
            }else{
                $query = $query->select(['id', 'name', 'image',  'price', 'banner',  ProductAttach::tableName() . '.describe']);
            }
            $data = $query->innerJoin(ProductAttach::tableName(), ProductAttach::tableName() . '.product_id=' . Product::tableName() . '.id')
                ->where([Product::tableName() . '.id' => $id, 'status' => Product::NORMAL_STATUS])
                ->asArray()
                ->one();

            if($data){
                $this->data = [
                    'code' => self::API_CODE_SUCCESS,
                    'msg' => self::API_CODE_SUCCESS_MSG,
                    'data' => [
                        'id' => $data['id'],
                        'title' => $data['name'],
                        'price' => $data['price'],
                        'carouselData' => $data['banner'] ? explode(',', $data['banner']) : [],
                        'detailData' => $data['describe'] ? explode(',', $data['describe']) : []
                    ]
                ];
                if($isMember){
                    $this->data['data']['isMember'] =  !is_null($data['member_price']);
                    $this->data['data']['memberPrice'] = $data['member_price'] ? $data['member_price'] : 0;
                    $this->data['data']['memberName'] = $data['memberName'] ? $data['memberName'] : '';
                }else{
                    $this->data['data']['isMember'] =  false;
                    $this->data['data']['memberPrice'] = 0;
                    $this->data['data']['memberName'] = '';
                }
            }
        }
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
            if($productArray){
                $query = Product::find();

                // 会员查会员价格
                $isMember = $this->userInfo['is_member'] ? true : false;
                if($isMember){
                    $memberId = $this->userInfo['member_id'];
                    $query = $query
                        ->select([Product::tableName() . '.id', 'name', 'image', 'price', 'member_price', 'describe'])
                        ->leftJoin(ProductPrice::tableName(), ProductPrice::tableName() . '.product_id=' . Product::tableName() . '.id and ' . ProductPrice::tableName() . '.member_id=' . $memberId);
                }else{
                    $query = $query->select(['id', 'name', 'image', 'price']);
                }
                $list = $query->where([Product::tableName() . '.id' => $productArray, 'status' => Product::NORMAL_STATUS])
                    ->asArray()
                    ->all();

                $data = [];
                foreach ($list as $key => $val){
                    $data[$val['id']] = [
                        'id' => $val['id'],
                        'title' => $val['name'],
                        'imgUrl' => $val['image']
                    ];
                    if($isMember){
                        $data[$val['id']]['price'] = $val['member_price'] ? $val['member_price'] : $val['price'];
                        $data[$val['id']]['isMember'] =  !is_null($val['member_price']);
                        $data[$val['id']]['memberName'] = $val['describe'] ? $val['describe'] : '';
                    }else{
                        $data[$val['id']]['price'] = $val['price'];
                        $data[$val['id']]['isMember'] =  false;
                        $data[$val['id']]['memberName'] = '';
                    }
                }

                if($data){
                    $this->data = [
                        'code' => self::API_CODE_SUCCESS,
                        'msg' => self::API_CODE_SUCCESS_MSG,
                        'data' => $data
                    ];
                }
            }
        }
        return $this->data;
    }
}
