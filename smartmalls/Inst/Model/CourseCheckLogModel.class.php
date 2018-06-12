<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/7/22
 * Time: 22:09
 */
namespace Inst\Model;
use Think\Model;
class CourseCheckLogModel extends Model{
    protected $tableName='course_check_log';
    public function insertLog($data){
        return $this->add($data);
    }
    public function add_log($log){
        return $this->add($log);
    }
    public function getCount($keyword){
        if (!empty($keyword)) {
            if(preg_match("/^\d*$/",$keyword)){
                $condition['id'] = $keyword;
            }else{
                $condition['name'] = array('LIKE','%'.$keyword.'%');
            }
        }
        return $this->where($condition)->count();
    }
    public function getList($keyword,$page='1,10'){
        if (!empty($keyword)) {
            if(preg_match("/^\d*$/",$keyword)){
                $condition['id'] = $keyword;
            }else{
                $condition['name'] = array('LIKE','%'.$keyword.'%');
            }
        }
        return $this->field('id,course_name,check_type,submitter,checker,check_state,course_category,cours_id,check_time')->where($condition)->page($page)->select();
    }
}