<?php
/**
 * Created by PhpStorm.
 * User: 44364
 * Date: 2017/10/16
 * Time: 17:47
 */
namespace Member\Model;
use Think\Model;

class ExpenseDetailModel extends Model{
    protected $tableName='expense_detail';
    public function getInstRecharge($start_time,$end_time,$institution_id=0,$page='1,10'){

    $condition['card_rechargetime'] = array('elt', $end_time);
    $condition['card_rechargetime'] = array('egt', $start_time);
        if (!empty($institution_id)) {
            if($institution_id){
                $condition['card_ownerid'] = $institution_id;
            }
        }
    $condition['card_typeid'] = '3';
    if($condition['card_typeid']==null){
        return false;
    }
    return $this->where($condition)->field('id,card_ownerid,cost_type,card_rechargenum,card_rechargetime')->page($page)->select();
}
    public function getCount($start_time,$end_time,$institution_id){
        $condition['card_rechargetime'] = array('elt', $end_time);
        $condition['card_rechargetime'] = array('egt', $start_time);
        if (!empty($institution_id)) {
            if($institution_id){
                $condition['card_ownerid'] = $institution_id;
            }
        }
        $condition['card_typeid'] = '3';
        if($condition['card_typeid']==null){
            return false;
        }
        return $this->where($condition)->count();
    }

    public function getList($start_time,$end_time,$merchant_id=0,$page='1,10'){
        $condition['card_rechargetime'] = array('elt', $end_time);
        $condition['card_rechargetime'] = array('egt', $start_time);
        if (!empty($merchant_id)) {
            if($merchant_id){
                $condition['card_ownerid'] = $merchant_id;
            }
        }
        $condition['card_typeid'] = '4';
        if($condition['card_typeid']==null){
            return false;
        }
        return $this->where($condition)->field('id,card_ownerid,cost_type,card_rechargenum,card_rechargetime')->page($page)->select();
    }
    public function get_count($start_time,$end_time,$merchant_id){
        $condition['card_rechargetime'] = array('elt', $end_time);
        $condition['card_rechargetime'] = array('egt', $start_time);
        if (!empty($merchant_id)) {
            if($merchant_id){
                $condition['card_ownerid'] = $merchant_id;
            }
        }
        $condition['card_typeid'] = '4';
        if($condition['card_typeid']==null){
            return false;
        }
        return $this->where($condition)->count();
    }

    public function get_by_count($start_time,$end_time,$keyword){
        $condition['card_rechargetime'] = array('elt', $end_time);
        $condition['card_rechargetime'] = array('egt', $start_time);
        if (!empty($keyword)) {
            if(preg_match("/^\d*$/",$keyword)){
                $condition['card_ownerid'] = $keyword;
            }
        }
       /* $condition['card_typeid'] = '1';
        if($condition['card_typeid']==null){
            return false;
        }*/
        return $this->where($condition)->count();
    }
    public function get_by_list($start_time,$end_time,$keyword,$page='1,10'){

        $condition['card_rechargetime'] = array('elt', $end_time);
        $condition['card_rechargetime'] = array('egt', $start_time);
        if (!empty($keyword)) {
            if(preg_match("/^\d*$/",$keyword)){
                $condition['card_ownerid'] = $keyword;
            }
        }
        /*$condition['card_typeid'] = '1';
        if($condition['card_typeid']==null){
            return false;
        }*/
        return $this->where($condition)->page($page)->select();
    }

   public function getExpenseDetail($card_ownerid,$page='1,10'){
        $condition['card_ownerid'] = $card_ownerid;
        $condition['card_typeid'] = 1;
        return $this->where($condition)->field('id,card_rechargenum,card_rechargetime,cost_type')->page($page)->select();

    }
    public function getCoun($card_ownerid){
        $condition['card_ownerid'] = $card_ownerid;
        $condition['card_typeid'] = 1;
        return $this->where($condition)->count();
    }
}