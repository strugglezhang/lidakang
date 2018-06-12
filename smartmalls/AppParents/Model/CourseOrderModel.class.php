<?php
/**
 * Created by PhpStorm.
 * User: 44364
 * Date: 2017/10/10
 * Time: 14:24
 */
namespace AppParents\Model;
use Think\Model;

class CourseOrderModel extends Model{
    protected $tableName ='course_order';
    public function get_list($member_id,$page='1,10',$fields = null){
        if(!$member_id){
            return false;
        }
        return $this->field('id,member_id,course_id,institution_id,time,card_id,used_times,total_degree')->where('member_id='.$member_id)->page($page)->select();
    }
    public function get_count($member_id){
        if (!$member_id) {
            return false;
        }
        return $this->where('member_id='.$member_id)->count();

    }

    public function get_reserve_info($member_id){
        $where['member_id']=$member_id;
        return $this->where($where)->count();
    }



    public function getList($member_id,$time,$page='1,10',$fields = null){
        $where['member_id']=$member_id;
        if($time){
            $where['time']= array('LIKE', '%' . $time . '%');
            $key_where['_logic'] = 'AND';
            $condition['_complex'] = $key_where;
        }

      return $this->field('id,member_id,course_id,institution_id,time,card_id,used_times,total_degree,time')
            ->where($where)
            ->page($page)->select();
    }
    public function getCount($member_id,$time){
        $where['member_id']=$member_id;
        if($time){
            $where['time']= array('LIKE', '%' . $time . '%');
            $key_where['_logic'] = 'AND';
            $condition['_complex'] = $key_where;
        }
        return $this->where($where)->count();

    }
}