<?php
namespace Mall\Model;
use Think\Model;
class AuthModel extends Model{
    protected $tableName='role_auth';
    public function add_auth($data){
        return $this->add($data);
    }
    public function get_auth_navi_list_by_role_id($role_id){
        if(!$role_id){
            return false;
        }
        return $this->field('role_id,gongneng,auth')->where('role_id='.$role_id)->select();
    }
}