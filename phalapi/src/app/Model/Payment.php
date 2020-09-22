<?php

namespace App\Model;

use App\Model\User as UserModel;
use App\Model\wxH5Pay as wxH5Pay;
use App\Pay\AlipayService;
use App\Pay\AlipayTradeAppPayRequest;
use App\Pay\AopClient as AopClient;
use App\Pay\wxMpPay;
use App\Pay\WxpayAppSDK;
use PhalApi\Model\NotORMModel as NotORM;
use PhalApi\Request\Formatter\StringFormatter;

class Payment extends NotORM
{
    protected function getTableName($id)
    {
        return 'payment';
    }

    protected function getTableKey($table)
    {
        return 'pay_id';
    }

    public function getPaymentList($user_id)
    {
        $data = $this->getORM()->where(array('enabled' => '1'))->fetchAll();
        $this->user_model = new UserModel();

        foreach ($data as $key => $item) {
            /**
            获取预存款金额，显示在付款页面
             **/
            if ($item['pay_code'] == 'balance') {
                $tmp = $this->user_model->get_user_info($user_id);
                $data[$key]['amount_txt'] = $tmp['user_money'];
            }
        }

        return $data;
    }

    public function doPaymentMpH5($order_id, $money, $passback_params)
    {
        if ($passback_params == 'deposit') { // 余额充值
            $body = "账户余额充值";
        } else { // 订单支付
            $passback_params = 'goods';
            $sql = "SELECT goods_name FROM ecs_order_goods WHERE order_id = (SELECT order_id FROM ecs_order_info WHERE order_sn = '" . $order_id . "')";
            $goods_name = $this->getORM()->queryRows($sql); // 取出当前订单的商品名称  只用第一个
            $body = $goods_name[0]['goods_name'];
        }

        // var_dump($body);
        // 查询微信支付配置信息  config的值
        // $sql = "SELECT pay_config FROM ecs_payment WHERE pay_code = 'wxpay'";
        // $wx_pay = $this->getORM()->queryRow($sql);
        // $wx_config = unserialize($wx_pay['pay_config']);   // 暂时不使用数据库
       //$wx_config = wxpay_config();


        $hselect = "select * from ecs_shop_config where code = 'hfive'";
        $hquery =  $this->getORM()->queryRow($hselect);
        $hresult = unserialize($hquery['value']);

        $wechatAppPay = new wxH5Pay($hresult['hfiveid'], $hresult['hfivesw'], $notify_url = $hresult['hfivehome'], $hresult['hfivekey']); // 回调地址需要更改

        $params['body'] = $body; //商品描述
        $params['out_trade_no'] = $order_id; //自定义的订单号
        $params['total_fee'] = $money * 100; //订单金额 只能为整数 单位为分   传来的单位为元
        $params['trade_type'] = 'MWEB'; //交易类型 JSAPI | NATIVE | APP | WAP
        $params['attach'] = $passback_params; // 原样返回的数据 进行支付类型的判断

        $result = $wechatAppPay->unifiedOrder($params);
//        echo '<pre>';
//        var_dump($result);
//        die;

        $redirect_url = urlencode($hresult['redirect_url']);
        if (!empty($result['mweb_url'])) {
            $url = $result['mweb_url'] . '&redirect_url=' . $redirect_url; //redirect_url 是支付完成后返回的页面   后面的跳转地址需要更改
            return ['res' => true, "url" => $url];
        } else {
            // return false;   // 暂时没有错误
            return ['res' => false];
        }
    }

    public function doPaymentAliApp($order_sn, $money, $passback_params)
    {
        $sql = "SELECT goods_name FROM ecs_order_goods WHERE order_id = (SELECT order_id FROM ecs_order_info WHERE order_sn = '" . $order_sn . "')";
        $goods_name = $this->getORM()->queryRows($sql); // 取出当前订单的商品名称  只用第一个

        $aop = new AopClient();
        $request = new AlipayTradeAppPayRequest();

        $ali_config = alipay_config();

        $aop->gatewayUrl = $ali_config['gatewayUrl'];
        $aop->appId = $ali_config['appid'];


        $aop->format = "json";
        $aop->charset = "UTF-8";
        $aop->signType = "RSA2";


        $zselect = "select * from ecs_shop_config where code = 'zhifu'";
        $zquery =  $this->getORM()->queryRows($zselect);
        $zunser = mysqli_fetch_assoc($zquery);
        $zresult = unserialize($zunser['value']);
        $aop->zsy = $zresult['zsy']; //请填写开发者私钥去头去尾去回车，一行字符串
        $aop->zgy = $zresult['zgy']; //请填写支付宝公钥，一行字符串


        //实例化具体API对应的request类,类名称和接口名称对应,当前调用接口名称：alipay.trade.app.pay
        // $request = new \AlipayTradeAppPayRequest();
        //SDK已经封装掉了公共参数，这里只需要传入业务参数
        if ($passback_params != 'deposit') {
            // $passback_params = 'goods';  暂时不需要原样返回参数验证
            $bizcontent = "{\"body\":\"" . $goods_name[0]['goods_name'] . "\","
            . "\"subject\": \"" . $goods_name[0]['goods_name'] . "\","
            . "\"out_trade_no\": \"" . $order_sn . "\","
            . "\"total_amount\": \"" . sprintf('%.2f', $money) . "\","
                . "\"product_code\":\"QUICK_MSECURITY_PAY\""
                . "}";
        } else {
            $bizcontent = "{\"body\":\"账户余额充值\","
            . "\"subject\": \"账户余额充值\","
            . "\"out_trade_no\": \"" . $order_sn . "\","
            . "\"total_amount\": \"" . sprintf('%.2f', $money) . "\","
                . "\"product_code\":\"QUICK_MSECURITY_PAY\","
                . "\"passback_params\":\"" . $passback_params . "\""
                . "}";
        }

        $request->setNotifyUrl($ali_config['notifyUrl']); //商户外网可以访问的异步地址,回调地址
        $request->setBizContent($bizcontent);

        //这里和普通的接口调用不同，使用的是sdkExecute
        $response = $aop->sdkExecute($request);
        //htmlspecialchars是为了输出到页面时防止被浏览器将关键参数html转义，实际打印到日志以及http传输不会有这个问题
        //echo htmlspecialchars($response);//就是orderString 可以直接给客户端请求，无需再做处理。

        $data['payInfo'] = $response;
        return array('status' => 'succ', 'message' => '', 'response' => $data);
    }

