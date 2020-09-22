<?php
 error_reporting(E_ALL);

define('APP_KEY', 'wolf100000001');
define('APP_SERECT', 'I4MoPIPaVCc8M5fnfruLBd');//秘钥 

/**
$allowed_services = array(
    'user.reg',
    'user.login'
);

if(!in_array($_REQUEST['func'],$allowed_services)){
    $data = array(
        'fail'=>'Invalid API call'
    );
    exit(return_msg($data));
}**/
define('IN_ECS', true);

require(dirname(__FILE__) . '/../includes/init.php');
include(dirname(__FILE__).'/../data/config.php');
include(dirname(__FILE__).'/AlipayTradeAppPayRequest.php');
include(dirname(__FILE__).'/AopClient.php');
include(dirname(__FILE__).'/WxpayAppSDK.php');
include(dirname(__FILE__).'/apiclass.php');
include(dirname(__FILE__).'/lang.php');
ini_set('display_errors',1);


if ($_GET['insert']) {
   $_REQUEST['method'] = 'mapi.chat.insert_chat';
}elseif ($_GET['select']){
    $_REQUEST['method'] = 'mapi.chat.chat_info';
}elseif ($_GET['sms']){
    $_REQUEST['method'] = 'mapi.order.get_sms_send';
}else{

if(create_sign($_POST) != $_POST['sign']){
    $data = array(
        'rsp'=>'fail',
        'data'=>'',
        'res'=>'sign error'
    );
    exit(json_encode($data));
}

}




$func = explode('.',$_REQUEST['method']);


if(count($func) == 0){
    $data = array(
        'rsp'=>'fail',
        'data'=>'',
        'res'=>'Invalid API dir'
    );
    exit(return_msg($data));
}
if(count($func) == 4){
    $dir_name = $func[1];
    $class_name = $func[2];
    $function_name = $func[3];
}else{
    $dir_name = '';
    $class_name = $func[1];
    $function_name = $func[2];
}




if(include_once(realpath(dirname(__FILE__)).'/app/'.$dir_name.'/'.$class_name.'.php')){
    $parmas = array();
    foreach($_POST as $key=>$item){
        $parmas[$key] = check_str($item);
    }
    
    $class = new $class_name;
    $return = call_user_func_array(array(&$class,$function_name),array($parmas));

    $return['message'] = __($return['message'],$parmas['lang']);



    $res['rsp'] = 'succ';
    $res['status'] = 'succ';
    $res['data'] = $return;
    $res['res'] = '';
    exit(json_encode($res));
}else{
    $data = array(
        'rsp'=>'fail',
        'data'=>'',
        'res'=>'Invalid API class'
    );
    exit(return_msg($data));
}

function __($string,$lan){
    global $lang; 
    if($lang[$lan][$string] != ''){
        return $lang[$lan][$string];
    }
    return $string;
}

function return_msg($res){
    return json_encode($res);
}

function errorlog($data){
    $fp = fopen("api.html","a+");
    $string = "<br>";
    $string =$string . "数据时间:".date("Y-m-d H:i:s")."<br>";

    foreach($data as $key => $item){
        $string = $string .$key."=>".$item."<br>";
    }
    $string .= $string ."<br>======================================<br>";
    fwrite($fp,$string);
    fclose($fp);
}

function create_sign($params) {
    return strtoupper(md5(assemble($params)));
}

function assemble($params) 
{
    if(!is_array($params))  return null;
    ksort($params, SORT_STRING);
    $sign = '';
    foreach($params AS $key=>$val){
        if($key == 'sign')  continue;
        if(is_null($val))   continue;
        if($val == '')   continue;
        if(is_bool($val))   $val = ($val) ? 1 : 0;
        $sign .= $key . (is_array($val) ? assemble($val) : $val);
    }
    $sign = $sign.APP_SERECT;
    return $sign;
}
function check_str( $string ) {
    $result = false;
    $var = filter_keyword( $string ); // 过滤sql与php文件操作的关键字
    if ( !empty( $var ) ) {
        if ( !get_magic_quotes_gpc() ) {    // 判断magic_quotes_gpc是否为打开
            $var = addslashes( $string );    // 进行magic_quotes_gpc没有打开的情况对提交数据的过滤
        }
        //$var = str_replace( "_", "\_", $var );    // 把 '_'过滤掉
        $var = str_replace( "%", "\%", $var );    // 把 '%'过滤掉
        $var = nl2br( $var );    // 回车转换
        $var = htmlspecialchars( $var );    // html标记转换
        $result = $var;
    }
    return $result;
}
function filter_keyword( $string ) {
    $keyword = 'select|insert|update|delete|\'|\/\*|\*|\.\.\/|\.\/|union|into|load_file|outfile';
    $arr = explode( '|', $keyword );
    $result = str_ireplace( $arr, '', $string );
    return $result;
}