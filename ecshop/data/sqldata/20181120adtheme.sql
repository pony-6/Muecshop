-- ecshop v2.x SQL Dump Program
-- http://shop.kaiyuykt.com
-- 
-- DATE : 2018-11-20 12:08:41
-- MYSQL SERVER VERSION : 5.6.40-log
-- PHP VERSION : 7.2.6
-- ECShop VERSION : v4.0.0
-- Vol : 1
DROP TABLE IF EXISTS `ecs_ad`;
CREATE TABLE `ecs_ad` (
  `ad_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `position_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `media_type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `ad_name` varchar(60) NOT NULL DEFAULT '',
  `ad_link` varchar(255) NOT NULL DEFAULT '',
  `ad_code` text NOT NULL,
  `start_time` int(11) NOT NULL DEFAULT '0',
  `end_time` int(11) NOT NULL DEFAULT '0',
  `link_man` varchar(60) NOT NULL DEFAULT '',
  `link_email` varchar(60) NOT NULL DEFAULT '',
  `link_phone` varchar(60) NOT NULL DEFAULT '',
  `click_count` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `enabled` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`ad_id`),
  KEY `position_id` (`position_id`),
  KEY `enabled` (`enabled`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `ecs_ad_position`;
CREATE TABLE `ecs_ad_position` (
  `position_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `position_name` varchar(60) NOT NULL DEFAULT '',
  `ad_width` smallint(5) unsigned NOT NULL DEFAULT '0',
  `ad_height` smallint(5) unsigned NOT NULL DEFAULT '0',
  `position_desc` varchar(255) NOT NULL DEFAULT '',
  `position_style` text NOT NULL,
  PRIMARY KEY (`position_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `ecs_template`;
CREATE TABLE `ecs_template` (
  `filename` varchar(30) NOT NULL DEFAULT '',
  `region` varchar(40) NOT NULL DEFAULT '',
  `library` varchar(40) NOT NULL DEFAULT '',
  `sort_order` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `number` tinyint(1) unsigned NOT NULL DEFAULT '5',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `theme` varchar(60) NOT NULL DEFAULT '',
  `remarks` varchar(30) NOT NULL DEFAULT '',
  KEY `filename` (`filename`,`region`),
  KEY `theme` (`theme`),
  KEY `remarks` (`remarks`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
INSERT INTO `ecs_template` ( `filename`, `region`, `library`, `sort_order`, `id`, `number`, `type`, `theme`, `remarks` ) VALUES  ('index', '左边区域', '/library/vote_list.lbi', '8', '0', '0', '0', 'default', '');
INSERT INTO `ecs_template` ( `filename`, `region`, `library`, `sort_order`, `id`, `number`, `type`, `theme`, `remarks` ) VALUES  ('index', '左边区域', '/library/email_list.lbi', '9', '0', '0', '0', 'default', '');
INSERT INTO `ecs_template` ( `filename`, `region`, `library`, `sort_order`, `id`, `number`, `type`, `theme`, `remarks` ) VALUES  ('index', '左边区域', '/library/order_query.lbi', '6', '0', '0', '0', 'default', '');
INSERT INTO `ecs_template` ( `filename`, `region`, `library`, `sort_order`, `id`, `number`, `type`, `theme`, `remarks` ) VALUES  ('index', '左边区域', '/library/cart.lbi', '0', '0', '0', '0', 'default', '');
INSERT INTO `ecs_template` ( `filename`, `region`, `library`, `sort_order`, `id`, `number`, `type`, `theme`, `remarks` ) VALUES  ('index', '左边区域', '/library/promotion_info.lbi', '3', '0', '0', '0', 'default', '');
INSERT INTO `ecs_template` ( `filename`, `region`, `library`, `sort_order`, `id`, `number`, `type`, `theme`, `remarks` ) VALUES  ('index', '左边区域', '/library/auction.lbi', '4', '0', '3', '0', 'default', '');
INSERT INTO `ecs_template` ( `filename`, `region`, `library`, `sort_order`, `id`, `number`, `type`, `theme`, `remarks` ) VALUES  ('index', '左边区域', '/library/group_buy.lbi', '5', '0', '3', '0', 'default', '');
INSERT INTO `ecs_template` ( `filename`, `region`, `library`, `sort_order`, `id`, `number`, `type`, `theme`, `remarks` ) VALUES  ('index', '', '/library/recommend_promotion.lbi', '0', '0', '4', '0', 'default', '');
INSERT INTO `ecs_template` ( `filename`, `region`, `library`, `sort_order`, `id`, `number`, `type`, `theme`, `remarks` ) VALUES  ('index', '右边主区域', '/library/recommend_hot.lbi', '2', '0', '10', '0', 'default', '');
INSERT INTO `ecs_template` ( `filename`, `region`, `library`, `sort_order`, `id`, `number`, `type`, `theme`, `remarks` ) VALUES  ('index', '右边主区域', '/library/recommend_new.lbi', '1', '0', '10', '0', 'default', '');
INSERT INTO `ecs_template` ( `filename`, `region`, `library`, `sort_order`, `id`, `number`, `type`, `theme`, `remarks` ) VALUES  ('index', '右边主区域', '/library/recommend_best.lbi', '0', '0', '10', '0', 'default', '');
INSERT INTO `ecs_template` ( `filename`, `region`, `library`, `sort_order`, `id`, `number`, `type`, `theme`, `remarks` ) VALUES  ('index', '左边区域', '/library/invoice_query.lbi', '7', '0', '0', '0', 'default', '');
INSERT INTO `ecs_template` ( `filename`, `region`, `library`, `sort_order`, `id`, `number`, `type`, `theme`, `remarks` ) VALUES  ('index', '左边区域', '/library/top10.lbi', '2', '0', '0', '0', 'default', '');
INSERT INTO `ecs_template` ( `filename`, `region`, `library`, `sort_order`, `id`, `number`, `type`, `theme`, `remarks` ) VALUES  ('index', '左边区域', '/library/category_tree.lbi', '1', '0', '0', '0', 'default', '');
INSERT INTO `ecs_template` ( `filename`, `region`, `library`, `sort_order`, `id`, `number`, `type`, `theme`, `remarks` ) VALUES  ('index', '', '/library/brands.lbi', '0', '0', '11', '0', 'default', '');
INSERT INTO `ecs_template` ( `filename`, `region`, `library`, `sort_order`, `id`, `number`, `type`, `theme`, `remarks` ) VALUES  ('category', '左边区域', '/library/category_tree.lbi', '1', '0', '0', '0', 'default', '');
INSERT INTO `ecs_template` ( `filename`, `region`, `library`, `sort_order`, `id`, `number`, `type`, `theme`, `remarks` ) VALUES  ('category', '右边区域', '/library/recommend_best.lbi', '0', '0', '5', '0', 'default', '');
INSERT INTO `ecs_template` ( `filename`, `region`, `library`, `sort_order`, `id`, `number`, `type`, `theme`, `remarks` ) VALUES  ('category', '右边区域', '/library/goods_list.lbi', '1', '0', '0', '0', 'default', '');
INSERT INTO `ecs_template` ( `filename`, `region`, `library`, `sort_order`, `id`, `number`, `type`, `theme`, `remarks` ) VALUES  ('category', '右边区域', '/library/pages.lbi', '2', '0', '0', '0', 'default', '');
INSERT INTO `ecs_template` ( `filename`, `region`, `library`, `sort_order`, `id`, `number`, `type`, `theme`, `remarks` ) VALUES  ('category', '左边区域', '/library/cart.lbi', '0', '0', '0', '0', 'default', '');
INSERT INTO `ecs_template` ( `filename`, `region`, `library`, `sort_order`, `id`, `number`, `type`, `theme`, `remarks` ) VALUES  ('category', '左边区域', '/library/price_grade.lbi', '3', '0', '0', '0', 'default', '');
INSERT INTO `ecs_template` ( `filename`, `region`, `library`, `sort_order`, `id`, `number`, `type`, `theme`, `remarks` ) VALUES  ('category', '左边区域', '/library/filter_attr.lbi', '2', '0', '0', '0', 'default', '');
-- END ecshop v2.x SQL Dump Program 