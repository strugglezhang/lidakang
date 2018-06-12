<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-07-05
 * Time: 3:59
 */
namespace Member\Model;
use Think\Model;
class CourseTryoutModel extends Model{
    protected $tableName ='course_tryout';
    public function get_list($member_id,$page='1,10',$fields = null){
        if(!$member_id){
            return false;
        }
        return $this->field('id,member_id,course_id,institution_id,time')->where('member_id='.$member_id)->page($page)->select();
    }
    public function get_count($member_id){
        if (!$member_id) {
            return false;
        }
        return $this->where('member_id='.$member_id)->count();

    }
    public function get_count_info($member_id){
        $where['member_id']=$member_id;
        return $this->where($where)->count();
    }
}