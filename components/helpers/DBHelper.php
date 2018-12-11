<?php
/**
 * Created by PhpStorm.
 * User: xywy
 * Date: 2017/3/25
 * Time: 16:31
 */

namespace app\components\helpers;


use yii\base\Object;
use yii\db\Query;

class DBHelper extends Object
{
    /**
     * @param null $page
     * @param null $pageSize
     * @return array
     */
    public static function limitInfo($page=null,$pageSize=null)
    {
        if($page === null){
            $page = \Yii::$app->request->get('page',1);
        }
        if($pageSize === null){
            $pageSize = \Yii::$app->request->get('pagesize');
        }
        $pageSize = intval($pageSize) ;
        if($pageSize < 1){
            $pageSize = 10;
        }
        $pageSize = min($pageSize,100);
        $page = min($page,2000);
        return ['offset'=>(max(intval($page),1)-1)*$pageSize,'limit'=>$pageSize];
    }

    /**
     * @param null $page
     * @param null $pageSize
     * @param Query $query
     * @return Query
     */
    public static function setLimit(Query $query,$page=null,$pageSize=null)
    {
        $info = static::limitInfo($page,$pageSize);
        $query->offset($info['offset'])->limit($info['limit']);
        return $query;
    }
}