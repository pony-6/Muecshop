<?php

class goods extends apiclass{
    
    function get_goods_category($params){
        /**
            判断是否为首页分类
        **/
        if($params['is_index'] == 'index'){
            $result = $this->_get_child_index($params['lang']);
        }else{
            $result = $this->_get_child(" ",$params['lang']);
        }
        if ($result) {
            return array('status'=>'succ', 'message'=>'', 'response' => $result);
        }
    	return array('status'=>'fail', 'message'=>'无数据', 'response' => $result);
    }
    
    public function _get_child_index($lang){
	    $sql = "select parent_id,cat_id,cat_name,cat_name_en from ecs_category where is_index = 'true'";
        $goods_cat_list = $this->db->getAll($sql);
        $return = array();
        foreach( $goods_cat_list as $key=>$row ){
            $return[$key]['parent_id'] = $row['parent_id'];
            $return[$key]['cat_id'] = $row['cat_id'];
            $return[$key]['cat_name'] = $row['cat_name'];
            if($lang == 'en'){
            $return[$key]['cat_name'] = $row['cat_name_en'];
            }
            $return[$key]['child_catlist'] = $this->_get_child($row['cat_id']);
        }
        return $return;
    }

    //首页导航商品分类
    public function get_goods_category_index($params)
    {   
        $sql = "select parent_id,cat_id,cat_name,cat_name_en from ecs_category where parent_id <> 0";
        $goods_cat_list = $this->db->getAll($sql);
        return array('status'=>'succ', 'message'=>'', 'response' => $goods_cat_list);
    }
    
    public function brandlist($params){

        if($params['is_index'] == 'index'){
            $result = $this->_get_brand($params['is_index']);
        }else{
            $result = $this->_get_brand();
        }
        if ($result) {
            return array('status'=>'succ', 'message'=>'', 'response' => $result);
        }
    	return array('status'=>'fail', 'message'=>'无数据', 'response' => $result);
    }
    
    function _get_brand($is_index='false'){
        if($s_index == 'index'){
            $sql = "select * from  ecs_brand order where is_index='true' by is_show desc";
        }else{
            $sql = "select * from  ecs_brand order by is_show desc";
        }
        $brand_list = $this->db->getAll($sql);
        //$this->host_url;
        $brand_url = $this->host_url.'/data/brandlogo/';
        foreach($brand_list as $key => $item){
            $brand_list[$key]['brand_logo'] = $brand_url.$item['brand_logo'];
        }
        return $brand_list;
    }
    
    

    public function _get_child($parent_id,$lang)
    {   
        $cat_id = intval($parent_id);
        if(empty($cat_id) || $cat_id < 0){
            $cat_id = 0;
        }
	    $sql = "select parent_id,cat_id,cat_name,cat_name_en from ecs_category where parent_id = ".$cat_id ." and is_show='1' order by sort_order asc";
        $goods_cat_list = $this->db->getAll($sql);
        if (empty($goods_cat_list)) {
            return array();
        } else {
            $return = array();
            foreach( $goods_cat_list as $key=>$row ){
                $return[$key]['parent_id'] = $row['parent_id'];
                $return[$key]['cat_id'] = $row['cat_id'];
                $return[$key]['cat_name'] = $row['cat_name'];
                if($lang == "en"){
                 $return[$key]['cat_name'] = $row['cat_name_en'];
                }
                $return[$key]['child_catlist'] = $this->_get_child($row['cat_id'],$lang);
            }
            return $return;
        }
    }

