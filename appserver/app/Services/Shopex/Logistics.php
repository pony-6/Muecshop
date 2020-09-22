<?php 
namespace App\Services\Shopex;

use Log;
use App\Models\v2\ShopConfig;

class Logistics
{
    public static function info($order_sn)
    {
        $certificate_info = ShopConfig::findByCode('certificate');
        
        if (!$certificate_info) {
            return false;
        }

        $certificate = unserialize($certificate_info);

        $token = $certificate['token'];

        //获取物流信息参数
        $param = array(
            'act' => 'ecmobile_get_logistics_info',//固定方法
            'order_sn' => $order_sn,//订单号
            'return_data' => 'json',//返回类型
        );

        $ac = self::get_ac($param, $token);//验证签名

        $param['ac'] = $ac;//签名值放入参数中

        Log::debug("获取物流信息参数".json_encode($param));

        $api = config('app.shop_url') . '/api.php';

        Log::debug("地址".$api);

        $response = curl_request($api, 'POST', $param);

        if ($response['result'] == 'success' && isset($response['info'])) {
            $format = [];

            if (!empty($response['info'])) {
                foreach ($response['info'] as $key => $value) {

                    $format[] = [
                        'datetime' => is_numeric($value['AcceptTime'])?$value['AcceptTime']:strtotime($value['AcceptTime']),
                        'content' => $value['AcceptStation']
                    ];
                }
            }

            return array_reverse($format);
        }

        return false;
    }

    //验证方法
    public static function get_ac($params, $token)
    {
        ksort($params);
        $tmp_verfy='';
        foreach ($params as $key=>$value) {
            $params[$key]=stripslashes($value);
            $tmp_verfy.=$params[$key];
        }
        return strtolower(md5(trim($tmp_verfy.$token)));
    }

    public static function order($goods_list)
    {
        $certificate_info = ShopConfig::findByCode('certificate');
        
        if (!$certificate_info) {
            return false;
        }

        $certificate = unserialize($certificate_info);

        $token = $certificate['token'];

        $a = array(
            "goods_id" => 81,
            "product_id" => 0,
            "subtotal" => "100"
        );
        $b = array(
            "goods_id" => 72,
            "product_id" => 0,
            "subtotal" => "149"
        );
        $goods_list = json_encode(array($a ,$b));
        //获取物流信息参数
        $param = array(
            'act' => 'compute_discount',//固定方法
            'goods_list' => $goods_list,//商品结构
            'return_data' => 'json',//返回类型
        );

        $ac = self::get_ac($param, $token);//验证签名

        $param['ac'] = $ac;//签名值放入参数中

        $api = "http://ecshop72.shopex123.com/api.php";

        $response = curl_request($api, 'POST', $param);

        if ($response['result'] == 'success' && isset($response['info'])) {
            $format = [];

            if (!empty($response['info'])) {
                foreach ($response['info'] as $key => $value) {
                    $format[] = [
                        'datetime' => $value['AcceptTime'],
                        'content' => $value['AcceptStation']
                    ];
                }
            }

            return array_reverse($format);
        }

        return false;
    }
}
