<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/7/13
 * Time: 9:58
 */
namespace Inst\Model;
use Think\Model;
class ActivityReserveModel extends Model{
    protected $tableName='activity_reserve';
    public function get_count($acitvity_id= 0,$institution_id=0){
            $condition['institution_id'] =$institution_id;
            $condition['id'] =$acitvity_id;
        return $this->where($condition)->count();
    }
    public function get_enroll_list($institution_id){
    $condition['institution_id'] =$institution_id;
    return $this->field('member_id')->where($condition)->select();
}
}