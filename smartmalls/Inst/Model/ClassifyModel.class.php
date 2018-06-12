<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/6/16
 * Time: 17:20
 */
namespace Inst\Model;
use Think\Model;
class ClassifyModel extends Model{
    protected $tableName='classify';
    public function get_sub_cat($id){
        return $this->field('name')->where('id='.$id)->select();
    }

    public function get_sub_cats($category_id){
        return $this->field('id,name')->where('category_id='.$category_id)->select();
    }
}