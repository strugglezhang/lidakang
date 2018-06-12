<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/8/22
 * Time: 18:12
 */
namespace Inst\Model;
use Think\Model;
class CourseBuyDetailModel extends Model{
    protected $tableName = 'course_buy_detail';
    //获取未报名的上课列表
    public function getUnregisterRoomReserveList($memberId,$page='1,10',$regiterRoomReserveList)
    {
        $condition="course_buy_detail.buyer_id=$memberId and room_reserve.id not in $regiterRoomReserveList";
        $data=$this->join('room_reserve on room_reserve.course_id=course_buy_detail.course_id')
            ->field('room_reserve.id,room_reserve.room_id,room_reserve.institution_id,room_reserve.start_time,room_reserve.end_time, room_reserve.max_number')
            ->where($condition)
            ->page($page)
            ->select();
        echo $this->getLastSql();die;
        return $data;
    }
    //获取未报名的上课列表数量
    public function getUnregisterRoomReserveNum($memberId,$regiterRoomReserveList)
    {

        //var_dump($regiterRoomReserveList);die;
        $condition="course_buy_detail.buyer_id=$memberId and room_reserve.id not in $regiterRoomReserveList";
        //var_dump($condition);die;
        $count=$this->join('course_buy_detail on room_reserve.course_id=course_buy_detail.course_id')->where($condition)->count('distinct room_reserve.id');
        //echo $this->getLastSql();die;
        return $count;
    }
    public function getEoor()
    {
        return 1111;
    }
}