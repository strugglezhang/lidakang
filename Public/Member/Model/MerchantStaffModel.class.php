<?php
/**
 * Created by PhpStorm.
 * User: 44364
 * Date: 2017/10/6
 * Time: 16:55
 */
namespace Member\Model;
use Think\Model;

class MerchantStaffModel extends Model{
    protected $tableName='merchant_staff';
    public function get_member_infos($id,$fields){
        if($id){
            return $this->field($fields)->where('id='.$id)->select();
        }else{
            return false;
        }
    }
    public function get_inst_id($inst_id){
        if($inst_id){
            return $this->where('id='.$inst_id)->field('merchant_id')->select();
        }
    }
}