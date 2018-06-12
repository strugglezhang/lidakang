<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/6/16
 * Time: 14:30
 */

namespace Inst\Controller;
class CourseController extends CommonController
{

    /*
     * 课程信息列表
     */
    public function course_api()
    {
//        before_api();

        checkLogin();

        checkAuth();
        //$instId='';
        //var_dump($instId);die;
       /* $state = I('state', 2, 'intval');
        $is_manager = true;
        if ($state !== 2 && !$is_manager) {
            $state = 2;
        }*/
        $course_catid = I('course_catid', 0, 'intval');
        $keyword = I('keyword');
        $page = I('page', 1, 'intval');
        $pagesize = I('pagesize', 10, 'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page . ',' . $pagesize;
        $courseModel = D('Course');
        $catModel = D('Category');
        $subCatModel = D('Classify');
        $InstModel = D('Institution');
        $count = $courseModel->get_count($course_catid, $keyword);
        //var_dump($count);die;
        $data = $courseModel->get_list( $course_catid, $keyword, $page, $fields = null);
        foreach ($data as $key => $item) {
            $catname = $catModel->get_cat($item['course_catid']);
            $data[$key]['course_catname'] = $catname[0]['name'];
            $course = $subCatModel->get_sub_cat($item['course_sub_catid']);
            $data[$key]['course_sub_catname'] = $course[0]['name'];
            $institution = $InstModel->get_inst_by_id($item['institution_id']);
            $data[$key]['institution_name'] = $institution[0]['name'];
        }
        if ($data) {
            Ret(array('code' => 1, 'data' => $data, 'total' => $count, 'page_count' => ceil($count / $pagesize)));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据！'));
        }

    }

    public function getCourseInfoByInst()
    {
        checkLogin();

        checkAuth();
        //$instId='';
        //var_dump($instId);die;
        /* $state = I('state', 2, 'intval');
         $is_manager = true;
         if ($state !== 2 && !$is_manager) {
             $state = 2;
         }*/
        $instId=session("inst.institution_id");
        $course_catid = I('course_catid', 0, 'intval');
        $keyword = I('keyword');
        $page = I('page', 1, 'intval');
        $pagesize = I('pagesize', 10, 'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page . ',' . $pagesize;
        $courseModel = D('Course');
        $catModel = D('Category');
        $subCatModel = D('Classify');
        $InstModel = D('Institution');
        $count = $courseModel->get_count_by_inst($course_catid, $keyword,$instId);
        //var_dump($count);die;
        $data = $courseModel->get_list_by_inst( $course_catid, $keyword, $page, $fields = null,$instId);
        foreach ($data as $key => $item) {
            $catname = $catModel->get_cat($item['course_catid']);
            $data[$key]['course_catname'] = $catname[0]['name'];
            $course = $subCatModel->get_sub_cat($item['course_sub_catid']);
            $data[$key]['course_sub_catname'] = $course[0]['name'];
            $institution = $InstModel->get_inst_by_id($item['institution_id']);
            $data[$key]['institution_name'] = $institution[0]['name'];
        }
        if ($data) {
            Ret(array('code' => 1, 'data' => $data, 'total' => $count, 'page_count' => ceil($count / $pagesize)));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据！'));
        }

    }
    /*
     * 课程详情
     */
    public function course_view_api()
    {
        before_api();

        checkLogin();

        checkAuth();
        $id = I('id');
        if ($id < 1) {
            Ret(array('code' => 2, 'info' => '参数（id）有误！'));
        }
        $res = D('Course')->get_course_infos($id);
        $catModel = D('Category');
        $subCatModel = D('Classify');
        $InstModel = D('Institution');
        $courseCardModel = D('CourseCard');
        if ($res) {
            $catname = $catModel->get_cat($res[0]['course_catid']);
            $res[0]['course_catname'] = $catname[0]['name'];
            $course = $subCatModel->get_sub_cat($res[0]['course_sub_catid']);
            $res[0]['course_sub_catname'] = $course[0]['name'];
            $institution = $InstModel->get_inst_by_id($res[0]['institution_id']);
            $res[0]['institution_name'] = $institution[0]['name'];
            $res[0]['credit_img'] = explode(',', $res[0]['credit_img']);
            $res[0]['card_bag'] = $courseCardModel->getCard($res[0]['id']);
            foreach ($res[0]['card_bag'] as $key => $value) {
                $res[0]['card_bag'] [$key]['price_typeid'] = get_typeid($value['price_typeid']);
            }
            Ret(array('code' => 1, 'data' => $res[0]));
        } else {
            Ret(array('code' => 2, 'info' => '获取相关信息失败！'));
        }

    }


    /*
     * 课程管理(增删改）
     */
    public function course_dml_api()
    {
        before_api();
        checkLogin();
        checkAuth();
        $courseModel = D('Course');
        $flag = I('flag');
        switch ($flag) {
            case "add":
                $data['institution_id'] = I('institution_id');
                $data['pic'] = I('pic', '');
                $data['course_catid'] = I('course_catid', 0, 'intval');
                if ($data['course_catid'] == 0) {
                    Ret(array('code' => 2, 'info' => '请选择课程类别！'));
                }
                $data['course_sub_catid'] = I('course_sub_catid', 0, 'intval');
                if ($data['course_sub_catid'] == 0) {
                    Ret(array('code' => 2, 'info' => '请选择课程子类！'));
                }
                $data['name'] = I('name', '');
                if ($data['name'] == null) {
                    Ret(array('code' => 2, 'info' => '请输入课程名称！'));
                }
                $data['course_time'] = I('course_time', 0);
                if ($data['course_time'] == 0) {
                    Ret(array('code' => 2, 'info' => '请输入课程时长！'));
                }
                $data['card_bag'] = $_POST['card_bag'];
//                var_dump($data['card_bag']);die;
                $data['content'] = I('content', '');
                $data['credit_img'] = I('credit_img');
                $data['state'] = 0;
                $data['submitter'] = session('worker_name');
                $data['submitter_id'] = session('worker_id');
				$data['check_type'] = 0;
				if(empty($data['card_bag'] )){
                    Ret(array('code' => 2, 'info' => '添加失败,课程包信息不能为空'));
				}
                $result = $courseModel->add_course($data);
                if ($result && !empty($data['card_bag'])) {
                    $model = D("Course");
                    $data['id'] = $model->getLastInserId();
//                    echo $data['id'];die;
                    $courseCardModel = D('CourseCard');
                    foreach ($data['card_bag'] as $key => $value) {
                        $priceType='日卡';
                        if($value['price_typeid']==2)
                        {
                            $priceType='月卡';
                        }
                        if($value['price_typeid']==3)
                        {
                            $priceType='季度卡';
                        }
                        if($value['price_typeid']==4)
                        {
                            $priceType='年卡';
                        }
                        $data['courseCard'][$key] = array(
                            'name' => $value['name'],
                            'course_id' => $data['id'],
                            'price_typeid' => $value['price_typeid'],
                            'price_type' => $priceType,
                            'validity' => $value['validity'],
                            'validity_ttimes' => $value['validity_ttimes'],
                            'course_price' => $value['course_price'],
                            'quantity' => $value['quantity'],
                            'gifts' => $value['gifts'],
                            'all_count' => $value['all_count'],
                            'course_name'=> $data['name'],
                            'course_time'=>$data['course_time']
                        );

//                        var_dump($data);die;
                        $courseCardModel->add($data['courseCard'][$key]);
                    }
                    if ($result) {
                        Ret(array('code' => 1, 'info' => '添加成功！'));
                    } else {
                        Ret(array('code' => 2, 'info' => '添加失败,系统出错！'));
                    }
                } else {
                    Ret(array('code' => 2, 'info' => '添加失败,系统出错！'));
                }


                break;
            case "update":
                $data['institution_id'] = I('institution_id');
                $data['id'] = I('id');
                if ($data['id'] < 1) {
                    Ret(array('code' => 2, 'info' => 'ID获取失败！'));
                }
                $data['pic'] = I('pic', '');
//                if($data['pic']==null){
//                    Ret(array('code' => 2, 'info' => '请上传课程图片！'));
//                }
                $data['course_catid'] = I('course_catid', 0, 'intval');
                if ($data['course_catid'] == 0) {
                    Ret(array('code' => 2, 'info' => '请选择课程类别！'));
                }
                $data['course_sub_catid'] = I('course_sub_catid', 0, 'intval');
                if ($data['course_sub_catid'] == 0) {
                    Ret(array('code' => 2, 'info' => '请选择课程子类！'));
                }
                $data['name'] = I('name', '');
                if ($data['name'] == null) {
                    Ret(array('code' => 2, 'info' => '请输入课程名称！'));
                }
                $data['course_time'] = I('course_time', 0);
                if ($data['course_time'] == 0) {
                    Ret(array('code' => 2, 'info' => '请输入课程时长！'));
                }
                $data['content'] = I('content', '');
                $data['credit_img'] = I('credit_img');
                $data['state'] = 4;
                $data['submitter'] = session('worker_name');
                $data['submitter_id'] = session('worker_id');
                $data['check_type'] = 4;
                $data['card_bag'] = $_POST['card_bag'];
	   
				if(empty($data['card_bag'] )){
                    Ret(array('code' => 2, 'info' => '添加失败,课程包信息不能为空'));
				}
				$result = $courseModel->update_course($data);
                //var_dump($data);die;
                $courseCardModel = D('CourseCard');
                $courseCardModel->where('course_id='.$data['id'])->delete();
				if (!empty($data['card_bag'])) {

                    foreach ($data['card_bag'] as $key => $value) {
                        $data['courseCard'][$key] = array(
                            'course_id' => $data['id'],
                            'name' => $value['name'],
                            'price_typeid' => $value['price_typeid'],
                            'price_type' => $value['price_type'],
                            'validity' => $value['validity'],
                            'validity_ttimes' => $value['validity_ttimes'],
                            'course_price' => $value['course_price'],
                            'quantity' => $value['quantity'],
                            'gifts' => $value['gifts'],
                            'all_count' => $value['all_count'],
                            'course_name'=> $data['name'],
                            'course_time'=>$data['course_time']
                        );
                        $courseCardModel->add($data['courseCard'][$key]);
                    }
                    Ret(array('code' => 1, 'info' => '修改成功！'));
                } else {
                    Ret(array('code' => 2, 'info' => '修改失败,请添加课程卡信息！'));
                }
                break;
            case "delete":
                $id = I('id');
                if ($id < 1) {
                    Ret(array('code' => 2, 'info' => '数据获取失败！'));
                }
                $result = $courseModel->del_course($id);
                if ($result) {
                    $courseCardModel = D('CourseCard');
                    $res = $courseCardModel->getCard($id);

                    foreach ($res as $key => $value) {
                        $id = $value['id'];
                        $courseCardModel->delete($id);
                    }
                    if ($result) {
                        Ret(array('code' => 1, 'info' => '删除成功！'));
                    } else {
                        Ret(array('code' => 2, 'info' => '删除失败,系统出错！'));
                    }
                } else {
                    Ret(array('code' => 2, 'info' => '删除失败,系统出错！'));
                }

                break;
            default :
                Ret(array('code' => 2, 'info' => 'Unknowed flag!'));
                break;
        }
    }

    public function deltab()
    {
        $id = I('course_id');
        if ($id < 1) {
            Ret(array('code' => 2, 'info' => '参数（id）有误！'));
        }
        $courseCardModel = D('CourseCard');
        $courseCardModel->where("id='{$id}'")->delete();
        Ret(array('code' => 1, 'info' => '成功！'));

    }
    //获取课程 编号
    public function setSessionInstitutionId()
    {
        $institutionId = I("institution_id");
        if (empty($institutionId)) {
            Ret(array('code' => 2, 'info' => 'institution_id不能为空'));
            die;
        }
        session("inst.institution_id", $institutionId);

        if (empty($institutionId)) {
            Ret(array('code' => 2, 'info' => '请重新登录'));
            die;
        }
        $list = D('Course')->get_course_number($institutionId);
        if (!$list) {
            Ret(array('code' => 2, 'info' => '没有符合该教室的课程！'));
        }
        Ret(array('code' => 1, 'data' => $list));
    }
//    public function get_course_number(){
//        before_api();
//        var_dump(session());die;
//        $institutionId = session("inst.institution_id");
//        if(empty($institutionId)){
//            Ret(array('code' => 2, 'info'=> '请重新登录'));
//        }
//
//        $list=D('Course')->get_course_number($institutionId);
//        if(!$list){
//            Ret(array('code'=>2,'info'=>'数据获取失败！'));
//        }
//        Ret(array('code'=>1,'data'=>$list));
//    }
    //获取课程 编号
    public function getCourseList()
    {
        before_api();
        $instID = session('inst.institution_id');
        if (!$instID) {
            Ret(array('code' => 1, 'info' => ''));
        }
        $list = D('Course')->where('institution_id=' . $instID)->field('id,name')->select();
        if (!$list) {
            Ret(array('code' => 2, 'info' => '数据获取失败！'));
        }
        Ret(array('code' => 1, 'data' => $list));
    }

    //获取类别
    public function cat()
    {
        before_api();
        $cat = D('Category')->get_cats();
        if (!$cat) {
            Ret(array('code' => 2, 'info' => '数据获取失败！'));
        }
        Ret(array('code' => 1, 'data' => $cat));
    }

    public function sub_cat()
    {
        before_api();

        $category_id = I('course_catid', 0);
        $cat = D('Classify')->get_sub_cats($category_id);
        if (!$cat) {
            Ret(array('code' => 2, 'info' => '暂无子类数据！'));
        }
        Ret(array('code' => 1, 'data' => $cat));
    }



    /*
     * APP接口
     */
    /**
     * 课程详情
     */
    public function app_course_view_api()
    {
        before_api();
        checkAuth();
        checkLogin();
        $id = I('id');
        if ($id < 1) {
            Ret(array('code' => 2, 'info' => '参数（id）有误!'));
        }
        $res = D('Course')->get_course_infos($id);
        $courseCatModel = D('CourseCat');
        $courseSubCatModel = D('CourseSubCat');
        $instModel = D('Institution');
        $instStaffModel = D('InstStaff');
        if ($res) {
            foreach ($res as $key => $value) {
                $course = $courseSubCatModel->get_sub_cat($value['id']);
                $res[$key]['catname'] = $course[0]['id'];
                $court = $courseCatModel->get_cat($value['id']);
                $res[$key]['subname'] = $court[0]['id'];
                $institution = $instModel->get_inst_by_id($value['institution_id']);
                $res[$key]['institution_name'] = $institution[0]['name'];
                $insti = $instStaffModel->get_course_list($id);
                $res[$key]['teacher'] = $insti;
            }
            Ret(array('code' => 1, 'data' => $res));
        } else {
            Ret(array('code' => 2, 'info' => '获取相关数据失败'));
        }
    }

    /*
     *
     */
    public function enroll_member_api()
    {
        before_api();

        checkLogin();

        checkAuth();
        $institution_id = session('inst.institution_id');
        $coursePlanModel = D('CoursePlan');
        $course = $coursePlanModel->get_course($institution_id);
        $courseReserveModel = D('DateCourse');
        $memberModel = D('Member');
        foreach ($course as $key => $item) {
            $member = $courseReserveModel->getMemberByMember($item['course_id']);
            foreach ($member as $k => $i) {
                $data[$key][$k] = $memberModel->getMemberInfo($i['member_id']);
            }
        }
        if ($data) {
            Ret(array('code' => 1, 'data' => $data));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据！'));
        }
    }

    /**
     * 课程首页
     */
    public function app_course_api()
    {
//        before_api();
        checkLogin();
        checkAuth();
        $state = I('state', 0, 'intval');
        $is_manager = true;
        if ($state != 2 && !$is_manager) {
            $state = 2;
        }
        $course_catid = I('course_id', 0, 'intval');
        $keyword = I('keyword');
        $page = I('page', 1, 'intval');
        $pagesize = I('pagesize', 10, 'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page . ',' . $pagesize;
        $courseModel = D('Course');
        $instModel = D('Institution');
        $count = $courseModel->get_count($state, $keyword, $course_catid);
        $data = $courseModel->get_by_list($state, $keyword, $page, $course_catid, $fields = null);
        foreach ($data as $key => $item) {
            $institution = $instModel->get_inst_by_id($item['id']);
            $data[$key]['institution_name'] = $institution[0]['name'];
        }
        if ($data) {
            Ret(array('code' => 1, 'data' => $data, 'total' => $count, 'page_count' => ceil($count / $pagesize)));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据'));
        }
    }

    /**
     * 订购课程
     */

    public function app_course_mod_api()
    {
        before_api();
        checkLogin();
        checkAuth();
        $id = I('id');
        if ($id < 1) {
            Ret(array('code' => 2, 'info' => '参数（worker_id）有误'));
        }
        $res = D('course')->get_infos($id);
        $instModel = D('Institution');
        if ($res) {
            $institution = $instModel->get_inst_by_id($res[0]['institution_id']);
            $res[0]['institution_name'] = $institution[0]['name'];
            Ret(array('code' => 1, 'data' => $res));
        }
        Ret(array('code' => 2, 'info' => '请求失败，没有数据！'));
    }

    /**
     * 报名试听
     */
    public function app_course_tryout_api()
    {
//        before_api();
        checkLogin();
        checkAuth();
        $courseTryoutModel = D('CourseTryout');
        $data['member_id'] = I('member_id');
        if ($data['member_id'] = null) {
            return false;
        }
        $data['course_id'] = I('course_id');
        $data['institution_id'] = I('institution_id');
        $data['time'] = date('Y-m-d H:i:s');
        $result = $courseTryoutModel->add_course_tryout($data);
        if ($result) {
            Ret(array('code' => 1, 'info' => '您的报名信息已发送给相关机构，稍后我们会跟您联系!'));
        } else {
            Ret(array('code' => 2, 'info' => '报名失败!'));
        }
    }

    public function course_log()
    {
        before_api();
        $keyword = I('keyword');
        $page = I('page', 1, 'intval');
        $pagesize = I('pagesize', 10, 'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page . ',' . $pagesize;
        $courseCheckLog = D('CourseCheckLog');
        $count = $courseCheckLog->getCount($keyword);
        $data = $courseCheckLog->getList($keyword, $page);
        if ($data) {
            Ret(array('code' => 1, 'data' => $data, 'total' => $count, 'page_count' => ceil($count / $pagesize)));
        } else {
            Ret(array('code' => 2, 'data' => '数据获取失败'));
        }

    }

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
//        if (!$data['course_name']) {
//            Ret(array('code' => 2, 'info' => '参数（course_name）有误!'));
//        }
//        $logdata['course_category']=I('course_category');
//        if (!$data['course_category']) {
//            Ret(array('code' => 2, 'info' => '参数（course_category）有误!'));
//        }
//
//
//        $logdata['check_type']=I('state');
//        if ($data['check_type']==null) {
//            Ret(array('code' => 2, 'info' => '参数（state）有误!'));
//        }
//        if($logdata['check_type']==0){
//            $logdata['check_type']='课程信息新增';
//        }
//        if($logdata['check_type']==4){
//            $logdata['check_type']='课程信息修改';
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
//        $roomCheckoutModel = D('Course');
//        $res= $roomCheckoutModel->save($data);
//        if($res){
//            Ret(array('code' => 1, 'info' => '保存成功'));
//        }else{
//            Ret(array('code' => 2, 'info' => '保存失败'));
//        }
//    }

    public function course_check_api()
    {
        before_api();
        checkAuth();
        checkLogin();
        $data['id'] = I('id');
        $data['state'] = I('state');
        if (intval($data['id']) < 1 || intval($data['state']) < 0) {
            Ret(array('code' => 2, 'info' => '参数有误!'));
        }
        //step 2: 根据主键id 获取需要的字段信息
        $roomCheckoutModel = D('Course');
        //step 3 ；修改审核表状态
        $editInfo = $roomCheckoutModel->updateState($data);
        if ($editInfo) {
            $info = $roomCheckoutModel->getAllField($data['id']);
            //step:4 保存日志
            $log = array(
                'check_time' => date('Y-m-d H:i:s'),
                'course_name' => $info[0]['name'],
                'course_category 	' => $info[0]['course_catid'],
                'check_type' => $info[0]['check_type'],
                'submitter' => $info[0]['submitter'],
                'checker' => session('worker_name'),
                'check_state' => $info[0]['state'],
                'cours_id' => $info[0]['id']
            );
            if ($log['check_type'] == 0) {
                $log['check_type'] = '课程信息新增';
            }
            if ($log['check_type'] == 4) {
                $log['check_type'] = '课程信息修改';
            }
            $logModel = D('CourseCheckLog');
            if ($logModel->insertLog($log)) {
                Ret(array('code' => 1, 'data' => '审核成功'));
            }
        } else {
            Ret(array('code' => 2, 'info' => '审核失败'));
        }


//        $data['id'] = I('id');
//        if ($data['id'] < 1) {
//            Ret(array('code' => 2, 'info' => '参数（id）有误!'));
//        }
////        if ($data['worker_id'] < 1) {
////            Ret(array('code' => 2, 'info' => '参数（worker_id）有误!'));
////        }
//        $logdata['course_name'] = I('name');
//        if (!$logdata['course_name']) {
//            Ret(array('code' => 2, 'info' => '参数（course_name）有误!'));
//        }
//        $logdata['course_category'] = I('course_category');
//        if (!$logdata['course_category']) {
//            Ret(array('code' => 2, 'info' => '参数（course_category）有误!'));
//        }
//
//        $logdata['check_type'] = I('check_type');
//        if ($logdata['check_type'] == null ) {
//            Ret(array('code' => 2, 'info' => '参数（check_type）有误!'));
//        }
//        if($logdata['check_type']==0){
//            $logdata['check_type']='课程信息新增';
//        }
//        if($logdata['check_type']==4){
//            $logdata['check_type']='课程信息修改';
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
//        $logdata['check_time'] = date('Y-m-d H:i:s');
//        $logdata['checker'] = session('worker_name');
//        $roomCheckoutModel = D('Course');
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
     * 课程
     */
    public function course_list()
    {
        before_api();

        checkLogin();

        checkAuth();

        $condition['state'] = 1;
        //$condition['institution_id']=session('inst.institution_id');
        $data = D('Course')->where($condition)->field('id,name')->select();


        if ($data) {
            Ret(array('code' => 1, 'data' => $data));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据！'));
        }
    }

    /**
     * 部门
     */
    public function dept_list()
    {
        before_api();
        checkLogin();
        checkAuth();
        $condition['state'] = 1;
        $condition['institution_id'] = session('inst.institution_id');
        $data = D('InstDept')->where($condition)->field('id,name')->select();
        if ($data) {
            Ret(array('code' => 1, 'data' => $data));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据！'));
        }
    }

    /*
     * 职位
     */
    public function position_list()
    {
        before_api();
        checkLogin();
        checkAuth();
        $condition['state'] = 1;
        $condition['institution_id'] = session('inst.institution_id');
        $data = D('InstPosition')->where($condition)->field('id,name')->select();
        if ($data) {
            Ret(array('code' => 1, 'data' => $data));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据！'));
        }
    }


    public function courseDml()
    {
        before_api();
        checkLogin();
        checkAuth();
        $institution_id = session('inst.institution_id');
        $courseModel = D('Course');
        $flag = I('flag');
        switch ($flag) {
            case add:
                $data['institution_id'] = $institution_id;
                $data['pic'] = I('pic', '');
//                if($data['pic']==null){
//                    Ret(array('code' => 2, 'info' => '请上传课程图片！'));
//                }
                $data['course_catid'] = I('course_catid', 0, 'intval');
                if ($data['course_catid'] == 0) {
                    Ret(array('code' => 2, 'info' => '请选择课程类别！'));
                }
                $data['course_sub_catid'] = I('course_sub_catid', 0, 'intval');
                if ($data['course_sub_catid'] == 0) {
                    Ret(array('code' => 2, 'info' => '请选择课程子类！'));
                }
                $data['name'] = I('name', '');
                if ($data['name'] == null) {
                    Ret(array('code' => 2, 'info' => '请输入课程名称！'));
                }
                $data['course_time'] = I('course_time', 0);
                if ($data['course_time'] == 0) {
                    Ret(array('code' => 2, 'info' => '请输入课程时长！'));
                }
                $data['single_time_price'] = I('single_time_price', 0);
                if ($data['single_time_price'] == 0) {
                    Ret(array('code' => 2, 'info' => '请课程单次基准价格！'));
                }
                $data['single_month_price'] = I('single_month_price', 0);
                $data['single_month_times'] = I('single_month_times', 0);
                $data['single_quarter_price'] = I('single_quarter_price', 0);
                $data['single_quarter_times'] = I('single_quarter_times', 0);
                $data['year_price'] = I('year_price', 0);
                $data['year_times'] = I('year_times', 0);
                $data['content'] = I('content', '');
                $data['credit_img'] = json_encode($_POST['credit_img']);
                $data['state'] = 0;
                $data['submitter'] = session('worker_name');
                $data['submitter_id'] = session('worker_id');
                $data['check_type'] = 0;
                $result = $courseModel->add_course($data);
                if ($result) {
                    Ret(array('code' => 1, 'info' => '添加成功！'));
                } else {
                    Ret(array('code' => 2, 'info' => '添加失败,系统出错！'));
                }
                break;
            case update:

                $data['id'] = I('id');
                if ($data['id'] < 1) {
                    Ret(array('code' => 2, 'info' => 'ID获取失败！'));
                }
                $data['pic'] = I('pic', '');
//                if($data['pic']==null){
//                    Ret(array('code' => 2, 'info' => '请上传课程图片！'));
//                }
                $data['course_catid'] = I('course_catid', 0, 'intval');
                if ($data['course_catid'] == 0) {
                    Ret(array('code' => 2, 'info' => '请选择课程类别！'));
                }
                $data['course_sub_catid'] = I('course_sub_catid', 0, 'intval');
                if ($data['course_sub_catid'] == 0) {
                    Ret(array('code' => 2, 'info' => '请选择课程子类！'));
                }
                $data['name'] = I('name', '');
                if ($data['name'] == null) {
                    Ret(array('code' => 2, 'info' => '请输入课程名称！'));
                }
                $data['course_time'] = I('course_time', 0);
                if ($data['course_time'] == 0) {
                    Ret(array('code' => 2, 'info' => '请输入课程时长！'));
                }
                $data['single_time_price'] = I('single_time_price');
                if ($data['single_time_price'] == 0) {
                    Ret(array('code' => 2, 'info' => '请课程单次基准价格！'));
                }
                $data['single_month_price'] = I('single_month_price');
                $data['single_month_times'] = I('single_month_times');
                $data['single_quarter_price'] = I('single_quarter_price');
                $data['single_quarter_times'] = I('single_quarter_times');
                $data['year_price'] = I('year_price');
                $data['year_times'] = I('year_times');
                $data['content'] = I('content', '');
                $data['credit_img'] = json_encode($_POST['credit_img']);
                $data['state'] = 4;
                $data['submitter'] = session('worker_name');
                $data['submitter_id'] = session('worker_id');
                $data['check_type'] = 4;
                $result = $courseModel->update_course($data);
                if ($result) {
                    Ret(array('code' => 1, 'info' => '修改成功！'));
                } else {
                    Ret(array('code' => 2, 'info' => '修改失败,系统出错！'));
                }
                break;
            case delete:
                $id = I('id');
                if ($id < 1) {
                    Ret(array('code' => 2, 'info' => '数据获取失败！'));
                }
                $result = $courseModel->del_course($id);
                if ($result) {
                    Ret(array('code' => 1, 'info' => '删除成功！'));
                } else {
                    Ret(array('code' => 2, 'info' => '删除失败,系统出错！'));
                }

                break;
            default :
                Ret(array('code' => 2, 'info' => 'Unknowed flag!'));
                break;
        }
    }


    public function getCats()
    {
        checkLogin();
        checkAuth();
        $data['name'] = I('name');
        if (!$data['name']) {
            Ret(array('code' => 2, 'info' => '参数（name）错误！'));
        }
        $data = D('Category')->add($data);
        if ($data) {
            Ret(array('code' => 1, 'info' => '添加成功'));
        } else {
            Ret(array('code' => 2, 'info' => '添加失败'));
        }
    }

    //获取分类列表
    public function getCls()
    {
        checkLogin();
        checkAuth();
        $data['name'] = I('name');
        if (!$data['name']) {
            Ret(array('code' => 2, 'info' => '参数（name）错误！'));
        }
        $data['category_id'] = I('course_catid');
        if (!$data['category_id']) {
            Ret(array('code' => 2, 'info' => '参数（category_id）错误！'));
        }
        $data = D('Classify')->add($data);
        if ($data) {
            Ret(array('code' => 1, 'info' => '添加成功'));
        } else {
            Ret(array('code' => 2, 'info' => '添加失败！'));
        }
    }


    /**
     * 课程报名试听
     */
    public function coursrAudition()
    {
        checkLogin();
        checkAuth();
        $instId=session("inst.institution_id");
        $keyword = I('keyword');
        $start_time = I('start_time', 0, 'intval');
        $end_time = I('end_time', 0, 'intval');
        $page = I('page', 1, 'intval');
        $pagesize = I('pagesize', 10, 'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page . ',' . $pagesize;
        $courseModel = D('Course');
        $courseTryoutModel = D('CourseTryout');
        $institutionModel = D('Institution');

//        var_dump($instModel);die;
        $count = $courseTryoutModel->audition_count($start_time, $end_time, $keyword,$instId);
        $data = $courseTryoutModel->get_audition($start_time, $end_time, $keyword, $page,$instId);
        foreach ($data as $key => $value) {
            $res[$key]['start_time'] = $value['start_time'];
            $res[$key]['end_time'] = $value['end_time'];
            $data[$key]['total_time'] = $res[$key]['start_time'] . '-' . $res[$key]['end_time'];
            $data[$key]['state'] = get_audit($value['state']);
            $institution = $institutionModel->get_inst_by_id($instId);
            //var_dump($institution);die;
            $data[$key]['institution_name'] = $institution[0]['name'];
            $course = $courseModel->get_course_number($value['course_id']);
            $data[$key]['course_name'] = $course[0]['name'];
        }
        if ($data) {
            Ret(array('code' => 1, 'data' => $data, 'total' => $count, 'page_count' => ceil($count / $pagesize)));
        } else {
            Ret(array('code' => 2, 'info' => '查无数据'));
        }
    }

    /**
     * 添加计划时间
     */
    public function getTime()
    {
        $condition['institution_id']=session("inst.institution_id");
        $condition['course_id']=I('course_id');

        $data = D('CoursePlan')->field('id,start_time,end_time')->where($condition)->select();
        foreach ($data as $key => $value) {
            $res[$key]['start_time'] = $value['start_time'];
            $res[$key]['end_time'] = $value['end_time'];
            $data[$key]['total_time'] = $res[$key]['start_time'] . '-' . $res[$key]['end_time'];
        }
        if ($data) {
            Ret(array('code' => 1, 'data' => $data));
        } else {
            Ret(array('code' => 2, 'info' => '查无数据'));
        }
    }

    public function getPlnaTime()
    {
        $plan_id = I('plan_id');
        if (!$plan_id) {
            Ret(array('code' => 2, 'info' => '参数有误'));
        }
        $data = D('CoursePlan')->where('id=' . $plan_id)->field('id,start_time,end_time')->select();
        foreach ($data as $key => $value) {
            $res[$key]['start_time'] = $value['start_time'];
            $res[$key]['end_time'] = $value['end_time'];
            $data[$key]['total_time'] = $res[$key]['start_time'] . '-' . $res[$key]['end_time'];
        }
        if ($data) {
            Ret(array('code' => 1, 'data' => $data));
        } else {
            Ret(array('code' => 2, 'info' => '查无数据'));
        }
    }

    /**
     * 取消试听报名
     */
    public function takeout()
    {
        $id = I('id');
        $count = D('CourseTryout')->getTakeou($id);
        $data['start_time'] = $count[0]['start_time'];
        $data['end_time'] = $count[0]['end_time'];
        $data['room_number'] = $count[0]['room_number'];
        $data['id'] = $count[0]['id'];
        $data['start_time'] = null;
        $data['end_time'] = null;
        $data['room_number'] = null;
        $res = D('CourseTryout')->save($data);
        if ($res) {
            Ret(array('code' => 1, 'info' => '取消成功'));
        } else {
            Ret(array('code' => 2, 'info' => '取消失败'));
        }
    }

    public function delet()
    {
        $id = I('id');
        if ($id < 1) {
            Ret(array('code' => 2, 'data' => '参数有吴'));
        }
        $data = D('CourseTryout')->delete($id);
        if ($data) {
            Ret(array('code' => 1, 'info' => '删除成功'));
        } else {
            Ret(array('code' => 2, 'info' => '删除失败'));
        }
    }

    public function addTryout()
    {
        $courseTryoutModel = D('CourseTryout');
        $data['number'] = I('number');
        $tryout = getMemberCard();
        $isin = in_array($data, $tryout);
        $data['course_id'] = I('course_id');
        $data['plan_id'] = I('plan_id');
        $plan = getCoursePlan($data['plan_id']);
        $data['start_time'] = $plan['start_time'];
        $data['end_time'] = $plan['start_time'];
        $data['room_number'] = $plan['room_number'];
        $data['institution_id'] = I('institution_id');
        $data['time'] = date('Y-m-d H:i:s');
        if (empty($data['number'])) {
            Ret(array('code' => 2, 'info' => '请输入卡号'));
        }
        $member = getMember($data['number']);
        $data['member_name'] = $member['name'];
        $data["phone"] = $member["phone"];
        $data['member_id'] = $member['id'];
        if ($isin) {
            $result = $courseTryoutModel->add_course_tryout($data);
        } else {
            Ret(array('code' => 2, 'info' => '卡号不存在'));
        }
        if ($result) {
            Ret(array('code' => 1, 'info' => '添加成功!'));
        } else {
            Ret(array('code' => 2, 'info' => '添加失败!'));
        }

    }


    /**
     *  添加课程计划
     */
    public function reserveDml()
    {

        $courseTryoutModel = D('CourseTryout');
        $data['id'] = I('id');
        $res['plan_id'] = I('plan_id');
        $plan = getCourse($res['plan_id']);
        $data['start_time'] = $plan['start_time'];
        $data['end_time'] = $plan['end_time'];
        $res = $courseTryoutModel->save($data);
        if ($res) {
            $dateInfo = getTryout($data['id']);
            $counInfo['course_id'] = $dateInfo[0]['course_id'];
            $counInfo['institution_id'] = $dateInfo[0]['institution_id'];
            $counInfo['member_id'] = $dateInfo[0]['member_id'];
            $counInfo['start_time'] = $dateInfo[0]['start_time'];
            $counInfo['end_time'] = $dateInfo[0]['end_time'];
            $counInfo['room_number'] = $dateInfo[0]['room_number'];
            $cour = getCourseName($counInfo['course_id']);
            $counInfo['course_name'] = $cour[0]['name'];
            $res = getRoom($counInfo['room_number']);
            $counInfo['use'] = $res[0]['use'];
            $counInfo['price'] = $res[0]['price'];
            $counInfo['room_number'] = $res[0]['position'];
            $counInfo['room_id'] = $res[0]['id'];
            $counInfo['max_member'] = $res[0]['max_number'];
            $reserveModel = D('CoursePlan');
            $result = $reserveModel->add($counInfo);
            if ($result) {
                Ret(array('code' => 1, 'data' => '添加成功'));
            } else {
                Ret(array('code' => 2, 'info' => '添加失败'));
            }
        } else {
            Ret(array('code' => 2, 'info' => '修改失败'));
        }
    }

}
