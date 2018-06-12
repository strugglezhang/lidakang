<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-07-07
 * Time: 7:58
 */
namespace AppParents\Model;
use Think\Model;

class InstStaffModel extends Model{
    protected $tableName='institution_staff';
    public function get_info($institution_id){
        if(!$institution_id){
            return false;
        }
        return $this->field('id,name,remarks,pic')->where('institution_id='.$institution_id)->select();
    }
    public function get_count($institution_id){
        $where['institution_id']=$institution_id;
        return $this->where($where)->count();
    }
    public function get_course_list($course_id){
        if(!$course_id){
            return false;
        }
        return $this->field('id,name,pic,remarks')->where('course_id='.$course_id)->select();
    }
    public function get_course_count($course_id){
        $where['course_id']=$course_id;
        return $this->where($where)->count();
    }

}