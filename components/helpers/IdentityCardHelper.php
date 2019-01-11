<?php
/**
 * Created by PhpStorm.
 * User: M
 * Date: 17/6/28
 * Time: 下午5:26
 */

namespace app\components\helpers;

use Yii;
use yii\validators\Validator;

/**
 * 身份证验证
 * Class IdentityCardHelper
 * @package app\components\helpers
 */
class IdentityCardHelper extends Validator
{
    public  $pattern = '/^\d{15}$|^\d{17}[\dxX]$/';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if ($this->message === null) {
            $this->message = Yii::t('yii', '{attribute}输入不正确');
        }
    }
    /**
     * @inheritdoc
     */
    public function validateAttribute($model, $attribute)
    {
        $value = $model->$attribute;
        if (is_array($value)) {
            $this->addError($model, $attribute, $this->message);
            return;
        }
        $str_len = strlen($value);
        if($str_len!=15 && $str_len!=18){
            $this->addError($model, $attribute, $this->message);
            return ;
        }

        if (!preg_match($this->pattern, "$value")) {
            $this->addError($model, $attribute, $this->message);
            return ;
        }

        if($str_len == 15 && !$this->isValidityBrithBy15IdCard($value)){
            $this->addError($model, $attribute, $this->message);
            return ;
        }
        if($str_len == 18){
            if(!$this->isValidityBrithBy18IdCard($value) || !$this->isTrueValidateCodeBy18IdCard($value)){
                $this->addError($model, $attribute, $this->message);
                return;
            }
        }
    }

    /**
     * 验证15位数身份证号码中的生日是否是有效生日
     * @param $value
     * @return bool
     */
    private function isValidityBrithBy15IdCard($value)
    {
        $year  =  substr($value,6,2);
        $month =  substr($value,8,2);
        $day   =  substr($value,10,2);
        $tmp = strtotime('19'.$year.'-'.$month.'-'.$day);
        if ( (intval(date('Y',$tmp))-1900) != intval($year) ||
            intval(date('m',$tmp)) != intval($month)  ||
            intval(date('d',$tmp) != intval($day))){
            return false;
        }
        return true;
    }

    /**
     * 验证18位数身份证号码中的生日是否是有效生日
     * @param $value
     * @return bool
     */
    private function isValidityBrithBy18IdCard($value)
    {
        $year  =  substr($value,6,4);
        $month =  substr($value,10,2);
        $day   =  substr($value,12,2);
        $tmp = strtotime($year.'-'.$month.'-'.$day);
        if ( (intval(date('Y',$tmp))) != intval($year) ||
            intval(date('m',$tmp)) != intval($month)  ||
            intval(date('d',$tmp) != intval($day))){
            return false;
        }
        return true;
    }

    /**
     * 判断身份证号码为18位时最后的验证位是否正确
     * @param $value
     * @return bool
     */
    private function isTrueValidateCodeBy18IdCard($value)
    {
        $Wi = [ 7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2, 1 ];    // 加权因子
        $validate_code = [ 1, 0, 10, 9, 8, 7, 6, 5, 4, 3, 2 ]; // 身份证验证位值.10代表X
        $sum = 0;                             // 声明加权求和变量
        $last = $value[17];
        if(strtolower($value[17]) =='x'){
            $last = 10;            // 将最后位为x的验证码替换为10方便后续操作
        }
        for ( $i = 0; $i < 17; $i++) {
            $sum += $Wi[$i] * $value[$i];            // 加权求和
        }
        return $last == $validate_code[ $sum % 11];
    }
}