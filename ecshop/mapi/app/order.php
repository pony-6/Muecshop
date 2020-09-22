<?php

class order extends apiclass
{
    // 购物车进入
    public function get_cart_init($params)
    {
        $sql = "update " . $GLOBALS['ecs']->table('cart')." set is_check = 'true'  WHERE session_id = '" . SESS_ID . "'";
        $this->db->query($sql);

        $data = $this->get_cart_desc($params['member_id']);

        return array(
            'status' => 'succ',
            'message' => '',
            'response' => $data
        );
    }

    // 购物车商品数量变更
    public function update_cart($params)
    {
        if (!is_numeric($params['num']) || intval($params['num']) <= 0)
        {
            return array(
                'status' => 'fail',
                'message' => '数量有误',
                'response' => ''
            );
        }
        $num = $params['num'];
        $goods_id = $params['goods_id'];
        //更新购物车中的商品数量
        $sql = "UPDATE " .$GLOBALS['ecs']->table('cart')." SET goods_number = '$num' WHERE goods_id='$goods_id' AND session_id='" . SESS_ID . "'";
        $this->db->getRow($sql);

        $data = $this->get_cart_desc($params['member_id']);

        return array(
            'status' => 'succ',
            'message' => '更新成功',
            'response' => $data
        ); 
    }
    
    // 勾选
    public function update_cart_checked($params){
        if (!empty($params['check_type'])) {
            if ($params['check_type'] == 'product' && $params['is_product'] == 'true') {
                $sql = "UPDATE " .$GLOBALS['ecs']->table('cart')." SET is_check = 'true' WHERE g_type = '0' AND session_id='" . SESS_ID . "'";
                $product_check = 'true';
            } elseif ($params['check_type'] == 'product' && $params['is_product'] == 'false') {
                $sql = "UPDATE " .$GLOBALS['ecs']->table('cart')." SET is_check = 'false' WHERE g_type = '0' AND session_id='" . SESS_ID . "'";
                $product_check = 'false';
            }
            if ($params['check_type'] == 'virtual' && $params['is_virtual'] == 'true') {
                $sql = "UPDATE " .$GLOBALS['ecs']->table('cart')." SET is_check = 'true' WHERE g_type = '1' AND session_id='" . SESS_ID . "'";
                $virtual_check = 'true';
            } elseif ($params['check_type'] == 'virtual' && $params['is_virtual'] == 'false') {
                $sql = "UPDATE " .$GLOBALS['ecs']->table('cart')." SET is_check = 'false' WHERE g_type = '1' AND session_id='" . SESS_ID . "'";
                $virtual_check = 'false';
            }
            $this->db->getRow($sql);
        } else {
            $sql = "select is_check from " .$GLOBALS['ecs']->table('cart')." WHERE goods_id='".$params['goods_id']."' AND session_id='" . SESS_ID . "'";
            $is_check = $this->db->getRow($sql);
            if ($is_check['is_check'] == 'true') {
                $sql = "UPDATE " .$GLOBALS['ecs']->table('cart')." SET is_check = 'false' WHERE goods_id='".$params['goods_id']."' AND session_id='" . SESS_ID . "'";
            } else {
                $sql = "UPDATE " .$GLOBALS['ecs']->table('cart')." SET is_check = 'true' WHERE goods_id='".$params['goods_id']."' AND session_id='" . SESS_ID . "'";
            }
            $this->db->getRow($sql);
        }

        $data = $this->get_cart_desc($params['member_id']);
        return array(
            'status' => 'succ',
            'message' => '修改成功',
            'response' => $data
        ); 
    }

    public function get_cart_desc($member_id)
    {
        // 取出购物车数据
        $object_sql = "select * from ecs_cart where session_id = '".SESS_ID."' and rec_type = ".CART_GENERAL_GOODS;
        $cart_info = $GLOBALS['db']->GetAll($object_sql);

        foreach ($cart_info as $key => $value) {
            $pic = $this->get_goods_images($value['goods_id']);

            $store_real_sql = "select goods_number from ecs_goods where goods_id = " . $value['goods_id'];
            $store_real = $GLOBALS['db']->GetRow($store_real_sql);

            $goods_info[] = array(
                'obj_ident' => 'goods_'.$value['goods_id'].'_'.$value['goods_id'],
                'obj_type' => 'goods',
                'goods_id' => $value['goods_id'],
                'product_id' => $value['goods_id'],
                'name' => $value['goods_name'],
                'spec_info' => $value['goods_attr'],
                'store_real' => $store_real['goods_number'],
                'quantity' => (int)$value['goods_number'],
                'price' => $value['goods_price'],
                'discount_price' => '0',
                'total_price' => bcmul($value['goods_number'], $value['goods_price'], 3),
                'score' => '0',
                'pic' => $pic['0']['s_url'],
                'gift' => '',
                'promotion' => '',
                'is_check' => $value['is_check'],
//                'g_type' => $value['g_type']            //是否是积分商品
            );
//            if ($value['g_type'] == '1') {  // 判断是否有积分商品
//                $has_points = 'true';
//            }
//            if ($value['g_type'] == '0') {  // 判断是否有实体商品
//                $has_product = 'true';
//            }
        }

        $object['goods'] = $goods_info;
        $object['gift'] = array();
        $object['coupon'] = array();

        $sql = 'SELECT SUM(goods_number) AS number, SUM(goods_price * goods_number) AS amount,g_type' .
               ' FROM ' . $GLOBALS['ecs']->table('cart') .
               " WHERE is_check='true' AND session_id = '" . SESS_ID . "' AND rec_type = '" . CART_GENERAL_GOODS . "'";
        $cart_data = $GLOBALS['db']->GetRow($sql);
        $count_sql = "select count(*) as count from ecs_cart where is_check='true' AND session_id = '".SESS_ID."' and rec_type = ".CART_GENERAL_GOODS;
        $goods_count = $GLOBALS['db']->GetRow($count_sql);

        $sql = 'SELECT SUM(goods_number) AS number' .
               ' FROM ' . $GLOBALS['ecs']->table('cart') .
               " WHERE session_id = '" . SESS_ID . "' AND rec_type = '" . CART_GENERAL_GOODS . "'";
        $all_num = $GLOBALS['db']->GetRow($sql);

        $data = array(
            'cart_total'=>$all_num['number'] ? $all_num['number'] : '0',
            'check_nums'=>$cart_data['number'] ? $cart_data['number'] : '0',
            'cart_count'=>$goods_count['count'],
            'subtotal_goods_price'=>$cart_data['amount'],   // 总价
            'subtotal_discount_amount'=>'',                 // 折扣
            'subtotal_gain_score'=>'',
            'subtotal_price'=>$cart_data['amount'],
            'object'=>$object,
            'cart_promotion_display' => true,
            'order_promotion' => array(),
//            'g_type'=>$cart_data['g_type'],
//            'has_points' => $has_points,
//            'has_product' => $has_product,
        );
        return $data;
    }

    public function get_cart_total($params)
    {
        $sql = 'SELECT SUM(goods_number) AS number, SUM(goods_price * goods_number) AS amount' .
            ' FROM ' . $GLOBALS['ecs']->table('cart') .
            " WHERE session_id = '" . SESS_ID . "' AND rec_type = '" . CART_GENERAL_GOODS . "'";
        $row = $GLOBALS['db']->GetRow($sql);
        $data = array('cart_total' => $row['number']);
        return array(
            'status' => 'succ',
            'message' => '',
            'response' => $data
        );
    }

