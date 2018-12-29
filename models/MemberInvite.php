<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%member_invite}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $invite_user_id
 * @property integer $created_at
 */
class MemberInvite extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%member_invite}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'invite_user_id', 'created_at'], 'integer'],
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
            'invite_user_id' => '邀请用户ID',
            'created_at' => '创建时间',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id'])->select([User::tableName() .'.id', 'realname', 'member_id']);
    }

    public function getUser2()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id'])->select(['u.id', 'realname', 'member_id']);
    }

    public function getInvite()
    {
        return $this->hasMany(self::className(), ['invite_user_id' => 'user_id'])->select(['user_id', 'invite_user_id']);
    }
}