    public function get_goods_gallery($params)
    {
        if($params['meiyou'] == 'meiyou'){
         return array('status'=>'fail', 'message'=>'暂无更多数据', 'response' => $result);
        }
        if($params['is_index'] == 'true'){
            $goods_list_sql = "select goods_id,goods_name as name,goods_name_en,goods_thumb as s_url,original_img as l_url,brand_id,shop_price,goods_number,market_price ,goods_sn as bn,goods_brief from ecs_goods where is_delete != 1 and is_index = 'true'";
            $goods_list_sum_sql = "select count(goods_id) as goods_id from ecs_goods where is_delete != 1 and is_index = 'true' ";
        }else{

            $goods_list_sql = "select goods_id,goods_name as name,goods_name_en,goods_thumb as s_url,original_img as l_url,brand_id,shop_price,goods_number,market_price ,goods_sn as bn,goods_brief from ecs_goods where is_delete != 1 and cat_id = ".$params['cat_id'];
            $goods_list_sum_sql = "select count(goods_id) as goods_id from ecs_goods where is_delete != 1 and cat_id = ".$params['cat_id'];
        }
        
        if($params['brand_id'] != ''){
            $goods_list_sql = "select goods_id,goods_name as name,goods_name_en,goods_thumb as s_url,original_img as l_url,brand_id,shop_price,goods_number,market_price ,goods_sn as bn,goods_brief from ecs_goods where is_delete != 1 and brand_id = ".$params['brand_id'];
            $goods_list_sum_sql = "select count(goods_id) as goods_id from ecs_goods where  is_delete != 1 and brand_id = ".$params['brand_id'];
        }
        
        if($params['search_key'] != ''){
            $goods_list_sql = "select goods_id,goods_name as name,goods_name_en,goods_thumb as s_url,original_img as l_url,brand_id,shop_price,goods_number,market_price,goods_sn as bn,goods_brief from ecs_goods where is_delete != 1 and goods_name like '%".$params['search_key']."%' or goods_sn like '%".$params['search_key']."%' ";
            $goods_list_sum_sql = "select count(goods_id) as goods_id from ecs_goods where is_delete != 1 and goods_name like '%".$params['search_key']."%' or goods_sn like '%".$params['search_key']."%' ";
        }

            $goods_sum = $this->db->getAll($goods_list_sum_sql);

 
            $data['pager_total'] = intval($goods_sum[0]['goods_id']/$params['req_number']) + 1;

        if($params['p_order'] == ''){
            $orderby = ' order by sort_order desc';
        }else{
            if( $params['p_order'] == 'buy_w_count DESC'){
                $orderby = ' order by virtual_sales desc';
            }
            if( $params['p_order'] == 'price_high'){
                $orderby = ' order by shop_price desc';
            }
            if( $params['p_order'] == 'price_low'){
                $orderby = ' order by shop_price asc';
            }
            if( $params['p_order']=='time_high'){
                $orderby = ' order by add_time desc';
            }
            if( $params['p_order']=='time_low'){
                $orderby = ' order by add_time asc';
            }
            if( $params['p_order']=='goods_desc'){
                $orderby = ' order by CONVERT (goods_name USING gbk) desc';
            }
            if( $params['p_order'] =='goods_asc'){
                $orderby = ' order by CONVERT (goods_name USING gbk) asc';
            }
            if($params['p_order']=='sales_high'){
                $orderby = ' order by virtual_sales desc';
            }
            if($params['p_order']=='sales_low'){
                $orderby = ' order by virtual_sales asc';
            }
        }
        
        $goods_list_sql = $goods_list_sql .$orderby;
//        var_dump($goods_list_sql);die;
        //处理分页
        $pages = intval($params['req_pages']) ? intval($params['req_pages']) : 1; //页码
        $number = intval($params['req_number']) ? intval($params['req_number']) : 10;//每页数量
        $offset =  ($pages - 1) * $number;

        $limit = " LIMIT " . $offset. ",". $number;
        // $goods_list_sql = $goods_list_sql . $limit;
        $goods_list_sql = $goods_list_sql ;

        if($params['status']){

            $sql = "select cat_id from ecs_category where parent_id = ".$params['cat_id']; 
            $cat_id = $this->db->getAll($sql);
            $a = array(
                'cat_id'=>$params['cat_id']
            );
            array_push($cat_id,$a);
            foreach ($cat_id as $key => $value) {
             $goods_list_sql = "select goods_id,goods_name as name,goods_name_en,goods_thumb as s_url,original_img as l_url,brand_id,shop_price,goods_number,market_price ,goods_sn as bn,goods_brief from ecs_goods where cat_id = ".$value['cat_id'];
             $goods_list_sql = $goods_list_sql .$orderby;
             $lists[] = $this->db->getAll($goods_list_sql);

            }
            foreach ($lists as $key => $value) {
                foreach ($value as $k => $v) {
                    $list[] =$v;
                }
            }
            $result = array();
            if (is_array($list) && !empty($list)) {
            $brand_ids = array();

            foreach ($list as $key => $value) {
                $list[$key]['s_url'] = 'http://' . $_SERVER['HTTP_HOST'] . '/' . $value['s_url'];
                $list[$key]['l_url'] = 'http://' . $_SERVER['HTTP_HOST'] . '/' . $value['l_url'];
                if ($value['brand_id'] != '0') {
                    $brand_ids[] = $value['brand_id'];
                }
                $list[$key]['products'] = array(
                    'goods_id' => $value['goods_id'],
                    'name' => $value['goods_name'],
                    'product_id' => $value['goods_id'],
                    'price' => $value['shop_price'],
                    'store' => $value['goods_number'],
                    'mktprice' => $value['market_price'],
                    'goods_brief' => $value['goods_brief'],
                );
                
            }
            $brand_id_list = array_unique($brand_ids);
            if (!empty($brand_list)) {
                $brand_sql = "select brand_id,brand_name from ecs_brand where brand_id in (".implode("','",(array)$brand_id_list).") ";
                $brand_list = $this->db->getAll($brand_sql);
            }
            $data['goods_list'] = $list;
            $data['screen']['brand'] = $brand_list;
            return array('status'=>'succ', 'message'=>'', 'response' => $data);
        }
        return array('status'=>'fail', 'message'=>'暂无更多数据', 'response' => $result);

        }else{

            $lists = $this->db->getAll($goods_list_sql);

             $result = array();
             if (is_array($lists) && !empty($lists)) {
            $brand_ids = array();
            foreach ($lists as $key => $value) {
                
                $lists[$key]['s_url'] = 'http://' . $_SERVER['HTTP_HOST'] . '/' . $value['s_url'];
                $lists[$key]['l_url'] = 'http://' . $_SERVER['HTTP_HOST'] . '/' . $value['l_url'];
                if ($value['brand_id'] != '0') {
                    $brand_ids[] = $value['brand_id'];
                }
                $lists[$key]['products'] = array(
                    'goods_id' => $value['goods_id'],
                    'name' => $value['goods_name'],
                    'product_id' => $value['goods_id'],
                    'price' => $value['shop_price'],
                    'store' => $value['goods_number'],
                    'mktprice' => $value['market_price'],
                    'goods_brief' => $value['goods_brief'],
                );
                 if($params['lang'] == 'en'){
                    $lists[$key]['name'] = $value['goods_name_en'];
                }
            }
            $brand_id_list = array_unique($brand_ids);
            if (!empty($brand_list)) {
                $brand_sql = "select brand_id,brand_name from ecs_brand where brand_id in (".implode("','",(array)$brand_id_list).") ";
                $brand_list = $this->db->getAll($brand_sql);
            }
            $data['goods_list'] = $lists;
            $data['screen']['brand'] = $brand_list;
            return array('status'=>'succ', 'message'=>'', 'response' => $data);
        }

        return array('status'=>'fail', 'message'=>'暂无更多数据', 'response' => $result);

        }

       

    }

