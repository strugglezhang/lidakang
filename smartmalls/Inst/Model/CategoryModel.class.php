<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/6/19
 * Time: 15:37
 */
namespace Inst\Model;
use Think\Model;
class CategoryModel extends Model{
    protected $tableName='category';
    public function get_cat($id){
        return $this->field('id,name')->where('id='.$id)->select();
    }
    public function get_cats(){
        return $this->field('id,name')->select();
    }
}