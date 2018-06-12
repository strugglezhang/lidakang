<?php
namespace Mall\Model;
use Think\Model;
class MemberRoleModel extends Model{
    protected $tableName='member_role';
    public function get_roleID_by_number($number){
        $map=array('number'=>$number);
        return $this->where($map)->find();
    }

    public function get_role(){
        return $this->select();
    }

    public function update($data){
        $number=$data['number'];
        $map=array("number=>$number");
        return $this->where($map)->save($data);
    }



    public function updateRole($id){
        if(!$id){
            return false;
        }
        return $this->field('id,role_id,role')->where('number='.$id)->select();
    }

  public function updateMallRole($data){
        return $this->save($data);
    }
}