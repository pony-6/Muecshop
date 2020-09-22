<?php

class payment extends apiclass
{
	function alipay_pay($param){

		$sql = "select order_id,order_sn,order_amount from ecs_order_info where order_sn = ".$param['order_id'];
        $orders = $this->db->getRow($sql);

        $sql = "select goods_name from ecs_order_goods where order_id = ".$orders['order_id'];
        $goods = $this->db->getRow($sql);
    	$aop = new AopClient();
	    $request = new AlipayTradeAppPayRequest();
        $aop->gatewayUrl = "https://openapi.alipay.com/gateway.do";
//        $aop->appId = '2019010462812067';
        $aop->appId = '2018073060829712';
        $aop->method = 'alipay.trade.wap.pay';
        $aop->rsaPrivateKey = 'MIIEpgIBAAKCAQEAtTp8Adk1s8p4nH0RzKECrJJN/b7Cwt/lLrEGHQJnQzHjwRlrDTUGC/HRAPMnNS0Zesu6BS4FRV9f5q/Wua4Uv7tqIL0OtBwzrpEIZRLpKav5SWKefnAfXI6WMVafkZdmsWPus7z+Ohj2cf9gJf2g0h7NK5CBL5PeZdiiNWuGk9BaczR4ZJB8Y5+Eab7hlA744xYGcrWFVnYw6cNUHpckNDTkdv1u6nuNYE9QrGPiEbsN0jK9VIXa6Crgm0d7sJAPVj5qlgnEiMjArcHRN2Cp1BGyOXfDUDEbKEfSWd731t02kZcxExnjQU/+Byvy57Uj4ZLnlpYmsAK0KO5HHrt4aQIDAQABAoIBAQCZbSDtA7H2OzRu72dxQOKdrOMALk5+YsSJCe9uAcngVk1F/jnmHXy4Agn6buBDoaHPlsgueG6X842iJD16Rwlj5MuNWeEn1DRg1oIv55mj4OcUHLynuE6kskYvEPHYdT/IcBz258qm7tk9W4FPyHAtU0xJVHY6DEqdGJEC/rwZSD+BmmouPJxUat++K8xAgXLcn8G8HvKxZuwfTtK4QNUqp6FFzEVyBVvISCb7EyyCUaXvz1mr2i57IdWFj5BZhk8lYMk0GoMDBn9RdAn2scgDprWUoLp5KKbcSBVWMCrumUGf6SHmsywEs98nYZS8w0yAGOV/ZpMrmq31tcmYUgkxAoGBANmMs1SGA7f122yZFAyVCPg9UnGgOBO3iYvkxYNB1+ZVG7RIrhFUPxB66255Y86tYgyuWe6lekWdRrt1Tx1a/eam5/ZbGZD27B5mjQGiRzuR0nrIpyi1jOSCXBDNmLIt0E6eOs6jMtGduinbdXsbjxdxOvLj2PaaktdGhiRmQlH9AoGBANVCY3a04IAB21jTlxF0/K0G9dAFT3O7fwgJEg2IIepRhcZoMMJ2QxGBmlnYL7LzszsFV531btHyc+RexLlfie8C0v9sC/MdLOcLutd/J/1CYJ3OT+LPOhhsBQ+l97ocNzOpUhr3dS6ksqAmq71jpAm4J4nDHSXP5SKKYa3f38XdAoGBAIBEvkOa1Lx1gI+yXijhAq3i8iY+snGlqLrMA0zV2KddDP2qUL+07Y4Y+5Fij19/ySzy6+GSvdon1lmW3DPRv5xsUp5lrlhTznpKOt94wAk2fGSaxDxzdwQQfJoHrv13l7eTAdduT/tZxTcCx2zzndXTlE872mPkVaoKUfNKO9Y9AoGBAMROS06PK4TJsMbwZsQsAxenK4kkCkIHWuTjrJmGMWoHRvQfHpsyz4QC4DlJ1oaM6/Qtc7y0myFpZCLY1Y+qEUdROzbhl5JfzeCUnJYXt5DalCNPMZwfk2O9s173MLVBmdLVTv4Bwf2An+jqD/bTDMHhoYufbmpLF6oW5dlvFgaFAoGBAKWXmT+5RKItIfLvJ0+gLhuR+ahQNCDf1GzaZ2sc07lvQ5eRxGc16dMzWI2K8o740Mk9i3MhbxLSwkeSwepWK/28p+dBm08X0i/SW7Uov3S5nTt74FZ0Kilw4t3x+FXioIf7Yrx6XfYZTc6qzC5mE2vf6QfkC0wJxFiiwvCqhc58';  //请填写开发者私钥去头去尾去回车，一行字符串
        $aop->apiVersion = '1.0';
        $aop->postCharset='GBK';
        $aop->format = "json";
        $aop->charset = "UTF-8";
        $aop->signType = "RSA2";
        $aop->alipayrsaPublicKey = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA387Fqoh5d8rx+3uJPg7H/8TWJgyTDcs+BbTUDvSzaN3pjXPgP2X782SfOwtiKsaY3G4w2h82XndZRq3x2GAW2e6lHFu5PYL4rje+gftmUCShYzrYw9p+kVRjzWhIpSsYE7ztt4qSc6Nf0Jk/86vbCFZGRM6CB5eZMMR+k6kIHRJ8cA1iPK7v6HWgUtz0WnZ7i7xkTZkP2VyStvxRbiaT6slffeyaAp95oRmG7AuUMnsMsFoLLp6ixhaq4iYa5AM3k1CqAvU3wJgmbE9MhEC0uIEB8ysvrN8D7QMtZiAzAc43ioNYFcEp3XRY1FlNtFm+LGKmA6diq3bpArRRuZHMjwIDAQAB'; //请填写支付宝公钥，一行字符串

        //实例化具体API对应的request类,类名称和接口名称对应,当前调用接口名称：alipay.trade.app.pay
        // $request = new \AlipayTradeAppPayRequest();
        //SDK已经封装掉了公共参数，这里只需要传入业务参数
        $bizcontent = "{\"body\":\"".$goods['goods_name']."\"," 
                        . "\"subject\": \"".$goods['goods_name']."\","
                        . "\"out_trade_no\": \"".$orders['order_sn']."\","
                        . "\"total_amount\": \"".sprintf('%.2f',$orders['order_amount'])."\","
                        . "\"product_code\":\"QUICK_MSECURITY_PAY\""
                        . "}";

        $request->setNotifyUrl('http://shop.kaiyuykt.com/mapi/alipay.php'); //商户外网可以访问的异步地址,回调地址
        $request->setBizContent($bizcontent);
        $response = $aop->pageExecute ( $request);
        //这里和普通的接口调用不同，使用的是sdkExecute
//        $response = $aop->sdkExecute($request);

        //htmlspecialchars是为了输出到页面时防止被浏览器将关键参数html转义，实际打印到日志以及http传输不会有这个问题
        //echo htmlspecialchars($response);//就是orderString 可以直接给客户端请求，无需再做处理。
	$data['payInfo'] = $response;
	return array('status'=>'succ', 'message'=>'', 'response' => $data);
	}

