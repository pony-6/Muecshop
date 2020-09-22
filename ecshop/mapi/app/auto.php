<?php

class auto extends apiclass
{
    protected $order;
    function __construct()
    {
        $sql = "select * from ecs_money_orders where order_status='false'";

        $this->order = $GLOBALS['db']->getAll($sql);
    }
    //todo 待优化
    
    public function update($order)
    {
            $sql ="select parent_id from ecs_users where user_id=1";
        $parent_id = $GLOBALS['db']->getRow($sql);
        echo '<pre>';var_dump($parent_id);exit;
        $order = $this->order;
        $time = time();
        foreach ($order as $k=>$v){
            $parent_id = $this->go_up($v['user_id']);
            if($parent_id == $v['parent_id'] && $time - $v['lastmodify'] > (86400 *2)){
                $this->up_money_order($order['order_id']);
            }elseif ($time+(86400 *2) < $time && $time < $time+(86400 *3)){
                $sql ="select * from ecs_users where user_id='".$v['parent_id']."'";
                $one_p = $GLOBALS['db']->getRow($sql);
                $sql = "update ecs_order_info set parent_id='".$one_p."' where order_id='".$order['order_id']."'";
                $GLOBALS['db']->query($sql);
                $this->up_money_order($order['order_id']);
            }elseif ($time+(86400 *3) < $time && $time < $time+(86400 *4)){

                $sql ="select * from ecs_users where user_id='".$v['parent_id']."'";
                $two_p = $GLOBALS['db']->getRow($sql);
                $sql = "update ecs_order_info set parent_id='".$two_p."' where order_id='".$order['order_id']."'";
                $GLOBALS['db']->query($sql);
                $sql = "update ecs_money_orders set parent_id='".$two_p."' where order_id='".$order['order_id']."'";
                $GLOBALS['db']->query($sql);
                $this->up_money_order($order['order_id']);
            }elseif ($time+(86400 *4) < $time && $time < $time+(86400 *5)){
                $sql ="select * from ecs_users where user_id='".$v['parent_id']."'";
                $three_p = $GLOBALS['db']->getRow($sql);
                $sql = "update ecs_order_info set parent_id='".$three_p."' where order_id='".$order['order_id']."'";
                $GLOBALS['db']->query($sql);
                $this->up_money_order($order['order_id']);
            }elseif ($time+(86400 *5) < $time && $time < $time+(86400 *6)){
                $sql ="select * from ecs_users where user_id='".$v['parent_id']."'";
                $four_p = $GLOBALS['db']->getRow($sql);
                $sql = "update ecs_order_info set parent_id='".$four_p."' where order_id='".$order['order_id']."'";
                $GLOBALS['db']->query($sql);
                $this->up_money_order($order['order_id']);
            }
        }
    }

    public function go_up($user_id)
    {
        $sql ="select parent_id from ecs_users where user_id='".$user_id."'";
        $parent_id = $GLOBALS['db']->getRow($sql);
        if($parent_id){
            return $parent_id['parent_id'];
        }else{
            return false;
        }
    }