    public function add_cart($params)
    {
        /* 检查：商品数量是否合法 */
        if (!is_numeric($params['num']) || intval($params['num']) <= 0) {
            return array(
                'status' => 'fail',
                'message' => '数量有误',
                'response' => ''
            );
        }
        
        if($params['member_id']){
            $_SESSION['user_id'] = $params['member_id'];
        }
        $spec_sql = "SELECT g.goods_attr_id FROM ecs_goods_attr AS g LEFT JOIN ecs_attribute AS a ON a.attr_id = g.attr_id WHERE a.attr_type = 1 AND g.goods_id = ".$params['goods_id'];
        $spec = $this->db->getRow($spec_sql);
        $params['spec'][] = $spec['goods_attr_id'];
        addto_cart($params['goods_id'], $params['num'], $params['spec'], 0);

        return array(
            'status' => 'succ',
            'message' => '添加成功',
            'response' => ''
        );
    }

    public function remove_cart($params)
    {
        if ($params['remove_all'] == 'true') {
            $sql = "DELETE FROM " . $GLOBALS['ecs']->table('cart') . " WHERE session_id = '" . SESS_ID . "' ";
        } else {
            $goods_id = $params['goods_id'];
            $sql = "DELETE FROM " . $GLOBALS['ecs']->table('cart') . " WHERE session_id = '" . SESS_ID . "' " . "AND goods_id = '" . $goods_id ."'";
        }
        $this->db->getRow($sql);

        return array(
            'status' => 'succ',
            'message' => '更新成功',
            'response' => ''
        ); 
    }

    // 购物车提交
    public function get_cart_checkout($params)
    {
        $res = array(
            'status' => 'fail',
            'response' => array(),
            'message' => '不能买',
        );

        /**
            获取选择购物车的商品
        **/
        $sql = 'SELECT SUM(goods_number) AS number, SUM(goods_price * goods_number) AS amount' .
               ' FROM ' . $GLOBALS['ecs']->table('cart') .
               " WHERE is_check='true' and session_id = '" . SESS_ID . "' AND rec_type = '" . CART_GENERAL_GOODS . "' and is_check='true'";
        $row = $GLOBALS['db']->GetRow($sql);
        if (empty($row['number'])) {
            return array('status' => 'fail', 'message' => '商品数量不能为空', 'data' => '');
        }

        $count_sql = "select count(*) as count from ecs_cart where session_id = '".SESS_ID."' and rec_type = ".CART_GENERAL_GOODS ." and is_check='true'";
        $goods_count = $GLOBALS['db']->GetRow($count_sql);

        $object_sql = "select * from ecs_cart where session_id = '".SESS_ID."' and rec_type = ".CART_GENERAL_GOODS." and rec_type = ".CART_GENERAL_GOODS ." and is_check='true'";
        $cart_info = $GLOBALS['db']->GetAll($object_sql);

        foreach ($cart_info as $key => $value) {
            $pic = $this->get_goods_images($value['goods_id']);
            if($params['lang'] == 'en'){
                $sql = "select goods_name_en FROM ecs_goods where goods_id =".$value['goods_id'];
                $row = $GLOBALS['db']->GetRow($sql);
                foreach ($row as  $val) {
                    $goods_name  =$val;
                }
              }else{
                $goods_name =$value['goods_name'];
              }
            $goods_info[] = array(
                'obj_ident' => 'goods_' . $value['goods_id'] . '_' . $value['goods_id'],
                'obj_type' => 'goods',
                'goods_id' => $value['goods_id'],
                'product_id' => $value['goods_id'],
                'name' => $goods_name,
                'spec_info' => $value['goods_attr'],
                'store_real' => '100',                  // 库存待修改
                'quantity' => (int)$value['goods_number'],
                'price' => $value['goods_price'],
                'discount_price' => '0',
                'total_price' => bcmul($value['goods_number'], $value['goods_price'], 3),
                'score' => '0',
                'pic' => $pic['0']['s_url'],
                'gift' => '',
                'promotion' => '',
                'is_check' => $value['is_check'],
                'g_type'=>$value['g_type']
            );
//            if ($value['g_type'] == '1') {
//                $has_points = 'true';
//            }
//            if ($value['g_type'] == '0') {
//                $has_product = 'true';
//            }
        }

        $sql = "select * from ".$GLOBALS['ecs']->table('order_info')." where user_id='".$_SESSION['user_id']."' and order_status='0' and pay_status='0'";
        $order_res = $GLOBALS['db']->getRow($sql);
        // if($order_res){
        //     return array('status' => 'succ', 'message' => '你有未付款订单，前去支付', 'data' => array());
        // }


        $object['goods'] = $goods_info;
        $object['gift'] = array();
        $object['coupon'] = array();


        /**
            积分抵扣
        **/
        $pay_points_status = 'false';
        if( $params['pay_points'] > 0){
            $row['amount'] = $row['amount'] - $params['pay_points'];
            $pay_points_status = 'true';
        }

        $data = array(
            'cart_total' => $row['number'],
            'cart_count' => $goods_count['count'],
            'subtotal_goods_price' => $row['amount'], // 总价
            'subtotal_discount_amount' => '',         // 折扣
            'subtotal_gain_score' => '',
            'subtotal_price' => $row['amount'],       // 折扣后总价
            'object' => $object,
            'cart_promotion_display' => true,
            'order_promotion' => array(),
            'pay_points' => $params['pay_points'],
            'pay_points_status'=>$pay_points_status
        );

        return array(
            'status' => 'succ',
            'message' => '',
            'response' => $data
        );
    }

