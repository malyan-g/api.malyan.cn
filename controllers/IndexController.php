<?php
/**
 * Created by PhpStorm.
 * User: M
 * Date: 17/7/5
 * Time: 下午5:36
 */

namespace app\controllers;

use app\models\BaseConfig;

/**
 * 首页接口
 * Class IndexController
 * @package app\controllers
 */
class IndexController extends Controller
{
    /**
     * 首页接口
     * @return array
     */
    public function actionIndex()
    {
        $data = [];
        $time = time();
        for($i = 0; $i < 12;$i++){
            $data[] = md5($time + $i);
        }
        return $data;
        $model = BaseConfig::findOne(1);
        $this->data = [
            'code' => self::API_CODE_SUCCESS,
            'msg' => self::API_CODE_SUCCESS_MSG,
            'data' => [
                'bannerArray' => explode(',', $model->index_banner),
                'productArray' => explode(',', $model->index_content),
                'recruitArray' => explode(',', $model->index_footer),
            ]
        ];
        return $this->data;
    }
}
