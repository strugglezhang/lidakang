<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/7/29
 * Time: 22:26
 */
namespace Mall\Model;
use Think\Model;
class NaviSecondModel extends Model
{
    protected $tableName = 'navi_second';

    public function get_third($first_level){
        $condition['first_level']=$first_level;

        return $this->field('second_level')->where($condition)->select();
    }
}