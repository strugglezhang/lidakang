<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/9/5
 * Time: 15:24
 */
namespace AppParents\Controller;
class CourseController extends CommonController{
    //根据课程ID获取课程介绍
    public function getCourseIntroduction()
    {
        before_api();
        checkAuth();
        checkLogin();
        $courseId=I('courseId');
        $data=D('Course')->field('content')->where('id=',$courseId)->find();
        if($data)
        {
            Ret(array('code' =>1,'data'=>$data));
        }
        else
        {
            Ret(array('code'=>2,'info'=>'无课程介绍信息'));
        }

    }
    //根据课程ID获取课程荣誉图片
    public function getCourseHonourImg()
    {
        before_api();
        checkAuth();
        checkLogin();
        $courseId=I('courseId');
        $data=D('Course')->field('credit_img')->where('id=',$courseId)->find();
        if($data)
        {
            Ret(array('code' =>1,'data'=>$data));
        }
        else
        {
            Ret(array('code'=>2,'info'=>'无课程介绍信息'));
        }
    }
    public function app_course_view_api(){
        before_api();
        checkAuth();
        checkLogin();
        $id = I('id');
        if($id < 1){
            Ret(array('code' =>2,'info'=>'参数（id）有误!'));
        }
        $res = D('Course')->get_course_infos($id);
//        var_dump($res);die;
        $courseCatModel = D('CourseCat');
        $courseSubCatModel = D('CourseSubCat');
        $instModel = D('Institution');
        $instStaffModel=D('InstStaff');
        if($res){
            foreach($res as $key=>$value){
                $course= $courseSubCatModel->get_sub_cat($value['id']);
                $res[$key]['catname'] = $course[0]['id'];
//                var_dump($course);die;

                $institution =$instModel ->get_info($value['institution_id']);
                $res[$key]['institution_name'] = $institution[0]['name'];
                $insti =$instStaffModel->get_course_list($id);
                $res[$key]['teacher'] = $insti;
            }
            Ret(array('code' =>1,'data'=>$res[0]));
        }else{
            Ret(array('code'=>2,'info'=>'获取相关数据失败'));
        }
    }

