<?php
/**
    @author     :   dev@yin-duo.com
    @since      :   2018/10/11
**/

class member extends apiclass
{
    /**
     * 获取指定会员订单列表
     *  order_status 订单状态 0未确认  1已确认  2 取消  3无效  4退货
     *  shipping_status 配送状态 0未发货  1已发货  2确认收货  3 备货中  4 已发货(部分商品)
     *  pay_status 款项状态  0未付款  1付款中  2已付款
     */
     

    function get_order_list($params)
    {
         //全部订单
        if ($params['order_status']=='all') {
          $orders_list_sql = "select * from ecs_order_info where user_id = " . $params['member_id'];
          $orders_list_sum_sql = "select count(order_id) from ecs_order_info where user_id = " . $params['member_id'];
        }
         //待付款订单
        if ($params['order_status']=='pay_wait') {
          $orders_list_sql = "select * from ecs_order_info where user_id = " . $params['member_id'] ." and pay_status = '0' and order_status!='2'";
//          var_dump($orders_list_sql);die;
          $orders_list_sum_sql = "select count(order_id) from ecs_order_info where user_id = " . $params['member_id'] ." and pay_status = '0' and order_status!='2'";
        }
         //待补货订单
        if ($params['order_status']=='wait_order') {
          $orders_list_sql = "select * from ecs_order_info where user_id = " . $params['member_id'] ." and pay_status = '7'";
          $orders_list_sum_sql = "select count(order_id) from ecs_order_info where user_id = " . $params['member_id'] ." and pay_status = '7'";
        }
       
         //待发货订单
        if ($params['order_status']=='delivery') {
            /* 待发货3改为0 */
          // $orders_list_sql = "select * from ecs_order_info where user_id = " . $params['member_id'] ." and pay_status = '2' and shipping_status = '0' and order_status = '0'";
             $orders_list_sql ="select * from ecs_order_info where user_id = '".$params['member_id']."' and pay_status = '2' and shipping_status = '0' and order_status = '1'";
          $orders_list_sum_sql = "select count(order_id) from ecs_order_info where user_id = " . $params['member_id'] ." and pay_status = '2' and shipping_status = '0' and order_status = '0'";
        }
         //待收货订单
        if ($params['order_status']=='received_wait') {
          $orders_list_sql = "select * from ecs_order_info where user_id = " . $params['member_id'] ." and shipping_status = '1'";
          $orders_list_sum_sql = "select count(order_id) from ecs_order_info where user_id = " . $params['member_id'] ." and shipping_status = '1'";
        }
         //已取消订单
        if ($params['order_status']=='cancel') {
          $orders_list_sql = "select * from ecs_order_info where user_id = " . $params['member_id'] ." and order_status = '2'";
          $orders_list_sum_sql = "select count(order_id) from ecs_order_info where user_id = " . $params['member_id'] ." and order_status = '2'";
        }
        
        $order_sum = $this->db->getAll($orders_list_sum_sql);

        //处理分页
        $pages = intval($params['req_pages']) ? intval($params['req_pages']) : 1;
        //页码
        $number = intval($params['req_number']) ? intval($params['req_number']) : 10;

        //每页数量
        $orderby = ' order by order_id desc ';
        $offset = ($pages - 1) * $number;
        $number = $pages * $number;
        $limit = " LIMIT " . $offset . "," . $number ;
        if($params['order_status']=='pay_wait'){
            $orders_list_sql = $orders_list_sql." order by add_time desc";
        }else{
            $orders_list_sql = $orders_list_sql . $orderby . $limit;
        }
        $orders_lists = $this->db->getAll($orders_list_sql);
        $return = array();
        foreach ($orders_lists as $k => $v) {
            //购买商品总数量
            $goods_number_sql = "select sum(goods_number) as goods_number,goods_price  from ecs_order_goods where order_id = " . $v['order_id'];
            
            $goods_number = $this->db->getAll($goods_number_sql);

            $return[$k]['order_id'] = $v['order_sn'];
            //订单号

            $return[$k]['itemnum'] = $goods_number[0]['goods_number'];
            
            $return[$k]['goods_price'] = $goods_number[0]['goods_price'];
            //订单商品总数量
            if($v['order_amount']>0){
                $return[$k]['amount'] = $v['order_amount']-$v['discount'];
            }else{
                $return[$k]['amount'] = $v['surplus']-$v['discount'];
            }

            //订单金额

            $return[$k]['createtime'] = $v['add_time'];
            //订单添加时间

            if ($v['pay_status']==1) {
                $v['pay_status']=0;
            }
            if ($v['pay_status']==2) {
                $v['pay_status']=1;
            }
            $return[$k]['pay_status'] = $v['pay_status'];
            //订单付款状态需对应匹配
            //pay_status 款项状态  0未付款  1付款中  2已付款(ecshop)
            //pay_status  付款状态 0:未支付;1:已支付;2:已付款至到担保方;3:部分付款;4:部分退款;5:全额退款;6:退款申请中;(ecstore)

            $return[$k]['ship_status'] = $v['shipping_status'];
            //订单发货状态需对应匹配
            //shipping_status 配送状态 0未发货  1已发货  2确认收货  3 备货中  4 已发货(部分商品)(ecshop)
            //发货状态 0:未发货;1:已发货;2:部分发货;3:部分退货;4:已退货;(ecstore)

            if ($v['order_status']==0) {
                $v['order_status']='active';
            }
            if ($v['order_status']==1 || $v['shipping_status']==2) {
                $v['order_status']='finish';
            }
            if ($v['order_status']==2 || $v['order_status']==3) {
                $v['order_status']='dead';
            }
            $return[$k]['status'] = $v['order_status'];
            //订单状态需对应匹配
            //order_status 订单状态 0未确认  1已确认  2 取消  3无效  4退货(ecshop)
            //订单状态 active:活动订单;dead:已作废;finish:已完成; (ecstore)

            $return[$k]['received_status'] = 0;
            //收货状态

            $return[$k]['pay_app_id'] = 'deposit';
            //支付方式

            $return[$k]['cancel'] = 'true';
            $order_goods_sql = "select * from ecs_order_goods where order_id = " . $v['order_id'];
            $order_goods = $this->db->getAll($order_goods_sql);
            $item = array();
            foreach ($order_goods as $m => $n) {
                if ($n['product_id'] == 0) {
                    $n['product_id'] = $n['goods_id'];
                }
                $goods_sql = "select * from ecs_goods where goods_id = " . $n['goods_id'];
                $goods = $this->db->getAll($goods_sql);
                $is_review['is_review'] = $n['is_review'];
                $item[$m]['goods_id'] = $n['goods_id'];
                $item[$m]['product_id'] = $n['product_id'];
                $item[$m]['goods_price'] = $n['goods_price'];
                $item[$m]['goods_name'] = $goods[0]['goods_name'];
                $item[$m]['spec_info'] = '';
                $item[$m]['quantity'] = $n['goods_number'];
                $item[$m]['item_type'] = 'product';
                $item[$m]['goods_pic']['s_url'] = 'http://' . $_SERVER['HTTP_HOST'] . '/' . $goods[0]['goods_thumb'];
                $item[$m]['goods_pic']['m_url'] = 'http://' . $_SERVER['HTTP_HOST'] . '/' . $goods[0]['goods_img'];
                $item[$m]['goods_pic']['l_url'] = 'http://' . $_SERVER['HTTP_HOST'] . '/' . $goods[0]['original_img'];
            }
            $return[$k]['item'] = $item;
            $return[$k]['is_review'] = $is_review;
        }
        $data['orderData'] = $return;
        $data['pager_total'] = intval($order_sum[0]['count(order_id)']/$params['req_number']) + 1;

        return array('status' => 'succ', 'message' => '', 'response' => $data);
    }
    /**
     * 订单详情接口
     * 需要传递sn或者order_id
     */
    function get_order_info($params)
    {
        $orders_list_sql = "select * from ecs_order_info where order_sn = " . $params['order_id'];
        $orders_lists = $this->db->getAll($orders_list_sql);
        //地址
        
        $address = $this->region($orders_lists[0]['country'], $orders_lists[0]['province'], $orders_lists[0]['city'], $orders_lists[0]['district']);
        //商品详细信息
        $order_goods_sql = "select * from ecs_order_goods where order_id = " . $orders_lists[0]['order_id'];
        $order_goods = $this->db->getAll($order_goods_sql);
        $item = array();
        foreach ($order_goods as $m => $n) {
            if ($n['product_id'] == 0) {
                $n['product_id'] = $n['goods_id'];
            }
            $goods_sql = "select * from ecs_goods where goods_id = " . $n['goods_id'];
            $goods = $this->db->getAll($goods_sql);
            $item[$m]['order_id'] = $orders_lists[0]['order_sn'];
            $item[$m]['goods_id'] = $n['goods_id'];
            $item[$m]['product_id'] = $n['product_id'];
            $item[$m]['name'] = $n['goods_name'];
            $item[$m]['price'] = $n['goods_price'];
            $item[$m]['mktprice'] = $n['market_price'];
            $item[$m]['spec_info'] = '';
            $item[$m]['quantity'] = $n['goods_number'];

            $item[$m]['goods_pic'][$n['goods_id']]['s_url'] = 'http://' . $_SERVER['HTTP_HOST'] . '/' . $goods[0]['goods_thumb'];
            $item[$m]['goods_pic'][$n['goods_id']]['m_url'] = 'http://' . $_SERVER['HTTP_HOST'] . '/' . $goods[0]['goods_img'];
            $item[$m]['goods_pic'][$n['goods_id']]['l_url'] = 'http://' . $_SERVER['HTTP_HOST'] . '/' . $goods[0]['original_img'];
        }
        $return['status'] = 'succ';
        $return['message'] = '';
        //支付信息
        $return['response']['order_id'] = $params['order_id'];
        //订单号
        $return['response']['total_amount'] = $orders_lists[0]['order_amount'];
        //订单总额
        $return['response']['cur_amount'] = $orders_lists[0]['order_amount']-$orders_lists[0]['points'];
//        $return['response']['invoice_name'] = $orders_lists[0]['invoice_name'];
        $return['response']['invoice_name'] = $orders_lists[0]['shipping_name'];
        $return['response']['invoice_no'] = $orders_lists[0]['invoice_no'];

            if ($orders_lists[0]['pay_status']==1) {
                $orders_lists[0]['pay_status']=0;
            }
            if ($orders_lists[0]['pay_status']==2) {
                $orders_lists[0]['pay_status']=1;
            }
        $return['response']['pay_status'] = $orders_lists[0]['pay_status'];
        //订单付款状态需对应匹配
        //pay_status 款项状态  0未付款  1付款中  2已付款(ecshop)
        //pay_status  付款状态 0:未支付;1:已支付;2:已付款至到担保方;3:部分付款;4:部分退款;5:全额退款;6:退款申请中;(ecstore)

        $return['response']['ship_status'] = $orders_lists[0]['shipping_status'];
        //订单发货状态需对应匹配
        //shipping_status 配送状态 0未发货  1已发货  2确认收货  3 备货中  4 已发货(部分商品)(ecshop)
        //发货状态 0:未发货;1:已发货;2:部分发货;3:部分退货;4:已退货;(ecstore)

        $return['response']['received_status'] = 0;
        $return['response']['is_delivery'] = 'Y';
        $return['response']['createtime'] = $orders_lists[0]['add_time'];
        $return['response']['received_time'] = $orders_lists[0]['confirm_time'];
        $return['response']['last_modified'] = $orders_lists[0]['pay_time'];
        //ecshop没有更新时间，这里用支付时间代替了
        //支付方式 这里用预存款支付显示
        $return['response']['payinfo']['pay_app_id'] = 'deposit';
        $return['response']['payinfo']['cost_payment'] = '0.000';
        //pay_id 4为支付宝支付  3为微信支付 6为预存款支付
        $return['response']['payinfo']['pay_id'] = $orders_lists[0]['pay_id'];
        $sql = "select * from ecs_payment where pay_id='".$orders_lists[0]['pay_id']."'";
        $pay_info_name = $this->db->getRow($sql);
        $pay_info_name = $pay_info_name['pay_code'];
        $return['response']['payinfo']['pay_app_id'] = $pay_info_name;

        if($orders_lists[0]['pay_name'] == '<font color="#FF0000">天工收银</font>'){
            $return['response']['payinfo']['pay_name'] = ' 天工收银';
        }else{
          $return['response']['payinfo']['pay_name'] = $orders_lists[0]['pay_name'];  
        }
        
        //快递
        $return['response']['shipping']['shipping_id'] = $orders_lists[0]['shipping_id'];
        $return['response']['shipping']['shipping_name'] = $orders_lists[0]['shipping_name'];
        $return['response']['shipping']['is_protect'] = 'false';
        $return['response']['shipping']['cost_protect'] = $orders_lists[0]['insure_fee'];
        $return['response']['shipping']['cost_shipping'] = $orders_lists[0]['shipping_fee'];
        $return['response']['member_id'] = $params['member_id'];
        $return['response']['promotion_type'] = 'normal';

            if ($orders_lists[0]['order_status']==0) {
                $orders_lists[0]['order_status']='active';
            }
            if ($orders_lists[0]['order_status']==1 || $orders_lists[0]['shipping_status']==2) {
                $orders_lists[0]['order_status']='finish';
            }
            if ($orders_lists[0]['order_status']==2 || $orders_lists[0]['order_status']==3) {
                $orders_lists[0]['order_status']='dead';
            }
        $return['response']['status'] = $orders_lists[0]['order_status'];
        //订单状态
        //order_status 订单状态 0未确认  1已确认  2 取消  3无效  4退货(ecshop)
        //订单状态 active:活动订单;dead:已作废;finish:已完成; (ecstore)

        $return['response']['confirm'] = 'N';
        $return['response']['consignee']['area'] = $address['area'];
        $return['response']['consignee']['name'] = $orders_lists[0]['consignee'];
        $return['response']['consignee']['addr'] = $address['addr'] . $orders_lists[0]['address'];
        $return['response']['consignee']['zip'] = $orders_lists[0]['zipcode'];
        $return['response']['consignee']['telephone'] = $orders_lists[0]['tel'];
        $return['response']['consignee']['email'] = $orders_lists[0]['email'];
        $return['response']['consignee']['r_time'] = '任意时间,任意时间段';
        $return['response']['consignee']['mobile'] = $orders_lists[0]['mobile'];
        //
        $return['response']['weight'] = '0.000';
        $return['response']['title'] = '订单明细介绍';
        $return['response']['itemnum'] = '';
        //订单子订单数量
        $return['response']['ip'] = '127.0.0.1';
        $return['response']['cost_item'] = $orders_lists[0]['goods_amount'];
        $return['response']['is_tax'] = 'false';
        $return['response']['tax_type'] = 'false';
        $return['response']['tax_content'] = $orders_lists[0]['inv_content'];
        $return['response']['cost_tax'] = '0.000';
        $return['response']['tax_title'] = $orders_lists[0]['inv_payee'];
        $return['response']['currency'] = 'CNY';
        $return['response']['cur_rate'] = '1.0000';
        $return['response']['score_u'] = '0.000';
        $return['response']['score_g'] = '0.000';
        $return['response']['discount'] = '0.000';
        //商品优惠促销（坑未填）
        $return['response']['pmt_goods'] = '0.000';
        //订单优惠促销（坑未填）(使用积分抵扣)
        $return['response']['pmt_order'] = $orders_lists[0]['discount'];
        $amount =  $return['response']['pmt_goods']+ $return['response']['pmt_order'];
        $return['response']['amount'] =  $orders_lists[0]['goods_amount']-$amount;
        //应支付金额
        $return['response']['total_amount'] =  $orders_lists[0]['goods_amount']+$orders_lists[0]['shipping_fee'];
        //实际付金额
        $return['response']['actual'] =  $orders_lists[0]['goods_amount']-$amount+$orders_lists[0]['shipping_fee'];
        $return['response']['payed'] = $orders_lists[0]['money_paid'];
        $return['response']['memo'] = $orders_lists[0]['postscript'];
        $return['response']['disabled'] = 'false';
        $return['response']['displayonsite'] = 'true';
        $return['response']['mark_type'] = 'b10';
        $return['response']['mark_text'] = '';
        $return['response']['extend'] = 'false';
        $return['response']['order_refer'] = 'local';
        $return['response']['addon'] = '';
        $return['response']['source'] = '';
        $return['response']['is_oversold'] = 'false';
        $return['response']['order_pmt'] = '';
        //优惠信息
        //demo
        //         'order_pmt' =>
        // array (
        //   0 =>
        //   array (
        //     'pmt_id' => '1',
        //     'order_id' => '181017112829884',
        //     'product_id' => NULL,
        //     'pmt_type' => 'order',
        //     'pmt_amount' => '0.000',
        //     'pmt_tag' => '送优惠券',
        //     'pmt_memo' => '全场购物满10打8折！',
        //     'pmt_describe' => '全场购物满10打8折',
        //     'cpns_type' => NULL,
        //   ),
        //   1 =>
        //   array (
        //     'pmt_id' => '1',
        //     'order_id' => '181017112829884',
        //     'product_id' => '549',
        //     'pmt_type' => 'goods',
        //     'pmt_amount' => '60.000',
        //     'pmt_tag' => '减价',
        //     'pmt_memo' => '商品优惠',
        //     'pmt_describe' => '商品价格优惠￥20.00出售',
        //     'cpns_type' => NULL,
        //   ),
        // ),
        $return['response']['order_items'] = $item;
        //商品信息
        return $return;
    }

