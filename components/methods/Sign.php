<?php
namespace app\components\methods;
/*
 * 数据验证
 * */
class Sign {
    public static function encrypt($data,$key){
		if(!$data ||  !$key){
			return '';
		}
        ksort($data);
        reset($data);
        $signStr = "";
        foreach($data as $k=>$v){
            if($k !='sign' && $k !='r'){
                $signStr .= "&".$k."=".trim($v);
            }
        }
        $sign =  md5(trim($signStr,"&").$key);
        return $sign;
    }
}
