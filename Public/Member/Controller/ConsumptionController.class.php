<?php
/**
 * Created by PhpStorm.
 * User: 44364
 * Date: 2017/10/13
 * Time: 17:51
 */

namespace Member\Controller;
class ConsumptionController extends CommonController{
    /**
     * 会员充值明细
     */
    public function memberDetail(){
        $keyword =I('keyword');
        $start_time = I('start_time');
        $end_time =I('end_time');
        $page = I('page',1,'intval');
        $pagesize = I('pagesize',10,'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page.','.$pagesize;
        $memberModel = D('Member');
        $rechargeDetailModel = D('RechargeDetail');
            $count =$rechargeDetailModel->getCount($start_time,$end_time,$keyword);
            $res=$rechargeDetailModel->getRecharge($start_time,$end_time,$keyword,$page);
            foreach($res as $key=>$value){
                $member = $memberModel->getNumberInfo($value['card_ownerid']);
                $res[$key]['member_name'] = $member[0]['name'];
            }
        if($res==null){
            Ret(array('code'=>2,'info'=>'没有数据'));
        }
        if($res){
                Ret(array('code'=>1,'data'=>$res,'total'=>$count,'page_count' => ceil($count/$pagesize)));
            }else{
                Ret(array('code'=>2,'info'=>'没有数据'));
            }



    }
    /**
     * 会员消费明细
     */
    public function memberConsumption(){
        $keyword =I('keyword');
        $start_time = I('start_time');
        $end_time =I('end_time');
        $page = I('page',1,'intval');
        $pagesize = I('pagesize',10,'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page.','.$pagesize;
        $memberModel = D('Member');
        $expenseDetailModel = D('ExpenseDetail');
        $count =$expenseDetailModel->get_by_count($start_time,$end_time,$keyword);
        $res=$expenseDetailModel->get_by_list($start_time,$end_time,$keyword,$page);
        foreach($res as $key=>$value){
            $member = $memberModel->getNumberInfo($value['card_ownerid']);
            $res[$key]['member_name'] = $member[0]['name'];
        }
        if($res){
            Ret(array('code'=>1,'data'=>$res,'total'=>$count,'page_count' => ceil($count/$pagesize)));
        }else{
            Ret(array('code'=>2,'info'=>'没有数据'));
        }

    }
    /**
     * 会员消费统计
     */
    public function memberDetailStatistics(){

    }
    /**
     * 会员返还统计
     */
    public function memberReturn(){

    }
    /**
     * 会员返还明细
     */
    public function memberReturnDetail(){
        $keyword =I('keyword');
        $start_time = I('start_time');
        $end_time =I('end_time');
        $page = I('page',1,'intval');
        $pagesize = I('pagesize',10,'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page.','.$pagesize;
        $memberModel = D('Member');
        $rechargeDetailModel=D('RechargeDetail');
        $count =$rechargeDetailModel->get_by_count($start_time,$end_time,$keyword);
        $res=$rechargeDetailModel->get_by_list($start_time,$end_time,$keyword,$page);
        foreach($res as $key=>$value){
            $member = $memberModel->getNumberInfo($value['card_ownerid']);
            $res[$key]['member_name'] = $member[0]['name'];
        }
        if($res){
            Ret(array('code'=>1,'data'=>$res,'total'=>$count,'page_count' => ceil($count/$pagesize)));
        }else{
            Ret(array('code'=>2,'info'=>'没有数据'));
        }
    }
    /**
     * 机构消费明细
     */
    public function instConsumptionDetail(){
        $start_time = I('start_time');
        $end_time =I('end_time');
        $institution_id =I('institution_id',0,'intval');
        $page = I('page',1,'intval');
        $pagesize = I('pagesize',10,'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page.','.$pagesize;
        $expenseDetailModel = D('ExpenseDetail');
        $InstModel=D('Institution');
        $InstStaff=D('InstStaff');
        $count =$expenseDetailModel->getCount($start_time,$end_time,$institution_id);
        $res=$expenseDetailModel->getInstRecharge($start_time,$end_time,$institution_id,$page);
        foreach ($res as $key=>$item) {
            $inststaff = $InstStaff->get_inst_id($item['card_ownerid']);
            $res[$key]['institution_id'] = $inststaff[0]['institution_id'];
            $institution = $InstModel->get_inst_by_id($res[$key]['institution_id']);
            $res[$key]['institution_name'] = $institution[0]['name'];
    }
    if($res){
        Ret(array('code'=>1,'data'=>$res,'total'=>$count,'page_count' => ceil($count/$pagesize)));
    }else{
        Ret(array('code'=>2,'info'=>'没有数据'));
    }

}
    /**
     * 机构收入明细
     */
    public function instIncomeDetail(){
        $start_time = I('start_time');
        $end_time =I('end_time');
        $institution_id =I('institution_id','0','intval');
        $page = I('page',1,'intval');
        $pagesize = I('pagesize',10,'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page.','.$pagesize;
        $institutionsMallRevenueModel = D('InstitutionsMallRevenue');
        $count=$institutionsMallRevenueModel->getCount($start_time,$end_time,$institution_id);
        $res=$institutionsMallRevenueModel->getIncomeRecharge($start_time,$end_time,$institution_id,$page);
        if($res){
            Ret(array('code'=>1,'data'=>$res,'total'=>$count,'page_count' => ceil($count/$pagesize)));
        }else{
            Ret(array('code'=>2,'info'=>'没有数据'));
        }
    }
    /**
     *机构消费统计
     */


    public function merchantConsumptionDetail(){
        $merchant_id =I('merchant_id',0,'intval');
        $start_time = I('start_time');
        $end_time =I('end_time');
        $page = I('page',1,'intval');
        $pagesize = I('pagesize',10,'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page.','.$pagesize;
        $expenseDetailModel = D('ExpenseDetail');
        $InstModel=D('Institution');
        $InstStaff=D('InstStaff');
        $count =$expenseDetailModel->get_count($start_time,$end_time,$merchant_id);
        $res=$expenseDetailModel->getList($start_time,$end_time,$merchant_id,$page);
        foreach ($res as $key=>$item) {
            $inststaff = $InstStaff->get_inst_id($item['card_ownerid']);
            $res[$key]['institution_id'] = $inststaff[0]['institution_id'];
            $institution = $InstModel->get_inst_by_id($res[$key]['institution_id']);
            $res[$key]['institution_name'] = $institution[0]['name'];
        }
        if($res){
            Ret(array('code'=>1,'data'=>$res,'total'=>$count,'page_count' => ceil($count/$pagesize)));
        }else{
            Ret(array('code'=>2,'info'=>'没有数据'));
        }
    }

    public function merchantIncomeDetail(){
        $start_time = I('start_time');
        $end_time =I('end_time');
        $merchant_id =I('merchant_id',0,'intval');
        $page = I('page',1,'intval');
        $pagesize = I('pagesize',10,'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page.','.$pagesize;
        $institutionsMallRevenueModel = D('InstitutionsMallRevenue');
        $count=$institutionsMallRevenueModel->get_count($start_time,$end_time,$merchant_id);
        $res=$institutionsMallRevenueModel->getList($start_time,$end_time,$merchant_id,$page);
        if($res){
            Ret(array('code'=>1,'data'=>$res,'total'=>$count,'page_count' => ceil($count/$pagesize)));
        }else{
            Ret(array('code'=>2,'info'=>'没有数据'));
        }
    }
}