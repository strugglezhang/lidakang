<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/8/19
 * Time: 22:58
 */
namespace Mall\Model;
use Think\Model;
class MemberDoorAuthModel extends Model{
    protected $tableName = 'member_door_auth';

    public function getId($data)
    {
        return $this->field('id')->where();
    }
}