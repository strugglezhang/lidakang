<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-07-15
 * Time: 0:48
 */
namespace Merchant\Controller;

class IncomeController extends CommonController{
    /**
     * 商户收入明细
     */
    public function income_view_api(){
        before_api();
        checkLogin();
        checkAuth();
        $merchant_id = I('merchant_id');
        $page = I('page',1,'intval');
        $pagesize = I('pagesize',10,'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page.','.$pagesize;
        $merchantIncomeModel = D('MerchantIncome');
        $count =$merchantIncomeModel->get_income_count($merchant_id);
        $data = $merchantIncomeModel->get_income_list($merchant_id,$page,$fields=null);
        if($data){
            Ret(array('code'=>1,'data'=>$data,'total'=>$count,'page_count'=>ceil($count/$pagesize)));
        }else{
            Ret(array('code'=>2,'info'=>'没有数据！'));
        }
    }
}