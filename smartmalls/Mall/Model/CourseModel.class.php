<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/9/3
 * Time: 14:20
 */

namespace Mall\Model;
use Think\Model;
class CourseModel extends Model {
    protected $tableName = 'Course';
    public function get_list_by_activityId($course_id){
        //var_dump($course_id);
        if(!$course_id){
            return false;
        }
        $condition['id']=$course_id;
//        var_dump($condition);die;
//        $condition['institution_id']=$institution_id;
        return $this->where($condition)->select();
    }
    public function getCourseNumber($course_id)
    {
        return $this->field('id,name')->where('id='.$course_id)->select();
    }
}