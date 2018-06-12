<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-07-06
 * Time: 4:29
 */
namespace Member\Model;
use Think\Model;

class RoomNumberModel extends Model{
    protected $tableName ='room';
    public function get_info($course_id){
        if(!$course_id){
            return false;
        }
        return $this->field('id,position')->where('id='.$course_id)->select();
    }
}
