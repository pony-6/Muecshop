<?php
include_once(dirname(__FILE__).'/../includes/cls_sms.php');
include_once(dirname(__FILE__).'/../includes/lib_order.php');
include_once(dirname(__FILE__).'/../includes/lib_clips.php');
include_once(dirname(__FILE__).'/../includes/lib_payment.php');
include_once(dirname(__FILE__).'/../includes/lib_transaction.php');

class apiclass{
    function __construct(){
        $this->_CFG = load_config();
        $cfg = $this->_CFG;
        global $db_host;
        global $db_user;
        global $db_pass;
        global $db_name;
        global $quiet;
        $this->db = new cls_mysql($db_host, $db_user, $db_pass, $db_name, $this->charset, NULL,  $quiet);
        $this->sms = new sms();
        $this->host_url = 'http://'.$_SERVER['SERVER_NAME'].'/';

    }
    
//    function check_token($member_id,$access_token){
//        $session = $this->db->getRow("SELECT userid, adminid, user_name, user_rank, discount, email, data, expiry FROM ecs_sessions WHERE sesskey = '" . $access_token . "' and userid = '".$member_id."'");
//        if($session){
//            return $session;
//        }
//        return false;
//    }

    /**
     * @Author:      dev@yin-duo.com
     * @DateTime:    2018-10-17 16:09:35
     * @Description: 取商品图片
     * @param:       goods_id img_size( all全部 s小图 m中图 l大图 ) img_count(first首张 all全部)
     */
    public function get_goods_images($goods_id, $img_size = 'all', $img_count = 'first')
    {
        if (!$goods_id) return false;

        if ($img_count == 'first') {
            switch ($img_size) {
                case 's':
                    $filter = " goods_thumb as s_url ";
                    break;
                case 'm':
                    $filter = " goods_img as m_url ";
                    break;
                case 'l':
                    $filter = " original_img as l_url ";
                    break;
                default:
                    $filter = " goods_thumb as s_url,goods_img as m_url,original_img as l_url ";
                    break;
            }
            $sql = 'select'.$filter.'from ecs_goods where goods_id = '.$goods_id;
        } else {
            switch ($img_size) {
                case 's':
                    $filter = " thumb_url as s_url ";
                    break;
                case 'm':
                    $filter = " img_url as m_url ";
                    break;
                case 'l':
                    $filter = " img_original as l_url ";
                    break;
                default:
                    $filter = " thumb_url as s_url,img_url as m_url,img_original as l_url ";
                    break;
            }
            $sql = 'select'.$filter.'from ecs_goods_gallery where goods_id = '.$goods_id;
        }
        $images = $this->db->getAll($sql);

        foreach ($images as $key => $value) {
            $image_list[$key] = array(
                'iamge_id' => $key,
                'l_url' => 'http://' . $_SERVER['HTTP_HOST'] . '/' . $value['l_url'],
                'm_url' => 'http://' . $_SERVER['HTTP_HOST'] . '/' . $value['m_url'],
                's_url' => 'http://' . $_SERVER['HTTP_HOST'] . '/' . $value['s_url'],
            );
        }
        return $image_list;
    }
    /*
     * @Author:      jinjiajin@yin-duo.com
     * @DateTime:    2018-10-17 17:11:46
     * @Description: 获取地址
     * @param:       $country_id,$province,$city,$district  国家(1)，省(1)，市(52)，区(502)
     * @example:     'area' => 'mainland:中国/上海/上海市/徐汇区:25',
                     'addr' => '中国上海上海市徐汇区',
     */
    public function region($country_id,$province_id,$city_id,$district_id)
    {
        //国家
        $country_sql = "select * from ecs_region where region_id = ".$country_id;
        $country = $this->db->getAll($country_sql);
        //省
        $province_sql = "select * from ecs_region where region_id = ".$province_id;
        $province = $this->db->getAll($province_sql);
        $data['area']='mainland:'.$country[0]['region_name'].'/'.$province[0]['region_name'].':'.$province_id;
        //市
        if (!empty($city_id)) {
            $city_sql = "select * from ecs_region where region_id = ".$city_id;
            $city = $this->db->getAll($city_sql);
            $data['area']='mainland:'.$country[0]['region_name'].'/'.$province[0]['region_name'].'/'.$city[0]['region_name'].':'.$city_id;
        }
        //区
        if (!empty($district_id)) {
            $district_sql = "select * from ecs_region where region_id = ".$district_id;
            $district = $this->db->getAll($district_sql);
            $data['area']='mainland:'.$country[0]['region_name'].'/'.$province[0]['region_name'].'/'.$city[0]['region_name'].'/'.$district[0]['region_name'].':'.$district_id;
        }
        $data['addr']=$country[0]['region_name'].$province[0]['region_name'].$city[0]['region_name'].$district[0]['region_name'];
        return $data;
    }

