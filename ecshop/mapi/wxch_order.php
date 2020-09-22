<?php
define('IN_ECS', true);

require(dirname(__FILE__) . '/../includes/init.php');
include(dirname(__FILE__).'/../data/config.php');
include(dirname(__FILE__).'/apiclass.php');
ini_set('display_errors',1);
// echo '<pre>';var_dump(3);exit;
		$wxch_config = $db->getRow("SELECT * FROM `wxch_config` WHERE `id` = 1");
		$appid = $wxch_config['appid'];
		$appsecret = $wxch_config['appsecret'];
		$user_id = 9;
	    $wxch_user_openid_sql = "SELECT `openid` FROM `ecs_users` WHERE `user_id` = '$user_id'";
	    $wxch_user_openid = $db->getRow($wxch_user_openid_sql);
	    $wxch_token = getToken($appid,$appsecret);
	    template($appid,$appsecret,$wxch_user_openid['openid'],$wxch_token,$params);
	    // echo '<pre>';var_dump($appid,$appsecret,$wxch_user_openid['openid'],$wxch_token);exit;

    function template($appid,$appsecret,$openid,$accessToken,$params) {
    	$order_first = '店铺库存数量达到待补货报警阈值';
    	$order_remark = '请尽快补货。';
    	$params['wx_shop'] = '宇宙中心店';
    	$params['wx_add'] = '北京市海淀区中关村智造大街A座1层';
    	$params['wx_quantity'] = '120';

         $data = array(
                'first'=>array('value'=>urlencode($order_first),'color'=>'#3A5FCD'),
                'keyword1'=>array('value'=>urlencode($params['wx_shop']),'color'=>'#3A5FCD'),//所在店铺
                'keyword2'=>array('value'=>urlencode($params['wx_add']),'color'=>'#FF0000'),//店铺位置
                'keyword3'=>array('value'=>urlencode($params['wx_quantity']),'color'=>'#3A5FCD'),//待补货数量
                'remark'=>array('value'=>urlencode($order_remark),'color'=>'#3A5FCD')
                );

        $template_id = '-nF_gHVjaKvyph1_A2EhT6aLPLqeDlNk1w0BPeFfO8A';//订单模板ID

        $cfg_baseurl = 'http://'.$_SERVER['SERVER_NAME'];
		$back_url = $cfg_baseurl.'/appwap/main.html?user_id=1&code='.$openid.'#/login';
        $template = array(
            'touser' => $openid,
            "url" => $back_url,
            'template_id' => $template_id,
            'topcolor' => '#FF0000',
            'data' => $data
        );
        $json_template = json_encode($template);
        $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$accessToken;
        $dataRes = curl_grab_page($url, urldecode($json_template));
        return true;
    }
	//获取相关token
    function getToken($appid, $appsecret) {
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $appid . "&secret=" .$appsecret;
        $token = curl_get_contents($url);
        $token = json_decode(stripslashes($token));
        $arr = json_decode(json_encode($token), true);
        $access_token = $arr['access_token'];
        return $access_token; 
    }
	function curl_get_contents($url) 
	{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.9; rv:26.0) Gecko/20100101 Firefox/26.0");
	curl_setopt($ch, CURLOPT_REFERER,$url);
	curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	$r = curl_exec($ch);
	curl_close($ch);
	return $r;
	}
	function curl_grab_page($url,$data,$proxy='',$proxystatus='',$ref_url='') {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
	curl_setopt($ch, CURLOPT_TIMEOUT, 3);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	if ($proxystatus == 'true') 
	{
		curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, TRUE);
		curl_setopt($ch, CURLOPT_PROXY, $proxy);
	}
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_URL, $url);
	if(!empty($ref_url))
	{
		curl_setopt($ch, CURLOPT_HEADER, TRUE);
		curl_setopt($ch, CURLOPT_REFERER, $ref_url);
	}
	curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
	curl_setopt($ch, CURLOPT_MAXREDIRS, 200);
	@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
	curl_setopt($ch, CURLOPT_POST, TRUE);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	ob_start();
	return curl_exec ($ch);
	ob_end_clean();
	curl_close ($ch);
	unset($ch);
	}
?>