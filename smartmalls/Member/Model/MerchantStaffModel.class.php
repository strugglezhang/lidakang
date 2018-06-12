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
    public function get_info_by_id($id)
    {
        return $this->where('id='.$id)->select();
    }

}