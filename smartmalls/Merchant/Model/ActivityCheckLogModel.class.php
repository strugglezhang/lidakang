<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-07-17
 * Time: 3:54
 */
namespace Merchant\Model;
use Think\Model;

class ActivityCheckLogModel extends Model{
    protected $tableName='activity_check_log';
    public function add_log($data){
        return $this->add($data);
    }
    public function get_list( $keyword , $page = '1,10',$fields = null){

        if (!empty($keyword)) {
            if(preg_match("/^\d*$/",$keyword)){
                $condition['id'] = $keyword;
            }else{
                $condition['activity_name'] = array('LIKE','%'.$keyword.'%');
            }
        }
        return $this->field('id,activity_name,check_time,hold_time,submitter,checker,check_state,institution,hold_address,contact,phone,start_time,end_time')->where($condition)->page($page)->select();
    }
    public function get_count($keyword){

        if (!empty($keyword)) {
            if(preg_match("/^\d*$/",$keyword)){
                $condition['id'] = $keyword;
            }else{
                $condition['activity_name'] = array('LIKE','%'.$keyword.'%');
            }
        }
        return $this->where($condition)->count();

    }

    public function insertLog($data){
        return $this->add($data);
    }
}