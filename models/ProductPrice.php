<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cgt_product_price".
 *
 *  @property integer $id
 * @property integer $product_id
 * @property integer $member_id
 * @property string $member_price
 * @property string $describe
 */
class ProductPrice extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%product_price}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_id'], 'integer'],
            [['member_price'], 'number'],
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
            'member_id' => '会员ID',
            'member_price' => '会员价格',
            'describe' => '描述',
        ];
    }
}
