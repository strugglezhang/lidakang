<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/6/20
 * Time: 11:01
 */
namespace Inst\Model;
use Think\Model;
class ServiceSubCategoryModel extends Model{
    protected $tableName='service_sub_category';
    public function get_sub_cat($id){
        if($id){
            return $this->field('name')->where('id='.$id)->select();
        }
    }
    public function get_sub_cat_list(){
        return $this->select();
    }

    public function get_cls($service_catid){
        if (empty($service_catid)) {
            return false;
        }
        return $this->field('id,name')->where(array('service_catid' => $service_catid))->select();
    }
}