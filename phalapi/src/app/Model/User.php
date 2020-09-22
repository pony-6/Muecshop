<?php

namespace  App\Model;

use PhalApi\Model\NotORMModel as NotORM;
use PhalApi\Model\Vcode as Vcode;
use PhalApi\Model\Rank as RankModel;


class User extends NotORM
{
    protected  function getTableName($id)
    {
        return 'users';
    }

    protected function getTableKey($table)
    {
        return 'user_id';
    }
    

    public function update_password($username,$password){
        $user_data =  $this->getORM()->where(array('mobile_phone' => $username))->fetchOne();
        if($user_data['ec_salt']){
            $user_password = md5(md5($password).$user_data['ec_salt']);
        }else{
            $user_password = md5($password);
        }
        $data['password'] = $user_password;
  
        
        $sql = "update ecs_users set password='".$data['password']."' where user_id='".$user_data['user_id']."'";
        $res = $this->getORM()->queryRows($sql);
        return $res;     
    }
    
    public function sms_login($username,$password){
        

        $user_data =  $this->getORM()->where(array('mobile_phone' => $username))->fetchOne();
        
        if(!$user_data){
            return false;
        }

        if($user_data['ec_salt']){
            $user_password = md5(md5($password).$user_data['ec_salt']);
        }else{
            $user_password = md5($password);
        }
        // var_dump($user_password);
        // var_dump($user_data['password']);

        if($user_password != $user_data['password']){
            return false;
        }

        return $user_data;
    }
    public function default_image(){
               $sql = "select * from ecs_app_config where k = 'default_image'";
                $data = $this->getORM()->queryRow($sql);
                return $data['val'];
    }

    public function GetRealyAddr($params){

        $url = 'https://apis.map.qq.com/ws/geocoder/v1/?location='.$params.'&key=K5EBZ-BS464-V3TUC-DKP4U-FSNG2-SHFA5&get_poi=0';
        $address = file_get_contents($url);
        echo $address;die;
//        return $address;

    }


    public function email_login($username,$password){

        $user_data =  $this->getORM()->where(array('email' => $username))->fetchOne();
        if(!$user_data){
            return false;
        }
        
        if($user_data['ec_salt']){
            $user_password = md5(md5($password).$user_data['ec_salt']);
        }else{
            $user_password = md5($password);
        }
        if($user_password != $user_data['password']){
            return false;
        }
        
        return $user_data;
    }

    public function username_login($username,$password){
        $user_data =  $this->getORM()->where(array('user_name' => $username))->fetchOne();
//        var_dump($user_data);
        if(!$user_data){
            return false;
        }
        
        if($user_data['ec_salt']){
            $user_password = md5(md5($password).$user_data['ec_salt']);
        }else{
            $user_password = md5($password);
        }
        if($user_password != $user_data['password']){
            return false;
        }

        return $user_data;
    }

    public function mobile_register($user_name,$password,$platform,$parent_id){
        if($email == ''){
            $email = $user_name.'@mail';
        }

        if($parent_id == '')
        {
            $data = array(
                'email' => $email,
                'user_name' => $user_name,
                'password' => md5($password),
                'mobile_phone' => $user_name,
                'reg_time'=>time(),
                'alias'=>'',
                'msn'=>'',
                'qq'=>'',
                'office_phone'=>'',
                'home_phone'=>'',
                'credit_line'=>'0',
                'platform'=>$platform,

            );
        }

        else{

            $data = array(
                'email' => $email,
                'user_name' => $user_name,
                'password' => md5($password),
                'mobile_phone' => $user_name,
                'reg_time'=>time(),
                'alias'=>'',
                'msn'=>'',
                'qq'=>'',
//                'birthday'=>'',
                'office_phone'=>'',
                'home_phone'=>'',
                'credit_line'=>'0',
                'platform'=>$platform,
                'parent_id'=>'0'
            );
        }
        if($parent_id == '')
            $sql = "insert into ecs_users (email,user_name,password,platform,alias,msn,qq,office_phone,home_phone,mobile_phone,credit_line,reg_time) values ('".$email."','".$user_name."','".md5($password)."','".$platform."','','','','','','','0','".time()."')";
        else
            $sql = "insert into ecs_users (email,user_name,password,platform,alias,msn,qq,office_phone,home_phone,mobile_phone,credit_line,reg_time,parent_id) values ('".$email."','".$user_name."','".md5($password)."','".$platform."','','','','','','','0','".time()."',$parent_id)";

        $orm = $this->getORM();
        $orm->query($sql);
//        $status = $orm->insert($data);

        $user_id = $orm->insert_id();


        if($parent_id!=''){
            $sql = "select user_name from ecs_users where user_id = $parent_id";
            $res = $this->getORM()->queryAll($sql);
//            var_dump($res);
            $sql = "insert into ecs_user_recommend (user_id,user_name,parent_id,recommend) values ($user_id,$user_name,$parent_id,'{$res[0]['user_name']}')";
//            var_dump($sql);exit;
            $this->getORM()->query($sql);
        }

        $user_data =  $this->getORM()->where(array('user_id' => $user_id))->fetchOne();
        return $user_data;
    }
    
    
    