    public function get_goods_details($params)
    {

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
        $aGoods['virtual_sales'] = $itemProduct['virtual_sales'];
        $aGoods['images'] = $this->get_goods_images_original($itemProduct);
        $aGoods['product'] = $this->get_product_info($itemProduct,$params['lang']);
        $aGoods['guige'] = $this->get_goods_properties($itemProduct['goods_id'],$aGoods['product']['product_id']);

        if ($_SESSION['user_id']) {
            $is_fav_sql = "select * from ecs_collect_goods where user_id = ".$_SESSION['user_id']." and goods_id = ".$productId;
            $is_fav = $this->db->getRow($is_fav_sql);
            if (!empty($is_fav)) {
                $aGoods['product']['is_fav'] = 'true';
            }
        }
        return array('status'=>'succ', 'message'=>'', 'response' => $aGoods);
    }


    /*规格*/
   function get_goods_properties($goods_id,$product_id)
{
    /* 对属性进行重新排序和分组 */
    $sql = "SELECT attr_group ".
            "FROM " . $GLOBALS['ecs']->table('goods_type') . " AS gt, " . $GLOBALS['ecs']->table('goods') . " AS g ".
            "WHERE g.goods_id='$goods_id' AND gt.cat_id=g.goods_type";
    $grp = $GLOBALS['db']->getOne($sql);

    if (!empty($grp))
    {
        $groups = explode("\n", strtr($grp, "\r", ''));
    }

    /* 获得商品的规格 */
    $sql = "SELECT a.attr_id, a.attr_name, a.attr_group, a.is_linked, a.attr_type, ".
                "g.goods_attr_id, g.attr_value, g.attr_price " .
            'FROM ' . $GLOBALS['ecs']->table('goods_attr') . ' AS g ' .
            'LEFT JOIN ' . $GLOBALS['ecs']->table('attribute') . ' AS a ON a.attr_id = g.attr_id ' .
            "WHERE g.goods_id = '$goods_id' " .
            'ORDER BY a.sort_order, g.attr_price, g.goods_attr_id';
    $res = $GLOBALS['db']->getAll($sql);

    $arr['pro'] = array();     // 属性
    $arr['spe'] = array();     // 规格
    $arr['lnk'] = array();     // 关联的属性

    foreach ($res AS $row)
    {
        $row['attr_value'] = str_replace("\n", '<br />', $row['attr_value']);

        if ($row['attr_type'] == 0)
        {
            $group = (isset($groups[$row['attr_group']])) ? $groups[$row['attr_group']] : $GLOBALS['_LANG']['goods_attr'];

            $arr['pro'][$group][$row['attr_id']]['name']  = $row['attr_name'];
            $arr['pro'][$group][$row['attr_id']]['value'] = $row['attr_value'];
        }
        else
        {
            $arr['spe'][$row['attr_id']]['attr_type'] = $row['attr_type'];
            $arr['spe'][$row['attr_id']]['name']     = $row['attr_name'];
            $arr['spe'][$row['attr_id']]['product_id']     = $product_id;
            $arr['spe'][$row['attr_id']]['values'][] = array(
                                                        'label'        => $row['attr_value'],
                                                        'price'        => $row['attr_price'],
                                                        'format_price' => price_format(abs($row['attr_price']), false),
                                                        'id'           => $row['goods_attr_id']);
        }

        if ($row['is_linked'] == 1)
        {
            /* 如果该属性需要关联，先保存下来 */
            $arr['lnk'][$row['attr_id']]['name']  = $row['attr_name'];
            $arr['lnk'][$row['attr_id']]['value'] = $row['attr_value'];
        }
    }

    return $arr;
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
    		$images[] = array(
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
    	$aGoods['goods_brief'] = $itemProduct['goods_brief'];
    	$aGoods['product_marketable'] = ($itemProduct['is_on_sale']=='1') ? 'true' : 'false';
    	$aGoods['title'] = $itemProduct['goods_name'];
//        $strUrl = 'http://'.$_SERVER['HTTP_HOST'];
        $strUrl = '';
        $aGoods['wapintro'] = $this->replacePicUrl($itemProduct['goods_desc'],$strUrl);
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
    /** 
     * 替换fckedit中的图片 添加域名 
     * @param  string $content 要替换的内容 
     * @param  string $strUrl 内容中图片要加的域名 
     * @return string  
     * @eg  
     */  
    function replacePicUrl($content = null, $strUrl = null) {  
        if ($strUrl) {  
            //提取图片路径的src的正则表达式 并把结果存入$matches中    
            preg_match_all("/<img(.*)src=\"([^\"]+)\"[^>]+>/isU",$content,$matches);  
            $img = "";    
            if(!empty($matches)) {    
            //注意，上面的正则表达式说明src的值是放在数组的第三个中    
            $img = $matches[2];    
            }else {    
               $img = "";    
            }  
              if (!empty($img)) {    
                    $patterns= array();    
                    $replacements = array();    
                    foreach($img as $imgItem){    
                        $final_imgUrl = $strUrl.$imgItem;    
                        $replacements[] = $final_imgUrl;    
                        $img_new = "/".preg_replace("/\//i","\/",$imgItem)."/";  
                        $img_new = preg_replace("/\(/i","\(",$img_new);    
                        $img_new = preg_replace("/\)/i","\)",$img_new); 
                        $patterns[] = $img_new;    
                    }    
        
                    //让数组按照key来排序    
                    ksort($patterns);    
                    ksort($replacements);    
        
                    //替换内容    
                    $vote_content = preg_replace($patterns, $replacements, $content);  
              
                    return $vote_content;  
            }else {  
                return $content;  
            }                     
        } else {  
            return $content;  
        }  
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
                    $spec_name[] = $value['attr_name'];
                }else{
                    $spec_name[] = $value['attr_name_en'];
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

    public function get_index_catgoods()
    {
        $sql = "select * from ecs_category where is_delete != 1 and show_in_nav = 1 and is_show = 1";
        $category_list = $this->db->getAll($sql);
        foreach ($category_list as $key => $value) {
            $sql = "select * from ecs_goods where is_on_sale = 1 and cat_id = " . $value['cat_id'] . ' order by sort_order desc limit 0, 6';
            $category_list[$key]['desc'] = $this->db->getAll($sql);
            $category_list[$key]['cat_img'] = 'http://' . $_SERVER['HTTP_HOST'] . '/' .$value['cat_img'];
        }
        foreach ($category_list as $key => $value) {
            if (!empty($value['desc'])) {
                foreach ($value['desc'] as $item => $desc) {
                    $value['desc'][$item]['goods_img'] = 'http://' . $_SERVER['HTTP_HOST'] . '/' .$value['desc'][$item]['goods_img'];
                }
                $response[] = $value;
            }
        }
        $data = array(
            'status' => 'succ',
            'response' => $response,
            'message' => ''
        );
        return $data;
    }

    // 首页顶部banner 精品
    public function get_index_imglist()
    {
        $sql = "select goods_id,goods_img from ecs_goods where is_delete != 1 and is_best = 1 and is_on_sale = 1";
        $img_list = $this->db->getAll($sql);
        foreach ($img_list as $key => $value) {
            $goods_img = $this->get_goods_images($value['goods_id']);
            $img_list[$key]['goods_img'] = $goods_img[0]['l_url'];
        }
        $data = array(
            'status' => 'succ',
            'response' => $img_list,
            'message' => ''
        );
        return $data;
    }

    // 首页品牌
    public function get_index_brand()
    {
        $sql = "select * from ecs_brand where is_show = 1 and brand_logo <> '' order by sort_order desc";
        $brand_list = $this->db->getAll($sql);
        foreach ($brand_list as $key => $value) {
            $brand_list[$key]['brand_logo'] = 'http://' . $_SERVER['HTTP_HOST'] . '/' . DATA_DIR . '/brandlogo/'.$value['brand_logo'];
        }
        $data = array(
            'status' => 'succ',
            'response' => $brand_list,
            'message' => ''
        );
        return $data;
    }

    /* 获取adbanner */
    public function get_banner()
    {
        $res = array(
            'status'=>'fail',
            'message'=>'',
            'response'=>array(),
        );
        $sql  = "select a.position_id,a.position_name,b.ad_link,b.ad_code from ecs_ad_position a left join ecs_ad b on a.position_id = b.position_id where a.position_name like '%wap首页轮播图%' and b.enabled ='1'";
        $data = $this->db->getAll($sql);
        foreach ($data as $k=>$v){
            if(strlen($v['ad_code'])=='23'){
                $data[$k]['ad_code'] = 'http://'.$_SERVER['HTTP_HOST'].'/data/afficheimg/'.$v['ad_code'];
            }
        }
        $res['status']='succ';
        $res['response']= $data;
        return $res;

    }

    /* 首页图片1 */
    public function get_ad_banner()
    {
        $res = array(
            'status'=>'fail',
            'message'=>'',
            'response'=>array(),
        );
        $sql  = "select a.position_id,a.position_name,b.ad_link,b.ad_code from ecs_ad_position a left join ecs_ad b on a.position_id = b.position_id where a.position_name like '%首页图片1%' and b.enabled ='1'";
        $data = $this->db->getAll($sql);
        foreach ($data as $k=>$v){
            if(strlen($v['ad_code'])=='23'){
                $data[$k]['ad_code'] = 'http://'.$_SERVER['HTTP_HOST'].'/data/afficheimg/'.$v['ad_code'];
            }
        }
        $res['status']='succ';
        $res['response']= $data;
        return $res;

    }
    /* 首页图片2*/
    public function get_ad_banner1()
    {
        $res = array(
            'status'=>'fail',
            'message'=>'',
            'response'=>array(),
        );
        $sql  = "select a.position_id,a.position_name,b.ad_link,b.ad_code from ecs_ad_position a left join ecs_ad b on a.position_id = b.position_id where a.position_name like '%首页图片2%' and b.enabled ='1'";
        $data = $this->db->getAll($sql);
        foreach ($data as $k=>$v){
            if(strlen($v['ad_code'])=='23'){
                $data[$k]['ad_code'] = 'http://'.$_SERVER['HTTP_HOST'].'/data/afficheimg/'.$v['ad_code'];
            }
        }
        $res['status']='succ';
        $res['response']= $data;
        return $res;

    }

    /* 首页图片3*/
    public function get_ad_banner2()
    {
        $res = array(
            'status'=>'fail',
            'message'=>'',
            'response'=>array(),
        );
        $sql  = "select a.position_id,a.position_name,b.ad_link,b.ad_code from ecs_ad_position a left join ecs_ad b on a.position_id = b.position_id where a.position_name like '%首页图片3%' and b.enabled ='1'";
        $data = $this->db->getAll($sql);
        foreach ($data as $k=>$v){
            if(strlen($v['ad_code'])=='23'){
                $data[$k]['ad_code'] = 'http://'.$_SERVER['HTTP_HOST'].'/data/afficheimg/'.$v['ad_code'];
            }
        }
        $res['status']='succ';
        $res['response']= $data;
        return $res;

    }
     /* 首页图片4*/
    public function get_ad_banner3()
    {
        $res = array(
            'status'=>'fail',
            'message'=>'',
            'response'=>array(),
        );
        $sql  = "select a.position_id,a.position_name,b.ad_link,b.ad_code from ecs_ad_position a left join ecs_ad b on a.position_id = b.position_id where a.position_name like '%首页图片4%' and b.enabled ='1'";
        $data = $this->db->getAll($sql);
        foreach ($data as $k=>$v){
            if(strlen($v['ad_code'])=='23'){
                $data[$k]['ad_code'] = 'http://'.$_SERVER['HTTP_HOST'].'/data/afficheimg/'.$v['ad_code'];
            }
        }
        $res['status']='succ';
        $res['response']= $data;
        return $res;

    }
     /* 首页图片5*/
    public function get_ad_banner4()
    {
        $res = array(
            'status'=>'fail',
            'message'=>'',
            'response'=>array(),
        );
        $sql  = "select a.position_id,a.position_name,b.ad_link,b.ad_code from ecs_ad_position a left join ecs_ad b on a.position_id = b.position_id where a.position_name like '%首页图片5%' and b.enabled ='1'";
        $data = $this->db->getAll($sql);
        foreach ($data as $k=>$v){
            if(strlen($v['ad_code'])=='23'){
                $data[$k]['ad_code'] = 'http://'.$_SERVER['HTTP_HOST'].'/data/afficheimg/'.$v['ad_code'];
            }
        }
        $res['status']='succ';
        $res['response']= $data;
        return $res;

    }
     /* 首页图片6*/
    public function get_ad_banner5()
    {
        $res = array(
            'status'=>'fail',
            'message'=>'',
            'response'=>array(),
        );
        $sql  = "select a.position_id,a.position_name,b.ad_link,b.ad_code from ecs_ad_position a left join ecs_ad b on a.position_id = b.position_id where a.position_name like '%首页图片6%' and b.enabled ='1'";
        $data = $this->db->getAll($sql);
        foreach ($data as $k=>$v){
            if(strlen($v['ad_code'])=='23'){
                $data[$k]['ad_code'] = 'http://'.$_SERVER['HTTP_HOST'].'/data/afficheimg/'.$v['ad_code'];
            }
        }
        $res['status']='succ';
        $res['response']= $data;
        return $res;

    }


    /* 首页图片7*/
    public function get_ad_banner6()
    {
        $res = array(
            'status'=>'fail',
            'message'=>'',
            'response'=>array(),
        );
        $sql  = "select a.position_id,a.position_name,b.ad_link,b.ad_code from ecs_ad_position a left join ecs_ad b on a.position_id = b.position_id where a.position_name like '%首页图片7%' and b.enabled ='1'";
        $data = $this->db->getAll($sql);
        foreach ($data as $k=>$v){
            if(strlen($v['ad_code'])=='23'){
                $data[$k]['ad_code'] = 'http://'.$_SERVER['HTTP_HOST'].'/data/afficheimg/'.$v['ad_code'];
            }
        }
        $res['status']='succ';
        $res['response']= $data;
        return $res;

    }


    /* 首页图片8*/
    public function get_ad_banner7()
    {
        $res = array(
            'status'=>'fail',
            'message'=>'',
            'response'=>array(),
        );
        $sql  = "select a.position_id,a.position_name,b.ad_link,b.ad_code from ecs_ad_position a left join ecs_ad b on a.position_id = b.position_id where a.position_name like '%首页图片8%' and b.enabled ='1'";
        $data = $this->db->getAll($sql);
        foreach ($data as $k=>$v){
            if(strlen($v['ad_code'])=='23'){
                $data[$k]['ad_code'] = 'http://'.$_SERVER['HTTP_HOST'].'/data/afficheimg/'.$v['ad_code'];
            }
        }
        $res['status']='succ';
        $res['response']= $data;
        return $res;

    }

    /* 首页图片9*/
    public function get_ad_banner8()
    {
        $res = array(
            'status'=>'fail',
            'message'=>'',
            'response'=>array(),
        );
        $sql  = "select a.position_id,a.position_name,b.ad_link,b.ad_code from ecs_ad_position a left join ecs_ad b on a.position_id = b.position_id where a.position_name like '%首页图片9%' and b.enabled ='1'";
        $data = $this->db->getAll($sql);
        foreach ($data as $k=>$v){
            if(strlen($v['ad_code'])=='23'){
                $data[$k]['ad_code'] = 'http://'.$_SERVER['HTTP_HOST'].'/data/afficheimg/'.$v['ad_code'];
            }
        }
        $res['status']='succ';
        $res['response']= $data;
        return $res;

    }

    /*首页销量最多的三个商品*/
    public function get_list_volume()
    {
        $sql = "select goods_id,goods_img,shop_price,goods_name,market_price from ecs_goods where  is_delete != 1  order by virtual_sales desc limit 0, 3";
        $img_list = $this->db->getAll($sql);
        foreach ($img_list as $key => $value) {
            $goods_img = $this->get_goods_images($value['goods_id']);
            $img_list[$key]['goods_img'] = $goods_img[0]['s_url'];
            $img_list[$key]['shop_price'] = $value['shop_price'];
            $img_list[$key]['goods_name'] = $value['goods_name'];
        }
        $data = array(
            'status' => 'succ',
            'response' => $img_list,
            'message' => ''
        );
        return $data;
    }


    /* 首页分类1*/
    public function get_ad_banner9()
    {
        $res = array(
            'status'=>'fail',
            'message'=>'',
            'response'=>array(),
        );
        $sql  = "select a.position_id,a.position_name,b.ad_link,b.ad_code from ecs_ad_position a left join ecs_ad b on a.position_id = b.position_id where a.position_name like '%首页分类1%' and b.enabled ='1'";
        $data = $this->db->getAll($sql);
        foreach ($data as $k=>$v){
            if(strlen($v['ad_code'])=='23'){
                $data[$k]['ad_code'] = 'http://'.$_SERVER['HTTP_HOST'].'/data/afficheimg/'.$v['ad_code'];
            }
        }
        $res['status']='succ';
        $res['response']= $data;
        return $res;

    }

    /* 首页分类2*/
    public function get_ad_banner10()
    {
        $res = array(
            'status'=>'fail',
            'message'=>'',
            'response'=>array(),
        );
        $sql  = "select a.position_id,a.position_name,b.ad_link,b.ad_code from ecs_ad_position a left join ecs_ad b on a.position_id = b.position_id where a.position_name like '%首页分类2%' and b.enabled ='1'";
        $data = $this->db->getAll($sql);
        foreach ($data as $k=>$v){
            if(strlen($v['ad_code'])=='23'){
                $data[$k]['ad_code'] = 'http://'.$_SERVER['HTTP_HOST'].'/data/afficheimg/'.$v['ad_code'];
            }
        }
        $res['status']='succ';
        $res['response']= $data;
        return $res;

    }
    /* 首页分类3*/
    public function get_ad_banner11()
    {
        $res = array(
            'status'=>'fail',
            'message'=>'',
            'response'=>array(),
        );
        $sql  = "select a.position_id,a.position_name,b.ad_link,b.ad_code from ecs_ad_position a left join ecs_ad b on a.position_id = b.position_id where a.position_name like '%首页分类3%' and b.enabled ='1'";
        $data = $this->db->getAll($sql);
        foreach ($data as $k=>$v){
            if(strlen($v['ad_code'])=='23'){
                $data[$k]['ad_code'] = 'http://'.$_SERVER['HTTP_HOST'].'/data/afficheimg/'.$v['ad_code'];
            }
        }
        $res['status']='succ';
        $res['response']= $data;
        return $res;

    }

    /* 首页分类4*/
    public function get_ad_banner12()
    {
        $res = array(
            'status'=>'fail',
            'message'=>'',
            'response'=>array(),
        );
        $sql  = "select a.position_id,a.position_name,b.ad_link,b.ad_code from ecs_ad_position a left join ecs_ad b on a.position_id = b.position_id where a.position_name like '%首页分类4%' and b.enabled ='1'";
        $data = $this->db->getAll($sql);
        foreach ($data as $k=>$v){
            if(strlen($v['ad_code'])=='23'){
                $data[$k]['ad_code'] = 'http://'.$_SERVER['HTTP_HOST'].'/data/afficheimg/'.$v['ad_code'];
            }
        }
        $res['status']='succ';
        $res['response']= $data;
        return $res;

    }

    /* 首页分类5*/
    public function get_ad_banner13()
    {
        $res = array(
            'status'=>'fail',
            'message'=>'',
            'response'=>array(),
        );
        $sql  = "select a.position_id,a.position_name,b.ad_link,b.ad_code from ecs_ad_position a left join ecs_ad b on a.position_id = b.position_id where a.position_name like '%首页分类5%' and b.enabled ='1'";
        $data = $this->db->getAll($sql);
        foreach ($data as $k=>$v){
            if(strlen($v['ad_code'])=='23'){
                $data[$k]['ad_code'] = 'http://'.$_SERVER['HTTP_HOST'].'/data/afficheimg/'.$v['ad_code'];
            }
        }
        $res['status']='succ';
        $res['response']= $data;
        return $res;

    }

    /* 首页分类6*/
    public function get_ad_banner14()
    {
        $res = array(
            'status'=>'fail',
            'message'=>'',
            'response'=>array(),
        );
        $sql  = "select a.position_id,a.position_name,b.ad_link,b.ad_code from ecs_ad_position a left join ecs_ad b on a.position_id = b.position_id where a.position_name like '%首页分类6%' and b.enabled ='1'";
        $data = $this->db->getAll($sql);
        foreach ($data as $k=>$v){
            if(strlen($v['ad_code'])=='23'){
                $data[$k]['ad_code'] = 'http://'.$_SERVER['HTTP_HOST'].'/data/afficheimg/'.$v['ad_code'];
            }
        }
        $res['status']='succ';
        $res['response']= $data;
        return $res;

    }

    /* 首页分类7*/
    public function get_ad_banner15()
    {
        $res = array(
            'status'=>'fail',
            'message'=>'',
            'response'=>array(),
        );
        $sql  = "select a.position_id,a.position_name,b.ad_link,b.ad_code from ecs_ad_position a left join ecs_ad b on a.position_id = b.position_id where a.position_name like '%首页分类7%' and b.enabled ='1'";
        $data = $this->db->getAll($sql);
        foreach ($data as $k=>$v){
            if(strlen($v['ad_code'])=='23'){
                $data[$k]['ad_code'] = 'http://'.$_SERVER['HTTP_HOST'].'/data/afficheimg/'.$v['ad_code'];
            }
        }
        $res['status']='succ';
        $res['response']= $data;
        return $res;

    }



    /* 首页商品分类1*/
    public function get_ad_category1()
    {
        $res = array(
            'status'=>'fail',
            'message'=>'',
            'response'=>array(),
        );
        $sql = "select * from ecs_category where shouye_img_status =1 and is_show =1";
        $data = $this->db->getAll($sql);
        if($data){
            $sql = "select goods_id,goods_img,shop_price,goods_name,market_price from ecs_goods where is_delete != 1 and  cat_id= ".$data[0]['cat_id']."  order by virtual_sales desc limit 0,4";
            $goods_data = $this->db->getAll($sql);
        }

        $aData['goods_data'] = $goods_data;
        $aData['category_data'] = $data;

        $res['status']='succ';
        $res['response']= $aData;
        return $res;

    }

    /* 首页商品分类2*/
    public function get_ad_category2()
    {
        $res = array(
            'status'=>'fail',
            'message'=>'',
            'response'=>array(),
        );
        $sql = "select * from ecs_category where shouye_img_status !=1 and shouye_img_status !=0 and is_show =1";
        $data = $this->db->getAll($sql);

        foreach ($data as $k=>$v){
            $sql = "select goods_id,goods_img,shop_price,goods_name,market_price from ecs_goods where is_delete != 1 and  cat_id= ".$v['cat_id']."  order by virtual_sales desc limit 0, 4";
            $data[$k]['goods_data'] = $this->db->getAll($sql);
//            if(strlen($v['ad_code'])=='23'){
//                $data[$k]['ad_code'] = 'http://'.$_SERVER['HTTP_HOST'].'/data/afficheimg/'.$v['ad_code'];
//            }
        }

        $res['status']='succ';
        $res['response']= $data;
        return $res;

    }

    public function get_issue1_image()
    {
        $res = array(
            'status'=>'fail',
            'message'=>'',
            'response'=>array(),
        );
        $sql  = "select a.position_id,a.position_name,b.ad_link,b.ad_code from ecs_ad_position a left join ecs_ad b on a.position_id = b.position_id where a.position_name like '%Issue1图%' and b.enabled ='1'";
        $data = $this->db->getAll($sql);
        foreach ($data as $k=>$v){
            if(strlen($v['ad_code'])=='23'){
                $data[$k]['ad_code'] = 'http://'.$_SERVER['HTTP_HOST'].'/data/afficheimg/'.$v['ad_code'];
            }
        }
        $res['status']='succ';
        $res['response']= $data;
        return $res;

    }


    /* Issue2图片*/
    public function get_issue2_image()
    {
        $res = array(
            'status'=>'fail',
            'message'=>'',
            'response'=>array(),
        );
        $sql  = "select a.position_id,a.position_name,b.ad_link,b.ad_code from ecs_ad_position a left join ecs_ad b on a.position_id = b.position_id where a.position_name like '%Issue2图%' and b.enabled ='1'";
        $data = $this->db->getAll($sql);
        foreach ($data as $k=>$v){
            if(strlen($v['ad_code'])=='23'){
                $data[$k]['ad_code'] = 'http://'.$_SERVER['HTTP_HOST'].'/data/afficheimg/'.$v['ad_code'];
            }
        }
        $res['status']='succ';
        $res['response']= $data;
        return $res;

    }

    /* Issue3图片*/
    public function get_issue3_image()
    {
        $res = array(
            'status'=>'fail',
            'message'=>'',
            'response'=>array(),
        );
        $sql  = "select a.position_id,a.position_name,b.ad_link,b.ad_code from ecs_ad_position a left join ecs_ad b on a.position_id = b.position_id where a.position_name like '%Issue3图%' and b.enabled ='1'";
        $data = $this->db->getAll($sql);
        foreach ($data as $k=>$v){
            if(strlen($v['ad_code'])=='23'){
                $data[$k]['ad_code'] = 'http://'.$_SERVER['HTTP_HOST'].'/data/afficheimg/'.$v['ad_code'];
            }
        }
        $res['status']='succ';
        $res['response']= $data;
        return $res;

    }


    /*Issue2商品 --- 新品*/
    public function get_list_xinpin()
    {

        // $sql = "select goods_id,goods_img,shop_price,goods_name from ecs_goods where is_hot = 1 and is_on_sale = 1 order by sort_order desc limit 0, 6";
        $sql = "select goods_id,goods_img,shop_price,goods_name,market_price from ecs_goods where is_new = 1  order by virtual_sales desc limit 0,4 ";

        $img_list = $this->db->getAll($sql);
        foreach ($img_list as $key => $value) {

            $goods_img = $this->get_goods_images($value['goods_id']);
            $img_list[$key]['goods_img'] = $goods_img[0]['l_url'];
            $img_list[$key]['shop_price'] = $value['shop_price'];
            $img_list[$key]['goods_name'] = $value['goods_name'];
            
        }
        $data = array(
            'status' => 'succ',
            'response' => $img_list,
            'message' => ''
        );
       
        return $data;
    }

    /*获取首页精品*/
    public function get_list_jingpin()
    {

        $sql = "select goods_id,goods_img,shop_price,goods_name,market_price from ecs_goods where is_best = 1 and is_on_sale = 1";
        $img_list = $this->db->getAll($sql);
        foreach ($img_list as $key => $value) {

            $goods_img = $this->get_goods_images($value['goods_id']);
            $img_list[$key]['goods_img'] = $goods_img[0]['l_url'];
            $img_list[$key]['shop_price'] = $value['shop_price'];
            $img_list[$key]['goods_name'] = $value['goods_name'];
            
        }
        $data = array(
            'status' => 'succ',
            'response' => $img_list,
            'message' => ''
        );
       
        return $data;
    }
     /*获取首页促销*/
    public function get_list_cuxiao()
    {

        // $sql = "select goods_id,goods_img,shop_price,goods_name from ecs_goods where is_hot = 1 and is_on_sale = 1 order by sort_order desc limit 0, 6";

        $sql = "select goods_id,goods_img,shop_price,goods_name,market_price from ecs_goods where is_hot = 1  order by sort_order desc ";

        $img_list = $this->db->getAll($sql);
        foreach ($img_list as $key => $value) {

            $goods_img = $this->get_goods_images($value['goods_id']);
            $img_list[$key]['goods_img'] = $goods_img[0]['l_url'];
            $img_list[$key]['shop_price'] = $value['shop_price'];
            $img_list[$key]['goods_name'] = $value['goods_name'];
            
        }
        $data = array(
            'status' => 'succ',
            'response' => $img_list,
            'message' => ''
        );
       
        return $data;
    }


    /*确认收货*/
    public function queren_order($params){
        $sql = "select * from ecs_order_info where user_id = ".$params['member_id']." and order_sn =".$params['order_id']." and shipping_status ='2'";
        $order_data = $this->db->getAll($sql);
        if($order_data){
            return array('status'=>'fail', 'message'=>'订单号已确定', 'response' =>$params);
        }
        $sql1 = "update ecs_order_info set shipping_status ='2' where  user_id = ".$params['member_id']." and order_sn =".$params['order_id'];
        $this->db->getAll($sql1);
        $data = array(
            'status' => 'succ',
            'response' => '',
            'message' => '确认收货成功'
        );
        return $data;

    }


}