    public function get_delivery_list($params)
    {
        $consignee['district'] = $params['area_id'];

        $city_sql = "select parent_id as city_id from ecs_region where region_id = " . $params['area_id'];
        $city_id = $this->db->getRow($city_sql);
        $consignee['city'] = $city_id['city_id'];
        $province_sql = "select parent_id as province_id from ecs_region where region_id = " . $city_id['city_id'];
        $province_id = $this->db->getRow($province_sql);
        $consignee['province'] = $province_id['province_id'];
        $sql = "select parent_id from ecs_region where region_id='".$consignee['province']."'";
        $country = $this->db->getRow($sql);
        $consignee['country'] = $country['parent_id'];
//        $consignee['country'] = '1';

        /* 取得配送列表 */
        $region = array($consignee['country'], $consignee['province'], $consignee['city'], $consignee['district']);
        $shipping_list = available_shipping_list($region);
        if (count($shipping_list)>1){
            unset($shipping_list['sf']);
        }
        foreach ($shipping_list as $key => $value) {
            $shipping_list[$key]['configure'] = unserialize($value['configure']);
        }

        $cart_weight_price = cart_weight_price($flow_type);
        $insure_disabled = true;
        $cod_disabled = true;

        $sql = 'SELECT count(*) FROM ecs_cart' . " WHERE `session_id` = '" . SESS_ID . "' AND `extension_code` != 'package_buy' AND `is_shipping` = 0";
        $shipping_count = $this->db->getOne($sql);

        foreach ($shipping_list AS $key => $val) {
            $shipping_cfg = unserialize_config($val['configure']);
            $shipping_fee = ($shipping_count == 0 AND $cart_weight_price['free_shipping'] == 1) ? 0 : shipping_fee($val['shipping_code'], unserialize($val['configure']),
                $cart_weight_price['weight'], $cart_weight_price['amount'], $cart_weight_price['number']);

            $shipping_list[$key]['format_shipping_fee'] = price_format($shipping_fee, false);
            $shipping_list[$key]['shipping_fee'] = $shipping_fee;
            $shipping_list[$key]['free_money'] = price_format($shipping_cfg['free_money'], false);
            $shipping_list[$key]['insure_formated'] = strpos($val['insure'], '%') === false ?
                price_format($val['insure'], false) : $val['insure'];

            if ($val['shipping_id'] == $order['shipping_id']) {
                $insure_disabled = ($val['insure'] == 0);
                $cod_disabled = ($val['support_cod'] == 0);
            }
        }
        foreach ($shipping_list as $k => $v) {
            foreach ($v['configure'] as $k1=>$v1){
                if($v1['name']=='base_fee'){
                    $base_fee = $v1['value'];
                }
                if($v1['name']=='free_money'){
                    $fee_money = $v1['value'];
                }
            }
            if($params['total_price'] > $fee_money){
                $base_fee = 0;
            }
            $data[$k] = array(
//                'delivery_fee' => $v['configure']['1']['value'],
                'delivery_fee' => $base_fee,
                'delivery_info' => $v['shipping_desc'],
                'delivery_name' => $v['shipping_name'],
                'dt_id' => $v['shipping_id'],
                'has_code' => 'false',
                'min_price' => $v['shipping_fee'],
                'protect' => 'false',
                'protect_rate' => ''
            );
        }
        return array(
            'status' => 'succ',
            'message' => '',
            'response' => $data
        );
    }
    public function create($params)
    {
        $res = array(
            'status' => 'fail',
            'response' => array(),
            'message' => '',
        );
//        echo "<pre>";
//        var_dump($params) | die;
        /* 检查购物车中是否有商品 */
        if($params['type']!='now'){
            $sql = "SELECT COUNT(*) FROM ecs_cart WHERE session_id = '" . SESS_ID . "' " .
                "AND parent_id = 0 AND is_gift = 0 AND rec_type = '$flow_type' and is_check='true'";
            if ($this->db->getOne($sql) == 0)
            {
                $res['message'] = '购物车为空';
                return $res;
            }
        }


        /*
         * 检查用户是否已经登录
         * 如果用户已经登录了则检查是否有默认的收货地址
         * 如果没有登录则跳转到登录和注册页面
         */
        if (empty($_SESSION['direct_shopping']) && $_SESSION['user_id'] == 0)
        {   
            // if($params['lang'] =="en"){
            //     $res['message'] = 'User not logged in';
            // }else{
            //     $res['message'] = '用户未登录';
            // }
            
            // return $res;
            $_SESSION['user_id']  =$params['member_id'];
        }

//        $consignee = get_consignee($_SESSION['user_id']);


        $area = explode(':',$params['area']);
        $consignee['district'] = $area['2'];
        if($area['2'] == "0"){

            $res['message'] = '请选择收货地址';
            return $res;
        }
        $city_sql = "select parent_id from ecs_region where region_id = " . $area['2'];
        $city = $this->db->getRow($city_sql);
        $consignee['city'] = $city['parent_id'];
        $province_sql = "select parent_id from ecs_region where region_id = " . $consignee['city'];
        $province = $this->db->getRow($province_sql);
        $consignee['province'] = $province['parent_id'];
        $consignee['country'] = '1';


        $_POST['how_oos'] = isset($_POST['how_oos']) ? intval($_POST['how_oos']) : 0;
        $_POST['card_message'] = isset($_POST['card_message']) ? compile_str($_POST['card_message']) : '';
        $_POST['inv_type'] = !empty($_POST['inv_type']) ? compile_str($_POST['inv_type']) : '';
        $_POST['inv_payee'] = isset($_POST['inv_payee']) ? compile_str($_POST['inv_payee']) : '';
        $_POST['inv_content'] = isset($_POST['inv_content']) ? compile_str($_POST['inv_content']) : '';
        $_POST['postscript'] = isset($_POST['postscript']) ? compile_str($_POST['postscript']) : '';

        if(empty($_POST['payment_pay_app_id'])){

            $res['message'] = '请选择支付方式';
            return $res;
        }
        $pay_id_sql = "select pay_id from ecs_payment where pay_code = '".$_POST['payment_pay_app_id']."'";
        $pay_id = $this->db->getRow(($pay_id_sql));

        $order = array(
            'shipping_id'     => intval($_POST['shipping_id']),
            'pay_id'          => intval($pay_id['pay_id']),
            'pack_id'         => isset($_POST['pack']) ? intval($_POST['pack']) : 0,
            'card_id'         => isset($_POST['card']) ? intval($_POST['card']) : 0,
            'card_message'    => trim($_POST['card_message']),
            'surplus'         => isset($_POST['surplus']) ? floatval($_POST['surplus']) : 0.00,
            'integral'        => isset($_POST['integral']) ? intval($_POST['integral']) : 0,
            'bonus_id'        => isset($_POST['bonus']) ? intval($_POST['bonus']) : 0,
            'need_inv'        => empty($_POST['need_inv']) ? 0 : 1,
            'inv_type'        => $_POST['inv_type'],
            'inv_payee'       => trim($_POST['inv_payee']),
            'inv_content'     => $_POST['inv_content'],
            'postscript'      => trim($_POST['postscript']),
            'how_oos'         => isset($_LANG['oos'][$_POST['how_oos']]) ? addslashes($_LANG['oos'][$_POST['how_oos']]) : '',
            'need_insure'     => isset($_POST['need_insure']) ? intval($_POST['need_insure']) : 0,
            'user_id'         => $_SESSION['user_id'],
            'add_time'        => gmtime(),
            'lastmodify'      => gmtime(),
            'order_status'    => OS_UNCONFIRMED,
            'shipping_status' => SS_UNSHIPPED,
            'pay_status'      => PS_UNPAYED,
            'agency_id'       => get_agency_by_regions(array($consignee['country'], $consignee['province'], $consignee['city'], $consignee['district'])),
            'consignee'       =>   $params['name'],
            'zipcode'        =>   $params['zip'],
            'address'       =>  $params['addr'],
            'house_number'  =>  $params['house_number'],
            'postscript'    =>  $params['postscript'],
            'mobile'    =>  $params['mobile'],
            'points'    =>  $params['pay_points'],
            'discount'    =>  $params['order_discount'],
        );

        /* 扩展信息 */
        if (isset($_SESSION['flow_type']) && intval($_SESSION['flow_type']) != CART_GENERAL_GOODS)
        {
            $order['extension_code'] = $_SESSION['extension_code'];
            $order['extension_id'] = $_SESSION['extension_id'];
        }
        else
        {
            $order['extension_code'] = '';
            $order['extension_id'] = 0;
        }

        /* 检查红包是否存在 */
        if ($order['bonus_id'] > 0)
        {
            $bonus = bonus_info($order['bonus_id']);

            if (empty($bonus) || $bonus['user_id'] != $user_id || $bonus['order_id'] > 0 || $bonus['min_goods_amount'] > cart_amount(true, $flow_type))
            {
                $order['bonus_id'] = 0;
            }
        }
        elseif (isset($_POST['bonus_sn']))
        {
            $bonus_sn = trim($_POST['bonus_sn']);
            $bonus = bonus_info(0, $bonus_sn);
            $now = gmtime();
            if (empty($bonus) || $bonus['user_id'] > 0 || $bonus['order_id'] > 0 || $bonus['min_goods_amount'] > cart_amount(true, $flow_type) || $now > $bonus['use_end_date'])
            {
            }
            else
            {
                if ($user_id > 0)
                {
                    $sql = "UPDATE " . $ecs->table('user_bonus') . " SET user_id = '$user_id' WHERE bonus_id = '$bonus[bonus_id]' LIMIT 1";
                    $db->query($sql);
                }
                $order['bonus_id'] = $bonus['bonus_id'];
                $order['bonus_sn'] = $bonus_sn;
            }
        }

        /* 订单中的商品 */
        /**
        获取确认选择商品
         **/
//        echo "<pre>";
//        var_dump($params);die;
        if($params['type']!='now'){
            $cart_goods = cart_goods($flow_type,'true');
        }else{
            $sql =  "select * from ecs_goods where goods_id='".$params['goods_id']."'";
            $goods_now = $this->db->getRow($sql);
            if(!$goods_now){
                $res['message'] = '商品错误';
                return $res;
            }
            $cart_goods = array();
            $cart_goods[0]['user_id'] = $params['member_id'];
            $cart_goods[0]['goods_id'] = $params['goods_id'];
            $cart_goods[0]['goods_name'] = $goods_now['goods_name'];
            $cart_goods[0]['goods_sn'] = $goods_now['goods_sn'];
            $cart_goods[0]['goods_number'] = $params['num'];
            $cart_goods[0]['market_price'] = $goods_now['market_price'];
            $cart_goods[0]['goods_price'] = $goods_now['shop_price'];
            $cart_goods[0]['goods_attr'] = '';
            $cart_goods[0]["is_real"]='1';
            $cart_goods[0]["extension_code"]='';
            $cart_goods[0]["parent_id"]='';
            $cart_goods[0]["is_gift"]='';
            $cart_goods[0]["is_shipping"]='';
            $cart_goods[0]["subtotal"]=number_format($goods_now['shop_price'] * $params['num'],2);
            $cart_goods[0]["formated_market_price"]="￥".$goods_now['market_price']."元";
            $cart_goods[0]["formated_goods_price"]="￥". $goods_now['shop_price']."元";
            $cart_goods[0]["formated_subtotal"]="￥".number_format($goods_now['shop_price']*$params['num'],2)."元";
        }
//        echo "<pre>";
//        var_dump($cart_goods);die;
        if (empty($cart_goods))
        {
            $res['message'] = '商品有误';
            return $res;
        }

        /* 检查商品总额是否达到最低限购金额 */


//        if ($flow_type == CART_GENERAL_GOODS && cart_amount(true, CART_GENERAL_GOODS) < $_CFG['min_goods_amount'])
//        {
//            $res['message'] = '商品总额未达到最低限购金额';
//            return $res;
//        }

        /* 收货人信息 */
        foreach ($consignee as $key => $value)
        {
            $order[$key] = addslashes($value);
        }

        /* 判断是不是实体商品 */
        foreach ($cart_goods AS $val)
        {
            /* 统计实体商品的个数 */
            if ($val['is_real'])
            {
                $is_real_good=1;
            }
        }
        if(isset($is_real_good))
        {
            $sql="SELECT shipping_id FROM ecs_shipping WHERE shipping_id=".$order['shipping_id'] ." AND enabled =1";
//            if(!$this->db->getOne($sql))
//            {
//                $res['message'] = 'flow_no_shipping';
//                return $res;
//            }
        }
        /* 订单中的总额 */
        $total = order_fee($order, $cart_goods, $consignee);


        $order['bonus']        = $total['bonus'];
        $order['goods_amount'] = $total['goods_price'];
//        $order['discount']     = $total['discount'];
        $order['surplus']      = $total['surplus'];
        $order['tax']          = $total['tax'];

        // 购物车中的商品能享受红包支付的总额
        $discount_amout = compute_discount_amount();
        // 红包和积分最多能支付的金额为商品总额
        $temp_amout = $order['goods_amount'] - $discount_amout;
        if ($temp_amout <= 0)
        {
            $order['bonus_id'] = 0;
        }

        /* 配送方式 */
        if ($order['shipping_id'] > 0)
        {
            $shipping = shipping_info($order['shipping_id']);
            $order['shipping_name'] = addslashes($shipping['shipping_name']);
        }
        $order['shipping_fee'] = $total['shipping_fee'];
        $order['insure_fee']   = $total['shipping_insure'];

        /* 支付方式 */
        if ($order['pay_id'] > 0)
        {
            $payment = payment_info($order['pay_id']);
            $order['pay_name'] = addslashes($payment['pay_name']);
        }
        $order['pay_fee'] = $total['pay_fee'];
        $order['cod_fee'] = $total['cod_fee'];


        $configure_id_sql = "select configure from ecs_shipping_area where shipping_id = ".$order['shipping_id'];
        $shipping_id = $this->db->getRow($configure_id_sql);

        $shipping_val = unserialize($shipping_id['configure']);


        foreach ($shipping_val as $k=>$v){
            if($v['name']=='base_fee'){
                $base_fee = $v['value'];
            }
            if($v['name']=='free_money'){
                $fee_money = $v['value'];
            }
        }

        if($fee_money){
            if($total['amount'] > $fee_money){
                $base_fee = 0;
            }
        }

//        $order['shipping_fee'] = $shipping_val[1]['value'];
        $order['shipping_fee'] = $base_fee;

        $order['order_amount']  = number_format($total['amount'], 2, '.', '');

        /* 最小金额限制 */
        $sql = "select * from ecs_shop_config where code='min_goods_amount'";
        $min_amount = $this->db->getRow($sql);
        $min_amount = $min_amount['value'];
        if($min_amount !='0'){
            if($order['order_amount'] < $min_amount){
                $res['message'] = '商品总额未达到最低限购金额,限购金额为:￥'.$min_amount."（不含配送费）";
                return $res;
            }
        }
//        echo "<pre>";
//        var_dump($shipping_val);die;
//        var_dump($order['order_amount']);die;
        $order['order_amount'] = $order['order_amount']+$order['shipping_fee'];

        /**
        获取用户信息
         **/
        $user_info = user_info($params['member_id']);

        /* 如果全部使用余额支付，检查余额是否足够 */
        if ($payment['pay_code'] == 'balance' && $order['order_amount'] > 0)
        {
            if($order['surplus'] >0) //余额支付里如果输入了一个金额
            {
                $order['order_amount'] = $order['order_amount'] + $order['surplus'];
                $order['surplus'] = 0;
            }
            if ($order['order_amount'] > ($user_info['user_money'] + $user_info['credit_line']))
            {
                $res['message'] = '预存款不足';
                return $res;
            }
            else
            {
                $order['surplus'] = $order['order_amount'];
                $order['order_amount'] = 0;
            }
        }

        /* 如果订单金额为0（使用余额或积分或红包支付），修改订单状态为已确认、已付款 */
        if ($order['order_amount'] <= 0)
        {
            $order['order_status'] = OS_CONFIRMED;
            $order['confirm_time'] = gmtime();
            $order['pay_status']   = PS_PAYED;
            $order['pay_time']     = gmtime();
            $order['order_amount'] = 0;
        }

        $order['integral_money']   = $total['integral_money'];
        $order['integral']         = $total['integral'];

        if ($order['extension_code'] == 'exchange_goods')
        {
            $order['integral_money']   = 0;
            $order['integral']         = $total['exchange_integral'];
        }

        $order['from_ad']          = !empty($_SESSION['from_ad']) ? $_SESSION['from_ad'] : '0';
        $order['referer']          = !empty($_SESSION['referer']) ? addslashes($_SESSION['referer']) : '';
        /* 来自app */
        $order['referer'] = 'APP';


        /* 记录扩展信息 */
        if ($flow_type != CART_GENERAL_GOODS)
        {
            $order['extension_code'] = $_SESSION['extension_code'];
            $order['extension_id'] = $_SESSION['extension_id'];
        }
//        echo "<pre>";
//        var_dump($order) | die;

        /* 插入订单表 */
        $error_no = 0;
        do
        {
            $order['order_sn'] = get_order_sn(); //获取新订单号
            $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('order_info'), $order, 'INSERT');

            $error_no = $GLOBALS['db']->errno();

            if ($error_no > 0 && $error_no != 1062)
            {
                die($GLOBALS['db']->errorMsg());
            }
        }
        while ($error_no == 1062); //如果是订单号重复则重新提交数据

