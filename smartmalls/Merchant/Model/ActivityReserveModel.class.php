<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-07-12
 * Time: 23:37
 */
namespace Merchant\Model;
use Think\Model;

class ActivityReserveModel extends Model{
    protected $tableName='activity_reserve';
    public function add_activity_reserve($data){
        return $this->add($data);
    }

    public function get_activityReservceNumber($activity_id){
        if(!$activity_id){
            return false;
        }
        $condition['activity_id']=$activity_id;
        return $this->where($condition)->count();
    }

    public function get_count($member_id)
    {
       if(!$member_id){
           $condition['member_id']=$member_id;
       }
        return $this->where($condition)->count();
    }

    public function get_list_by_activityId($activity_id,$page='1,10'){
        if(!$activity_id){
            return false;
        }
        $condition['activity_id']=$activity_id;
        return $this->where($condition)->page($page)->select();
    }


}