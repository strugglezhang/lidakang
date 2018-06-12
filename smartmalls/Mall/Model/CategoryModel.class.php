<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/6/26
 * Time: 17:07
 */
namespace Mall\Model;
use Think\Model;
class CategoryModel extends Model
{
    protected $tableName = 'inst_category';
    public function get_category($id){
        $condition['id']=$id;
        return $this->field('name')->where($condition)->select();
    }
}