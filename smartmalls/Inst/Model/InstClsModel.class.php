<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/7/10
 * Time: 21:53
 */

namespace Inst\Model;
use Think\Model;
class InstClsModel extends Model{
    protected $tableName='inst_classify';
    public function get_cat($id){
        $condition['id']=$id;
        return $this->field('name')->where($condition)->select();
    }

    public function get_cat_list(){
        return $this->field('id,name')->select();
    }

    public function get_Spf($category_id){
        if(!$category_id){
            return false;
        }
        $where['category_id']=$category_id;
        return $this->field('id,name,category_id')->where($where)->select();
    }
}