        // $new_order_id = $this->db->insert_id();
        $new_order_id_sql = "select order_id from ecs_order_info where order_sn = ".$order['order_sn'];
        $new_order_id = $this->db->getRow($new_order_id_sql);
        $new_order_id = $new_order_id['order_id'];
        $order['order_id'] = $new_order_id;

        if($params['type']!='now'){
            /* 插入订单商品 */
            $sql = "INSERT INTO ecs_order_goods ( " .
                "order_id, goods_id, goods_name, goods_sn, product_id, goods_number, market_price, ".
                "goods_price, goods_attr, is_real, extension_code, parent_id, is_gift, goods_attr_id) ".
                " SELECT '$new_order_id', goods_id, goods_name, goods_sn, product_id, goods_number, market_price, ".
                "goods_price, goods_attr, is_real, extension_code, parent_id, is_gift, goods_attr_id".
                " FROM ecs_cart WHERE session_id = '".SESS_ID."' AND rec_type = '$flow_type' and is_check='true'";
            $this->db->query($sql);

        $sql = "select value as status from ecs_shop_config where code = 'sms_order_placed'";
        $status = $GLOBALS['db']->getRow($sql);
        if($status['status']){

        $sql = "select value as mobile from ecs_shop_config where code = 'sms_shop_mobile'";
        $mobile = $this->db->getRow($sql);
        $content = '您有一条新的订单，订单号为：'. $order['order_sn'];
        $this->sms->send($mobile['mobile'], $content, 0);
        
        // $content = '您的订单,'. $order['order_sn']."已支付".$goods_now['shop_price'];
        // $this->sms->send($params['mobile'], $content, 0);
        }
       

        }else{
            $sql = "Select * from ecs_users where user_id ='".$_SESSION['user_id']."'";
            $now_user = $this->db->getRow($sql);
            $parent_id =  $now_user['parent_id'];
            $sql =  "insert into ecs_order_goods (order_id,goods_id,goods_name,goods_sn,product_id,goods_number,market_price,goods_price,goods_attr,is_real,extension_code,parent_id,is_gift,goods_attr_id) values ('".$new_order_id."','".$params['goods_id']."','".$goods_now['goods_name']."','".$goods_now['goods_sn']."','".$params['product_id']."','".$params['num']."','".$goods_now['market_price']."','".$goods_now['shop_price']."','0','".$goods_now['is_real']."','0','".$parent_id."','0','0')";
            $this->db->query($sql);

        $sql = "select value as status from ecs_shop_config where code = 'sms_order_placed'";
        $status = $GLOBALS['db']->getRow($sql);
        if($status['status']){
         $sql = "select value as mobile from ecs_shop_config where code = 'sms_shop_mobile'";
         $mobile = $this->db->getRow($sql);
         $content = '您有一条新的订单，订单号为：'. $order['order_sn'];
         $this->sms->send($mobile['mobile'], $content, 0);
        // $content = '您的订单,'. $order['order_sn']."已支付".$goods_now['shop_price'];
        // $this->sms->send($params['mobile'], $content, 0);
        }
        
        }

