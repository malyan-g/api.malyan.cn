<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cgt_user".
 *
 * @property integer $id
 * @property string $realname
 * @property integer $mobile
 * @property string $idcard
 * @property string $openid
 * @property integer $is_member
 * @property integer $member_id
 * @property integer $member_time
 * @property integer $created_at
 */
class User extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cgt_user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mobile','is_member', 'member_id', 'member_time', 'created_at'], 'integer'],
            [['realname', 'idcard'], 'string', 'max' => 20],
            [['openid'], 'string', 'max' => 30],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'realname' => '真实姓名',
            'mobile' => '手机号',
            'idcard' => '身份证',
            'openid' => '微信ID',
            'is_member' => '会员',
            'member_id' => '会员等级',
            'member_time' => '会员开通时间',
            'created_at' => '创建时间',
        ];
    }

    /**
     * 获取用户
     * @param $openid
     * @return array|bool
     */
    public static function getUserInfo($openid)
    {
        $user = self::findOne(['openid' => $openid]);
        if(!$user){
            $user = new self();
            $user->openid = $openid;
            $user->created_at = time();
            if(!$user->save()){
                self::createCertificate($user->id);
                return false;
            }
        }
        $userInfo = [
            'id' =>   $user->id,
            'is_member' => $user->is_member,
            'member_id' => $user->member_id,
            'openid' => $openid
        ];

        return $userInfo;
    }
}
