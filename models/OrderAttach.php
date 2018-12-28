<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cgt_order_attach".
 *
 * @property integer $id
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
        return '{{%order_attach}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'product_id', 'product_id', 'buy_price'], 'required'],
            [['order_id', 'product_id', 'buy_number'], 'integer'],
            [['buy_number'], 'compare', 'compareValue' => 1, 'operator' => '>='],
            [['buy_number'], 'compare', 'compareValue' => 999, 'operator' => '<='],
            [['buy_price'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => '订单ID',
            'product_id' => '产品ID',
            'buy_number' => '购买数量',
            'buy_price' => '价格',
        ];
    }

    /**
     * 订单产品和产品的关联
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['id' => 'product_id'])->select(['id', 'name', 'image']);
    }

}