        /* 修改拍卖活动状态 */
        if ($order['extension_code']=='auction')
        {
            $sql = "UPDATE ecs_goods_activity SET is_finished='2' WHERE act_id=".$order['extension_id'];
            $this->db->query($sql);
        }

        /* 处理余额、积分、红包 */
        if ($order['user_id'] > 0 && $order['surplus'] > 0)
        {
//            log_account_change($order['user_id'], $order['surplus'] * (-1), 0, 0, 0, sprintf($_LANG['pay_order'], $order['order_sn']));
            insert_account_log($params['member_id'],$params['pay_points'],$order['order_sn']);
        }
        if ($order['user_id'] > 0 && $order['integral'] > 0)
        {
//            log_account_change($order['user_id'], 0, 0, 0, $order['integral'] * (-1), sprintf($_LANG['pay_order'], $order['order_sn']));
              insert_account_log($params['member_id'],$params['pay_points'],$order['order_sn']);
        }


        if ($order['bonus_id'] > 0 && $temp_amout > 0)
        {
            use_bonus($order['bonus_id'], $new_order_id);
        }

        change_order_goods_storage($order['order_id'], true, SDT_PLACE);
        $sql1 = "update ecs_goods set virtual_sales = virtual_sales  +".$params['num'].' where goods_id ='.$params['goods_id'];
        $query = $GLOBALS['db']->query($sql1);
        // header('Content-Type: text/html;charset=utf-8');echo '<pre>';var_dump($sql1);exit;
        /* 如果使用库存，且下订单时减库存，则减少库存 */
        // if ($_CFG['use_storage'] == '1' && $_CFG['stock_dec_time'] == SDT_PLACE)
        // {
        //     change_order_goods_storage($order['order_id'], true, SDT_PLACE);
        // }

