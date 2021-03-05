# feishu-robot-msg
一款对接飞书机器人发送消息轻量级工具


# 关于
一款轻量级的飞书机器人通知工具，支持PHP签名验证

# 需求
使用即时通知，常使用来告警，业务通知

# 安装
```shell
composer require php-feishu/robot-msg
```



# 示例
```php

require __DIR__ . '/vendor/autoload.php';


/*
* function: noticeMsg
* @param string $title 发送标题
* @param string $content 发送内容
* @param string $developer 具体开发者，在项目尽量使用常量定义
*/
\Feishu\Robot\SendMsg::noticeMsg('通知标题','通知的具体内容','jackin.chen');