    /**
     * 根据用户id获取商品收藏列表
     */
    public function get_fav($params)
    {   
        $collect_goods_sql = "select * from ecs_collect_goods where user_id = " . $params['member_id'];
        //处理分页
        $pages = intval($params['req_pages']) ? intval($params['req_pages']) : 1;
        //页码
        $number = intval($params['req_number']) ? intval($params['req_number']) : 10;
        //每页数量
        $offset = ($pages - 1) * $number;
        $limit = " LIMIT " . $offset . "," . $number;
        $collect_goods_sql = $collect_goods_sql . $limit;
        $collect_goods = $this->db->getAll($collect_goods_sql);
        if (!$collect_goods) {
            
            if($params['lang'] == 'en'){
                 return array('status' => 'fail', 'message' => 'No favorite record', 'response' => array());
            }else{
                return array('status' => 'fail', 'message' => '无收藏记录', 'response' => array());
            }
            
        }
        $favGoods = array();
        foreach ($collect_goods as $k => $v) {
            $goods_sql = "select * from ecs_goods where goods_id = " . $v['goods_id'];
            $goods = $this->db->getAll($goods_sql);
            $goods_pic = $this->get_goods_images($v['goods_id'], 'all', 'first');
            $favGoods['goods'][$k]['goods_id'] = $v['goods_id'];
            $favGoods['goods'][$k]['product_id'] = $v['goods_id'];
            $favGoods['goods'][$k]['goods_name'] = $goods[0]['goods_name'];
            $favGoods['goods'][$k]['spec_info'] = $goods[0]['goods_name'];
            if($params['lang'] == 'en'){
            $favGoods['goods'][$k]['goods_name'] = $goods[0]['goods_name_en'];
            
            }
            
            $favGoods['goods'][$k]['goods_price'] = $goods[0]['market_price'];
            $favGoods['goods'][$k]['marketable'] = $goods[0]['is_on_sale'];
            $favGoods['goods'][$k]['goods_pic'] = $goods_pic[0];
        }
        $return = $favGoods;
        return array('status' => 'succ', 'message' => '', 'response' => $return);
    }
    /**
     * 商品加入收藏夹接口
     */
    public function save_fav($params) {
        $goods_id = intval($params['goods_id']);
        if ($good_id < 0) {
            return array('status'=>'fail', 'message'=>'商品ID错误', 'response' => array());
        }
        //获取商品
        $goods_sql = "select * from ecs_goods where goods_id = " . $params['goods_id'];
        $goods = $this->db->getAll($goods_sql); 
        if (empty($goods)) {
            return array('status'=>'fail', 'message'=>'商品不存在', 'response' => array());
        }
        $collect_goods_sql = "select * from ecs_collect_goods where goods_id = " . $params['goods_id'] ." and user_id = ". $params['member_id'];
        $collect_goods = $this->db->getAll($collect_goods_sql);        
        //判断是否已经添加
        if($collect_goods) {
             return array('status'=>'fail', 'message'=>'商品已经收藏', 'response' => array());
            // if($params['lang'] == 'en'){
            //     return array('status'=>'fail', 'message'=>'Goods have been collected', 'response' => array());
            // }else{
            //     return array('status'=>'fail', 'message'=>'商品已经收藏', 'response' => array());
            // }
            
        }
        $insert_goods_sql = "insert into ecs_collect_goods set goods_id = " . $params['goods_id'] ." , user_id = ". $params['member_id'] ." , add_time = ". time();
        $insert_goods = $this->db->getAll($insert_goods_sql);   

        $return['status'] = 'succ';
        $return['message'] = '添加成功';
        $return['response'] = array();

        return $return;
    }
    /**
     * 取消收藏商品接口
     */
    public function del_fav($params)
    {
        if($params['remove_all']){
            $sql = "delete from ecs_collect_goods where user_id =".$params['member_id'];
            $this->db->getAll($sql);
        }else{
            $goods_id = intval($params['goods_id']);
            $collect_goods_sql = "delete from ecs_collect_goods where goods_id = " . $goods_id ." and user_id = ". $params['member_id'];
            $collect_goods = $this->db->getAll($collect_goods_sql);
        }

        $return['status'] = 'succ';
        $return['message'] = '删除成功';
        $return['response'] = array();
        return $return;
    }
    /**
     * 保存会员新建/编辑的收货地址
     */
    public function save_address($params){
        $must_params = array('ship_name','ship_area','ship_addr');

        foreach($must_params as $must_params_v){
            if(empty($params[$must_params_v])){
                $msg = $must_params_v.' 参数为必填参数';
                return array('status'=>'fail', 'message'=>$msg, 'response' => array());
            }
        }

        if( empty($params['ship_tel']) && empty($params['ship_mobile']) ){
            return array('status'=>'fail', 'message'=>'电话号码和手机号码必填一项', 'response' => array());
        }
        if( !empty($params['ship_id']) ){
            $data['addr_id'] = $params['ship_id'];
            $inup = 'update ';
            $inupwh = 'where address_id = '.$data['addr_id'];
        }else{
            $inup = 'insert into ';
            $inupwh = '';
        }
        $data['name']       = $params['ship_name'];
        $data['area']       = $params['ship_area'];
        $data['addr']       = $params['ship_addr'];
        $data['zip']        = !empty($params['ship_zip']) ? $params['ship_zip'] : 000000;
        $data['tel']        = !empty($params['tel']) ? $params['tel'] : '';
        $data['mobile']     = $params['ship_mobile'];
        $data['def_addr']   = $params['is_default'] == true ? 1 : 0 ;
        if( !isset($data['addr_id']) && $params['is_default'] == 'false' ){
            unset($data['def_addr']);
        }

        $district = explode(':',$data['area']);
        $district = $district['2'];
        
        // $reg='/(\d{3}(\.\d+)?)/is';//匹配数字的正则表达式
        // preg_match($reg,$data['area'],$district);
        // $district[0] = substr($data['area'],strripos($data['area'],':')+1,strlen($data['area']));

        if ($district) {

            $add_first_sql = "select parent_id from ecs_region where region_id = " . $district;
            $add_first = $this->db->getRow($add_first_sql);
            if ($add_first) {
                $add_second_sql = "select parent_id from ecs_region where region_id = " . $add_first['parent_id'];
                $add_second = $this->db->getRow($add_second_sql);
            }
            if ($add_second) {
                $add_third_sql = "select parent_id from ecs_region where region_id = " . $add_second['parent_id'];
                $add_third = $this->db->getRow($add_third_sql);
            }

            if (!empty($add_third['parent_id'])) {
                $country = $add_third['parent_id'];
                $province = $add_second['parent_id'];
                $city = $add_first['parent_id'];
                $district = $district;
                $sql = $inup ."ecs_user_address set user_id='".$params['member_id']."',consignee='".$data['name']."',email='',country={$country},province={$province},city={$city},district={$district},address='".$data['addr']."',zipcode='".$data['zip']."',house_number='".$data['house_number']."',tel='".$data['tel']."',mobile='".$data['mobile']."' $inupwh";
            } elseif (!empty($add_second['parent_id'])) {
                $country = $add_second['parent_id'];
                $province = $add_first['parent_id'];
                $city = $district;
                $sql = $inup ."ecs_user_address set user_id='".$params['member_id']."',consignee='".$data['name']."',email='',country={$country},province={$province},city={$city},district='',address='".$data['addr']."',zipcode='".$data['zip']."',house_number='".$data['house_number']."',tel='".$data['tel']."',mobile='".$data['mobile']."' $inupwh";
            } elseif (!empty($add_first['parent_id'])) {
                $country = $add_first['parent_id'];
                $province = $district;
                $sql = $inup ."ecs_user_address set user_id='".$params['member_id']."',consignee='".$data['name']."',email='',country={$country},province={$province},city='',district='',address='".$data['addr']."',zipcode='".$data['zip']."',house_number='".$data['house_number']."',tel='".$data['tel']."',mobile='".$data['mobile']."' $inupwh";
            }
            
            // $city_sql = "select parent_id from ecs_region where region_id = " . $district;
            // $city = $this->db->getAll($city_sql);
            // $province_sql = "select parent_id from ecs_region where region_id = " . $city[0]['parent_id'];
            // $province = $this->db->getAll($province_sql);
            // $sql = $inup ."ecs_user_address set user_id='".$params['member_id']."',consignee='".$data['name']."',email='',country={$country},province={$province},city={$city},district={$district},address='".$data['addr']."',zipcode='".$data['zip']."',house_number='".$data['house_number']."',tel='".$data['tel']."',mobile='".$data['mobile']."' $inupwh";
            // echo '<pre>';var_dump($sql);exit;
            $address = $this->db->getAll($sql);
        }else{
            $sql = $inup ." ecs_user_address set user_id='".$params['member_id']."',consignee='".$data['name']."',email='',address='".$data['addr']."',zipcode='".$data['zip']."',tel='".$data['tel']."',house_number='".$data['house_number']."',mobile='".$data['mobile']."' $inupwh";
            // echo '<pre>';var_dump($sql);exit;
            $address = $this->db->getAll($sql);
        }
        $sql = "update ecs_user_address set def_addr='0'  where user_id = " . intval($params['member_id']);

        $default_member_addr = $this->db->getAll($sql);

        $sql = "update ecs_user_address set def_addr='1'  where user_id = '" . intval($params['member_id'])."' and address_id = " . intval($params['ship_id']);

        $default_member_addr = $this->db->getAll($sql);

        return array('status'=>'succ', 'message'=>'成功', 'response' => array());
    }

