<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-07-15
 * Time: 0:48
 */
namespace Merchant\Controller;


class ConsumeController extends CommonController{
    /**
     * 商户消费明细
     */
    public function consume_view_api(){
        before_api();
        checkLogin();
        checkAuth();
        $merchant_id = I('merchant_id',1);
        if(!$merchant_id){
            Ret(array('code'=>2,'info'=>'参数（merchant_id）有误！'));
        }
        $start_time=I('start_time');
        $end_time=I('end_time');
        $page = I('page',1,'intval');
        $pagesize = I('pagesize',10,'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page.','.$pagesize;

        $merchantConsumeModel = D('MerchantConsume');
        $count =$merchantConsumeModel->get_inst_count($start_time,$end_time,$merchant_id);
        $fields=array('id,merchant_id,merchant_name,time,category_id,category_name,content,start_time,end_time,amont,money');
        $data = $merchantConsumeModel->get_inst_list($start_time,$end_time,$merchant_id,$page,$fields);
        if($data){
            Ret(array('code'=>1,'data'=>$data,'total'=>$count,'page_count'=>ceil($count/$pagesize)));
        }else{
            Ret(array('code'=>2,'info'=>'没有数据！'));
        }
    }

    /*
     * 商户消费统计
     */
    public function consume_statistics(){
        before_api();
        checkLogin();
        checkAuth();
        $start_time = I('start_time');
        $end_time = I('end_time');
        $merchant_id = I('merchant_id');
        $consumeModel=D('MerchantConsume');
        $datas=$consumeModel->get_statistics($start_time,$end_time,$merchant_id);
        $data['merchant_name']=$datas[0]['merchant_name'];
        foreach ($datas as $item) {
            $money = $money + $item['money'];
        }
        $data['money']=$money;
        $data['time']=$start_time.'-'.$end_time;
        if($data){
            Ret(array('code'=>1,'data'=>$data));
        }else{
            Ret(array('code'=>2,'info'=>'没有数据！'));
        }
    }

    /*
     * 商户收入统计
     */

    public function income_statistics(){
        before_api();
        checkLogin();
        checkAuth();
        $start_time = I('start_time');
        $end_time = I('end_time');
        $merchant_id = I('merchant_id');
        $consumeModel=D('MerchantIncome');
        $datas=$consumeModel->get_statistics($start_time,$end_time,$merchant_id);
        $data['merchant_name']=$datas[0]['merchant_name'];
        foreach ($datas as $item) {
            $money = $money + $item['money'];
        }
        $data['money']=$money;
        $data['time']=$start_time.'-'.$end_time;
        if($data){
            Ret(array('code'=>1,'data'=>$data));
        }else{
            Ret(array('code'=>2,'info'=>'没有数据！'));
        }
    }
}