<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/7/11
 * Time: 15:18
 */
namespace Inst\Model;
use Think\Model;
class RoomModel extends  Model{
    protected $tableName='room';
    public function get_room_by_id($id){
        if(!$id){
            return false;
        }
        return $this->where('id='.$id)->select();
    }
}