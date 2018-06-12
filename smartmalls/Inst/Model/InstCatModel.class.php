<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/7/10
 * Time: 14:36
 */

namespace Inst\Model;
use Think\Model;
class InstCatModel extends Model{
    protected $tableName='inst_category';
    public function get_cat($id){
        $condition['id']=$id;
        return $this->field('name')->where($condition)->select();
    }

    public function get_cat_list(){
        return $this->field('id,name')->select();
    }
    public function get_inst_by_id($id){
        if($id){
            return $this->where('id='.$id)->field('id,name')->select();
        }
    }

}