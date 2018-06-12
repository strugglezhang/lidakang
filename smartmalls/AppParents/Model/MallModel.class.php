<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/6/22
 * Time: 14:25
 */
namespace AppParents\Model;
use Think\Model;
class MallModel extends Model
{
    protected $tableName='mall';

    public function get_mall(){
        return $this->field('id,name')->select();
    }

    public function get_mall_by_id($id){
        return $this->field('id,name')->where('id='.$id)->select();
    }

}
