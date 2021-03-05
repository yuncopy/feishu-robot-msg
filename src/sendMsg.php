<?php

namespace Feishu\Robot;

class sendMsg{

    /**
     * @var string 机器人通知地址
     */
    private static $OPEN_URL = '您自定义机器人webhook地址';

    /**
     * @var string 签名字符
     */
    private static $ROOT_SECRET = '开启签名验证字符串';

    /**
     * Notes: 发送飞书消息
     * User: yuncopy.chen
     * Date: 2021/2/25 下午5:37
     * function: doSendMessage
     * @param string $title
     * @param string $content
     * @param string $developer
     * @return mixed
     * @static
     */
    public static function noticeMsg($title,$content,$developer){
        try{
            if ($title && $content && $developer) {
                return self::sendRequest($title,$content,$developer);
            }
        }catch (\Throwable $e){
            return false;
        }
    }


    /**
     * Notes: 执行发送
     * User: yuncopy.chen
     * Date: 2021/2/25 下午6:25
     * function: sendRequest
     * @param $title
     * @param $content
     * @param $developer
     * @return mixed
     * @static
     */
    private static function sendRequest($title,$content,$developer){

        $timestamp = time();
        $message = "{$content}，开发者：{$developer}。";
        $data = json_encode([
            'timestamp'=>$timestamp,
            'sign'=>self::makeSign($timestamp),
            'msg_type'=>'post',
            'content'=>['post'=>['zh_cn'=>['title'=>"通知：{$title}",'content'=>[[['tag'=>'text','text'=>$message]]]]] ]
        ], JSON_UNESCAPED_UNICODE);
        $header = ['Content-Type: application/json; charset=utf-8'];
        return  self::doPostRequest(self::$OPEN_URL,$data,100,$header);
    }


    /**
     * Notes: HmacSHA256 算法计算签名
     * User: yuncopy.chen
     * Date: 2021/2/25 下午5:46
     * function: makeSign
     * @param string $time
     * @return string
     * @static
     */
    private static function makeSign($time=''){
        $timestamp = $time ? $time : time();
        $secret = self::$ROOT_SECRET;
        $string = "{$timestamp}\n{$secret}";
        return base64_encode(hash_hmac('sha256',"", $string,true));
    }

    /**
     * 发送请求
     * @param string $url 请求CURL
     * @param string $data 请求数据
     * @param int $timeout 请求超时时间
     * @param array $header 请求头
     * @return mixed
     */
    private static function doPostRequest($url, $data, $timeout = 10, $header = []){

        $curlObj = curl_init();
        $ssl = stripos($url,'https://') === 0 ? true : false;
        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_AUTOREFERER => 1,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)',
            CURLOPT_TIMEOUT => $timeout, //设置cURL允许执行的最长秒数
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_0,
            CURLOPT_HTTPHEADER => ['Expect:'],
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4
        ];
        if (!empty($header)) {
            $options[CURLOPT_HTTPHEADER] = $header;
        }

        if ($ssl) {
            //support https
            $options[CURLOPT_SSL_VERIFYHOST] = false;
            $options[CURLOPT_SSL_VERIFYPEER] = false;
        }
        curl_setopt_array($curlObj, $options);
        $returnData = curl_exec($curlObj);
        if (curl_errno($curlObj)) {
            //error message
            $returnData = curl_error($curlObj);
        }
        //var_dump($returnData);
        curl_close($curlObj);
        return $returnData;
    }
}

