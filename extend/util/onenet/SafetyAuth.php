<?php
namespace util\onenet;

/**
 * OneNet平台工具类
 * Class SafetyAuth
 * @package util
 * @author liuchao <249757247@qq.com>
 * @date 2021/03/04 11:22
 */

class SafetyAuth
{
    private  $_version = '2020-05-29';//版本
    private  $_method = 'sha1';//加密方法，还可以用md5、sha256
    private  $_access_key;//访问密钥
    private  $_res ;//资源参数
    private  $_et;//到期时间戳
    private  $_expiration;//有效期，有效时间

    public function __construct($access_key, $expiration,$res){
        $this->_expiration = $expiration;
        $this->_access_key = $access_key;
        $this->_res = $res;
    }

   //生成sign
    private function _makeSign() {

        date_default_timezone_set('PRC');
        $this->_et =time() + $this->_expiration;
        $string_for_signature = $this->_et . "\n" . $this->_method . "\n" . $this->_res  . "\n" . $this->_version;
        $b64_decode_acckey = base64_decode($this->_access_key);
        $hmac_key = hash_hmac($this->_method, $this->_strToUtf8($string_for_signature), $b64_decode_acckey, true);
        $sign = base64_encode($hmac_key);
        return $sign;

    }

    private function _strToUtf8($str){
        $encode = mb_detect_encoding($str, array("ASCII",'UTF-8',"GB2312","GBK",'BIG5'));
        if($encode == 'UTF-8'){
            return $str;
        }else{
            return mb_convert_encoding($str, 'UTF-8', $encode);
        }
    }

	//生成token
    public function makeToken() {
        $sign = $this->_makeSign();
        $token = sprintf("version=%s&res=%s&et=%s&method=%s&sign=%s", $this->_version, urlencode($this->_res), $this->_et, $this->_method, urlencode($sign));
        return $token;
    }

}
