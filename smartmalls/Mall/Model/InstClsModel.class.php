<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/7/1
 * Time: 14:33
 */
namespace Mall\Model;
use Think\Model;
class InstClsModel extends Model
{
    protected $tableName = 'inst_classify';

    public function get_cat()
    {
        return $this->field('id,name')->select();
    }

    public function get_category($id){
        $condition['id']=$id;
        return $this->field('name')->where($condition)->select();
    }
}
