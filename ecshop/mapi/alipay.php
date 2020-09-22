<?php
define('IN_ECS', true);
require(dirname(__FILE__) . '/../includes/init.php');
include(dirname(__FILE__).'/../data/config.php');
include(dirname(__FILE__).'/apiclass.php');
include(dirname(__FILE__).'/AlipayTradeAppPayRequest.php');
//include(dirname(__FILE__).'/AopClient.php');
ini_set('display_errors',1);
// ini_set('display_errors',1);
// echo '<pre>';var_dump(3);exit;
//回调待验证

$aop = new AopClient();

$request = new AlipayTradeAppPayRequest();

$aop->gatewayUrl = "https://openapi.alipay.com/gateway.do";
        $aop->appId = '2019010462812067';
$aop->rsaPrivateKey = 'MIIEpAIBAAKCAQEAv1zSsF9LZ6luEJD2u9HpFkD6wr5hrX8hNeI4OYgKGOLnwM6uCBWXU98FF1Ud6HjWXF59rVdQohN0DuOSozFb7czDU76Mm8cyc4+xqibk9x9czst9I6ORlyFNs1ozY8FVVEW4511CUyEWiHGJnWhR0NS6+4rF+KZpVGeH7tJ5chRl3/dcnnR3PZ1v1xi3LRizC5dbOXwAJets3d6yDiBigCb//LL6qQVae0hNu370VLjrw0JUJtzQP6UZR/QxnM4AHjKBp28QKybHE40WhSAEwo/kLdvFd0AwWAhaj2qux15wdr1RwypY+Ksl+1Be9tGa4JV0QY7XX2DziDdXvPayvwIDAQABAoIBADLyW4VpWYH3sb3nWkkW0Z+DHT/Lv+WY+xaFa8KlXUrS3jCO1faBCRDjR0+28Yd65FcjMQJ9RJJh63wrSlb4RSlCYp5hFL5EgkIR95L9V+gTCXpk4qQiYBiEXVNqqFPenQEarueqBIZtQLqCv8iQhXe7qyxc/ef9Jf26so36qyqbnalmEhZVTiio17lAXT9FFESoDw1DMFCyE+yAZhxymf8NQs72PRvHHbhZUPzRzj2XiFdkj4Lq6wggBmJQY0WKPz8QEwRmpcxG93qWZKLXLov/N6gGGUY4/3r/W2UelmPRd5bBkUGmtQaHY6vEc54Xq86SN8njAvjWszby4NZ/TUECgYEA57qERiOi7XkNJJQQE4leX4v4TeuE1hq3PLIiHsvBskfOQteo9Whgt2pleKdWo1nH2mOHK6Eo+p0s9HhOuWfjFGT3/lYudCIxe+wFdXc8hzRIcUpgmaYnH+ocRFDkMFHaxSxfnNNoIM3NfrvG+hS8eKDzacQUYGmNN/GSMI2Y05MCgYEA02fyoDdV37fIvzMvv3ae7OKf0bdWZI7duwyRdZvrGDEykFypH94Z/gqP07KTb9v8meZKffsLT4uJL3VIqNTSb6Lv1JgftirPcWWJSKaobeXgGmWvgJ4BYz06SKacKETOCU51EI+gjTBvZ1k9k8v3pOpUIGEmO9zrJhWO2sCPd6UCgYEAu0BO7taRK9ArvKkgrIjOh+r1RRHSD6ka9KMFS1lfNg8sL3tRfq1PqicBxBOEwQ8lneXbLnhWQt2LnCkzhELcjnhgpVdCxnly/y5j4t3tPVwURefSU5ad+v4UxWeQXTNn0vjecj2q4QcszQIz8ZWcDuYQjCD0Tkw83z4H4dAp9J0CgYAWI2mOC51riyRzmVmjtDlaVzUjUg6zAx8HKn7FESniY6yG3506YBjauKJtKeM0sJXYS/x7CuIZV2RrWt3cCEVtOWPiIHgZrIwCtP5WZFV5BLyeJw3k6yb8DHphB0mNEf99jADIVjIa9vqDmR3Qr2hklcFvjpzhYfYiC9nk4ItlnQKBgQDPX4cXITY/36Japm7AzydVpoGjro1GOnTfDIVD84qqn37qSQNytI/I7DcLS0QxYnrufSC2NEewo0rk3pxOwKc/crfTYTS2VO9vo9xtF3F5MBByeicZ0MtUP5yqLVAiQYBzN40JkKrFXZC1IipKWL5I8RIFyOG848+/e++E0zRNqw==';  //请填写开发者私钥去头去尾去回车，一行字符串

        $aop->format = "json";
        $aop->charset = "UTF-8";
        $aop->signType = "RSA2";
$aop->alipayrsaPublicKey = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAh69NsGlh+602w4nDUqCqiHW3UjLZT6s5yGLc+26a5cY82SX1DhEFleGIIlB0M3BMelmWrSeGNVp/YMDy3D3FmnGNpdyMcm4aCRqRHU/GsHuH73/+8vMyDVRtKhA1c45+PXcoasRq7meA3a5VeCGimBwyd5lso6aI52gameN8EwIKvymO3qz8SBYxMHILAO8P4X6b2HUQtLFU6CcnARnkX5lRbAnTdsqh485M+nOhb0j2DVDfNXsYXpAi4vVL3RL37KtosfZIC+XFcp+sikxHGmybhRYMjECtdY9pp1oTO5ytXDyY4zxhFIm5UEyX4pWggl2FlVsjOly5RVPXFV41YwIDAQAB'; //请填写支付宝公钥，一行字符串

$param=$_POST;//接受post数据，参考【支付结果异步通知】
file_put_contents('/data/httpd/www/source/ecshop/data/alipay6666.log',var_export($param,true),FILE_APPEND);

$res=$aop->rsaCheckV1($param,'','RSA2');
file_put_contents('/data/httpd/www/source/ecshop/data/alipay1111.log',var_export($res,true),FILE_APPEND);
// if(!$res){
//      // log_error("支付宝notify通知",$param['out_trade_no']."参数验证失败");
//      //        return error("参数验证失败");
// 	echo '<pre>';var_dump('参数验证失败');exit;
//      }


file_put_contents('/data/httpd/www/source/ecshop/data/alipay1.log',var_export($param,true),FILE_APPEND);
if ($param['out_trade_no'] && $param['total_amount']) {
	$sql = "update ecs_order_info set pay_time='".time()."', order_status = '1',pay_status = '2',money_paid = '".$param['total_amount']."'  WHERE order_sn = '" . $param['out_trade_no'] . "'";
    $GLOBALS['db']->query($sql);
    $sql = "select user_id,points from  ecs_order_info  WHERE order_sn = '" . $param['out_trade_no'] . "'";
    $data =  $GLOBALS['db']->getRow($sql);
    insert_account_log($data['user_id'],$data['points'],$param['out_trade_no']);
}



?>
