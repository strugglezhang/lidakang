<?php
/**
 * Created by PhpStorm.
 * User: 44364
 * Date: 2017/10/13
 * Time: 17:51
 */

namespace Member\Controller;
class ConsumptionController extends CommonController
{
    private $member;

    public function __construct()
    {
        $this->member = D('Member');
    }

    /**
     * 会员充值明细
     */
    public function memberDetail()
    {
        $keyword = I('keyword');
        $start_time = I('start_time');
        $end_time = I('end_time');
        $page = I('page', 1, 'intval');
        $pagesize = I('pagesize', 10, 'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page . ',' . $pagesize;
        $memberModel = D('Member');
        $rechargeDetailModel = D('RechargeDetail');
        $count = $rechargeDetailModel->getCount($start_time, $end_time, $keyword);
        //var_dump($count);die;
        $res = $rechargeDetailModel->getRecharge($start_time, $end_time, $keyword, $page);
        foreach ($res as $key => $value) {
            //$member = $memberModel->getNumberInfo($value['card_ownerid']);
            $res[$key]['member_name'] = $value['card_owner_name'];
        }
        //var_dump($res);die;
        if ($res == null) {
            Ret(array('code' => 2, 'info' => '没有数据'));
        }
        if ($res) {
            Ret(array('code' => 1, 'data' => $res, 'total' => $count, 'page_count' => ceil($count / $pagesize)));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据'));
        }


    }

    /**
     * 会员消费明细
     */
    public function memberConsumption()
    {
        $keyword = I('keyword');
        $start_time = I('start_time');
        $end_time = I('end_time');
        $page = I('page', 1, 'intval');
        $pagesize = I('pagesize', 10, 'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page . ',' . $pagesize;
        $memberModel = D('Member');
        $expenseDetailModel = D('ExpenseDetail');
        $count = $expenseDetailModel->get_by_count($start_time, $end_time, $keyword);
        $res = $expenseDetailModel->get_by_list($start_time, $end_time, $keyword, $page);
        foreach ($res as $key => $value) {
            $member = $memberModel->getNumberInfo($value['card_ownerid']);
            $res[$key]['member_name'] = $member[0]['name'];
            // $res[$key]['startEndTime'] = $value['validation_start_time']."——".$value['validation_end_time'];
            //$res[$key]['timeLength']=$item['time_long'].$item['time_long_unit'];
        }
        if ($res) {
            Ret(array('code' => 1, 'data' => $res, 'total' => $count, 'page_count' => ceil($count / $pagesize)));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据'));
        }

    }
    /*
     * 会员上课统计
     */
    public function getCourseStudyDetailList()
    {
        $keyword = I('keyword');
        $start_time = I('start_time');
        $end_time = I('end_time');
        $instId=I('inst_id');
        $courseId=I('course_id');
        $page = I('page', 1, 'intval');
        $pagesize = I('pagesize', 10, 'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page . ',' . $pagesize;
        $res=D('CourseStudyDetail')->getCourseStudyDetailList($start_time, $end_time, $keyword, $page,$instId,$courseId);
        $count=D('CourseStudyDetail')->getCourseStudyDetailCount($start_time, $end_time, $keyword, $page,$instId,$courseId);
        if ($res) {
            Ret(array('code' => 1, 'data' => $res, 'total' => $count, 'page_count' => ceil($count / $pagesize)));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据'));
        }
    }
    /**
     * 会员返还明细
     */
    public function memberReturnDetail()
    {
        $keyword = I('keyword');
        $start_time = I('start_time');
        $end_time = I('end_time');
        $page = I('page', 1, 'intval');
        $pagesize = I('pagesize', 10, 'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page . ',' . $pagesize;
        $memberModel = D('Member');
        $rechargeDetailModel = D('RechargeDetail');
        $count = $rechargeDetailModel->get_by_count($start_time, $end_time, $keyword);
        $res = $rechargeDetailModel->get_by_list($start_time, $end_time, $keyword, $page);
        foreach ($res as $key => $value) {
            $member = $memberModel->getNumberInfo($value['card_ownerid']);
            $res[$key]['member_name'] = $member[0]['name'];
        }
        if ($res) {
            Ret(array('code' => 1, 'data' => $res, 'total' => $count, 'page_count' => ceil($count / $pagesize)));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据'));
        }
    }

    /**
     * 机构消费明细
     */
    public function instConsumptionDetail()
    {
        $start_time = I('start_time');
        $end_time = I('end_time');
        $institution_id = I('institution_id', 0, 'intval');
        $page = I('page', 1, 'intval');
        $pagesize = I('pagesize', 10, 'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page . ',' . $pagesize;
        $instOutComeDetailModel = D('InstOutComeDetail');
        $InstModel = D('Institution');
        $InstStaff = D('InstStaff');
        $count = $instOutComeDetailModel->getCount($start_time, $end_time, $institution_id);
        //var_dump($count);die;
        $res = $instOutComeDetailModel->getInstRecharge($start_time, $end_time, $institution_id, $page);
        foreach ($res as $key => $item) {
            //$inststaff = $InstStaff->get_inst_id($item['card_ownerid']);
            $res[$key]['startEndTime'] = $item['start_time'] . "——" . $item['end_time'];
            $res[$key]['timeLength'] = $item['time_long'] . $item['time_long_unit'];
            //$institution = $InstModel->get_inst_by_id($res[$key]['institution_id']);
            // $res[$key]['institution_name'] = $institution[0]['name'];
        }
        if ($res) {
            Ret(array('code' => 1, 'data' => $res, 'total' => $count, 'page_count' => ceil($count / $pagesize)));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据'));
        }
    }

    /**
     * 机构收入明细
     */
    public function instIncomeDetail()
    {
        //var_dump('11111');die;
        $start_time = I('start_time');
        $end_time = I('end_time');
        $institution_id = I('institution_id', '0', 'intval');
        $page = I('page', 1, 'intval');
        $pagesize = I('pagesize', 10, 'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page . ',' . $pagesize;
        $institutionsMallRevenueModel = D('InstitutionsMallRevenue');
        $count = $institutionsMallRevenueModel->getCount($start_time, $end_time, $institution_id);
        $res = $institutionsMallRevenueModel->getIncomeRecharge($start_time, $end_time, $institution_id, $page);
        foreach ($res as $key => $item) {
            //$inststaff = $InstStaff->get_inst_id($item['card_ownerid']);
            $res[$key]['startEndTime'] = $item['validation_start_time'] . "——" . $item['validation_end_time'];
            //$res[$key]['timeLength']=$item['time_long'].$item['time_long_unit'];
            //$institution = $InstModel->get_inst_by_id($res[$key]['institution_id']);
            // $res[$key]['institution_name'] = $institution[0]['name'];
        }
        if ($res) {
            Ret(array('code' => 1, 'data' => $res, 'total' => $count, 'page_count' => ceil($count / $pagesize)));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据'));
        }
    }

    /**
     *机构消费统计
     */
    public function merchantConsumptionDetail()
    {
        $merchant_id = I('merchant_id', 0, 'intval');
        $start_time = I('start_time');
        $end_time = I('end_time');
        $page = I('page', 1, 'intval');
        $pagesize = I('pagesize', 10, 'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page . ',' . $pagesize;
        $expenseDetailModel = D('ExpenseDetail');
        $InstModel = D('Institution');
        $InstStaff = D('InstStaff');
        $count = $expenseDetailModel->get_count($start_time, $end_time, $merchant_id);
        $res = $expenseDetailModel->getList($start_time, $end_time, $merchant_id, $page);
        foreach ($res as $key => $item) {
            $inststaff = $InstStaff->get_inst_id($item['card_ownerid']);
            $res[$key]['institution_id'] = $inststaff[0]['institution_id'];
            $institution = $InstModel->get_inst_by_id($res[$key]['institution_id']);
            $res[$key]['institution_name'] = $institution[0]['name'];
        }
        if ($res) {
            Ret(array('code' => 1, 'data' => $res, 'total' => $count, 'page_count' => ceil($count / $pagesize)));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据'));
        }
    }

    /**
     * 商户收入明细
     */
    public function merchantIncomeDetail()
    {
        $start_time = I('start_time');
        $end_time = I('end_time');
        $merchant_id = I('merchant_id', 0, 'intval');
        $page = I('page', 1, 'intval');
        $pagesize = I('pagesize', 10, 'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page . ',' . $pagesize;
        $institutionsMallRevenueModel = D('InstitutionsMallRevenue');
        $count = $institutionsMallRevenueModel->get_count($start_time, $end_time, $merchant_id);
        $res = $institutionsMallRevenueModel->getList($start_time, $end_time, $merchant_id, $page);
        if ($res) {
            Ret(array('code' => 1, 'data' => $res, 'total' => $count, 'page_count' => ceil($count / $pagesize)));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据'));
        }
    }

    /**
     * 导出数据
     */
    public function exportData()
    {
        before_api();
        checkLogin();
        checkAuth();

        $type = I("type");
        if (empty($type)) {
            Ret(array('code' => 2, 'info' => '没有数据'));
        }

        switch ($type) {
            case "memberRecharge":
                $data = $this->memberDetailExportData();
                $fileName = "1.csv";
                break;
            case "memberConsumption":
                $data = $this->memberConsumptionExportData();
                $fileName = "会员消费明细.csv";
                break;
            case "memberReturnDetail":
                $data = $this->memberReturnDetailExportData();
                $fileName = "会员反还明细.csv";
                break;
            case "instConsumptionDetail":
                $data = $this->instConsumptionDetailExportData();
                $fileName = "机构消费明细.csv";
                break;
            case "instIncomeDetail":
                $data = $this->instIncomeDetailExportData();
                $fileName = "机构收入明细.csv";
                break;
            case "attendance":
                $data = $this->attendanceExportData();
                $fileName = "考勤明细.csv";
                break;
            default:
                Ret(array('code' => 2, 'info' => '没有数据'));
        }

        $this->getExportData($data, $fileName);
    }

    /**
     * @desc 导出数据
     * @param $data
     * @param $file_name
     */
    private function getExportData($data, $file_name)
    {
        header('Content-Disposition: attachment; filename=' . $file_name);
        header('Content-type: text/csv; charset=UTF-8');
        $file = fopen("php://output", 'w');
        foreach ($data as $content) {
            fputcsv($file, $content);
        }
        fclose($file);
    }

    /****
     * @desc 会员充值明细导出数据
     * @return array
     */
    private function memberDetailExportData()
    {
        $res = D('RechargeDetail')->order('id desc')->select();
        $data = [["姓名", "充值卡号", "充值卡类别", "充值时间", "消费金额", "操作人"]];
        $data = [];
        foreach ($res as $key => $value) {
            $tmp['member_name'] = $value['card_owner_name'];
            $tmp['card_number'] = $value['card_number'];
            $tmp['card_type'] = $value['card_type'];
            $tmp['recharge_time'] = $value['recharge_time'];
            $tmp['recharge_num'] = $value['recharge_num'];
            $tmp['submitter'] = $value['submitter'];
            array_push($data, $tmp);
        }
        return $data;
    }


    /**
     * @desc 会员消费明细导出数据
     * @return array
     */
    private function memberConsumptionExportData()
    {
        $res = D('ExpenseDetail')->order('id desc')->select();
        $data = [["姓名", "卡号", "消费时间", "消费主体类别", "消费主体名称", "消费类别", "消费内容", "数量", "消费金额", "提交人"]];
        foreach ($res as $key => $value) {
            $tmp['member_name'] = $value['card_owner_name'];
            $tmp['card_number'] = $value['card_number'];
            $tmp['card_rechargetime'] = $value['card_rechargetime'];
            $tmp['expense_owner_type'] = $value['expense_owner_type'];
            $tmp['expense_owner_name'] = $value['expense_owner_name'];
            $tmp['cost_content_name'] = $value['cost_content_name'];
            $tmp['cost_type'] = $value['cost_type'];
            $tmp['number'] = $value['number'];
            $tmp['card_rechargenum'] = $value['card_rechargenum'];
            $tmp['submitter'] = $value['submitter'];
            array_push($data, $tmp);
        }
        return $data;
    }

    /**
     * 会员返还明细导出数据
     */
    private function memberReturnDetailExportData()
    {
        $res = D('RechargeDetail')->order('id desc')->select();
        $data = [["姓名", "反还时间", "反还金额", "卡号"]];
        foreach ($res as $key => $value) {
            $tmp['member_name'] = $value['card_owner_name'];
            $tmp['recharge_num'] = $value['recharge_num'];
            $tmp['recharge_time'] = $value['recharge_time'];
            $tmp['card_number'] = $value['card_number'];
            array_push($data, $tmp);
        }
        return $data;
    }

    /***
     * @desc 机构消费明细导出数据
     */
    private function instConsumptionDetailExportData()
    {
        $res = D('InstOutComeDetail')->order('id desc')->select();
        $data = [["机构名称", "刷卡时间", "消费类别", "消费内容", "消费起止时间", "时限", "消费金额", "提交人"]];
        foreach ($res as $key => $item) {
            $tmp['institution_name'] = $item['institution_name'];
            $tmp['time'] = $item['time'];
            $tmp['cost_type'] = $item['cost_type'];
            $tmp['cost_content_name'] = $item['cost_content_name'];
            $tmp['startEndTime'] = $item['start_time'] . "——" . $item['end_time'];
            $tmp['timeLength'] = $item['time_long'] . $item['time_long_unit'];
            $tmp['money'] = $item['money'];
            $tmp['submitter'] = $item['submitter'];
            array_push($data, $tmp);
        }
        return $data;
    }

    /***
     * @desc 机构收入明细导出数据
     */
    private function instIncomeDetailExportData()
    {
        $res = D('InstitutionsMallRevenue')->order('id desc')->select();
        $data = [["机构名称", "姓名", "卡号", "缴费时间", "收入类别", "收入内容", "数量", "应收金额", "实收金额", "有效期", "提交人"]];
        foreach ($res as $key => $item) {
            $tmp['institutions_name'] = $item['institutions_name'];
            $tmp['income_ownername'] = $item['income_ownername'];
            $tmp['card_number'] = $item['card_number'];
            $tmp['income_time'] = $item['income_time'];
            $tmp['income_type'] = $item['income_type'];
            $tmp['course_card_name'] = $item['course_card_name'];
            $tmp['number'] = $item['number'];
            $tmp['income_receive_num'] = $item['income_receive_num'];
            $tmp['income_num'] = $item['income_num'];
            $res[$key]['startEndTime'] = $item['validation_start_time'] . "——" . $item['validation_end_time'];
            $tmp['submitter'] = $item['submitter'];
            array_push($data, $tmp);
        }
        return $data;
    }

    /**
     * @desc 考勤明细
     * @return array
     */
    public function attendanceExportData()
    {
        $res = M('Attendenc_detail')->order('id desc')->select();
        $data = [["头像", "姓名", "所在地", "部门", "联系电话", "上班时间", "上班考勤", "下班时间", "下班考勤", "备注", "日期"]];
        foreach ($res as $key => $item) {
            $tmp['eployee_pic'] = $item['eployee_pic'];
            $tmp['eployee_name'] = $item['eployee_name'];
            $tmp['eployee_address'] = $item['eployee_address'];
            $tmp['eployee_department'] = $item['eployee_department'];
            $tmp['eployee_phone'] = $item['eployee_phone'];
            $tmp['punch_in'] = $item['thedate'] . " " . $item['punch_in'];
            $tmp['am_remark'] = $item['am_remark'];
            $tmp['punch_out'] = $item['thedate'] . " " . $item['punch_out'];
            $tmp['pm_remark'] = $item['pm_remark'];
            $tmp['remarks'] = $item['remarks'];
            $tmp['thedate'] = $item['thedate'];
            array_push($data, $tmp);
        }
        return $data;
    }

}