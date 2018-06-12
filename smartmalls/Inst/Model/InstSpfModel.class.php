<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/8/13
 * Time: 6:47
 */
namespace Inst\Model;
use Think\Model;
class InstSpfModel extends Model{
    protected $tableName = 'inst_specify';
    public function get_cat($id){
        $condition['id']=$id;
        return $this->field('name')->where($condition)->select();
    }

    public function get_cat_list(){
        return $this->field('id,name')->select();
    }
    public function get_Spf($industry_id){
        if ( empty($industry_id)) {
            return false;
        }
        return $this->field('id,name,industry_id')->where(array('industry_id' => $industry_id))->select();
    }
    public function getSpf($id){
        if (empty($id)) {
            return false;
        }
        return $this->field('id,name,industry_id')->where(array('id' => $id))->select();
    }
}