    public function doPaymentWxApp($order_sn, $money, $passback_params)
    {
        if ($passback_params == 'deposit') { // 余额支付
            $body = '账户余额充值';
        } else { // 商品购买
            $passback_params = 'goods';
            $sql = "SELECT goods_name FROM ecs_order_goods WHERE order_id = (SELECT order_id FROM ecs_order_info WHERE order_sn = '" . $order_sn . "')";
            $goods_name = $this->getORM()->queryRows($sql); // 取出当前订单的商品名称  只用第一个
            $body = $goods_name[0]['goods_name'];
        }

        //$wx_config = wxpay_config();
        $aselect = "select * from ecs_shop_config where code = 'app'";
        $aquery =  $this->getORM()->queryRow($aselect);
//        $aunser = mysqli_fetch_assoc($aquery);
        $aresult = unserialize($aquery['value']);
        ////填写微信分配的开放平台账号ID https://open.weixin.qq.com
        $option['appid'] = $aresult['appid'];
        //填写微信支付分配的商户号
        $option['mchid'] = $aresult['appsw'];
        //填写微信支付结果回调地址
        $option['notify_url'] = $aresult['appgethome'];
        //填写微信商户支付密钥
        $option['key'] = $aresult['appkey'];

        $wxpaysdk = new WxpayAppSDK($option);

        $params['body'] = $body; //商品描述
        $params['out_trade_no'] = $order_sn; //自定义的订单号
        $params['total_fee'] = $money * 100; //订单金额 只能为整数 单位为分
        $params['attach'] = $passback_params;

        $data = $wxpaysdk->getAppPaySign($params);

        return array('status' => 'succ', 'message' => '', 'response' => $data);
    }

    public function doPaymentAliWap($out_trade_no, $total_amount, $body, $quit_url, $passback_params)
    {
        // 支付宝移动支付

        header('Content-type:text/html; Charset=utf-8');

        //$ali_config = alipay_config();
        $zselect = "select * from ecs_shop_config where code = 'zhifu'";

        $zquery =  $this->getORM()->queryRow($zselect);
        $zresult = unserialize($zquery['value']);

        $appid = $zresult['zappid']; //4https://open.alipay.com 账户中心->密钥管理->开放平台密钥，填写添加了电脑网站支付的应用的APPID
        $returnUrl = ''; //付款成功后的同步回调地址   这个为空就行
        $geteawayUrl = $zresult['zwg']; //3支付宝网关

        $notifyUrl = $zresult['zgethome']; //2付款成功后的异步回调地址
        $outTradeNo = $out_trade_no; //你自己的商品订单号
        $payAmount = $total_amount; //付款金额，单位:元
        $orderName = $body; //订单标题
        $quitUrl = $quit_url;

        $passback_params = $passback_params;
        if ($passback_params != 'deposit') { //不是余额充值既为订单付款
            $passback_params = 'goods';
        }
        $signType = 'RSA2'; //签名算法类型，支持RSA2和RSA，推荐使用RSA2
        $rsaPrivateKey = $zresult['zsy']; //1商户私钥，填写对应签名算法类型的私钥，如何生成密钥参考：https://docs.open.alipay.com/291/105971和https://docs.open.alipay.com/200/105310
        /*** 配置结束 ***/

        $aliPay = new AlipayService();
        $aliPay->setAppid($appid);
        $aliPay->setReturnUrl($returnUrl);
        $aliPay->setNotifyUrl($notifyUrl);
        $aliPay->setRsaPrivateKey($rsaPrivateKey);
        $aliPay->setTotalFee($payAmount);
        $aliPay->setOutTradeNo($outTradeNo);
        $aliPay->setOrderName($orderName);
        $aliPay->setQuitUrl($quitUrl);
        $aliPay->setPassbackParams($passback_params);
        $aliPay->setGatewayUrl($geteawayUrl);

        $sHtml = $aliPay->doPay();

        return $sHtml;

    }

