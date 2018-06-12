<?php
/**
 * Created by PhpStorm.
 * User: 44364
 * Date: 2017/9/23
 * Time: 19:58
 */
namespace AppParents\Model;
use Think\Model;

class UserSessionModel extends Model{
    protected $tableName ='user_session';

    public function addUser($val)
    {
        return $this->add($val);

    }

    public function getStatus($phone)
    {
        return $this->field("status")->where("userPhone=".$phone)->select();
    }

    public function getCode($phone)
    {
        return $this->field("userCode")->where("userPhone=".$phone)->select();
    }

    public function delUser($phone)
    {
        return $this->where("userPhone=".$phone)->delete();

    }
}