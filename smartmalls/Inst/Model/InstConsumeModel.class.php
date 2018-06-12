<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-07-15
 * Time: 0:51
 */
namespace Inst\Model;
use Think\Model;

class InstConsumeModel extends Model{
    protected $tableName='inst_consume_detail';
    public function get_inst_list($start_time,$end_time,$merchant_id,$page='1,10',$fields=null){
        if($start_time || $end_time){
            $condition['time']=array('between',array($start_time,$end_time));
        }

        if($merchant_id){
            $condition['merchant_id']=$merchant_id;
        }
        return $this->field($fields)->where($condition)->page($page)->select();
    }
    public function get_inst_count($start_time,$end_time,$merchant_id){
        if($start_time || $end_time){
            $condition['time']=array('between',array($start_time,$end_time));
        }

        if($merchant_id){
            $condition['merchant_id']=$merchant_id;
        }
        return $this->where($condition)->count();
    }

    public function get_statistics($start_time,$end_time,$inst_id){
        if($inst_id){
            if($start_time || $end_time){
                $where['time']=array('between',array($start_time,$end_time));
            }
           $where['inst_id']=$inst_id;
            return $this->where($where)->field('id,inst_name,money')->select();
        }
    }
}