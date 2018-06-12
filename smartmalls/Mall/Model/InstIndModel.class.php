<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/7/1
 * Time: 14:34
 */
namespace Mall\Model;
use Think\Model;
class InstIndModel extends Model
{
    protected $tableName = 'inst_industry';

    public function get_cat()
    {
        return $this->field('id,name')->select();
    }

    public function get_category($id){
        $condition['id']=$id;
        return $this->field('name')->where($condition)->select();
    }
}