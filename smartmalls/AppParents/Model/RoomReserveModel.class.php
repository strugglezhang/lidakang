<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/7/12
 * Time: 15:20
 */
namespace AppParents\Model;
use Think\Model;
class RoomReserveModel extends Model {
    protected $tableName = 'room_reserve';
    public function get_dated_list($mall_id = 0 ,$date = 0 ,$cat = 0){
        if(!$mall_id){
            return false;
        }
        $condition['mall_id'] = $mall_id;
        if($date!=0){
            $condition['start_time'] = array( array('egt',$date), array('lt',$date.'+1 day'));
        }

        if($cat!=0){
            $condition['category_id'] = $cat;
        }
        return $this->where($condition)->count();
    }

    public function get_count($mall_id = 0 ,$date = 0 ){
        if(!$mall_id){
            return false;
        }
        $condition['mall_id'] = $mall_id;
        if($date!=0){
            $condition['start_time'] = array( array('egt',$date), array('lt',$date.'+1 day'));
        }
        return $this->where($condition)->field('members_use')->select();
    }

    public function date_classroom($data){
        if($data){
            return $this->add($data);
        }
    }

    public function get_list_by_activityId($room_id){
        if(!$room_id){
            return false;
        }
        $condition['room_id']=$room_id;
//        var_dump($condition);die;
//        $condition['institution_id']=$institution_id;
      return $this->where($condition)->field('room_id')->select();

    }
//    public function get_activityReservceNumber($room_id,$institution_id){
//        if(!$room_id){
//            return false;
//        }
//        $condition['room_id']=$room_id;
//        $condition['institution_id']=$institution_id;
//        return $this->where($condition)->count();
//    }

    public function getList($institution_id=0,$keyword=null,$state=0,$page='1,10',$fields){
        if($institution_id!=0){
            $condition['institution_id']=$institution_id;
        }
        if (!empty($keyword)) {
            if(is_numeric($keyword)){
                $where['course_id'] = $keyword;
            }else{
                $where['course_name'] = $keyword;

            }
        }
        return $this->where($condition)->page($page)->select();
    }
    public function getClassCount($institution_id=0,$keyword=null,$state=0){
        if($institution_id){
            $condition['institution_id']=$institution_id;
        }
        if (!empty($keyword)) {
            if (is_numeric($keyword)) {
                $where['course_id'] = $keyword;
            } else {
                $where['course_name'] = $keyword;

            }
        }
        return $this->where($condition)->count();
    }

    public function delete_room($id){
        if(!$id){
            return false;
        }
        return $this->where('id='.$id)->delete($id);
    }
    public function deleteRoomReserveByRoomId($roomId)
    {
        return $this->where('room_id='.$roomId)->delete();
    }
    public function get_reserve_count($institution_id,$start_time,$end_time)
    {
        if ($institution_id !== 0) {
            $condition['institution_id'] = $institution_id;
        }
        $condition['state'] = 1;

        if($start_time||$end_time){
            $key_where['end_time'] = array('elt',$end_time);
            $key_where['start_time'] = array('egt',$start_time);
            $key_where['_logic'] = 'AND';
            $condition['_complex'] = $key_where;

        }
        return $this->where($condition)->count();
    }
    public function get_list_by_time($institution_id,$start_time,$end_time,$page = '1,10',$fields = null)
    {
        if ($institution_id !== 0) {
            $condition['institution_id'] = $institution_id;
        }
//        $condition['state'] = 1;
        if($start_time||$end_time){
            $key_where['end_time'] = array('elt',$end_time);
            $key_where['start_time'] = array('egt',$start_time);
            $key_where['_logic'] = 'AND';
            $condition['_complex'] = $key_where;
        }
        return $this->field('id,course_id,price,institution_id,start_time,end_time,max_number,room_id')
            ->where($condition)
            ->page($page)
            ->select();
    }
    public function getRoomReserveInfo($institution_id,$course_id,$start_time)
    {
        $condition['institution_id'] = $institution_id;
        $condition['course_id']=$course_id;
        $condition['start_time']=$start_time;
        return $this->where($condition)->find();
    }

    //获取未报名的上课列表数量
    public function getUntryoutRoomReserveNum($memberId,$regiterRoomReserveList,$courseId)
    {
        $condition="id not in $regiterRoomReserveList and course_id=$courseId ";
        //var_dump($condition);die;
        $count=$this->where($condition)->count('distinct id');
        return $count;
    }
    //获取未报名上课列表
    public function getUntryoutRoomReserveList($memberId,$page='1,10',$regiterRoomReserveList,$courseId)
    {
        $condition="id not in $regiterRoomReserveList and course_id=$courseId ";
        //var_dump($condition);die;
        $data=$this->where($condition)->page($page)->select();
        return $data;
    }

    //获取未报名的上课列表
    public function getUnregisterRoomReserveList($memberId,$page='1,10',$regiterRoomReserveList,$buyerCourseList)
    {
        $condition="id not in $regiterRoomReserveList and course_id in $buyerCourseList";
        //var_dump($condition);die;
        $data=$this->where($condition)->page($page)->select();
        return $data;
    }
    //获取未报名的上课列表数量
    public function getUnregisterRoomReserveNum($memberId,$regiterRoomReserveList,$buyerCourseList)
    {

        //var_dump($regiterRoomReserveList);die;
        $condition="id not in $regiterRoomReserveList and course_id in $buyerCourseList";
        //var_dump($condition);die;
        $count=$this->where($condition)->count('distinct id');
        //echo $this->getLastSql();die;
        return $count;
    }
}