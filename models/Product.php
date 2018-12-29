<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cgt_product".
 *
 * @property integer $id
 * @property string $name
 * @property string $image
 * @property string $label
 * @property string $price
 * @property integer $status
 * @property integer $balance_deduct
 * @property integer $sort
 * @property integer $created_at
 */
class Product extends \yii\db\ActiveRecord
{
    /**
     * 上架状态
     */
    const NORMAL_STATUS = 1;
    /**
     * 下架状态
     */
    const BAN_STATUS = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%product}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['price'], 'number'],
            [['status', 'balance_deduct', 'sort', 'created_at'], 'integer'],
            [['name', 'label'], 'string', 'max' => 80],
            [['image'], 'string', 'max' => 120],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '产品ID',
            'name' => '名称',
            'image' => '图片',
            'label' => '标签',
            'price' => '价格',
            'status' => '状态（1:上架 2:下价）',
            'balance_deduct' => '支持余额',
            'sort' => '排序',
            'created_at' => '创建时间',
        ];
    }
}
