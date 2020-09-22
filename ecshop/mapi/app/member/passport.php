<?php
/**
    @author     :   dev@yin-duo.com
    @since      :   2018/10/11
**/
class passport extends apiclass{
    function signin($parmas){
//短信登录
    if ($parmas['vcode']) {
            $sql = "select * from ecs_users where mobile_phone ='".$parmas['uname']."'";
            $user = $this->db->getRow($sql);
            if (!$user) {
                    $data['message'] = '手机号不存在';
                    $data['status'] ='fail';
                    $data['response'] = $response;
                    return $this->langTransReturn($data,$parmas['lang']);  
            }
 

                $sql = "select * from ecs_user_vcode where mobile = '".$parmas['uname']."'";
                $tmp = $this->db->getRow($sql);
                if($tmp['vcode'] != $parmas['vcode']){
                    $data['message'] = '验证码错误，请重新填写';
                    $data['status'] ='fail';
                    $data['response'] = $response;
                    return $this->langTransReturn($data,$parmas['lang']);
                }



    }else{
//标准账号密码登录
           $user = init_users();
           $res = $user->check_user($parmas['uname'], $parmas['password']);
            if(!$res){
                $data['status'] ='fail';
                $data['response'] = '';
                $data['message'] ='登录失败';
                return $this->langTransReturn($data,$parmas['lang']);
            }
    }
        $sql = "SELECT eu.*,eur.rank_id,eur.rank_name from  ecs_users eu left join ecs_user_rank eur on eu.user_rank = rank_id where user_name = '".$parmas['uname']."'";
        $row = $this->db->getRow($sql);

        $response = $this->get_login_info($parmas);

        $data['status'] ='succ';
        $data['response'] = $response;
        $data['message'] ='登录成功';
        return $this->langTransReturn($data,$parmas['lang']);

    }
//找回密码1
    function lost_verify_vcode($parmas){
// echo '<pre>';var_dump($parmas);exit;
        $sql = "select * from ecs_users where mobile_phone ='".$parmas['mobile']."'";

        $user = $this->db->getRow($sql);
            if (!$user) {
                    $data['message'] = '手机号不存在';
                    $data['status'] ='fail';
                    $data['response'] = $response;
                    return $this->langTransReturn($data,$parmas['lang']);  
            }
        $sql = "select * from ecs_user_vcode where mobile = '".$parmas['mobile']."'";
        $tmp = $this->db->getRow($sql);
            if($tmp['vcode'] != $parmas['vcode']){
                    $data['message'] = '验证码错误，请重新填写';
                    $data['status'] ='fail';
                    $data['response'] = $response;
                    return $this->langTransReturn($data,$parmas['lang']);
            }
        $parmas['uname']=$parmas['mobile'];
        $response = $this->get_login_info($parmas);

        $data['status'] ='succ';
        $data['response'] = $response;
        $data['message'] ='登录成功';

        return $this->langTransReturn($data,$parmas['lang']);


    } 
    //找回密码2
    function lost_reset_password($parmas){
        //验证
        if (SESS_ID != $parmas['lost_token']) {
                    $data['message'] = '修改失败，请重试';
                    $data['status'] ='fail';
                    $data['response'] = $response;
                    return $this->langTransReturn($data,$parmas['lang']);
        }
        $sql = "select * from ecs_users where user_id ='".$parmas['member_id']."'";
        $user = $this->db->getRow($sql);
        if($user['ec_salt']){
            $password = md5(md5($parmas['password']).$user['ec_salt']);
        }else{
            $password = md5($parmas['password']);
        }


        $sql = "update ecs_users set password='".$password."' where user_id='".$parmas['member_id']."' ";

        $GLOBALS['db']->query($sql);

        $data['status'] ='succ';
        $data['response'] = $response;
        $data['message'] ='登录成功';

        return $this->langTransReturn($data,$parmas['lang']);

    }   
    function get_login_info($parmas){
        if (!$parmas['uname']) {
           $parmas['uname']= $parmas['mobile_phone'];
        }
  
//        $sql = "SELECT eu.*,eur.rank_id,eur.rank_name from  ecs_users eu left join ecs_user_rank eur on eu.user_rank = rank_id where user_name = '".$parmas['uname']."'";
        $sql = "SELECT eu.*,eur.rank_id,eur.rank_name from  ecs_users eu left join ecs_user_rank eur on eu.user_rank = rank_id where user_name = '".$parmas['uname']."'";

        $row = $this->db->getRow($sql);

        $response['member_id'] = $row['user_id'];
        $response['email'] = $row['email'];
        $response['uname'] = $row['user_name'];
        $response['member_lv_id'] = $row['rank_name'];
        
        $response['accesstoken'] = SESS_ID;
        
        $_SESSION['user_id']   = $row['user_id'];
        $_SESSION['user_name'] = $row['user_name'];
        $_SESSION['email']     = $row['email'];
        return $response;
    }
    
    function checklogin($params){
        $res = $this->check_token($params['member_id'],$params['accesstoken']);

        if($res){
            $data['status'] ='succ';
            $data['message'] ='成功';
            $data['response'] = $res;
            return $data;
        }
        $data['status'] ='fail';
        $data['message'] ='失败';
        $data['response'] = $res;
        return $data;
    }
    
