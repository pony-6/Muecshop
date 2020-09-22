<?php

namespace  App\Model;

use PhalApi\Model\NotORMModel as NotORM;


class Vcode extends NotORM
{
    protected  function getTableName($id)
    {
        return 'vcode';
    }

    protected function getTableKey($table)
    {
        return 'sms_id';
    }
    /**
     * 检测手机号码是否正确
     *
     */
    function is_moblie($moblie)
    {
        return  preg_match("/^1[34578]\d{9}$/", $moblie);
    }

    //检查手机号和发送的内容并生成生成短信队列
    function get_contents($phones,$msg)
    {
        if (empty($phones) || empty($msg))
        {
            return false;
        }
        $msg.= "【".$GLOBALS['_CFG']['default_sms_sign']."】";

        $phone_key=0;
        $i=0;
        $phones=explode(',',$phones);
        foreach($phones as $key => $value)
        {
            // 打平台单次请求每次不超过200个手机号
            if($i<200)
            {
                $i++;
            }
            else
            {
                $i=0;
                $phone_key++;
            }

            if($this->is_moblie($value))
            {
                $phone[$phone_key][]=$value;
            }
            else
            {
                $i--;
            }
        }
        if(!empty($phone))
        {
            foreach($phone as $phone_key => $val)
            {
                if (EC_CHARSET != 'utf-8')
                {
                    $phone_array[$phone_key]['phones']=implode(',',$val);
                    $phone_array[$phone_key]['content']=iconv('gb2312','utf-8',$msg);
                }
                else
                {
                    $phone_array[$phone_key]['phones']=implode(',',$val);
                    $phone_array[$phone_key]['content']=$msg;
                }

            }
            return $phone_array;
        }
        else
        {
            return false;
        }

    }

    //查询是否已有通行证
    function has_registered()
    {
        $sql = "SELECT `value`
                FROM ecs_shop_config  WHERE `code` = 'certificate'";

        $result = $this->getORM()->queryRow($sql);
        if (empty($result)) return false;

        $result = unserialize($result['value']);

        if(!$result['yunqi_code']) return false;

        return true;
    }

    /**
     * 获取云起证书信息
     * @param   string  $key
     * @return  string
     */
    function get_certificate_info($key,$code='certificate'){
        $sql = "select value from ecs_shop_config where code='".$code."'";
        $row = $this->getORM()->queryRow($sql);
        if(!$row) return false;
        $certificate = unserialize($row['value']);
        return isset($certificate[$key])?$certificate[$key]:false;
    }
    private function ksort($data)
    {
        if(empty($data)) return $data;
        ksort($data);
        foreach($data as $key => &$val){
            if (is_array($val)) {
                $val = $this->ksort($val);
            }
        }
        return $data;
    }

    public  function signup_sms($user_name){

        $sql = "select value from  ecs_shop_config  WHERE code = 'sms_set_update'";
        $res = $this->getORM()->queryRow($sql);
        $result = unserialize($res['value']);//数据转换
        //等于1 开始其他短信
        if($result['status']== 1){
            $userid = $result['user_id']; //用户Id
            $timestamp= time();//时间戳
//            $md5  ="ys201912345678".$timestamp; //账号+密码+时间戳 生成MD5字符串作为签名。MD5生成32位，且需要小写
            $md5  =$result['user_name'].$result['password'].$timestamp; //账号+密码+时间戳 生成MD5字符串作为签名。MD5生成32位，且需要小写
            $sign =  md5($md5);
            $sql = "select value from  ecs_shop_config  WHERE code = 'default_sms_sign'";
            $sms_sign = $this->getORM()->queryRow($sql);
            if(empty($sms_sign['value'])){
                $sms_sign['value'] = 'ecshop';
            }
            $sms_sign = "【".$sms_sign['value']."】";

            $vcode =  $this->set_vcode($user_name);
            $content = '短信验证码：'.$vcode.$sms_sign;
            $url = $result['url'].'?action=send&userid='.$userid.'&timestamp='.$timestamp.'&sign='.$sign.'&mobile='.$user_name.'&content='.$content.'&sendTime=&extno=';
            $res =  file_get_contents($url);
            //XML格式转换
            $xmlObj = simplexml_load_string($res, 'SimpleXMLElement', LIBXML_NOCDATA);
            $xmlObj = json_decode(json_encode($xmlObj), true); // 这里是最终转化后得到的数据
            if($xmlObj['returnstatus'] == 'Success'){
                return true;
            }else{
                return false;
            }

        }
        //使用ecshop 自带的短信
        if($result['status']== 2){
            $time = time()+300;
            $type = 'signup';
            $shop = 'shop.kaiyuykt.com';
            $md5 = $time.$type.$user_name.$shop;
            $sign =  strtoupper(md5($md5));
            $url = "http://shop.kaiyuykt.com/user.php?act=send&user_name=".$user_name."&t=".$time."&s=".$sign."&g=signup";
            $data = \PhalApi\DI()->config->get('app');
            $url = $data['host_url']."user.php?act=send&user_name=".$user_name."&t=".$time."&s=".$sign."&g=signup";
            $status =  file_get_contents($url);
        }

//        $res =  <<<EOF
/*<?xml version="1.0" encoding="utf-8" ?><returnsms>*/
// <returnstatus>Success</returnstatus>
// <message>ok</message>
// <remainpoint>49</remainpoint>
// <taskID>27735652</taskID>
// <successCounts>1</successCounts></returnsms>
//EOF;

        return $status;

    }

    public function set_vcode($mobile){
        $vcode = rand(1000,9999);
        $data = array(
            'mobile' => $mobile,
            'vcode' => $vcode,
            'add_time'=>time(),
        );

        $orm = $this->getORM();
        $orm->insert($data);
        return $vcode;
    }

    public function check_vcode($mobile,$vcode){
        $check_time = time()-3600;

        $sql = "select sms_id from ecs_vcode where mobile='".$mobile."' and vcode='".$vcode."' and add_time >'".$check_time."'";
        $check_data = $this->getORM()->queryAll($sql);


        if($check_data){
            return true;
        }

        return false;
    }

    public function get_vcode(){
        $randStr = str_shuffle('1234567890');
        $rand = substr($randStr,0,6);
        return $rand;
    }
}