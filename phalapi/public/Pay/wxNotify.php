<?php
$wx = new wxNotify();
$wx->Notify();

class wxNotify
{
    /**
     * 微信通知中转
     */
    public function Notify()
    {

        $wx_config = require '../../config/app.php'; // 引入配置数据
        // 获取微信回调的数据
        $notifiedData = file_get_contents('php://input');

        $url = $wx_config['wxpay_notify']; // 数据验证  此处需要填写相应数据  更改域名即可

        $res = $this->curl_get_contents($url, $notifiedData);
        file_put_contents('./wechat.log', var_export($res, true), FILE_APPEND); // 记录返回数据  便于查看
        return $res;
    }
    public function curl_get_contents($url, $data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'RMDesign');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_URL, $url);
        $response = curl_exec($ch);

        curl_close($ch);

        return $response;
    }
}
