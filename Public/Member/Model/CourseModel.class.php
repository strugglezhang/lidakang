<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-07-05
 * Time: 4:45
 */
namespace Member\Model;
use Think\Model;

class CourseModel extends Model{
    protected $tableName='course';
    public function get_info($course_id){
        if(!$course_id){
            return false;
        }
        return $this->field('name,pic')->where('id='.$course_id)->select();
    }
}