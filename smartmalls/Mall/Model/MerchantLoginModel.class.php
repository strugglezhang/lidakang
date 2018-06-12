<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/8/23
 * Time: 12:21
 */
namespace Mall\Model;
use Think\Model;
class MerchantLoginModel extends Model{
    protected $tableName='merchant_staff';
    public function login($username,$password){
        $password = createPassword($password);
        return $this->where(array('number' => $username, 'password' => $password))->find();
    }
    public function loginByPassword($username,$password){
        $password = createPassword($password);
        $con['password'] = $password;
        $con['number'] = $username;
        return $this->where($con)->find();
    }
    public function loginByToken($username,$token){
        $con['token'] = $token;
        $con['number'] = $username;
        return $this->where($con)->find();
    }


}