    function up_money_orders($order_id)
    {
        $sql="select * from ".$GLOBALS['ecs']->table('order_info')." where order_id='".$order_id."'";
        $order = $GLOBALS['db']->getRow($sql);
        $user_id = $order['user_id'];
        $parent_id = $order['parent_id'];
        $sql = "select * from ".$GLOBALS['ecs']->table('users')." where user_id ='".$parent_id."'";
        $parent_spec = $GLOBALS['db']->getRow($sql);
        if($order['order_amount']=='0'){
            $order['order_amount'] = $order['surplus'];
        }
        /* 判断此人是否有上级 */
        if($parent_id == '0'){
            /* 判断此订单是否是积分商品 */
            if($order['g_type'] == '0'){
                //TODO 应该是不做任何改变,相当于从系统买
            }elseif ($order['g_type']=='1'){
//            var_dump(123);die;
                /* 改变用户购买平均价格 */
                make_average_price($order_id);

                /* 获取当前订单得到的积分 */
                $get_points = check_goods_points($order_id);

                $sql ="update ".$GLOBALS['ecs']->table('users')." set pay_points = pay_points+".$get_points." where user_id ='".$user_id."'";
                $GLOBALS['db']->query($sql);

                /* 改变订单状态为已收货 */
                $sql ="update ecs_order_info set shipping_status=".SS_RECEIVED." where order_id='".$order_id."'";
                $GLOBALS['db']->query($sql);

                /* 插入日志 */
                $msg = $order['order_sn'].'订单购买赠送';
                insert_account_log($user_id,-$order['order_amount'],$get_points,ACT_SYS_GIVE,$msg);
            }
        }

        /* 计算存在父级 */
        if($parent_id > 0 ){
            /* 获取当前订单得到的积分 */
            $get_points = check_goods_points($order_id);
            $p_points = $parent_spec['pay_points'];
            /* 判断会员等级 */
            if(check_level($user_id,$parent_id)){
                /* 判断用户平均采购价格 */
                if(check_average_price($order_id)){
                    if($p_points >= $get_points){
                        if($order['g_type']=='0'){
                            //父亲日志
                            $msg = $order['order_sn'].'补货自动兑换';
                            insert_account_log($parent_id,'0',-$get_points,ACT_STOCK,$msg);
                        }elseif($order['g_type']=='1'){
                            //当前用户日志
                            $msg = $order['order_sn'].'购买商品赠送积分';
                            insert_account_log($user_id,-$order['order_amount'],$get_points,ACT_GIVE,$msg);
                            //更新用户积分
                            $sql = "update ecs_users set pay_points= pay_points+".$get_points." where user_id='".$user_id."'";
                            $GLOBALS['db']->query($sql);
                            //父亲日志
                            $msg = $order['order_sn'].'补货自动兑换';
                            insert_account_log($parent_id,'0',-$get_points,ACT_STOCK,$msg);
                            //更新父亲积分
                            $sql = "update ecs_users set pay_points= pay_points-".$get_points." where user_id='".$parent_id."'";
                            $GLOBALS['db']->query($sql);
                            /* 改变订单状态为已收货 */
                            $sql ="update ecs_order_info set shipping_status=".SS_RECEIVED." where order_id='".$order_id."'";
                            $GLOBALS['db']->query($sql);
                        }
                        /* 改变返利订单状态 */
                        $sql ="update ecs_money_orders set order_status='succ' where order_id='".$order_id."'";
                        $GLOBALS['db']->query($sql);
                        /* 改变用户平均购买价格 */
                        make_average_price($order_id);
                    }else{
                        /* 改变订单为待补货 */
                        $sql ="update ecs_order_info set order_status=".OS_STOCK." where order_id='".$order_id."'";
                        $GLOBALS['db']->query($sql);
                        message_push($parent_id,$order_id);
                        $sql ="update ecs_money_orders set order_status='false' where order_id='".$order_id."'";
                        $GLOBALS['db']->query($sql);
                    }
                }else{
                    /* 改变订单为待补货 */
                    $sql ="update ecs_order_info set order_status=".OS_STOCK." where order_id='".$order_id."'";
                    $GLOBALS['db']->query($sql);
                    message_push($parent_id,$order_id);
                    $sql ="update ecs_money_orders set order_status='false' where order_id='".$order_id."'";
                    $GLOBALS['db']->query($sql);
                }
            }else{
                /* 改变订单为待补货 */
                $sql ="update ecs_order_info set order_status=".OS_STOCK." where order_id='".$order_id."'";
                $GLOBALS['db']->query($sql);
                message_push($parent_id,$order_id);

                /* 父级需要升级 */
                $sql ="update ecs_money_orders set order_status='false',parent_status='1' where order_id='".$order_id."'";
                $GLOBALS['db']->query($sql);
            }
        }
    }

    public function new_message_push($parent_id,$order_id){
        $sql = "select * from ".$GLOBALS['ecs']->table('wechat_push')." where user_id='".$parent_id."' and order_id='".$order_id."'";
        $res =  $GLOBALS['db']->getRow($sql);
        if(!$res){
            $sql = "insert into ".$GLOBALS['ecs']->table('wechat_push')." (user_id,order_id,lastmodify) values ('".$parent_id."','".$order_id."','".time()."')";
            $GLOBALS['db']->query($sql);
        }
        $sql = "select * from ".$GLOBALS['ecs']->table('sms_push')." where user_id='".$parent_id."' and order_id='".$order_id."'";
        $res =  $GLOBALS['db']->getRow($sql);
        if(!$res){
            $sql = "insert into ".$GLOBALS['ecs']->table('sms_push')." (user_id,order_id,lastmodify) values ('".$parent_id."','".$order_id."','".time()."')";
            $GLOBALS['db']->query($sql);
        }
    }

}