    function signup($params){
        $data['status'] ='fail';
        $data['response'] = $params;
        $mobile_phone = $params['mobile_phone'];
        $vcode = $params['vcode'];
        $email = $params['email'];
        $user_name = $params['user_name'];
        $password = $params['password'];
        $confirm_password = $params['confirm_password'];

        // $parent_mobile_phone = $params['parent_mobile_phone'];
        $payment = $params['payment'];
        if ($password != $confirm_password){
            $data['message'] = '两次输入密码不匹配';

            // return $data;
            return $this->langTransReturn($data,$params['lang']);
        }
        if (strlen($mobile_phone) != '11'){
            $data['message'] = '手机号码错误';
            // return $data;
            return $this->langTransReturn($data,$params['lang']);
        }
        if (strlen($password) < 6){
            $data['message'] = '密码小于6位，请重新填写';
            // return $data;
            return $this->langTransReturn($data,$params['lang']);
        }

        $sqlb = "select * from ecs_users where mobile_phone = '".$mobile_phone."' ";
        $tmpb = $this->db->getRow($sqlb);
        if($tmpb){
            $data['status'] ='fail';
            $data['message'] ='手机号码已被注册';
            $data['response'] = $res;
            // return $data;
            return $this->langTransReturn($data,$params['lang']);
        }

        $sql = "select * from ecs_user_vcode where mobile = '".$mobile_phone."'";
        $tmp = $this->db->getRow($sql);

       if($tmp['vcode'] != $vcode){
           $data['message'] = '验证码错误，请重新填写';
           return $data;
           // return $this->langTransReturn($data,$params['lang']);
       }
       
        $user = init_users();
        $user_name = $mobile_phone;
//        $res = $user->add_user($user_name, $password, $email);
        $md5 = md5($password);
        $sql = "insert into ecs_users (user_name,mobile_phone,password) values ('".$user_name."','".$mobile_phone."','".$md5."')";
        $this->db->query($sql);
        if($params['parent_id']){
            $parent['user_id'] = $params['parent_id'];
        }
        $sql = "update ecs_users set mobile_phone = '".$mobile_phone."',parent_id='".$parent['user_id']."',pay_password='".$payment."',reg_time='".time()."' where mobile_phone = '".$mobile_phone."'";

        $this->db->query($sql);


        $response = $this->get_login_info($params);
        $data['status'] ='succ';
        $data['response'] = $response;
        $data['message'] ='注册成功';
        // return $data;
        return $this->langTransReturn($data,$params['lang']);
    }
    //登陆短信
    function send_signup_sms($params){
        if (strlen($params['mobile_phone']) != '11'){
            $data['status'] ='fail';
            $data['message'] ='手机号码错误';
            $data['response'] = $res;
            return $this->langTransReturn($data,$params['lang']);
            // return $data;
        }
        $sql = "select * from ecs_users where mobile_phone = '".$params['mobile_phone']."' or user_name = '".$params['mobile_phone']."'";
        $tmp = $this->db->getRow($sql);
        // if($tmp){
        //     $data['status'] ='fail';
        //     $data['message'] ='手机号码已被注册';
        //     $data['response'] = $res;
        //     // return $data;
        //     return $this->langTransReturn($data,$params['lang']);
        // }
        
        $vcode = rand(1000,9999);
        $add_time = time();
        $expire_time = time()+15*60;
        $sql = "delete from ecs_user_vcode where mobile = '".$params['mobile_phone']."'";
        $this->db->query($sql);
        $sql = "insert into ecs_user_vcode (mobile,vcode,add_time,expire_time) values (
            '".$params['mobile_phone']."',
            '".$vcode."',
            '".$add_time."',
            '".$expire_time."')
        ";
        $this->db->query($sql);
        
        $content = '验证码为：'.$vcode;
        $this->sms->send($params['mobile_phone'], $content, 0);
        $data['status'] ='succ';
        $data['message'] ='成功';
        $data['response'] = $res;
        return $this->langTransReturn($data,$params['lang']);
        // return $data;
    }
    //找回密码短信发送
    function lost_send_vcode($params){
        if (strlen($params['mobile_phone']) != '11'){
            $data['status'] ='fail';
            $data['message'] ='手机号码错误';
            $data['response'] = $res;
            return $this->langTransReturn($data,$params['lang']);
            // return $data;
        }
        $sql = "select * from ecs_users where mobile_phone = '".$params['mobile_phone']."' or user_name = '".$params['mobile_phone']."'";
        $tmp = $this->db->getRow($sql);
        if(!$tmp){
            $data['status'] ='fail';
            $data['message'] ='手机号码未注册';
            $data['response'] = $res;
            // return $data;
            return $this->langTransReturn($data,$params['lang']);
        }
        
        $vcode = rand(1000,9999);
        $add_time = time();
        $expire_time = time()+15*60;
        $sql = "delete from ecs_user_vcode where mobile = '".$params['mobile_phone']."'";
        $this->db->query($sql);
        $sql = "insert into ecs_user_vcode (mobile,vcode,add_time,expire_time) values (
            '".$params['mobile_phone']."',
            '".$vcode."',
            '".$add_time."',
            '".$expire_time."')
        ";
        $this->db->query($sql);
        
        $content = '验证码为：'.$vcode;
        $this->sms->send($params['mobile_phone'], $content, 0);
        $data['status'] ='succ';
        $data['message'] ='成功';
        $data['response'] = $res;
        return $this->langTransReturn($data,$params['lang']);
        // return $data;
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
}

?>