<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cgt_product".
 *
 * @property integer $id
 * @property string $name
 * @property string $image
 * @property string $price
 * @property integer $status
 * @property integer $created_at
 */
class Product extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cgt_product';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['image'], 'required'],
            [['price'], 'number'],
            [['status', 'created_at'], 'integer'],
            [['name', 'image'], 'string', 'max' => 80],
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
            'price' => '价格',
            'status' => '状态（1:上架 2:下价）',
            'created_at' => '创建时间',
        ];
    }
}