        /* 如果订单金额为0 处理虚拟卡 */
        if ($order['order_amount'] <= 0)
        {
            $sql = "SELECT goods_id, goods_name, goods_number AS num FROM ecs_cart
                     WHERE is_real = 0 AND extension_code = 'virtual_card'".
                " AND session_id = '".SESS_ID."' AND rec_type = '$flow_type' and is_check='true'";

            $res = $GLOBALS['db']->getAll($sql);

            $virtual_goods = array();
            foreach ($res AS $row)
            {
                $virtual_goods['virtual_card'][] = array('goods_id' => $row['goods_id'], 'goods_name' => $row['goods_name'], 'num' => $row['num']);
            }
            if ($virtual_goods AND $flow_type != CART_GROUP_BUY_GOODS)
            {
                /* 虚拟卡发货 */
                if (virtual_goods_ship($virtual_goods,$msg, $order['order_sn'], true))
                {
                    /* 如果没有实体商品，修改发货状态，送积分和红包 */
                    $sql = "SELECT COUNT(*)" .
                        " FROM ecs_order_goods WHERE order_id = '$order[order_id]' " .
                        " AND is_real = 1";
                    if ($this->db->getOne($sql) <= 0)
                    {
                        /* 修改订单状态 */
                        update_order($order['order_id'], array('shipping_status' => SS_SHIPPED, 'shipping_time' => gmtime()));

                        /* 如果订单用户不为空，计算积分，并发给用户；发红包 */
                        if ($order['user_id'] > 0)
                        {
                            /* 取得用户信息 */
                            $user = user_info($order['user_id']);

                            /* 计算并发放积分 */
                            $integral = integral_to_give($order);
                            log_account_change($order['user_id'], 0, 0, intval($integral['rank_points']), intval($integral['custom_points']), sprintf($_LANG['order_gift_integral'], $order['order_sn']));

                            /* 发放红包 */
                            send_order_bonus($order['order_id']);
                        }
                    }
                }
            }
        }

        /* 清空购物车 */
        if($params['type']!='now'){
            clear_cart($flow_type,'true');
        }
        /* 清除缓存，否则买了商品，但是前台页面读取缓存，商品数量不减少 */
        clear_all_files();

        /* 插入支付日志 */
        $order['log_id'] = insert_pay_log($new_order_id, $order['order_amount'], PAY_ORDER);

        /* 取得支付信息，生成支付代码 */
        if ($order['order_amount'] > 0)
        {
            $payment = payment_info($order['pay_id']);

            include_once('includes/modules/payment/' . $payment['pay_code'] . '.php');
            $pay_obj    = new $payment['pay_code'];
            //为天宫支付传递商品名
            $sql = "SELECT goods_name FROM ecs_order_goods WHERE order_id =" . $order['order_id'];
            $res = $this->db->query($sql);
            while ($aaa[] = $this->db->fetchRow($res))
            {
                $bbb = array_values($aaa);
            }
            foreach($bbb as $v)
            {
                $ccc[] = $v['goods_name'];
            }
            $goods_name = implode(',',$ccc);
            $order['goods_name'] = $goods_name;

            //天工结束

            //云起收银
            $payment['pay_code']='yunqi' and  $order['yunqi_paymethod'] = $_POST['yunqi_paymethod'];
            $pay_online = $pay_obj->get_code($order, unserialize_config($payment['pay_config']));

            $order['pay_desc'] = $payment['pay_desc'];
        }
        if(!empty($order['shipping_name']))
        {
            $order['shipping_name']=trim(stripcslashes($order['shipping_name']));
        }
        unset($_SESSION['flow_consignee']); // 清除session中保存的收货人信息
        unset($_SESSION['flow_order']);
        unset($_SESSION['direct_shopping']);

        /**
        隐藏order_id,修改为sn
         **/
        $order['order_id'] = $order['order_sn'];