	function alipay_wap($param){

        $sql = "select order_id,order_sn,order_amount from ecs_order_info where order_sn = ".$param['order_id'];
        $orders = $this->db->getRow($sql);

        $sql = "select goods_name from ecs_order_goods where order_id = ".$orders['order_id'];
        $goods = $this->db->getRow($sql);
        if($param['pay_app_id'] == '66'){
            $sql = "SELECT * FROM  ecs_users WHERE user_id = '".$param['member_id']."'";
            $user = $GLOBALS['db']->getRow($sql);
            $o_amount=  $orders['order_amount'] -  $user['user_money'];
            $orders['order_amount'] = round($o_amount, 2);

        }
        $appid = '2016101000653257';  //https://open.alipay.com 账户中心->密钥管理->开放平台密钥，填写添加了电脑网站支付的应用的APPID
        $returnUrl = 'http://www.fenxiao.com/appwap/#/member';     //付款成功后的同步回调地址
        $notifyUrl = 'http://ecshop.fenxiao.ecweixin.cn/mapi/alipay.php';     //付款成功后的异步回调地址
        $outTradeNo = $orders['order_sn'];     //你自己的商品订单号
        $payAmount = $orders['order_amount'];          //付款金额，单位:元
        $orderName = $goods['goods_name'];    //订单标题
        $signType = 'RSA2';			//签名算法类型，支持RSA2和RSA，推荐使用RSA2
        $rsaPrivateKey='MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQCjl7kB7An8fIdvyKw9V/NPYTUMglpXbW4U96xc1Ek0GN2g2NdvMeXc8iltU0rAVVperv2TPUsbG9wdSUt2oFS12lGTTejXLsS0+yN4VWfeEYC8hR1IjysxWjE6vYoZJnBrUq2UKwGAML5b00MnTgEZSKZPqIYo9l21eHGKHkTBcU7v2R6y8HhDm9qEPFK/H575I4UkyKnS6ObRp5GIk35gDzBeX/Toa6vqKRLUvcs/+YMdS2IQz+fQnoj44+ieYfyCAoPGUkh4MPQnEt/To/IaroyyVfJ8hiszQIp+l4ruioHMWUlCbDU+OlLJ6WaQWpjMTVXpZyzbPQL/MoMrsJmlAgMBAAECggEAd4jyf0wI0/vuPqjj6gLs31DJIXXSK1XjfOCoij/3qWCN+OzZJf/Q1QwBZ0fYNAdp/AtjcAX2bj2CLcgTov9uRl7bAoaIH0umIhPrjA7j0Wj6p0Wg6xbp2arsk95RHSVwOYt1F/IAq7lyLsiEpiCiPuZN16DtPtseB+6VD/YkCS/hGKJKS4cohYJTrKDmMWd7Hj77pBPoVAYVxI5c2VhtIsv34ZIl5aAZqaD71DFhvzhFqozeGP2149Qc3C/F/yFuNGthRgh6BeQ8gtJbxsiCOLSAm//Ke9ZCKc0HEvHxbWQl8mZnNwS6bfIePNvtKSChY5HzQMeR4wN5HTIhZlaewQKBgQD1anBZch5V0WK4HJaRX3qfrbRJkzmsnR/Z9P55+SdpDKX0UOs9O5e3SQtkOi6flpH9a3eKkqSMzXZte56TRTgAbr9j8B+mRTmCf+ikuOBpm8O4Gg8BtAtQCameNCkjrkggFkOUAM349J+PUEYtOkHuak6ktyQCU+kA9kWCCQcYVQKBgQCqpeZR3izvSZsPWM863mHHvGojfYDJxaRNsv0GsKlh441xt6ShZQANclHVme1kh0VFhJDsZsgwejG4k9qcoZwPNuUc2JwxnLEBBP9KDQzMiZg2g2pKYJrQYoBMvJJml3gz3JsJjGsjBkJTN2EgfljNmcL+yHkSIHGr+QJ7LMQMEQKBgFi6CLBt78W7E+PYgh9A08aTOJE5JyrfC7rzNmXGzJbQOdegwcu7ldhwEixEVMLh3xouFmQFLHSze9ONVAGOjvapE40ALZEhie9Ca0vSg1/rLtGKqk5FV6myNJ674PvDcNQY+Imz2MPfPSjFLvn/DAM0cAZhKCWnTKBKaUrgFK6ZAoGAdKmeX/HIPn7PpxpL8i7+IhLJbSHr3gVkYkoveVdlNSrgFBI8Vqo58vdowuLMzKE91lzexv8tdRbUzx8loVdK3Yvl7maXwcMhr1S/QtzRFzQp/3qwO9D3hecRV2TDoaeD4dC7nTeGNxecWE/P8urtwwGPsadUPfQ4Qh2meJcOIEECgYEAuGp1u8xAPhqa64lbXv5dWxdMQMHety6lFk7fZ9AsVf8cuc5m2POqtA7aISJozoqDOii5tpVDiJxKbdqjhh1Y/raU0duQ/fizlfsz8dzO6n+E4j+KGmMCnIEPWmwWhaxGH7Jmx/pFfkE7x5zdoBFL7Gd2jSChQnhCfTC751egdGs=';
        //商户私钥，填写对应签名算法类型的私钥，如何生成密钥参考：https://docs.open.alipay.com/291/105971和https://docs.open.alipay.com/200/105310
        /*** 配置结束 ***/
        $aliPay = new AlipayService();
        $aliPay->setAppid($appid);
        $aliPay->setReturnUrl($returnUrl);
        $aliPay->setNotifyUrl($notifyUrl);
        $aliPay->setRsaPrivateKey($rsaPrivateKey);
        $aliPay->setTotalFee($payAmount);
        $aliPay->setOutTradeNo($outTradeNo);
        $aliPay->setOrderName($orderName);
        $sHtml = $aliPay->doPay();
        $data['payInfo'] = $sHtml;
        return array('status'=>'succ', 'message'=>'', 'response' => $data);

    }

