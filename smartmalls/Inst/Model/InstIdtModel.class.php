<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/8/13
 * Time: 6:30
 */
namespace Inst\Model;
use Think\Model;
class InstIdtModel extends Model{
    protected $tableName = 'inst_industry';
    public function get_cat($id){
        $condition['id']=$id;
        return $this->field('name')->where($condition)->select();
    }

    public function get_cat_list(){
        return $this->field('id,name')->select();
    }
    public function get_Spf($classify_id){
        if (empty($classify_id)) {
            return false;
        }
//        $where['industry_id']=$classify_id;
//        $where['category_id']=$category_id;
        return $this->field('id,name,classify_id')->where(array('classify_id' => $classify_id))->select();
    }
}