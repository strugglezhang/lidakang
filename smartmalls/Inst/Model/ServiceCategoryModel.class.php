<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/6/20
 * Time: 11:01
 */
namespace Inst\Model;
use Think\Model;
class ServiceCategoryModel extends Model{
    protected $tableName='service_category';
    public function get_cat($id){
        if($id){
            return $this->field('name')->where('id='.$id)->select();
        }
    }
    public function get_cat_list(){
        return $this->select();
    }
}