<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/6/27
 * Time: 22:02
 */
namespace Merchant\Model;
use Think\Model;
class GoodsCatModel extends Model{
    protected $tableName='goods_category';
    public function get_cat($cat_id){
        if(!$cat_id){
            return false;
        }
        return $this->where('id='.$cat_id)->find();
    }

    public function get_cat_by_id($cat_id){
        if($cat_id){
        return $this->where('id='.$cat_id)->field('id,name')->find();
        }
    }
    public function get_list(){
        return $this->select();
    }

    public function get_sub_cat_by_id($cat_id){
        if($cat_id) {
            return $this->where('id=' . $cat_id)->field('id,name')->find();
        }
    }
}