<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/7/19
 * Time: 12:25
 */
namespace AppParents\Model;
use Think\Model;
class CourseBackDetailModel extends Model{
    protected $tableName='member_course_reback_detail';
    public function get_count($mall_id,$time,$member_id){
        if($mall_id){
            $condition['mall_id']=$mall_id;
        }
        if($time){
            $condition['time']=array('elt',$time);
        }
        if($member_id){
            $condition['member_id']=$member_id;
        }
        return $this->where($condition)->count();

    }
    public function get_list($mall_id,$time,$member_id,$page){
        if($mall_id){
            $condition['mall_id']=$mall_id;
        }
        if($time){
            $condition['time']=array('elt',$time);
        }
        if($member_id){
            $condition['member_id']=$member_id;
        }
        return $this->where($condition)->page($page)->select();


    }
}