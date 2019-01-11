<?php
/**
 * Created by PhpStorm.
 * User: M
 * Date: 17/6/28
 * Time: 下午5:26
 */

namespace app\components\helpers;


class MatchHelper
{
    /**
     * 手机号验证
     * @var string
     */
    public static $mobile =  '/^((13\d)|(14[5,7])|(15[0-3,5-9])|(17[0,3,5-8])|(18\d)|166|198|199|(147))\d{8}$/';

    /**
     * 中文验证
     * @var string
     */
    public static $chinese =  '/^[\x{4e00}-\x{9fa5}]+$/u';
}