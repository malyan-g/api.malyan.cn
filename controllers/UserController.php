<?php
/**
 * Created by PhpStorm.
 * User: M
 * Date: 17/7/5
 * Time: 下午5:36
 */

namespace app\controllers;

use app\models\SmsRecord;
use YII;
use app\models\User;
use yii\db\ActiveQuery;
use app\models\Order;
use app\models\Product;
use app\models\Member;
use app\models\Complaint;
use app\models\UserImage;
use app\models\OrderAttach;
use app\models\MemberInvite;
use app\components\helpers\ScHelper;
use app\components\helpers\WxApiHelper;
use PhpOffice\PhpWord\TemplateProcessor;
use app\components\helpers\ImageHelper;
use app\components\helpers\QiniuApiHelper;

/**
 * 用户接口
 * Class UserController
 * @package app\controllers
 */
class UserController extends Controller
{

    /**
     * 登录接口
     * @return mixed
     */
    public function actionLogin()
    {
        $code = Yii::$app->request->post('code');
        return ['code' => Yii::$app->request->post()];
        if(!is_null($code)){
            $this->data['msg'] = '服务器异常';
            // 请求微信登录获取openid
            $loginInfo = WxApiHelper::getLoginInfo($code);

            if($loginInfo){
                // 根据openid获取用户信息
                $user = User::getUserInfo($loginInfo['openid']);
                if($user){
                    $this->data = [
                      'code' => self::API_CODE_SUCCESS,
                      'msg' => self::API_CODE_SUCCESS_MSG,
                      'data' => [
                        'sid' => ScHelper::encode([
                          'id' => $user['id'],
                          'loginExpire' => time() + LOGIN_EXPIRE
                        ])
                      ]
                    ];
                    $user['session_key'] = $loginInfo['session_key'];
                    // 记录用户登录状态
                    Yii::$app->cache->set(self::CACHE_USER_LOGIN_KEY . $user['id'], $user);
                    //Yii::$app->cache->set(self::CACHE_USER_LOGIN_KEY . $user['id'], $user, 1800);
                }
            }
        }else{
            $this->data['msg'] = '非法请求';
        }
        return $this->data;
    }

    /**
     * 首页
     * @return array
     */
    public function actionIndex()
    {
        $user = User::findOne($this->userId);
        if($user){
            $userImage = UserImage::findOne(['user_id' => $user->id]);
            $member = Member::findOne($user->member_id);
            $data = [
              'userId' => $this->userId,
              'memberId' => $user->member_id,
              'memberName' => $member ? $member->name : '普通会员',
              'realname' => $user->realname,
              'percent' => 82,
              'certificateUrl' => $userImage && $userImage->certificate_url ? self::IMAGE_DOMAIN . $userImage->certificate_url : '',
              'contractUrl' =>$userImage && $userImage->contract_url ? self::IMAGE_DOMAIN . $userImage->contract_url : ''
            ];
            $this->data = [
                'code' => self::API_CODE_SUCCESS,
                'msg' => self::API_CODE_SUCCESS_MSG,
                'data' => $data
            ];
        }
        return $this->data;
    }

    /**
     * 我的团队
     * @return array
     */
    public function actionGroup()
    {
         $inviteData =   MemberInvite::find()
             ->select([MemberInvite::tableName() . '.user_id'])
             ->where([MemberInvite::tableName() . '.invite_user_id' => $this->userId])
             ->joinWith(['user' => function(ActiveQuery $query) {
                 $query->joinWith(['member AS m']);
             }])
             ->joinWith(['invite AS t' => function(ActiveQuery $query) {
                 $query->joinWith(['user2 AS u' => function(ActiveQuery $query){
                     $query->joinWith(['member2 AS mb']);
                 }]);
             }])
             ->orderBy([MemberInvite::tableName() . '.created_at' => SORT_ASC])
             ->asArray()
             ->all();

        $data = [];
         if($inviteData){
             foreach ($inviteData as $key => $val){
                 $data[$key] = [
                     'realname' => $val['user']['realname'],
                     'memberId' => $val['user']['member_id'],
                     'memberName' => $val['user']['member']['name'],
                 ];

                 if($val['invite']){
                     $data[$key]['isHidden'] = true;
                     foreach ($val['invite'] as $v){
                         $data[$key]['item'][] = [
                             'realname' => $v['user2']['realname'],
                             'memberId' => $v['user2']['member_id'],
                             'memberName' => $v['user2']['member2']['name'],
                         ];
                     }
                 }
             }
         }
        $this->data = [
            'code' => self::API_CODE_SUCCESS,
            'msg' => self::API_CODE_SUCCESS_MSG,
            'data' => $data
        ];
        return $this->data;
    }

