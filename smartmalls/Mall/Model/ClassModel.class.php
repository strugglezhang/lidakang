<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/6/1
 * Time: 17:33
 */
namespace Common\Model;

use Think\Model;

class ClassModel extends Model
{
    protected $trueTableName = 'room';

    public function class_add($data){
        return $this->add($data);
    }

    public function get_room($room_id){
        if(!$room_id){
            return false;
        }
        $map =array('id'=>$room_id);
        return $this->field('position')->where($map)->select();
    }
    public function get_list($mall_id){
        return $this->field('id,position')->where(array('mall_id' => $mall_id))->select();
    }
//    public function get_room($room_id){
//        if(!$room_id){
//            $where['room_id']=$room_id;
//        }
//        return $this->field('')
//    }

}