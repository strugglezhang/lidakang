<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-07-05
 * Time: 5:03
 */
namespace Member\Model;
use Think\Model;

class CoursePlanModel extends Model{
    protected $tableName ='course_plan';
    public function get_info($course_id){
        if(!$course_id){
            return false;
        }
       return $this->field('id,start_time,end_time,room_id')->where('course_id='.$course_id)->select();
    }
    public function  get_info_by_id($room_id){
        if(!$room_id){
            return false;
        }
        return $this->field('start_time')->where('room_id='.$room_id)->select();
    }
    public function get_count_info($id){
        $where['id']=$id;
        return $this->where($where)->count();
    }
}