    public function doPaymentWxMp($order_sn, $money, $openid, $passback_params = '')
    {
        // 微信小程序支付
        if ($passback_params == 'deposit') {
            $body = "账户余额充值";
        } else {
            $passback_params = 'goods';
            $sql = "SELECT goods_name FROM ecs_order_goods WHERE order_id = (SELECT order_id FROM ecs_order_info WHERE order_sn = '" . $order_sn . "')";
            $goods_name = $this->getORM()->queryRows($sql);
            $body = $goods_name[0]['goods_name'];
        }
        //$wx_config = wxpay_config();
        $select = "select * from ecs_shop_config where code = 'small'";
        $query =  $this->getORM()->queryRows($select);
        $result = unserialize($query[0]['value']);
//        var_dump($result);
//        die;

        $wxpay = new wxMpPay($result['id'], $result['sw'], $result['gethome'], $result['key'], $openid, $body, $order_sn, $money, $passback_params);
//var_dump($wxpay);
//die;
        $data = $wxpay->Pay(); // 获取接口信息
//        print_r($data);
//        die;
        if ($data['state'] == 1) {
            return array(
                "timeStamp" => $data['timeStamp'],
                "nonceStr" => $data['nonceStr'],
                "package" => $data['package'],
                "paySign" => $data['paySign'],
                "out_trade_no" => $data['out_trade_no'],
                "res" => true,
            );
        } else if ($data['state'] === 0) {
            return array("res" => false, "msg" => "支付配置错误");
        }

    }

    public function getPayConfig($code)
    {
        // 获取支付配置信息
        $sql = "select * from ecs_shop_config where code = '".$code."'";
        $data = $this->getORM()->queryRow($sql);
        $data = unserialize($data["value"]); // 数据转换
        return $data;
    }

    //微信浏览器H5支付
    public function wechatPay($order_sn, $money,$openid,$code){
        $sql = "select order_id,order_sn,order_amount,user_id from ecs_order_info where order_sn = ".$order_sn;
        $orders = $this->getORM()->queryRow($sql);

        $sql = "select goods_name from ecs_order_goods where order_id = ".$orders['order_id'];
        $goods = $this->getORM()->queryRow($sql);

        $config = \PhalApi\DI()->config->get('app');
        $option['appid'] = $config['wx_h5_appid'];  // 微信分配的平台账号
        $option['mchid'] = $config['wx_h5_mchid'];  //微信分配的商户号
        $option['key'] = $config['wx_h5_key']; //商户支付密钥
        $option['appsecret'] = $config['wx_h5_appsecret'];  // app secret

        //填写微信支付结果回调地址
        $option['notify_url'] = $config['wxpay_notify_url'];   // 这个是回调通知地址  他会把你支付成功的消息发送给这个地址
        $option['redirect_url'] = $config['redirect_url'];

        /*如下注释日后使用*/

        //首先判断openid是否为空，如果是空的，那么根据code去获取openid
        if(empty($openid)){
            $url ="https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$option['appid']."&secret=".$option['appsecret']."&code=".$code."&grant_type=authorization_code";
            $access_token = $this->getWechatMsg($url);
            $access_token = json_decode($access_token,true);
            $openid = $access_token['openid'];
            $sql = "update ecs_users set  openid_h5= '".$openid."' where  user_id  = '".$orders['user_id']."'";
            $this->getORM()->query($sql);
        }

//        if(empty($openid))
//        {
//            $sql = "select openid_h5 from ecs_users where user_id = {$orders['user_id']}";
//            $data = $this->getORM()->queryRow($sql);
//            $openid = $data['openid_h5'];
//        }




        if(!empty($option['appid'])){
            $_SESSION['appid']   = $option['appid'];
        }else{
            $option['appid'] = $_SESSION['appid'];
        }

        $wxpaysdk = new wxH5Pay($option['appid'], $option['mchid'], $option['notify_url'], $option['key']); // 回调地址需要更改


        $params['body'] = $goods['goods_name']; //商品描述
        $params['out_trade_no'] = $orders['order_sn']; //自定义的订单号
        $params['total_fee'] = $money * 100; //订单金额 只能为整数 单位为分   传来的单位为元
        $params['trade_type'] = 'JSAPI'; //交易类型 JSAPI | NATIVE | APP | WAP
        $params['openid'] = $openid;
        $params['attach'] = "goods";


        $result = $wxpaysdk->unifiedOrder($params,$option['key']);

        file_put_contents('appid.log',var_export($result,true),FILE_APPEND);
        $redirect_url = urlencode($option['redirect_url']);

        return array('res'=>true,'result'=>$result,'url'=>$option['redirect_url']);



    }

    /**
     * 获取微信信息
     * @desc 请求获取微信信息
     */
    public function getWechatMsg($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.9; rv:26.0) Gecko/20100101 Firefox/26.0");
        curl_setopt($ch, CURLOPT_REFERER, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $r = curl_exec($ch);
        curl_close($ch);
        return $r;
    }
}
