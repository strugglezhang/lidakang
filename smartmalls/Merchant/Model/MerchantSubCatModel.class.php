<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/7/6
 * Time: 17:24
 */
namespace Merchant\Model;
use Think\Model;
class MerchantSubCatModel extends Model
{
    protected $tableName = 'merchant_sub_category';

    public function get_cat()
    {
        return $this->field('id,name')->select();
    }
    public function get_category($id){
        $condition['id']=$id;
        return $this->field('name')->where($condition)->select();
    }

    public function get_cls($category_id){
        if (empty($category_id)) {
            return false;
        }
        return $this->field('id,name')->where(array('category_id' => $category_id))->select();
    }
}