<?php
namespace Merchant\Model;
use Think\Model;
class LoginModel extends Model{
    protected  $tableName  = 'merchant_staff';

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

    public function update_worker($data){

        return $this->save($data);
    }

}