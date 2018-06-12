<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/7/7
 * Time: 15:11
 */
namespace Merchant\Model;
use Think\Model;
class MerchantShopModel extends Model{
    public function get_shop($merchant_id){
        if(!$merchant_id){
            return false;
        }
        return $this->field('shop_id')->where('merchant_id='.$merchant_id)->select();
    }
}
