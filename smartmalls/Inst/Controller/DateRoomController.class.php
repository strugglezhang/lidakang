<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/7/10
 * Time: 14:57
 */
/*
 * 教室预定
 */
namespace Inst\Controller;
class DateRoomController extends CommonController {
    /*
     * 教室预定
     */
    public function index(){
        checkLogin();
        checkAuth();
        $data['institution_id']=session('inst.institution_id');
        $data['course_id']=I('course_id',0,'intval');
        if(!$data['course_id']){
            Ret(array('code'=>2,'info'=>'请选择课程！'));
        }
        $data['start_time']=I('start_time');
        if(!$data['start_time']){
            Ret(array('code'=>2,'info'=>'请选择开始时间！'));
        }
        $data['end_time']=I('end_time');
        if(!$data['end_time']){
            Ret(array('code'=>2,'info'=>'请选择结束时间！'));
        }
        $data['state']=1;
        $data['submmit_time']=date("Y-m-d H:i:s");
        $model=D('DateRoom');
        $result=$model->dateroom($data);
        if ($result) {
            Ret(array('code' => 1, 'info' => '保存成功！'));
        }else{
            Ret(array('code' => 2, 'info' => '保存失败,系统出错！'));
        }
    }

    /*
     * 教室预定订单
     */
    public function order(){

        checkLogin();

        checkAuth();

        $state = I('state',2,'intval');
        $is_manager = true;
        if ($state !== 2 && !$is_manager) {
            $state = 2;
        }
        $institution_id=session('inst.institution_id');
        $keyword = I('keyword');
        $page = I('page',1,'intval');
        $pagesize = I('pagesize',10,'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page.','.$pagesize;
        $model=D('DateRoom');
        $count = $model->get_count($institution_id,$state=0,$keyword );
        $data=$model->get_list($institution_id ,$state=0, $keyword , $page, $fields = null);
        if($data){
            Ret(array('code'=>1,'data'=>$data,'total' => $count, 'page_count' => ceil($count/$pagesize)));
        }else{
            Ret(array('code'=>2,'info'=>'没有数据！'));
        }
    }


}