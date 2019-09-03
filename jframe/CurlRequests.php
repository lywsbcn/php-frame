<?php
namespace jframe;
/**
 * Created by PhpStorm.
 * User: shellvon
 * Date: 16/5/28
 * Time: 下午11:49
 */

/**
 * Class CurlRequests.
 */
class CurlRequests
{
    private $curl_options = array(
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_0,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => 'utf8',
        CURLOPT_USERAGENT => 'CurlRequests',
    );
    private $timeout = 30;
    private $req_method = 'GET';
    private $headers = array();
    private $replace_space = false;
    private $allow_method = array('GET',"POST");

    protected static $instances = array();
    /**
     * 单例.
     *
     * @return self
     */
    public static function Instance()
    {
        return self::InstanceInternal(__CLASS__);
    }

    /**
     * 单例.
     *
     * @param string $cls 类名字.
     *
     * @return mixed
     */
    protected static function InstanceInternal($cls)
    {
        if (!isset(self::$instances[$cls]))
            self::$instances[$cls] = new $cls();
        return self::$instances[$cls];
    }

    /**
     * 设置curl的选项.
     *
     * @param $key
     * @param $value
     *
     * @return $this
     */
    public function setCurlOption($key, $value)
    {
        $this->curl_options[$key] = $value;
        return $this;
    }

    public function replaceSpace($b){
        $b = $b ? true : false;
        $this->replace_space = $b;
        return $this;
    }

    /**
     * 设置请求方法.
     *
     * @param string $method 请求方法.
     *
     * @return $this
     */
    public function setRequestMethod($method)
    {
        $method = strtoupper($method);
        $method = in_array($method,$this->allow_method) ? $method : "GET";
        $this->req_method = $method;
        return $this;
    }

    /**
     * 设置请求头,比如User_agent等信息.
     * 可以多次设置
     * @param $value  如: Content-Type: application/json; charset=utf-8
     *
     * @return $this
     */
    public function setHeader($value)
    {
        if(empty($value)) return;
        $this->headers[] = $value;
        return $this;
    }

    /**
     * @param $value  如: array('Content-Type: application/json', 'charset=utf-8')
     * 
     * @return $this
     */
    public function setHeaders($value){
        $this->headers = $value;
        return $this;
    }

    /**
     * 设置超时时间，单位秒,默认30秒.
     *
     * @param $timeout
     *
     * @return $this
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
        return $this;
    }

    /**
     * 发起一个请求.
     *
     * @param string $url    请求URL.
     * @param array  $params 请求的参数.
     *
     * @return mixed 请求结果.
     * @throws \Exception
     */
    public function request($url, $params=array())
    {
        $ch = curl_init();
        foreach ($this->curl_options as $k => $v) {
            curl_setopt($ch, $k, $v);
        }
        switch ($this->req_method) {
            case 'GET':
                $contact_char = strpos($url, '?') === false ?  '?' : '&';
                $url = $url.$contact_char.http_build_query($params, null, '&');

                if($this->replace_space){
                    $url = str_replace('+', '%20', $url);
                }
                break;
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
                break;
        }
        /*不添加下面一个选项或报错误:
            curl: (60) SSL certificate problem: unable to get local issuer certificate 错误        
        */
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($ch, CURLOPT_URL, $url);
        $response = curl_exec($ch);
        if ($response === false) {
            throw new \Exception(curl_error($ch));
            $response = array("flag"=>2,"content"=>curl_error($ch),"data"=>"");
        }else{
            return $response;
            $response =array("flag"=>1,"data"=>$response);
        }
        curl_close($ch);
        return $response;
    }

}

