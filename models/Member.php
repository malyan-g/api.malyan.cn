<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cgt_member".
 *
 * @property integer $id
 * @property string $name
 * @property integer $buy_num
 */
class Member extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%member}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['buy_num'], 'integer'],
            [['name'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '会员ID',
            'name' => '会员名称',
            'buy_num' => '购买数量',
        ];
    }
}
