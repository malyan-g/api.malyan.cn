<?php
/**
 * Created by PhpStorm.
 * User: M
 * Date: 2019/1/30
 * Time: 9:05 AM
 */

namespace app\controllers;

use Yii;
use yii\web\Response;
use app\models\Order;
use yii\helpers\ArrayHelper;

/**
 * 支付
 * Class PaymentController
 * @package app\controllers
 */
class PaymentController extends \yii\web\Controller
{
    /**
     * 异步回调
     * @return array
     */
    public function actionCallback()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $getData = Yii::$app->request->get();
        $id = (int) ArrayHelper::getValue($getData, 'id');
        if($id > 0){
            $model = Order::findOne(['id' => $id, 'status' => Order::ORDER_STATUS_NOT_PAY]);
            $model->serial_number = rand(10000000,99999999);
            $model->status = Order::ORDER_STATUS_STAY_SEND_GOODS;
            $model->payment_time = time();
            if($model->save(false)){
                $model->deleteRingQueue();
            }
        }

        return [
            'code' => 'error',
            'msg' => '非法请求'
        ];
    }
}
