<?php
/**
 * Created by PhpStorm.
 * User: M
 * Date: 17/7/5
 * Time: 下午5:36
 */

namespace app\controllers;

use YII;

/**
 * Class TestController
 * @package app\controllers
 */
class TestController extends ApiController
{
    public $modelClass ='app\models\Book';

    /**
     * @return array
     */
    public function actionCreate()
    {
        $data = [
            'recordCount' => 20,
            'totalCount' => 20,
            'page' => 20,
            'pageSize' => 20,
            'totalPage' => 20,
        ];
    }

    /**
     * @return array
     */
    public function actionBrowse()
    {
        $data = [
            'recordCount' => 20,
            'totalCount' => 20,
            'page' => 20,
            'pageSize' => 20,
            'totalPage' => 20,
        ];
        return $this->data;
    }

    /**
     * @return array
     */
    public function actionUpdate()
    {
        $data = [
            'recordCount' => 20,
            'totalCount' => 20,
            'page' => 20,
            'pageSize' => 20,
            'totalPage' => 20,
        ];
        return $this->data;
    }

    /**
     * @return array
     */
    public function actionDelete()
    {
        $data = [
            'recordCount' => 20,
            'totalCount' => 20,
            'page' => 20,
            'pageSize' => 20,
            'totalPage' => 20,
        ];
        return $this->data;
    }
}