    /**
     * 我的业绩
     * @return array
     */
    public function actionAchievement()
    {
        // 邀请代理商量
        $inviteData = MemberInvite::find()
            ->select(['member_id',' count(member_id) member_number'])
            ->innerJoin(User::tableName(), User::tableName() . '.id=user_id')
            ->where(['invite_user_id' => $this->userId])
            ->groupBy('member_id')
            ->limit(4)
            ->indexBy('member_id')
            ->asArray()
            ->all();

        $memberData = Member::find()
            ->select(['id', 'name'])
            ->where(['type' => 0])
            ->limit(4)
            ->asArray()
            ->all();

        foreach ($memberData as $key => $val) {
            $memberData[$key]['memberNumber'] = isset($inviteData[$val['id']]) ? $inviteData[$val['id']]['member_number'] : 0;
        }

        // 商品销售量
        $productData = OrderAttach::find()
            ->select([Product::tableName() . '.id', 'sum(buy_number) as buyNumber', 'name', 'image'])
            ->innerJoin(Order::tableName(),Order::tableName() .'.id=order_id and ' . Order::tableName() . '.status=' . Order::ORDER_STATUS_HAS_COMPLETE)
            ->innerJoin(Product::tableName(), Product::tableName() . '.id=product_id and ' . Product::tableName()  . '.is_balance=0')
            ->where(['user_id' => $this->userId])
            ->groupBy(Product::tableName() . '.id')
            ->orderBy(['buyNumber' => SORT_DESC, 'price' => SORT_DESC])
            ->asArray()
            ->all();

        $this->data = [
            'code' => self::API_CODE_SUCCESS,
            'msg' => self::API_CODE_SUCCESS_MSG,
            'memberData' => $memberData ? $memberData : [],
            'productData' => $productData ? $productData : []
        ];
        return $this->data;
    }

    /**
     * 投诉与建议
     * @return array
     */
    public function actionComplaint()
    {
        $todayTime = strtotime(date("Y-m-d"), time());
        $count = Complaint::find()
            ->where(['user_id' => $this->userId])
            ->andWhere(['between', 'created_at', $todayTime, $todayTime + 24*3600])
            ->count();
        if($count < 5){
            $describe = Yii::$app->request->post('describe');
            $model = new Complaint();
            $data = [
                'user_id' => $this->userId,
                'describe' => $describe
            ];
            if($model->load(['data' => $data], 'data') && $model->validate()){
                if($model->save()){
                    $this->data = [
                        'code' => self::API_CODE_SUCCESS,
                        'msg' => self::API_CODE_SUCCESS_MSG
                    ];
                }
            }
        }else{
            $this->data['msg'] = '每日最多可提交5条投诉或者建议';
        }

        return $this->data;
    }

    /**
     * 获取用户信息
     * @return array
     */
    public function actionGetInfo()
    {
        $model = User::findOne($this->userId);
        if($model){
            $this->data = [
                'code' => self::API_CODE_SUCCESS,
                'msg' => self::API_CODE_SUCCESS_MSG,
                'data' => [
                    'realname' => $model->realname,
                    'idcard' => $model->idcard
                ]
            ];
        }
        return $this->data;
    }

