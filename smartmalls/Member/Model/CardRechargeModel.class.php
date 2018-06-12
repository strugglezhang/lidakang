<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/6/14
 * Time: 11:14
 */

namespace Member\Model;
use Think\Model;
class CardRechargeModel extends Model{
    protected $tableName='card_recharge';
    public function get_recharge_count(){

        return $this->count();
    }
    public function get_recharge_list($mall_id,$page){
        if(!$mall_id){
            return false;
        }
        return $this->field('id,member_id,time,recharge_monney')->page($page)->select();
    }

    public function get_recharge_by_keyword($keyword){
        $condition['member_id']=$keyword['member_id'];
        $condition['time']= array('egt',$keyword['begintime']);
        $condition['time']= array('elt',$keyword['endtime'],'AND');
        return $this->field('id,member_id,time,recharge_monney')->where($condition)->select();

    }


    public function make_charge($data){
        if($data){
            return $this->add($data);
        }

    }

    public function get_balance($member_id){
        if($member_id){
            $condition['member_id']=$member_id;
            return $this->where($condition)->field('balance')->select();
        }
    }
}