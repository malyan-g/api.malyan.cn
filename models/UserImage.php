<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%user_image}}".
 *
 * @property integer $user_id
 * @property string $certificate_url
 * @property string $contract_url
 */
class UserImage extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_image}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'integer'],
            [['certificate_url', 'contract_url'], 'string', 'max' => 80],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => '用户ID',
            'certificate_url' => '证书地址',
            'contract_url' => '合同地址',
        ];
    }
}
