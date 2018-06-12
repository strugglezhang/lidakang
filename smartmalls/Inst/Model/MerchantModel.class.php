<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/8/20
 * Time: 19:57
 */
namespace Inst\Model;
use Think\Model;

class MerchantModel extends Model{
    protected $tableName='merchant';
    public function get_cat_by_id($merchant_id){
        if($merchant_id){
            return $this->where('id='.$merchant_id)->field('id,name')->find();
        }
    }
}
