<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/7/11
 * Time: 16:27
 */
namespace Inst\Model;
use Think\Model;
class CoursePlanModel extends Model{
    protected $tableName='course_plan';
    public function get_course($insititution){
        if(!$insititution){
            return false;
        }
        $condition['insititution_id']=$insititution;
        return $this->field('course_id')->where($condition)->select();
    }
    public function get_count($institution_id,$start_time,$end_time)
{
    if ($institution_id !== 0) {
        $condition['institution_id'] = $institution_id;
    }
    $condition['state'] = 1;

    if($start_time||$end_time){
        $key_where['end_time'] = array('elt',$end_time);
        $key_where['start_time'] = array('egt',$start_time);
        $key_where['_logic'] = 'AND';
        $condition['_complex'] = $key_where;

    }
    return $this->where($condition)->count();
}


    public function get_list_by_time($institution_id,$start_time,$end_time,$page = '1,10',$fields = null){
        if ($institution_id !== 0) {
            $condition['institution_id'] = $institution_id;
        }
//        $condition['state'] = 1;
        if($start_time||$end_time){
            $key_where['end_time'] = array('elt',$end_time);
            $key_where['start_time'] = array('egt',$start_time);
            $key_where['_logic'] = 'AND';
            $condition['_complex'] = $key_where;
        }
        return $this->field('id,course_id,price,institution_id,start_time,end_time,max_member,room_id')
            ->where($condition)
            ->page($page)
            ->select();
    }

    public function get_Plan($course_id){
        if(!$course_id){
            return false;
        }
        $condition['course_id']=$course_id;
        return $this->field('id,max_member')->where($condition)->select();
    }
    public function add_course_tryout($data){
        return $this->add($data);
    }

}