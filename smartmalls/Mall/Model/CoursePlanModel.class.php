<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/4
 * Time: 13:23
 */
namespace Mall\Model;
use Think\Model;
class CoursePlanModel extends Model{
    protected $tableName = 'course_plan';
    public function getCount($institution_id=0,$keyword=null,$state=0){
        if($institution_id){
            $condition['institution_id']=$institution_id;
        }
        if (!empty($keyword)) {
            if (is_numeric($keyword)) {
                $where['course_id'] = $keyword;
            } else {
                $where['course_name'] = $keyword;

            }
        }
        return $this->where($condition)->count();
    }
    public function getList($institution_id=0,$keyword=null,$state=0,$page='1,10',$fields){
        if($institution_id!=0){
            $condition['institution_id']=$institution_id;
        }
        if (!empty($keyword)) {
            if(is_numeric($keyword)){
                $where['course_id'] = $keyword;
            }else{
                $where['course_name'] = $keyword;

            }
        }
        return $this->field($fields)->where($condition)->page($page)->select();
    }
    public function deleteCoursePlanByRoomId($roomId)
    {
        return $this->where('room_id='.$roomId)->delete();
    }
}