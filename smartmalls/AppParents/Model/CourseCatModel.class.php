<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/6/30
 * Time: 11:21
 */
namespace AppParents\Model;
use Think\Model;
class CourseCatModel extends Model{
    protected $tableName='course_category';
    public function get_cat(){
        return $this->field('id,name')->select();
    }

    public function get_cat_by_id($id){
        $condition['id']=$id;
        return $this->field('name')->where($condition)->select();
    }
}