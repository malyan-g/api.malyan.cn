<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%product_balance_price}}".
 *
 * @property integer $id
 * @property integer $product_id
 * @property integer $balance_id
 * @property string $balance_price
 * @property string $describe
 */
class ProductBalancePrice extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%product_balance_price}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id', 'balance_id'], 'integer'],
            [['balance_price'], 'number'],
            [['describe'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'product_id' => '产品ID',
            'balance_id' => '会员ID',
            'balance_price' => '会员价格',
            'describe' => '描述',
        ];
    }
}
