<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/9/5
 * Time: 15:24
 */
namespace AppParents\Controller;
class InstController extends CommonController{
    public function app_inst_view_api()
    {
        before_api();
        checkAuth();
        checkLogin();
        $id = I('id');
        if ($id < 1) {
            Ret(array('code' => 2, 'info' => '参数（id）有误!'));
        }
        $res= D('Institution')->app_inst_info($id);
        $instStaffModel=D('InstStaff');
        if ($res){
            foreach($res as $key=>$value){
                $count = $instStaffModel->get_count($id);
                $insti =$instStaffModel ->get_info($id);
                $res[$key]['teacher']=$insti;
            }
            Ret(array('code' => 1, 'data' =>$res,'total'=>$count));
        } else {
            Ret(array('code' => 2, 'info' => '获取相关数据失败'));
        }
    }

    public function app_course_api(){
        before_api();
        checkLogin();
        checkAuth();
        $institution_id =I('id');
        $page = I('page',1,'intval');
        $pagesize = I('pagesize',10,'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page.','.$pagesize;
        $courseModel = D('Course');
        $instModel = D('Institution');
        $count = $courseModel ->get_app_count($institution_id);
        $data = $courseModel ->get_by_list($institution_id,$page,$fields = null);
//        var_dump($data);die;
        foreach($data as $key=>$item){
            $institution =$instModel ->get_inst_by_id($item['institution_id']);
            $data[$key]['institution_name'] = $institution[0]['name'];
        }
        if($data){
            Ret(array('code' =>1,'data'=>$data,'total'=>$count,'page_count'=>ceil($count/$pagesize)));
        }else{
            Ret(array('code' =>2,'info'=>'没有数据'));
        }
    }

    public function app_inst_course_api()
    {
        $category_id = I('category_id',0,'intval');
           $page = I('page',1,'intval');
        $pagesize = I('pagesize',10,'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page.','.$pagesize;
        $instModel = D('Institution');
        $count = $instModel->get_app_count($category_id);
        $data = $instModel->get_app_list($category_id,$page, $fields=null);
        if ($data) {
            Ret(array('code' => 1, 'data' => $data, 'total' => $count,'page_count'=>ceil($count/$pagesize)));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据！'));
        }
    }

    /**
     * 机构简介
     */
    public function inst_content(){
        $id =I('id');
        if($id<1){
            Ret(array('code'=>2,'info'=>'参数有误'));
        }
//        $instModel = D('Institution');
        $data =D('Institution')->get_content($id);
        if($data){
            Ret(array('code'=>1,'data'=>$data[0]));
        }else{
            Ret(array('code'=>2,'info'=>'没有数据'));
        }
    }

    /**
     * 师之力量
     */
    public function inst_teacher(){
        $id =I('id');
        if($id<1){
            Ret(array('code'=>2,'info'=>'参数有误'));
        }
        $instStaffModel=D('InstStaff');
        $data= D('Institution')->get_isnt_info($id);
        foreach($data as $key=> $value){
            $insti =$instStaffModel ->get_info($data[0]['id']);
            $data[$key]['teacher']=$insti;
        }
        if($data){
            Ret(array('code'=>1,'data'=>$data[0]));
        }else{
            Ret(array('code'=>2,'info'=>'没有数据'));
        }
    }

    /**
     * 课程环境
     */
    public function inst_honor(){
        $id =I('id');
        if($id<1){
            Ret(array('code'=>2,'info'=>'参数有误'));
        }
        $data =D('Institution')->get_honor($id);
        if($data){
            $data[0]['imgs'] = explode(',',$data[0]['imgs']);
            Ret(array('code'=>1,'data'=>$data[0]));
        }else{
            Ret(array('code'=>2,'info'=>'没有数据'));
        }
    }

    /**
     * 机构相片
     */
    public function inst_pic(){
        $id =I('id');
        if($id<1){
            Ret(array('code'=>2,'info'=>'参数有误'));
        }
        $data =D('Institution')->get_pic($id);
        if($data){
            Ret(array('code'=>1,'data'=>$data[0]));
        }else{
            Ret(array('code'=>2,'info'=>'没有数据'));
        }
    }

    /**
     * 机构行业证书
     */
    public function inst_certificate(){
        $id =I('id');
        if($id<1){
            Ret(array('code'=>2,'info'=>'参数有误'));
        }
        $data =D('Institution')->get_certificate($id);
        if($data){
            $data[0]['certificate_img'] = explode(',',$data[0]['certificate_img']);
            Ret(array('code'=>1,'data'=>$data[0]));
        }else{
            Ret(array('code'=>2,'info'=>'没有数据'));
        }
    }

}