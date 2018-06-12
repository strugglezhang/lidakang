<?php
/**
 * Created by PhpStorm.
 * User: 44364
 * Date: 2017/10/16
 * Time: 15:06
 */
namespace Member\Model;
use Think\Model;

class MerchantModel extends Model{
    protected $tableName='merchant';
    public function get_inst_by_id($inst_id){
        if($inst_id){
            return $this->where('id='.$inst_id)->field('id,name')->select();
        }
    }
}