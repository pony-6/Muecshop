<?php
/**
 * 请在下面放置任何您需要的应用配置
 *
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author dogstar <chanzonghuang@gmail.com> 2017-07-13
 */

return array(

    /**
     * 应用接口层的统一参数
     */
    'apiCommonRules' => array(
//        'app_key' => array('name' => 'app_key', 'require' => true, 'default' => 'wolf100000001', 'desc' => 'key'),
        //        'format' => array('name' => 'format', 'require' => true, 'default' => 'JSON', 'desc' => 'JSON'),
        //        'api_version' => array('name' => 'api_version', 'require' => true, 'default' => '2.0', 'desc' => '版本'),
        //        'timestamp' => array('name' => 'timestamp', 'require' => true, 'desc' => '时间'),
        //        'req_source' => array('name' => 'req_source', 'require' => true, 'default' => 'app', 'desc' => '类型'),
        //        'sign' => array('name' => 'sign', 'require' => true, 'desc' => '加密sign'),
    ),

    /**
     * 接口服务白名单，格式：接口服务类名.接口服务方法名
     *
     * 示例：
     * - *.*         通配，全部接口服务，慎用！
     * - Site.*      Api_Default接口类的全部方法
     * - *.Index     全部接口类的Index方法
     * - Site.Index  指定某个接口服务，即Api_Default::Index()
     */
    'service_whitelist' => array(
        'Site.Index',
    ),

    'host_url' => 'http://www.mushiyuan.com.cn/',
    'host_url_res' => 'http://www.mushiyuan.com.cn/',
//    'host_url_res' => 'http://kaiyu.ecshop.net.ecweixin.cn/',
    'default_category_img' => 'https://imgt1.oss-cn-shanghai.aliyuncs.com/tools/default_category.png',
    'default_category_banner' => 'https://imgt1.oss-cn-shanghai.aliyuncs.com/tools/default_category_banner.jpg',
    'default_article_img' => 'https://imgt1.oss-cn-shanghai.aliyuncs.com/tools/default_category_banner.jpg',
    'default_category_goodsImage' => 'https://imgt1.oss-cn-shanghai.aliyuncs.com/tools/default_category_banner.jpg',//默认商品图片
    'alipay_h5_url' => 'https://api.ecshop.yunyingbao.net/?service=App.Pay.aliPayWap', // 支付宝手机网页支付调起地址
    /**
     * 支付配置部分
     */
    /* 支付宝支付配置 */
    'gateway_url' => 'https://openapi.alipaydev.com/gateway.do', // 支付宝网关地址
    'alipay_appid' => '2016101300678355', // 支付宝APPID
    'rsa_private_key' => 'MIIEpQIBAAKCAQEAw+bZRwy/HPBwEB0TKYNifxzHm+gONpS1wPpT+U1wEtrChCd6mx+t07RUZy7kplS4E+NkwcRARmzAchLVM4NiSngSBUAdu6SGCb4jtKVfbYqwHk1Un/KnRmOpoatjCL+vdzmo85H304a9E1cK/bh641/fTrYOejmDOq7+UbVe61chS10HjXuoFFwE276DWBj4mvagsqn6AbhwM3YfGlKut6NP0VmyKrEILkqa043CVl0AvAXdw3jxE3FIbKAyC8OkGcOVpECEsCip+j5vYEJPX6GlKuPsKwPYSZV55c6abfBRSkTW5f9PK16b+CRWEjQYszlRmX86x2T1hyvlYZlKFQIDAQABAoIBAAY0ndVeVf94rlQUV7MM8mXMZ5ZMt4aLF2bRhy9ygIZYX6kIBx461qrKH5JVLfEG5f3bSinJKbARadczOYW0N7zcKNX5vfdfss9EMqVaR7eUmReco32dliboqUrxvuVcDRbdwLhWe187+WlPbI209k+VjeFUawj8IWgOewfjkuopfiEx8Ths/+RYuNXPfKt5goih4UUgxLyZMQZ/EAJImS0stJkdniAKn6LVDZhh0e64pqogR4pBIKVterWzniSK67vj4Md3XT6CG3otVMCB4evYeVASXbVYa/hfebbs5L4T3rQ+iXqqhWpiXMAMjv0DMOuxdjyd66bICWRmG58/7dECgYEA+rz762op6J+SXbNfY8UnGbyNGgsVE6+vQsvweQEUTLrvZOGj4JfQ1PHnenXCY04rYHAr8VxRX04tHklfywYfE+7RCOZb4VjUClVPtTRhVlt7K/fe0cHHh2Q+9HGGp7MsvdudPkPWiMr9q9uJ7QN7OABsVx9Wf/MNN+fmxhxOJO8CgYEAyANFrtYY+/Xx9ElhSKRu6PaH35Alr+TJ1x9+7XRr1DQ1bO5zUpouqurqOda23L7fkyERdLjz6b+UDh+DAt8JpYY/aktwop3yOITFbP8pLpFrYu97wideroQ8Hw3a+r+e+oI7XE7MhqKAnPKD1twkK1FV3Zyq6pa5YlDnZqAbqTsCgYEAzgw/eVXk2qVHQnkFCpgZaazox14UE/lrrRiqpMWeSO/VirFiWk5n7pZcAuaCt9ilDLshYVbcB9XWfe0OK2j8YQUAArBLEQROO6+fsOk6lBzZO2iWUwlLTpeypG4mmelD+0FeFqzov8EQdQoUbhFOUNcIMRAvpTW2xxprKqJYFUECgYEAlC6ob7G67OD4Ew5SfukkKoCLhbxbz4bnavOwq+f3A05yznOTCP2l2YcVZSiIbd8T8QMs1Zc5TlJbNGNwQq5PjUx6qxudJT1zeSAUzH32WsPuFWxr+hoE8bKSgEdZlzBkbiASLn9K0+yM8LpTptmOCnHF9dveXNKyIf7ikYvJMoUCgYEA2QOPmREvNDGDdsiaxh2Dtefk3Vsw0V/RM8ubFcPYPldL6KEhAMCySBEBFbFnMngZ1p0u+jU8US+wyN6vXKpWeYlA+980sjzPT5uRsT947GEyuyGCi8iCEBq43EDIvHhdg2sruVvz3KzBS4SQoQkyCr2HF5DPbFKkQCx9NIYSEvc=', // 支付宝私钥
    'alipayrsa_public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA8qRuafEmrSg6VjGN5hX01azbB758u43cLVq4TyKJ+4IY4TSdMDrYfoh/VWP++bMoMAxW7542b2auZmjQG4WzZzBofwSY2FaMbGMNdRRcv1JgogjD8dLdtcIA7/Bk0WFfWN2I7ZO0lpMyi2eCrKVcrLrnQCpCkOmJGfjk+S/PfOYBQERYB2gi65t4qPjC+jMH+YyRP3WFmsKULMDYg1olTp47RJ2RDUt+tVelPGag2QvTQ4s3SW3eGXTypi6fjH/+iOOypCcxmd40wQZhM6zoCtrafnUHxfP9mvghwk5WQmmtbOVUl+XtgrAefwxARPJZ2S7swjFx/CpgtsCfNNq34QIDAQAB', // 支付宝公钥
    'alipay_notify_url' => 'https://api.ecshop.yunyingbao.net/?service=App.Pay.aliPayNotify', // 支付宝回调验证地址
    /* 微信支付配置 */
    'wxpay_appid_app' => 'wx10db0f61dc2a007c', // 微信支付appid  APP支付 需要 开放平台的appid
    'wxpay_appid' => 'wxf33465abccab0ff3', // 微信支付H5
    'wxpay_appid_mp' => 'wx60e8a1a1640539fd', // 微信小程序支付及小程序登录所需要的appid
    'mchid' => '1544216351', // 微信支付商户号,
    'wxpay_notify_url' => 'http://api.mushiyuan.com.cn/Pay/wxNotify.php', // 这个地址是微信回调地址  中转地址  接收数据后会发送到下面的地址去进行验证  原因：微信回调地址不可携带参数
    'wxpay_notify' => 'http://api.mushiyuan.com.cn/?service=App.Pay.wxPayNotify', // 这个地址是微信回调数据验证地址
    'key' => 'MuShiYuancaihonghengYuan66685555', // 微信支付key
    'appsecret' => 'a190687892782afb9621723266200cac', // 微信登录时需要的值
    'redirect_url' => 'http://www.mushiyuan.com.cn/h5/apiCart/payresult/main?', // 这个是H5支付后跳转的页面  一般填写H5首页地址

    /*故宫微信h5支付配置*/
    'wx_h5_appid' => 'wxf33465abccab0ff3',  //微信h5的 APPID
    'wx_h5_mchid' => '1544216351', //微信H5支付商户号
    'wx_h5_appsecret' => 'a190687892782afb9621723266200cac', //微信H5的APP SECRET
    'wx_h5_key' => 'MuShiYuancaihonghengYuan66685555', // 微信H5的 支付key
    'wx_h5_notify_url' => 'http://api.mushiyuan.com.cn/?service=App.Pay.wxPayNotify'
);
