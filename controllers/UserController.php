<?php
/**
 * Created by PhpStorm.
 * User: M
 * Date: 17/7/5
 * Time: 下午5:36
 */

namespace app\controllers;

use YII;
use app\models\User;
use app\components\helpers\ScHelper;
use app\components\helpers\WxApiHelper;

/**
 * Class UserController
 * @package app\controllers
 */
class UserController extends Controller
{

    public function actionLogin()
    {
        $code = Yii::$app->request->get('code');
        if(!is_null($code)){
            $this->data['msg'] = '服务器异常';
            // 请求微信登录获取openid
            $loginInfo = WxApiHelper::getLoginInfo($code);
            if($loginInfo){
                // 根据openid获取用户信息
                $user = User::getUserInfo($loginInfo['openid']);
                if($user){
                    $this->data['code'] = self::API_CODE_SUCCESS;
                    $this->data['msg'] = self::API_CODE_SUCCESS_MSG;
                    $this->data['sign'] =  ScHelper::encode([
                        'id' => $user->id,
                        'loginTime' => time()
                    ]);
                    Yii::$app->cache->set(self::CACHE_USER_LOGIN_KEY . $user->id, $loginInfo, 1800);
                }
            }
        }else{
            $this->data['msg'] = '非法请求';
        }
        return $this->data;
    }
}