    /**
     * 根据会员查询收货地址列表
     */
    public function get_addresslist($params){
        $user_address_list_sql = "select * from ecs_user_address where user_id = " . $params['member_id'];
        $user_address_list = $this->db->getAll($user_address_list_sql);
        $result = array();
        foreach ($user_address_list as $k => $v) {
            $addr = $this->region($v['country'],$v['province'],$v['city'],$v['district']);
            $result['response']['addresslist'][$k]['ship_id']=$v['address_id'];
            $result['response']['addresslist'][$k]['ship_name']=$v['consignee'];
            $result['response']['addresslist'][$k]['ship_area']=$addr['area'];
            $result['response']['addresslist'][$k]['ship_addr']=$v['address'];
            $result['response']['addresslist'][$k]['ship_zip']=$v['zipcode'];
            $result['response']['addresslist'][$k]['ship_tel']=$v['tel'];
            $result['response']['addresslist'][$k]['ship_mobile']=$v['mobile'];
            $result['response']['addresslist'][$k]['ship_detail']=$v['house_number'];
            $result['response']['addresslist'][$k]['is_default']=$v['def_addr'];
        }
        $result['status']='succ';
        $result['message']='';
        return $result;

    }
    /**
     * 根据会员查询收货地址详情
     */
    public function get_address($params){
        $user_address_sql = "select * from ecs_user_address where user_id = " . $params['member_id'] . " and  address_id = " .  $params['ship_id'];
        $user_address = $this->db->getAll($user_address_sql);
            $addr = $this->region($user_address[0]['country'],$user_address[0]['province'],$user_address[0]['city'],$user_address[0]['district']);
            $result['response']['ship_id']=$user_address[0]['address_id'];
            $result['response']['ship_name']=$user_address[0]['consignee'];
            $result['response']['ship_area']=$addr['area'];
            $result['response']['ship_addr']=$user_address[0]['address'];
            $result['response']['ship_zip']=$user_address[0]['zipcode'];
            $result['response']['ship_tel']=$user_address[0]['tel'];
            $result['response']['ship_mobile']=$user_address[0]['mobile'];
            $result['response'][$k]['is_default']='false';
            $result['response'][$k]['ship_day']='任意日期';
            $result['response'][$k]['ship_time']='任意时间段';
            $result['status']='succ';
            $result['message']='';
        return $result;

    }
    /**
     * 删除会员地址接口
     */
    public function del_address($params){        
        $return = array('status'=>'fail', 'message'=>'', 'response' => array());
        $addr_id = intval($params['ship_id']);
        if ($addr_id < 1) {
            $return['message'] = '请填写正确的addr_id';
            return $return;
        }
        $user_address_sql = "select * from ecs_user_address where user_id = " . $params['member_id'] . " and  address_id = " .  $params['ship_id'];
        $user_address = $this->db->getAll($user_address_sql);

        if (empty($user_address)) {
            $return['message'] = '地址不存在';
            return $return;
        } 
        $del_address_sql = "delete from ecs_user_address where  address_id = " .  $params['ship_id'];
        $del_address = $this->db->getAll($del_address_sql);

        $return['status'] = 'succ';
        $return['message'] = '删除成功';

        return $return;
        
    }

    /**
     *  获取个人中心页面信息  
     *  order_status 订单状态 0未确认  1已确认  2 取消  3无效  4退货
     *  shipping_status 配送状态 0未发货  1已发货  2确认收货  3 备货中  4 已发货(部分商品)
     *  pay_status 款项状态  0未付款  1付款中  2已付款
     */ 
    public function get_member_other_info($params){
        $user_sql = "select * from ecs_users where user_id = '".$params['member_id']."'";
        $user = $this->db->getRow($user_sql);

        $user['uid'] = substr('00000000' . $user['user_id'],-8);

        //待付款
        $user_sql = "select count(order_id) as wait from ecs_order_info where user_id = '".$params['member_id']."' and pay_status='0' and order_status!='2'";
        $tmp = $this->db->getRow($user_sql);
        $user['wait'] = intval($tmp['wait']);

        //待发货
        $user_sql = "select count(order_id) as delivery from ecs_order_info where user_id = '".$params['member_id']."' and pay_status = '2' and shipping_status = '0' and order_status = '1'";
        $tmp = $this->db->getRow($user_sql);
        $user['delivery'] = intval($tmp['delivery']);

        //待收货
        $user_sql = "select count(order_id) as rec from ecs_order_info where user_id = '".$params['member_id']."' and shipping_status='1' and pay_status = '2'";
        $tmp = $this->db->getRow($user_sql);
        $user['rec'] = intval($tmp['rec']);

        //退款/售后
        $user_sql = "select count(order_id) as refund from ecs_order_info where user_id = '".$params['member_id']."' and order_status='4' and pay_status = '2'";
        $tmp = $this->db->getRow($user_sql);
        $user['refund'] = intval($tmp['refund']);

        //全部订单
        $user_sql = "select count(order_id) as own from ecs_order_info where user_id = '".$params['member_id']."' ";
        $tmp = $this->db->getRow($user_sql);
        $user['own'] = intval($tmp['own']);
        $user['user_name'] = utf8_decode($user['user_name']);
        return array('status'=>'succ', 'message'=>'', 'response' => $user);
    }
    
    /**
     * 用户基本信息查询
     */
    public function get_member_info($params){

        $user_sql = "select * from ecs_users where user_id = " . $params['member_id'];
        $user = $this->db->getAll($user_sql);

        if ($user[0]['user_rank']==0) {
          $user_rank[0]['rank_name']=='注册会员';
        }else{
          $user_rank_sql = "select * from ecs_user_rank where rank_id = " . $user[0]['user_rank'];
          $user_rank = $this->db->getAll($user_rank_sql);
        }
        

        
        $data['lianxiren']=$user[0]['user_name'];
        $data['name']='';
        // $data['area']='';
        $data['gender']=$user[0]['sex'];
        // $data['addr']='';
        $data['telephone']=$user[0]['mobile_phone'];
        // $data['zipcode']='';
        $data['uname']=$user[0]['user_name'];
        $data['levelname']=$user_rank[0]['rank_name'];
        $data['sex'] = $user[0]['sex'];
        $data['birthday'] = $user[0]['birthday'];
        $data['real_name'] = $user[0]['real_name'];
        $data['email'] = $user['0']['email'];
        $data['pay_password'] = $user[0]['pay_password'];
        $data['user_pic'] = $user[0]['user_pic'];
        return array('status'=>'succ', 'message'=>'', 'response' => $data);
    }
    
    public function get_member_pinfo($params){
        $user_sql = "select parent_id from ecs_users where user_id = " . $params['member_id'];
        $tmp = $this->db->getRow($user_sql);
        if($params['lang'] == "en"){

            if($tmp['parent_id'] == '0'){
            return array('status'=>'fail', 'message'=>'No superior', 'response' => $data);
        }

        }else{
            if($tmp['parent_id'] == '0'){
            return array('status'=>'fail', 'message'=>'无上级', 'response' => $data);
        }

        }
      
        $user_sql = "select * from ecs_users where user_id = " . $tmp['parent_id'];
        $user = $this->db->getRow($user_sql);
        return array('status'=>'succ', 'message'=>'', 'response' => $user);

    }

    /**
     * 更新会员信息 
     */
    public function save_member_info($params){
        //信息过少，后续个人信息修改再处理
        $user_sql = "select * from ecs_users where user_id = " . $params['member_id'];
        $user = $this->db->getAll($user_sql);

        if ($user[0]['user_rank']==0) {
          $user_rank[0]['rank_name']=='注册会员';
        }else{
          $user_rank_sql = "select * from ecs_user_rank where rank_id = " . $user[0]['user_rank'];
          $user_rank = $this->db->getAll($user_rank_sql);
        }

        
        $data['lianxiren']=$user[0]['user_name'];
        $data['name']='';
        // $data['area']='';
        $data['gender']=$user[0]['sex'];
        // $data['addr']='';
        $data['telephone']=$user[0]['mobile_phone'];
        // $data['zipcode']='';
        $data['uname']=$user[0]['user_name'];
        $data['levelname']=$user_rank[0]['rank_name'];
        return $data;
    }
    
    /**
        获取用户积分
    **/
    public function get_member_point($params){
        //change_type 0为充值，1为提现，2为管理员调节，99为其他类型
        $where = '';
        if($params['pstatus'] == 1 || !$params['pstatus']){
            $where  = ' and (pay_points <> 0)';
        }
        if($params['pstatus'] == 2){
            $where  = ' and (pay_points > 0)';
        }
        if($params['pstatus'] == 3){
            $where  = ' and (pay_points < 0)';
        }
        $data['status'] = 'succ';
//        $sql = "select * from ecs_account_log where user_id = " . intval($params['member_id']) . $where;
        $orderby =" order by change_time desc";
        $sql = "select * from ecs_account_log where user_id = " . intval($params['member_id']).$orderby;
        $data['point_list'] = $this->db->getAll($sql);
        foreach($data['point_list'] as $key => $item){
            $data['point_list'][$key]['change_time'] = date('Y-m-d H:i:s',$item['change_time']);
        }
        
        $sql = "select pay_points ,rank_points,agency_level from ecs_users where user_id = " . intval($params['member_id']);
        $data['point_count'] = $this->db->getRow($sql);
        
        $sql = "select points_limit from  ecs_user_agency  where agency_id = '". $data['point_count']['agency_level']."'";
        $ctmp = $this->db->getRow($sql);
        $data['point_count']['wait_points'] = $ctmp['points_limit'] - $data['point_count']['pay_points'];

        return $data;
    }
    
    public function get_member_wallet($params){
        $sql = "select agency_level,user_money,frozen_money  from ecs_users where user_id = " . intval($params['member_id']);
        $userinfo = $this->db->getRow($sql);
        if($userinfo['agency_level'] < 2){
            /**
                等级大于2，开启钱包，否则关闭钱包
            **/
            $return['show_status'] = 'false';
        }else{
            $return['show_status'] = 'true';
        }
        $return['status'] = 'succ';
        $return['user_info'] = $userinfo;
        
        
        return $return;
    }
    
    public function set_id_verify($params){
        $sql = "update ecs_users set real_name='".$params['real_name']."',id_card='".$params['id_card']."'  where user_id = " . intval($params['member_id']);
        $userinfo = $this->db->getAll($sql);
        $return['status'] = 'succ';
        $return['message'] = '设置成功';
        $return['user_info'] = $userinfo;
        return $return;
    }
    
