# Yii2 Error DingTalk 



[![JavaScript Style Guide: Good Parts](https://img.shields.io/badge/code%20style-goodparts-brightgreen.svg?style=flat)](https://github.com/Kay-Wei/yii2-error-dingtalk "JavaScript The Good Parts")[![FOSSA Status](https://app.fossa.com/api/projects/git%2Bgithub.com%2FKay-Wei%2Fyii2-error-dingtalk.svg?type=shield)](https://app.fossa.com/projects/git%2Bgithub.com%2FKay-Wei%2Fyii2-error-dingtalk?ref=badge_shield)![](https://img.shields.io/github/languages/code-size/Kay-Wei/yii2-error-dingtalk)

基于Yii2错误处理的钉钉群机器人Webhook通知

## 安装

使用 Composer 安装:

```
$ composer require Kay-Wei/yii2-error-dingtalk
```

## 使用

1. 入口文件 `index.php` 中 `defined('YII_DEBUG') or define('YII_DEBUG', true);` 修改为 `defined('YII_DEBUG') or define('YII_DEBUG', false);`

2. 确保应用下 main.php 配置文件中已配置 

   ```php
   'errorHandler' => [
               'errorAction' => 'site/error',
           	],
   ```

3. SiteController控制器中配置

```php
public function actions()
	{
            return [
                'error' => [
                    'class' => 'kaywei\yii2ErrorDingtalk\ErrorDingtalk', 
                    'handle' => [ '500','403','401','502','404'], //启用哪些状态码
                    'title' => 'PC Web 异常状态码监测通知', //通知标题
                    'logCategoryName' => 'error-status', //日志分类名
                    'dingTalkWebHookUrl' => 'https://oapi.dingtalk.com/robot/send?access_token=xxxxxx', //钉钉群机器人获取到的webhook URL
                    'enable' => true, //是否启用
                    'text'=>'- 用户IP: '.Yii::$app->request->userIP, //设置额外的通知内容

                ],
            ];
	}
```



## 获取钉钉机器人Webhook URL

点击自己的钉钉群 - 群设置 - 智能群助手 - 群机器人 - 添加机器人 - 获取Webhook 地址

**注意： 安全设置可以选择自定义关键词 通知内容中只要包含 设置的关键词 就可以正常推送**

## 效果展示

![](https://s1.ax1x.com/2020/09/10/wJAqun.png)

# License

[![alt text](https://app.fossa.io/api/projects/git%2Bgithub.com%2FKay-Wei%2Fyii2-error-dingtalk.svg?type=large "License")](https://app.fossa.io/projects/git%2Bgithub.com%2FKay-Wei%2Fyii2-error-dingtalk?ref=badge_large)