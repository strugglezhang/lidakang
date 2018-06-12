<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/6/30
 * Time: 11:21
 */
namespace AppParents\Model;
use Think\Model;
class CourseSubCatModel extends Model{
    protected $tableName='course_sub_category';
    public function get_sub_cat($pcatid){
        return $this->field('id,name')->where('pcatid='.$pcatid)->select();
    }
}