<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/9/6
 * Time: 10:43
 */
namespace Mall\Model;
use Think\Model;
class PermissionsModel extends Model
{
    protected $tableName = 'permissions';

    public function get_permissions(){
        return $this->field('id,first_level,second_level,therd_level')->select();
    }
}