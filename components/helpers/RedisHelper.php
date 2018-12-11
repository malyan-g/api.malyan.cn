<?php
/**
 * Created by PhpStorm.
 * User: M
 * Date: 17/3/31
 * Time: 上午11:44
 */

namespace app\components\helpers;

use Yii;
class RedisHelper
{
    public static $redis;

    /**
     * 获取redis对象
     * @return mixed
     */
    private static function redis()
    {
        self::$redis = Yii::$app->redis;
        return self::$redis;
    }

    /**
     * 设置
     * @param $key
     * @param $val
     * @param $expire
     */
    public static function set($key, $val, $expire = 0)
    {
        $redis = self::redis();
        $redis->set($key, $val);
        if($expire){
            $redis->expire($key, $expire);
        }
    }

    /**
     * 获取
     * @param $key
     */
    public static function get($key)
    {
        return self::redis()->get($key);
    }

    /**
     * 删除
     * @param $key
     */
    public static function del($key)
    {
        self::redis()->del($key);
    }

    /**
     * 判断某个key是否存在
     * @param $key
     * @return bool
     */
    public static function exists($key)
    {
       return self::$redis->exists($key);
    }

    /**
     * 添加value元素到key队列
     * @param $key
     * @param $val
     * @param $expire
     */
    public static function setQueue($key, $val, $expire = 0)
    {
        $redis = self::redis();
        $redis->rpush($key, $val);
        if($expire){
            $redis->expire($key, $expire);
        }
    }

    /**
     * 获取key队列的元素
     * @param $key
     * @param int $start
     * @param int $end
     * @return mixed
     */
    public static function getQueue($key, $start = 0, $end = -1)
    {
        return self::redis()->lrange($key, $start, $end);
    }

    /**
     * 从key队列中删除value
     * @param $key
     * @param $value
     * @param int $count
     */
    public static function remQueue($key, $value, $count = -1)
    {
        self::redis()->lrem($key, $count, $value);
    }
}