<?php


class mobilead extends apiclass{
    function get_ads_list($params){

        $content = simplexml_load_file(dirname(__FILE__) . '/../../data/flash_data.xml');
        $val = json_decode(json_encode($content),true);
        $ad_data = array();
        foreach($val['item'] as $key => $item){
            $ad_data[$key]['ad_image_name'] = $item['@attributes']['text'];
            $ad_data[$key]['ad_image_url'] = $this->host_url.$item['@attributes']['item_url'];
            $ad_data[$key]['ad_image_type'] = '';
            $ad_data[$key]['url_type'] = '';
            $ad_data[$key]['goods_cat_id'] = '';
            $ad_data[$key]['goods_id'] = '';
            $ad_data[$key]['top'] = $val['sort'];
        }
        return array('status'=>'succ', 'message'=>'', 'response' => $ad_data);
    }
}