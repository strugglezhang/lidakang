<?php
/**
 * Created by PhpStorm.
 * User: 44364
 * Date: 2017/10/24
 * Time: 13:29
 */
namespace Member\Model;
use Think\Model;

class InstOutComeDetailModel extends Model{
    protected $tableName='institution_outcome_detail';
    public function getInstRecharge($start_time,$end_time,$institution_id=0,$page='1,10'){

        if(!empty($end_time) && !empty($start_time))
        {
            $condition['card_rechargetime'] = array('elt', $end_time);
            $condition['card_rechargetime'] = array('egt', $start_time);
        }

        if (!empty($institution_id)) {
            if($institution_id){
                $condition['card_ownerid'] = $institution_id;
            }
        }
        /*$condition['card_typeid'] = '3';
        if($condition['card_typeid']==null){
            return false;
        }*/
        return $this->where($condition)->page($page)->select();
    }
    public function getCount($start_time,$end_time,$institution_id){
        if(!empty($end_time) && !empty($start_time))
        {
            $condition['card_rechargetime'] = array('elt', $end_time);
            $condition['card_rechargetime'] = array('egt', $start_time);
        }
        if (!empty($institution_id)) {
            echo('123');
            if($institution_id){
                $condition['card_ownerid'] = $institution_id;
            }
        }
        /*$condition['card_typeid'] = '3';
        if($condition['card_typeid']==null){
            return false;
        }*/
        return $this->where($condition)->count();
    }
}