<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-07-14
 * Time: 23:37
 */
namespace Inst\Model;
use Think\Model;

class InstitutionIncomeModel extends Model{
    protected $tableName='institution_income_detail';
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
//        var_dump($condition);die();
        return $this->field('id,institution_id,member_name,institution_name,time,category,content,amont,money')->where($condition)->page($page)->select();
    }
    public function get_inst_count($institution_id){
        if($institution_id){
            $condition['institution_id']=$institution_id;
        }
        return $this->where($condition)->count();
    }
}