        $res = array(
            'status' => 'succ',
            'response' => $order,
            'message' => '',
        );
        return $res;
    }

    /* 获取用户升级价格 */
    public function get_level_price($level_id)
    {
        $sql = "select * from ecs_user_agency where agency_id='" . $level_id . "'";
        $res = $this->db->getRow($sql);
        $sql = "select * from ecs_user_agency where level_type='" . $res['level_type'] . "'";
        $res = $this->db->getRow($sql);
        $arr['0'] = $res['level_price'];
        $arr['1'] = $res['jump_price'];
        return $arr;
    }

    /* 获取用户剩余库存和总库存 */
    public function get_stock($user_id)
    {
        $sql = "select * from ecs_users where user_id='" . $user_id . "'";
        $res = $this->db->getRow($sql);
        $points = $res['pay_points'];
        $agency_id = $res['agency_level'];
        $sql = "select * from ecs_user_agency where agency_id = '" . $agency_id . "'";
        $res = $this->db->getRow($sql);
        $limit = $res['points_limit'];
        $surplus = $limit - $points;
        $arr = array(
            'surplus' => $surplus,
            'limit' => $limit
        );
        return $arr;
    }

    /**
     * @param $user_id
     * @param $money
     * @param $up_price
     * 判断用户购买东西所需要的价格比率,以及库存
     */
    public function get_cou_money($user_id, $money, $up_price)
    {
//        $up_price[0] != 0 && $money > $up_price[0]  ? $add_up ='1' : $up_price[1] != 0 && $money > $up_price[1] ? $add_up = '2' :  $add_up = '0' ;
        if($up_price[1] !=0 && $money >= $up_price[1]){
            $add_up = '2';
        }elseif($up_price[0] !=0 && $money >= $up_price[0]){
            $add_up = '1';
        }else{
            $add_up = '0';
        }

        return $this->get_will_discount_stock($user_id,$add_up);
    }

    public function get_will_discount_stock($user_id,$add_up)
    {
        $arr = array();
        $sql = "select b.level_type from ecs_users a left join ecs_user_agency b on a.agency_level=b.agency_id where  a.user_id = '" . $user_id . "'";
        $res = $this->db->getRow($sql);
        $u_level = $res['level_type'];
        $will_level = $u_level + $add_up;
        $sql = "select * from ecs_user_agency where level_type='" . $will_level . "'";
        $agency = $this->db->getRow($sql);
        $arr['stock'] = $agency['points_limit'];
        $agency_id = $agency['agency_id'];
        $section_time = $this->make_time();
        /* 筛选订单 */
        $sql = "select sum(goods_amount) count_price from ecs_order_info where user_id='".$user_id."' and  pay_time> '".$section_time['0']."' and pay_time <'".$section_time['1']."' and order_status=".OS_CONFIRMED;
        $count_price = $this->db->getRow($sql);

        $sql = "select discount from ecs_user_month_agency where agency_id='" . $agency_id . "' and month_price <= '" . $count_price . "' order by month_price desc";
        $discount = $this->db->getRow($sql);
        $arr['discount'] = $discount['discount'];      //最终得到优惠比率
        return $arr;
    }

    public function make_time()
    {
        $sql = "select value from ".$GLOBALS['ecs']->table('shop_config')." where code ='bg_time' or code='end_time' or code='end_time_type' ";
        $result = $this->db->getAll($sql);
        $res['bg_time'] = $result[0]['value'];
        $res['end_time'] = $result[1]['value'];
        $res['end_time_type'] = $result[2]['value'];
        $time = time();
        $date = date('Ymd',$time);    //计算出几号
        $D = date('d',$time);
        $bg_time = $res['bg_time'];
        $end_time  = $res['end_time'];
        $res['end_time_type'] && $D > $bg_time['time'] ?  $arr=array('+ 0','+ 1') : $res['end_time_type'] && $D < $bg_time['time'] ? $arr = array('- 1','+ 0'): $arr=array('+ 0','+ 0');
        $res = $this->get_time($arr,$bg_time,$end_time);
        return $res;
    }

    public function get_time($arr,$bg_time,$end_time)
    {
        $time = time();
        $bg_time = date('Y-m-', strtotime($arr[0]." month", $time)).$bg_time;
        $end_time = date('Y-m-', strtotime($arr[1]." month", $time)).$end_time;

        //TODO  时间戳返回不了00：00格式，只能返回8点 待优化

        $bg_time =  strtotime($bg_time)+(60*60*16);
        $end_time =  strtotime($end_time)+(60*60*16);
        $data = array(
            $bg_time,$end_time
        );
        return $data;
    }


    public function show_cart_head($up_price,$agency_id,$user_spec,$cart_info)
    {
        $sql = "select * from ".$GLOBALS['ecs']->table('user_agency')." where agency_id='".$agency_id."'";
        $lv = $this->db->getRow($sql);
        $u_level = $lv['level_type'];
        $data['agency_name'] = $lv['agency_name'];
        if($lv['jump_level']=='true'){
            $need_money = $up_price['0'] - $data['subtotal_goods_price'];
            $n_level = $u_level+1;
            $sql = "select * from ".$GLOBALS['ecs']->table('user_agency')." where level_type='".$n_level."'";
            $nv = $this->db->getRow($sql);
            $need_money = $need_money - $cart_info['subtotal_goods_price'];

            $data['new_name'] = $nv['agency_name'];//升什么级
            $data['level_info'] ="(还差 ￥".$need_money."可定".$data['new_name']."价格)";
            $data['points_limit'] = $lv['points_limit'];
            $data['surplus_points_limit'] = $lv['points_limit'] - $user_spec['pay_points'];
            if($need_money <= '0'){
                $need_money = $up_price['1'] - $data['subtotal_goods_price'];
                $n_level = $u_level+2;
                $sql = "select * from ".$GLOBALS['ecs']->table('user_agency')." where level_type='".$n_level."'";
                $jp_level = $u_level+1;
                $nv = $this->db->getRow($sql);
                $sql = "select * from ".$GLOBALS['ecs']->table('user_agency')." where level_type='".$jp_level."'";
                $jv = $this->db->getRow($sql);
                $need_money = $need_money - $cart_info['subtotal_goods_price'];
                $data['new_name'] = $nv['agency_name'];//升什么级
                $data['level_info'] ="(当前已达到".$jv['agency_name'].",还差 ￥".$need_money."可定".$data['new_name']."价格)";
                $data['points_limit'] = $jv['points_limit'];
                $data['surplus_points_limit'] = $jv['points_limit'] - $user_spec['pay_points'];
                if($need_money <= '0'){
                    $n_level = $u_level+2;
                    $sql = "select * from ".$GLOBALS['ecs']->table('user_agency')." where level_type='".$n_level."'";
                    $nv = $this->db->getRow($sql);
                    $data['new_name'] = $nv['agency_name'];//升什么级
                    $data['level_info'] ="(当前已达到".$data['new_name']."价格)";
                    $data['points_limit'] = $nv['points_limit'];
                    $data['surplus_points_limit'] = $nv['points_limit'] - $user_spec['pay_points'];
                }
            }

        }else{
            if($up_price[0] != 0){
                $need_money = $up_price['0'] - $data['subtotal_goods_price'];
                $n_level = $u_level+1;
                $sql = "select * from ".$GLOBALS['ecs']->table('user_agency')." where level_type='".$n_level."'";
                $nv = $this->db->getRow($sql);
//                echo '<pre>';
//                var_dump($cart_info['subtotal_goods_price']);
//                var_dump($need_money);die;
                $need_money = $need_money - $cart_info['subtotal_goods_price'];
                $data['new_name'] = $nv['agency_name'];//升什么级
                $data['level_info'] ="(还差 ￥".$need_money."可定".$data['new_name']."价格)";
                $data['points_limit'] = $lv['points_limit'];
                $data['surplus_points_limit'] = $lv['points_limit'] - $user_spec['pay_points'];
                if($need_money <= '0'){
                    $n_level = $u_level+1;
                    $sql = "select * from ".$GLOBALS['ecs']->table('user_agency')." where level_type='".$n_level."'";
                    $nv = $this->db->getRow($sql);
                    $data['new_name'] = $nv['agency_name'];//升什么级
                    $data['level_info'] ="(当前已达到".$data['new_name']."价格)";
                    $data['points_limit'] = $nv['points_limit'];
                    $data['surplus_points_limit'] = $nv['points_limit'] - $user_spec['pay_points'];
                }
            }elseif($up_price['0'] == '0'){
                $data['level_info'] ="(当前已达到最高等级)";
                $data['points_limit'] = $lv['points_limit'];
                $data['surplus_points_limit'] = $lv['points_limit'] - $user_spec['pay_points'];
            }
        }
        $arr = array(
            'agency_name'=>$data['agency_name'],
            'points_limit'=>$data['points_limit'],
            'surplus_points_limit'=>$data['surplus_points_limit'],
            'level_info'=>$data['level_info']
        );
        return $arr;
    }

    /* 立即购买 */
    public function buy_now($params)
    {
        var_dump(123);die;
    }

    public function now_data($params)
    {
//        $params =array(
//            'goods_id'=>'43',
//            'product_id' =>'43',
//            'member_id'=>'3',
//            'num'=>'1'
//        );
        $goods_id = $params['goods_id'];
        $product_id = $params['product_id'];
        $num = $params['num'];

        $data = array();
        if (isset($params['product_id']) && $params['product_id'] != null)
        {
            $productId = $params['product_id'];
        } else {
            if($params['lang'] == 'en'){
                return array('status'=>'fail', 'message'=>'Please enter the product id', 'response' => $data);
            }else{

                return array('status'=>'fail', 'message'=>'请输入商品id', 'response' => $data);
            }

        }
        $member_id = $params['member_id'];

        $goods_sql = "select * from ecs_goods where goods_id = ".$productId;
        $itemProduct = $this->db->getRow($goods_sql);

        if(!$itemProduct || $itemProduct === false ){
            if($params['lang'] == 'en'){
                return array('status'=>'fail', 'message'=>'This item does not exist', 'response' => $data);
            }else{

                return array('status'=>'fail', 'message'=>'该商品不存在', 'response' => $data);
            }

        }

        $aGoods['images'] = $this->get_goods_images_original($itemProduct);
        $aGoods['product'] = $this->get_product_info($itemProduct,$params['lang']);

        if ($_SESSION['user_id']) {
            $is_fav_sql = "select * from ecs_collect_goods where user_id = ".$_SESSION['user_id']." and goods_id = ".$productId;
            $is_fav = $this->db->getRow($is_fav_sql);
            if (!empty($is_fav)) {
                $aGoods['product']['is_fav'] = 'true';
            }
        }
        $return = array(
            'cart_total' => $num,
            'cart_count'=>$num,
            'subtotal_goods_price'=>$aGoods['product']['price']* $num,
            "subtotal_discount_amount"=> "",
            "subtotal_gain_score"=> "",
            "subtotal_price" => $aGoods['product']['price']* $num,
            "object"=>array(
                'goods'=>array(
                    array(
                        "obj_ident"=> "goods_".$goods_id."_".$product_id,
                        "obj_type"=> "goods",
                        "goods_id"=> $goods_id,
                        "product_id"=> $product_id,
                        "name"=>$aGoods['product']['title'],
                        "spec_info"=> "",
                        "store_real"=> "100",
                        "quantity"=> $num,
                        "price"=> $aGoods['product']['price'],
                        "discount_price"=> "0",
                        "total_price"=>$aGoods['product']['price'],
                        "score"=> "0",
                        "pic"=> $aGoods['images'][0]['s_url'],
                        "gift"=> "",
                        "promotion"=> "",
                        "is_check"=> "true",
                        "g_type"=> "0"
                    ),

                ),
                "gift"=> [],
		        "coupon"=> []
            ),
            "cart_promotion_display"=> true,
	        "order_promotion"=> []
        );
        return array('status'=>'succ', 'message'=>'', 'response' => $return);
    }


    public function get_goods_images_original($itemProduct)
    {
        $image_sql = "select img_url as m_url,thumb_url as s_url,img_original as l_url from ecs_goods_gallery where goods_id = ".$itemProduct['goods_id'];
        $image_list = $this->db->getAll($image_sql);
        if (empty($image_list)) {
            $image_sql = "select goods_thumb as s_url,goods_img as m_url,original_img as l_url from ecs_goods where goods_id = ".$itemProduct['goods_id'];
            $image_list = $this->db->getAll($image_sql);
        }
        foreach ($image_list as $key => $value) {
            $images[$key] = array(
                'iamge_id' => $key,
                'l_url' => 'http://' . $_SERVER['HTTP_HOST'] . '/' . $value['l_url'],
                'm_url' => 'http://' . $_SERVER['HTTP_HOST'] . '/' . $value['m_url'],
                's_url' => 'http://' . $_SERVER['HTTP_HOST'] . '/' . $value['s_url'],
            );
        }
        return $images;
    }

    public function get_product_info($itemProduct,$lang)
    {
        $aGoods = array();
        if ($itemProduct['brand_id'] != '0') {
            $brand_sql = "select brand_id,brand_name from ecs_brand where brand_id = ".$itemProduct['brand_id'];
            $aGoods['brand'] = $this->db->getRow($brand_sql);
        }

        $cat_sql = "select cat_name from ecs_category where cat_id = ".$itemProduct['cat_id'];
        $cat_name = $this->db->getRow($cat_sql);
        $aGoods['cat_name'] = $cat_name['cat_name'];

        $props_sql = "select attr_value as value,attr_name as name from ecs_goods_attr a join ecs_attribute b where a.goods_id = ".$itemProduct['goods_id']." and a.attr_id = b.attr_id";
        $aGoods['props'] = $this->db->getAll($props_sql);

        $aGoods['goods_bn'] = $itemProduct['goods_sn'];
        $aGoods['goods_id'] = $itemProduct['goods_id'];
        $aGoods['goods_marketable'] = ($itemProduct['is_on_sale']=='1') ? 'true' : 'false';
        $aGoods['price'] = $itemProduct['shop_price'];
        $aGoods['mktprice'] = $itemProduct['market_price'];
        $aGoods['product_bn'] = $itemProduct['goods_sn'];
        $aGoods['product_id'] = $itemProduct['goods_id'];
        $aGoods['product_marketable'] = ($itemProduct['is_on_sale']=='1') ? 'true' : 'false';
        $aGoods['title'] = $itemProduct['goods_name'];
        $aGoods['wapintro'] = $itemProduct['goods_desc'];
        if($lang == 'en'){
            $aGoods['title'] = $itemProduct['goods_name_en'];
            $aGoods['wapintro'] = $itemProduct['goods_desc_en'];
        }
        $aGoods['store'] = $itemProduct['goods_number'];
        $aGoods['spec'] = $this->get_goods_spec($itemProduct,$lang);

        // 活动促销 待定
        $aGoods['promotion'] = array();

        return $aGoods;
    }
    public function get_goods_spec($itemProduct,$lang)
    {
        $spec_goods_sql = "SELECT a.attr_id, a.attr_name,a.attr_name_en, a.attr_type, g.goods_attr_id, g.attr_value, g.attr_price, g.goods_id, p.product_id, p.product_number FROM (ecs_goods_attr AS g LEFT JOIN ecs_attribute AS a ON a.attr_id = g.attr_id) LEFT JOIN ecs_products AS p ON g.goods_id = p.goods_id WHERE a.attr_type != 0 AND g.goods_id = ".$itemProduct['goods_id']." ORDER BY a.sort_order, g.attr_price, g.goods_attr_id";
        $goods_attr = $this->db->getAll($spec_goods_sql);

        foreach ($goods_attr as $key => $value) {
            if ($value['attr_type'] == '1') {
                $spec_goods[$value['attr_id']] = array(
                    'private_spec_value_id' => $value['attr_id'],
                    'spec_value' => $value['attr_value'],
                    'spec_value_id' => $value['goods_attr_id'],
                    'spec_image' => '',
                    'spec_goods_images' => '',
                    // 'product_id' => $value['goods_id'],
                    // 'marketable' => 'true',
                    // 'store' => $value['product_number'],
                );
                $spec['goods'][] = $spec_goods;
                if($lang == 'en'){
                    $spec_name[] = $value['attr_name_en'];
                }else{
                    $spec_name[] = $value['attr_name'];
                }

                $spec_type[] = 'text';
            }
        }

        foreach ($goods_attr as $key => $value) {
            if ($value['attr_type'] == '1') {
                $spec['product'][] = $value['attr_id'];
            }
        }

        $spec['specification']['spec_name'] = $spec_name;
        $spec['specification']['spec_type'] = $spec_type;

        $attr_keys = array_keys($goods_attr);

        return $spec;

    }
    //支付成功发送短信
    public  function get_sms_send(){

     //用户支付用户收到短信
     $sql = "select value as status from ecs_shop_config where code = 'sms_order_payed_to_customer'";
     $status = $GLOBALS['db']->getRow($sql);

     $sql = "select value as status from ecs_shop_config where code = 'sms_order_payed'";
     $status1 = $GLOBALS['db']->getRow($sql);


     $sql = "select * from ecs_order_info where sms_status = '0' and pay_status = '2'";
     $data = $this->db->getAll($sql);
     $sql = "select value as mobile from ecs_shop_config where code = 'sms_shop_mobile'";
     $mobile1 = $GLOBALS['db']->getRow($sql);
     foreach ($data as $k => $v) {
        if($v['pay_id']=='3'){
            $pay_way = '微信';
        }elseif($v['pay_id']=='2'){
            $pay_way ='云卡通';
        }elseif($v['pay_id']=='4'){
            $pay_way = '支付宝';
        }else{
            $pay_way = '';
        }

        if($status['status']){
 
        $content = '您的订单,'. $v['order_sn'].$pay_way."已支付".$v['order_amount'];
        $this->sms->send($v['mobile'], $content, 0);
        $sql ="UPDATE ecs_order_info set sms_status ='1' where order_sn = ".$v['order_sn'];
        $this->db->query($sql);
        }

        //用户支付商家收到短信
        if($status1['status']){
            $content = '您的订单,'. $v['order_sn'].$pay_way."已支付".$v['order_amount'];
            $this->sms->send($mobile1['mobile'], $content, 0);
        }

    }


    }



    //end_class
}
