<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-07-12
 * Time: 23:37
 */
namespace AppParents\Model;
use Think\Model;

class ActivityReserveModel extends Model{
    protected $tableName='activity_reserve';
    public function get_activity_info($member_id){
        $where['member_id']=$member_id;
        return $this->where($where)->count();
    }
    public function get_app_list($member_id,$keyword , $page = '1,10',$fields = null){
       if(!$member_id){
           return false;
       }
        if (!empty($keyword)) {
            if(preg_match("/^\d*$/",$keyword)){
                $key_where['id'] = $keyword;
            }else{
                $key_where['id'] = $keyword;
                $key_where['name'] = array('LIKE','%'.$keyword.'%');
                $key_where['_logic'] = 'OR';
                $condition['_complex'] = $key_where;
            }
        }

        return $this->field('id,activity_id,institution_id')->where('member_id='.$member_id)->page($page)->select();
    }

    public function add_activity_reserve($data){
        return $this->add($data);
    }
}