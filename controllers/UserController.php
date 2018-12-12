<?php
/**
 * Created by PhpStorm.
 * User: M
 * Date: 17/7/5
 * Time: 下午5:36
 */

namespace app\controllers;

use app\components\methods\Sign;
use YII;
use app\components\methods\ApiRequest;

class UserController extends Controller
{
    public function actionLogin()
    {
        $code = Yii::$app->request->get('code');
        if(!is_null($code)){
            $data = ApiRequest::login($code);
            if($data){
                $this->data['msg'] = self::API_CODE_SUCCESS_MSG;
                $this->data['sign'] =  md5($data['openid'] . rand(10000,99999) . $data['session_key']);
                Yii::$app->cache->set($this->data['sign'], $data, 1800);
            }else{
                $this->data['msg'] = '服务器异常';
            }
        }else{
            $this->data['msg'] = '非法请求';
        }
        return $this->data;
    }
}