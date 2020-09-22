<?php

class article extends apiclass
{
    //消息中心列表
    function get_article_message()
    {
        $res = array(
            'status' => 'fail',
            'response' => array(),
            'message' => '',
        );
        $sql = "select * from ".$GLOBALS['ecs']->table('article_cat')." where cat_id='12'";
        $res['response'][0] = $GLOBALS['db']->getRow($sql);
        $sql ="select count(article_id) total from ".$GLOBALS['ecs']->table('article')." where cat_id='12'";
        $total = $this->db->getRow($sql);
        $res['response'][0]['total'] =$total['total'];
        $sql ="select * from ".$GLOBALS['ecs']->table('article')." where cat_id='12'";
        $res['response'][0]['data'] = $GLOBALS['db']->getAll($sql);
        $sql = "select * from ".$GLOBALS['ecs']->table('article_cat')." where cat_id='11'";
        $res['response'][1] = $GLOBALS['db']->getRow($sql);
        $sql ="select count(article_id) total from ".$GLOBALS['ecs']->table('article')." where cat_id='11'";
        $total = $this->db->getRow($sql);
        $res['response'][1]['total'] =$total['total'];
        $sql ="select * from ".$GLOBALS['ecs']->table('article')." where cat_id='11'";
        $res['response'][1]['data'] = $GLOBALS['db']->getAll($sql);
        $res['response'] = array_values($res['response']);
        $res['status'] = 'succ';
        return $res;
    }

    //获取系统公告
    public function get_sys_msg()
    {
        $res = array(
            'status' => 'fail',
            'response' => array(),
            'message' => '',
        );
        $id = $_POST['cat_id'];
        $sql = "select * from ".$GLOBALS['ecs']->table('article')." where cat_id='".$id."'";
        $data = $this->db->getAll($sql);
        if($data){
            $res['response'] = $data;
            $res['status'] = 'succ';
        }
        return $res;

    }

    //获取消息详情
    public function get_sys_detail()
    {
        $res = array(
            'status' => 'fail',
            'response' => array(),
            'message' => '',
        );
        $id = $_POST['article_id'];
        $sql = "select * from ".$GLOBALS['ecs']->table('article')." where article_id='".$id."'";
        $data = $this->db->getRow($sql);
        if($data){
            $res['response'] = $data;
            $res['status'] = 'succ';
        }
        return $res;

    }

    //帮助中心列表
    function get_article_help()
    {
        $res = array(
            'status' => 'fail',
            'response' => array(),
            'message' => '',
        );
        $sql = "select * from ".$GLOBALS['ecs']->table('article')." where help_type='1' and is_open='1' order by article_type desc";
        $data = $this->db->getAll($sql);
        $res['response'] = $data;
        $res['status'] = 'succ';
        return $res;
    }

    //帮助中心详情
    function get_article_help_info()
    {
        $id = $_POST['article_id'];
        $res = array(
            'status' => 'fail',
            'response' => array(),
            'message' => '',
        );
        if(!$id){
            $res['message'] = '参数错误';
            return $res;
        }
        $sql = "select * from ".$GLOBALS['ecs']->table('article')." where article_id='".$id."' and is_open='1' ";
        $data = $this->db->getRow($sql);
        if($data){
            $res['status'] = 'succ';
            $res['response'] = $data;
            return $res;
        }
    }

    //  about_us 关于我们
    public function get_about_us()
    {
        $res = array(
            'status' => 'fail',
            'response' => array(),
            'message' => '',
        );
        //关于我们这个谁写死了？？？
        $sql = "select * from ". $GLOBALS['ecs']->table('article')." where article_id='80'";
        $data = $this->db->getRow($sql);
        if($data){
            $res['status'] = 'succ';
            $res['response'] = $data;
        }
        return $res;
    }

    // 首页新闻资讯
    // 有图且显示 || 有图且显示置顶
    public function get_index_news()
    {
        $sql = "select * from ecs_article where file_url <> '' and is_open='1' and article_type = '1'";
        $news_res = $this->db->getAll($sql);
        $sql = "select * from ecs_article where file_url <> '' and is_open='1' and article_type = '0'";
        $news_res_normal = $this->db->getAll($sql);
        foreach ($news_res as $key => $value) {
            $news_res[$key]['file_url'] = 'http://'.$_SERVER['HTTP_HOST'].'/'.$value['file_url'];
        }
        foreach ($news_res_normal as $key => $value) {
            $news_res_normal[$key]['file_url'] = 'http://'.$_SERVER['HTTP_HOST'].'/'.$value['file_url'];
        }
        $response = array(
            'top' => $news_res,
            'normal' => $news_res_normal,
        );
        $data = array(
            'status' => 'succ',
            'response' => $response,
            'message' => '',
        );
        return $data;
    }
}