    public function app_course_api(){
//        before_api();
//        $state = I('state',0,'intval');
//        $is_manager =true;
//        if($state !=2 && !$is_manager){
//            $state = 2;
//        }
        $course_catid = I('course_catid',0,'intval');
        $page = I('page',1,'intval');
        $pagesize = I('pagesize',10,'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page.','.$pagesize;
        $courseModel = D('Course');
        $instModel = D('Institution');
        $count = $courseModel ->get_count($course_catid);
        $data = $courseModel ->get_list($course_catid,$page,$fields = null);
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

    public function app_course_mod_api(){
        before_api();
        checkLogin();
        checkAuth();
        $id =I('id');
        if($id <1){
            Ret(array('code' =>2,'info'=>'参数（worker_id）有误'));
        }
        $res = D('course')->get_infos($id);
        $instModel = D('Institution');
        if($res){
            $institution =$instModel ->get_inst_by_id($res[0]['institution_id']);
            $res[0]['institution_name'] = $institution[0]['name'];
            Ret(array('code' =>1,'data'=>$res));
        }
        Ret(array('code' =>2,'info' =>'请求失败，没有数据！' ));
    }
    /**
     *   获得已报名的试听课程列表
     */
    public function getTryoutCourseList()
    {
        $member_id = I('member_id');
        $courseId=I('course_id');
        $page = I('page',1,'intval');
        $pagesize = I('pagesize',10,'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page.','.$pagesize;
        $reserveModel=D('CourseReserve');
        $model = D('CoursePlan');
        $count=$reserveModel->get_tryout_count($member_id,$courseId);
        $res= $reserveModel->get_tryout_list($member_id,$page,$courseId);
        if ($res) {
            foreach ($res as $key => $value) {
                $host = $this->get_institution_by_id($value['institution_id']);
                $res[$key]['institution'] = $host[0]['name'];
                $course = $this->get_course_by_id($value['course_id']);
                $res[$key]['course'] = $course[0]['name'];
                $res[$key]['pic'] = $course[0]['pic'];
                $apply =$model->get_info($value['course_id']);
//                var_dump($apply);die;
                $type = $this->get_room($apply[0]['room_id']);
                $res[$key]['room'] = $type[0]['position'];
                $res[$key]['start_time']=$apply[0]['start_time'];
                $res[$key]['end_time']=$apply[0]['end_time'];
                $start_time=strtotime($apply[0]['start_time']);
                $end_time=strtotime($apply[0]['end_time']);
                $res[$key]['time']=ceil(($end_time-$start_time)/60);
            }
            Ret(array('code' => 1, 'data' => $res ,'total'=>$count ,'page_count' => ceil($count/$pagesize)));

        } else {
            Ret(array('code' => 2, 'info' => '没有课程计划信息'));

        }
    }

    /*
     * 获取未报名试听列表
    */
    public function getUntryoutCourseList()
    {
        $member_id= I('member_id');
        $courseId=I('course_id');
        $page = I('page',1,'intval');
        $pagesize = I('pagesize',10,'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page.','.$pagesize;
        $buyerCourseModel=D('CourseBuyDetail');
        $model = D('RoomReserve');
        $reserveModel=D('CourseReserve');
        $registerRoomReserveList=$reserveModel->getTryoutRoomReserveList($member_id,$courseId);
        $cond['buyer_id']=$member_id;
        //$buyerCourseList=$buyerCourseModel->field('course_id')->where($cond)->group('course_id')->buildSql();
        //var_dump($registerRoomReserveList);die;
        $buyerCourseListInfo=$buyerCourseModel->field('course_id')->where($cond)->select();
        $courseBuyList=array();
        foreach ($buyerCourseListInfo as $key=>$value)
        {
            $courseBuyList[$key]=$value['course_id'];
        }
        if(in_array($courseId, $courseBuyList))
        {
            Ret(array('code' => 2, 'info' => '该课程您已经购买'));
        }
        else {
            $count = $model->getUntryoutRoomReserveNum($member_id, $registerRoomReserveList, $courseId);
            //var_dump($member_id);die;
            // $try=D('CourseBuyDetail')->count();
            //var_dump($try);
            $res = $model->getUntryoutRoomReserveList($member_id, $page, $registerRoomReserveList,$courseId);
            if ($res) {
                foreach ($res as $key => $value) {
                    $type = $this->get_room($value['room_id']);
                    $res[$key]['room'] = $type[0]['position'];
                    $host = $this->get_institution_by_id($value['institution_id']);
                    $res[$key]['institution'] = $host[0]['name'];
                    $start_time = strtotime($value['start_time']);
                    $end_time = strtotime($value['end_time']);
                    $res[$key]['time'] = ceil(($end_time - $start_time) / 60);
                    $course = $this->get_course_by_id($value['course_id']);
                    $res[$key]['course'] = $course[0]['name'];
                    $res[$key]['pic'] = $course[0]['pic'];
                    $res[$key]['room_reserve_id'] = $value['id'];
                    $memberPeople = $reserveModel->get_courseReservceNumber($value['course_id']);
                    if ($memberPeople <= $value['max_number']) {
                        $res[$key]['member'] = '未报满';
                    }
                    if ($memberPeople >= $value['max_number']) {
                        $res[$key]['member'] = '已报满';
                    }
                }
                Ret(array('code' => 1, 'data' => $res, 'total' => $count, 'page_count' => ceil($count / $pagesize)));

            } else {
                Ret(array('code' => 2, 'info' => '没有课程计划信息'));

            }
        }
    }
    /**
     * 报名试听
     */
    public function app_course_tryout_api(){
//        before_api();
        checkLogin();
        checkAuth();
        $data['member_id'] = I('member_id');
        if($data['member_id']==null){
            return false;
        }
        $data['course_id'] = I('course_id');
        $data['institution_id'] = I('institution_id');
        $data['room_reserve_id']=I('room_reserve_id');
        $data['time']=date('Y-m-d H:i:s');
        $data['state']=1;
        $memberPhone=I('member_phone');
        $memberId=$data['member_id'];
        //查找该会员的信息
        //$memberInfo=getCardInfoByNumber($cardNo);
        $typeArr = [1 => 'member', 2 => 'institution_staff', 3 => 'mall_staff', 4 => 'merchant_staff'];
        foreach ($typeArr as $key => $v) {
            $member = D($v);
            $obj = $member->where(['id' => $memberId])->find();
            if (!empty($obj)) {
                $card = $v;
                $type = $key;
                $model = $obj;
            }
        }
        if (empty($model)) {
            Ret(array('code' => 2, 'info' => '暂无该会员！'));
            die;
        }

        $memberId = $model['id'];
        $courseReserveModel = D("CourseReserve");
        $institutionId=$data['institution_id'];
        $courseId=$data['course_id'];
        $courseReserveList = $courseReserveModel->where("institution_id = $institutionId and 
                             course_id = $courseId and member_id = $memberId")->field("*")->select();
        if (!empty($courseReserveList))
        {
            $countNum = count($courseReserveList);
            $courseReserveList = $courseReserveList[$countNum - 1];
            $useNum = $courseReserveList['used_times'];
        } else
        {
            $useNum = 0;
        }
        $courseReserveData = array(
            "mall_id" => 1,
            "institution_id" => $institutionId,
            "course_id" => $courseId,
            "member_id" => $memberId,
            "time" => $data['time'],
            "used_times" => $useNum + 1,
            "card_id" => $model['card_number'],
            "member_type" => 2,  //试听人员状态为2
            "room_reserve_id"=>$data['room_reserve_id']
        );
        //var_dump($courseReserveData);die;
        $courseReserveModel->data($courseReserveData)->add();
        $roomReserveInfo=D('RoomReserve')->where('id='.$data['room_reserve_id'])->find();
        //var_dump($roomReserveInfo);die;
        $roomModel = D("Room");
        $roomList = $roomModel->where('id='.$roomReserveInfo['room_id'])->field("*")->find();
        //$equip = $roomList['equip_ip'];
        $shopInfo = D('Shop')->where('id=' . $roomList['shop_id'])->find();
        $equip = $shopInfo['equipment_ip'];
        //echo $roomModel->getLastSql();
        //var_dump($roomModel->getLastSql());die;
        $memberDoorAuthModel = D("MemberDoorAuth");
        $memberDoorAuthInsertData = array(
            "eqIp" => $equip,
            "cardNo" => $model['card_number'],
            "memberId" => $model['id'],
            "memberName" => $model['name'],
            "time" => $this->formatTime($roomReserveInfo['start_time'], $roomReserveInfo['end_time'])
        );
        //var_dump($memberDoorAuthInsertData);die;
        $memberDoorAuthModel->add($memberDoorAuthInsertData);
        Ret(array('code'=>1,'info'=>'您已报名试听'));
       /* $roomModel = D("Room");
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
        /*$courseTryoutModel=D('CourseTryout');
        $data['member_id'] = I('member_id');
        if($data['member_id']==null){
            return false;
        }
        $data['course_id'] = I('course_id');
        $data['institution_id'] = I('institution_id');
        $data['time']=date('Y-m-d H:i:s');
        $data['state']=1;
        $member=getMember($data['member_id']);
        $course=getCourse($data['course_id']);
        $data['member_name'] = $member['name'];
        $data['phone'] = $member['phone'];
        $data['start_time'] = $course['start_time'];
        $data['end_time'] = $course['end_time'];
        $data['room_number'] = $course['room_number'];
        $data['plan_id'] = $course['id'];
        $tryout=getCourseTryout();
        if(!$data['member_id']==$tryout){
            $result = $courseTryoutModel->add_course_tryout($data);
        }else{
           Ret(array('code'=>2,'info'=>'您已报名试听'));
        }
        if ($result) {
            Ret(array('code' => 1, 'info' => '您的报名信息已发送给相关机构，稍后我们会跟您联系!'));
        } else {
            Ret(array('code' => 2, 'info' => '报名失败!'));
        }*/
    }

    /**
     * 我的课程(未报名)
     */
    public function getUnregisterCoursePlanList()
    {
//        before_api();
//        checkLogin();
//        checkAuth();
        $member_id= I('member_id');
        $page = I('page',1,'intval');
        $pagesize = I('pagesize',10,'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page.','.$pagesize;
        $buyerCourseModel=D('CourseBuyDetail');
        $model = D('RoomReserve');
        $reserveModel=D('CourseReserve');
        $registerRoomReserveList=$reserveModel->getRegisterRoomReserveList($member_id);
        $cond['buyer_id']=$member_id;
        $buyerCourseList=$buyerCourseModel->field('course_id')->where($cond)->group('course_id')->buildSql();
        //var_dump($registerRoomReserveList);die;
        $count=$model->getUnregisterRoomReserveNum($member_id,$registerRoomReserveList,$buyerCourseList);
        //var_dump($member_id);die;
       // $try=D('CourseBuyDetail')->count();
        //var_dump($try);
        $res = $model->getUnregisterRoomReserveList($member_id,$page,$registerRoomReserveList,$buyerCourseList);
        if ($res) {
            foreach ($res as $key => $value) {
                $type = $this->get_room($value['room_id']);
                $res[$key]['room'] = $type[0]['position'];
                $host = $this->get_institution_by_id($value['institution_id']);
                $res[$key]['institution'] = $host[0]['name'];
                $start_time=strtotime($value['start_time']);
                $end_time=strtotime($value['end_time']);
                $res[$key]['time']=ceil(($end_time-$start_time)/60);
                $course = $this->get_course_by_id($value['course_id']);
                $res[$key]['course'] = $course[0]['name'];
                $res[$key]['pic'] = $course[0]['pic'];
                $res[$key]['room_reserve_id']=$value['id'];
                $memberPeople =$reserveModel->get_courseReservceNumber($value['course_id']);
                if($memberPeople<=$value['max_number']){
                    $res[$key]['member'] = '未报满';
                }
                if($memberPeople>=$value['max_number']){
                    $res[$key]['member'] = '已报满';
                }
            }
            Ret(array('code' => 1, 'data' => $res ,'total'=>$count ,'page_count' => ceil($count/$pagesize)));

        } else {
            Ret(array('code' => 2, 'info' => '没有课程计划信息'));
        }
    }




    private function get_institution_by_id($institution_id)
    {
        if($institution_id){
            return D('Institution')->get_inst_by_id($institution_id);
        }

    }

    /*
     * 根据id查课程
     */
    private function get_course_by_id($course_id)
    {
        if($course_id){
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

    /*
     * 根据id查教室
     */
    private function get_room($room_id)
    {
        if($room_id){
            return D('Room')->where('id='.$room_id)->field('id,position')->select();
        }
    }

//上课ID
    public function addReserve()
    {
        //$mallId = session("mall.mall_id");
        $institutionId = I("institution_id");
        //var_dump($institutionId);die;
        $data['member_id'] = I('member_id');
        $memberId = $data['member_id'];
        //$data['course_id']= I('course_id');
        $courseId = I("course_id");
        //$cardNo = I("card_number");
        $position = I("position");
        $startTime = I("start_time");
        $endTime = I("end_time");
        //var_dump($institutionId);die;
        $data['room_reserve_id'] = I('room_reserve_id');

        if (empty($institutionId) || empty($courseId) ) {
            Ret(array('code' => 2, 'info' => '参数错误！'));
            die;
        }
        $nowTime = date("Y-m-d H:i:s");
        //var_dump($endTime);die;
        if ($nowTime < $endTime) {
            //查找该会员的信息
            //$memberModel = D("Member");
            //$memberInfo = $memberModel->where("card_number = $cardNo")->find();
            //$memberInfo = getCardInfoByNumber($cardNo);
            $typeArr = [1 => 'member', 2 => 'institution_staff', 3 => 'mall_staff', 4 => 'merchant_staff'];
            foreach ($typeArr as $key => $v) {
                $member = D($v);
                $obj = $member->where(['id' => $memberId])->find();
                if (!empty($obj)) {
                    $card = $v;
                    $type = $key;
                    $model = $obj;
                }
            }
            if (empty($model)) {
                Ret(array('code' => 2, 'info' => '暂无该会员！'));
                die;
            }
            //$memberId = $model['id'];
            //var_dump($memberId);die;
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

            //判断使用卡的使用次数
            $institutionsMallRevenueModel = D('InstitutionsMallRevenue');
            $institutionsMallRevenueList = $institutionsMallRevenueModel->where("institutions_id= $institutionId and 
                                        course_id = $courseId and income_ownerid = $memberId")->field("*")->select();
            //echo $institutionsMallRevenueModel->_sql();
            //var_dump($institutionsMallRevenueList);die;
            if (empty($institutionsMallRevenueList)) {
                Ret(array('code' => 2, 'info' => '暂无购卡！'));
                die;
            }
            $institutionsMallRevenueList = $institutionsMallRevenueList[count($institutionsMallRevenueList) - 1];
            $roomReserveModel = D('RoomReserve');
            $roomReserveInfo = $roomReserveModel->getRoomReserveInfo($institutionId, $courseId, $startTime);
            $repeatCourseReserveInfo = $courseReserveModel->hasRepeatCourseReserve($memberId, $courseId, $roomReserveInfo['id']);
            if ($repeatCourseReserveInfo == true) {
                Ret(array('code' => 2, 'info' => '已经报名该课程计划！'));
                die;
            }
            $courseInfo = D('Course')->where('id=' . $courseId)->find();
            $con['buyer_id']=$memberId;
            $con['course_id']=$courseId;
            $courseBuyInfo=D('CourseBuyDetail')->where($con)->select();
            if ($useNum == 0 || $useNum < $courseBuyInfo[0]['validate_times']) {
                $courseReserveData = array(
                    "mall_id" => 1,
                    "institution_id" => $institutionId,
                    "course_id" => $courseId,
                    "member_id" => $memberId,
                    "time" => $courseInfo['course_time'],
                    "used_times" => $useNum + 1,
                    "card_id" => $model['card_number'],
                    "room_reserve_id" => $roomReserveInfo['id'],
                    "member_type" => 1 //花钱报名的状态为1
                );

                $courseReserveModel->data($courseReserveData)->add();
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
                    "cardNo" => $model['card_number'],
                    "memberId" => $model['id'],
                    "memberName" => $model['name'],
                    "time" => $this->formatTime($startTime, $endTime)
                );
                //var_dump($memberDoorAuthInsertData);die;
                $memberDoorAuthModel->add($memberDoorAuthInsertData);
                Ret(array('code' => 1, 'info' => '报名成功！', 'data' => []));

            } else {
                Ret(array('code' => 2, 'info' => '卡的次数不足！'));
                die;
            }
        } else {
            Ret(array('code' => 2, 'info' => '报名时间已过！'));
            die;
        }
    }
        /*
        $reserveModel=D('CourseReserve');
        $data['member_id'] =I('member_id');
        $data['course_id']= I('course_id');
        $card = getCardInfo($data['course_id']);
        $car['course_price'] = $card[0]['course_price'];
        $reserve=getReserve($data['member_id']);
        $res['id'] = $reserve[0]['id'];
        $res['used_times'] = $reserve[0]['used_times'];
        $res['total_degree'] = $reserve[0]['total_degree'];
        if( $res['total_degree']<=0){
            Ret(array('code'=>2,'info'=>'您的次数已不足，请从新购买'));
        }else{
            $res['total_degree'] =  $res['total_degree']-1;
            $res['used_times'] =  $res['used_times']+1;
        }
        updateReserve($res);
        $data['institution_id'] =I('institution_id');
        $data['time']=date('Y-m-d H:i:s');
        $data = $reserveModel->add($data);
        if($data){
            Ret(array('code'=>1,'data'=>'报名成功'));
        }else{
            Ret(array('code'=>2,'报名失败'));
        }
    }

    public function delReserve(){
        $id = I('id');
        if(!$id){
            Ret(array('code'=>2,'info'=>'参数有误'));
        }
        $reserveModel=D('CourseReserve');
        $data =$reserveModel ->deleteReservce($id);
        if($data){
            Ret(array('code'=>1,'data'=>'删除成功'));
        }else{
            Ret(array('code'=>2,'info'=>'删除失败'));
        }
    }


    /**
     * 我的课程(已报名)
     */
    public function getRegisterCoursePlanList()
    {
//        before_api();
//        checkLogin();
//        checkAuth();
        $member_id = I('member_id');
        $page = I('page',1,'intval');
        $pagesize = I('pagesize',10,'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page.','.$pagesize;
        $reserveModel=D('CourseReserve');
        $model = D('CoursePlan');
        $count=$reserveModel->get_count($member_id,false);
       $res= $reserveModel->get_list($member_id,$page,false);
        if ($res) {
            foreach ($res as $key => $value) {
                $host = $this->get_institution_by_id($value['institution_id']);
                $res[$key]['institution'] = $host[0]['name'];
                $course = $this->get_course_by_id($value['course_id']);
                $res[$key]['course'] = $course[0]['name'];
                $res[$key]['pic'] = $course[0]['pic'];
                $apply =$model->get_info($value['course_id']);
//                var_dump($apply);die;
                $type = $this->get_room($apply[0]['room_id']);
                $res[$key]['room'] = $type[0]['position'];
                $res[$key]['start_time']=$apply[0]['start_time'];
                $res[$key]['end_time']=$apply[0]['end_time'];
                $start_time=strtotime($apply[0]['start_time']);
                $end_time=strtotime($apply[0]['end_time']);
                $res[$key]['time']=ceil(($end_time-$start_time)/60/60);
            }
            Ret(array('code' => 1, 'data' => $res ,'total'=>$count ,'page_count' => ceil($count/$pagesize)));

        } else {
            Ret(array('code' => 2, 'info' => '没有课程计划信息'));

        }
    }


    /**
     * 课程相片
     */
    public function course_pic(){
        $id =I('id');
        if($id<1){
            Ret(array('code'=>2,'info'=>'参数有误'));
        }
        $data =D('Course')->get_pic($id);
//        $content = $this->course_content($id);
//        $data[0]['content']=$content[0]['content'];
//        $teacher = $this->course_teacher($id);
//        $data[0]['teacher']=$teacher[0]['teacher'];
//        $honor = $this->course_honor($id);
//        $data[0]['credit_img']=$honor[0]['credit_img'];
//        $card = $this->course_card($id);
//        $data[0]['card_bag']=$card[0]['card_bag'];
        if($data){
            Ret(array('code'=>1,'data'=>$data[0]));
        }else{
            Ret(array('code'=>2,'info'=>'没有数据'));
        }
    }
    /**
     * 课程介绍
     */
    public function course_content(){
        $id =I('id');
        if($id<1){
            Ret(array('code'=>2,'info'=>'参数有误'));
        }
        $instModel = D('Institution');
        $data =D('Course')->get_content($id);
        if($data){
            $res = $instModel->get_info($data[0]['institution_id']);
            $data[0]['institution_name']=$res[0]['name'];
            Ret(array('code'=>1,'data'=>$data[0]));
        }else{
            Ret(array('code'=>2,'info'=>'没有数据'));
        }
    }

    /**
     * 师之力量
     */
    public function course_teacher(){
        $id =I('id');
        if($id<1){
            Ret(array('code'=>2,'info'=>'参数有误'));
        }

        $instStaffModel=D('InstStaff');
        $data =D('Course')->get_content($id);
        foreach($data as $key=> $value){
            $insti =$instStaffModel->get_course_list($id);
            $data[$key]['teacher'] = $insti;
        }
        if($data){
            Ret(array('code'=>1,'data'=>$data[0]));
        }else{
            Ret(array('code'=>2,'info'=>'没有数据'));
        }
    }

    /**
     * 课程荣誉图片
     */
    public function course_honor(){
        $id =I('id');
        if($id<1){
            Ret(array('code'=>2,'info'=>'参数有误'));
        }
        $data =D('Course')->get_honor($id);
        if($data){
            $data[0]['credit_img'] = explode(',',$data[0]['credit_img']);
            Ret(array('code'=>1,'data'=>$data[0]));
        }else{
            Ret(array('code'=>2,'info'=>'没有数据'));
        }
    }
    /**
     * 课程包订购
     */
    public function course_card(){
        $id = I('id');
        if(!$id){
            Ret(array('code'=>2,'info'=>'参数有误'));
        }
        $data =D('Course')->get_card($id);
        $courseCardModel  = D('CourseCard');
        $card = $courseCardModel->getCard($id);
        $data[0]['card_bag'] = $card;
        foreach($data[0]['card_bag'] as $key=> $value){
            $data[0]['card_bag'] [$key]['price_typeid']=get_typeid($value['price_typeid']);
        }
        if($data){
            Ret(array('code'=>1,'data'=>$data[0]));
        }else{
            Ret(array('code'=>2,'info'=>'没有数据'));
        }
    }

    /**
     * 订购
     */
    public function order()
    {
        //$data['card_number'] = I('card_number');
        //缴费收入
        $data['total'] = I('course_price');
        $data['institutions_id'] = I('institutions_id');
        $data['institutions_name'] = I('institutions_name');
        $data['course_name'] = I('course_name');
        //var_dump($data['course_name']);die;
        $data['course_id'] = I('course_id');
        $data['course_card_id'] = I('course_card_id');
        $data['number'] = 1;
        $data['course_card_name'] = I('course_card_name');
        $data['start_time']=date("Y-m-d H:i:s");
        $data['end_time']=date('Y-m-d H:i:s', strtotime("+1 year"));
        $memberId=I('member_id');
        $memberPhone=I('member_phone');
        //var_dump($data);die;
        foreach ($data as $k => $val){
            if(empty($val)){
                Ret(array('code' => 3, 'info' => $k .'不能为空！'));
            }
        }
       /* $card = getCard($data['card_number']);
        $cardInfo['card_typeid'] = $card[0]['card_typeid'];
        $cardInfo['card_state'] = $card[0]['card_state'];
        $cardInfo['cardnumber_no'] = $card[0]['cardnumber_no'];
        $cardInfo['card_number'] = $card[0]['card_number'];
        if (!$cardInfo['card_number']) {
            Ret(array('code' => 2, 'info' => '卡号不存在'));
        }
        */
        $typeArr = [1 => 'member', 2 => 'institution_staff', 3 => 'mall_staff', 4 => 'merchant_staff'];
        foreach ($typeArr as $key => $v) {
            $member = D($v);
            $obj = $member->where(['phone' => $memberPhone])->find();
            if (!empty($obj)) {
                $card = $v;
                $type = $key;
                $model = $obj;
            }
        }
        $data['card_number']=$model['card_number'];
        $memberCard = getCardByCardNo($data['card_number']);
        $cardInfo['card_typeid'] = $memberCard[0]['card_typeid'];
        $cardInfo['card_state'] = $memberCard[0]['card_state'];
        $cardInfo['cardnumber_no'] = $memberCard[0]['cardnumber_no'];
        $cardInfo['card_number'] = $memberCard[0]['card_number'];
        if (!$cardInfo['card_number']) {
            Ret(array('code' => 2, 'info' => '卡号不存在'));
        }
        //var_dump($model);die;
        if(($model['balance'] - $data['total']) < 0) {
            Ret(array('code' => 2, 'info' => '余额不足'));
        }

        /*if(!session("worker_id")){
            Ret(array('code' => 3, 'info' => '请登录！'));
        }*/
        $carModel = D($card);
        $amount = $model['balance'] - $data['total'];
        $res = $carModel->where(['card_number' => $data['card_number']])->save(['balance'=>$amount]);

        //获取课程卡的信息
        $courseCardInfo=D('CourseCard')->where('id='.$data['course_card_id'])->find();
        //var_dump($courseCardInfo);die;
        $timeLength=$this->getDayNum($data['start_time'], $data['end_time']);
        //存入购买课程信息表中
        $courseBuyInfo['buyer_id']=$model['id'];
        $courseBuyInfo['buyer_type_id']=1;
        $courseBuyInfo['buyer_type']='会员';
        if($card=='institution_staff')
        {
            $courseBuyInfo['buyer_type_id']=3;
            $courseBuyInfo['buyer_type']='机构员工';
        }
        if($card=='mall_staff')
        {
            $courseBuyInfo['buyer_type_id']=2;
            $courseBuyInfo['buyer_type']='商场员工';
        }
        if($card=='merchant_staff')
        {
            $courseBuyInfo['buyer_type_id']=4;
            $courseBuyInfo['buyer_type']='商户员工';
        }
        $courseBuyInfo['buyer_card_number']=$cardInfo['card_number'];
        $courseBuyInfo['buyer_card_numberNo']=$cardInfo['cardnumber_no'];
        $courseBuyInfo['course_id']=$data['course_id'];
        $courseBuyInfo['course_name']=$data['course_name'];
        $courseBuyInfo['course_card_id']=$data['course_card_id'];
        $courseBuyInfo['course_card_name']=$data['course_card_name'];
        $courseBuyInfo['course_card_typeid']=$courseCardInfo['price_typeid'];
        $courseBuyInfo['course_card_type']=$courseCardInfo['price_type'];
        $courseBuyInfo['validate_start_time']=$data['start_time'];
        $courseBuyInfo['validate_end_time']=$data['end_time'];
        $courseValidateTimes=$data['number']*$courseCardInfo['all_count'];
        $courseBuyInfo['validate_times']=$courseValidateTimes;
        $courseBuyInfo['used_times']=0;
        $courseBuyInfo['unused_times']=$courseValidateTimes;
        $courseBuyInfo['fee_times']=$courseCardInfo['validity_ttimes']*$data['number'];
        $courseBuyInfo['gift_times']=$courseCardInfo['gifts']*$data['number'];
        $courseBuyInfo['buy_num']=$data['number'];

        //该课程是否为年卡
        if($courseCardInfo['price_typeid']==4)
        {
            $coursePriceValue=$data['total']*0.7/$courseBuyInfo['fee_times'];
            //var_dump($coursePriceValue);die;
            $coursePrice=round($coursePriceValue,2);
            //var_dump($coursePrice);die;
            $courseBuyInfo['course_price']=$coursePrice;

            D('CourseBuyDetail')->add($courseBuyInfo);
            //var_dump($courseBuyInfo);die;
            //$Model = M();
            //$sql="select * from course_buy_detail order by desc limit 1";
            $newCourseBuyInfo=D('CourseBuyDetail')->max('id');
            //var_dump($newCourseBuyInfo);die;
            // 机构收入InstMallRevenueModel
            $insMallRevenueModel = D('InstitutionsMallRevenue');
            //var_dump($courseBuyInfo);die;
            $institutions['income_ownertypeid'] = $courseBuyInfo['buyer_type_id'];
            $institutions['income_ownertype'] = $courseBuyInfo['buyer_type'];
            $institutions['income_ownerid'] = $model['id'];
            $institutions['income_ownername'] = $model['name'];
            $institutions['income_typeid'] = 2;
            $institutions['income_type'] = "课程包收入";
            $institutions['income_time'] = date("Y-m-d H:i:s");
            //机构收入金额
            //$institutions['income_num'] = $data['total'];
            //实际收入金额
            $institutions['income_num'] = $data['total']*0.3;
            $institutions['institutions_id'] = $data['institutions_id'];
            $institutions['institutions_name'] = $data['institutions_name'];
            $institutions['course_card_name'] = $data['course_card_name'];
            $institutions['course_card_id'] = $data['course_card_id'];
            $institutions['course_id'] = $data['course_id'];
            $institutions['course_name'] = $data['course_name'];;
            $institutions['submit_id'] = $model['id'];
            $institutions['submitter'] = $model['name'];
            //课程包ID
            $institutions['number'] = $data['number'];
            $institutions['validation_start_time']=$data['start_time'];
            $institutions['validation_end_time']=$data['end_time'];
            //课程包包含的总次数（包含购买次数+赠送次数）
            $institutions['validate_course_times']=$courseCardInfo['all_count'];
            //课程包的单次价格
            $institutions['course_price']=$coursePrice;
            //机构应收金额
            $institutions['income_receive_num']=$data['total'];
            $institutions['card_number']=$data['card_number'];
            $institutions['card_numberNo']=$cardInfo['cardnumber_no'];
            $institutions['course_buy_detail_id']=$newCourseBuyInfo[0]['id'];

            //会员卡消费
            $expenseDetailModel = D('ExpenseDetail');
            //var_dump($institutions);die;
            $expenseDetail['cardnumber_no'] = $cardInfo['cardnumber_no'];
            $expenseDetail['cost_typeid'] = 5;
            $expenseDetail['cost_type'] = "课程包费用-" . $data['course_card_name'];
            $expenseDetail['card_ownerid'] = $model['id'];
            $expenseDetail['card_owner_name'] = $model['name'];
            $expenseDetail['card_rechargenum'] = $data['total'];
            $expenseDetail['card_rechargetime'] = date("Y-m-d H:i:s");
            $expenseDetail['card_typeid'] = $courseBuyInfo['buyer_type_id'];
            $expenseDetail['submit_id'] = $model['id'];
            $expenseDetail['submitter'] = $model['name'];
            //课程购买数量
            $expenseDetail['number'] = $data['number'];
            $expenseDetail['cost_content_id']=$data['course_card_id'];
            $expenseDetail['cost_content_name']=$data['course_card_name'];
            $expenseDetail['card_type']=$courseBuyInfo['buyer_type'];
            $expenseDetail['course_buy_detail_id']=$newCourseBuyInfo[0]['id'];
            $expenseDetail['card_number']=$data['card_number'];
            $expenseDetail['expense_owner_id']=$data['institutions_id'];
            $expenseDetail['expense_owner_name']=$data['institutions_name'];
            $expenseDetail['expense_owner_typeid']=1;
            //1为机构，2 为商户
            $expenseDetail['expense_owner_type']='机构';
            //var_dump($institutions);die;
            $res1 = $insMallRevenueModel->add($institutions);
            $res2 = $expenseDetailModel->data($expenseDetail)->add();

            //商场收入
            //缴费会员ID
            $mallRevenueInfo['income_ownerid']=$model['id'];
            //缴费会员名字
            $mallRevenueInfo['income_ownername']=$model['name'];
            $mallRevenueInfo['income_typeid']=5;
            $mallRevenueInfo['income_type']="课程包费收入";
            //缴费收入金额
            $mallRevenueInfo['income_num']=$data['total']*0.7;
            $mallRevenueInfo['income_time']=date("Y-m-d H:i:s");
            //收入对象的ID
            $mallRevenueInfo['income_content_id']=$data['course_card_id'];
            //收入对象的名称
            $mallRevenueInfo['income_content_name']=$data['course_card_name'];
            $mallRevenueInfo['income_ownertypeid']=$courseBuyInfo['buyer_type_id'];
            $mallRevenueInfo['income_ownertype']=$courseBuyInfo['buyer_type'];
            $mallRevenueInfo['start_time']=$data['start_time'];
            $mallRevenueInfo['end_time']=$data['end_time'];

            $mallRevenueInfo['time_long']=$timeLength;
            $mallRevenueInfo['time_long_unit']="天";
            $mallRevenueInfo['cardnumber_no']=$cardInfo['cardnumber_no'];
            $mallRevenueInfo['card_number']=$data['card_number'];
            $mallRevenueInfo['card_typeid']=$courseBuyInfo['buyer_type_id'];
            $mallRevenueInfo['card_ownerid']=$model['id'];
            $mallRevenueInfo['card_owner_name']=$model['name'];
            $mallRevenueInfo['submitter']=$model['name'];
            $mallRevenueInfo['submitter_id']=$model['id'];
            $mallRevenueInfo['course_buy_detail_id']=$newCourseBuyInfo[0]['id'];

            D('MallRevenue')->add($mallRevenueInfo);

            if ($res && $res1 && $res2) {
                Ret(array('code' => 1, 'info' => '缴费成功！'));
            }
            Ret(array('code' => 2, 'info' => '缴费失败！'));
        }
        else {
            $coursePriceValue = $data['total'] / $courseBuyInfo['fee_times'];
            //var_dump($coursePriceValue);die;
            $coursePrice = round($coursePriceValue, 2);
            //var_dump($coursePrice);die;
            $courseBuyInfo['course_price'] = $coursePrice;
            //var_dump($courseBuyInfo);die;
            D('CourseBuyDetail')->add($courseBuyInfo);
            //$Model = M();
            //$sql="select * from course_buy_detail order by desc limit 1";
            //$newCourseBuyInfo=$Model->query($sql);
            $newCourseBuyInfo = D('CourseBuyDetail')->max('id');
            //var_dump($newCourseBuyInfo);die;
            // 机构收入
            $institutionsMallRevenueModel = D('InstitutionsMallRevenue');
            $institutions['income_ownertypeid'] = $courseBuyInfo['buyer_type_id'];
            $institutions['income_ownertype'] = $courseBuyInfo['buyer_type'];
            $institutions['income_ownerid'] = $model['id'];
            $institutions['income_ownername'] = $model['name'];
            $institutions['income_typeid'] = 2;
            $institutions['income_type'] = "课程包收入";
            $institutions['income_time'] = date("Y-m-d H:i:s");
            //机构消费金额
            //$institutions['income_num'] = $data['total'];
            $institutions['income_num'] = 0;
            $institutions['institutions_id'] = $data['institutions_id'];
            $institutions['institutions_name'] = $data['institutions_name'];
            $institutions['course_card_name'] = $data['course_card_name'];
            $institutions['course_card_id'] = $data['course_card_id'];
            $institutions['course_id'] = $data['course_id'];
            $institutions['course_name'] = $data['course_name'];
            $institutions['submit_id'] = $model['id'];
            $institutions['submitter'] = $model['name'];
            $institutions['number'] = $data['number'];
            $institutions['validation_start_time'] = $data['start_time'];
            $institutions['validation_end_time'] = $data['end_time'];
            //课程包包含的总次数（包含购买次数+赠送次数）
            $institutions['validate_course_times'] = $courseCardInfo['all_count'];
            //课程包的单次价格
            $institutions['course_price'] = $coursePrice;
            //机构应收金额
            $institutions['income_receive_num'] = $data['total'];
            $institutions['card_number'] = $data['card_number'];
            $institutions['card_numberNo'] = $cardInfo['cardnumber_no'];
            $institutions['course_buy_detail_id'] = $newCourseBuyInfo[0]['id'];

            //会员卡消费
            $expenseDetailModel = D('ExpenseDetail');
            $expenseDetail['cardnumber_no'] = $cardInfo['cardnumber_no'];
            $expenseDetail['cost_typeid'] = 5;
            $expenseDetail['cost_type'] = "课程包费用-" . $data['course_card_name'];
            $expenseDetail['card_ownerid'] = $model['id'];
            $expenseDetail['card_owner_name'] = $model['name'];
            $expenseDetail['card_rechargenum'] = $data['total'];
            $expenseDetail['card_rechargetime'] = date("Y-m-d H:i:s");
            $expenseDetail['card_typeid'] = $courseBuyInfo['buyer_type_id'];
            $expenseDetail['submit_id'] = $model['id'];
            $expenseDetail['submitter'] = $model['name'];
            //课程购买数量
            $expenseDetail['number'] = $data['number'];
            $expenseDetail['cost_content_id'] = $data['course_card_id'];
            $expenseDetail['cost_content_name'] = $data['course_card_name'];
            $expenseDetail['card_type'] = $courseBuyInfo['buyer_type'];
            $expenseDetail['course_buy_detail_id'] = $newCourseBuyInfo[0]['id'];
            $expenseDetail['card_number'] = $data['card_number'];
            $expenseDetail['expense_owner_id'] = $data['institutions_id'];
            $expenseDetail['expense_owner_name'] = $data['institutions_name'];
            $expenseDetail['expense_owner_typeid'] = 1;
            //1为机构，2 为商户
            $expenseDetail['expense_owner_type'] = '机构';

            $res1 = $institutionsMallRevenueModel->data($institutions)->add();
            $res2 = $expenseDetailModel->data($expenseDetail)->add();

            //商场收入
            //缴费会员ID
            $mallRevenueInfo['income_ownerid'] = $model['id'];
            //缴费会员名字
            $mallRevenueInfo['income_ownername'] = $model['name'];
            $mallRevenueInfo['income_typeid'] = 5;
            $mallRevenueInfo['income_type'] = "课程包费收入";
            //缴费收入金额
            $mallRevenueInfo['income_num'] = $data['total'];
            $mallRevenueInfo['income_time'] = date("Y-m-d H:i:s");
            //收入对象的ID
            $mallRevenueInfo['income_content_id'] = $data['course_card_id'];
            //收入对象的名称
            $mallRevenueInfo['income_content_name'] = $data['course_card_name'];
            $mallRevenueInfo['income_ownertypeid'] = $courseBuyInfo['buyer_type_id'];
            $mallRevenueInfo['income_ownertype'] = $courseBuyInfo['buyer_type'];
            $mallRevenueInfo['start_time'] = $data['start_time'];
            $mallRevenueInfo['end_time'] = $data['end_time'];

            $mallRevenueInfo['time_long'] = $timeLength;
            $mallRevenueInfo['time_long_unit'] = "天";
            $mallRevenueInfo['cardnumber_no'] = $cardInfo['cardnumber_no'];
            $mallRevenueInfo['card_number'] = $data['card_number'];
            $mallRevenueInfo['card_typeid'] = $courseBuyInfo['buyer_type_id'];
            $mallRevenueInfo['card_ownerid'] = $model['id'];
            $mallRevenueInfo['card_owner_name'] = $model['name'];
            $mallRevenueInfo['submitter'] = $model['id'];
            $mallRevenueInfo['submitter_id'] = $model['name'];
            $mallRevenueInfo['course_buy_detail_id'] = $newCourseBuyInfo[0]['id'];

            D('MallRevenue')->add($mallRevenueInfo);


            if ($res && $res1 && $res2) {
                Ret(array('code' => 1, 'info' => '缴费成功！'));
            }
            Ret(array('code' => 2, 'info' => '缴费失败！'));
        }
       /* $data['course_id'] =I('id');
        $data['institution_id'] = I('institution_id');
        $data['time'] =date('Y-m-d H:i:s');
        $data['used_times'] =0;
        $data['card_id'] = I('card_id');
        $card=getCard($data['card_id']);
        $card_price =$card[0]['course_price'];
        $course_id =$card[0]['course_id'];
        $course=getCoursePic( $data['course_id']);
        $name =$course[0]['name'];
        $pic =$course[0]['pic'];
        $validity_ttimes =$card[0]['validity_ttimes'];
        $gifts =$card[0]['gifts'];
        $all_count =$card[0]['all_count'];
        $data['total_degree'] =$validity_ttimes+$gifts;
        $data['member_id'] = I('member_id');
       $member= getMemberCard($data['member_id']);
        $res['cardnumber_no'] = $member[0]['cardnumber_no'];
        $res['card_number'] = $member[0]['card_number'];
        $res['id'] = $member[0]['id'];
        $res['name'] = $member[0]['name'];
        $res['balance'] = $member[0]['balance'];
        if($res['balance']< $card_price){
            Ret(array('code'=>2,'info'=>'余额不足'));
        }else{
            $res['balance'] = $res['balance']- $card_price;
        }
        if(updateMemberBalance($res)) {
            $cads['cardnumber_no'] = $res['cardnumber_no'];
            $cads['card_ownerid'] = $res['id'];
            $cads['card_rechargetime'] = date('Y-m-d H:i:s');
            $cads['card_typeid'] = 1;
            $cads['cost_typeid'] = 5;
            $cads['cost_type'] = '课程包费用';
            $cads['card_rechargenum'] = $card_price;
            $cads['goods_name'] =$name;
            $cads['totall'] = $all_count;
            $cads['pic'] =  $pic;
            }
            addFees($cads);

        $mallinfo['income_ownerid'] = $res['id'];
        $mallinfo['income_ownername'] = $res['name'];
        $mallinfo['income_time'] = date('Y-m-d H:i:s');
        $mallinfo['income_ownertypeid'] = 3;
        $mallinfo['income_ownertype'] = '会员';
        $mallinfo['income_typeid']=5;
        $mallinfo['income_type'] ='课程包费收入';
        $mallinfo['income_num'] =$card_price;
        addMall($mallinfo);
       $data= addReserve($data);
        if($data){
            Ret(array('code'=>1,'data'=>'订购成功'));
        }else{
            Ret(array('code'=>2,'info'=>'订购失败'));
        }*/
    }
    public function getDayNum($start_time, $end_time, $tags='-')
    {
        return (strtotime($end_time) - strtotime($start_time)) / 86400;
    }
    public function formatTime($startTime, $endTime)
    {
        //0,0:00-23:59;1,0:00-23:59;2,0:00-23:59;3,0:00-23:59;4,0:00-23:59;5,0:00-23:59;6,0:00-23:59
        $str = "";
        $sTime = explode(" ", $startTime);
        $week = date("w", strtotime($sTime[0]));

        $eTime = explode(" ", $endTime);
        $str .= $sTime[0] . "," . $eTime[0] . "#" . rand(1, 1000) . "]";
        $strTime = "";

        for ($i = 0; $i <= 6; $i++) {
            $sHour = explode(":", $sTime[1]);
            $eHour = explode(":", $eTime[1]);
            if ($sHour[0] == "00：00") {
                $sTime1 = "0:00";
            } else {
                $sTime1 = $sHour[0] . ":" . $sHour[1];
            }

            if ($i == $week) {
                $strTime .= $i . "," . $sTime1 . "-" . $eHour[0] . ":" . $eHour[1] . ";";
                continue;
            }
            $strTime .= $i . ",23:59-23:59;";
        }
        return $str . substr($strTime, 0, strlen($strTime) - 1);


    }
}