<?php
namespace App\Model;

use PhalApi\Model\NotORMModel as NotORM;

class Accountlog extends NotORM{
    
    protected  function getTableName($id)
    {
        return 'account_log';
    }

    protected function getTableKey($table)
    {
        return 'log_id';
    }
    
    function log_account_change($user_id, $user_money = 0, $frozen_money = 0, $rank_points = 0, $pay_points = 0, $change_desc = '', $change_type = 99)
    {
        /* 插入帐户变动记录 */
        $account_log = array(
            'user_id'       => $user_id,
            'user_money'    => $user_money,
            'frozen_money'  => $frozen_money,
            'rank_points'   => $rank_points,
            'pay_points'    => $pay_points,
            'change_time'   => time(),
            'change_desc'   => $change_desc,
            'change_type'   => $change_type
        );
        
        $this->getORM()->insert($account_log);


        /* 更新用户信息 */
        $sql = "UPDATE ecs_users " .
            " SET user_money = user_money + ('$user_money')," .
            " frozen_money = frozen_money + ('$frozen_money')," .
            " rank_points = rank_points + ('$rank_points')," .
            " pay_points = pay_points + ('$pay_points')" .
            " WHERE user_id = '$user_id' LIMIT 1";
        $this->getORM()->queryRow($sql);
    }
    function write_affiliate_log($oid, $uid, $username, $money, $point, $separate_by)
    {
        $time = time();
        $sql = "INSERT INTO ecs_affiliate_log (order_id,user_id,user_name,time,money,point,separate_type) values
                ('$oid','$uid','$username','$time','$money','$point','$separate_by')";

        if ($oid)
        {
            $this->getORM()->queryRow($sql);
        }
    }
}