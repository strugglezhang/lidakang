<?php
/**
 * Created by PhpStorm.
 * User: 44364
 * Date: 2017/10/16
 * Time: 13:05
 */
namespace Member\Model;
use Think\Model;

class InstStaffModel extends Model{
    protected $tableName='institution_staff';
    public function get_inst_id($inst_id){
        if($inst_id){
            return $this->where('id='.$inst_id)->field('institution_id')->select();
        }
    }
}