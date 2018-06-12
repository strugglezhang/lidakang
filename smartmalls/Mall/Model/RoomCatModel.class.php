<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/6/26
 * Time: 16:30
 */
namespace Mall\Model;
use Think\Model;
class RoomCatModel extends Model
{
    protected $tableName = 'room_category';
    public function get_roomcat($id){
        return $this->field('category_id')->where('room_id='.$id)->select();
    }
}