	function wechat_pay($param){

//        file_put_contents('/data/httpd/www/test/ecshop.fenxiao.ecweixin.cn/ecshopfenxiao/ecshop/data'.'/abcde.log',var_export($param,true),FILE_APPEND);

		$sql = "select order_id,order_sn,order_amount from ecs_order_info where order_sn = ".$param['order_id'];
        $orders = $this->db->getRow($sql);

        $sql = "select goods_name from ecs_order_goods where order_id = ".$orders['order_id'];
        $goods = $this->db->getRow($sql);

		////填写微信分配的开放平台账号ID https://open.weixin.qq.com
		$option['appid'] = "wxf33465abccab0ff3";
		//填写微信支付分配的商户号
		$option['mchid'] = '1544216351';
		//填写微信支付结果回调地址
		$option['notify_url'] = 'http://www.mushiyuan.com.cn/mapi/wechat.php';
		//填写微信商户支付密钥
		$option['key'] = 'MuShiYuancaihonghengYuan66685555';
		$option['appsecret'] = 'a190687892782afb9621723266200cac';

		$sql = "SELECT openid FROM `ecs_users` WHERE `user_id` = '".$param['member_id']."'";
        $u_openid = $GLOBALS['db']->getRow($sql);

        //用户的opne_id
        if (empty($u_openid[ 'openid'])) {

            $code =  $param['code'];//微信code
            //通过code获取access_token   在这里就从微信返回了app，以下都是在app里进行的操作了
            $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$option['appid'].'&secret='.$option['appsecret'].'&code='.$code.'&grant_type=authorization_code';
            $ret_oa_json = $this->curl_get_contents($url);
            $resultjson = json_decode($ret_oa_json);
            $access_token = $resultjson -> access_token;
            $openid = $resultjson -> openid;

            $sql = "update ecs_users set  openid= '".$openid."' where  user_id  = '".$param['member_id']."'";
            $GLOBALS['db']->query($sql);
        }else{
            $openid = $u_openid['openid'];
        }

		$wxpaysdk = new WxpayAppSDK($option);


        if($param['pay_app_id'] == '99'){
            $sql = "SELECT * FROM  ecs_users WHERE user_id = '".$param['member_id']."'";
            $user = $GLOBALS['db']->getRow($sql);
            $o_amount=  $orders['order_amount'] -  $user['user_money'];
            $orders['order_amount'] = round($o_amount, 2);

        }

 	    $params['body'] = $goods['goods_name'];                        //商品描述
 	    $params['open_id'] = $openid;                        //open_id
        $params['out_trade_no'] = $orders['order_sn'];    //自定义的订单号
        $params['total_fee'] = bcmul($orders['order_amount'], '100', '0');                    //订单金额 只能为整数 单位为分

        $data=$wxpaysdk->getAppPaySign($params);

	    return array('status'=>'succ', 'message'=>'', 'response' => $data);
	}

//    function b_wechat_pay($param){
//
////        file_put_contents('/data/httpd/www/test/ecshop.fenxiao.ecweixin.cn/ecshopfenxiao/ecshop/data'.'/abcde.log',var_export($param,true),FILE_APPEND);
//
//        $sql = "select order_id,order_sn,order_amount from ecs_order_info where order_sn = ".$param['order_id'];
//        $orders = $this->db->getRow($sql);
//
//        $sql = "select goods_name from ecs_order_goods where order_id = ".$orders['order_id'];
//        $goods = $this->db->getRow($sql);
//
//        ////填写微信分配的开放平台账号ID https://open.weixin.qq.com
//        $option['appid'] = "wx01e8e212c21e5adc";
//        //填写微信支付分配的商户号
//        $option['mchid'] = '1463803302';
//        //填写微信支付结果回调地址
//        $option['notify_url'] = 'http://ecshop.fenxiao.ecweixin.cn/mapi/wechat.php';
//        //填写微信商户支付密钥
//        $option['key'] = 'e7a1878fb11c0373dc646ae5ae18e43b';
//        $option['appsecret'] = '82f0f623f5bf23b59a1487b33c036bf6';
//        $sql = "SELECT openid FROM `ecs_users` WHERE `user_id` = '".$param['member_id']."'";
//        $u_openid = $GLOBALS['db']->getRow($sql);
//
//        //用户的opne_id
//        if (empty($u_openid[ 'openid'])) {
//
//            $code =  $param['code'];//微信code
//            //通过code获取access_token   在这里就从微信返回了app，以下都是在app里进行的操作了
//            $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$option['appid'].'&secret='.$option['appsecret'].'&code='.$code.'&grant_type=authorization_code';
//            $ret_oa_json = $this->curl_get_contents($url);
//            $resultjson = json_decode($ret_oa_json);
//            $access_token = $resultjson -> access_token;
//            $openid = $resultjson -> openid;
//            $sql = "update ecs_users set  openid= '".$openid."' where  user_id  = '".$param['member_id']."'";
//            $GLOBALS['db']->query($sql);
//        }else{
//            $openid = $u_openid['openid'];
//        }
//        $wxpaysdk = new WxpayAppSDK($option);
//
//        $params['body'] = $goods['goods_name'];                        //商品描述
//        $params['open_id'] = $openid;                        //open_id
////        $params['open_id'] = 'o10kYwigGY4uAoMZCKVaJS9lI_gU';                        //open_id
//        $params['out_trade_no'] = $orders['order_sn'];    //自定义的订单号
//        $params['total_fee'] = bcmul($orders['order_amount'], '100', '0');                    //订单金额 只能为整数 单位为分
//
//        $data=$wxpaysdk->getAppPaySign($params);
//
//        return array('status'=>'succ', 'message'=>'', 'response' => $data);
//    }

