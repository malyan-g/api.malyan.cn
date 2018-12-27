<?php
/**
 * Created by PhpStorm.
 * User: M
 * Date: 17/7/5
 * Time: 下午5:36
 */

namespace app\controllers;

use YII;
use app\models\Order;
use app\models\Product;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use app\models\OrderAttach;
use app\models\ProductPrice;
use app\models\OrderAddress;
use yii\data\ActiveDataProvider;

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
        $requestData = Yii::$app->request->post();
        $page = (int) ArrayHelper::getValue($requestData, 'page', 1);
        $status = (int) ArrayHelper::getValue($requestData, 'status', 0);
        if($page > 0) {
            $this->data['code'] = self::API_CODE_SUCCESS;
            $query = Order::find()
                ->select(['id', 'order_number', 'total_amount', 'total_number', 'status'])
                ->where(['user_id' => $this->userInfo['id']]);

            // 订单状态
            if($status){
                $query->andWhere(['status' => $status]);
            }

            $allPages = (int) ceil($query->count()/6);
            $data = [
                'code' => self::API_CODE_SUCCESS,
                'msg' => self::API_CODE_SUCCESS_MSG,
                'allPages' => $allPages,
            ];

            if($page <= $allPages){
                $pageSize = 6;
                $order = $query->orderBy(['created_at' => SORT_DESC])
                    ->offset(($page-1) * $pageSize)
                    ->limit($pageSize)
                    ->asArray()
                    ->all();

                $orderArray = array_column($order, 'id');
                $orderAttach = OrderAttach::find()
                    ->select(['order_id', 'product_id'])
                    ->innerJoinWith('product')
                    ->where(['order_id' =>$orderArray])
                    ->asArray()
                    ->all();

                $attachData = [];
                foreach ($orderAttach as $val){
                    $attachData[$val['order_id']][] = $val;
                }

                foreach($order as $key => $val) {
                    $data['data'][$key] = [
                        'id' => $val['id'],
                        'orderNumber' => $val['order_number'],
                        'totalAmount' => $val['total_amount'],
                        'totalNumber' => $val['total_number'],
                        'status' => $val['status'],
                        'isMoreProduct' => count($attachData[$val['id']])
                    ];

                    if ($data['data'][$key]['isMoreProduct'] > 1) {
                        $_productData = [];
                        foreach ($attachData[$val['id']] as $v) {
                            $_productData[] = $v['product_id'];
                            $data['data'][$key]['images'][] =$v['product']['image'];
                        }
                        $data['data'][$key]['productData'] = json_encode($_productData);
                    } else {
                        $data['data'][$key]['title'] = $attachData[$val['id']][0]['product']['name'];
                        $data['data'][$key]['image'] = $attachData[$val['id']][0]['product']['image'];
                        $data['data'][$key]['productData'] = $attachData[$val['id']][0]['product_id'];
                    }
                }
            }
            $this->data = $data;
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
            $orderData = Order::find()
                ->innerJoinWith(['attach' => function(ActiveQuery $query) {
                    $query->select(['order_id', 'product_id', 'buy_number', 'buy_price'])->innerJoinWith(['product' => function(ActiveQuery $query){
                        $query->select(['id', 'name', 'image']);
                    }]);
                }])
                ->innerJoinWith('address')
                ->where([Order::tableName() . '.id' => $id, 'user_id' => $this->userInfo['id']])
                ->asArray()
                ->one();

            if($orderData){
                $orderData['address']['telNumber'] = substr_replace($orderData['address']['telNumber'], '****', 3,4);
                $data = [
                    'id' => $orderData['id'],
                    'orderNumber' => $orderData['order_number'],
                    'totalAmount' => $orderData['total_amount'],
                    'totalNumber' => $orderData['total_number'],
                    'status' => $orderData['status'],
                    'statusText' => Order::$statusArray[$orderData['status']],
                    'statusImage' => Order::$statusImageArray[$orderData['status']],
                    'serialNumber' => $orderData['serial_number'],
                    'paymentTime' => $this->getDate($orderData['payment_time']),
                    'deliveryTime' => $this->getDate($orderData['delivery_time']),
                    'completeTime' => $this->getDate($orderData['complete_time']),
                    'createdTime' => $this->getDate($orderData['created_at']),
                    'address' => $orderData['address']
                ];

                $productArray = [];
                foreach ($orderData['attach'] as $val){
                    $data['productData'][] = [
                        'id' => $val['product_id'],
                        'price' => $val['buy_price'],
                        'number' => $val['buy_number'],
                        'title' => $val['product']['name'],
                        'image' => $val['product']['image']
                    ];
                    $productArray[] = $val['product_id'];
                }
                $data['productArray'] = json_encode($productArray);
                $this->data = [
                    'code' => self::API_CODE_SUCCESS,
                    'msg' => self::API_CODE_SUCCESS_MSG,
                    'data' => $data
                ];
            }
        }
        return $this->data;
    }

    /**
     * 新增订单
     * @return mixed
     */
    public function actionAdd()
    {
        $requestData = Yii::$app->request->post();
        $orderData = json_decode(ArrayHelper::getValue($requestData, 'orderData'), true);
        $addressData = json_decode(ArrayHelper::getValue($requestData, 'addressData'), true);

        // 判断数据的合法性
        if(is_array($orderData) && $orderData && is_array($addressData) && $addressData){
            // 获取产品ID数据
            $productArray =  array_keys($orderData);
            $query = Product::find();
            // 会员查会员价格
            $isMember = $this->userInfo['is_member'] ? true : false;
            if($isMember){
                $memberId = $this->userInfo['member_id'];
                $query = $query
                    ->select([Product::tableName() . '.id',  'price', 'member_price'])
                    ->leftJoin(ProductPrice::tableName(), ProductPrice::tableName() . '.product_id=' . Product::tableName() . '.id and ' . ProductPrice::tableName() . '.member_id=' . $memberId);
            }else{
                $query = $query->select(['id', 'price']);
            }
            $productData = $query
                ->where([Product::tableName() . '.id' => $productArray, 'status' => Product::NORMAL_STATUS])
                ->asArray()
                ->all();

            if(count($productArray) === count($productData)){
                $order = new Order();
                $orderAddress= new OrderAddress();
                $order->user_id = $this->userInfo['id'];
                if($order->validate() && $orderAddress->load(['data' => $addressData], 'data') && $orderAddress->validate()){
                    $orderAttach = new OrderAttach();
                    $trans = Yii::$app->db->beginTransaction();
                    try {
                        $order->save();
                        $orderAddress->order_id = $order->id;
                        $orderAddress->save();
                        // 购买产品处理
                        foreach ($productData as $val){
                            $_orderAttach = clone  $orderAttach;
                            $_orderAttach->order_id = $order->id;
                            $_orderAttach->product_id = $val['id'];
                            $_orderAttach->buy_number = $orderData[$val['id']]['count'];
                            // 是否是会员价格
                            if($isMember){
                                $_orderAttach->buy_price = $val['member_price'] ? $val['member_price'] : $val['price'];
                            }else{
                                $_orderAttach->buy_price = $val['price'];
                            }
                            // 验证失败直接数据回滚
                            if($_orderAttach->validate()){
                                $_orderAttach->save();
                            }else{
                                $trans->rollBack();
                                return $this->data;
                            }

                            // 计算购买总价和购买总数量
                            $order->total_number += $_orderAttach->buy_number;
                            $order->total_amount += $_orderAttach->buy_price * $_orderAttach->buy_number;
                        }

                        $order->save();
                        $trans->commit();
                        $this->data = [
                            'code' => self::API_CODE_SUCCESS,
                            'msg' => self::API_CODE_SUCCESS_MSG,
                            'data' => [
                                'id' => $order->id,
                                'timeStamp' => '',
                                'nonceStr' => '',
                                'package' => '',
                                'paySign' => ''
                            ]
                        ];
                    } catch (\Exception $e) {
                        $trans->rollBack();
                    }
                }
            }
        }
        return $this->data;
    }

    /**
     * 支付
     * @return mixed
     */
    public function actionPayment()
    {
        $requestData = Yii::$app->request->post();
        $id = (int) ArrayHelper::getValue($requestData, 'id');
        if($id > 0){
            $model = Order::findOne(['id' => $id, 'user_id' => $this->userInfo['id'], 'status' => Order::ORDER_STATUS_NOT_PAY]);
            if($model){
                $this->data = [
                    'code' => self::API_CODE_SUCCESS,
                    'msg' => self::API_CODE_SUCCESS_MSG,
                    'data' => [
                        'id' => $id,
                        'timeStamp' => '',
                        'nonceStr' => '',
                        'package' => '',
                        'paySign' => ''
                    ]
                ];
            }
        }
        return $this->data;
    }

    /**
     * 取消
     * @return mixed
     */
    public function actionCancel()
    {
        $requestData = Yii::$app->request->post();
        $id = (int) ArrayHelper::getValue($requestData, 'id');
        if($id > 0){
            $model = Order::findOne(['id' => $id, 'user_id' => $this->userInfo['id'], 'status' => Order::ORDER_STATUS_NOT_PAY]);
            $model->status = Order::ORDER_STATUS_HAS_CANCEL;
            $model->complete_time = time();
            if ($model->save()) {
                $this->data = [
                    'code' => self::API_CODE_SUCCESS,
                    'msg' => self::API_CODE_SUCCESS_MSG
                ];
            } else {
                $this->data['msg'] = '服务器异常，请联系管理员';
            }
        }
        return $this->data;
    }

    /**
     * 确认收货
     * @return mixed
     */
    public function actionConfirmReceipt()
    {
        $requestData = Yii::$app->request->post();
        $id = (int) ArrayHelper::getValue($requestData, 'id');
        if($id > 0) {
            $model = Order::findOne(['id' => $id, 'user_id' => $this->userInfo['id'], 'status' => Order::ORDER_STATUS_STAY_RECEIVE_GOODS]);
            if ($model) {
                $model->status = Order::ORDER_STATUS_HAS_COMPLETE;
                $model->complete_time = time();
                if ($model->save()) {
                    $this->data = [
                        'code' => self::API_CODE_SUCCESS,
                        'msg' => self::API_CODE_SUCCESS_MSG
                    ];
                } else {
                    $this->data['msg'] = '服务器异常，请联系管理员';
                }
            }
        }
        return $this->data;
    }

    /**
     * 时间转换
     * @param $timestamp
     * @return false|int|string
     */
    public function getDate($timestamp)
    {
        return $timestamp ? date('Y-m-d H:i:s', $timestamp) : 0;
    }
}