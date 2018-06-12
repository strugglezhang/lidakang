<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-07-14
 * Time: 5:59
 */
namespace Member\Model;
use Think\Model;

class MemberRebackDetailModel extends Model{
    protected $tableName='member_course_reback_detail';
    public function get_member_reback($time,$member_id,$member_name,$page='1,10'){
        if($time){
            $condition['time']=$time;
        }
        if($member_id){
            $condition['member_id']=$member_id;
        }
        if($member_name){
            $condition['member_name']=$member_name;
        }
        return $this->field('id,time,member_id,member_name,money')->where($condition)->page($page)->select();
    }
    public function get_reback_count($member_id){
        if(!$member_id){
            return false;
        }
        return $this->where('member_id='.$member_id)->count();
    }
}