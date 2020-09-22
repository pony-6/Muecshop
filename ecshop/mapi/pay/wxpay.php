<?php
define('IN_ECS', true);

require(dirname(__FILE__) . '/../../includes/init.php');
include(dirname(__FILE__).'/../../data/config.php');
include(dirname(__FILE__).'/../apiclass.php');
include(dirname(__FILE__).'/../aWxpayAppSDK.php');
	

    /**
     * 异步通知
     * @return mixed
     */
    function notify(){
    	//填写微信分配的开放平台账号ID https://open.weixin.qq.com
		$option['appid'] = "wx75d16c84209a0c04";
		//填写微信支付分配的商户号
		$option['mchid'] = '1523808911';
		//填写微信支付结果回调地址
		$option['notify_url'] = 'http://shop.kaiyuykt.com/mapi/wechat.php';
		//填写微信商户支付密钥
		$option['key'] = 'kyjxAPP1665JXcx75020726550457699';

		$wxpaysdk = new WxpayAppSDK($option);
		
        $data=$wxpaysdk->getNotifyData();

        file_put_contents('/data/httpd/www/source/ecshop/data/wechat4.log',var_export($data,true),FILE_APPEND);
//        log_debug("微信app支付回调",json_encode($data));
        if(!$wxpaysdk->verifyNotify($data)){
            $wxpaysdk->replyNotifyFail();
            log_debug("微信app支付回调","签名失败！");
            return false;
        }

		if ($data['out_trade_no'] && $data['total_fee']) {
			$data['total_fee'] = $data['total_fee']/100.00;
			$sql = "update ecs_order_info set pay_time='".time()."',pay_status = '2',order_status = '1',money_paid = '".$data['total_fee']."'  WHERE order_sn = '" . $data['out_trade_no'] . "'";

		    $GLOBALS['db']->query($sql);
		}

    }
    notify();

?>