<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/7/6
 * Time: 17:18
 */
namespace Merchant\Model;
use Think\Model;
class MerchantCatModel extends Model
{
    protected $tableName = 'merchant_category';

    public function get_cat()
    {
        return $this->field('id,name')->select();
    }
    public function get_category($id){
        $condition['id']=$id;
        return $this->field('name')->where($condition)->select();
    }
}