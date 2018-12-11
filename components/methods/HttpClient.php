<?php
/**
 * Created by PhpStorm.
 * User: houqian
 * Date: 16-1-7
 * Time: 下午1:58
 */

namespace app\components\methods;


use yii\base\UserException;

class HttpClient {

    protected $_ch ; //curl handler
    protected $_cookies = array();//设置的cookie
    protected $_headers = array();//设置的header
    protected $_code    = 0; //状态码
    protected $_msg;         //错误信息
    protected $_ssl = false; //是否用https请求
    protected $_data = null;//请求体内容
    protected $_times = 1 ;//请求失败重试次数（包括第一次请求）

    /**
     * @param string $url
     * @param bool $ssl
     * @param int $times
     * @return HttpClient
     */
    public static function getInstance($url='',$ssl=false,$times=1)
    {
        return new self($url,$ssl,$times);
    }
    /**
     * @param string $url 请求访问的url
     * @param bool $ssl 设置请求方式是否以https方式
     * @param int $times 请求失败时重试次数，最多3次
     */
    public function __construct($url='',$ssl=false,$times=1)
    {
        $this->_ch = curl_init($url);
        if($ssl){
            $this->_ssl = $ssl;
        }
        if($times>1){
            $this->setTimes($times);
        }
    }

    /**
     * @param boolean 设置请求方式是否以https方式
     * @return HttpClient
     */
    public function setSSL($boolean=true)
    {
        $this->_ssl = $boolean;
        return $this;
    }

    /**
     * @param string $name cookie名称
     * @param string $value cookie 值
     * @return HttpClient
     */
    public function setCookie($name,$value)
    {
        $this->_cookies[$name] = $value;
        return $this;
    }

    /**
     * @param string $name header名称
     * @param string $value header值
     * @return HttpClient
     */
    public function setHeader($name,$value)
    {
        $this->_headers[$name] = $value;
        return $this;
    }

    /**
     * @param string|array $data 请求方法为post put delete 时可能需要设置
     * @return HttpClient
     */
    public function setData($data)
    {
        if($data){
            $this->_data = $data;
        }
        return $this;
    }

    /**
     * @param int 设置请求失败时重试次数
     * @return HttpClient
     */
    public function setTimes($times){
        $this->_times = min(3,intval($times));
        return $this;
    }

    /**
     * 返回curl 可以自定义设置一些参数
     * @return HttpClient
     */
    public function getCurlInstance()
    {
        return $this->_ch;
    }

    /**
     * 返回执行结果状态码
     * @return int
     */
    public function getCode()
    {
        return $this->_code;
    }

    /**
     * 返回执行的错误提示信息
     * @return int
     */
    public function getMsg()
    {
        return $this->_msg;
    }

    /**
     * 初始化curl选项
     */
    protected function init($method='get',$url=null)
    {
        if($url){
            curl_setopt($this->_ch,CURLOPT_URL,$url);
        }
        curl_setopt($this->_ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($this->_ch,CURLOPT_HEADER,false);
        curl_setopt($this->_ch,CURLOPT_AUTOREFERER,true);
        curl_setopt($this->_ch,CURLOPT_CONNECTTIMEOUT,5);
        curl_setopt($this->_ch,CURLOPT_TIMEOUT,5);
        curl_setopt($this->_ch,CURLOPT_FOLLOWLOCATION,true);
        curl_setopt($this->_ch,CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
        if($this->_ssl){
            curl_setopt($this->_ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($this->_ch, CURLOPT_SSL_VERIFYPEER, 0);
        }
        if($method == 'get'){
            curl_setopt($this->_ch,CURLOPT_HTTPGET,true);
        }else if($method == 'post') {
            curl_setopt($this->_ch,CURLOPT_POST,true);
            if(!$this->_data) $this->_data = array();
            curl_setopt($this->_ch,CURLOPT_POSTFIELDS,$this->_data);
        }else {
            curl_setopt($this->_ch,CURLOPT_CUSTOMREQUEST,strtoupper($method));
            if($this->_data){
                $fields = (is_array($this->_data)) ? http_build_query($this->_data) : $this->_data;
                $this->_headers['Content-Length'] = strlen($fields);
                curl_setopt($this->_ch,CURLOPT_POSTFIELDS,$fields);
            }
        }
        $headers = array();
        foreach($this->_headers as $k=>$v){
            $headers[] = "$k:$v";
        }
        $cookie = array();
        foreach($this->_cookies as $k=>$v)
        {
            $cookie[] = "$k=$v";
        }
        if($cookie){
            curl_setopt($this->_ch,CURLOPT_COOKIE,implode('; ',$cookie));
        }
        if($headers){
            curl_setopt($this->_ch,CURLOPT_HTTPHEADER,$headers);
        }
    }

    protected function execute()
    {
        $result = false;
        while($this->_times > 0){
            $result = curl_exec($this->_ch);
            if($this->_code = curl_errno($this->_ch)){
                $this->_msg = curl_error($this->_ch);
            }else{
                $this->_code = curl_getinfo($this->_ch,CURLINFO_HTTP_CODE);
            }
            $this->_times--;
            if($this->_code == 200){
                break;
            }
        }
        if($this->_code != 200){
            throw new UserException($this->_msg,$this->_code);
        }
        return $result;
    }

    /**
     * @param null $url
     * @return bool|mixed
     */
    public function get($url=null)
    {
        $this->init('get',$url);
        return $this->execute();
    }

    /**
     * @param null $url
     * @param string $type
     * @return bool|mixed
     */
    public function post($url=null,$type="from")
    {
        $this->init('post',$url);
        if($type == 'text'){
            $this->setHeader('Content-Type','application/text');
        }
        return $this->execute();
    }


    /**
     * @param null $url
     * @return bool|mixed
     */
    public function put($url=null)
    {
        $this->init('put',$url);
        return $this->execute();
    }

    /**
     * @param null $url
     * @return bool|mixed
     */
    public function delete($url=null)
    {
        $this->init('delete',$url);
        return $this->execute();
    }
}