<?php
/**
 * Created by PhpStorm.
 * User: M
 * Date: 17/6/28
 * Time: 下午5:26
 */

namespace app\components\helpers;

use yii;
use yii\base\Object;
use yii\helpers\ArrayHelper;

/**
 * 200: OK。一切正常。
201: 响应 POST 请求时成功创建一个资源。Location header 包含的URL指向新创建的资源。
204: 该请求被成功处理，响应不包含正文内容 (类似 DELETE 请求)。
304: 资源没有被修改。可以使用缓存的版本。
400: 错误的请求。可能通过用户方面的多种原因引起的，例如在请求体内有无效的JSON 数据，无效的操作参数，等等。
401: 验证失败。
403: 已经经过身份验证的用户不允许访问指定的 API 末端。
404: 所请求的资源不存在。
405: 不被允许的方法。 请检查 Allow header 允许的HTTP方法。
415: 不支持的媒体类型。 所请求的内容类型或版本号是无效的。
422: 数据验证失败 (例如，响应一个 POST 请求)。 请检查响应体内详细的错误消息。
429: 请求过多。 由于限速请求被拒绝。
500: 内部服务器错误。 这可能是由于内部程序错误引起的。
 */

/**
 * Class ApiHelper
 * @package app\components\helpers
 */
class ApiHelper extends Object
{
    /**
     * 	请求处理成功
     */
    const STATUS_CODE_SUCCESS = 200;
    const STATUS_CODE_SUCCESS_MSG = 'Success';

    /**
     * 	请求处理失败
     */
    const STATUS_CODE_FAILURE = 500;
    const STATUS_CODE_FAILURE_MSG = 'Failure';

    /**
     * 	请求未认证
     */
    const STATUS_CODE_NOT_AUTHENTICATED = 401;
    const STATUS_CODE_NOT_AUTHENTICATED_MSG = 'Request not authenticated';

    /**
     * 	请求未授权
     */
    const STATUS_CODE_NOT_AUTHORIZED = 406;
    const STATUS_CODE_NOT_AUTHORIZED_MSG = 'Request not authorized';

    /**
     * 	请求超时
     */
    const STATUS_CODE_TIMEOUT = 408;
    const STATUS_CODE_TIMEOUT_MSG = 'Token has expired';
    const REQUEST_TIMEOUT = 1800;

    /**
     * 登录缓存key
     */
    const CACHE_USER_LOGIN_KEY = 'user.login.';

    /**
     * 允许访问的路由
     * @var array
     */
    protected $allowRoutes = ['site/login', 'test/create'];

    /**
     * 允许访问的角色
     * @var array
     */
    protected $allowRule = '超级管理员';

    /**
     * 请求认证开关
     * @var bool
     */
    protected $authenticatedSwitch = false;

    /**
     * 当前路由
     * @var string
     */
    public $route;

    /**
     * 用户ID
     * @var int
     */
    public $userId;

    /**
     * 返回数据
     * @var array
     */
    public $data;

    /**
     * 用户角色
     * @var array
     */
    protected $userRules;

    /**
     * 用户路由
     * @var array
     */
    protected $userRoutes;

    /**
     * 加密key
     * @var string
     */
    protected $inputKey = '4d89g13j4j91j27c';

    /**
     * 认证
     * @return bool
     */
    public function authenticated()
    {
        $this->data = [
            'code' => self::STATUS_CODE_FAILURE,
            'message' => self::STATUS_CODE_FAILURE_MSG
        ];

        // 是否需要认证
        if($this->authenticatedSwitch === false){
            $this->data = [
                'code' => self::STATUS_CODE_SUCCESS,
                'message' => self::STATUS_CODE_SUCCESS_MSG
            ];
            return true;
        }

        // 路由认证
        if($this->routeAuthenticated() === true){
            $this->data = [
                'code' => self::STATUS_CODE_SUCCESS,
                'message' => self::STATUS_CODE_SUCCESS_MSG
            ];
            return true;
        }

        // token认证
        return $this->tokenAuthenticated();
    }

    /**
     * 路由认证
     * @param bool $isAuth
     * @return bool
     */
    protected function routeAuthenticated($isAuth = false)
    {
        if($isAuth === true){
            return in_array($this->route, $this->userRoutes);
        }else{
            return in_array($this->route, $this->allowRoutes);
        }
    }

    /**
     * 角色认证
     * @return bool
     */
    protected function ruleAuthenticated()
    {
        return !in_array($this->allowRule, $this->userRules);
    }

    /**
     * @return bool
     */
    protected function tokenAuthenticated()
    {
        $data = ArrayHelper::merge(Yii::$app->request->get(), Yii::$app->request->post());
        $token = Yii::$app->security->decryptByKey(ArrayHelper::getValue($data, 'access-token'), $this->inputKey);
        // token检测
        if($token){
            $tokenData = json_decode($token, true);
            $id = ArrayHelper::getValue($tokenData, 'id', 0);
            $cacheUserLoginKey = self::CACHE_USER_LOGIN_KEY . $id;
            $userInfo = Yii::$app->cache->get($cacheUserLoginKey);
            // token超时检测
            if($userInfo){
                $this->userId = $userInfo['id'];
                $this->userRules = $userInfo['rules'];
                $this->userRoutes = $userInfo['routes'];

                // 角色认证
                if($this->ruleAuthenticated() === true){
                    $this->data = [
                        'code' => self::STATUS_CODE_SUCCESS,
                        'message' => self::STATUS_CODE_SUCCESS_MSG
                    ];
                    Yii::$app->cache->set($cacheUserLoginKey, $userInfo,self::REQUEST_TIMEOUT);
                    return true;
                }

                // 权限路由认证
                if($this->routeAuthenticated(true) === true){
                    $this->data = [
                        'code' => self::STATUS_CODE_SUCCESS,
                        'message' => self::STATUS_CODE_SUCCESS_MSG
                    ];
                    Yii::$app->cache->set($cacheUserLoginKey, $userInfo,self::REQUEST_TIMEOUT);
                    return true;
                }else{
                    $this->data = [
                        'code' => self::STATUS_CODE_NOT_AUTHORIZED,
                        'message' => self::STATUS_CODE_NOT_AUTHORIZED_MSG
                    ];
                }
            }else{
                $this->data = [
                    'code' => self::STATUS_CODE_TIMEOUT,
                    'message' => self::STATUS_CODE_TIMEOUT_MSG
                ];
            }
        }else{
            $this->data = [
                'code' => self::STATUS_CODE_NOT_AUTHENTICATED,
                'message' => self::STATUS_CODE_NOT_AUTHENTICATED_MSG
            ];
        }

        return false;
    }

}
