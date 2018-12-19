<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cgt_order_attach".
 *
 * @property integer $order_id
 * @property integer $product_id
 * @property integer $buy_number
 * @property string $buy_price
 */
class OrderAttach extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cgt_order_attach';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id', 'buy_number'], 'integer'],
            [['buy_price'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'order_id' => '订单ID',
            'product_id' => '产品ID',
            'buy_number' => '购买数量',
            'buy_price' => '价格',
        ];
    }
}