    public function get_rank_discount($user_id){
        $sql = "select discount,user_rank from ecs_users eu left join ecs_user_rank eur on eu.user_rank = eur.rank_id where eu.user_id = '".$user_id."'";
        $member_data = $this->getORM()->queryRow($sql);
        if($member_data['member_data'] == ''){
            $discount  = 100;
        }else{
            $discount  = $member_data['discount'];
        }
        $discount = $discount/100;
        $return = array(
            'discount'=>$discount,
            'user_rank'=>$member_data['user_rank']
        );
        return $return;
    }
    

    public function getAddressList($user_id)
    {
        $sql = "select * from ecs_user_address where user_id ={$user_id}";
        $data = $this->getORM()->queryAll($sql);
        return $data;
    }
    
    public function check_username($user_name){
        $sql = "select * from ecs_users where user_name='".$user_name."' or mobile_phone = '".$user_name."'";
        $data = $this->getORM()->queryRow($sql);
        return $data;
    }
    public function memberinfoSave($user_id,$birthdaytime,$sex){

        $sql = "update ecs_users set birthday ='".$birthdaytime."' , sex = '".$sex."' where user_id ='".$user_id."'";
        $this->getORM()->queryRow($sql);

        return true;
    }

    public function get_UserFeedback($user_id,$conent,$title){
        $sql = "select * from  ecs_users where user_id ='".$user_id."'";
        $user_data = $this->getORM()->queryRow($sql);
        $sql = "insert  into ecs_feedback (parent_id, user_id, user_name, user_email, msg_title, msg_type, msg_status,  msg_content, msg_time) values (0,'".$user_id."','".$user_data['user_name']."','".$user_data['email']."','".$title."','1','0','".$conent."','".time()."')";
        $this->getORM()->queryRow($sql);
        return true;
    }

    public function get_user($user_id){
        $user_data =  $this->getORM()->where(array('user_id' => $user_id))->fetchOne();
        $user_data['user_name'] = urldecode($user_data['user_name']);
        return $user_data;
    }

    public function get_user_info($user_id){

        $user_data =  $this->getORM()->where(array('user_id' => $user_id))->fetchOne();
        if(!$user_data){
            return false;
        }
        
        $sql = "select * from ecs_user_rank where rank_id = '".$user_data['user_rank']."'";
        $user_rank = $this->getORM()->queryRow($sql);

        $thistime = time();
        $sql = "select count(bonus_id) as sum from ecs_user_bonus eub left join ecs_bonus_type ebt on eub.bonus_type_id = ebt.type_id where user_id = '".$user_id."' and order_id = '0' and use_end_date > '".$thistime."' ";
        $bonus = $this->getORM()->queryRow($sql);

        // 获得可用红包的数量
//        $thistime = time();
//        $sql = "select count(bonus_id) as num from ecs_user_bonus eub left join ecs_bonus_type ebt on eub.bonus_type_id = ebt.type_id where user_id = '".$user_id."' and order_id = '0'  and use_end_date > '".$thistime."' and use_start_date <= '".$thistime."'  ";
//        $bonus_data = $this->getORM()->queryRow($sql);

        $return = array(
            'advance'=>$user_data['user_money'],
            'user_money'=>$user_data['user_money'],
            'frozen_money'=>$user_data['frozen_money'],
            'email'=>$user_data['email'],
            'point'=>$user_data['pay_points'],
            'couponNum'=>$bonus['sum'],
            'user_rank'=>$user_rank['rank_name'],
            'mobile'=>$user_data['mobile_phone'],
        );
        
        return $return;
    }


