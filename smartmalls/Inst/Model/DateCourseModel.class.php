<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/7/10
 * Time: 16:24
 */
namespace Inst\Model;
use Think\Model;
class DateCourseModel extends Model{
    protected $tableName='course_reserve';

    public function get_list_by_time($data){
        if(!$data){
            return false;
        }
        return $this->add($data);
    }

    public function getMemberByMember($course_id){
        if(!$course_id){
            return false;
        }
        return $this->field('member_id')->where('course_id='.$course_id)->select();
    }
}