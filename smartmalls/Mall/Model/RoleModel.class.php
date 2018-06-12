<?php
namespace Mall\Model;
use Think\Model;
class RoleModel extends Model{
    protected $tableName='role';
    public function get_role_list($page){
        return $this->page($page)->select();
    }
    public function get_role_count(){
        return $this->count();
    }
    public function get_role_by_name($role_name){
        if(!$role_name){
            return false;
        }
        $map=array('rolename'=>$role_name);
        return $this->field('id,rolename')->where($map)->select();
    }

    public function get_role_by_id($role_id){

        return $this->field('id,rolename')->where('id='.$role_id)->select();
    }

    public function add_role($data){
        return $this->add($data);
    }
    public function update_role($data){
        return $this->save($data);
    }
    public function delete_role($id){
        return $this->delete($id);
    }
}