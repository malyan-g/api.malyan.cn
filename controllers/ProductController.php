<?php
/**
 * Created by PhpStorm.
 * User: M
 * Date: 17/7/5
 * Time: 下午5:36
 */

namespace app\controllers;

use app\models\ProductBalancePrice;
use app\models\User;
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
            $user = User::findOne($this->userId);
            if($user){
                // 查询
                $query = Product::find();
                $selectData  = [Product::tableName() . '.id', 'name', 'image', 'label',  'price'];
                // 会员查会员价格
                $isMember = $user->is_member ? true : false;
                if($isMember){
                    $selectData = array_merge($selectData, ['member_price', ProductPrice::tableName() . '.describe']) ;
                    $query = $query->leftJoin(ProductPrice::tableName(), ProductPrice::tableName() . '.product_id=' . Product::tableName() . '.id and ' . ProductPrice::tableName() . '.member_id=' . $user->member_id);
                }
                // 支持余额价格
                $isBalance = $user->is_balance && ($user->balance_expire_time == 0 || $user->balance_expire_time > time()) ? true : false;
                if($isBalance){
                    $selectData = array_merge($selectData, ['balance_price', ProductBalancePrice::tableName() . '.describe as balance_describe']) ;
                    $query = $query->leftJoin(ProductBalancePrice::tableName(), ProductBalancePrice::tableName() . '.product_id=' . Product::tableName() . '.id and ' . ProductBalancePrice::tableName() . '.balance_id=' . $user->balance_id);
                }
                // 上架产品
                $query = $query->select($selectData)->where(['status' => Product::NORMAL_STATUS]);
                $allPages = (int) ceil($query->count()/6);

                if($page <= $allPages){
                    $pageSize = 6;
                    $list = $query->orderBy(['sort' => SORT_ASC,  'created_at' => SORT_ASC])
                        ->offset(($page - 1) * $pageSize)
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
                        if($isMember && $val['member_price']){
                            $data['data'][$key]['isMember'] =  true;
                            $data['data'][$key]['memberPrice'] = $val['member_price'];
                            $data['data'][$key]['memberName'] = $val['describe'];
                        }else if($isBalance && $val['balance_price']){
                            $data['data'][$key]['isMember'] =  true;
                            $data['data'][$key]['memberPrice'] = $val['balance_price'];
                            $data['data'][$key]['memberName'] = $val['balance_describe'];
                        }else{
                            $data['data'][$key]['isMember'] =  false;
                            $data['data'][$key]['memberPrice'] = 0;
                            $data['data'][$key]['memberName'] = '';
                        }
                    }
                    $this->data = $data;
                }
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
            $user = User::findOne($this->userId);
            if($user){
                // 商品详情
                $productData = Product::find()
                    ->select(['id', 'name', 'image',  'price', 'is_balance', 'banner', 'describe'])
                    ->innerJoin(ProductAttach::tableName(), ProductAttach::tableName() . '.product_id=' . Product::tableName() . '.id')
                    ->where([Product::tableName() . '.id' => $id, 'status' => Product::NORMAL_STATUS])
                    ->asArray()
                    ->one();
                if($productData){
                    // 查询价格
                    $isBalance = $user->is_balance && ($user->balance_expire_time == 0 || $user->balance_expire_time > time()) ? true : false;
                    if($productData['is_balance'] && $isBalance){ // 余额价
                        $priceData = ProductBalancePrice::find()
                            ->select(['balance_price as member_price', 'describe'])
                            ->where(['product_id' => $id, 'balance_id' => $user->balance_id])
                            ->asArray()
                            ->one();
                        $isMember = true;
                    }else if($user->is_member){ // 会员查会员价格
                        $priceData = ProductPrice::find()
                            ->select(['member_price', 'describe'])
                            ->where(['product_id' => $id, 'member_id' => $user->member_id])
                            ->asArray()
                            ->one();
                        $isMember = true;
                    }else{
                        $isMember = false;
                    }

                    $this->data = [
                        'code' => self::API_CODE_SUCCESS,
                        'msg' => self::API_CODE_SUCCESS_MSG,
                        'data' => [
                            'id' => $productData['id'],
                            'title' => $productData['name'],
                            'price' => $productData['price'],
                            'carouselData' => $productData['banner'] ? explode(',', $productData['banner']) : [],
                            'detailData' => $productData['describe'] ? explode(',', $productData['describe']) : []
                        ]
                    ];
                    if($isMember && $priceData){
                        $this->data['data']['isMember'] =  true;
                        $this->data['data']['memberPrice'] = $priceData['member_price'];
                        $this->data['data']['memberName'] = $priceData['describe'];
                    }else{
                        $this->data['data']['isMember'] =  false;
                        $this->data['data']['memberPrice'] = 0;
                        $this->data['data']['memberName'] = '';
                    }
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
                $user = User::findOne($this->userId);
                if($user){
                    $query = Product::find();
                    $selectData  = [Product::tableName() . '.id', 'name', 'image',  'price'];
                    // 会员查会员价格
                    $isMember = $user->is_member ? true : false;
                    if($isMember){
                        $selectData = array_merge($selectData, ['member_price', ProductPrice::tableName() . '.describe']) ;
                        $query = $query->leftJoin(ProductPrice::tableName(), ProductPrice::tableName() . '.product_id=' . Product::tableName() . '.id and ' . ProductPrice::tableName() . '.member_id=' . $user->member_id);
                    }
                    // 支持余额价格
                    $isBalance = $user->is_balance && ($user->balance_expire_time == 0 || $user->balance_expire_time > time()) ? true : false;
                    if($isBalance){
                        $selectData = array_merge($selectData, ['balance_price', ProductBalancePrice::tableName() . '.describe as balance_describe', 'is_balance']) ;
                        $query = $query->leftJoin(ProductBalancePrice::tableName(), ProductBalancePrice::tableName() . '.product_id=' . Product::tableName() . '.id and ' . ProductBalancePrice::tableName() . '.balance_id=' . $user->balance_id);
                    }
                    // 上架产品
                    $list = $query->select($selectData)
                        ->where([Product::tableName() . '.id' => $productArray, 'status' => Product::NORMAL_STATUS])
                        ->asArray()
                        ->all();

                    foreach($list as $key => $val){
                        $data[$val['id']] = [
                            'id' => $val['id'],
                            'title' => $val['name'],
                            'imgUrl' => $val['image']
                        ];
                        if($isMember && $val['member_price']){
                            $data[$val['id']]['price'] = $val['member_price'];
                            $data[$val['id']]['isMember'] =  true;
                            $data[$val['id']]['isBalance'] =  false;
                            $data[$val['id']]['memberName'] = $val['describe'];
                        }else if($isBalance && $val['is_balance'] && $val['balance_price']){
                            $data[$val['id']]['price'] = $val['balance_price'];
                            $data[$val['id']]['isMember'] =  true;
                            $data[$val['id']]['isBalance'] =  true;
                            $data[$val['id']]['memberName'] = $val['balance_describe'];
                        }else{
                            $data[$val['id']]['price'] = $val['price'];
                            $data[$val['id']]['isMember'] =  false;
                            $data[$val['id']]['isBalance'] =  false;
                            $data[$val['id']]['memberName'] = '';
                        }
                    }

                    if($data){
                        $this->data = [
                            'code' => self::API_CODE_SUCCESS,
                            'msg' => self::API_CODE_SUCCESS_MSG,
                            'userBalance' => $user->balance_amount,
                            'data' => $data
                        ];
                    }
                }
            }
        }
        return $this->data;
    }
}
