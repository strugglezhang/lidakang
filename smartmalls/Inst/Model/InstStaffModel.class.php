<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-07-07
 * Time: 7:58
 */
namespace Inst\Model;
use Think\Model;

class InstStaffModel extends Model{
    protected $tableName='institution_staff';
    public function get_info($institution_id){
        $condition['institution_id']=$institution_id;
        return $this->field('name,imgs,remarks')->where($condition)->select();
    }
    public function get_count($institution_id){
        $where['institution_id']=$institution_id;
        return $this->where($where)->count();
    }
    public function get_course_list($course_id){
        if(!$course_id){
            return false;
        }
        return $this->field('id,name,imgs,remarks')->where('course_id='.$course_id)->select();
    }
    public function get_course_count($course_id){
        $where['course_id']=$course_id;
        return $this->where($where)->count();
    }

    public function get_info_by_id($staffId)
    {
        return $this->where('id='.$staffId)->find();
    }

}