    /**
     * @Author:      dev@yin-duo.com
     * @Description: message翻译
     * @param:       $data 返回数据 $lang cn/en
     */
    public function langTransReturn($data, $lang)
    {
        $langConfig = array(
            'cn' => array(
                '更新成功' => '更新成功',
                '登录成功' => '登录成功',
                '登录失败' => '登录失败',
                '手机号码错误' => '手机号码错误',
                '手机号码已被注册' => '手机号码已被注册',
                '成功' => '成功',
                '两次输入密码不匹配' => '两次输入密码不匹配',
                '手机号码未注册'=>'手机号码未注册',
                '密码小于6位，请重新填写' => '密码小于6位，请重新填写',
                '用户名已被注册' => '用户名已被注册',
                '手机号码已被注册' => '手机号码已被注册',
                '邮箱已被注册' => '邮箱已被注册',
                '验证码错误，请重新填写' => '验证码错误，请重新填写',
                '手机号不存在' => '手机号不存在',
                '注册成功' => '注册成功',
                '注册成功' => '注册成功',
                '' => '',
            ),
            'en' => array(
                '更新成功' => 'update success',
                '登录成功' => 'login success',
                '登录失败' => 'Login failed',
                '手机号码错误' => 'Mobile number error',
                '手机号码已被注册' => 'Mobile number has been registered',
                '成功' => 'success',
                '两次输入密码不匹配' => 'Two passwords do not match',
                '手机号码未注册'=>'Mobile number is not registered',
                '密码小于6位，请重新填写' => 'The password is less than 6 digits, please re-fill',
                '用户名已被注册' => 'User name has been registered',
                '手机号码已被注册' => 'Mobile number has been registered',
                '邮箱已被注册' => 'The mailbox has been registered',
                '验证码错误，请重新填写' => 'Verification code error, please re-enter',
                '注册成功' => 'registration success',
                '手机号不存在' => 'Phone number does not exist',
                '手机号码未注册'=>'Mobile number is not registered',
                '' => '',
                '' => '',
                '' => '',
                '' => '',
                '' => '',
                '' => '',
                '' => '',
                '' => '',
                '' => '',
                '' => '',
                '' => '',
                '' => '',
                '' => '',
                '' => '',
                '' => '',
                '' => '',
                '' => '',
                '' => '',
                '' => '',
                '' => '',
                '' => '',
                '' => '',
                '' => '',
                '' => '',
                '' => '',
                '' => '',
                '' => '',
                '' => '',
                '' => '',
                '' => '',
                '' => '',
                '' => '',
                '' => '',
                '' => '',
                '' => '',
                '' => '',
                '' => '',
                '' => '',
                '' => '',
                '' => '',
                '' => '',
                '' => '',
                '' => '',
                '' => '',
                '' => '',
                '' => '',
                '' => '',
                '' => '',
                '' => '',
                '' => '',
                '' => '',
                '' => '',
                '' => '',
                '' => '',
                '' => '',
                '' => '',
                '' => '',
                '' => '',
                '' => '',
                '' => '',
                '' => '',
            ),
        );
        if (!empty($data['message'])) {
            $data['message'] = $langConfig[$lang][$data['message']];
        }
        return $data;
    }

}