<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cgt_product_price".
 *
 * @property integer $product_id
 * @property integer $member_id
 * @property string $member_price
 */
class ProductPrice extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cgt_product_price';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_id'], 'integer'],
            [['member_price'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'product_id' => '产品ID',
            'member_id' => '会员ID',
            'member_price' => '会员价格',
        ];
    }
}
