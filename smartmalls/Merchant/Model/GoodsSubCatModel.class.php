<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/6/27
 * Time: 22:08
 */
namespace Merchant\Model;
use Think\Model;
class GoodsSubCatModel extends Model{
    protected $tableName='goods_sub_category';
    public function get_sub_cat($subcat_id){
        if(!$subcat_id){
            return false;
        }
        return $this->where('id='.$subcat_id)->find();
    }

    public function get_sub_cat_by_id($cat_id){
        if($cat_id) {
             return $this->where('id=' . $cat_id)->field('id,name')->find();
        }
    }

    public function get_list($cat_id){
        if(!$cat_id){
            return false;
        }
        return $this->field('id,name')->where('goods_catid='.$cat_id)->select();
    }

    public function get_sub_cat_list(){
        return $this->field('id,name')->select();
    }


    public function get_cls($goods_catid){
        if (empty($goods_catid)) {
            return false;
        }
        return $this->field('id,name')->where(array('goods_catid' => $goods_catid))->select();
    }
}