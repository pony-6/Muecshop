<?php
/**
    @author     :   dev@yin-duo.com
    @since      :   2018/10/11
**/
class tools extends apiclass
{
    /**
     * 取消订单接口
     */   
    function cancel_order($params){
    /* 查询订单信息，检查状态 */
    $sql = "SELECT user_id, order_id, order_sn , surplus , integral , bonus_id, order_status, shipping_status, pay_status FROM " .$GLOBALS['ecs']->table('order_info') ." WHERE order_sn = ".$params['order_id'];

    $order = $GLOBALS['db']->GetRow($sql);

    if (empty($order))
    {
        return array('status'=>'fail', 'message'=>'订单号不存在', 'response' =>'');
    }

    // 订单状态只能是“未确认”或“已确认”
    if ($order['order_status'] != OS_UNCONFIRMED && $order['order_status'] != OS_CONFIRMED)
    {
        return array('status'=>'fail', 'message'=>'错误', 'response' =>'');
    }

    //订单一旦确认，不允许用户取消
    if ( $order['order_status'] == OS_CONFIRMED)
    {
        return array('status'=>'fail', 'message'=>'订单已确认', 'response' =>'');
    }

    // 发货状态只能是“未发货”
    if ($order['shipping_status'] != SS_UNSHIPPED)
    {
        return array('status'=>'fail', 'message'=>'发货状态只能是未发货', 'response' =>'');
    }

    // 如果付款状态是“已付款”、“付款中”，不允许取消，要取消和商家联系
    if ($order['pay_status'] != PS_UNPAYED)
    {
        return array('status'=>'fail', 'message'=>'错误', 'response' =>'');
    }

    //将用户订单设置为取消
    $sql = "UPDATE ".$GLOBALS['ecs']->table('order_info') ." SET order_status = '".OS_CANCELED."',lastmodify = '".gmtime()."' WHERE order_sn = ".$params['order_id'];
    if ($GLOBALS['db']->query($sql))
    {
        /* 记录log */
        order_action($order['order_sn'], OS_CANCELED, $order['shipping_status'], PS_UNPAYED,$GLOBALS['_LANG']['buyer_cancel'],'buyer');
        /* 退货用户余额、积分、红包 */
        if ($order['user_id'] > 0 && $order['surplus'] > 0)
        {
            $change_desc = sprintf($GLOBALS['_LANG']['return_surplus_on_cancel'], $order['order_sn']);
            log_account_change($order['user_id'], $order['surplus'], 0, 0, 0, $change_desc);
        }
        if ($order['user_id'] > 0 && $order['integral'] > 0)
        {
            $change_desc = sprintf($GLOBALS['_LANG']['return_integral_on_cancel'], $order['order_sn']);
            log_account_change($order['user_id'], 0, 0, 0, $order['integral'], $change_desc);
        }
        if ($order['user_id'] > 0 && $order['bonus_id'] > 0)
        {
            change_user_bonus($order['bonus_id'], $order['order_id'], false);
        }

        /* 如果使用库存，且下订单时减库存，则增加库存 */
        if ($GLOBALS['_CFG']['use_storage'] == '1' && $GLOBALS['_CFG']['stock_dec_time'] == SDT_PLACE)
        {
            change_order_goods_storage($order['order_id'], false, 1);
        }

        /* 修改订单 */
        $arr = array(
            'bonus_id'  => 0,
            'bonus'     => 0,
            'integral'  => 0,
            'integral_money'    => 0,
            'surplus'   => 0
        );
        update_order($order['order_id'], $arr);

        return array('status'=>'succ', 'message'=>'成功', 'response' =>'');
    }
    else
    {
        return array('status'=>'fail', 'message'=>'失败', 'response' =>'');
    }
  }

  function afterrec(){
    return array('status'=>'fail', 'message'=>'暂无数据', 'response' =>'');
  }


}