<?php
/**
 * Created by PhpStorm.
 * User: 44364
 * Date: 2017/9/24
 * Time: 15:06
 */
namespace AppParents\Model;
use Think\Model;

class CourseCardModel extends Model{
    protected $tableName='course_card';
    public function getCard($id){
        if(!$id){
            return false;
        }
        return $this->field('id,course_id,name,price_typeid,all_count,validity,validity_ttimes,course_price,quantity,gifts')->where('course_id='.$id)->select();
    }
}