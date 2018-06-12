<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/6/22
 * Time: 14:26
 */

namespace AppParents\Model;
use Think\Model;
class MerchantModel extends Model
{
    protected $tableName='merchant';


    public function get_merchant($merchant_id){
        if(!$merchant_id){
            return false;
        }
        return $this->field('id,name')->where('id='.$merchant_id)->select();
    }


}