    //返回url 访问url 登录拿到code
    function get_wx_code($params){

        //填写微信分配的开放平台账号ID https://open.weixin.qq.com
        $appid = "wxf33465abccab0ff3";
        $appsecret = 'a190687892782afb9621723266200cac';
        $scope = "snsapi_base";
        $state = 'wechat';
        $redirect_uri = 'http://www.mushiyuan.com.cn/appwap/#/pay_order?order_id='.$params['order_id'];
        $redirect_uri =  urlencode($redirect_uri);

        $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$appid.'&redirect_uri='.$redirect_uri.'&response_type=code&scope='.$scope.'&state='.$state.'#wechat_redirect';

        $data['status'] ='succ';
        $data['url'] = $url;
        $data['message'] ='';
        return $data;
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


    /* 凯宇云卡通支付

        Api/Api/SecurityCode.ashx  验证
        Api/Api/GetCardMsg.ashx   卡户查询
        Api/Api/Record_XF.ashx     支付
    */
    // b44ae5c8bf08715a54ae0d17b6b25a00 密钥

    public function ky_pay($params)
    {
        $res = [
            'status'=>'fail',
            'message'=>'',
            'response'=>[],
        ];
        $sql = "select * from ecs_order_info where order_sn ='".$params['order_sn']."' and user_id='".$params['member_id']."' and pay_status !='2'";
        $order = $this->db->getRow($sql);
        if(!$order){
            $res['message'] = '订单不存在或已支付';
            return $res;
        }
        $p_code = $params['p_code'];
        $s_code = $params['s_code'];

        $uri = [
            'verification'=>'Api/Api/SecurityCode.ashx',
            'select'=>'Api/Api/GetCardMsg.ashx',
            'pay'=>'Api/Api/Record_XF.ashx'
        ];
        $sql = "select * from pay_config where pay_key='url'";
        $url = $this->db->getRow($sql);
        $url = $url['value'];
//        $key = 'b44ae5c8bf08715a54ae0d17b6b25a00';

          $sql = "select * from pay_config where pay_key='key'";
          $key = $this->db->getRow($sql);
          $key = $key['value'];

//        $url = $this->db->getRow($sql);

        $check_params = [
            'p_code' => $p_code,
            's_code' => $s_code,
            'key' =>$key,
        ];
        $check_response = $this->check_ky_pay($uri['verification'],$url,$check_params);
        if($check_response['respCode'] != 'Success'){
            $res['message'] = '云卡通卡号安全码错误或卡片不可用';
            return $res;
        }



        $sql = "Select * from pay_config where pay_key='comId'";
        $comId = $this->db->getRow($sql);
        $comId = $comId['value'];
//        /* 固定1596 */
//        $comId = '1596';
        $select_params = [
            'comId'=>$comId,
            'p_code'=>$p_code,
            'key'=>$key,
        ];
        $select_response = $this->select_card($uri['select'],$url,$select_params);
        if($select_response['respCode'] != 'Success'){
            $res['message'] = '云卡通卡号安全码错误';
            return $res;
        }

        /* 更新p_code,s_code */
        $this->update_ps_code($params['member_id'],$p_code,$s_code);

        if($select_response['respDesc']['money'] < $order['order_amount']){
            $res['message'] = '余额不足';
            return $res;
        }
        $sql = "select * from pay_config where pay_key='machno'";
        $machno = $this->db->getRow($sql);
        $machno = $machno['value'];
        $noce_str = md5(time());
        $pay_params = [
            'comId'=>$comId, //单位编号
            'p_code'=>$p_code, //人员编号
            'money'=>$order['order_amount'], //消费金额(次数)
            'opType'=>'1', //消费类型
            'nonce_str'=>$noce_str, //随机字符串
            'machno'=>$machno, //机具号
            'key'=>$key, //key
        ];
        $pay_response = $this->pay($uri['pay'],$url,$pay_params);
        $pay_params_s = serialize($pay_params);
        $pay_response_s = serialize($pay_response);
        if($pay_response['respCode'] != 'Success'){
            $status= 'fail';
        }else{
            $status ='succ';
        }
        $sql = "insert into ykt_pay_log (user_id,order_id,params,response,status,created_at) values ('".$params['member_id']."','".$order['order_id']."','".$pay_params_s."','".$pay_response_s."','".$status."','".time()."')";
        $this->db->query($sql);
        $pay_response['respCode'] = 'Success';
        if($pay_response['respCode'] == 'Success'){
            $order = $this->finish_order($order['order_id']);

            $sql = "select user_id,points from  ecs_order_info  WHERE order_id = '" . $order['order_id'] . "'";
            $data =  $GLOBALS['db']->getRow($sql);
            insert_account_log($data['user_id'],$data['points'],$order['order_sn']);
            $res['status'] = 'succ';
            $res['message'] = '支付成功';
            $res['response'] = $order;
            return $res;
        }else{
            $res['message'] = '云卡通卡号付款失败';
            return $res;
        }

    }

    /* 凯宇检查卡号安全码 */
    public function check_ky_pay($uri,$url,$params)
    {
        $url = $url.$uri;
        $response = $this->curl($url,$params);
        return $response;
    }

    /* 卡户查询接口 */
    public function select_card($uri,$url,$params)
    {
        $url = $url.$uri;
        $response = $this->curl($url,$params);
        return $response;
    }

    /* 用户支付接口 */
    public function pay($uri,$url,$params)
    {
        $url = $url.$uri;
        $response = $this->curl($url,$params);
        return $response;
    }


    public function curl($url,$post_data)
    {
        $ch = curl_init(); //初始化curl
        curl_setopt($ch, CURLOPT_URL, $url);//设置链接
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//设置是否返回信息
        curl_setopt($ch, CURLOPT_POST, 1);//设置为POST方式
        curl_setopt($ch, CURLOPT_HEADER, 0);//设置是否返回头信息

        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);//POST数据
        $response = curl_exec($ch);//接收返回信息
//        if (curl_errno($ch)) {//出错则显示错误信息
//            print curl_error($ch);
//        }
        curl_close($ch); //关闭curl链接

        $response = json_decode($response,true);
        return $response;
    }

