<?php
/**
 * Created by PhpStorm.
 * User: M
 * Date: 17/7/5
 * Time: 下午5:36
 */

namespace app\controllers;

use YII;
use app\models\User;
use app\models\Order;
use app\models\Product;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use app\models\OrderAttach;
use app\models\ProductPrice;
use app\models\OrderAddress;
use app\models\ProductBalancePrice;
use app\components\helpers\ExpressApiHelper;

/**
 * 订单接口
 * Class OrderController
 * @package app\controllers
 */
class OrderController extends Controller
{
    /**
     * 订单列表
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
                ->select(['id', 'order_number', 'total_amount', 'total_number', 'cash_amount',  'status'])
                ->where(['user_id' => $this->userId]);

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
                        'totalAmount' => $val['status'] == Order::ORDER_STATUS_HAS_CANCEL ? $val['total_amount'] : $val['cash_amount'],
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
                ->where([Order::tableName() . '.id' => $id, 'user_id' => $this->userId])
                ->asArray()
                ->one();

            if($orderData){
                /*$orderData['address']['telNumber'] = substr_replace($orderData['address']['telNumber'], '****', 3,4);*/
                $data = [
                    'id' => $orderData['id'],
                    'orderNumber' => $orderData['order_number'],
                    'totalAmount' => $orderData['total_amount'],
                    'cashAmount' => $orderData['cash_amount'],
                    'balanceAmount' => $orderData['balance_amount'],
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

                if($orderData['status'] == Order::ORDER_STATUS_NOT_PAY){
                    $statusTime = $orderData['created_at'] + 24*3600 - time();
                    $h = floor($statusTime/3600);
                    $i = floor(($statusTime - $h*3600)/60);
                    $data['statusTime'] = '（支付剩余' . $h . '时' . $i .'分）';
                }
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
        $_orderData = json_decode(ArrayHelper::getValue($requestData, 'orderData'), true);
        $addressData = json_decode(ArrayHelper::getValue($requestData, 'addressData'), true);

        // 判断数据的合法性
        if(is_array($_orderData) && $_orderData && is_array($addressData) && $addressData){
            $orderData = [];
            foreach ($_orderData as $val){
                $orderData[$val['id']] = $val;
            }
            // 获取产品ID数据
            $productArray =  array_values(array_column($_orderData, 'id'));
            $user = User::findOne($this->userId);
            if($user && $productArray){
                $query = Product::find();
                $selectData = [Product::tableName() . '.id',  'price', 'is_balance'];
                //  会员查会员价格
                if($user->is_member){
                    $selectData = array_merge($selectData, ['member_price']) ;
                    $query = $query->leftJoin(ProductPrice::tableName(), ProductPrice::tableName() . '.product_id=' . Product::tableName() . '.id and ' . ProductPrice::tableName() . '.member_id=' . $user->member_id);
                }
                // 支持余额价格
                $isBalance = $user->is_balance && ($user->balance_expire_time == 0 || $user->balance_expire_time > time()) ? true : false;
                if($isBalance) {
                    $selectData = array_merge($selectData, ['balance_price']);
                    $query = $query->leftJoin(ProductBalancePrice::tableName(), ProductBalancePrice::tableName() . '.product_id=' . Product::tableName() . '.id and ' . ProductBalancePrice::tableName() . '.balance_id=' . $user->balance_id);
                }

                $productData = $query->select($selectData)
                    ->where([Product::tableName() . '.id' => $productArray, 'status' => Product::NORMAL_STATUS])
                    ->indexBy('id')
                    ->asArray()
                    ->all();

                if(count($productArray) === count($productData)) {
                    $order = new Order();
                    $orderAddress = new OrderAddress();
                    $order->user_id = $this->userId;
                    if ($order->validate() && $orderAddress->load(['data' => $addressData], 'data') && $orderAddress->validate()) {
                        $orderAttach = new OrderAttach();
                        $trans = Yii::$app->db->beginTransaction();
                        try {
                            $order->save();
                            $orderAddress->order_id = $order->id;
                            $orderAddress->save();
                            // 购买产品处理
                            $balance_amount = 0;
                            foreach ($productArray as $val) {
                                $_orderAttach = clone  $orderAttach;
                                $_orderAttach->order_id = $order->id;
                                $_orderAttach->product_id = $val;
                                $_orderAttach->buy_number = $orderData[$val]['count'];
                                $_orderAttach->is_balance = $productData[$val]['is_balance'];
                                // 是否是会员价格
                                if ($user->is_member && $productData[$val]['member_price']) {
                                    $_orderAttach->buy_price = $productData[$val]['member_price'];
                                }else if ($isBalance && $productData[$val]['is_balance'] && $productData[$val]['balance_price']) {
                                    $_orderAttach->buy_price = $productData[$val]['balance_price'];
                                }else {
                                    $_orderAttach->buy_price = $productData[$val]['price'];
                                }

                                // 验证失败直接数据回滚
                                if ($_orderAttach->validate()) {
                                    $_orderAttach->save();
                                } else {
                                    $trans->rollBack();
                                    return $this->data;
                                }

                                // 商品可余额付款金额
                                if($productData[$val]['is_balance'] ){
                                    $balance_amount += $_orderAttach->buy_price * $_orderAttach->buy_number;
                                }

                                // 计算购买总价和购买总数量
                                $order->total_number += $_orderAttach->buy_number;
                                $order->total_amount += $_orderAttach->buy_price * $_orderAttach->buy_number;
                            }

                            // 余额付款实际金额
                            $order->balance_amount = $balance_amount  > $user->balance_amount ? $user->balance_amount : $balance_amount;
                            // 有余额购买
                            if($order->balance_amount){
                                // 用户余额
                                $user->balance_amount = $user->balance_amount - $order->balance_amount;
                                //  全是余额购买，直接扣款
                                if($order->balance_amount === $order->total_amount){
                                    $order->status = Order::ORDER_STATUS_STAY_SEND_GOODS;
                                    $order->payment_time = $order->created_at;
                                    // 余额小于300，30天内不续费，取消会员价购买资格
                                    if($user->balance_amount  < 300){
                                        $user->balance_expire_time = time() + 30 * 24 * 3600;
                                    }
                                }
                                $user->save();
                            }
                            // 实际现金付款金额
                            $order->cash_amount = $order->total_amount - $order->balance_amount;
                            $order->save();
                            $trans->commit();

                            // 订单是否支付
                            if($order->status === Order::ORDER_STATUS_STAY_SEND_GOODS){
                               $data = [
                                   'isPayment' => true,
                                   'id' => $order->id
                                ];
                            }else{
                                $data = [
                                    'isPayment' => false,
                                    'id' => $order->id,
                                    'timeStamp' => '',
                                    'nonceStr' => '',
                                    'package' => '',
                                    'paySign' => ''
                                ];
                            }
                            $this->data = [
                                'code' => self::API_CODE_SUCCESS,
                                'msg' => self::API_CODE_SUCCESS_MSG,
                                'data' => $data
                            ];
                        } catch (\Exception $e) {
                            $trans->rollBack();
                        }
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
        $id = (int)  Yii::$app->request->post('id');
        if($id > 0){
            $model = Order::findOne(['id' => $id, 'user_id' => $this->userId, 'status' => Order::ORDER_STATUS_NOT_PAY]);
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
        $id = (int)  Yii::$app->request->post('id');
        if($id > 0){
            $model = Order::findOne(['id' => $id, 'user_id' => $this->userId, 'status' => Order::ORDER_STATUS_NOT_PAY]);
            if($model){
                $model->status = Order::ORDER_STATUS_HAS_CANCEL;
                $model->complete_time = time();
                $trans = Yii::$app->db->beginTransaction();
                try {
                    if($model->balance_amount){
                        $user = User::findOne($this->userId);
                        $user->balance_amount = $user->balance_amount  + $model->balance_amount;
                        $user->save();
                    }
                    $model->save();
                    $trans->commit();
                    $this->data = [
                        'code' => self::API_CODE_SUCCESS,
                        'msg' => self::API_CODE_SUCCESS_MSG
                    ];
                } catch (\Exception $e) {
                    $trans->rollBack();
                    $this->data['msg'] = '服务器异常，请联系管理员';
                }
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
        $id = (int)  Yii::$app->request->post('id');
        if($id > 0) {
            $model = Order::findOne(['id' => $id, 'user_id' => $this->userId, 'status' => Order::ORDER_STATUS_STAY_RECEIVE_GOODS]);
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
     *  查询物流
     * @return array
     * @throws \Exception
     */
    public function actionLogistics()
    {
        $id = (int)  Yii::$app->request->post('id');
        if($id > 0){
            $model = Order::findOne($id);
            if($model && $model->status > $model::ORDER_STATUS_STAY_SEND_GOODS && $model->status < $model::ORDER_STATUS_HAS_CANCEL){
                $this->data = [
                    'code' => self::API_CODE_SUCCESS,
                    'msg' => self::API_CODE_SUCCESS_MSG,
                    'data' => [
                        'orderNumber' => $model->order_number,
                        'shipperName' => $model->shipperArray[$model->shipper_code],
                        'logisticsNumber' => $model->logistics_number
                    ]
                ];
                $result = ExpressApiHelper::query($model->shipper_code, $model->logistics_number);
                $this->data['data']['detailArray'] = $result['Success'] ? $result['Traces'] : [];
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