    /**
     * 修改基本信息
     * @return array
     */
    public function actionInfo()
    {
        $model = User::findOne($this->userId);
        if($model){
            $model->setScenario('info');
            $data = json_decode(Yii::$app->request->post('form'), true);
            if($model->load(['data' => $data], 'data') && $model->validate()){
                if($model->save(false)){
                    $this->data = [
                        'code' => self::API_CODE_SUCCESS,
                        'msg' => self::API_CODE_SUCCESS_MSG
                    ];
                }
            }else{
                $this->data['msg'] = current($model->firstErrors);
            }
        }
        return $this->data;
    }

    /**
     * 获取绑定手机号
     * @return array
     */
    public function actionGetMobile()
    {
        $model = User::findOne($this->userId);
        if($model){
            $this->data = [
                'code' => self::API_CODE_SUCCESS,
                'msg' => self::API_CODE_SUCCESS_MSG,
                'mobile' => $model->mobile
            ];
        }
        return $this->data;
    }

    /**
     * 修改绑定手机
     * @return array
     */
    public function actionMobile()
    {
        $model = User::findOne($this->userId);
        if($model){
            $model->setScenario('mobile');
            $data = json_decode(Yii::$app->request->post('form'), true);
            if($model->load(['data' => $data], 'data') && $model->validate()){
                Yii::$app->cache->delete($model::VERIFY_CODE_KEY . $model->id);
                if($model->save(false)){
                    $this->data = [
                        'code' => self::API_CODE_SUCCESS,
                        'msg' => self::API_CODE_SUCCESS_MSG
                    ];
                }
            }else{
                $this->data['msg'] = current($model->firstErrors);
            }
        }
        return $this->data;
    }

    /**
     * 发送验证码
     * @return array
     */
    public function actionSendCode()
    {
        $model = User::findOne($this->userId);
        if($model){
            $model->setScenario('sendMsm');
            $mobile = Yii::$app->request->post('mobile');
            if($model->load(['data' => ['mobile' => $mobile]], 'data') && $model->validate()){
                if(SmsRecord::checkSend($model->id)){
                    if($model->sendVerifyCode()){
                        $this->data = [
                            'code' => self::API_CODE_SUCCESS,
                            'msg' => self::API_CODE_SUCCESS_MSG
                        ];
                    }else{
                        $this->data['msg'] = '发送失败';
                    }
                }else{
                    $this->data['msg'] = '每天最多获取10次验证码';
                }
            }else{
                $this->data['msg'] = current($model->firstErrors);
            }
        }
        return $this->data;
    }

    /**
     * 生成证书
     * @param $id
     * @throws \PhpOffice\PhpWord\Exception\CopyFileException
     * @throws \PhpOffice\PhpWord\Exception\CreateTemporaryFileException
     */
    public function actionCertificate()
    {
        try{
            $memberData = User::find()
                ->select(['realname', 'idcard', 'member_time', 'name'])
                ->innerJoin(Member::tableName(), Member::tableName() . '.id=member_id')
                ->where([User::tableName() . '.id' => $this->userId])
                ->asArray()
                ->one();
            if($memberData){
                $issueDate =  date('Ymd', $memberData['member_time']);
                // word路径
                $path = Yii::getAlias('@webroot') . '/files/';
                $tmpName = $path. md5(rand(10000,99999));
                // 替换模板中的变量并保存
                $templateProcessor = new TemplateProcessor($path . 'certificate.docx');
                $templateProcessor->setValue('certificate_number', date('Ymd' . rand(1000,9999)));
                $templateProcessor->setValue('name', $memberData['realname']);
                $templateProcessor->setValue('member_name', $memberData['name']);
                $templateProcessor->setValue('id_card', $memberData['idcard']);
                $templateProcessor->setValue('issue_date', $issueDate);
                $templateProcessor->setValue('valid_date', date('Ymd', strtotime("+1 year", strtotime($issueDate))));
                $templateProcessor->saveAs($tmpName . '.docx');
                // word转为pdf
                $resultPdf = ImageHelper::word2pdf($tmpName . '.docx', $tmpName . '.pdf', $path);
                // 删除本地文件
                unlink($tmpName . '.docx'); // 删除本地文件
                if($resultPdf){
                    // pdf转为图片
                    $resultPng = ImageHelper::pdf2png($tmpName . '.pdf', $tmpName . '.png');
                    unlink($tmpName . '.pdf');
                    if($resultPng){
                        // 上传七牛
                        $pngName = md5(time() . $this->userId) . '.png';
                        $result = QiniuApiHelper::upload($tmpName . '.png', $pngName);
                        unlink($tmpName . '.png');
                        if(isset($result['key'])){
                            $model = UserImage::findOne(['user_id' => $this->userId]);
                            if($model){
                                if($model->certificate_url != ''){
                                    QiniuApiHelper::delete($model->certificate_url);
                                }
                            }else{
                                $model = new UserImage();
                                $model->user_id = $this->userId;
                            }
                            $model->certificate_url = $pngName;
                            if($model->save()){
                                $this->data = [
                                    'code' => self::API_CODE_SUCCESS,
                                    'msg' => self::API_CODE_SUCCESS_MSG,
                                    'url' => self::IMAGE_DOMAIN . $pngName
                                ];
                            }
                        }
                    }
                }
            }
        }catch (\Exception $e){
        }
        return $this->data;
    }

