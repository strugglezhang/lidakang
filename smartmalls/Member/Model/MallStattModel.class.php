<?php
/**
 * Created by PhpStorm.
 * User: 44364
 * Date: 2017/10/6
 * Time: 16:52
 */

namespace Member\Model;
use Think\Model;

class MallStaffModel extends Model{
    protected $tableName='mall_staff';
    public function get_member_infos($id,$fields){
        if($id){
            return $this->field($fields)->where('id='.$id)->select();
        }else{
            return false;
        }
    }
}