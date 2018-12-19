<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cgt_order".
 *
 * @property integer $id
 * @property string $order_number
 * @property string $total_amount
 * @property integer $total_number
 * @property integer $status
 * @property string $serial_number
 * @property integer $payment_time
 * @property integer $delivery_time
 * @property integer $complete_time
 * @property integer $created_at
 */
class Order extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cgt_order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['total_amount'], 'number'],
            [['total_number', 'status', 'payment_time', 'delivery_time', 'complete_time', 'created_at'], 'integer'],
            [['order_number', 'serial_number'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '订单ID',
            'order_number' => '订单号',
            'total_amount' => '总金额',
            'total_number' => '总数量',
            'status' => '订单状态（1-未支付 2-待发货 3-待收货 4-已完成 5-已取消）',
            'serial_number' => '支付流水号',
            'payment_time' => '支付时间',
            'delivery_time' => '发货时间',
            'complete_time' => '完成时间',
            'created_at' => '创建时间',
        ];
    }
}
