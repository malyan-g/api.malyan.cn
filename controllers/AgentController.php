<?php
/**
 * Created by PhpStorm.
 * User: M
 * Date: 17/7/5
 * Time: 下午5:36
 */

namespace app\controllers;

use app\models\BaseConfig;
use app\models\User;

/**
 * 代理商接口
 * Class AgentController
 * @package app\controllers
 */
class AgentController extends Controller
{
    /**
     * 邀请接口
     * @return array
     */
    public function actionInvite()
    {
        $this->data['isInvite'] = false;
        $userModel = User::findOne($this->userId);
        if($userModel){
            $this->data['code'] = self::API_CODE_SUCCESS;
            if($userModel->is_member){
                $this->data['isInvite'] = true;
                $model = BaseConfig::findOne(1);
                $this->data['data'] =  [
                    'backgroundUrl' => $model->agent_image
                ];
            }
        }
        return $this->data;
    }
}
