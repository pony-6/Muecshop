ALTER TABLE `ecs_ad` ADD `link_qt` varchar(250)  DEFAULT  '';

ALTER TABLE `ecs_cart`
ADD COLUMN `is_checked`  enum('false','true') NULL DEFAULT 'false' AFTER `goods_attr_id`;

ALTER TABLE `ecs_user_address` 
ADD COLUMN `mobile_addr_id_list` varchar(255) NULL AFTER `is_default`;

CREATE TABLE `ecs_delivery_method` (
  `delivery_id` int(11) NOT NULL AUTO_INCREMENT  COMMENT'配送id',
  `parent_id` int(11) DEFAULT '0' COMMENT '父级id',
  `delivery_name` varchar(255) DEFAULT NULL COMMENT '配送名称',
  `sort_order` varchar(255) DEFAULT NULL COMMENT '排序',
  `cost` varchar(255) DEFAULT NULL COMMENT '费用',
  `is_show` enum('false','true') DEFAULT 'true' COMMENT '是否显示',
  `k_status` enum('false','true') DEFAULT 'false' COMMENT '是否为凯宇配送',
  `ctime` varchar(255) DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`delivery_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

ALTER TABLE `ecs_delivery_method`
ADD COLUMN `type` int(2) NOT NULL DEFAULT 0 COMMENT '是否是快递配送' AFTER `ctime`;

ALTER TABLE `ecs_users`
MODIFY COLUMN `birthday` date NOT NULL DEFAULT '1970-01-01' AFTER `sex`,
MODIFY COLUMN `last_time` datetime(0) NOT NULL DEFAULT '1970-01-01 10:20:10' AFTER `last_login`,
ADD COLUMN `platform` varchar(255) NULL AFTER `passwd_answer`;

ALTER TABLE `ecs_order_info`
ADD COLUMN `platform` varchar(255) NULL AFTER `apply_for_status`;

INSERT INTO `ecs_shop_config`(`id`, `parent_id`, `code`, `type`, `store_range`, `store_dir`, `value`, `sort_order`) VALUES (123, 1, 'shop_other', 'file', '', '', '', 1);


ALTER TABLE `ecs_goods`
ADD COLUMN `active` enum('true','false') NOT NULL DEFAULT 'false' COMMENT '是否开启秒杀，true开启，false关闭' AFTER `delivery_status`;

ALTER TABLE `ecs_goods`
ADD COLUMN `start_time` varchar(255) NULL COMMENT '秒杀开始时间' AFTER `active`,
ADD COLUMN `end_time` varchar(255) NULL COMMENT '秒杀结束时间' AFTER `start_time`;

ALTER TABLE `ecs_goods`
ADD COLUMN `spike_count` int(11) NULL COMMENT '秒杀商品数量' AFTER `end_time`,
ADD COLUMN `spike_sum` varchar(255) NULL COMMENT '秒杀商品金额' AFTER `spike_count`;


ALTER TABLE `ecs_order_info`
ADD COLUMN `order_type` tinyint(1) NOT NULL DEFAULT 0 AFTER `platform`;
ALTER TABLE `ecs_users`
ADD COLUMN  `rank_up` int(10) NOT NULL DEFAULT 0 AFTER `platform`




ALTER TABLE `ecs_goods` 
ADD COLUMN `is_pintuan` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否开启商品拼团' AFTER `delivery_status`,
ADD COLUMN `pt_price` decimal(10, 0) NOT NULL DEFAULT 0.00 COMMENT '拼团金额' AFTER `is_pintuan`;


ALTER TABLE `ecs_order_info` 
ADD COLUMN `order_type` tinyint(1) NOT NULL DEFAULT 0 COMMENT '订单类型：0普通订单，1拼团订单，2秒杀订单。默认普通订单' AFTER `platform`;

ALTER TABLE `ecs_order_info` 
ADD COLUMN `pt_id` int(11) NOT NULL DEFAULT 0 COMMENT '相互拼团的订单ID' AFTER `order_type`;


ALTER TABLE `ecs_order_goods`
ADD COLUMN `act_id` int(11) NOT NULL DEFAULT 0 COMMENT '超值礼包的ID' AFTER `goods_id`;

ALTER TABLE `ecs_order_info` 
ADD COLUMN `tax_num` varchar(128) NULL COMMENT '纳税人识别号' AFTER `tax`;


CREATE TABLE `ecs_user_visit_log` (
  `visit_id` int(11) NOT NULL AUTO_INCREMENT  COMMENT'用户浏览id',
  `user_id` int(11) DEFAULT '0' COMMENT '用户id',
  `goods_id` int(5) DEFAULT NULL COMMENT '商品id',
  `hitCounts` int(5) DEFAULT NULL COMMENT '点击次数',
  `addTime` datetime(0) NOT NULL DEFAULT '1970-01-01 00:00:00' COMMENT '添加时间',
	`platform` varchar(255) DEFAULT NULL  COMMENT '来源',
  PRIMARY KEY (`visit_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `ecs_user_recommend` (
  `recommend_id` int(11) NOT NULL AUTO_INCREMENT  COMMENT'推荐id',
  `user_id` int(11) DEFAULT '0' COMMENT '新注册用户id',
  `user_name` varchar(255) DEFAULT NULL COMMENT '用户名',
  `parent_id` int(11) DEFAULT NULL COMMENT '推荐人id',
  `recommend` varchar(255) DEFAULT NULL COMMENT '推荐人名',
  PRIMARY KEY (`recommend_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

INSERT INTO `ecs_app_config`(`k`, `val`) VALUES ('default_image', 'https://imgt1.oss-cn-shanghai.aliyuncs.com/tools/default_category.png');

ALTER TABLE `ecs_goods`
ADD COLUMN `sales_volume_count` varchar(255) NULL DEFAULT 0 COMMENT '订单销量' AFTER `pt_price`;

ALTER TABLE `ecs_users`
ADD COLUMN `openid_h5` varchar(32) NULL  COMMENT 'H5的openid' AFTER `openid`;

