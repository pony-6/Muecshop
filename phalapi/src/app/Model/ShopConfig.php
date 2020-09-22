<?php
namespace App\Model;

use PhalApi\Model\NotORMModel as NotORM;

class ShopConfig extends NotORM
{
    public function getLogo(){
        $sql = "select value from ecs_shop_config where code='shop_logo'";
        return $this->getORM()->queryRows($sql);
        // return $this->getORM()->select("value")->where("code","shop_log")->fetchOne();
    }
    public function index_prompt(){
        $sql = "select * from ecs_index_prompt where status = 'true' order by sort asc limit 3";
        return $this->getORM()->queryRows($sql);
    }
}