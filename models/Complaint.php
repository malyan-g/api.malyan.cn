<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%complaint}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $describe
 * @property integer $created_at
 */
class Complaint extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%complaint}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'created_at'], 'integer'],
            [['describe'], 'string', 'max' => 500],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '用户ID',
            'describe' => '描述',
            'created_at' => '创建时间',
        ];
    }
}
