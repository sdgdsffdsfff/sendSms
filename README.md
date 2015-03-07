send mobile sms
======

## 短信发送手机验证码（用户注册时）
```php
require_once './sendSms.php';
$obj = new sendSms;

$mobile                = '18702032386' //目标手机号
$_SESSION['send_code'] = '131958';     //对照码，防用户恶意请求,最好是随机生成。
$obj->sendCode($mobile, $_SESSION['send_code']){
```

## 短信发送信息
```php
require_once './sendSms.php';
$obj = new sendSms;

$mobile = '18702032386' //目标手机号
$msg    = 'hello!!'     //要发送的内容
$obj->sendMsg($mobile, $msg){
```
