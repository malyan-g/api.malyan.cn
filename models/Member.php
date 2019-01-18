<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%member}}".
 *
 * @property integer $id
 * @property string $name
 * @property integer $type
 * @property string $start_amount
 * @property string $end_amount
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
            [['type'], 'integer'],
            [['start_amount', 'end_amount'], 'number', 'min' => 0],
            [['name'], 'string', 'max' => 20],
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
            'type' => '会员类型',
            'start_amount' => '起始金额',
            'end_amount' => '终止金额',
        ];
    }
}
