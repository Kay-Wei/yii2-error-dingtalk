<?php
/**
 * @name:错误处理钉钉通知
 * @author: weikai
 * @date: 20.9.10 15:11
 */

namespace kaywei\yii2ErrorDingtalk;


use yii\base\Action;

class ErrorDingtalk extends Action
{
    /**
     * @var bool 启动状态
     */
    public $enable = true;
    /**
     * @var string 默认错误处理
     */
    public $defaultErrorAction = 'yii\web\ErrorAction';
    /**
     * @var array 自定义处理状态码
     */
    public $handle = [];
    /**
     * @var string 通知标题
     */
    public $title = 'Error';
    /**
     * @var 自定义日志分类名称
     */
    public $logCategoryName;
    /**
     * @var 钉钉自定义机器人webhook url
     */
    public $dingTalkWebHookUrl;
    /**
     * @var 自定义通知内容 markdown格式
    */
    public $text;

    public function run()
    {
        $currentExceptionCode = \Yii::$app->response->getStatusCode();
        if ($this->enable){
            if (empty($this->dingTalkWebHookUrl || $this->logCategoryName)){
                throw new Exception('Required parameters are not configured for ErrorDingtalk Components');
            }
            if(in_array($currentExceptionCode, $this->handle))
            {
                \Yii::error($this->getText(),$this->logCategoryName);
                $this->dingTalkMarkdownWebHook($this->dingTalkWebHookUrl,$this->title,$this->getText());
            }
        }
        $defaultAction = new $this->defaultErrorAction('error', $this->controller);
        return $defaultAction->run();


    }

    /**
     * @return string
     * @name:获取通知内容
     * @author: weikai
     * @date: 20.9.9 14:23
     */
    protected function getText(  )
    {
        $date = date('Y-m-d H:i:s',time());
        $datetime = date('Y-m-d',time());
        $session = \Yii::$app->session;
        $code = \Yii::$app->response->getStatusCode();
        $exception = \Yii::$app->getErrorHandler()->exception;
        $text = <<<TEXT
- 异常状态码: {$code}
- 请求HOST: {$_SERVER['HTTP_HOST']}
- 请求URI: {$_SERVER['REQUEST_URI']}
- 请求METHOD: {$_SERVER['REQUEST_METHOD']}
- 请求时间: {$date}

- 服务器IP: {$_SERVER['SERVER_ADDR']}
- 错误信息: {$exception->getMessage()}
- 错误文件: {$exception->getFile()}
- 错误行数: {$exception->getLine()}
- 调用栈: {$exception->getTraceAsString()}

{$this->text}


#### 详情请查询日志文件:frontend/runtime/logs/error-status-$datetime.log
TEXT;
        return $text;
    }
    /**
     * @param $url
     * @param $title
     * @param $text
     * @param $messageUrl
     * @param $picUrl
     * @name:钉钉通知
     * @author: weikai
     * @date: 20.1.17 15:43
     */
    protected function dingTalkMarkdownWebHook( $url,$title,$text  )
    {
        $params = [
            'msgtype' => 'markdown',
            'markdown' => [
                "title"=>$title,
                "text"=>$text,
            ]
        ];
        $this->cPost($params,$url);
        return true;
    }

    /**
     * @param $param
     * @param $api
     * @return mixed
     * @name:curl Post
     * @author: weikai
     * @date: 20.9.9 15:01
     */
    protected function cPost($param ,$api )
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $api);
        curl_setopt($curl, CURLOPT_HEADER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Connection: Keep-Alive'
        ));
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_POST, 1);
        $str = json_encode($param);
        curl_setopt($curl, CURLOPT_POSTFIELDS,$str);
        $data = curl_exec($curl);
        curl_close($curl);
        return json_decode($data);
    }
}