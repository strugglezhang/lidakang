<?php

namespace AppParents\Controller;
class IndexController extends CommonController
{
    /**
     *  我的会员信息
     */
    public function app_member_view_api()
    {

        $id = I('id');
        if ($id < 1) {
            Ret(array('code' => 2, 'info' => '参数（id）有误'));
        }
        $res = D('Login')->get_app_info($id);
        $courseTryoutModel = D('CourseTryout');
        $courseOrderModel = D('CourseOrder');
        $activityReserveModel = D('ActivityReserve');
        if ($res) {
            $card = D('CardBond')->get_card($id);
            $res[0]['card_number'] = $card['card_number'];
            $cour = $courseTryoutModel->get_count_info($id);
            $res[0]['tryout'] = $cour;
            $reserve = $courseOrderModel->get_reserve_info($id);
            $res[0]['reserve'] = $reserve;
            $activity = $activityReserveModel->get_activity_info($id);
            $res[0]['activity'] = $activity;
            Ret(array('code' => 1, 'data' => $res));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据'));
        }
    }

    /**
     *   预约报名
     */
    public function app_member_course_api()
    {
        before_api();
        checkAuth();
        checkLogin();
        $member_id = I('member_id');
        $page = I('page', 1, 'intval');
        $pagesize = I('pagesize', 10, 'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page . ',' . $pagesize;
        $courseTryoutModel = D('CourseReserve');
        $courseModel = D('Course');
        $instModel = D('institution');
        $courseReserveData = $courseTryoutModel->where("member_id=$member_id and member_type=2")->page($page)->select();
        if (empty($courseReserveData)) {
            Ret(array('code' => 2, 'info' => '没有数据'));
        }
        $data = array();
        foreach ($courseReserveData as $key => $value) {
            $data[$key]['courseName'] = $courseModel->where("id={$value['course_id']}")->getField("name");
            $data[$key]['instName'] = $instModel->where("id={$value['institution_id']}")->getField("name");
            $data[$key]['pic'] = $value['pic'];
            $data[$key]['time'] = $value['time'];
            $data[$key]['address'] = $instModel->where("id={$value['institution_id']}")->getField("address");
        }
        $count = count($data);
        Ret(array('code' => 1, 'data' => $data, 'total' => $count, 'page_count' => ceil($count / $pagesize)));
    }

    /**
     * 订购表
     */
    public function app_order_course_api()
    {
//        before_api();
//        checkAuth();
//        checkLogin();
        $member_id = I('member_id');
        $page = I('page', 1, 'intval');
        $pagesize = I('pagesize', 10, 'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page . ',' . $pagesize;
        $courseReserveModel = D('CourseReserve');
        $instModel = D('institution');
        $courseModel = D('Course');
        $roomModel = D("RoomReserve");
        $courseBuyDetail = D("CourseBuyDetail");
        $courseBuyData = $courseBuyDetail->where("buyer_id=$member_id")->page($page)->select();
        if (empty($courseBuyData)) {
            Ret(array('code' => 2, 'info' => '没有数据'));
        }
        $return = array();
        //var_dump($courseBuyData);die;
        foreach ($courseBuyData as $key => $value) {
            $return[$key]['courseName'] = $value['course_name'];
            $courseInfo=$courseModel->where('id='.$value['course_id'])->find();
            //var_dump($courseInfo['institution_id']);die;
            $return[$key]['instName'] = $instModel->where('id='.$courseInfo['institution_id'])->getField("name");
            $return[$key]['pic'] = $courseInfo['pic'];
           // $courseTime = $roomModel->where("id={$value['room_reserve_id']}")->select();
            $return[$key]['courseTime'] =$courseInfo['course_time'];
            $return[$key]['address'] = $instModel->where('id='.$courseInfo['institution_id'])->getField("address");
            //$useNumber = $courseBuyDetail->where(['buyer_id' => $member_id, 'course_id' => $value['course_id']])->select();
            $return[$key]['useNumber'] = $value['used_times'];
            $return[$key]['unUseNumber'] = $value['unused_times'];
            $return[$key]['course_id'] = $value['course_id'];
        }
        $count = count($return);
        Ret(array('code' => 1, 'data' => $return, 'total' => $count, 'page_count' => ceil($count / $pagesize)));
    }

    /**
     * 课程表
     */
    public function app_reserve_api()
    {
        $member_id = I('member_id');
        $time = I('time');
        $page = I('page', 1, 'intval');
        $pagesize = I('pagesize', 10, 'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page . ',' . $pagesize;
        $courseOrderModel = D('CourseOrder');
        $coursePlanModel = D('CoursePlan');
        $roomNumberModel = D('RoomNumber');
        $courseModel = D('Course');
        $instModel = D('institution');
        $count = $courseOrderModel->getCount($member_id, $time);
        $data = $courseOrderModel->getList($member_id, $time, $page, $false = null);
//        var_dump($data);die;
        foreach ($data as $key => $value) {
            $course = $coursePlanModel->get_info($value['course_id']);
            $res[$key]['start_time'] = $course[0]['start_time'];
            $res[$key]['end_time'] = $course[0]['end_time'];
            $cour = $roomNumberModel->get_info($course[0]['room_id']);
            $res[$key]['position'] = $cour[0]['position'];
            $inst = $instModel->get_info($value['institution_id']);
            $res[$key]['inst_name'] = $inst[0]['name'];
            $cour = $courseModel->get_info($value['course_id']);
            $res[$key]['cour_name'] = $cour[0]['name'];
        }
        if ($res) {
            Ret(array('code' => 1, 'data' => $res, 'total' => $count, 'page_count' => ceil($count / $pagesize)));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据'));
        }
    }

    /**
     * 我的活动
     */
    public function app_activity()
    {
        $member_id = I('member_id');
        $page = I('page', 1, 'intval');
        $pagesize = I('pagesize', 10, 'intval');
        $page = $page . ',' . $pagesize;
        $activityReserveModel = D('ActivityReserve');
        $activityModel = D('Activity');
        $instModel = D('institution');
        //$count = $activityReserveModel->get_activity_info($member_id);
        $data = $activityReserveModel->where('member_id='.$member_id)->page($page)->select();
        $activityNum = $activityReserveModel->where('member_id='.$member_id)->count();
        $instModel = D('institution');
        foreach ($data as $key => $value) {
            $activity = $activityModel->where("id=" . $value['activity_id'])->find();
            if (empty($activity)) {
                continue;
            }
            //var_dump($activity);die;
            //foreach ($activity as $v) {
                $data[$key]['activity_img'] = $activity['img'];
                $data[$key]['activity_name'] = $activity['name'];
                $data[$key]['activity_start_time'] = $activity['start_time'];
                $data[$key]['activity_end_time'] = $activity['end_time'];
//                $inst[] = $instModel->get_info($v['institution_id']);
//                $data[$key]['inst_name'] = $inst['name'];
                $data[$key]['instName'] = $instModel->where("id={$value['institution_id']}")->getField("name");
            //}
        }
        if ($data) {
            Ret(array('code' => 1, 'data' => $data, 'total' => $activityNum, 'page_count' => ceil($activityNum / $pagesize)));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据'));
        }
    }

    /**
     * 家长
     */
    public function app_parent()
    {
        $member_id = I('member_id');
        $data = D('Member')->get_app_parent($member_id);
        $json = $data[0]['relations']; //json格式的数组转换成 php的数组
        $res = (Array)json_decode($json);
        if ($res) {
            Ret(array('code' => 1, 'data' => $res));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据'));
        }
    }

    var $project = array(1 => '父亲', 2 => '母亲', 3 => '爷爷', 4 => '奶奶', 5 => '姥姥', 6 => '姥爷', 7 => '其他');

    public function project()
    {
        before_api();
        Ret(array('code' => 1, 'data' => $this->project));
    }

    private function get_project($id)
    {
        $array = explode(',', $id);
        foreach ($array as $k => $v) {
            $items[$k] = $this->project[$v];
        }
        return implode(',', $items);
    }

    /**
     * 个人资料
     */
    public function app_material()
    {
//        before_api();
        checkAuth();
        checkLogin();
        $id = I('id');
        $data = D('Member')->get_app_material($id);
        foreach ($data as $key => $value) {
            $data[$key]['sex'] = get_sex($value['sex']);
        }
        if ($data) {
            Ret(array('code' => 1, 'data' => $data));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据'));
        }
    }

    /**
     *    修改个人资料
     */
    public function app_amend_material()
    {
        $data['id'] = I('id');
        $data['pic'] = I('pic');
        $data['name'] = I('name');
        $data['sex'] = I('sex');
        $data['birthdate'] = I('birthdate');
        $data['health'] = I('health');
        $data['address'] = I('address');
        $data['weight'] = I('weight');
        $data['phone'] = I('phone');
        $data['school'] = I('school');
        if (!$data['pic']) {
            Ret(array('code' => 2, 'info' => '参数（pic）有误'));
        }
        if (!$data['name']) {
            Ret(array('code' => 2, 'info' => '参数（name）有误'));
        }
        if (!$data['birthdate']) {
            Ret(array('code' => 2, 'info' => '参数（birthdate）有误'));
        }
        if (!$data['sex']) {
            Ret(array('code' => 2, 'info' => '参数（sex）有误'));
        }
        if (!$data['health']) {
            Ret(array('code' => 2, 'info' => '参数（health）有误'));
        }
        if (!$data['address']) {
            Ret(array('code' => 2, 'info' => '参数（address）有误'));
        }
        if (!$data['weight']) {
            Ret(array('code' => 2, 'info' => '参数（weight）有误'));
        }
        if (!$data['phone']) {
            Ret(array('code' => 2, 'info' => '参数（phone）有误'));
        }
        if (!$data['school']) {
            Ret(array('code' => 2, 'info' => '参数（school）有误'));
        }
        $memberModel = D('Member');
        $memberModel->update_member($data);

        Ret(array('code' => 1, 'data' => '修改成功'));
    }

    /**
     * 课程表
     *
     */
    public function app_reserve_list()
    {
        before_api();
        checkAuth();
        checkLogin();
        $course_id = I('course_id');
        $member_id = I('member_id');
        $page = I('page', 1, 'intval');
        $pagesize = I('pagesize', 10, 'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page . ',' . $pagesize;
        $courseReserveModel = D('CourseReserve');
        $coursePlanModel = D('CoursePlan');
        $roomNumberModel = D('RoomNumber');
        $instModel = D('institution');
        $courseModel = D('Course');
        $count = $courseReserveModel->get_count($course_id);
        $data = $courseReserveModel->where("course_id = {$course_id} and member_id={$member_id}")->page($page)->select();
        foreach ($data as $key => $value) {
            $course = $coursePlanModel->get_info($value['course_id']);
            $data[$key]['start_time'] = $course[0]['start_time'];
            $data[$key]['courseName'] = $courseModel->where("id={$value['course_id']}")->getField("name");
            $data[$key]['instName'] = $instModel->where("id={$value['institution_id']}")->getField("name");
            $data[$key]['end_time'] = $course[0]['end_time'];
            $cour = $roomNumberModel->get_info($course[0]['room_id']);
            $data[$key]['position'] = $cour[0]['position'];

        }
        if ($data) {
            Ret(array('code' => 1, 'data' => $data, 'total' => $count, 'page_count' => ceil($count / $pagesize)));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据'));
        }
    }

//    public function getCourseNamelist()
//    {
//        $courseId = I("courseId");
//    }

}


