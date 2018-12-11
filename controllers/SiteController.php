<?php
/**
 * Created by PhpStorm.
 * User: M
 * Date: 17/7/5
 * Time: 下午5:36
 */

namespace app\controllers;

class SiteController extends Controller
{
    public function actionError()
    {
        $this->data['msg'] = '请求错误';
        return $this->data;
    }
}