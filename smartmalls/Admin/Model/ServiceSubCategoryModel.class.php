<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/6/20
 * Time: 11:01
 */
namespace Admin\Model;
use Think\Model;
class ServiceSubCategoryModel extends Model{
    protected $tableName='service_sub_category';
    public function get_sub_cat($id){
        return $this->field('name')->where('id='.$id)->select();
    }
    public function get_sub_cat_list(){
        return $this->select();
    }
}