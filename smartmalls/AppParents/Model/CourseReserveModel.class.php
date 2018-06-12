<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-07-06
 * Time: 1:00
 */
namespace AppParents\Model;
use Think\Model;
class CourseReserveModel extends Model{
    protected $tableName ='course_reserve';
    public function get_list($member_id,$page='1,10',$fields = null,$isTryout){
        if(!$member_id){
            return false;
        }
        $condition['member_id']=$member_id;
        if($isTryout==true)
        {
            $condition['member_type']=2;
        }
        else
        {
            $condition['member_type']=1;
        }
        return $this->field('id,member_id,course_id,institution_id,time,card_id,used_times,total_degree')->where($condition)->page($page)->select();
    }
    public function get_count($member_id,$isTryout){
        if (!$member_id) {
            return false;
        }
        $condition['member_id']=$member_id;
        if($isTryout==true)
        {
            $condition['member_type']=2;
        }
        else
        {
            $condition['member_type']=1;
        }
        return $this->where($condition)->count();

    }
    public function get_tryout_list($member_id,$page='1,10',$courseId)
    {
        if (!$member_id) {
            return false;
        }
        $condition['member_id']=$member_id;
        $condition['member_type']=2;
        $condition['course_id']=$courseId;
        return $this->where($condition)->page($page)->select();
       // echo $this->getLastSql();die;
    }
    public function get_tryout_count($member_id,$courseId){
        if (!$member_id) {
            return false;
        }
        $condition['member_id']=$member_id;
        $condition['member_type']=2;
        $condition['course_id']=$courseId;
        return $this->where($condition)->count();
        //echo $this->getLastSql();

    }
    public function get_reserve_info($member_id){
        $where['member_id']=$member_id;
        return $this->where($where)->count();
    }

    public function get_courseReservceNumber($course_id){
        if(!$course_id){
            return false;
        }
        $condition['course_id']=$course_id;
        return $this->where($condition)->count();
    }

    public function deleteReservce($id){
        if(!$id){
            return false;
        }
        return $this->where('id='.$id)->delete();
    }
    public function getRegisterRoomReserveList($memberId)
    {
        $condition="member_id=$memberId and room_reserve_id is not null";
        $regiterRoomReserveList=$this->field('room_reserve_id')->where($condition)->group('room_reserve_id')->buildSql();
        return $regiterRoomReserveList;
    }
    public function getTryoutRoomReserveList($memberId,$courseId)
    {
        $condition['member_id']=$memberId;
        $condition['course_id']=$courseId;
        $regiterRoomReserveList=$this->field('room_reserve_id')->where($condition)->group('room_reserve_id')->buildSql();
        return $regiterRoomReserveList;
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