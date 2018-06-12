<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/8/31
 * Time: 21:49
 */
namespace Inst\Model;
use Think\Model;
class CourseReserveModel extends Model{
    protected $tableName='course_reserve';
    public function get_courseReservceNumber($course_id,$room_reserve_id){
        if(!$course_id){
            return false;
        }
        $condition['course_id']=$course_id;
        $condition['room_reserve_id']=$room_reserve_id;
        return $this->where($condition)->count();
    }

    public function get_count($member_id)
    {
        if(!$member_id){
            $condition['member_id']=$member_id;
        }
        return $this->where($condition)->count();
    }

    public function get_list_by_activityId($course_id,$room_reserve_id,$page='1,10'){
        if(!$course_id){
            return false;
        }
        $condition['course_id']=$course_id;
        $condition['room_reserve_id']=$room_reserve_id;
        return $this->where($condition)->page($page)->select();
    }

    public function get_list_by_couserIdAndMemberId($member_id,$course_id,$page='1,10')
    {
        if(!$member_id || !$course_id )
        {
            return false;
        }
        $condition['member_id']=$member_id;
        $condition['course_id']=$course_id;
        return $this->where($condition)->page($page)->select();

    }
    public function get_courseReservce($course_id){
        if(!$course_id){
            return false;
        }
        $condition['course_id']=$course_id;
        return $this->where($condition)->count();
    }
    public function get_courseReservceByRoom($course_id,$roomReserveId)
    {

        $condition['course_id']=$course_id;
        $condition['room_reserve_id']=$roomReserveId;
        return $this->where($condition)->count();
    }
    public function get_courseReservceID($course_id){
        if(!$course_id){
            return false;
        }
        $condition['course_id']=$course_id;
        return $this->field('course_id')->where($condition)->select();
    }
    public function hasRepeatCourseReserve($member_id,$course_id,$room_reserve_id)
    {
        $condition['member_id']=$member_id;
        $condition['course_id']=$course_id;
        $condition['room_reserve_id']=$room_reserve_id;
        $info=$this->where($condition)->find();
        if(empty($info))
        {
            return false;
        }
        else
        {
            return true;
        }
    }
}