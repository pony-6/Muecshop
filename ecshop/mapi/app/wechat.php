<?php

class wechat extends apiclass{
    
    //微信登录1
    function get_openid($params){
        $sql = "select * from  ecs_config where code = 'wechat.web'  and status ='1'";
        $app_cofig = $this->db->getRow($sql);
        $config =  json_decode($app_cofig['config']);
        $appid = $config->app_id;
        $appsecret = $config->app_secret;

//        $appid = 'wxf33465abccab0ff3';
//        $appsecret = 'a190687892782afb9621723266200cac';
        $scope = "snsapi_userinfo";
        $state = 'wechat';
        $redirect_uri = 'http://www.mushiyuan.com.cn/appwap/#/login';
//        $redirect_uri = 'http://www.mushiyuan.com.cn/notify.php';

        $redirect_uri =  urlencode($redirect_uri);
        $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$appid.'&redirect_uri='.$redirect_uri.'&response_type=code&scope='.$scope.'&state='.$state.'#wechat_redirect';

        $data['status'] ='succ';
        $data['url'] = $url;
        $data['message'] ='';
        return $data;

    }
    /*
     *微信登录2
     * */
    function wx_login($code){

        $sql = "select * from  ecs_config where code = 'wechat.web'  and status ='1'";
        $app_cofig = $this->db->getRow($sql);
        $config =  json_decode($app_cofig['config']);
        $appid = $config->app_id;
        $appsecret = $config->app_secret;
//        $appid = 'wxf33465abccab0ff3';
//        $appsecret = 'a190687892782afb9621723266200cac';

        $url ="https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$appid."&secret=".$appsecret."&code=".$code['code']."&grant_type=authorization_code";
        $access_token = $this->curl_get_contents($url);
        $access_token = json_decode($access_token);
        $access_token1 = $access_token -> access_token;
        $openid = $access_token -> openid;

        $url1= "https://api.weixin.qq.com/sns/userinfo?access_token=".$access_token1."&openid=".$openid."&lang=zh_CN";
        $uesr_data = $this->curl_get_contents($url1);
        $uesr_data = json_decode($uesr_data);
        $openid = $uesr_data -> openid;
        $nickname = $uesr_data -> nickname;
        $headimgurl = $uesr_data -> headimgurl;
        if(empty($openid)){
            $data['status'] ='error';
//            $data['message'] ='openid为空';
            return $data;
        }
        if(empty($nickname)){
            $nickname = 'wx_'.md5(time().mt_rand(1,999));
        }
//        $nickname_new = substr($nickname, 0, -25);
        $nickname_new = utf8_encode($nickname);
        $user_id = $this->db->getRow("SELECT `user_id` FROM `ecs_users` WHERE `openid` = '$openid'");

        //新的opne_id进行注册
        if (empty($user_id[ 'user_id'])) {
            $randpwd = time();
            $md5 = md5($randpwd);
            $sql = "insert into ecs_users (user_name,password,openid,reg_time,user_pic,wx_status) values ('".$nickname_new."','".$md5."','".$openid."','".time()."','".$headimgurl."','true')";

            $this->db->query($sql);
        }
        $sql = "SELECT eu.*,eur.rank_id,eur.rank_name from  ecs_users eu left join ecs_user_rank eur on eu.user_rank = rank_id where openid = '". $openid."'";
        $row = $this->db->getRow($sql);

        $response['member_id'] = $row['user_id'];
        $response['email'] = $row['email'];
        $response['uname'] = $row['user_name'];
        $response['member_lv_id'] = $row['rank_name'];
        $response['accesstoken'] = SESS_ID;
        $_SESSION['user_id']   = $row['user_id'];
        $_SESSION['user_name'] = $row['user_name'];
        $_SESSION['email']     = $row['email'];
        $data['status'] ='succ';
        $data['response'] = $response;
        $data['message'] ='登录成功';
        return $data;


    }





    //获取openid后跳转至get_openid方法
    function openid_url($params){
        $appid = $this->db->getRow("SELECT appid FROM `wxch_config` WHERE `id` = 1");
        $state = 'wechat';
        $scope = 'snsapi_base';
        $cfg_baseurl = 'http://'.$_SERVER['SERVER_NAME'];
        if ($params['user_id']) {
            $back_url = $cfg_baseurl.'/appwap/main.html?user_id='.$params['user_id'].'#/register_wxfx';
        }
        else{
            $back_url = $cfg_baseurl.'/appwap/main.html#/login';
        }

        $redirect_uri = urlencode($back_url);
        $oauth_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$appid['appid'].'&redirect_uri='.$redirect_uri.'&response_type=code&scope='.$scope.'&state='.$state.'#wechat_redirect';
        
            $data['url'] = $oauth_url;
            $data['status'] ='succ';
            $data['response'] = '';
            $data['message'] ='成功';
            return $data;
    }

    function access_token() 
    {
        $ret = $this->db->getRow("SELECT * FROM `wxch_config` WHERE `id` = 1");
        $appid = $ret['appid'];
        $appsecret = $ret['appsecret'];
        $access_token = $ret['access_token'];
        $dateline = $ret['dateline'];
        $time = time();
        if(($time - $dateline) >= 7200) 
        {
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$appsecret";
            $ret_json = $this->curl_get_contents($url);
            $ret = json_decode($ret_json);
            if($ret->access_token)
            {
                $this->db->getRow("UPDATE `wxch_config` SET `access_token` = '$ret->access_token',`dateline` = '$time' WHERE `id` =1;");
                return $ret->access_token;
            }
        }
        elseif(empty($access_token)) 
        {
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$appsecret";
            $ret_json = $this->curl_get_contents($url);
            $ret = json_decode($ret_json);
            if($ret->access_token)
            {
                $db->query("UPDATE `wxch_config` SET `access_token` = '$ret->access_token',`dateline` = '$time' WHERE `id` =1;");
                return $ret->access_token;
            }
        }
        else 
        {
            return $access_token;
        }
    }
    function emoji_encode($nickname){

        $nickname = preg_replace_callback(
            '/./u',
            function (array $match) {
                return strlen($match[0]) >= 4 ? '' : $match[0];
            },
            $nickname);

        if( !$nickname ){return '';}
        $nickname = json_encode($nickname);
        $nickname = preg_replace("#(\\\u(e|d)[0-9a-f]{3})#ie","addslashes('\\1')",$nickname);
        $nickname = json_decode($nickname);

        return $nickname;
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