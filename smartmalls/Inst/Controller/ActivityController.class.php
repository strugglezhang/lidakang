<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/7/11
 * Time: 22:54
 */
namespace Inst\Controller;
class ActivityController extends CommonController {
    /*
     * 活动报名管理
     */

    public function activity_enroll_api()
    {
        before_api();
        checkLogin();

        checkAuth();

        $state = I('state', 2, 'intval');
        $is_manager = true;
        if ($state !== 2 && !$is_manager) {
            $state = 2;
        }
        $institution_id = session('institution_id');
        $start_time = I('start_time');
        $end_time = I('end_time');
        $activityModel = D('Activity');
        $act = $activityModel->get_list_by_time($institution_id, $start_time, $end_time);

        $model = D('ActivityReserve');
        foreach ($act as $k => $v) {
            $count = $model->get_count($v["id"], $institution_id);
            $act[$k]['count'] = $count;
        }
        if ($act) {
            Ret(array('code' => 1, 'data' => $act));

        } else {
            Ret(array('code' => 2, 'info' => '没有活动信息'));
        }
    }
    /*
     * 活动报名详情
     */

    public function activity_enroll_view_api()
    {
        before_api();
        checkLogin();

        checkAuth();

        $state = I('state', 2, 'intval');
        $is_manager = true;
        if ($state !== 2 && !$is_manager) {
            $state = 2;
        }
        $institution_id = session('inst.institution_id');
        $activityModel = D('ActivityReserve');
        $act = $activityModel->get_enroll_list($institution_id);
        $model = D('Member');
        $cardBondModel=D('CardBond');
        foreach ($act as $k => $v) {
            $member = $model->getMemberInfo($v['member_id']);
            $act['id'] = $member[0]['id'];
            $act['name'] = $member[0]['name'];
            $act['phone'] = $member[0]['phone'];
            $act['parent_phone'] = $member[0]['parent_phone'];
            $act['pic'] = $member[0]['pic'];
            $act['card_number']=$cardBondModel->getCardByMemberID($v['member_id']);
        }
        if ($act) {
            Ret(array('code' => 1, 'data' => $act));

        } else {
            Ret(array('code' => 2, 'info' => '没有活动信息'));
        }

    }

/*
 * 根据教室id查教室
 */
    private function get_room($room_id){
        return D('Room')->get_room_by_id($room_id);

    }
    private function get_institution_by_id($institution_id){
        return D('Institution')->get_inst_by_id($institution_id);

    }
    private function get_course_by_id($course_id){
        return D('Course')->get_course_by_id($course_id);

    }

    /*
     * 活动日志列表
     */
    public function activity_log()
    {
        before_api();
        $page = I('page', 1, 'intval');
        $pagesize = I('pagesize', 10, 'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page . ',' . $pagesize;
        $data = D('ActivityCheckLog')->page($page)->select();
        $count = D('ActivityCheckLog')->count();
        if ($data) {
            Ret(array('code' => 1, 'data' => $data, 'total' => $count, 'page_count' => ceil($count / $pagesize)));
        } else {
            Ret(array('code' => 2, 'data' => '数据获取失败'));
        }
    }

    
}