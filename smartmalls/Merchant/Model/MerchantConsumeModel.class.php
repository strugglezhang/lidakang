<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-07-15
 * Time: 0:51
 */
namespace Merchant\Model;
use Think\Model;

class MerchantConsumeModel extends Model{
    protected $tableName='merchant_consume_detail';
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

    public function get_statistics($start_time,$end_time,$merchant_id){
        if($merchant_id){
           $where['time']=array('between',array($start_time,$end_time));
           $where['merchant_id']=$merchant_id;
            return $this->where($where)->field('id,merchant_name,money')->select();
        }
    }
}