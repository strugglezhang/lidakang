<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/7/3
 * Time: 21:04
 */
namespace Inst\Controller;
class CourseDiscountController extends CommonController {

    public function get_times(){
          $start_time='2017-06-02 11:08:16';
         $end_time='2017-06-02 11:08:19';
        get_time($start_time,$end_time);
    }
    /*
    * 课程折扣管理api
    */
    public function index_api(){
        before_api();
        checkLogin();
        checkAuth();
        $institution_id =session('inst.institution_id');
        $state =I('state',0,'intval');
        if($state !==2 && !$institution_id){
            $state =2;
        }
        $category_id = I('category_id');
        $keyword = I('keyword');
        $page = I('page',1,'intval');
        $pagesize =I('pagesize',10,'intval');
        $pagesize =$pagesize < 1 ? 1:$pagesize;
        $pagesize =$pagesize > 50 ? 50 :$pagesize;
        $page =$page.','.$pagesize;
        $courseDiscountModel = D('CourseDiscount');
//        $courseCategoryModel = D('CourseCat');
//        var_dump($courseDiscountModel);die;
        $count =$courseDiscountModel->get_count($institution_id,$category_id,$keyword);
        $data =$courseDiscountModel->get_list($institution_id,$category_id,$keyword,$page,$fields = null);
//        var_dump($data);die;
        foreach($data as $key =>$item){
            $data[$key]['state'] =get_state($item['state']);
            $data[$key]['category'] = $this->get_cat_by_id($item['category_id']);
            $data[$key]['course'] = $this->get_course_by_number($item['number']);
        }
        if($data){
            Ret(array('code'=>1,'data' =>$data,'total' =>$count,'page_count'=>ceil($count/$pagesize)));
        }else{
            Ret(array('code' =>2,'info'=>'没有数据！'));
        }
    }

    private function get_course_by_number($number){
        $courseModel = D('Course');
        $array=explode('_',$number);
        foreach($array as $k => $v ) {
            $item = $courseModel->get_course_by_id($v);
            if ($item[0][name] != null) {
                $items[$k] = $item[0][name];
            }
            $data= implode('_', $items);
        }
        return $data;

    }


    private function get_cat_by_id($id){
        $catModel = D('Category');
        $array=explode('-',$id);
        foreach($array as $k => $v ) {
            $item = $catModel->get_cat($v);
//            var_dump($item);die;
            if ($item[0][name] != null) {
                $items[$k] = $item[0][name];
            }
            $data= implode('-', $items);
        }
        return $data;

    }


    /*
    * 教室折扣增删改api
    */

    public function coursediscount_dml_api(){
        checkLogin();
        before_api();
        checkAuth();
        $institution_id =session('inst.institution_id');
        $courseDiscountModel = D('CourseDiscount');
        $flag = I('flag');
        switch ($flag){
            case 'add':
                $data['institution_id'] = $institution_id;
                $data['type'] = I('type',0);
                $data['category_id'] = I('category_id',0);
//                if($data['category_id']==null){
//                    Ret(array('code'=>2,'info'=>'请选择教室类别！'));
//                }
                $data['number'] = I('number',0);

//                if($data['number']==null){
//                    Ret(array('code'=>2,'info'=>'请选择课程编号！'));
//                }
                $data['price_type'] = I('price_type',0);

                $data['start_time'] = I('start_time',0);
                if($data['start_time']==null){
                    Ret(array('code'=>2,'info'=>'请输入折扣开始时间!'));
                }
                $data['end_time'] = I('end_time',0);
                if($data['end_time']==null){
                    Ret(array('code'=>2,'info'=>'请输入折扣结束时间!'));
                }
                $data['discount'] = I('discount',0);
                if($data['discount']==null){
                    Ret(array('code' =>2,'info'=>'请输入折扣系数！'));
                }
                if($data['discount']>1||0>$data['discount']){
                    Ret(array('code' =>2,'info'=>'请正确输入折扣系数（大于0，小于1）！'));
                }
                $data['submitter']  = session('worker_name');
                $data['submitter_id']  = session('worker_id');
                $data['state']  = I('state',0);
                $data['check_type']  = I('check_type',0);
                $result =$courseDiscountModel ->add_course($data);
                if($result){
                    Ret(array('code' =>1,'info'=>'添加成功'));
                }else{
                    Ret(array('code' =>2,'info'=>'添加失败，系统出错'));
                }
                break;
            case 'update':
                $data['id'] =I('id');
                $data['institution_id'] = $institution_id;
                $data['type'] = I('type',0);
                $data['category_id'] = I('category_id',0);
//                if($data['category_id']==null){
//                    Ret(array('code'=>2,'info'=>'请选择教室类别！'));
//                }
                $data['number'] = I('number',0);
//                if($data['number']==null){
//                    Ret(array('code'=>2,'info'=>'请选择课程编号！'));
//                }
                $data['price_type'] = I('price_type',0);
//                $data['room_id'] = I('room_id',0);
//                $data['room_number'] = I('room_id',0,'intval');
                $data['start_time'] = I('start_time',0);
                if($data['start_time']==null){
                    Ret(array('code'=>2,'info'=>'请输入折扣开始时间!'));
                }
                $data['end_time'] = I('end_time',0);
                if($data['end_time']==null){
                    Ret(array('code'=>2,'info'=>'请输入折扣结束时间!'));
                }
                $data['discount'] = I('discount',0);
                if($data['discount']==null){
                    Ret(array('code' =>2,'info'=>'请输入折扣系数！'));
                }
                if($data['discount']>1||0>$data['discount']){
                    Ret(array('code' =>2,'info'=>'请正确输入折扣系数（大于0，小于1）！'));
                }
                $data['state']  = I('state',0);
                $data['check_type']  = I('check_type',4);
                $data['submitter']  = session('worker_name');
                $data['submitter_id']  = session('worker_id');
                $result = $courseDiscountModel->update_course($data);
                if($result){
                    Ret(array('code'=>1,'info'=>'修改成功'));
                }else{
                    Ret(array('code'=>2,'info'=>'修改失败，系统出错！'));
                }
                break;
            case 'delete':
                $id=I('id');
                if(!$id){
                    Ret(array('code'=>2,'info'=>'数据获取失败'));
                }
                $result =$courseDiscountModel ->del_coure($id);
                if($result){
                    Ret(array('code'=>1,'info'=>'删除成功'));
                }else{
                    Ret(array('code'=>2,'info'=>'删除失败，系统出错！'));
                }
                break;
            default:
                Ret(array('code' => 2, 'info' => 'Unknowed flag!'));
                break;
        }
    }

