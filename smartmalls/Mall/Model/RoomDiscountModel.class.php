<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/6/26
 * Time: 17:55
 */
namespace Mall\Model;
use Think\Model;
class RoomDiscountModel extends Model{
    protected $tableName='room_discount';
    public function get_discount($room_id){
        if(!$room_id){
            return false;
        }
        return $this->field('start_time,end_time,discount_rate')->where('room_id='.$room_id)->select();
    }
    public function updateState($data)
    {
        return $this->where('id='.$data['id'])->setField('state',$data['state']);
    }
    public function getAllField($id)
    {
        return $this->where('id='.$id)->field('*')->select();
    }
}