    function log_account_change($user_id, $user_money = 0, $frozen_money = 0, $rank_points = 0, $pay_points = 0, $change_desc = '', $change_type = ACT_OTHER)
    {
        /* 插入帐户变动记录 */
        $account_log = array(
            'user_id'       => $user_id,
            'user_money'    => $user_money,
            'frozen_money'  => $frozen_money,
            'rank_points'   => $rank_points,
            'pay_points'    => $pay_points,
            'change_time'   => gmtime(),
            'change_desc'   => $change_desc,
            'change_type'   => $change_type
        );
        $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('account_log'), $account_log, 'INSERT');

        /* 更新用户信息 */
        $sql = "UPDATE " . $GLOBALS['ecs']->table('users') .
            " SET user_money = user_money + ('$user_money')," .
            " frozen_money = frozen_money + ('$frozen_money')," .
            " rank_points = rank_points + ('$rank_points')," .
            " pay_points = pay_points + ('$pay_points')" .
            " WHERE user_id = '$user_id' LIMIT 1";
        $GLOBALS['db']->query($sql);
    }


    public function getUserRank($user_id)
    {
        // 获取当前会员的等级
        $sql = "SELECT rank_points FROM ecs_users WHERE user_id={$user_id}";
        $rank = $this->getORM()->queryRow($sql);
        // $sql = "SELECT rank_name,max_points FROM ecs_user_rank WHERE ".$rank['rank_points']." BETWEEN min_points AND max_points";
        // 获取当前的等级及下一等级
        $sql = "SELECT a.rank_name,a.max_points,b.rank_name AS next_rank_name FROM ecs_user_rank AS a LEFT JOIN ecs_user_rank AS b ON a.max_points = b.min_points WHERE ".$rank['rank_points']." BETWEEN a.min_points AND a.max_points";
        $rank_name = $this->getORM()->queryRow($sql);
        $next_lv = $rank_name['max_points'] - $rank['rank_points'];  // 距离下个等级的积分
        return ['rank_name'=>$rank_name['rank_name'],'next_lv'=>$next_lv,'next_rank_name'=>$rank_name['next_rank_name']];
        return $rank_name;
        return $rank_name[0]['rank_name'];
    }

    //查询其它页logo
    public function logo_other(){
        $sql = "select value from ecs_shop_config where code = 'shop_other'";
        $result = $this->getORM()->queryRow($sql);
        return empty($result['value']) ? "https://imgt1.oss-cn-shanghai.aliyuncs.com/ecAllRes/images/logo.png" : goods_img_url($result['value']);
    }

    public function kefu_tel(){
        $sql = "select val from ecs_app_config where k = 'kefu_tel'";
        $result = $this->getORM()->queryRow($sql);
        return $result;
    }

    public function getDeposit($user_id)
    {
        // 我的钱包显示当前余额
        $sql = "SELECT user_money FROM ecs_users WHERE user_id = {$user_id}";
        $data = $this->getORM()->queryRows($sql);
        return $data[0]['user_money'];
    }

    public function getCollectListAction($page,$user_id)
    {
        // 我的收藏
        $page_size = getConfigPageSize();

        $offset = ($page - 1) * $page_size;
        $sql = "SELECT a.goods_id AS id,b.goods_name AS name,b.shop_price AS retail_price,b.goods_id,b.goods_thumb AS list_pic_url
                FROM ecs_collect_goods AS a
                LEFT JOIN ecs_goods AS b ON a.goods_id = b.goods_id
                WHERE a.user_id = {$user_id} LIMIT $offset,$page_size";
//        var_dump($sql);die;
        $data = $this->getORM()->queryRows($sql);
        foreach($data as $k => $v){
            $data[$k]['list_pic_url'] = goods_img_url($data[$k]['list_pic_url']);
        }
        $sql = "SELECT count(a.goods_id) as sum FROM ecs_collect_goods AS a
                LEFT JOIN ecs_goods AS b ON a.goods_id = b.goods_id
                WHERE a.user_id = {$user_id}";
        $sum= $this->getORM()->queryRow($sql);
        $return['pagetotal'] = $this->pageTotal($sql,$page_size);
        $return['data'] = $data;

        return $return;
    }
    
    public function getAdvanceListAction($user_id,$page)
    {
        // 余额变动记录
        $return = array();
        $page_size = pagesize();
        $offset = ($page - 1) * $page_size;

        $sql = "select * from ecs_account_log where user_id = $user_id and (user_money != 0 or frozen_money != 0) order by log_id desc limit $offset,$page_size";
//        $sql = "SELECT * FROM ecs_account_log WHERE user_id = {$user_id} AND rank_points = 0 AND pay_points = 0 ORDER BY log_id DESC LIMIT {$offset},{$page_size} ";
        $data = $this->getORM()->queryRows($sql);

        foreach($data as $k => $v){
            $data[$k]['do_money'] = $data[$k]['user_money'];
            $data[$k]['mtime'] = date("Y-m-d H:i:s",$data[$k]['change_time']);
            $data[$k]['message'] = $data[$k]['change_desc'];
        }
        $sql = "SELECT COUNT(*) AS num FROM ecs_account_log WHERE user_id = {$user_id} AND rank_points = 0 AND pay_points = 0";
        $total = $this->getORM()->queryRow($sql);
        $return['data'] = $data;
        $return['count'] = $total['num'];
//        $return['pagetotal'] = $this->pageTotal($sql,$page_size);
        $return['pagetotal'] = ceil($total['num']/$page_size);

        return $return;
    }

    public function getPointList($user_id,$page)
    {
        // 积分变动记录
        $return = array();
        $page_size = pagesize();
        $offset = ($page - 1) * $page_size;

        $sql = "SELECT * FROM ecs_account_log WHERE user_id = {$user_id} AND user_money = '0' AND frozen_money = '0' ORDER BY log_id DESC LIMIT {$offset},{$page_size} ";
        $data = $this->getORM()->queryRows($sql);
        foreach($data as $k => $v){
            $data[$k]['change_point'] = $data[$k]['pay_points'];
            $data[$k]['addtime'] = date("Y-m-d H:i:s",$data[$k]['change_time']);
            $data[$k]['reason'] = $data[$k]['change_desc'];
        }
        $sql = "SELECT COUNT(*) as sum FROM ecs_account_log WHERE user_id = {$user_id} AND user_money = '0' AND frozen_money = '0'";
        $sum= $this->getORM()->queryRow($sql);
        $return['data'] = $data;
        $return['a']= $sum['sum'];
        $return['pagetotal'] = $this->pageTotal($sql,$page_size);

        return $return;
    }
    public  function GetPromoteApi($user_id){

        $data = \PhalApi\DI()->config->get('app');
        $return['url'] = $data['host_url']."h5/apiPam/register/main?parent_id=".$user_id;
        $return['code_url'] = 'http://qr.liantu.com/api.php?text='.urlencode($return['url']);

        return  $return;
    }

    public  function  SendBonus($user_id,$bonus_id){

        $sql = "SELECT * FROM ecs_bonus_type WHERE type_id = '".$bonus_id."'";
        $bonus_type = $this->getORM()->queryRow($sql);
        $sql ="select count(user_id) as count from   ecs_user_bonus where user_id='".$user_id."' and bonus_type_id='".$bonus_id."'";
        $count = $this->getORM()->queryRow($sql);
        if($count['count']>=1){
            return array("res"=>false,"msg"=>"你已领取该红包");
        }
        if(empty($bonus_type)){
            return array("res"=>false,"msg"=>"领取失败");
        }
        if($bonus_type['send_start_date']>time()){
            return array("res"=>false,"msg"=>"该红包还未开始领取");
        }
        if($bonus_type['send_end_date']< time()){
            return array("res"=>false,"msg"=>"该红包领取结束");
        }
        /* 向会员红包表录入数据 */
        $sql = "INSERT INTO ecs_user_bonus (bonus_type_id, bonus_sn, user_id, used_time, order_id, emailed) VALUES ('$bonus_id', 0, '$user_id', 0, 0,1)";
        $da =  $this->getORM()->query($sql);
        return array("res"=>true,"msg"=>"领取成功");
    }
    function pageTotal($sql,$page_size)
    {
        /**
         * 返回总页数
         * 页数别名 num
         */
        $page_total = $this->getORM()->queryRows($sql);
        return ceil($page_total[0]['sum'] / $page_size);
    }

    public function vcodeLogin($mobile)
    {
        // 短信验证码登录
        $user_data =  $this->getORM()->where(array('mobile_phone' => $mobile))->fetchOne();
        
        if(!$user_data)
        {
            return false;
        }
        return $user_data;
    }
    
    public function getAviBonusList($user_id,$page,$status){
        $thistime = time();


        if($status == 'showinvalid'){
            $sql = "select * from ecs_user_bonus eub left join ecs_bonus_type ebt on eub.bonus_type_id = ebt.type_id where user_id = '".$user_id."' and (order_id > '0' OR use_end_date < '".$thistime."') ";
            
            $data = $this->getORM()->queryRows($sql);

            foreach($data as $key=>$item){
                $data[$key]['use_end_date'] = date('Y-m-d',$item['use_end_date']);
                $data[$key]['use_start_date'] = date('Y-m-d',$item['use_start_date']);
            }

        }else{

            $sql = "select * from ecs_user_bonus eub left join ecs_bonus_type ebt on eub.bonus_type_id = ebt.type_id where user_id = '".$user_id."' and order_id = '0' and use_end_date > '".$thistime."' ";
            $data = $this->getORM()->queryRows($sql);
            foreach($data as $key=>$item){
                $data[$key]['use_end_date'] = date('Y-m-d',$item['use_end_date']);
                $data[$key]['use_start_date'] = date('Y-m-d',$item['use_start_date']);
            }

        }

        $sql = "select count(bonus_id) as sum from ecs_user_bonus eub left join ecs_bonus_type ebt on eub.bonus_type_id = ebt.type_id where user_id = '".$user_id."' and order_id = '0' and use_end_date > '".$thistime."' ";

        $bonus_Available = $this->getORM()->queryRow($sql);

        $sql = "select count(bonus_id) as sum from ecs_user_bonus eub left join ecs_bonus_type ebt on eub.bonus_type_id = ebt.type_id where user_id = '".$user_id."' and order_id > '0' OR use_end_date < '".$thistime."' ";

        $bonus_Invalid = $this->getORM()->queryRow($sql);

        $return['bonus'] = $data;
        $return['showinvalid'] = $bonus_Invalid['sum']; //失效数量
        $return['showvalid'] = $bonus_Available['sum'];//可用数量
        return $return;
        
    }

    public function checkMobile($user_id,$mobile){
        // 检测修改手机号时id与手机号是否照应

        $sql = "SELECT * FROM ecs_users WHERE user_id = ".$user_id." AND mobile_phone = ".$mobile." ";
        $res = $this->getORM()->queryRow($sql);
        if(empty($res) || $res == ''){
            return false;
        }
        return true;

    }

    public function securityphoneUpdateBefore($mobile,$vcode){
        // 更改手机号第一步  验证身份
        $check_time = time()-3600;

        $sql = "SELECT * FROM ecs_vcode WHERE mobile = ".$mobile." AND vcode = ".$vcode." AND add_time >'".$check_time."'";
        $res = $this->getORM()->queryRow($sql);
        if(empty($res) || $res == ''){
            return false;
        }

        return true;
    }

    public function securityphoneUpdateAfter($mobile,$vcode,$user_id){
        // 更改手机号第二步 更换手机号
        $check_time = time()-3600;

        $sql = "SELECT * FROM ecs_vcode WHERE mobile = ".$mobile." AND vcode = ".$vcode." AND add_time >'".$check_time."'";
        $res = $this->getORM()->queryRow($sql);
        if(empty($res) || $res == ''){
            return false;
        }

        $sql = "UPDATE ecs_users SET `user_name` = '".$mobile."',mobile_phone = ".$mobile." WHERE user_id = ".$user_id."";  // 修改手机号的同时修改用户名
        $this->getORM()->queryRow($sql);
        return true;
    }

    public function securitypwdUpdate($mobile,$vcode,$user_id,$password){
        // 修改登录密码
        $check_time = time()-3600;

        $sql = "SELECT * FROM ecs_vcode WHERE mobile = ".$mobile." AND vcode = ".$vcode." AND add_time >'".$check_time."'";
        $res = $this->getORM()->queryRow($sql);
        if(empty($res) || $res == ''){
            return false;
        }

        $sql = "SELECT ec_salt FROM ecs_users WHERE user_id = ".$user_id."";
        $user_data = $this->getORM()->queryRow($sql);

        if(empty($user_data['ec_salt']) || $user_data['ec_salt'] == ''){
            $new_password = md5($password);
        }else{
            $new_password = md5(md5($password).$user_data['ec_salt']);
        }

        $sql = "update ecs_users set password='".$new_password."' where user_id='".$user_id."'";
        $this->getORM()->queryRow($sql);
        
        return true;
    }

    public function checkWechatReg($openid,$platform)
    {
        // 检测该微信是否注册
        if($platform == 'MP-WEIXIN'){   // 微信小程序登录
            $sql = "select * from ecs_users where openid_mp = '".$openid."'";
        }if($platform == 'APP-PLUS'){
            $sql = "select * from ecs_users where openid = '".$openid."'";
        }
        else{
            $sql = "select * from ecs_users where openid_h5 = '".$openid."'";
        }

        $res = $this->getORM()->queryRow($sql);

        if($res){
            return array("res"=>true,"msg"=>"该微信已注册");
        }
        return array("res"=>false,"msg"=>"该微信尚未注册");
    }

    public function wechatLogin($openid,$platform = '')
    {
        // 通过openid来获取用户的信息
        if($platform == 'MP-WEIXIN'){
            $user_data = $this->getORM()->where(array("openid_mp" => $openid))->fetchOne();
        }if($platform == 'APP-PLUS'){
            $user_data =  $this->getORM()->where(array('openid' => $openid))->fetchOne();
        }
        if($platform == 'H5'){
            $user_data =  $this->getORM()->where(array('openid_h5' => $openid))->fetchOne();
        }
        
        if(!$user_data)
        {
            return false;
        }
        return $user_data;
    }

    public function bindWechat($mobile,$vcode,$openid,$type,$platform)
    {
        // 绑定或注册微信
        $check_time = time()-3600;

        $sql = "SELECT * FROM ecs_vcode WHERE mobile = ".$mobile." AND vcode = ".$vcode." AND add_time >'".$check_time."'";
        $res = $this->getORM()->queryRow($sql);
        if(empty($res) || $res == ''){   // 验证不通过
            return false;
        }
        if($type == "bind")
        {
            // 绑定微信
            if($platform == 'MP-WEIXIN'){
                $sql = "update ecs_users set openid_mp = '".$openid."' where mobile_phone = '".$mobile."'";
            }else{
                $sql = "update ecs_users set openid = '".$openid."' where mobile_phone = '".$mobile."'";
            }
            
            $this->getORM()->queryRow($sql);  // 进行绑定
            return true;
        }
        elseif($type == 'reg')
        {
            // 进行注册操作
            if($email == ''){
                $email = $mobile.'@mail';
            }
            if($platform == 'MP-WEIXIN'){
                $data = array(
                    'email' => $email, 
                    'user_name' => $mobile,
                    'password' => md5(rand(100000,999999)),
                    'mobile_phone' => $mobile,
                    'reg_time'=>time(),
                    'alias'=>'',
                    'msn'=>'',
                    'qq'=>'',
                    'office_phone'=>'',
                    'home_phone'=>'',
                    'credit_line'=>'0',
                    'openid_mp' => $openid
                );                
            }else{
                $data = array(
                    'email' => $email, 
                    'user_name' => $mobile,
                    'password' => md5(rand(100000,999999)),
                    'mobile_phone' => $mobile,
                    'reg_time'=>time(),
                    'alias'=>'',
                    'msn'=>'',
                    'qq'=>'',
                    'office_phone'=>'',
                    'home_phone'=>'',
                    'credit_line'=>'0',
                    'openid' => $openid
                );
            }

            
            $orm = $this->getORM();
            $orm->insert($data);
            
            $user_id = $orm->insert_id();
            $user_data =  $this->getORM()->where(array('user_id' => $user_id))->fetchOne();
            return $user_data;
        }

    }

    public function deleteCoupon($user_id,$coupon_id){
        $sql = "delete from ecs_user_bonus where bonus_id = '$coupon_id' and user_id = '$user_id'";
        $k = $this->getORM()->query($sql);
        return $k;
    }

    public function bindWXH5($nickname,$openid,$randpwd){
        $password = md5(rand(100000,999999));
        $nickname = urlencode($nickname);
        $sql = "select * from ecs_users where user_name = '$nickname'";
        $data = $this->getORM()->queryRow($sql);
        if($data){
            $sql = "update ecs_users set openid_h5 = '$openid' where user_name = '$nickname'";
            $status = $this->getORM()->query($sql);
        }
        else{
            $sql = "insert into ecs_users (user_name,password,reg_time,openid_h5,alias,msn,qq,office_phone,home_phone,credit_line,mobile_phone) values('$nickname','$password','$randpwd','$openid','','','','','','0','')";
            $status = $this->getORM()->query($sql);
        }


        return $status;
    }

    /**
     * emoji_decode
     *
     * @param mixed $nicknamei 微信昵称
     * @access public
     * @return 转码之后的emoji表情
     */
    public function emoji_decode($nickname){
        if( !$nickname ){return '';}
        $nickname_json =  '{"nickname":"' . $nickname . '"}';
        $arr = json_decode($nickname_json,true);
        $nickname = $arr['nickname'];

        return $nickname;
    }

    /**
     * emoji_encode
     *
     * @param mixed $nickname 微信昵称
     * @access public
     * @return 编码之后 的emoji表情
     */
    public function emoji_encode($nickname){
        if( !$nickname ){return '';}
        $nickname = json_encode($nickname);
        $nickname = preg_replace("#(\\\u(e|d)[0-9a-f]{3})#ie","addslashes('\\1')",$nickname);
        $nickname = json_decode($nickname);

        return $nickname;
    }


    public function get_user_visit($goods_id,$user_id,$platform){
//        var_dump($platform);exit;
        $sql = "select * from ecs_user_visit_log where user_id = $user_id and goods_id = $goods_id";
        $res = $this->getORM()->queryAll($sql);
        if ($res){
            $sql = "update ecs_user_visit_log set hitCounts = hitCounts+1 where user_id = $user_id and goods_id = $goods_id";
        }else{
            $sql = "insert into ecs_user_visit_log (user_id,goods_id,hitCounts,addTime,platform) values ($user_id,$goods_id,1,NOW(),'"."$platform')";
        }
//        var_dump($sql);exit;
        $this->getORM()->queryAll($sql);
        $num = 0;
//        var_dump($str);exit;

        $sql = "select * from ecs_user_visit_log where goods_id = $goods_id";
        $data = $this->getORM()->queryAll($sql);
        foreach ($data as $row)
        {
            $num += $row['hitCounts'];
        }

        return $num;
    }

    public function activeBonus($user_id,$bonus_id){
        /* 查询红包序列号是否已经存在 */
        $sql = "SELECT bonus_id, bonus_sn, user_id, bonus_type_id FROM ecs_user_bonus where bonus_sn = $bonus_id";
        $row = $this->getORM()->queryRow($sql);
        if ($row)
        {
            if ($row['user_id'] == 0)
            {
                //红包没有被使用
                $sql = "SELECT send_end_date, use_end_date ".
                    " FROM ecs_bonus_type" .
                    " WHERE type_id = '" . $row['bonus_type_id'] . "'";

                $bonus_time = $this->getORM()->queryRow($sql);

                $now = time();
                if ($now > $bonus_time['use_end_date'])
                {
                    //使用超时
                    return false;
                }

                $sql = "UPDATE ecs_user_bonus SET user_id = '$user_id' ".
                    "WHERE bonus_id = '$row[bonus_id]'";
                $result = $this->getORM()->query($sql);
                if ($result)
                {
                    //更新成功
                    return true;
                }
                else
                {
                    //更新失败
                    return false;
                }
            }
            else
            {
                //已经被别人占用
                return false;
            }
        }
        else
        {
            //不存在这个红包
            return false;
        }
    }

    //获取系统配置
    public function getConfig(){
        $sql = "select * from ecs_config where code = 'wechat.web'";
        $data = $this->getORM()->queryRow($sql);
        $data = json_decode($data['config'],true);
        return $data;
    }

}