    /*
    *
    *课程折扣审核
    */
//    public function course_check_api(){
//        before_api();
//        checkAuth();
//        checkLogin();
//        $data['id'] = I('id');
//        if ($data['id'] < 1) {
//            Ret(array('code' => 2, 'info' => '参数（id）有误!'));
//        }
//        $data['state'] = I('check_state');
//        if ($data['state'] < 1) {
//            Ret(array('code' => 2, 'info' => '参数（check_state）有误!'));
//        }
//        $logdata['course_name']=I('course_name');
//        if ($logdata['course_name']) {
//            Ret(array('code' => 2, 'info' => '参数（course_name）有误!'));
//        }
//        $logdata['course_category']=I('course_category');
//        if ($logdata['course_category']) {
//            Ret(array('code' => 2, 'info' => '参数（course_category）有误!'));
//        }
//
//
//        $logdata['check_type']=I('state');
//        if ($logdata['check_type']==null) {
//            Ret(array('code' => 2, 'info' => '参数（state）有误!'));
//        }
//        if($logdata['check_type']==0){
//            $logdata['check_type']='课程计费新增';
//        }
//        if($logdata['check_type']==4){
//            $logdata['check_type']='课程计费修改';
//        }
//        $logdata['check_time']=date('Y-m-d H:i:s');
//        $logdata['checker']=session('worker_id');
//        $logdata['submitter']=I('submitter');
//        if($data['state']==1){
//            $logdata['result']='通过';
//        }
//        if($data['state']==2){
//            $logdata['result']='未通过';
//        }
//        $logModel=D('CourseCheckLog')->add($logdata);
//        $roomCheckoutModel = D('CourseDiscount');
//        $res= $roomCheckoutModel->save($data);
//        if($res){
//            Ret(array('code' => 1, 'info' => '保存成功'));
//        }else{
//            Ret(array('code' => 2, 'info' => '保存失败'));
//        }
//    }

