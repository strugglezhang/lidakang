<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-07-14
 * Time: 7:36
 */
namespace Inst\Model;
use Think\Model;

class InstitutionOutcomeModel extends Model{
    protected $tableName='institution_outcome_detail';
    public function get_inst_reback($institution_id,$start_time,$end_time,$page='1,10',$fields=null){
        if($start_time){
            $condition['time']=array('egt',$start_time);
        }
        if($end_time){
            $condition['time']=array('egt',$end_time);
        }
        if($institution_id){
            $condition['institution_id']=$institution_id;
        }

        return $this->field('id,institution_name,time,category,content,start_time,end_time,time_long,money,classroom_pos')->where($condition)->page($page)->select();
    }
    public function get_inst_count($institution_id){
        if($institution_id){
            $condition['institution_id']=$institution_id;
        }
        return $this->where($condition)->count();
    }
}