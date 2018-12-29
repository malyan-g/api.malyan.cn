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
 * @property integer $is_balance
 * @property integer $balance_id
 * @property integer $balance_amount
 * @property integer $balance_time
 * @property integer $balance_expire_time
 * @property integer $created_at
 */
class User extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mobile', 'is_member', 'member_id', 'member_time', 'is_balance', 'balance_id', 'balance_time', 'balance_expire_time', 'created_at'], 'integer'],
            [['balance_amount'], 'number'],
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
            'is_member' => '会员（0-否 1-是）',
            'member_id' => '会员等级',
            'member_time' => '会员开通时间',
            'is_balance' => '余额会员（0-否 1-是）',
            'balance_id' => '余额会员等级',
            'balance_amount' => '余额金额',
            'balance_time' => '余额会员开通时间',
            'balance_expire_time' => '余额会员到期时间',
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
                return false;
            }
        }
        $userInfo = [
            'id' =>   $user->id,
            'openid' => $openid
        ];
        return $userInfo;
    }
}
