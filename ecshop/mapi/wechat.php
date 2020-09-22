<?php
define('IN_ECS', true);

require(dirname(__FILE__) . '/../includes/init.php');
include(dirname(__FILE__).'/../data/config.php');
include(dirname(__FILE__).'/apiclass.php');
include(dirname(__FILE__).'/WxpayAppSDK.php');
	

    /**
     * 异步通知
     * @return mixed
     */
    function notify(){
    	//填写微信分配的开放平台账号ID https://open.weixin.qq.com
		$option['appid'] = "wxf33465abccab0ff3";
		//填写微信支付分配的商户号
		$option['mchid'] = '1544216351';
		//填写微信支付结果回调地址
		$option['notify_url'] = 'http://www.mushiyuan.com.cn/mapi/wechat.php';
		//填写微信商户支付密钥
		$option['key'] = 'MuShiYuancaihonghengYuan66685555';

		$wxpaysdk = new WxpayAppSDK($option);
		
        $data=$wxpaysdk->getNotifyData();

//        log_debug("微信app支付回调",json_encode($data));
//        if(!$wxpaysdk->verifyNotify($data)){
//            $wxpaysdk->replyNotifyFail();
//            log_debug("微信app支付回调","签名失败！");
//            return false;
//        }

		if ($data['out_trade_no'] && $data['total_fee']) {
			$data['total_fee'] = $data['total_fee']/100.00;
			$sql = "update ecs_order_info set pay_time='".time()."',pay_status = '2',order_status = '1',money_paid = '".$data['total_fee']."'  WHERE order_sn = '" . $data['out_trade_no'] . "'";
		    $GLOBALS['db']->query($sql);
            $sql = "select user_id,points from  ecs_order_info  WHERE order_sn = '" . $data['out_trade_no'] . "'";
            $data1 =  $GLOBALS['db']->getRow($sql);
            insert_account_log($data1['user_id'],$data1['points'],$data['out_trade_no']);
		}

    }
    notify();

?>