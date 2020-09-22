<?php
/**
    @author     :   dev@yin-duo.com
    @since      :   2018/10/11
**/
class ectools extends apiclass
{
    /**
     * 获取三级地区信息接口
     * @return array 三级地区数组
     */
    public function get_regions_list($param){
    	$region_id = intval($param['region_id']) > 0 ? intval($param['region_id']) : 0;
        if ($region_id==0) {
	        $region_list_sql = "select * from ecs_region where region_type = 0";
	        $region_lists = $this->db->getAll($region_list_sql);
	        $arr_regions = array();
	        foreach ($region_lists as $k => $v) {
	        	$arr_regions[$k]['region_id']=$v['region_id'];
	        	// $arr_regions[$k]['p_region_id']='0';
	        	$arr_regions[$k]['local_name']=$v['region_name'];
	        	// $arr_regions[$k]['ordernum']='';
	        	$arr_regions[$k]['region_path']=$v['region_id'];
	        	// $arr_regions[$k]['step']='1';
	        	// $arr_regions[$k]['child_count']='1';
	        }
        }else{    
            $region_list_sql = "select * from ecs_region where parent_id = " . $region_id;
	   		$region_lists = $this->db->getAll($region_list_sql);
			$arr_regions = array();
	        foreach ($region_lists as $k => $v) {
	        	$arr_regions[$k]['region_id']=$v['region_id'];
	        	// $arr_regions[$k]['p_region_id']='0';
	        	$arr_regions[$k]['local_name']=$v['region_name'];
	        	// $arr_regions[$k]['ordernum']='';
	        	$arr_regions[$k]['region_path']=$v['region_id'];
	        	// $arr_regions[$k]['step']='1';
	        	// $arr_regions[$k]['child_count']='1';
	        }	   	
        }
        return array('status'=>'succ', 'message'=>'', 'response' => $arr_regions);
    }

    public function get_checkout_payments($params)
    {

	    $payment_list = available_payment_list(1, $cod_fee);
	    foreach ($payment_list as $key => $value) {
	    	if($params['lang'] == 'en'){
	    		$name =$value['pay_name_en'];
	    	}else{
	    		if($value['pay_name'] == '<font color="#FF0000">天工收银</font>'){
	    			$name = "天工收银";
	    		}else{
	    			$name =$value['pay_name'];
	    		}
	    		
	    	}
	    	$data[$value['pay_code']] = array(
	    		'pay_brief' => $value['pay_desc'],
	    		'payment_id' => $value['pay_code'],
	    		'pay_id' => $value['pay_id'],
	    		'payment_name' => $name,
	    		'payout_type' => $value['pay_code']
	    	);
	    }
        return array('status'=>'succ', 'message'=>'', 'response' => $data);
    }
    /**
     * 获取商城基本信息
     * @return text 商城基本信息
     */
    
    public function get_siteinfo($param) {
        $sql = "select * from app_config";
        $data = $GLOBALS['db']->getAll($sql);
        foreach ($data as $k=>$v){
            if($v['k']=='version'){
                $version = $v['val'];
            }
            if($v['k']=='url'){
                $url = $v['val'];
            }
            if($v['k']=='day'){
                $day = $v['val'];
            }
            if($v['k']=='tel'){
                $tel = $v['val'];
            }
        }


        $return['register_license']=$version;
        $return['shop_telphone']=$tel;
//        $return['shop_mobile']='88888888';
        $return['shop_mobile_logo']="http://".$_SERVER['HTTP_HOST']."/".$url;
        $return['shop_kefu'] = $day;
        return array('status'=>'succ', 'message'=>'', 'response' => $return);
    }
}