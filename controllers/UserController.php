<?php
/**
 * Created by PhpStorm.
 * User: M
 * Date: 17/7/5
 * Time: 下午5:36
 */

namespace app\controllers;

use app\components\helpers\ImageHelper;
use app\models\Complaint;
use app\models\Member;
use app\models\MemberInvite;
use app\models\Order;
use app\models\OrderAttach;
use app\models\Product;
use app\models\UserImage;
use YII;
use app\models\User;
use app\components\helpers\ScHelper;
use app\components\helpers\WxApiHelper;
use PhpOffice\PhpWord\TemplateProcessor;
use app\components\helpers\QiniuApiHelper;
use yii\db\ActiveQuery;

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
                        'sign' => ScHelper::encode([
                            'id' => $user['id'],
                            'loginTime' => time()
                        ])
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
            $memberModel = Member::findOne($user->member_id);
            $data = [
              'userId' => $this->userId,
              'memberId' => $user->member_id,
              'memberName' => $memberModel ? $memberModel->name : '普通会员',
              'percent' => 38,
              'certificateUrl' => 'http://img.malyan.cn/WechatIMG34.png',
              'contractUrl' => 'http://img.malyan.cn/WechatIMG34.png'
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
            ->indexBy('member_id')
            ->asArray()
            ->all();

        $memberData = Member::find()
            ->select(['id', 'name'])
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
                $tmpName = $path. 'tmp-certificate';
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
                                QiniuApiHelper::delete($model->certificate_url);
                            }else{
                                $model = new UserImage();
                                $model->user_id = $this->userId;
                            }
                            $model->certificate_url = $result['key'];
                            if($model->save()){
                                $this->data = [
                                    'code' => self::API_CODE_SUCCESS,
                                    'msg' => self::API_CODE_SUCCESS_MSG
                                ];
                            }else{
                                QiniuApiHelper::delete($result['key']);
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
