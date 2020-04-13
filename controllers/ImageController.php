<?php
/**
 * Created by PhpStorm.
 * User: M
 * Date: 17/7/5
 * Time: 下午5:36
 */

namespace app\controllers;

use YII;
use yii\helpers\ArrayHelper;

/**
 * 图片接口
 * Class ImageController
 * @package app\controllers
 */
class ImageController extends Controller
{
    /**
     * 图片接口
     * @return array
     */
    public function actionIndex()
    {
        $requestData = Yii::$app->request->post();
        $id = (int) ArrayHelper::getValue($requestData, 'id', 0);

        $data = [
            [
                'id' => 1,
                'title' => '云南大理游',
                'url' => 'http://img.malyan.cn/a2c7d9d5b897b43ba08bb71f.jpg',
                'count' => 123
            ],
            [
                'id' => 2,
                'title' => '云南丽江游',
                'url' => 'http://img.malyan.cn/693470e935aa1ecfa2f90b80810042e5.jpeg',
                'count' => 4
            ],
            [
                'id' => 3,
                'title' => '云南天水游',
                'url' => 'http://img.malyan.cn/30850e61a1256de58cb10d19.jpg',
                'count' => 542
            ],[
                'id' => 4,
                'title' => '云南天水游',
                'url' => 'http://img.malyan.cn/a2c7d9d5b897b43ba08bb71f.jpg',
                'count' => 109
            ],
            [
                'id' => 5,
                'title' => '云南天水游',
                'url' => 'http://img.malyan.cn/7acb0a46f21fbe09d6bb8c6065600c338644add9.jpg',
                'count' => 2334
            ],
            [
                'id' => 6,
                'title' => '云南天水游',
                'url' => 'http://img.malyan.cn/71b77de83901213f0ec739d95ae736d12e2e956c.jpg',
                'count' => 24
            ],
            [
                'id' => 7,
                'title' => '云南天水游',
                'url' => 'http://img.malyan.cn/timg.jpeg',
                'count' => 33
            ]
        ];

        if($id > 0){
            for($i = 0; $i < 2; $i++){
                foreach($data as $key => $val) {
                    $data[] = $val;
                }
            }
        }

        $this->data = [
            'code' => self::API_CODE_SUCCESS,
            'msg' => self::API_CODE_SUCCESS_MSG,
            'allPages' => 3,
            'data' => $data
        ];
        return $this->data;
    }
}
