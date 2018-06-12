<?php
/**
 * Created by PhpStorm.
 * User: 44364
 * Date: 2017/9/20
 * Time: 11:46
 */
namespace Inst\Model;
use Think\Model;

class CourseCardModel extends Model{
    protected $tableName ='course_card';
    public function get_price($id){
        if(!$id){
            return false;
        }

        return $this->field('course_id,course_price')->where('course_id='.$id)->select();
    }
    public function get_info($course_id){
        if(!$course_id){
            return false;
        }
        return $this->field('course_price,name,price_typeid,validity,validity_ttimes,quantity,gifts')->where('course_id='.$course_id)->select();
    }

    public function getCard($id){
        if(!$id){
            return false;
        }
        return $this->field('id,course_id,course_price,name,price_typeid,validity,validity_ttimes,quantity,gifts,all_count')->where('course_id='.$id)->select();
    }


    public function getPrice($id){
        if(!$id){
            return false;
        }
        return $this->field('price_typeid,course_price')->where('course_id='.$id)->select();
    }

    public function updateCoureCard($data)
    {
        return $this->where("id={$data['id']}")->save($data);
    }
}