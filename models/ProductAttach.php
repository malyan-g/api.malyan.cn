<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cgt_product_attach".
 *
 * @property integer $product_id
 * @property string $banner
 * @property string $describe
 */
class ProductAttach extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cgt_product_attach';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['banner', 'describe'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'product_id' => '产品ID',
            'banner' => '轮播',
            'describe' => '描述',
        ];
    }
}