    public function finish_order($order_id)
    {

        $sql = "update ecs_order_info set order_status='1',confirm_time='".time()."',pay_status='2',pay_time ='".time()."' where order_id='".$order_id."'";
        $sql_res = $this->db->query($sql);

        $sql = "select * from ecs_order_info where order_id='".$order_id."'";
        $order = $this->db->getRow($sql);
        $order['order_id'] = $order['order_sn'];
        return $order;
    }

    public function update_ps_code($user_id,$new_p_code,$new_s_code)
    {
        $sql = "select * from ecs_reg_extend_info where user_id='".$user_id."' and reg_field_id='100'";
        $p_code = $this->db->getRow($sql);
        if($p_code){
            $sql = "update ecs_reg_extend_info set content = '".$new_p_code."' where user_id='".$user_id."' and reg_field_id='100'";
        }else{
            $sql = "insert into ecs_reg_extend_info (user_id,reg_field_id,content) values ('".$user_id."','100','".$new_p_code."') ";
        }
        $this->db->query($sql);

        $sql = "select * from ecs_reg_extend_info where user_id='".$user_id."' and reg_field_id='101'";
        $s_code = $this->db->getRow($sql);
        if($s_code){
            $sql = "update ecs_reg_extend_info set content = '".$new_s_code."' where user_id='".$user_id."' and reg_field_id='101'";
        }else{
            $sql = "insert into ecs_reg_extend_info (user_id,reg_field_id,content) values ('".$user_id."','101','".$new_s_code."') ";
        }
        $this->db->query($sql);
    }
}