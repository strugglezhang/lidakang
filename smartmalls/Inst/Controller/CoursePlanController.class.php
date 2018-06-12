<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/7/10
 * Time: 22:23
 * 教室计划
 */

namespace Inst\Controller;
class CoursePlanController extends CommonController
{
    /*
     *课程计划列表
     */
    public function index()
    {
        before_api();
        checkLogin();

        checkAuth();

        $state = I('state', 2, 'intval');
        $is_manager = true;
        if ($state !== 2 && !$is_manager) {
            $state = 2;
        }
        $page = I('page', 1, 'intval');
        $pagesize = I('pagesize', 10, 'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page . ',' . $pagesize;
        $institution_id = I('institution_id', 0, 'intval');
        $start_time = I('start_time');
        $end_time = I('end_time');
        $model = D('CoursePlan');
        $reserveModel = D('CourseReserve');
        $count = $model->get_count($institution_id, $start_time, $end_time);
        $res = $model->get_list_by_time($institution_id, $start_time, $end_time, $page);
//        var_dump($res);die;
        $resData = '';
        if ($res) {
            foreach ($res as $key => $value) {
                $type = $this->get_room($value['room_id']);
                $resData[$key]['room'] = $type[0]['position'];
                $host = $this->get_institution_by_id($value['institution_id']);
                $resData[$key]['institution'] = $host[0]['name'];
                $resData[$key]['institution_id'] = $value['institution_id'];
                $resData[$key]['start_time'] = $value['start_time'];
                $resData[$key]['end_time'] = $value['end_time'];
                $course = $this->get_course_by_id($value['course_id']);
                $resData[$key]['id'] = $course[0]['id'];
                $resData[$key]['course'] = $course[0]['name'];
                $resData[$key]['course_id'] = $value['course_id'];
                $resData[$key]['price'] = $value['price'] . "元";
                $resData[$key]['courseReserve_number'] = $reserveModel->get_courseReservceNumber($value['course_id']);
                $resData[$key]['max_member'] = $value['max_member'];
                $resData[$key]['reserveRatio'] = round($resData[$key]['courseReserve_number'] / $resData[$key]['max_member'] * 100, 2) . "％";

            }
            Ret(array('code' => 1, 'data' => $resData, 'total' => $count, 'page_count' => ceil($count / $pagesize)));

        } else {
            Ret(array('code' => 2, 'info' => '没有课程计划信息'));

        }
    }

    /*
 *课程计划列表
 */
    public function getCoursePlanListByInst()
    {
        before_api();
        checkLogin();

        checkAuth();

       /* $state = I('state', 2, 'intval');
        $is_manager = true;
        if ($state !== 2 && !$is_manager) {
            $state = 2;
        }*/

        $instId=session("inst.institution_id");
        $page = I('page', 1, 'intval');
        $pagesize = I('pagesize', 10, 'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page . ',' . $pagesize;
        //$institution_id = session()
        $start_time = I('start_time');
        $end_time = I('end_time');
        $model = D('RoomReserve');
        $reserveModel = D('CourseReserve');
        $count = $model->get_reserve_count($instId, $start_time, $end_time);
        $res = $model->get_list_by_time($instId, $start_time, $end_time, $page);
        $resData = '';
        if ($res) {
            foreach ($res as $key => $value) {
                $type = $this->get_room($value['room_id']);
                $resData[$key]['room'] = $type[0]['position'];
                $host = $this->get_institution_by_id($value['institution_id']);
                $resData[$key]['institution'] = $host[0]['name'];
                $resData[$key]['start_time'] = $value['start_time'];
                $resData[$key]['end_time'] = $value['end_time'];
                $course = $this->get_course_by_id($value['course_id']);
                $resData[$key]['id'] = $course[0]['id'];
                $resData[$key]['course'] = $course[0]['name'];
                $resData[$key]['price'] = $value['price'];
                $resData[$key]['courseReserve_number'] = $reserveModel->get_courseReservceByRoom($value['course_id'],$value['id']);
                $resData[$key]['max_member'] = $value['max_number'];
                $resData[$key]['reserveRatio'] = round($resData[$key]['courseReserve_number'] / $resData[$key]['max_member'] * 100, 2) . "％";
                $resData[$key]['room_reserve_id']=$value['id'];
                $resData[$key]['room_id']=$value['room_id'];
               // $resData[$key]['room_reserve_id']=$value['id'];
            }
            Ret(array('code' => 1, 'data' => $resData, 'total' => $count, 'page_count' => ceil($count / $pagesize)));

        } else {
            Ret(array('code' => 2, 'info' => '没有课程计划信息'));

        }
    }


    /*
     * 根据id查教室
     */
    private function get_room($room_id)
    {
        if ($room_id) {
            return D('Room')->get_room_by_id($room_id);
        }
    }

    /*
     * 根据id查机构
     */
    private function get_institution_by_id($institution_id)
    {
        if ($institution_id) {
            return D('Institution')->get_inst_by_id($institution_id);
        }

    }

    /*
     * 根据id查课程
     */
    private function get_course_by_id($course_id)
    {
        if ($course_id) {
            return D('Course')->get_course_by_id($course_id);
        }
    }

    public function getCoursePlanDetail()
    {
        before_api();
        checkLogin();

        checkAuth();
        $courseID = I('id');
        if (!$courseID) {
            Ret(array('code' => 2, 'info' => '参宿（id）错误'));
        } else {


        }
    }

    /**
     * 课程计划报名详情
     */
    public function reserveCourseView()
    {
        before_api();
        checkLogin();
        checkAuth();
        $course_id = I('id');
       // $room_id=I()
        $room_reserve_id=I('room_reserve_id');
        $reserveModel = D('CourseReserve');
        $memberModel = D('Member');
        $page = I('page', 1, 'intval');
        $pagesize = I('pagesize', 10, 'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page . ',' . $pagesize;
        $count = $reserveModel->get_courseReservceNumber($course_id,$room_reserve_id);

        $plan = D('CoursePlan')->get_Plan($course_id);
        $max_member = $plan[0]['max_member'];
        $courseReserve_number = $count;
        $reserveRatio = round($courseReserve_number / $max_member * 100, 2) . '％';
        $data = $reserveModel->get_list_by_activityId($course_id, $room_reserve_id,$page);
        #var_dump($data);die;
        foreach ($data as $key => $value) {
            $member = $memberModel->get_info($value['member_id']);
            //var_dump($member);die;
            $data[$key]['name'] = $member[0]['name'];
            $data[$key]['card_number'] = $member[0]['card_number'];
            $data[$key]['phone'] = $member[0]['phone'];
            $data[$key]['parent_phone'] = $member[0]['parent_phone'];
            $data[$key]['number'] = $member[0]['number'];
            $data[$key]['pic'] = $member[0]['pic'];
        }
        if ($data) {
            Ret(array('code' => 1, 'data' => $data, 'total' => $count, 'page_count' => ceil($count / $pagesize), 'max_member' => $max_member, 'count' => $courseReserve_number, 'reserveRatio' => $reserveRatio));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据'));
        }
    }

    /**
     *  根据会员ID和课程ID查询课程计划列表
     */
    public  function getCousePlanListByMembIdAndCourseId()
    {
        before_api();
        checkLogin();
        checkAuth();
        $course_id = I('course_id');
        $member_id=I('member_id');
        $reserveModel = D('CourseReserve');
        //$memberModel = D('Member');
        $page = I('page', 1, 'intval');
        $pagesize = I('pagesize', 10, 'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page . ',' . $pagesize;
        $data=$reserveModel->get_list_by_couserIdAndMemberId($member_id,$course_id,$page);
        $count=count($data);
        foreach ($data as $key => $value) {
            $coursePlanInfo =D('CoursePlan')->get_Plan($value['id']);
            $data[$key]['start_time']=$coursePlanInfo[0]['start_time'];
            $data[$key]['end_time']=$coursePlanInfo[0]['end_time'];
            $data[$key]['course_pos']=$coursePlanInfo[0]['room'];
            $data[$key]['courseplan_id']=$value['id'];
            $data[$key]['course_id']=$coursePlanInfo[0]['course_id'];
            $data[$key]['course_name']=$coursePlanInfo[0]['course'];
        }
        if ($data) {
            Ret(array('code' => 1, 'data' => $data));
        } else {
            Ret(array('code' => 2, 'info' => '没有相关课程计划信息'));
        }
    }
    /**
     * 课程计划报名详情
     */
    public function reserveCount()
    {
        $course_id = I('id');
        $reserveModel = D('CourseReserve');
        $model = D('CoursePlan');
        $count = $reserveModel->get_courseReservce($course_id);
        $res = $reserveModel->get_courseReservceID($course_id);
        $data['id'] = $res[0]['course_id'];
        $course = $this->get_course_by_id($data['id']);
        $data['course_name'] = $course[0]['name'];
        $plan = $model->get_Plan($course_id);
        $data['max_member'] = $plan[0]['max_member'];
        $data['courseReserve_number'] = $count;
        $data['reserveRatio'] = round($data['courseReserve_number'] / $data['max_member'] * 100, 2) . "％";
//        var_dump($data);die;
        if ($data) {
            Ret(array('code' => 1, 'data' => $data));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据，参数有误！'));
        }
    }

    /**
     * 课程试听报名
     */
    public function trialCourseRegistration()
    {

        $mallId = session("mall.mall_id");
        if(!empty(I("institution_id")))
        {
            $institutionId = I("institution_id");
        }
        else
        {
            $institutionId =session("inst.institution_id");
        }
        $courseId = I("course_id");
        $cardNo = I("card_number");
        $position = I("position");
        $startTime = I("start_time");
        $endTime = I("end_time");

        if (empty($mallId)) {
            Ret(array('code' => 2, 'info' => '请换号登录！'));
            die;
        }

        if (empty($institutionId) || empty($courseId) || empty($cardNo) || empty($position) || empty($startTime) || empty($endTime)) {
            Ret(array('code' => 2, 'info' => '参数错误！'));
            die;
        }
        //查找该会员的信息
        $memberInfo=getCardInfoByNumber($cardNo);

        if (empty($memberInfo)) {
            Ret(array('code' => 2, 'info' => '暂无该会员！'));
            die;
        }
        $memberId = $memberInfo['id'];
        $courseReserveModel = D("CourseReserve");

        $courseReserveList = $courseReserveModel->where("institution_id = $institutionId and 
                             course_id = $courseId and member_id = $memberId")->field("*")->select();
        if (!empty($courseReserveList)) {
            $countNum = count($courseReserveList);
            $courseReserveList = $courseReserveList[$countNum - 1];
            $useNum = $courseReserveList['used_times'];
        } else {
            $useNum = 0;
        }
        $roomReserveModel = D('RoomReserve');
        $roomReserveInfo = $roomReserveModel->getRoomReserveInfo($institutionId, $courseId, $startTime);
        $repeatCourseReserveInfo = $courseReserveModel->hasRepeatCourseReserve($memberId, $courseId, $roomReserveInfo['id']);
        if ($repeatCourseReserveInfo == true) {
            Ret(array('code' => 2, 'info' => '已经报名该课程！'));
            die;
        }
        $courseInfo=D('Course')->where('id='.$courseId)->find();
        $courseReserveData = array(
            "mall_id" => $mallId,
            "institution_id" => $institutionId,
            "course_id" => $courseId,
            "member_id" => $memberId,
            "time" => $courseInfo['course_time'],
            "used_times" => $useNum + 1,
            "card_id" => $cardNo,
            "member_type" => 2,  //试听人员状态为2
            "room_reserve_id" => $roomReserveInfo['id']
        );

        $courseReserveModel->data($courseReserveData)->add();
        $roomModel = D("Room");
        $roomList = $roomModel->where("position = '".$position."'")->field("*")->find();
        $equip = $roomList['equip_ip'];

        $memberDoorAuthModel = D("MemberDoorAuth");
        $memberDoorAuthInsertData = array(
            "eqIp" => $equip,
            "cardNo" => $cardNo,
            "memberId" => $memberInfo['id'],
            "memberName" => $memberInfo['name'],
            "time" => $this->formatTime($startTime,$endTime)
        );
        $memberDoorAuthModel->data($memberDoorAuthInsertData)->add();
        Ret(array('code' => 1, 'info' => '报名成功！', 'data' => []));


    }

    //课程报名
    public function courseRegistration()
    {
        $mallId = session("mall.mall_id");
        if(!empty(I("institution_id")))
        {
            $institutionId = I("institution_id");
        }
        else
        {
            $institutionId =session("inst.institution_id");
        }
        //var_dump($institutionId);die;
        $courseId = I("course_id");
        $cardNo = I("card_number");
        $position = I("position");
        //$roomId=I('room_id');
        $startTime = I("start_time");
        $endTime = I("end_time");
        $roomReserveId=I("room_reserve_id");
        //var_dump($institutionId);die;
        $roomReserveModel = D('RoomReserve');
        $roomReserveInfo = $roomReserveModel->getRoomReserveInfoById($roomReserveId);
        /*if (empty($mallId)) {
            Ret(array('code' => 2, 'info' => '请换号登录！'));
            die;
        }*/

        if (empty($institutionId) || empty($courseId) || empty($cardNo) || empty($position) || empty($startTime) || empty($endTime)) {
            Ret(array('code' => 2, 'info' => '参数错误！'));
            die;
        }
        $nowTime=date("Y-m-d H:i:s");
        if($nowTime<$endTime) {
            //查找该会员的信息
            //$memberModel = D("Member");
            //$memberInfo = $memberModel->where("card_number = $cardNo")->find();
            $memberInfo = getCardInfoByNumber($cardNo);
            if (empty($memberInfo)) {
                Ret(array('code' => 2, 'info' => '暂无该会员！'));
                die;
            }
            $memberId = $memberInfo['id'];
            //var_dump($memberId);die;
            $courseReserveModel = D("CourseReserve");

            $courseReserveList = $courseReserveModel->where("institution_id = $institutionId and 
                             course_id = $courseId and member_id = $memberId ")->field("*")->select();
            //echo $courseReserveModel->getLastSql();
            if (!empty($courseReserveList)) {
                $countNum = count($courseReserveList);
                $courseReserveList = $courseReserveList[$countNum - 1];
                $useNum = $courseReserveList['used_times'];
                // echo('11111');
            } else {
                // echo ('22222');
                $useNum = 0;
            }
            //var_dump($useNum);die;
            //判断使用卡的使用次数
            /*$institutionsMallRevenueModel = D('InstitutionsMallRevenue');
            $institutionsMallRevenueList = $institutionsMallRevenueModel->where("institutions_id= $institutionId and 
                                        course_id = $courseId and income_ownerid = $memberId")->field("*")->select();
            //echo $institutionsMallRevenueModel->_sql();
            //var_dump($institutionsMallRevenueList);die;
            if (empty($institutionsMallRevenueList)) {
                Ret(array('code' => 2, 'info' => '暂无购卡！'));
                die;
            }*/
            //$institutionsMallRevenueList = $institutionsMallRevenueList[count($institutionsMallRevenueList) - 1];
            $courseInfo = D('Course')->where('id=' . $courseId)->find();
            $con['buyer_id'] = $memberId;
            $con['course_id'] = $courseId;
            $courseBuyInfo = D('CourseBuyDetail')->where($con)->select();
            //已上课程总数
            $usedCourseSum = 0;
            //购买课程总数
            $courseSum = 0;
            //计算课程卡的总数
            //var_dump($courseBuyInfo);die;
            foreach ($courseBuyInfo as $key => $value) {
               // echo('11111');
                $usedCourseSum = $usedCourseSum + $value['used_times'];
                $courseSum = $courseSum + $value['validate_times'];
            }
            //var_dump($usedCourseSum);
            //var_dump($courseSum);
            //die;
            if($usedCourseSum>=$courseSum)
            {
                Ret(array('code' => 2, 'info' => '卡的次数已经用完了！或未购卡'));
                die;
            }
            $repeatCourseReserveInfo = $courseReserveModel->hasRepeatCourseReserve($memberId, $courseId, $roomReserveInfo['id']);
            if ($repeatCourseReserveInfo == true) {
                Ret(array('code' => 2, 'info' => '已经报名该课程！'));
                die;
            }
            //$courseInfo=D('Course')->where('id='.$courseId)->find();


            //echo D('CourseBuyDetail')->getLastSql();
            //var_dump($courseBuyInfo);die;


            if ($usedCourseSum < $courseSum)
            {
                //课程预订上
                $courseReserveData = array(
                    "mall_id" => $mallId,
                    "institution_id" => $institutionId,
                    "course_id" => $courseId,
                    "member_id" => $memberId,
                    "time" => $courseInfo['course_time'],
                    "used_times" => $useNum + 1,
                    "card_id" => $cardNo,
                    "room_reserve_id" => $roomReserveId,
                    "member_type" => 1, //花钱报名的状态为1
                    "room_id" => $roomReserveInfo['room_id'],
                    "room_number" => $roomReserveInfo['room_number']
                );
                $courseReserveModel->data($courseReserveData)->add();

                //进门权限加上
                $roomModel = D("Room");
                $roomList = $roomModel->where("position like '" . $position . "'")->field("*")->find();
                //$equip = $roomList['equip_ip'];
                $shopInfo = D('Shop')->where('id=' . $roomList['shop_id'])->find();
                $equip = $shopInfo['equipment_ip'];
                //echo $roomModel->getLastSql();
                //var_dump($roomModel->getLastSql());die;
                $memberDoorAuthModel = D("MemberDoorAuth");
                $memberDoorAuthInsertData = array(
                    "eqIp" => $equip,
                    "cardNo" => $cardNo,
                    "memberId" => $memberInfo['id'],
                    "memberName" => $memberInfo['name'],
                    "time" => $this->formatTime($startTime, $endTime)
                );
                //var_dump($memberDoorAuthInsertData);die;
                $memberDoorAuthModel->add($memberDoorAuthInsertData);
                Ret(array('code' => 1, 'info' => '报名成功！', 'data' => []));

            } else
            {
                Ret(array('code' => 2, 'info' => '卡的次数不足！'));
                die;
            }
        }
        else
        {
            Ret(array('code' => 2, 'info' => '报名时间已过！'));
            die;
        }
    }

    private function formatTime($startTime, $endTime)
    {
        //0,0:00-23:59;1,0:00-23:59;2,0:00-23:59;3,0:00-23:59;4,0:00-23:59;5,0:00-23:59;6,0:00-23:59
        $str = "";
        $sTime = explode(" ", $startTime);
        $week = date("w",strtotime($sTime[0]));

        $eTime = explode(" ", $endTime);
        $str .=$sTime[0].",".$eTime[0]."#".rand(1,1000)."]";
        $strTime = "";

        for ($i = 0 ;$i<=6; $i++){
            $sHour = explode(":",$sTime[1]);
            $eHour = explode(":", $eTime[1]);
            if($sHour[0] == "00：00"){
                $sTime1 = "0:00";
            } else {
                $sTime1 = $sHour[0].":".$sHour[1];
            }

            if($i == $week){
                $strTime .= $i.",".$sTime1."-".$eHour[0].":".$eHour[1].";";
                continue;
            }
            $strTime .= $i.",23:59-23:59;";
        }
        return $str.substr($strTime,0,strlen($strTime)-1);











//        $m = array(
//            0 => 7,
//            1 => 1,
//            2 => 2,
//            3 => 3,
//            4 => 4,
//            5 => 5,
//            6 => 6
//        );

//        $weekEndTime = 0;
//        foreach ($m as $k => $v) {
//            if ($k == $weekTime['week_end']) {
//                $weekEndTime = $v;
//            }
//        }
//        $t = "";
//        if ($weekEndTime != 0) {
//            for ($i = 0;  $i <$weekEndTime;$i++) {
//                if($weekTime['time'][0]['time_start'] == "00:00"){
//                    $weekTime['time'][0]['time_start'] = "0:00";
//                }
//                $t .= $i . "," . $weekTime['time'][0]['time_start']."-".$weekTime['time'][0]['time_end'] . ";";
//            }
//        }
//
//        $mallStaffModel = D("Worker");
//        $res = $mallStaffModel->field("*")->where("card_number=$carNumber")->select();
//        if (!empty($res)) {
//            $startTime = $res[0]['join_time'];
//            $endTime = $res[0]['contract_time'];
//            $returnTime = date("Ymd",strtotime($startTime)) . "," . date("Ymd",strtotime($endTime)) . "#" . rand(1, 1000) . "]" . $t;
//            return rtrim($returnTime,";");
//        }
    }
}