<?php
/**
 * Created by PhpStorm.
 * User: 44364
 * Date: 2017/10/16
 * Time: 16:58
 */
namespace Member\Model;

use Think\Model;

class RechargeDetailModel extends Model{
    protected $tableName='recharge_detail';
   public function getRechargeDetail($keyword){

       if (!empty($keyword)) {
           if(preg_match("/^\d*$/",$keyword)){
               $condition['card_ownerid'] = $keyword;
           }
       }
        return $this->field('card_type,card_ownerid')->where($condition)->select();
    }
    public function getCount($start_time,$end_time,$keyword){
        $condition['recharge_time'] = array('elt', $end_time);
        $condition['recharge_time'] = array('egt', $start_time);
        if (!empty($keyword)) {
            if(preg_match("/^\d*$/",$keyword)){
                $condition['card_ownerid'] = $keyword;
            }
        }
        $condition['card_type'] = '会员';
        if($condition['card_type']==null){
            return false;
        }

        return $this->where($condition)->count();
    }
    public function getRecharge($start_time,$end_time,$keyword,$page='1,10'){
        $condition['recharge_time'] = array('elt', $end_time);
        $condition['recharge_time'] = array('egt', $start_time);
        if (!empty($keyword)) {
            if(preg_match("/^\d*$/",$keyword)){
                $condition['card_ownerid'] = $keyword;
            }
        }

            $condition['card_type'] = '会员';
        if($condition['card_type']==null){
            return false;
        }
        return $this->where($condition)->field('id,card_type,card_ownerid,recharge_num,recharge_time,recharge_typeid')->page($page)->select();
    }




    public function get_by_count($start_time,$end_time,$keyword){
        $condition['recharge_time'] = array('elt', $end_time);
        $condition['recharge_time'] = array('egt', $start_time);
        if (!empty($keyword)) {
            if(preg_match("/^\d*$/",$keyword)){
                $condition['card_ownerid'] = $keyword;
            }
        }
        $condition['card_type'] = '会员';
        $condition['recharge_typeid'] =array('elt', 4);
        $condition['recharge_typeid']= array('egt', 2);
        return $this->where($condition)->count();
    }
    public function get_by_list($start_time,$end_time,$keyword,$page='1,10'){
        $condition['recharge_time'] = array('elt', $end_time);
        $condition['recharge_time'] = array('egt', $start_time);
        if (!empty($keyword)) {
            if(preg_match("/^\d*$/",$keyword)){
                $condition['card_ownerid'] = $keyword;
            }
        }
        $condition['recharge_typeid'] =array('elt', 4);
        $condition['recharge_typeid']= array('egt', 2);

        return $this->where($condition)->field('id,card_ownerid,recharge_num,recharge_time')->page($page)->select();
    }
}