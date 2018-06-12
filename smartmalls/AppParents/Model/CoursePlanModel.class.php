<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-07-05
 * Time: 5:03
 */
namespace AppParents\Model;
use Think\Model;

class CoursePlanModel extends Model{
    protected $tableName ='course_plan';
    public function get_info($course_id){
        if(!$course_id){
            return false;
        }
       return $this->field('id,start_time,end_time,room_id')->where('course_id='.$course_id)->select();
    }
    public function  get_info_by_id($course_id){
        if(!$course_id){
            return false;
        }
        return $this->field('id,start_time,room_number')->where('course_id='.$course_id)->select();
    }
    public function get_count_info($id){
        $where['id']=$id;
        return $this->where($where)->count();
    }



    public function get_count($member_id)
    {
        if($member_id){
            $where['member_id']=$member_id;
        }
        return $this->where($where)->count();
    }

    public function get_list_by_time($member_id,$page = '1,10',$fields = null){
        if($member_id){
            $where['member_id']=$member_id;
        }
        return $this->field('id,course_id,institution_id,start_time,end_time,max_member,room_id,member_id')
            ->where($where)->page($page)
            ->select();
//        echo $this->_sql();
    }


}