    public function course_check_api(){
        before_api();
        checkAuth();
        checkLogin();
        $data['id'] = I('id');
        $data['state'] = I('state');
        if(intval($data['id']) <1 || intval($data['state']) <0){
            Ret(array('code' => 2, 'info' => '参数有误!'));
        }
        //step 2: 根据主键id 获取需要的字段信息
        $roomCheckoutModel = D('CourseDiscount');
        //step 3 ；修改审核表状态
        $editInfo = $roomCheckoutModel->updateState($data);
        if($editInfo){
            $info = $roomCheckoutModel->getAllField($data['id']);
            foreach($info as $key =>$item){
                $info[$key]['state'] =get_state($item['state']);
                $info[$key]['category'] = $this->get_cat_by_id($item['category_id']);
                $info[$key]['course'] = $this->get_course_by_number($item['number']);
            }
            //step:4 保存日志
            $log = array(
                'check_time' => date('Y-m-d H:i:s'),
                'course_name' => $info[0]['course'],
                'course_category ' => $info[0]['category'],
                'check_type' => $info[0]['check_type'],
                'submitter' => $info[0]['submitter'],
                'checker' => session('worker_name'),
                'check_state' => $info[0]['state'],
                'cours_id'=> $info[0]['id']
            );
            if($log['check_type']==0){
                $log['check_type']='课程计费新增';
            }
            if($log['check_type']==4){
                $log['check_type']='课程计费修改';
            }
            $logModel=D('CourseCheckLog');
            if($logModel->insertLog($log)){
                Ret(array('code'=>1,'data'=>'审核成功'));
            }
        }else {
            Ret(array('code'=>2,'info'=>'审核失败'));
        }



//        $data['id'] = I('id');
//        if ($data['id'] < 1) {
//            Ret(array('code' => 2, 'info' => '参数（id）有误!'));
//        }
////        if ($data['worker_id'] < 1) {
////            Ret(array('code' => 2, 'info' => '参数（worker_id）有误!'));
////        }
//        $logdata['course_name'] = I('course');
//        if (!$logdata['course_name']) {
//            Ret(array('code' => 2, 'info' => '参数（course_name）有误!'));
//        }
//        $logdata['course_category'] = I('category');
//        if (!$logdata['course_category']) {
//            Ret(array('code' => 2, 'info' => '参数（course_category）有误!'));
//        }
//
//        $logdata['check_type'] = I('check_type');
//        if ($logdata['check_type'] == null ) {
//            Ret(array('code' => 2, 'info' => '参数（check_type）有误!'));
//        }
//        if($logdata['check_type']==0){
//            $logdata['check_type']='课程计费新增';
//        }
//        if($logdata['check_type']==4){
//            $logdata['check_type']='课程计费修改';
//        }
//        $data['state'] = I('state');
//        if ($data['state'] < 1) {
//            Ret(array('code' => 2, 'info' => '参数（check_state）有误!'));
//        }
//        if($data['state']==1){
//            $logdata['check_state']='通过';
//        }
//        if($data['state']==2){
//            $logdata['check_state']='未通过';
//        }
//        $logdata['submitter'] = I('submitter');
//        if (!$logdata['submitter']) {
//            Ret(array('code' => 2, 'info' => '参数（submitter）有误!'));
//        }
//        $logdata['check_time'] = date('Y-m-d H:i:s');
//        $logdata['checker'] = session('worker_name');
//        $roomCheckoutModel = D('CourseDiscount');
//        $roomCheckoutModel->startTrans();//开启事务
//        $logModel=D('CourseCheckLog');
//        $log=$logModel->add($logdata);
//        if($log){
//            $result=$roomCheckoutModel->save($data);
//            $logModel->commit();//日志保存成功，提交
//            if($result){
//                Ret(array('code' => 1, 'info' => '审核成功'));
//            }else{
//                Ret(array('code' => 2, 'info' => '审核失败'));
//            }
//        }else{
//            $logModel->rollback();//日志保存失败，回滚
//            Ret(array('code' => 2, 'info' => '审核失败'));
//        }
    }

    /**
     * 课程折扣审核列表
     */
    public function course_check_list(){
    before_api();
    checkLogin();
    checkAuth();
    $institution_id =session('inst.institution_id');
    $state =I('state',0,'intval');
    if($state !==2 && !$institution_id){
        $state =2;
    }
    $category_id = I('category_id');
    $keyword = I('keyword');
    $page = I('page',1,'intval');
    $pagesize =I('pagesize',10,'intval');
    $pagesize =$pagesize < 1 ? 1:$pagesize;
    $pagesize =$pagesize > 50 ? 50 :$pagesize;
    $page =$page.','.$pagesize;
    $courseDiscountModel = D('CourseDiscount');
//    $courseCategoryModel = D('CourseCat');
    $count =$courseDiscountModel->get_count($institution_id,$category_id,$keyword);
    $data =$courseDiscountModel->get_list($institution_id,$category_id,$keyword,$page,$fields = null);
    foreach($data as $key =>$item){
        $data[$key]['state'] =get_state($item['state']);
        $data[$key]['category'] = $this->get_cat_by_id($item['category_id']);
        $data[$key]['course'] = $this->get_course_by_number($item['number']);
        $data[$key]['nnn']='1';
    }
    if($data){
        Ret(array('code'=>1,'data' =>$data,'total' =>$count,'page_count'=>ceil($count/$pagesize)));
    }else{
        Ret(array('code' =>2,'info'=>'没有数据！'));
    }
}
    /**
     * 课程折扣管理（详情）
     */
    public function discount_view_api(){
        before_api();
        checkAuth();
        checkLogin();
        $id = I('id');
        if($id<1){
            Ret(array('code' => 2, 'info' => '参数（id）有误！'));
        }
        $res = D('CourseDiscount')->get_course_infos($id);
//        var_dump($res);die;
       foreach($res as $key =>$item){
           $res[$key]['state'] =get_state($item['state']);
           $res[$key]['category'] = $this->get_cat_by_id($item['category_id']);
           $res[$key]['course'] = $this->get_course_by_number($item['number']);
    }
        if ($res) {
//            $data[0]['category'] = $this->get_cat_by_id($res['category_id']);
//            $data[0]['course'] = $this->get_course_by_number($res['number']);
            Ret(array('code' => 1, 'data' => $res[0]));
        }else{
            Ret(array('code' => 2, 'info' => '获取相关信息失败！'));
        }
    }


}