    public function get_verifycode($params){
        $vcode = rand(1000,9999);
        $add_time = time();
        $expire_time = time()+15*60;
        $sql = "delete from ecs_user_vcode where mobile = '".$params['mobile']."'";
        $this->db->query($sql);
        $sql = "insert into ecs_user_vcode (mobile,vcode,add_time,expire_time) values (
            '".$params['mobile']."',
            '".$vcode."',
            '".$add_time."',
            '".$expire_time."')
        ";
        $this->db->query($sql);
        $return['status'] = 'succ';
        $return['message'] = '发送成功，请接收验证短信';
        $return['user_info'] = $userinfo;
        return $return;
    }
    
    public function set_mobile_card($params){
        $return['status'] = 'fail';
        $sql = "select * from ecs_user_vcode where mobile = '".$params['mobile']."'";
        $tmp = $this->db->getRow($sql);
        
        if($tmp['vcode'] != $params['vcode']){
            $return['message'] = '验证码错误，请重新填写';
            return $return;
        }
        $sql = "update ecs_users set mobile_phone='".$params['mobile']."',bank_card='".$params['bank_card']."'  where user_id = " . intval($params['member_id']);
        $userinfo = $this->db->getAll($sql);
        
        $return['status'] = 'succ';
        $return['message'] = '验证成功';
        return $return;
    }
    
    public function set_transfer_log($params){
        $return['status'] = 'fail';
        $sql = "select * from ecs_user_vcode where mobile = '".$params['mobile']."'";
        $tmp = $this->db->getRow($sql);
        
        if($tmp['vcode'] != $params['vcode']){
            $return['message'] = '验证码错误，请重新填写';
            return $return;
        }
        
        $sql = "select pay_password from ecs_users where user_id = " . intval($params['member_id']);
        $userinfo = $this->db->getRow($sql);

        if($userinfo['pay_password'] != md5($params['pay_password'])){
            $data['status'] = 'fail';
            $data['message'] = '支付密码错误，请重试';
            return $data;
        }
        
        $sql = "insert into ecs_transfer_log ( card_id,user_id,bank_name,amount,tocard_no,tobank_name,real_name,id_card,mobile,add_time) values (
            '".$_POST['card_id']."',
            '".$_POST['member_id']."',
            '".$_POST['bank_name']."',
            '".$_POST['amount']."',
            '".$_POST['tocard_no']."',
            '".$_POST['tobank_name']."',
            '".$_POST['real_name']."',
            '".$_POST['id_card']."',
            '".$_POST['mobile']."',
            '".$_POST['add_time']."'
        )";
        $this->db->query($sql);
        $return['status'] = 'succ';
        $return['response']['id'] = $this->db->insert_id();
        $return['message'] = '成功';
        return $return;
    }
    
    public function get_transfer_info($params){
        $sql = "select * from ecs_transfer_log where tid='".$params['tid']."'";
        $tmp = $this->db->getRow($sql);
        $return['status'] = 'succ';
        
        $tmp['bank_str'] = $tmp['tobank_name'].$this->get_bank_str($tmp['card_no']);
        $tmp['amount'] ='￥'.$tmp['amount'];

        $return['response']['transfer_info'] = $tmp;
        $return['message'] = '成功';
        return $return;
    }
    
    public function check_verifycode($params){
        $return['status'] = 'fail';
        $sql = "select * from ecs_user_vcode where mobile = '".$params['mobile']."'";
        $tmp = $this->db->getRow($sql);
        
        if($tmp['vcode'] != $params['vcode']){
            $return['message'] = '验证码错误，请重新填写';
            return $return;
        }
        $return['status'] = 'true';
        $return['message'] = '验证码正确';
        return $return;
    }
    
    public function set_pay_password($params){
        $sql = "update ecs_users set pay_password='".md5($params['pay_password'])."'  where user_id = " . intval($params['member_id']);
        $userinfo = $this->db->getAll($sql);
        $return['status'] = 'succ';
        $return['message'] = '设置成功';
        $return['user_info'] = $userinfo;
        return $return;
    }
    
    public function set_pay_newpassword($params){
        $sql = "select user_name  from ecs_users where user_id = " . intval($params['member_id']);
        $userinfo = $this->db->getRow($sql);
        $user = init_users();

        $res = $user->check_user($userinfo['user_name'], $parmas['password']);
        if(!$res){
            $return['status'] = 'fail';
            $return['message'] = '密码错误';
            return $return;
        }
        $sql = "update ecs_users set pay_password='".md5($params['pay_password'])."'  where user_id = " . intval($params['member_id']);
        $userinfo = $this->db->getAll($sql);
        $return['status'] = 'succ';
        $return['message'] = '设置成功';
        $return['user_info'] = $userinfo;
        return $return;
    }
    
    public function get_record($params){
        //change_type 0为充值，1为提现，2为管理员调节，99为其他类型
        $data['status'] = 'succ';
        $sql = "select * from ecs_account_log where (user_money != 0 ) and user_id = " . intval($params['member_id']);
        $data['record_list'] = $this->db->getAll($sql);
        foreach($data['record_list'] as $key => $item){
            $data['record_list'][$key]['change_time'] = date('Y-m-d H:i:s',$item['change_time']);
            if($item['user_money'] > 0){
                $data['record_list'][$key]['str'] = '充值';
            }else{
                $data['record_list'][$key]['str'] = '消费';
            }
        }
        return $data;
    }
    
    public function get_bankcard_detail($params){
        $data['status'] = 'succ';
        $sql = "select * from ecs_bankcard where user_id = '" . intval($params['member_id'])."' and card_id='".$params['card_id']."'";
        $tmp = $this->db->getRow($sql);
        if(!$tmp){
            $data['status'] = 'fail';
            $data['message'] = '无此银行卡';
            return $data;
        }
        
        $data['bankinfo'] = $tmp;

        if($data['bankinfo']['card_type'] == 'deposit'){
            $data['bankinfo']['card_type'] = '储值卡';
        }else{
            $data['bankinfo']['card_type']  = '信用卡';
        }
        
        return $data;
    }
    
    public function del_bank_card($params){
        $data['status'] = 'succ';
        $sql = "delete from ecs_bankcard where user_id = '" . intval($params['member_id'])."' and card_id='".$params['card_id']."'";
        var_dump($sql);
        $this->db->query($sql);
        $data['message'] = '解除成功';
        return $data;
    }
    
    public function get_bankcard_list($params){
        
        if($params['card_id'] != ''){
            $where = " and card_id != '".$params['card_id']."'";
        }
        $data['status'] = 'succ';
        $sql = "select * from ecs_bankcard where user_id = " . intval($params['member_id']).$where;
       
        
        $data['bankcard_list'] = $this->db->getAll($sql);
        foreach( $data['bankcard_list'] as $key => $item){
            if($item['card_type'] == 'deposit'){
                $data['bankcard_list'][$key]['card_type'] = '储值卡';
            }else{
                $data['bankcard_list'][$key]['card_type'] = '信用卡';
            }
            $data['bankcard_list'][$key]['bank_str'] = $data['bankcard_list'][$key]['bank_name'].$data['bankcard_list'][$key]['card_type'].$this->get_bank_str( $data['bankcard_list'][$key]['card_no']);
        }
        
        return $data;
    }
    
    public function add_bankcard($params){
        $data['status'] = 'succ';
        $data['response'] = $params;
        if($params['card_id'] == ''){
            $sql = "insert into ecs_bankcard (user_id,name,card_no,bank_name,card_type) values(
                '".$params['member_id']."',
                '".$params['name']."',
                '".$params['card_no']."',
                '".$params['bank_name']."',
                '".$params['card_type']."'
            )";
            $this->db->query($sql);
            $data['response']['card_id'] = $this->db->insert_id();
        }else{
            $sql = "update ecs_bankcard set 
            name='".$params['name']."',
            card_no='".$params['card_no']."',
            bank_name='".$params['bank_name']."',
            card_type='".$params['card_type']."'
            where user_id='".$parmas['member_id']."' and card_id = '".$parmas['card_id']."'";
            $this->db->query($sql);
            $data['response']['card_id'] = $parmas['card_id'];
        }

        return $data;
    }
    
    public function set_mobile_bank($params){
        $return['status'] = 'fail';
        $sql = "select * from ecs_user_vcode where mobile = '".$params['mobile']."'";
        $tmp = $this->db->getRow($sql);
        
        if($tmp['vcode'] != $params['vcode']){
            $return['message'] = '验证码错误，请重新填写';
            return $return;
        }
        $data['status'] = 'succ';
        $sql = "update ecs_bankcard set mobile = '".$params['mobile']."',bind_status='true' where card_id='".$params['card_id']."'";
        $this->db->query($sql);

        $data['message'] = '绑定成功';
        return $data;
    }
    
    public function get_member_withdraw($params){
        $data['status'] = 'succ';
        $sql = "select * from ecs_bankcard where user_id = " . intval($params['member_id']) ." and bind_status='true' order by card_id desc limit 0,1";
       
        $tmp =  $this->db->getRow($sql);
        if(!$tmp){
            $data['status'] = 'fail';
            if($params['lang'] == 'en'){
            $data['message'] = 'Please bind the bank card';
            }else{
            $data['message'] = '请绑定银行卡';
            }
            
            return $data;
        }
        $data['bankcard'] =$tmp;
        if($data['bankcard']['card_type'] == 'deposit'){
             if($params['lang'] == 'en'){
                $data['bankcard']['card_type'] = 'Stored value card';
            }else{
                $data['bankcard']['card_type'] = '储值卡';
            }
            
        }else{
            if($params['lang'] == 'en'){
                 $data['bankcard']['card_type'] = 'credit card';
            }else{
                $data['bankcard']['card_type'] = '信用卡';
            }
           
        }
        
        $data['bankcard']['str'] = $data['bankcard']['bank_name'].' '.$this->get_bank_str($data['bankcard']['card_no']);

        $sql = "select agency_level,user_money,frozen_money  from ecs_users where user_id = " . intval($params['member_id']);
        $userinfo = $this->db->getRow($sql);
        $data['userinfo'] = $userinfo;
        $data['userinfo']['money_str'] = '钱包余额 ￥'.$userinfo['user_money'];
        return $data;
    }
    
    public function set_member_withdraw($params){
        $sql = "select agency_level,user_money,frozen_money,pay_password from ecs_users where user_id = " . intval($params['member_id']);
        $userinfo = $this->db->getRow($sql);
        if($params['money'] > $userinfo['user_money']){
            $data['status'] = 'fail';
            $data['message'] = '输入金额大于账户金额';
            return $data;
        }
        
        if($userinfo['pay_password'] == ''){
            $data['status'] = 'fail';
            $data['message'] = '支付密码为空，请设置';
            return $data;
        }
        if($userinfo['pay_password'] != md5($params['pay_password'])){
            $data['status'] = 'fail';
            $data['message'] = '支付密码错误，请重试';
            return $data;
        }
        
        
        $sql = "select * from ecs_bankcard where user_id = " . intval($params['member_id']) ." and bind_status='true' and card_id='".$params['card_id']."'";
       
        $tmp =  $this->db->getRow($sql);
        
        $sql = "insert into ecs_user_account(user_id,amount,add_time,payment,process_type,card_id) values (
            ".$params['member_id'].",
            ".$params['money'].",
            ".time().",
            '".$tmp['bank_name'].'-'.$tmp['card_no']."',
            '1',
            ".$params['card_id']."
        )";
        $this->db->query($sql);
        $data['status'] = 'succ';
        $data['response']['id'] = $this->db->insert_id();;
        $data['message'] = '申请提现成功';
        return $data;
    }
    
    function get_member_withdraw_info($params){
        $sql = "select * from ecs_user_account where user_id = " . intval($params['member_id']) ."  and `id`='".intval($params['id'])."'";
        $tmp =  $this->db->getRow($sql);
        
        $sql = "select * from ecs_bankcard where user_id = " . intval($params['member_id']) ." and bind_status='true' and card_id='".$tmp['card_id']."'";
       
        $ctmp =  $this->db->getRow($sql);

        $tmp['bankcard_str'] = $ctmp['bank_name'].' '.$this->get_bank_str($ctmp['card_no']);

        $data['response'] = $tmp;
        $data['status'] = 'succ';
        return $data;
    }
    
    
    
    function get_bank_str($string){
        return '**** **** **** '.substr($string,-4);
    }

    public function save_userid_image(){
        $image = new cls_image($_CFG['bgcolor']);
        $img_up_info = basename($image->upload_image($_FILES['ad_img'], 'useridimage'));
        $ad_code = "ad_code = '".$img_up_info."'".',';
    }
    
    public function get_my_team($params){
        $where = '';
        if($params['agency_level'] != ''){
            $where = " and agency_level='".$params['agency_level']."'";
        }
        
        $sql = "select user_name,mobile_phone,user_id,agency_level from ecs_users where parent_id='".$params['member_id']."'" . $where;
        $ctmp =  $this->db->getAll($sql);
        
        $lv_list = array();
        foreach($ctmp as $key=>$item){
            if(!in_array($lv_list,$item['agency_level'])){
                $lv_list[] = $item['agency_level'];
            }
        }
        

        $sql = "select agency_name,agency_id from ecs_user_agency";
        $atmp =  $this->db->getAll($sql);
        
        foreach($ctmp as $key=>$item){
            foreach($atmp as $i => $j){
                if($item['agency_level'] == $j['agency_id']){
                    $ctmp[$key]['level_name'] = $j['agency_name'];
                }
            }
        }
        
        
        
        $data['status'] = 'succ';
        $data['user_list']= $ctmp;
        $data['level_list']= $atmp;
        $data['all_count']= count($ctmp);
        return $data;
    }

    public function change_password($params)
    {

        $data = array('status'=>'fail', 'message'=>'', 'response' => '');
        if (empty($params['password_old'])) {
            if($params['lang'] == 'en'){
                $msg = 'Please enter the original password';
            }else{
                $msg = '请输入原密码';
            }
            
            $data['message'] = $msg;
            return $data;
        }
        $user = init_users();
        $res = $user->check_user($params['username'], $params['password_old']);
        if (!$res) {
             if($params['lang'] == 'en'){
                $msg = 'The original password is incorrect. Please try again.';
            }else{
                $msg = '原密码有误，请重试';
            }
            
            $data['message'] = $msg;
            return $data;
        }
        if (empty($params['password_new']) || (int)strlen($params['password_new']) < 6) {
               if($params['lang'] == 'en'){
                $msg = 'The new password is incorrect. Please enter a six-digit password.';
            }else{
                $msg = '新密码有误，请输入六位密码';
            }
            
            $data['message'] = $msg;
            return $data;
        }
        if (empty($params['password_repeat']) || $params['password_new']!=$params['password_repeat']) {
               if($params['lang'] == 'en'){
                $msg = 'Duplicate passwords are inconsistent, please re-enter';
            }else{
                $msg = '重复密码不一致，请重新输入';
            }
            
            $data['message'] = $msg;
            return $data;
        }

        if (!$user->edit_user(array('username'=>$params['username'], 'password'=>$params['password_new']),1)) {
            if($params['lang'] == 'en'){
                $msg = 'The modification failed, please try again';
            }else{
                $msg = '修改失败，请重试';
            }
            $msg = '修改失败，请重试';
            $data['message'] = $msg;
            return $data;
        }
        if(!empty($params['password_new'])) {
            $sql="UPDATE ecs_users SET `ec_salt`='0' WHERE user_name= '".$params['username']."'";
            $this->db->getRow($sql);
        }
        $data = array('status'=>'succ', 'message'=>'修改成功', 'response' => '');
        return $this->langTransReturn($data,$parmas['lang']);
        // return $data;
    }

    /* 获取分成列表 */
    public function member_get_affiliate($params)
    {
        $res = array(
            'status'=>'fail',
            'message'=>'',
            'response'=>array(),
        );
        $user_id = $params['member_id'];
        if($user_id<=0){
            $res['message'] = '请先登陆';
            return $res;
        }
        $sql = "select * from ecs_affiliate_log where user_id='".$user_id."' order by time desc";
        $data = $this->db->getAll($sql);
        foreach ($data as $k=>$v){
            $sql = "select a.*,b.mobile_phone from ecs_order_info a left join ecs_users b on a.user_id=b.user_id where a.order_id='".$v['order_id']."'";
            $data[$k]['order_info'] = $this->db->getRow($sql);
            $data[$k]['time'] = date('Y-m-d',$data[$k]['time']);
            if($data[$k]['separate_type']=='0'){
                $data[$k]['order_type'] = '推荐注册分成';
            }else{
                $data[$k]['order_type'] = '推荐订单分成';
            }
        }
        if($data){
            $res['status'] = 'succ';
            $res['response'] = $data;
            return $res;
        }else{
            $res['status'] = 'succ';
            return $res;
        }
    }


  /***************************************好友相关******************************************************/

    /* 搜索好友 */
    public function select_friend($params)
    {
        $res = array(
            'status'=>'fail',
            'message'=>'',
            'response'=>array(),
        );
        $content = $params['content'];
        $sql = "select * from ecs_users ";
        $filter_sql = array(
            'user_name', 'mobile_phone',
        );
        $data = array();
        foreach ($filter_sql as $k=>$v){
            $select_sql = $sql." where ".$filter_sql[$k]." = '".$content."'";
            $result = $this->db->getRow($select_sql);
            if($result){
                $data[$k] = $result;
            }
        }
        $data = array_values($data);
        if($data){
            $res['status'] = 'succ';
            $res['response'] = $data;
            return $res;
        }else{
            $res['message'] = '查无此人';
            return $res;
        }
        
        
    }

    /* 好友详情 */
    public function user_info($params)
    {
        $res = array(
            'status'=>'fail',
            'message'=>'',
            'response'=>array(),
        );
        $user_id = $params['user_id'];
        $sql = "select * from ecs_users where user_id= '".$user_id."'";
        $data = $this->db->getRow($sql);
        if($data){
            $res['status'] ='succ';
            $res['response'] = $data;
            return $res;
        }else{
            return $res;
        }
    }

    /* 创建好友分组 */
    public function create_group($params)
    {
        $res = array(
            'status'=>'fail',
            'message'=>'',
            'response'=>array(),
        );
        $user_id = $params['member_id'];
        $group_name = $params['name'];
        $group_name = addslashes(htmlspecialchars($group_name));
        $sql = "select * from ecs_users_groups where group_name='".$group_name."' and user_id='".$user_id."'";
        $check_group_name = $this->db->getRow($sql);
        if($check_group_name['group_name']==$group_name){
            $res['message'] = 'group name is exists';
            return $res;
        }
        $sql = "insert into ecs_users_groups (user_id,group_name,created_at) values ('".$user_id."','".$group_name."','".time()."')";
        $data = $this->db->query($sql);
        if($data){
            $res['status'] = 'succ';
            $res['message'] = '创建成功';
            return $res;
        }else{
            $res['message'] = 'insert database failed';
            return $res;
        }
    }

    /* 添加好友 */
    public function insert_friend($params)
    {
        $res = array(
            'status'=>'fail',
            'message'=>'',
            'response'=>array(),
        );
        $user_id = $params['member_id'];
        $friend_id = $params['friend_id'];
        $group_id = $params['group_id'];

        $sql = "select * from ecs_users_friends where user_id='".$user_id."' and friend_id='".$friend_id."'";
        $check_friend = $this->db->getRow($sql);
        if($check_friend){
            $res['message'] = '已添加过该好友';
            return $res;
        }
        if($user_id == $friend_id){
            $res['message'] = '不能添加自己';
            return $res;
        }
        if(!$group_id) $group_id='0';
        $sql = "insert into ecs_users_friends (user_id,group_id,friend_id,created_at) values (".$user_id.",".$group_id.",".$friend_id.",".time().")";
        $data = $this->db->query($sql);

        /* 被添加人自动添加该好友 */
        $sql = "insert into ecs_users_friends (user_id,group_id,friend_id,created_at) values (".$friend_id.",'0',".$user_id.",".time().")";
        $this->db->query($sql);

        if($data){
            $res['status'] = 'succ';
            $res['message'] = '添加成功';
            return $res;
        }else{
            $res['message'] = 'insert database failed';
            return $res;
        }
    }

    /* 分组列表*/
    public function member_get_group($params)
    {
        $res = array(
            'status' => 'fail',
            'message'=>'',
            'response'=>array(),
        );
        $user_id = $params['member_id'];
        $sql = "select * from ecs_users_groups where user_id='".$user_id."' order by created_at asc";
        $data = $this->db->getAll($sql);
        $merge_arr = array(
            array('group_id'=>'0','user_id'=>$user_id,'group_name'=>'我的好友'),
        );
        $data = array_merge($merge_arr,$data);
//        $arr = array();
//        foreach ($data as $k=>$v){
//            $arr[$v['group_name']]=$data[$k]['friends'];
//        }
        if($data){
            $res['status'] = 'succ';
            $res['response'] = $data;
            return $res;
        }else{
            $res['status'] = 'succ';
            $res['message'] = $data;
            return $res;
        }
    }

    /* 好友列表 */
    public function member_get_friends($params)
    {
        $res = array(
            'status' => 'fail',
            'message'=>'',
            'response'=>array(),
        );
        $data = $this->member_get_group($params);
        $data = $data['response'];

        foreach ($data as $k=>$v){
            $sql = "select a.*,b.user_name,b.user_pic from ecs_users_friends a left join ecs_users b on a.friend_id=b.user_id where a.group_id='".$v['group_id']."' and a.user_id='".$params['member_id']."'";
            $data[$k]['friends']= $this->db->getAll($sql);
            foreach ($data[$k]['friends'] as $k1=>$v1){
                $sql = "select * from ecs_user_chat where response_id='".$v1['friend_id']."' and user_id='".$v['user_id']."' order by last_modify desc";
                $chat_res1 = $this->db->getRow($sql);
                $sql = "select * from ecs_user_chat where user_id='".$v1['friend_id']."' and response_id='".$v['user_id']."' order by last_modify desc";
                $chat_res2 = $this->db->getRow($sql);
                if($chat_res1['last_modify'] > $chat_res2['last_modify']){
                    $chat = $chat_res1['content'];
                }else{
                    $chat = $chat_res2['content'];
                }
                $chat ? $chat : $chat="";
                $data[$k]['friends'][$k1]['last'] = $chat;
                /* 计算有多少条未读消息 */
                $sql = "select count(id) count from ecs_user_chat where user_id='".$v1['friend_id']."' and response_id='".$v1['user_id']."' and see_type='0'";
                $count = $this->db->getRow($sql);
                $count = intval($count['count']);
                $data[$k]['friends'][$k1]['count'] = $count;
            }
        }

        $arr = array();
        foreach ($data as $k=>$v){
            /* 不加空格会影响返回值的排序 */
            $arr[' '.$v['group_name']]['list'] = array_values($data[$k]['friends']);
        }
        foreach ($arr as $k=>$v){
            if($k=='我的好友'){
                $arr[$k]['selected']=true;
            }else{
                $arr[$k]['selected']=false;
            }
        }
        /* 新消息放在最上面 */
        foreach ($arr as $k=>$v){
            $arr[$k]['list'] = $this->arraySequence($arr[$k]['list'], 'count', $sort = 'SORT_DESC');
        }
        if($arr){
            $res['status'] ='succ';
            $res['response'] = $arr;
            return $res;
        }else{
            $res['status'] ='succ';
            $res['response'] = $arr;
            return $res;
        }
    }

    /* 删除好友 */
    public function delete_friend($params)
    {
        $res = array(
            'status'=>'fail',
            'message'=>'',
            'response'=>array(),
        );
        $user_id = $params['member_id'];
        $friend_id = $params['friend_id'];
        if(!($user_id && $friend_id)){
            $res['message']='参数错误';
            return $res;
        }
        $sql = "delete * from ecs_users_friends where user_id='".$user_id."' and friend_id='".$friend_id."'";
        $data = $this->db->query($sql);
        if($data){
            $res['status']='succ';
            return $data;
        }else{
            $res['message'] = 'delete database failed';
            return $res;
        }
    }

    /* 更新分组 */
    public function update_group($params)
    {
        $res = array(
            'status'=>'fail',
            'message'=>'',
            'response'=>'',
        );
        $name = $params['group_name'];
        $id = $params['group_id'];
        if(!$name){
            $res['message'] = 'name is required';
            return $res;
        }
        if($id==0){
            $res['message'] = '此分组不能更改';
            return $res;
        }
        $name = addslashes(htmlspecialchars($name));
        $sql = "update ecs_users_groups set group_name='".$name."' where group_id='".$id."'";
        $data = $this->db->query($sql);
        if($data){
            $res['status'] = 'succ';
            $res['message'] = 'success';
            return $res;
        }else{
            $res['message'] = 'update database failed';
            return $res;
        }
    }

    /* 移动好友分组 */
    public function update_friend_group($params)
    {
        $res = array(
            'status'=>'fail',
            'message'=>'',
            'response'=>array(),
        );
        $friend_id = $params['friend_id'];
        $user_id = $params['member_id'];
        $group_name = $params['group_name'];
        $sql = "select * from ecs_users_groups where user_id='".$user_id."' and group_name='".$group_name."'";
        $group_id = $this->db->getRow($sql);
        $group_id = $group_id['group_id'];
        if(!$group_id) $group_id='0';
        $sql = "update  ecs_users_friends set group_id='".$group_id."' where user_id='".$user_id."' and friend_id='".$friend_id."'";
        $data = $this->db->query($sql);
        if($data){
            $res['status'] = 'succ';
            $res['message'] = 'successfully removed';
            return $res;
        }else{
            $res['message'] = 'update database failed';
            return $res;
        }
    }

    /* 删除分组 */
    public function delete_group($params)
    {
        $res = array(
            'status'=>'fail',
            'message'=>'',
            'response'=>'',
        );
        $id = $params['group_id'];
        if($id==0){
            $res['message'] ='此分组不能删除';
            return $res;
        }
        $sql = "delete  from ecs_users_groups where group_id ='".$id."'";
        $data1 = $this->db->query($sql);
        $sql = "update ecs_users_friends set group_id='0' where group_id='".$id."'";
        $data2 = $this->db->query($sql);
        if($data1 && $data2){
            $res['status'] = 'succ';
            $res['message'] = 'successfully deleted';
            return $res;
        }else{
            $res['message'] = 'delete database failed';
            return $res;
        }
    }

    /* 二维数组排序 */
    public function arraySequence($array, $field, $sort = 'SORT_DESC')
    {
        $arrSort = array();
        foreach ($array as $uniqid => $row) {
            foreach ($row as $key => $value) {
                $arrSort[$key][$uniqid] = $value;
            }
        }
        array_multisort($arrSort[$field], constant($sort), $array);
        return $array;
    }

    /***********************tab栏相关************************************/
    public function member_get_tab($params)
    {
        $res = array(
            'status'=>'fail',
            'message'=>'',
            'response'=>array(),
        );
        $user_id = $params['member_id'];
        /* 好友消息 */
        $sql = "select count(id) count from ecs_user_chat where response_id='".$user_id."' and see_type='0'";
        $user_count = $this->db->getRow($sql);
        $user_count = intval($user_count['count']);
        /* 客服消息 */
        $sql ="select count(id) count from ecs_chat where user_id='".$user_id."' and see_type='0' and per_type='1'";
        $staff_count = $this->db->getRow($sql);
        $staff_count = intval($staff_count['count']);
        /* 系统消息 */
        $sql = "select * from ecs_users where user_id='".$user_id."'";
        $article = $this->db->getRow($sql);
        $article_count = $article['article_num'];
        $data = array(
            array('name'=>'物流','count'=>0),
            array('name'=>'系统','count'=>$article_count),
            array('name'=>'客服','count'=>$staff_count),
            array('name'=>'好友','count'=>$user_count),
        );
        $res['status'] ='succ';
        $res['response'] = $data;
        return $res;
    }

    public function get_system_notification($params)
    {
        $res =array(
            'status'=>'fail',
            'message'=>'',
            'response'=>array()
        );
        $user_id = $params['member_id'];
        /* 更新未读文章数量 */
        // $sql = "update ecs_users set article_num='0' where user_id='".$user_id."'";
        // $this->db->query($sql);
        $sql  = "select  cat_id from  ecs_article_cat where cat_name like '%系统消息%'";
        $cat_id = $this->db->getRow($sql);
        if(empty($cat_id)){
            $res =array(
            'status'=>'fail',
            'message'=>'暂无数据',
            'response'=>array()
        );

        return $res;
        }
        $sql = "select * from ecs_article where cat_id=".$cat_id['cat_id'];
        $article = $this->db->getAll($sql);
        foreach ($article as $k=>$v){
            $article[$k]['add_time'] = date('Y-m-d',$v['add_time']);
        }
        if(!$article){
            $article = array();
        }
        $sql = "select * from ecs_users where user_id='".$user_id."'";
        $count = $this->db->getRow($sql);
        $count = $count['article_num'];
        $res['status'] = 'succ';
        // $res['response']= array('count'=>$count,'params'=>$article);
        $res['response']= $article;
        return $res;
    }

    public function create_member_qrcode($params)
    {
        $res = array(
            'status' => 'fail',
            'message' => '',
            'response' => array(),
        );
        $user_id = $params['member_id'];
        $url = "http://" . $_SERVER['HTTP_HOST'] . "androdiapp/www/main.html#/register?u_id=" . $user_id;
//        $url = $user_id;

        $qrCode = new QrCode();
        if( !$data )
        {
            $data = $qrCode
                ->setText($url)
                ->setSize(200)
                ->setPadding(0)
                ->setErrorCorrection(1)
                ->setForegroundColor(array('r' => 0, 'g' => 0, 'b' => 0, 'a' => 0))
                ->setBackgroundColor(array('r' => 255, 'g' => 255, 'b' => 255, 'a' => 0))
                ->setLabelFontSize(16)
                ->getDataUri('png');

        }
//        echo "<img width='200' height='200' src='".$data."'/> ";
        if ($data ){
            $res['status'] = 'succ';
            $res['response'] = $data;
            return $res;
        } else {
            $res['message'] = "生成二维码图片失败";
            return $res;
        }
    }

    /* 修改用户个人信息 */
    public function edit_member_info($params)
    {
        $res = array(
            'status'=>'fail',
            'message'=>'不可以为空',
            'response' => array(),
        );
//        isset($params['real_name']) ? $where=" real_name='".$params['real_name']."'" :
//        isset($params['sex']) ? $where="sex='".$params['sex']."'" :
//        isset($params['birthday']) ? $where="birthday='".$params['birthday']."'" :
//        isset($params['pay_password']) ? $where="pay_password='".$params['pay_password']."'" : $where='';
        if(!empty($params['real_name'])){
            $where=" real_name='".$params['real_name']."'";
        }elseif(!empty($params['sex'])){
            $where="sex='".$params['sex']."'";
        }elseif(!empty($params['birthday'])){
            $where="birthday='".$params['birthday']."'" ;
        }elseif(!empty($params['pay_password'])){
            $where="pay_password='".$params['pay_password']."'" ;
        }elseif(!empty($params['email'])){
            $where="email='".$params['email']."'" ;
        }elseif(!empty($params['phone'])){
            $where="mobile_phone='".$params['phone']."'" ;
        }
        if(empty($where)){
            $res['status'] = 'succ';
            return $res;
        }
        if(isset($params['pay_password'])){
            if(strlen($params['pay_password'])!='6'){
                $res['message'] ='支付密码必须为6位';
                return $res;
            }
        }
        $sql = "update ecs_users set ".$where." where user_id='".$params['member_id']."'";
        $data = $this->db->query($sql);
        if($data){
            $res['status'] = 'succ';
            $res['message'] = '修改成功';
            return $res;
        }else{
            $res['message'] = 'update database failed';
            return $res;
        }

    }

    public function create_pdf()
    {
//        $name='sda';
//        shell_exec("wkhtmltopdf http://www.wapu.com/androdiapp/www/main.html#/wapu_receipt  ".$_SERVER['DOCUMENT_ROOT']."outpdf/".$name.".pdf");
//        die;
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator('wapu');

        $pdf->SetAuthor('wapu');

        $pdf->SetTitle('wapu');

        $pdf->SetSubject('TCPDF Tutorial');

        $pdf->SetKeywords('TCPDF, PDF, PHP');
        $pdf->SetHeaderData(PDF_HEADER_LOGO,PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 038', PDF_HEADER_STRING);
        $pdf->SetHeaderData('./logo.jpg', 30, 'wapu', '韦博', array(0,64,255), array(0,64,128));
        $pdf->setFooterData(array(0,64,0), array(0,64,128));
        $pdf->setHeaderFont(Array('stsongstdlight', '', '10'));
        $pdf->setFooterFont(Array('helvetica', '', '8'));
        $pdf->SetDefaultMonospacedFont('courier');
        $pdf->SetDefaultMonospacedFont('courier');
        $pdf->SetMargins(15, 27, 15);
        $pdf->SetHeaderMargin(5);
        $pdf->SetFooterMargin(10);
        $pdf->AddPage();
        $pdf->SetFont('stsongstdlight', '', 12);
        $sql = "select * from ecs_users where user_id='3'";
        $data = $this->db->getRow($sql);
        $user_name = $data['user_name'];
        $orders = $this->get_post_order('3');
        $str = '';
        $price=0;
        $orders = array_values($orders);
        foreach ($orders as $k=>$v){
            $price +=$v['goods_amount'];
            $sql = "select * from ecs_order_goods where order_id='".$v['order_id']."'";
            $goods = $this->db->getRow($sql);
            $goods_name = $goods['goods_name'];
            $no = $k+1;
            $str .= "<tr><td>".$no."</td><td>订单号:".$v['order_sn'].'    商品名称:'.$goods_name."</td><td></td><td></td><td></td></tr>";
        }
        $contents ='
        <!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <table  border="1">
        <tr><th>订单</th></tr>
        '.$str.'
        <tr><th>总价</th></tr>
        <tr><td>'.$price.'</td></tr>
</table>
</body>
</html>
        ';

        $html = '<!DOCTYPE html>
<html ng-app="B2C">
<head>
<meta charset="UTF-8">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache">
<meta name="format-detection" content="telephone=no">
<meta name="msapplication-tap-highlight" content="no">

<link rel="stylesheet" type="text/css" href="http://www.wapu.com/mapi/pdf/http://www.wapu.com/mapi/pdf/ionic.min.css">

<link rel="stylesheet" type="text/css" href="http://www.wapu.com/mapi/pdf/http://www.wapu.com/mapi/pdf/base.css">
<script src="jquery.min.js"></script>

<script>var jq=jQuery.noConflict();</script>

<script>
	 var _devicePixelRatio = window.devicePixelRatio > 1.51 ? window.devicePixelRatio : 1;
            var scale = 1/_devicePixelRatio;
            document.write(\'<meta name="viewport" content="target-densitydpi=device-dpi, width=device-width, user-scalable=no, initial-scale=1, maximum-scale=\'+scale+\', minimum-scale=\'+scale+\'" />\');
</script>
<title>ecshop</title>
</head>
<body>
<div main="main" style="height:100%;overflow-x:hidden">
<ion-view class="wapu_receipt" ng-controller="wapu_receiptCtrl">
    <ion-content class="wapuReceiptScroll">
        <section>
            <img src="//s01-vip.xypcdn.com/201812/12/14364837196" class="logo">
                <div class="simple">
                    <p>Company Reg. No. 201822606N</p>
                    <p>GST Reg. No. 201822606N</p>
                </div>
            <!--</div>-->

            <div class="tax">
                <h2 class="title">
                    TAX INVOICE
                </h2>
                <table>
                    <thead>
                        <tr>
                            <th style="width:32%" class="first">Invoice No.</th>
                            <th>WP/IN/00005</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="first">Issue Date</td>
                            <td>21/9/2018</td>
                        </tr>
                        <tr>
                            <td class="first">Customer P.O.</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="first">Payment Term</td>
                            <td>Sales Person</td>
                        </tr>
                        <tr>
                            <td class="first">Issue Date</td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="bt_st">
                <div class="bt">
                    <h3 class="title">
                        Bill To
                    </h3>
                    <p>ABC Pte Ltd</p>
                    <p>Blk XXX</p>
                    <p>#XX-OO</p>
                    <p>Singapore XXXXX</p>
                    <p>Attn:</p>
                    <p>Tel:</p>

                </div>
                <div class="st">
                    <h3 class="title">
                        Ship To
                    </h3>
                    <p>Blk 289-A Bukit Batok St 25</p>
                    <p>#15-220</p>
                    <p>Singapore 650289</p>
                    <p>ATTN:</p>
                    <p>TEL:</p>
                </div>
            </div>
            <div class="pt_list">
                <table>
                    <thead>
                        <tr>
                            <th style="width:2.9036rem">S/N</th>
                            <th>DESCRIPTION</th>
                            <th style="width:2.9036rem">QTY</th>
                            <th style="width:5.124rem">U/P</th>
                            <th style="width:6.832rem">AMOUNT S$</th>
                        </tr>
                    </thead>
                    <tbody>
                        '.$str.'
                    </tbody>
                    <tfoot>
                        <tr>
                            <td style="border:none;" colspan="3"></td>
                            <td style="white-space: nowrap;">Subtotal</td>
                            <td>123</td>
                        </tr>
                        <tr>
                            <td style="border:none;" colspan="3"></td>
                            <td style="white-space: nowrap;">GST(7%)</td>
                            <td>123</td>
                        </tr>
                        <tr>
                            <td style="border:none;" colspan="3"></td>
                            <td style="white-space: nowrap;">Total Due</td>
                            <td>'.$price.'</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <h6>Thank You For Your Business!</h6>
            <div class="cus_copy">
                <div>Customer Copy</div>
            </div>
            <div class="cginsr">
                Computer Generated Invoice No Signature Required.
            </div>
        </section>
    </ion-content>
</ion-view>
</div>
</body>
<style type="text/css">
.loading{font-family:"Open Sans"}
.wapuReceiptScroll{top:3.7576rem;}
.wapu_receipt section{padding:0 0.854rem 1.281rem;}
.wapu_receipt .logo{padding:0.854rem 0;display: block;}
.wapu_receipt h3.title{font-size:1.281rem;font-weight: 600;line-height: 1.5}
.wapu_receipt .cp_infor{margin-bottom: 1.281rem}
.wapu_receipt .cp_infor .groups{font-size:1.1956rem;display: flex;align-content: center;line-height: 1.8788rem}
.wapu_receipt .cp_infor .groups .label{width:5.978rem;display: flex;justify-content:space-between;}
.wapu_receipt .cp_infor .groups .label:after{content:":";padding-right: .3416rem}
.wapu_receipt .cp_infor .groups .infor{word-break: break-all;width:0;flex:1;}
.wapu_receipt .simple p{font-size:1.1956rem;line-height: 1.8788rem}
.wapu_receipt h2.title{font-size: 1.708rem;font-weight: 600;margin-bottom: .3416rem}
.wapu_receipt th,.wapu_receipt td{border:0.0854rem solid #333;vertical-align: middle;}
.wapu_receipt table{width: 100%}
.wapu_receipt .tax{margin-bottom: 1.281rem}
.wapu_receipt .tax th,.wapu_receipt .tax td{font-size:1.1956rem;line-height: 1.281rem;padding:0 .854rem;word-break: break-all;}
.wapu_receipt .tax th{text-align: left;padding:1.024rem .6832rem;}
.wapu_receipt .tax .first{padding:.5124rem .3416rem;}
.wapu_receipt .bt_st{display: flex;}
.wapu_receipt .bt{width:50%;padding-right:.427rem;}
.wapu_receipt .st{width:50%;padding-left:.427rem;}
.wapu_receipt .bt_st p{font-size:1.1102rem;line-height: 1.8788rem}
.wapu_receipt .bt_st .title{margin-bottom: .2562rem}
.wapu_receipt .pt_list th{padding:0.1708rem 0;}
.wapu_receipt .pt_list td{padding: .3416rem;text-align: center;word-break: break-all;}
.wapu_receipt .pt_list{margin-bottom: 1.281rem}
.wapu_receipt .bank_infor .groups{font-size: 1.1956rem;line-height: 1.5372rem;display: flex;align-content: center;padding:.1708rem 0;}
.wapu_receipt .bank_infor .infor{word-break: break-all;width:0;flex:1;padding-left:.2562rem;}
.wapu_receipt h6{margin-top:1.0248rem;font-size:1.3664rem;text-align: center;line-height: 2;font-weight: 600}
.wapu_receipt .cus_copy{padding:.5124rem 0;display: flex;flex-direction: row-reverse}
.wapu_receipt .cus_copy div{height:1.8788;line-height:1.708rem;padding:0 .6832rem;border:0.0854rem solid #666;}
.wapu_receipt .cginsr{text-align: center;margin:.6832rem 0;font-size:1.1102rem;color:#333;}
</style>


<script src="http://www.wapu.com/mapi/pdf/index.js"></script>
<script src="http://www.wapu.com/mapi/pdf/ionic.bundle.min.js"></script>
<script src="http://www.wapu.com/mapi/pdf/angular-route.min.js"></script>
<script src="http://www.wapu.com/mapi/pdf/angular-translate.min.js"></script>
<script src="http://www.wapu.com/mapi/pdf/angular-touch.min.js"></script>

<script src="http://www.wapu.com/mapi/pdf/script.js"></script>
<script src="http://www.wapu.com/mapi/pdf/global.js"></script>
<script src="http://www.wapu.com/mapi/pdf/app_route.js"></script>
';
        echo $html;
        $name = md5(time());
        shell_exec("wkhtmltopdf http://www.wapu.com/mapi/api.php?select=select ".$_SERVER['DOCUMENT_ROOT']."outpdf/".$name.".pdf");
//        $name='sda';
//        shell_exec("wkhtmltopdf http://www.wapu.com/androdiapp/www/main.html#/wapu_receipt  ".$_SERVER['DOCUMENT_ROOT']."outpdf/".$name.".pdf");
//        shell_exec("wkhtmltopdf http://www.wapu.com/mapi/pdf/main.html F:we1.pdf");
//        die;
//        $filename = 'c:/www/wapu/ecshop/mapi/pdf/main.html';
//        $handle = fopen($filename, "r");//读取二进制文件时，需要将第二个参数设置成'rb'
////通过filesize获得文件大小，将整个文件一下子读到一个字符串中
//        $contents = fread($handle, filesize ($filename));
//        fclose($handle);
//
//        $pdf->writeHTML($contents, true, false, true, false, '');
//        $name = md5(time());
//        $pdf->Output($_SERVER['DOCUMENT_ROOT'].'outpdf/'.$name.'.pdf', 'F');
        include_once ($_SERVER['DOCUMENT_ROOT'].'mapi/phpmailer/Email.php');

        $email = 'xuzhicheng@yin-duo.com';
        $attachment = $_SERVER['DOCUMENT_ROOT'].'outpdf/'.$name.'.pdf';
        $attachment_name = $user_name.'的订单.pdf';
        $config = $this->get_mail_setting();
        Email::send($config,$email,'由wapu发送的用户订单邮件','这是您的订单，请查收附件pdf档',$attachment,$attachment_name);
    }

    public function html_to_pdf($params)
    {
        // $res = [
        //     'status'=>'fail',
        //     'message'=>'',
        //     'response'=>array(),
        // ];
        $user_id = $params['member_id'];
        $sql = "select * from ecs_users where user_id='".$user_id."'";
        $data = $this->db->getRow($sql);
        $email = $data['email'];
        if(!$email){
            $res['message'] = '请设置您的邮箱';
            return $res;
        }
        $sql ="select * from ecs_order_info where order_status='1' and post_type='0' and user_id='".$user_id."'";
        $check_post = $this->db->getRow($sql);
        if(!$check_post){
            $res['message'] = '暂无可领取订单';
            return $res;
        }

        $sql  = "select * from ecs_users_email where create_status='0' or post_type='0' and user_id='".$user_id."'";
        $check_create = $this->db->getRow($sql);
        if($check_create){
            $res['message'] = '您有邮件正在生成哦';
            return $res;
        }

        $user_name = $data['user_name'];
        $orders = $this->get_post_order($user_id);
        $str = '';
        $price=0;
        $orders = array_values($orders);
        foreach ($orders as $k=>$v){
            $price +=$v['goods_amount'];
            $sql = "select * from ecs_order_goods where order_id='".$v['order_id']."'";
            $goods = $this->db->getRow($sql);
            $goods_name = $goods['goods_name'];
            $no = $k+1;
            $str .= "<tr><td>".$no."</td><td>订单号:".$v['order_sn'].'    商品名称:'.$goods_name."</td><td></td><td></td><td></td></tr>";


            /* 更改订单发送状态 */
            $sql = "update ecs_order_info set post_type='1' where order_id='".$v['order_id']."'";
            $this->db->query($sql);
        }

        $now = date('d/m/Y',time());
//        $res = $this->get_html($now,$str,$price);
//        echo $res;
        $time= time();
        $date = date('Y-m-d',$time);
        $name = $date.$user_name;

        $pdf_name = md5(time().$user_id);
        $sql = "insert into ecs_users_email (user_id,name,post_type,create_status,created_at) values ('".$user_id."','".$pdf_name."','0','0','".time()."')";
        $data = $this->db->query($sql);
        if($data){
            $res['status'] = 'succ';
            $res['message'] = '申请成功';
            return  $res;
        }else{
            $res['message'] = '申请失败';
            return $res;
        }
    }

    public function get_html($now,$str,$price)
    {
        $html = '<!DOCTYPE html>
<html ng-app="B2C">
<head>
<meta charset="UTF-8">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache">
<meta name="format-detection" content="telephone=no">
<meta name="msapplication-tap-highlight" content="no">

<link rel="stylesheet" type="text/css" href="http://www.wapu.com/mapi/pdf/http://www.wapu.com/mapi/pdf/ionic.min.css">

<link rel="stylesheet" type="text/css" href="http://www.wapu.com/mapi/pdf/http://www.wapu.com/mapi/pdf/base.css">
<script src="jquery.min.js"></script>

<script>var jq=jQuery.noConflict();</script>

<script>
	 var _devicePixelRatio = window.devicePixelRatio > 1.51 ? window.devicePixelRatio : 1;
            var scale = 1/_devicePixelRatio;
            document.write(\'<meta name="viewport" content="target-densitydpi=device-dpi, width=device-width, user-scalable=no, initial-scale=1, maximum-scale=\'+scale+\', minimum-scale=\'+scale+\'" />\');
</script>
<title>ecshop</title>
</head>
<body>
<div main="main" style="height:100%;overflow-x:hidden">
<ion-view class="wapu_receipt" ng-controller="wapu_receiptCtrl">
    <ion-content class="wapuReceiptScroll">
        <section>
            <img src="//s01-vip.xypcdn.com/201812/12/14364837196" class="logo">
                <div class="simple">
                    <p>Company Reg. No. 201822606N</p>
                    <p>GST Reg. No. 201822606N</p>
                </div>
            <!--</div>-->

            <div class="tax">
                <h2 class="title">
                    TAX INVOICE
                </h2>
                <table>
                    <thead>
                        <tr>
                            <th style="width:32%" class="first">Invoice No.</th>
                            <th>WP/IN/00005</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="first">Issue Date</td>
                            <td>'.$now.'</td>
                        </tr>
                        <tr>
                            <td class="first">Customer P.O.</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="first">Payment Term</td>
                            <td>Sales Person</td>
                        </tr>
                        <tr>
                            <td class="first">Issue Date</td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="bt_st">
                <div class="bt">
                    <h3 class="title">
                        Bill To
                    </h3>
                    <p>ABC Pte Ltd</p>
                    <p>Blk XXX</p>
                    <p>#XX-OO</p>
                    <p>Singapore XXXXX</p>
                    <p>Attn:</p>
                    <p>Tel:</p>

                </div>
                <div class="st">
                    <h3 class="title">
                        Ship To
                    </h3>
                    <p>Blk 289-A Bukit Batok St 25</p>
                    <p>#15-220</p>
                    <p>Singapore 650289</p>
                    <p>ATTN:</p>
                    <p>TEL:</p>
                </div>
            </div>
            <div class="pt_list">
                <table>
                    <thead>
                        <tr>
                            <th style="width:2.9036rem">S/N</th>
                            <th>DESCRIPTION</th>
                            <th style="width:2.9036rem">QTY</th>
                            <th style="width:5.124rem">U/P</th>
                            <th style="width:6.832rem">AMOUNT S$</th>
                        </tr>
                    </thead>
                    <tbody>
                        '.$str.'
                    </tbody>
                    <tfoot>
                        <tr>
                            <td style="border:none;" colspan="3"></td>
                            <td style="white-space: nowrap;">Subtotal</td>
                            <td>123</td>
                        </tr>
                        <tr>
                            <td style="border:none;" colspan="3"></td>
                            <td style="white-space: nowrap;">GST(7%)</td>
                            <td>123</td>
                        </tr>
                        <tr>
                            <td style="border:none;" colspan="3"></td>
                            <td style="white-space: nowrap;">Total Due</td>
                            <td>'.$price.'</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <h6>Thank You For Your Business!</h6>
            <div class="cus_copy">
                <div>Customer Copy</div>
            </div>
            <div class="cginsr">
                Computer Generated Invoice No Signature Required.
            </div>
        </section>
    </ion-content>
</ion-view>
</div>
</body>
<style type="text/css">
.loading{font-family:"Open Sans"}
.wapuReceiptScroll{top:3.7576rem;}
.wapu_receipt section{padding:0 0.854rem 1.281rem;}
.wapu_receipt .logo{padding:0.854rem 0;display: block;}
.wapu_receipt h3.title{font-size:1.281rem;font-weight: 600;line-height: 1.5}
.wapu_receipt .cp_infor{margin-bottom: 1.281rem}
.wapu_receipt .cp_infor .groups{font-size:1.1956rem;display: flex;align-content: center;line-height: 1.8788rem}
.wapu_receipt .cp_infor .groups .label{width:5.978rem;display: flex;justify-content:space-between;}
.wapu_receipt .cp_infor .groups .label:after{content:":";padding-right: .3416rem}
.wapu_receipt .cp_infor .groups .infor{word-break: break-all;width:0;flex:1;}
.wapu_receipt .simple p{font-size:1.1956rem;line-height: 1.8788rem}
.wapu_receipt h2.title{font-size: 1.708rem;font-weight: 600;margin-bottom: .3416rem}
.wapu_receipt th,.wapu_receipt td{border:0.0854rem solid #333;vertical-align: middle;}
.wapu_receipt table{width: 100%}
.wapu_receipt .tax{margin-bottom: 1.281rem}
.wapu_receipt .tax th,.wapu_receipt .tax td{font-size:1.1956rem;line-height: 1.281rem;padding:0 .854rem;word-break: break-all;}
.wapu_receipt .tax th{text-align: left;padding:1.024rem .6832rem;}
.wapu_receipt .tax .first{padding:.5124rem .3416rem;}
.wapu_receipt .bt_st{display: flex;}
.wapu_receipt .bt{width:50%;padding-right:.427rem;}
.wapu_receipt .st{width:50%;padding-left:.427rem;}
.wapu_receipt .bt_st p{font-size:1.1102rem;line-height: 1.8788rem}
.wapu_receipt .bt_st .title{margin-bottom: .2562rem}
.wapu_receipt .pt_list th{padding:0.1708rem 0;}
.wapu_receipt .pt_list td{padding: .3416rem;text-align: center;word-break: break-all;}
.wapu_receipt .pt_list{margin-bottom: 1.281rem}
.wapu_receipt .bank_infor .groups{font-size: 1.1956rem;line-height: 1.5372rem;display: flex;align-content: center;padding:.1708rem 0;}
.wapu_receipt .bank_infor .infor{word-break: break-all;width:0;flex:1;padding-left:.2562rem;}
.wapu_receipt h6{margin-top:1.0248rem;font-size:1.3664rem;text-align: center;line-height: 2;font-weight: 600}
.wapu_receipt .cus_copy{padding:.5124rem 0;display: flex;flex-direction: row-reverse}
.wapu_receipt .cus_copy div{height:1.8788;line-height:1.708rem;padding:0 .6832rem;border:0.0854rem solid #666;}
.wapu_receipt .cginsr{text-align: center;margin:.6832rem 0;font-size:1.1102rem;color:#333;}
</style>


<script src="http://www.wapu.com/mapi/pdf/index.js"></script>
<script src="http://www.wapu.com/mapi/pdf/ionic.bundle.min.js"></script>
<script src="http://www.wapu.com/mapi/pdf/angular-route.min.js"></script>
<script src="http://www.wapu.com/mapi/pdf/angular-translate.min.js"></script>
<script src="http://www.wapu.com/mapi/pdf/angular-touch.min.js"></script>

<script src="http://www.wapu.com/mapi/pdf/script.js"></script>
<script src="http://www.wapu.com/mapi/pdf/global.js"></script>
<script src="http://www.wapu.com/mapi/pdf/app_route.js"></script>
';
        echo $html;
    }
    
    //设置默认地址
    function save_default_member_addr($params){

        $sql = "update ecs_user_address set def_addr='0'  where user_id = " . intval($params['member_id']);

        $default_member_addr = $this->db->getAll($sql);

        $sql = "update ecs_user_address set def_addr='1'  where user_id = '" . intval($params['member_id'])."' and address_id = " . intval($params['ship_id']);

        $default_member_addr = $this->db->getAll($sql);

        $return['status'] = 'succ';
        $return['message'] = '设置成功';
        return $return;
    }

    /* 获取用户未发送邮件订单 */
    public function get_post_order($user_id)
    {
        $sql = "select * from ecs_order_info where order_status='1' and user_id='".$user_id."' and post_type='0'";
        $data = $this->db->getAll($sql);
        return $data;
    }

    /* 获取邮箱配置 */
    public function get_mail_setting()
    {
        $sql = "select * from ecs_shop_config where code in ('smtp_host','smtp_port','smtp_user','smtp_pass','smtp_mail')";
        $res = $GLOBALS['db']->getAll($sql);
        $data = array();
        foreach ($res as $k=>$v){
            if($v['code']=='smtp_host') $data['smtp_host'] = $v['value'];
            if($v['code']=='smtp_port') $data['smtp_port'] = $v['value'];
            if($v['code']=='smtp_user') $data['smtp_user'] = $v['value'];
            if($v['code']=='smtp_pass') $data['smtp_pass'] = $v['value'];
            if($v['code']=='smtp_mail') $data['smtp_mail'] = $v['value'];
        }

        // return [
        //     'host' => $data['smtp_host'],
        //     'port' => $data['smtp_port'],
        //     'username' =>$data['smtp_user'] ,
        //     'password' => $data['smtp_pass'],
        //     'send'=>'wapu',
        // ];

    }

    public function get_reg_fields($params)
    {
        $res = array(
            'status'=>'fail',
            'message'=> '',
            'response'=>array(),
        );
        $user_id = $params['member_id'];
        if(!$user_id){
            $res['message'] = '用户id不能为空';
            return  $res;
        }


        $sql = "select a.*,b.content from ecs_reg_fields a left outer join ecs_reg_extend_info b on a.id=b.reg_field_id where b.user_id='".$user_id."' and a.display=1";
        $data = $this->db->getAll($sql);
        foreach ($data as  $k=>$v){
            if($v['content']==null){
                $data[$k]['content'] = '';
            }
        }
        if($data){
            $res['status']='succ';
            $res['response'] = $data;
            return $res;
        }
    }

      public function save_reg_fields($params)
    {
        $res = array(
            'status'=>'fail',
            'message'=>'',
            'response'=>array(),
        );
        $ids = substr($params['ids'],'0',strlen($params['ids'])-1);
        $ids_arr = explode(',',$ids);
        $contents = substr($params['contents'],'0',strlen($params['contents'])-1);
        $contents_arr = explode(',',$contents);
        $user_id = $params['member_id'];
        if(empty($user_id)){
            $res['message'] = '用户id不能为空';
            return $res;
        }


        $sql = "select mobile_phone from ecs_users where user_id='".$user_id."'";
        $mobile = $this->db->getRow($sql);
        if(!$mobile['mobile_phone']){
            $res['message'] = '请先设置上方手机号';
            return  $res;
        }

        $result= array();
        foreach ($ids_arr as $key => $info) {
            $result[$key]['id'] = $info;
        }
        foreach ($contents_arr as $key => $info) {
            $result[$key]['content'] = $info;
        }
        foreach($result as $k=>$v){
            if($v['id']=='100'){
                $check_params['p_code']=$v['content'];
            }
            if($v['id']=='101'){
                $check_params['s_code'] = $v['content'];
            }
        }

        $sql = "select * from pay_config where pay_key='key'";
        $key = $this->db->getRow($sql);
        $check_params['key'] = $key['key'];
        if($check_params['s_code'] || $check_params['p_code']){
            $sql = "select * from pay_config where pay_key='url'";
            $url = $this->db->getRow($sql);
            $url = $url['key'];

            $uri['verification'] = 'Api/Api/SecurityCode.ashx';
            $check_response = $this->check_ky_pay($uri['verification'],$url,$check_params);

            if($check_response['respCode'] != 'Success'){
                $res['message'] = '云卡通卡号安全码错误或卡片不可用';
                return $res;
            }
        }



        foreach ($result as $k=>$v){
            $sql = "select * from ecs_reg_extend_info where user_id='".$user_id."' and reg_field_id='".$v['id']."'";
            $reg_field = $this->db->getRow($sql);
            if(!$reg_field){
                $sql = "insert into ecs_reg_extend_info (user_id,reg_field_id,content) values ('".$user_id."','".$v['id']."','".$v['content']."')";
                $this->db->query($sql);
            }else{
                $sql = "update ecs_reg_extend_info set content='".$v['content']."' where user_id='".$user_id."' and reg_field_id='".$v['id']."'";
                $this->db->query($sql);
            }
        }
        $res['status'] = 'succ';
        $res['message'] = '保存成功';
        return $res;
    }


    /* 凯宇检查卡号安全码  */
    public function check_ky_pay($uri,$url,$params)
    {
        $url = $url.$uri;
        $response = $this->curl($url,$params);
        return $response;
    }

    public function curl($url,$post_data)
    {
        $ch = curl_init(); //初始化curl
        curl_setopt($ch, CURLOPT_URL, $url);//设置链接
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//设置是否返回信息
        curl_setopt($ch, CURLOPT_POST, 1);//设置为POST方式
        curl_setopt($ch, CURLOPT_HEADER, 0);//设置是否返回头信息

        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);//POST数据
        $response = curl_exec($ch);//接收返回信息
//        if (curl_errno($ch)) {//出错则显示错误信息
//            print curl_error($ch);
//        }
        curl_close($ch); //关闭curl链接

        $response = json_decode($response,true);
        return $response;
    }

    public function pointcount($params){
        $sql = "select * from ecs_users where user_id = '".$params['member_id']."'";
        $data = $this->db->getRow($sql);
        
        $object_sql = "select * from ecs_cart where session_id = '".SESS_ID."' and rec_type = ".CART_GENERAL_GOODS;
        $cart_info = $GLOBALS['db']->GetAll($object_sql);
        
        foreach( $cart_info as $key=>$item){
            $total_amount = bcmul($item['goods_number'], $item['goods_price'], 3) + $total_amount ;
        }
        $data['total_amount'] = $total_amount;
        
        $data = array('status'=>'succ', 'message'=>'修改成功', 'response' => $data);
        return $data;
    }

      /*评价数据*/
    public function pingjia($params){
        $page = $params['page'];
        $psize = $params['psize'];
        if(empty($page) || empty($psize)){
            $page = '1';
            $psize = '5';
        }
        $offset = ($params['page']-1) * $params['psize'];
        $sql = "select a.*,b.user_name,b.user_pic from ecs_user_review a  left join ecs_users b on a.user_id=b.user_id where a.goods_id = ".$params['goods_id'] ." order by a.created_at desc limit ".$offset.",".$psize;

        $sql = "select a.*,b.user_name,b.user_pic from ecs_comment as a left join ecs_users b on a.user_id=b.user_id where a.id_value ='".$params['goods_id']."' and a.status='1' order by a.add_time desc limit ".$offset.",".$psize;

        $data = $this->db->getAll($sql);
        foreach ($data as $k=>$v){
            if($data[$k]['user_pic']){
                $data[$k]['user_pic'] = "http://".$_SERVER['HTTP_HOST']."/".$data[$k]['user_pic'];
            }else{
                $data[$k]['user_pic'] = './img/u2922.png';
            }
            $data[$k]['score'] = ceil($v['score']);
        }
        $sql = "select count(id) as total from ecs_user_review where goods_id='".$params['goods_id']."'";
        $sql = "select count(comment_id) as total from ecs_comment where id_value='".$params['goods_id']."' and status='1'";
        $total = $this->db->getRow($sql);
        $total = $total['total'];
        $page = ceil($total / $psize);
        $arr = array(
            'total'=>ceil($total),
            'params'=>$data,
            'page'=>$page
        );
        if($total>'0'){
            $res = array(
                'status'=>'succ',
                'message'=>'',
                'response'=>$arr
            );
        }else{
            $res = array(
                'status'=>'succ',
                'message'=>'',
                'response'=>array(
                    'total'=>'0',
                    'params'=>array(),
                    'page'=>'0',
                ),
            );
        }

        return $res;
    }


     /* 用户评价 */

    public function user_review($params)
    {
        $res = array(
            'status'=>'fail',
            'message'=>'',
            'response'=>array(),
        );
        // $goods_id = $params['goods_id'];
        $user_id  = $params['member_id'];
        $content = $params['content'];
        // $pic_url = $params['pic_url'];
        // $score = $params['score'];
        $clsaa = $params['clsaa'];
        $order_id  = $params['order_id'];

        $sql = "select order_id from ecs_order_info where order_sn = ".$order_id;
        $orders_id = $this->db->getRow($sql);
        $sql = "select goods_id from ecs_order_goods where order_id = ".$orders_id['order_id'];
        $goods_id = $this->db->getAll($sql);
        foreach ($goods_id as $k => $v) {
            $sql = "select * from ecs_order_goods where goods_id='".$v['goods_id']."' and order_id='".$orders_id['order_id']."'";
            $order_goods = $this->db->getRow($sql);
            if($order_goods['is_review']=='1'){
                $res['message'] = '您已经评价过了';
                return $res;
            }


//            $sql  =  "insert into ecs_user_review (goods_id,user_id,content,score,created_at) values ('".$v['goods_id']."','".$user_id."','".$content."','".$clsaa."','".time()."')";
//            $review = $this->db->query($sql);
            $ip = $_SERVER['116.237.134.36'];
            $sql = "Select * from ecs_users where user_id='".$params['member_id']."'";
            $user = $this->db->getRow($sql);
            $email = $user['email'];
            $user_name = $user['user_name'];
            $comment =  array(
                'id_value'=>$v['goods_id'],
                'email' =>$email,
                'user_name'=>$user_name,
                'content'=>$params['content'],
                'comment_rank'=>$clsaa,
                'add_time'=>time(),
                'ip_address'=>$ip,
                'status'=>'0',
                'user_id'=>$params['member_id']
            );

            $review = $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('comment'), $comment, 'INSERT');

//            $sql = "insert into ecs_comment (id_value,email,user_name,content,comment_rank,add_time,ip_address,status,user_id) values ()";
//            $this->db->getRow($sql);

            /* 更新用户评价积分以及人数 */
            $sql = "update ecs_goods set review_sum = review_sum+1 ,review_score = review_score+1 where goods_id='".$v['goods_id']."'";
            $this->db->query($sql);
            $sql = "update ecs_order_goods set is_review = 1  where goods_id='".$v['goods_id']."' and order_id =".$orders_id['order_id'];
            $this->db->query($sql);

        }

        if($review){
            $res['status']='succ';
            $res['message']='评价成功,需管理员审核后给予显示';
            return $res;
        }else{
            $res['message'] = 'insert database failed';
            return $res;
        }
        // $sql = "select * from ecs_order_goods where goods_id='".$goods_id."' and order_id='".$order_id."'";
        //    $order_goods = $this->db->getRow($sql);
        //    if($order_goods['is_review']=='1'){
        //    $res['message'] = '您已经评价过了';
        //    return $res;
        //    }

        // $sql = "select * from ecs_order_goods where goods_id='".$goods_id."' and order_id='".$order_id."'";
        // $order_goods = $this->db->getRow($sql);
        // if($order_goods['is_review']=='1'){
        //     $res['message'] = '您已经评价过了';
        //     return $res;
        // }

        // $sql  =  "insert into ecs_user_review (goods_id,user_id,content,pic_url,score,created_at) values ('".$goods_id."','".$user_id."','".$content."','".$pic_url."','".$score."','".time()."')";

        // $review = $this->db->query($sql);

        // /* 更新用户评价积分以及人数 */
        // $sql = "update ecs_goods set review_sum = review_sum+1 ,review_score = review_score+".$score." where goods_id='".$goods_id."'";
        // $this->db->query($sql);

        // if($review){
        //     $res['status']='succ';
        //     $res['message']='评价成功';
        //     return $res;
        // }else{
        //     $res['message'] = 'insert database failed';
        //     return $res;
        // }
    }



    public function get_card($params)
    {
        $res = [
            'status'=>'fail',
            'message'=>'',
            'response'=>array(),
        ];

        $sql = "select * from ecs_reg_extend_info where user_id='".$params['member_id']."' and reg_field_id='100'";
        $p_code = $this->db->getRow($sql);
        $p_code = $p_code['content'];
        $sql = "select * from ecs_reg_extend_info where user_id='".$params['member_id']."' and reg_field_id='101'";
        $s_code = $this->db->getRow($sql);
        $s_code = $s_code['content'];
        if($s_code && $p_code){
            $res['status'] = 'succ';
            $res['response'] = [
                'p_code'=>$p_code,
                's_code'=>$s_code
            ];
            return $res;
        }else{
            $res['message'] = '请填写相关信息';
            return $res;
        }
    }


    public function change_payment($params)
    {
        $pay_id = $params['pay_id'];
        $pay_name = $params['pay_name'];
        $order_id = $params['order_id'];
        $sql = "update ecs_order_info set pay_id='".$pay_id."',pay_name='".$pay_name."' where order_sn ='".$order_id."'";
        if($this->db->query($sql))
        return [
            'status'=>'succ',
            'message'=>'更换成功',
            'response'=>[],
        ];
        return [
            'status'=>'fail',
            'message'=>'更换失败',
            'response'=>[],
        ];
    }

    //用户使用积分
    public  function  get_member_balance($params){

        $sql = "select pay_points from ecs_users where user_id= ".$params['member_id'];
        $pay_points = $this ->db->getRow($sql);
        if($pay_points['pay_points']<$params['balance']){
            $res['status']='fail';
            $res['message']='积分不够';
            return $res;
        }
        $sql = "select value from ecs_shop_config where code ='integral_scale'";
        $data =  $this->db->getRow($sql);
        $sum = $params['balance']*$data['value'];
        $res['status']='succ';
        $res['message']='使用成功';
        $res['response']=$sum;
        return $res;

    }


    public function delete_cart($params)
    {
        $res = array(
            'status'=>'fail',
            'message'=>'',
            'response'=>'',
        );
        if(empty($params['goods_ids'])){
            $res['message'] = '参数错误';
            return $res;
        }
        $goods_ids = explode(',',$params['goods_ids']);
        foreach ($goods_ids as $k=>$v){
            $sql = "delete from ecs_cart where goods_id='".$v."' and user_id='".$params['member_id']."'";
            $result = $this->db->query($sql);
            if(!$result){
                $res['message'] = "update database failed";
                return $res;
            }
        }
        $res['status'] = 'succ';
        $res['message'] = '删除成功';
        return $res;
    }

    public function batch_deletion($params)
    {
        $res = array(
            'status'=>'fail',
            'message'=>'',
            'response'=>'',
        );
        $sql = "delete from ecs_cart where user_id='".$params['member_id']."' and is_check='true'";
        $result = $this->db->query($sql);
        if($result){
            $res['status'] = 'succ';
            $res['message'] = '删除成功';
            return $res;
        }else{
            $res['message'] = 'update database failed';
            return $res;
        }
    }
    //end_class
}
