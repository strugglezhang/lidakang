<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-06-25
 * Time: 22:58
 */
namespace Mall\Model;
use Think\Model;
class ClassCategoryModel extends Model{
    protected $tableName ='room_category';
    public function get_cat($room_id){
        if(!$room_id){
            return false;
        }
        $map =array('mall_id'=>$room_id);
        return $this->field('category_id,classify_id,industry_id,specify_id')-> where($map)->select();
    }
}