<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cgt_order".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $order_number
 * @property string $total_amount
 * @property string $cash_amount
 * @property string $balance_amount
 * @property integer $total_number
 * @property integer $status
 * @property string $serial_number
 * @property string $shipper_code
 * @property string $logistics_number
 * @property integer $payment_time
 * @property integer $delivery_time
 * @property integer $complete_time
 * @property integer $created_at
 */
class Order extends \yii\db\ActiveRecord
{
    const ORDER_RING_QUEUE_KEY = 'order.ring.queue'; // 环信队列key

    const ORDER_STATUS_NOT_PAY = 1; // 未支付
    const ORDER_STATUS_STAY_SEND_GOODS = 2; // 待发货
    const ORDER_STATUS_STAY_RECEIVE_GOODS = 3; // 待收货
    const ORDER_STATUS_HAS_COMPLETE = 4; // 已完成
    const ORDER_STATUS_HAS_CANCEL = 5; // 已取消
    const ORDER_STATUS_HAS_REFUND = 6; // 已退款
    const ORDER_STATUS_STAY_REFUND = 7; // 待退款

    public static $statusArray = [
        self::ORDER_STATUS_NOT_PAY => '未支付',
        self::ORDER_STATUS_STAY_SEND_GOODS => '待发货',
        self::ORDER_STATUS_STAY_RECEIVE_GOODS => '待收货',
        self::ORDER_STATUS_HAS_COMPLETE => '已完成',
        self::ORDER_STATUS_HAS_CANCEL => '已取消'
    ];

    public static $statusImageArray = [
        self::ORDER_STATUS_NOT_PAY => 'not-pay',
        self::ORDER_STATUS_STAY_SEND_GOODS => 'send-goods',
        self::ORDER_STATUS_STAY_RECEIVE_GOODS => 'receive-goods',
        self::ORDER_STATUS_HAS_COMPLETE => 'complete',
        self::ORDER_STATUS_HAS_CANCEL => 'cancel'
    ];

    /**
     * 快递公司编码
     * @var array
     */
    public $shipperArray = [
        //'SF' => '顺丰速运',
        'ZTO' => '中通快递',
        'YTO' => '圆通速递',
        'YD' => '韵达速递',
        'YZPY' => '邮政快递包裹',
        'EMS' => 'EMS',
        //'STO' => '申通快递',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%order}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['total_amount', 'cash_amount', 'balance_amount'], 'number'],
            [['user_id', 'total_number', 'status', 'payment_time', 'delivery_time', 'complete_time', 'created_at'], 'integer'],
            [['order_number', 'serial_number', 'shipper_code', 'logistics_number'], 'string', 'max' => 30],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '订单ID',
            'user_id' => '用户ID',
            'order_number' => '订单号',
            'total_amount' => '总金额',
            'cash_amount' => '现金金额',
            'balance_amount' => '余额金额',
            'total_number' => '总数量',
            'status' => '订单状态（1-未支付 2-待发货 3-待收货 4-已完成 5-已取消）',
            'serial_number' => '支付流水号',
            'shipper_code' => '快递公司',
            'logistics_number' => '物流号',
            'payment_time' => '支付时间',
            'delivery_time' => '发货时间',
            'complete_time' => '完成时间',
            'created_at' => '创建时间',
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        if($this->isNewRecord){
            $this->order_number = $this->getOrderNumber();
            $this->status = self::ORDER_STATUS_NOT_PAY;
            $this->created_at = time();
        }
        return parent::behaviors();
    }

    /**
     * 订单和产品的关联
     * @return \yii\db\ActiveQuery
     */
    public function getAttach()
    {
        return $this->hasMany(OrderAttach::className(), ['order_id' => 'id'])->select(['order_id', 'product_id']);
    }

    /**
     * 订单和收货地址的关联
     * @return \yii\db\ActiveQuery
     */
    public function getAddress()
    {
        return $this->hasOne(OrderAddress::className(), ['order_id' => 'id'])->select(['order_id', 'userName', 'telNumber', 'provinceName', 'cityName', 'countyName', 'detailInfo', 'postalCode']);
    }

    /**
     * 生成订单号
     * @return string
     */
    public function getOrderNumber()
    {
        $orderNumber = 'DDH' . time() . rand(10000,99999);
        $result = self::findOne(['order_number' =>$orderNumber]);
        if($result){
            return $this->getOrderNumber();
        }
        return $orderNumber;
    }

    /**
     * 订单存入到环形队列
     */
    public function addRingQueue()
    {
        $d = date('d', $this->created_at + 3600 * 24);
        $h = date('H', $this->created_at);
        $i = date('i', $this->created_at);
        $s = date('s', $this->created_at);
        $key = self::ORDER_RING_QUEUE_KEY . '.' . $d . $h;
        $cache = Yii::$app->cache;
        $data = $cache->get($key);
        if(!$data){
            $data = [];
        }

        $data[$i][$s][$this->id] = 0;
        $cache->set($key, $data);
    }

    /**
     * 删除到环形队列
     */
    public function deleteRingQueue()
    {
        $d = date('d', $this->created_at + 3600 * 24);
        $h = date('H', $this->created_at);
        $i = date('i', $this->created_at);
        $s = date('s', $this->created_at);
        $key = self::ORDER_RING_QUEUE_KEY . '.' . $d . $h;
        $cache = Yii::$app->cache;
        $data = $cache->get($key);
        if($data){
            unset($data[$i][$s][$this->id]);
            $cache->set($key, $data);
        }
    }
}
