<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/6/15
 * Time: 15:24
 */
namespace AppParents\Controller;
class ConsumeController extends CommonController{
    //会员消费统计
    public function index_api(){
        before_api();
        checkLogin();
        checkAuth();

        $page = I('page',1,'intval');
        $pagesize = I('pagesize',10,'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page.','.$pagesize;


        $start_time=I('start_time');
        $end_time=I('end_time');
        $type=I('type');
        $category=I('category');
        $member_id=I('member_id');
        $consumeModel=D('Consume');
        $data=$consumeModel->get_consume_list($start_time,$end_time,$type,$category,$member_id,$page);
        $total=$consumeModel->get_consume_count($start_time,$end_time,$type,$category,$member_id);

        foreach($data as $k => $v){
            $s=$k-1;
            if($v['content']==$data[$s]['content']){
                $data[$k]['count']=$data[$k]['count']+$data[$s]['count'];
                $data[$k]['money']=$data[$k]['count']*$data[$k]['price'];
                $data[$s]='';
            }
        }

        foreach($data as $k => $v){
            $i=0;
            if($v){
                $d[$i]=$v;
                $i++;
            }
        }

        if($d){
            Ret(array('code'=>1,'data'=>$d,'total' => $total, 'page_count' => ceil($total/$pagesize)));
        }else{
            Ret(array('code'=>2,'info'=>'没有数据！'));
        }

    }

    //会员消费明细
    public function consume_view_api(){
        before_api();
        checkLogin();
        checkAuth();
        //$mall_id = session('mall.mall_id');
        $page = I('page',1,'intval');
        $pagesize = I('pagesize',10,'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page.','.$pagesize;

        //$start_time = I('start_time');
        //$end_time = I('end_time');
        //$category = I('category');
        $member_id = I('member_id');
        //$type = I('type');


        $ExpenseDetailModel=D('ExpenseDetail');
        $data=$ExpenseDetailModel->get_by_list($member_id,$page);
        $total=$ExpenseDetailModel->get_by_count($member_id);
       /* //$memberModel=D('Member');
        foreach($data as $k=>$v){
                    $model=D('Institution');
                    $institutionInfo=$model->get_institution_info($v['institution_id']);
                    $data[$k]['institution_name']=$institutionInfo[0]['name'];
            }
            $memberinfo=$memberModel->get_member_info_by_id($v['member_id']);
            $data[$k]['member_name']=$memberinfo[0]['name'];
        }*/
        if($data){
            Ret(array('code'=>1,'data'=>$data,'total' => $total, 'page_count' => ceil($total/$pagesize)));
        }else{
            Ret(array('code'=>2,'info'=>'没有数据！'));
        }

    }

    //会员消费查询
    public function consume_search_api(){
        before_api();
        checkLogin();
        checkAuth();
        $mall_id = session('mall.mall_id');
        $consumeModel=D('Consume');

        $keyword['begintime']=I('begintime','');
        if($keyword['begintime']==null){
            Ret(array('code'=>2,'info'=>'请选择开始时间！'));
        }
        $keyword['endtime']=I('endtime','');
        if($keyword['endtime']==null){
            Ret(array('code'=>2,'info'=>'请选择结束时间！'));
        }
        $keyword['institution_id']=I('institution_id','');
        if($keyword['institution_id']==null){
            Ret(array('code'=>2,'info'=>'请选择机构！'));
        }
        $keyword['consume_type']=I('consume_type','');
        if($keyword['consume_type']==null){
            Ret(array('code'=>2,'info'=>'请输入消费类别！'));
        }

        $keyword['member_id']=I('member_id','');
        if($keyword['member_id']==null){
            Ret(array('code'=>2,'info'=>'请输入会员号！'));
        }

        $data= $consumeModel->get_recharge_by_keyword($mall_id,$keyword);

        $data=$consumeModel->get_consume_list($mall_id);
        $memberModel=D('Member');
        foreach($data as $k=>$v){
            switch($v['type']){
                case 1:
                    $model=D('Institution');
                    $institutionInfo=$model->get_institution_info($v['institution_id']);
                    $data[$k]['institution_name']=$institutionInfo[0]['name'];
            }
            $memberinfo=$memberModel->get_member_info_by_id($v['member_id']);
            $data[$k]['pic']=$memberinfo[0]['pic'];
            $data[$k]['member_name']=$memberinfo[0]['name'];
        }
        if($data){
            Ret(array('code'=>1,'data'=>$data));
        }else{
            Ret(array('code'=>2,'info'=>'没有数据！'));
        }

    }


    /**
     * 会员返还明细
     */
    public function member_detail_api()
    {
        before_api();
        checkLogin();
        checkAuth();

        $member_id = 1;
        $time = I('time');
        $page = I('page', 1, 'intval');
        $pagesize = I('pagesize', 10, 'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page . ',' . $pagesize;
        $memberRebackDetailModel = D('MemberRebackDetail');
        $count = $memberRebackDetailModel->get_reback_count($time,$member_id);
        $data = $memberRebackDetailModel->get_member_reback($time,$member_id, $page, $fields = null);
        if ($data) {
            Ret(array('code' => 1, 'data' => $data, 'total' => $count, 'page_count' => ceil($count / $pagesize)));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据！'));
        }
    }

    //课程返还统计
    public function course_reback_retistic_api(){
        before_api();
        checkLogin();
        checkAuth();
        $mall_id = session('mall.mall_id');
        $page = I('page',1,'intval');
        $pagesize = I('pagesize',10,'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page.','.$pagesize;
        $time=I('time');
        $member_id=I('member_id');
        $consumeModel=D('Courseback');
        $count=$consumeModel->get_count($time,$member_id);
        $data=$consumeModel->get_list($time,$member_id,$page);
        if($data){
            Ret(array('code'=>1,'data'=>$data,'total' => $count, 'page_count' => ceil($count/$pagesize)));
        }else{
            Ret(array('code'=>2,'info'=>'没有数据！'));
        }

    }


    //课程返还统计
    public function course_reback_ditail_api(){
        before_api();
        checkLogin();
        checkAuth();
        $mall_id = session('mall.mall_id');
        $page = I('page',1,'intval');
        $pagesize = I('pagesize',10,'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page.','.$pagesize;
        $time=I('time');
        $member_id=I('member_id');
        $consumeModel=D('CoursebackDetail');
        $count=$consumeModel->get_count($mall_id,$time,$member_id);
        $data=$consumeModel->get_list($mall_id,$time,$member_id,$page);
        if($data){
            Ret(array('code'=>1,'data'=>$data,'total' => $count, 'page_count' => ceil($count/$pagesize)));
        }else{
            Ret(array('code'=>2,'info'=>'没有数据！'));
        }

    }

    /**
     *
     * app会员消费明细
     */
    public function app_consume_view_api(){
//        checkLogin();
//        checkAuth();
        $member_id = I('member_id');
        $page = I('page',1,'intval');
        $pagesize = I('pagesize',10,'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page.','.$pagesize;
        $start_time = I('start_time');
        $end_time = I('end_time');
        $consumeModel=D('Consume');
        $courseModel= D('Course');
        $data=$consumeModel->get_app_consume_list($start_time,$end_time,$member_id,$page);
        $count=$consumeModel->get_app_consume_count($start_time,$end_time,$member_id);
        foreach($data as $key =>$value) {
            $course = $courseModel->get_info($value['course_id']);
            $data[$key]['course_pic'] = $course[0]['pic'];
            $data[$key]['course_name'] = $course[0]['name'];
            $data[$key]['price'] = empty($value['pic']) ? 0 : $value['pic'];
        }
        if($data){
            Ret(array('code'=>1,'data'=>$data,'total'=>$count,'page_count'=>ceil($count/$pagesize)));
        }else{
            Ret(array('code'=>2,'info'=>'没有数据'));
        }
    }

    public function app_consume_detail(){
//        checkLogin();
//        checkAuth();
        $member_id = I('id');
        $page = I('page',1,'intval');
        $pagesize = I('pagesize',10,'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page.','.$pagesize;

        $start_time = I('start_time');
        $end_time = I('end_time');
        $consumeModel=D('Consume');
        $courseModel= D('Course');
        $data=$consumeModel->get_app_consume_list($start_time,$end_time,$member_id,$page);
        $count=$consumeModel->get_app_consume_count($start_time,$end_time,$member_id);
        foreach($data as $key =>$value) {
            $course = $courseModel->get_info($value['course_id']);
            $data[$key]['course_pic'] = $course[0]['pic'];
            $data[$key]['course_name'] = $course[0]['name'];
        }
        if($data){
            Ret(array('code'=>1,'data'=>$data,'total'=>$count,'page_count'=>ceil($count/$pagesize)));
        }else{
            Ret(array('code'=>2,'info'=>'没有数据'));
        }
    }


    public function memberDetail(){
        $member_id = I('member_id');
        $page = I('page',1,'intval');
        $pagesize = I('pagesize',10,'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page.','.$pagesize;
        $expenseDetailModel = D('ExpenseDetail');
        $count =$expenseDetailModel->get_by_count($member_id);
        $res=$expenseDetailModel->get_by_list($member_id,$page);
        if($res){
            Ret(array('code'=>1,'data'=>$res,'total'=>$count,'page_count' => ceil($count/$pagesize)));
        }else{
            Ret(array('code'=>2,'data'=>'没有数据'));
        }
    }
}