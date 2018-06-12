<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-07-06
 * Time: 4:13
 */
namespace Member\Model;
use Think\Model;

class RoomReserveModel extends Model{
    protected $tableName ='room_reserve';
    public function get_info($id){
        if(!$id){
            return false;
        }
        return $this->field('id,room_id,start_time,end_time')->where('id='.$id)->select();
    }
}