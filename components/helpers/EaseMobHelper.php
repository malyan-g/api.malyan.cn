<?php
/**
 * Created by PhpStorm.
 * User: xywy
 * Date: 2017/3/25
 * Time: 13:56
 */

namespace app\components\helpers;

use yii\base\Object;
use yii\httpclient\Client;


/**
 * 环信
 * Class EaseMob
 * @package app\components\helpers
 */
class EaseMobHelper extends Object
{
    const URL = 'https://a1.easemob.com/xywy/yixian/';
    const GET_TOKE_URL  = "http://xianxia.club.xywy.com/index.php?r=api/EaseMobToken";
    const USER_PATIENT = 1;
    const USER_DOCTOR = 2;
    /**
     * 执行请求操作
     * @param string $path 请求的路径
     * @param string $method 请求的方式（get post delete put)
     * @param string $data 请求的数据内容
     * @return mixed
     */
    protected static function execute($path, $method, $data = '')
    {
        $header = [];
        if ($path != 'token') {
            $header = ['Authorization'=>'Bearer ' . static::getToken()];
        }
        return HttpClientHelper::request (static::URL,$method,$data,$header,Client::FORMAT_JSON);
    }

    /**
     * 获取环信accessToken
     * @return string
     */
    public static function getToken()
    {
        $key = "ease_mob_token";
        $cache = \Yii::$app->cache;
        $content = $cache->get($key);
        $accessToken = json_decode($content, true);
        if (!$accessToken || (time() - $accessToken['time'] > $accessToken['expires_in'] - 20)) {
            $time = time();
            $param = "&timestamp=$time&sign=".md5($time.'dJlkdsjfle%^***(&^&&dd');
            $result = HttpClientHelper::request(static::GET_TOKE_URL.'&'.$param,'get','',[],Client::FORMAT_JSON);
            $accessToken = $result['data'];
            $cache->set($key, $accessToken);
            $accessToken = json_decode($accessToken,true);
        }
        return $accessToken['access_token'];
    }

    /**
     * 获取环信用户
     * @param string $username
     * @return array
     */
    public static function getUser($username)
    {
        return self::execute('users/' . $username, 'get');
    }

    /**
     * 注册环信用户
     * @param string $username 用户名
     * @param string $password 用户密码
     * @param string $nickname 用户昵称
     * @return array
     */
    public static function addUser($username, $password,  $nickname = '')
    {
        $data = '{"username":"' . $username . '","password":"' . $password . '", "nickname":"' . $nickname . '"}';
        return self::execute('users', 'post', $data);
    }

    /**
     * 删除环信用户
     * @param string $username
     * @return array
     */
    public static function delUser($username)
    {
        return self::execute('users/' . $username, 'delete');
    }

    /**
     * 增加环信用户好友关系
     * @param string $fromName 用户名
     * @param string $toName 即将添加好友的用户名
     * @return array
     */
    public static function addRelation($fromName, $toName)
    {
        return self::execute('users/' . $fromName . '/contacts/users/' . $toName, 'post');
    }

    /**
     * 判断环信用户是否在线
     * @param string $userName 环信用户名
     * @return boolean true在线 false 离线
     */
    public static function userStatus($userName) {
        $retVal = self::execute('users/' . $userName . '/status', 'get');
        return $retVal['data'][$userName] == 'online';
    }

    /**
     * 删除环信用户好友关系
     * @param string $fromName 用户名
     * @param string $toName 即将删除好友的用户名
     * @return array
     */
    public static function delRelation($fromName, $toName) {
        return self::execute('users/' . $fromName . '/contacts/users/' . $toName, 'delete');
    }

    /**
     * 获取环信用户好友关系
     * @param string $userName 用户名
     * @return array
     */
    public static function getRelation($userName) {
        $data = self::execute('users/' . $userName . '/contacts/users/', 'get');
        return isset($data['data']) ? $data['data'] : array();
    }

    /**
     * 给环信好友发送普通文本消息
     * @param string $from 发送人环信用户名
     * @param string|array 接收人环信用户名，一个或多个
     * @param string $msg  发送的消息内容
     * @param array $ext 自定义消息
     * @return array
     */
    public static function setTxtMessage($from, $to, $msg, $ext = array()) {
        $data = array();
        $data['target_type'] = 'users';
        $data['target'] = is_array($to) ? $to : array($to);
        $data['msg'] = array('type' => 'txt', 'msg' => $msg);
        $data['from'] = $from;
        $data['ext'] = $ext ? $ext : new \stdClass();
        $message = json_encode($data);
        return self::execute('messages', 'post', $message);
    }

    /**
     * 给环信好友发送图片消息
     * @param string $from 发送人环信用户名
     * @param string|array 接收人环信用户名，一个或多个
     * @param string $url 图片url
     * @param string $secret 上传图片后的secret返回值
     * @param int  $width 图片宽度
     * @param int $height 图片高度
     * @param array $ext 自定义消息
     * @return array
     */
    public static function setImgMessage($from, $to, $url, $secret = '', $width = 100, $height = 100, $ext = array()) {
        $data = array();
        $data['target_type'] = 'users';
        $data['target'] = is_array($to) ? $to : array($to);
        $data['msg'] = array(
            'type' => 'img',
            'url' => $url,
            'filename' => basename($url),
            'secret' => $secret,
            'size' => array('width' => $width, 'height' => $height),
        );
        $data['from'] = $from;
        $data['ext'] = $ext;
        $message = json_encode($data);
        return self::execute('messages', 'post', $message);
    }

    /**
     * 给环信好友发透传消息
     * @param string $from 发送人环信用户名
     * @param string|array 接收人环信用户名，一个或多个
     * @param array $userMsg 用户自定义消息内容
     * @param array $ext 自定义消息
     * @return array
     */
    public static function setCmdMessage($from, $to, $userMsg = array(), $ext = array()) {
        $data = array();
        $data['target_type'] = 'users';
        $data['target'] = is_array($to) ? $to : array($to);
        $data['msg'] = array_merge(array('type' => 'cmd'), $userMsg);
        $data['from'] = $from;
        $data['ext'] = $ext;
        $message = json_encode($data);
        return self::execute('messages', 'post', $message);
    }

    /**
     * 获取环信用户名和密码
     * @param $id
     * @param $type
     * @return array
     */
    public static function getUserInfo($id,$type)
    {
        if($type == static::USER_PATIENT){
            $username = 'medicine_uid_'.$id;
        }else{
            $username = 'medicine_did_'.$id;
        }
        $password = md5('xywy.com'.$username);
        return ['username'=>$username,'password'=>$password];
    }

    /**
     * 注册用到到环信
     * @param $id
     * @param $type
     * @return array
     */
    public static function registerUser($id,$type)
    {
        $user = static::getUserInfo($id,$type);
        return static::addUser($user['username'],$user['password']);
    }
}