    /**
     * 生成证书
     * @param $id
     * @throws \PhpOffice\PhpWord\Exception\CopyFileException
     * @throws \PhpOffice\PhpWord\Exception\CreateTemporaryFileException
     */
    public function actionContract()
    {
        try{
            $memberData = User::find()
                ->select(['realname', 'idcard', 'member_time', 'name'])
                ->innerJoin(Member::tableName(), Member::tableName() . '.id=member_id')
                ->where([User::tableName() . '.id' => $this->userId])
                ->asArray()
                ->one();
            if($memberData){
                $issueDate =  date('Ymd', $memberData['member_time']);
                // word路径
                $path = Yii::getAlias('@webroot') . '/files/';
                $tmpName = $path. md5(rand(10000,99999));
                // 替换模板中的变量并保存
                $templateProcessor = new TemplateProcessor($path . 'certificate.docx');
                $templateProcessor->setValue('certificate_number', date('Ymd' . rand(1000,9999)));
                $templateProcessor->setValue('name', $memberData['realname']);
                $templateProcessor->setValue('member_name', $memberData['name']);
                $templateProcessor->setValue('id_card', $memberData['idcard']);
                $templateProcessor->setValue('issue_date', $issueDate);
                $templateProcessor->setValue('valid_date', date('Ymd', strtotime("+1 year", strtotime($issueDate))));
                $templateProcessor->saveAs($tmpName . '.docx');
                // word转为pdf
                $resultPdf = ImageHelper::word2pdf($tmpName . '.docx', $tmpName . '.pdf', $path);
                // 删除本地文件
                unlink($tmpName . '.docx'); // 删除本地文件
                if($resultPdf){
                    // pdf转为图片
                    $resultPng = ImageHelper::pdf2png($tmpName . '.pdf', $tmpName . '.png');
                    unlink($tmpName . '.pdf');
                    if($resultPng){
                        // 上传七牛
                        $pngName = md5(time() . $this->userId) . '.png';
                        $result = QiniuApiHelper::upload($tmpName . '.png', $pngName);
                        unlink($tmpName . '.png');
                        if(isset($result['key'])){
                            $model = UserImage::findOne(['user_id' => $this->userId]);
                            if($model){
                                if($model->contract_url != ''){
                                    QiniuApiHelper::delete($model->contract_url);
                                }
                            }else{
                                $model = new UserImage();
                                $model->user_id = $this->userId;
                            }
                            $model->contract_url = $pngName;
                            if($model->save()){
                                $this->data = [
                                    'code' => self::API_CODE_SUCCESS,
                                    'msg' => self::API_CODE_SUCCESS_MSG,
                                    'url'  => self::IMAGE_DOMAIN . $pngName
                                ];
                            }
                        }
                    }
                }
            }
         }catch (\Exception $e){
         }
        return $this->data;
    }
}
