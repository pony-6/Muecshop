<?php
/**
@author     :   dev@yin-duo.com
@since      :   2018/11/23
 **/
class chat extends apiclass
{
    /* 过滤用户输入的内容 */
    private function xxs($data)
    {
        $data = htmlspecialchars(addslashes($data));
        return $data;
    }

    /* 二位数组根据字段排序 */
    private function arraySequence($array, $field, $sort = 'SORT_DESC')
    {
        $arrSort = array();
        foreach ($array as $uniqid => $row) {
            foreach ($row as $key => $value) {
                $arrSort[$key][$uniqid] = $value;
            }
        }
        array_multisort($arrSort[$field], constant($sort), $array);
        return $array;
    }

    /* 客服聊天插入接口 */
    public function insert_chat($params)
    {

        $res = array(
            'status' => 'fail',
            'response' => array(),
            'message' => '',
        );
        $user_id = $params['member_id'];
        $staff_id = $params['staff_id'];
        $content = $params['content'];
        $content = $this->xxs($content);
        /* 0:用户发出  1:客服发出 */
        $type = $params['type'];
        if(!$type){
            $type = '0';
            $staff_id = '0';
        }
        $is_staff = $params['is_staff'];
        if(!$is_staff){
            $is_staff = '0';
        }
        $last_modify = time();
        if(empty($params['content'])){
            $res['message'] = '发送内容不能为空';
            return $res;
        }
        if(empty($user_id)  || !isset($params['type']) || !isset($params['content']) || !isset($params['is_staff'])){
            $res['message'] = '参数错误';
            return $res;
        }
        $sql = "insert into ecs_chat (user_id,staff_id,content,per_type,see_type,last_modify,is_staff) values ('" . $user_id . "','" . $staff_id . "','" . $content . "','".$type."','0','".$last_modify."','".$is_staff."')";
        $data = $GLOBALS['db']->query($sql);
        if($data){
            $res['status'] = 'succ';
            $res['message'] = '发送成功';
            return $res;
        }else{
            $res['message'] = '发送失败';
            return $res;
        }
    }

    /* 用户客服聊天详情 */
    public function chat_info($params)
    {
        $res = array(
            'status' => 'fail',
            'response' => array(),
            'message' => '',
        );
        $user_id = $params['member_id'];
        /* 谁获取聊天列表 type:1为用户获取客服 0为客服获取 */
        $type = $params['type'];
        if(empty($user_id) || !isset($params['type'])){
            $res['message'] = '参数错误';
            return $res;
        }
        /* 更新聊天消息为已读 */
        $sql = "update ecs_chat set see_type='1' where user_id='".$user_id."' and is_staff='1' and per_type='".$type."'";
        $GLOBALS['db']->query($sql);

        /* 聊天详情 */
        $sql = "select a.*,b.user_pic,b.user_name from ecs_chat a left join ecs_users b on a.user_id=b.user_id where a.user_id='".$user_id."' and a.is_staff='1'";
        $data = $GLOBALS['db']->getAll($sql);
        /* 按时间排序 */
        $data = $this->arraySequence($data,'last_modify','SORT_ASC');
        if($data){
            $res['status'] = 'succ';
            $res['message'] = '获取成功';
            $res['response'] = $data;
            return $res;
        }else{
            $res['status'] = 'succ';
            $res['response'] =array();
            return $res;
        }
    }

    /* 会员聊天插入 */
    public function user_insert_chat($params)
    {

        $res = array(
            'status' => 'fail',
            'response' => array(),
            'message' => '',
        );
        $user_id = $params['member_id'];
        $response_id = $params['response_id'];
        $content = $params['content'];
        $content = $this->xxs($content);
        if(empty($content)){
            $res['message']='发送消息不能为空';
            return $res;
        }
        $sql = "insert into ecs_user_chat (user_id,response_id,content,last_modify) values ('".$user_id."','".$response_id."','".$content."','".time()."')";
        $data = $GLOBALS['db']->query($sql);
        if($data){
            $res['status']='succ';
            $res['message']='发送成功';
            return $res;
        }else{
            $res['message']='发送失败，请重新发送';
            return $res;
        }
    }

    /* 用户聊天详情 */
    public function user_chat_info($params)
    {
        $res = array(
            'status' => 'fail',
            'response' => array(),
            'message' => '',
        );
        $user_id = $params['member_id'];
        $response_id = $params['response_id'];
        if(!$user_id && $response_id){
            $res['status'] = 'fail';
            $res['message'] = "parameter error";
            return $res;
        }
        $sql = "select * from ecs_user_chat where user_id='".$user_id."' and response_id='".$response_id."' or user_id='".$response_id."' and response_id='".$user_id."' order by last_modify";
        $data = $GLOBALS['db']->getAll($sql);
        $sql = "update ecs_user_chat set see_type='1' where user_id='".$response_id."' and response_id='".$user_id."'";
        $GLOBALS['db']->query($sql);
//        $sql = "select a.* from ecs_user_chat a left join ecs_users b on a.user_id=b.user_id left join ecs_users c on a.response_id=c.user_id where user_id='".$user_id."' and response_id='".$response_id."' or user_id='".$response_id."' and response_id='".$user_id."' order by last_modify";
        foreach ($data as $k=>$v){
            $sql = "select * from ecs_users where user_id='".$data[$k]['user_id']."'";
            $result = $GLOBALS['db']->getRow($sql);
            $data[$k]['pic'] = $result['user_pic'];
            $data[$k]['name'] = $result['user_name'];
        }
        if($data){
            $res['status']='succ';
            $res['response'] = $data;
            return $res;
        }else{
            $res['status'] ='succ';
            $res['response'] = array();
            return $res;
        }
    }























    public function staff_chat_list()
    {
        $sql = "select * from ecs_chat where is_staff='1' group by user_id";
    }

    /* 用户聊天列表 */
    public function user_chat_list()
    {
        $res = array(
            'status' => 'fail',
            'response' => array(),
            'message' => '',
        );
        $user_id = $_POST['user_id'];
        $staff_id = $_POST['staff_id'];
        if($user_id && $staff_id){
            $res['message'] = '参数错误';
            return $res;
        }
        if(empty($user_id) && empty($staff_id)){
            $res['message'] = '参数错误';
            return $res;
        }
        $staff_id ? $user_type = 'staff_id' : $user_type = 'user_id';
        $staff_id ? $id = $staff_id : $id = $user_id;
        $staff_id ? $type='1' : $type='0';
        $sql = "select distinct $id as id from ecs_chat where $user_type ='".$id."' and per_type='".$type."'";
        $data = $GLOBALS['db']->getAll($sql);
//        echo '<pre>';
        var_dump($data);
        $arr = array_unique($data, SORT_REGULAR);
        echo '<pre>';
//        var_dump($arr);die;

    }



}