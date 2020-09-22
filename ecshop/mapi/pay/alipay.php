<?php
define('IN_ECS', true);

require(dirname(__FILE__) . '/../../includes/init.php');
include(dirname(__FILE__).'/../../data/config.php');
include(dirname(__FILE__).'/../apiclass.php');
include(dirname(__FILE__).'/../AlipayTradeAppPayRequest.php');
include(dirname(__FILE__).'/../AopClient.php');

ini_set('display_errors',1);

$aop = new AopClient();
$request = new AlipayTradeAppPayRequest();

$aop->gatewayUrl = "https://openapi.alipay.com/gateway.do";
        $aop->appId = '2019010462812067';
$aop->rsaPrivateKey = 'MIIEpAIBAAKCAQEAv1zSsF9LZ6luEJD2u9HpFkD6wr5hrX8hNeI4OYgKGOLnwM6uCBWXU98FF1Ud6HjWXF59rVdQohN0DuOSozFb7czDU76Mm8cyc4+xqibk9x9czst9I6ORlyFNs1ozY8FVVEW4511CUyEWiHGJnWhR0NS6+4rF+KZpVGeH7tJ5chRl3/dcnnR3PZ1v1xi3LRizC5dbOXwAJets3d6yDiBigCb//LL6qQVae0hNu370VLjrw0JUJtzQP6UZR/QxnM4AHjKBp28QKybHE40WhSAEwo/kLdvFd0AwWAhaj2qux15wdr1RwypY+Ksl+1Be9tGa4JV0QY7XX2DziDdXvPayvwIDAQABAoIBADLyW4VpWYH3sb3nWkkW0Z+DHT/Lv+WY+xaFa8KlXUrS3jCO1faBCRDjR0+28Yd65FcjMQJ9RJJh63wrSlb4RSlCYp5hFL5EgkIR95L9V+gTCXpk4qQiYBiEXVNqqFPenQEarueqBIZtQLqCv8iQhXe7qyxc/ef9Jf26so36qyqbnalmEhZVTiio17lAXT9FFESoDw1DMFCyE+yAZhxymf8NQs72PRvHHbhZUPzRzj2XiFdkj4Lq6wggBmJQY0WKPz8QEwRmpcxG93qWZKLXLov/N6gGGUY4/3r/W2UelmPRd5bBkUGmtQaHY6vEc54Xq86SN8njAvjWszby4NZ/TUECgYEA57qERiOi7XkNJJQQE4leX4v4TeuE1hq3PLIiHsvBskfOQteo9Whgt2pleKdWo1nH2mOHK6Eo+p0s9HhOuWfjFGT3/lYudCIxe+wFdXc8hzRIcUpgmaYnH+ocRFDkMFHaxSxfnNNoIM3NfrvG+hS8eKDzacQUYGmNN/GSMI2Y05MCgYEA02fyoDdV37fIvzMvv3ae7OKf0bdWZI7duwyRdZvrGDEykFypH94Z/gqP07KTb9v8meZKffsLT4uJL3VIqNTSb6Lv1JgftirPcWWJSKaobeXgGmWvgJ4BYz06SKacKETOCU51EI+gjTBvZ1k9k8v3pOpUIGEmO9zrJhWO2sCPd6UCgYEAu0BO7taRK9ArvKkgrIjOh+r1RRHSD6ka9KMFS1lfNg8sL3tRfq1PqicBxBOEwQ8lneXbLnhWQt2LnCkzhELcjnhgpVdCxnly/y5j4t3tPVwURefSU5ad+v4UxWeQXTNn0vjecj2q4QcszQIz8ZWcDuYQjCD0Tkw83z4H4dAp9J0CgYAWI2mOC51riyRzmVmjtDlaVzUjUg6zAx8HKn7FESniY6yG3506YBjauKJtKeM0sJXYS/x7CuIZV2RrWt3cCEVtOWPiIHgZrIwCtP5WZFV5BLyeJw3k6yb8DHphB0mNEf99jADIVjIa9vqDmR3Qr2hklcFvjpzhYfYiC9nk4ItlnQKBgQDPX4cXITY/36Japm7AzydVpoGjro1GOnTfDIVD84qqn37qSQNytI/I7DcLS0QxYnrufSC2NEewo0rk3pxOwKc/crfTYTS2VO9vo9xtF3F5MBByeicZ0MtUP5yqLVAiQYBzN40JkKrFXZC1IipKWL5I8RIFyOG848+/e++E0zRNqw==';  //请填写开发者私钥去头去尾去回车，一行字符串

        $aop->format = "json";
        $aop->charset = "UTF-8";
        $aop->signType = "RSA2";
$aop->alipayrsaPublicKey = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAh69NsGlh+602w4nDUqCqiHW3UjLZT6s5yGLc+26a5cY82SX1DhEFleGIIlB0M3BMelmWrSeGNVp/YMDy3D3FmnGNpdyMcm4aCRqRHU/GsHuH73/+8vMyDVRtKhA1c45+PXcoasRq7meA3a5VeCGimBwyd5lso6aI52gameN8EwIKvymO3qz8SBYxMHILAO8P4X6b2HUQtLFU6CcnARnkX5lRbAnTdsqh485M+nOhb0j2DVDfNXsYXpAi4vVL3RL37KtosfZIC+XFcp+sikxHGmybhRYMjECtdY9pp1oTO5ytXDyY4zxhFIm5UEyX4pWggl2FlVsjOly5RVPXFV41YwIDAQAB'; //请填写支付宝公钥，一行字符串

// 异步通知地址
$notify_url = urlencode('http://shop.kaiyuykt.com/mapi/wechat.php');
// 订单标题
$subject = 'DCloud项目捐赠';
// 订单详情
$body = 'DCloud致力于打造HTML5最好的移动开发工具，包括终端的Runtime、云端的服务和IDE，同时提供各项配套的开发者服务。'; 
// 订单号，示例代码使用时间值作为唯一的订单ID号
$out_trade_no = date('YmdHis', time());

        //实例化具体API对应的request类,类名称和接口名称对应,当前调用接口名称：alipay.trade.app.pay
        // $request = new \AlipayTradeAppPayRequest();
        //SDK已经封装掉了公共参数，这里只需要传入业务参数
$total = 1;
$bizcontent = "{\"body\":\"".$body."\"," 
                        . "\"subject\": \"".$subject."\","
                        . "\"out_trade_no\": \"".$out_trade_no."\","
                        . "\"total_amount\": \"".sprintf('%.2f',$total)."\","
                        . "\"product_code\":\"QUICK_MSECURITY_PAY\""
                        . "}";

$request->setNotifyUrl('http://shop.kaiyuykt.com/mapi/alipay.php'); //商户外网可以访问的异步地址,回调地址
$request->setBizContent($bizcontent);

        //这里和普通的接口调用不同，使用的是sdkExecute
$response = $aop->sdkExecute($request);
        //htmlspecialchars是为了输出到页面时防止被浏览器将关键参数html转义，实际打印到日志以及http传输不会有这个问题
        //echo htmlspecialchars($response);//就是orderString 可以直接给客户端请求，无需再做处理。
echo $response;

?>