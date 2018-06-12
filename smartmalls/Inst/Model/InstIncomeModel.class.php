<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-07-15
 * Time: 0:50
 */
namespace Inst\Model;
use Think\Model;

class InstIncomeModel extends Model{
    protected $tableName='inst_income_detail';
    public function get_income_list($merchant_id,$start_time,$end_time,$page='1,10',$fields=null){
        if($start_time){
            $condition['time']=array('egt',$start_time);
        }
        if($end_time){
            $condition['time']=array('egt',$end_time);
        }
        if($merchant_id){
            $condition['merchant_id']=$merchant_id;
        }

        return $this->field('id,merchant_id,merchant_name,member_id,member_name,time,category_id,category_name,content,amont,money')->where($condition)->page($page)->select();
    }
    public function get_income_count($merchant_id){
        if($merchant_id){
            $condition['merchant_id']=$merchant_id;
        }
        return $this->where($condition)->count();
    }
    public function get_statistics($start_time,$end_time,$inst_id){
        if($inst_id){
            if($start_time||$end_time){
                $where['time']=array('between',array($start_time,$end_time));
            }
            $where['inst_id']=$inst_id;
            return $this->where($where)->field('id,inst_name,money')->select();
        }
    }
}