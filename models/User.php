<?php

namespace app\models;

use Yii;
use app\components\helpers\MatchHelper;
use app\components\helpers\SendSmsHelper;
use app\components\helpers\IdentityCardHelper;

/**
 * This is the model class for table "ml_wx_user".
 *
 * @property integer $id
 * @property string $realname
 * @property integer $mobile
 * @property string $openid
 * @property integer $status
 * @property integer $balance_expire_time
 * @property integer $created_at
 * @property integer $verifyCode
 */
class User extends \yii\db\ActiveRecord
{
    const VERIFY_CODE_KEY = 'phone.verify.code.';
    /**
     * 验证码
     * @var
     */
    public $verifyCode;

    public $oldMobile;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%wx_user}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['openid'], 'required', 'on' => 'create'],
            [['realname'], 'string', 'max' => 20],
            [['mobile', 'verifyCode'], 'required', 'on' => ['sendMsm', 'mobile']],
            [['status', 'created_at'], 'integer'],
            [['mobile'], 'integer', 'on' => ['sendMsm', 'mobile']],
            [['mobile'], 'match', 'pattern' => MatchHelper::$mobile, 'on' => ['sendMsm', 'mobile'], 'message' => '手机号格式不正确的'],
            [['mobile'], 'compare', 'operator'=>'!==', 'compareAttribute'=>'oldMobile', 'on' => ['sendMsm', 'mobile'], 'message' => '该手机号和原号码一致'],
            [['mobile'], 'unique', 'on' => ['sendMsm', 'mobile'], 'message' => '该手机号已绑定'],
            [['openid'], 'string', 'max' => 30, 'on' => 'create'],
            [['verifyCode'], 'string', 'min' => 6, 'max'=>6, 'on' => ['mobile'], 'message' => '验证码输入错误'],
            [['verifyCode'], 'match', 'pattern' => '/^[0-9]{6}$/', 'on' => ['mobile'], 'message' => '验证码输入错误'],
            ['verifyCode', 'validateVerifyCode', 'on' => ['mobile']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            'create'=>[
                'openid', 'created_at'
            ],
            'info' => [
                'realname'
            ],
            'mobile' => [
                'mobile', 'oldMobile', 'verifyCode'
            ],
            'select' => [
                'id', 'realname', 'mobile',  'openid', 'status', 'created_at', 'verifyCode'
            ],
            'sendMsm' => [
                'mobile', 'oldMobile'
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'realname' => '昵称',
            'mobile' => '手机号',
            'openid' => '微信ID',
            'status' => '状态',
            'created_at' => '创建时间',
            'verifyCode' => '验证码'
        ];
    }

    /**
     * @inheritdoc
     */
    public function afterFind()
    {
        $this->oldMobile = $this->mobile;
        parent::afterFind(); // TODO: Change the autogenerated stub
    }

    /**
     * @inheritdoc
     */
    public function validateVerifyCode()
    {
        if (!$this->hasErrors()) {
            $data = $this->checkVerifyCode();
            if ($data['code'] === false) {
                $this->addError('verifyCode', $data['msg']);
            }
        }
    }

    /**
     * 验证手机验证码
     * @return array
     */
    public function checkVerifyCode()
    {
        $data = [
            'code' => false,
            'msg' => '验证码输入错误'
        ];
        if($this->verifyCode){
            $verifyData = Yii::$app->cache->get(self::VERIFY_CODE_KEY . $this->id);
            if($this->verifyCode == $verifyData['verifyCode'] && $this->mobile == $verifyData['mobile']){
                if($verifyData['duration'] >= time()){
                    $data = [
                        'code' => true
                    ];
                }else{
                    $verifyCode['msg'] = '验证码已过期';
                }
            }
        }
        return $data;
    }

    /**
     * 发送验证码
     * @return bool
     */
    public function sendVerifyCode()
    {
        $data = [
            'mobile' => $this->mobile,
            'verifyCode' => rand(100000, 999999),
            'duration' => time() + 600
        ];
        $model = new SmsRecord();
        $model->mobile = $this->mobile;
        $model->user_id = $this->id;
        $model->content = (string) $data['verifyCode'];
        if($model->save()){
            // 发送手机验证码
            $result = SendSmsHelper::sendCode($this->mobile, $data['verifyCode']);
            if($result['Code'] === 'OK'){
                $model->send_result = 1;
                $model->save(false);
                return Yii::$app->cache->set(self::VERIFY_CODE_KEY . $this->id, $data, 3600 * 6);
            }
        }
       return false;
    }

    /**
     * 获取用户
     * @param $openid
     * @return array|bool
     */
    public static function getUserInfo($openid)
    {
        $user = self::findOne(['openid' => $openid, 'status' => 1]);
        if(!$user){
            $user = new self();
            $user->scenario = 'create';
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
