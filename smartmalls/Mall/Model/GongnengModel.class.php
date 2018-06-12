<?php
namespace Mall\Model;
use Think\Model;
class GongnengModel extends Model{
    protected $tableName='gongneng';
    public function get_gongneng_info($role_id){
        if(!$role_id){
            return false;
        }
        return $this->field('id,role_id,project,auth')->where('id='.$role_id)->select();
    }

    public function get_gongneng_count(){
        return $this->count();

    }

    public function add_gongneng($data){
        return $this->add($data);

    }
    public function get_gongneng_list($page){
        return $this->field('id,role_id,project,auth')->page($page)->select();
    }


    public function update_gongneng($data){
//        $id=$data['id'];
        return $this->save($data);
    }
    public function delete_gongneng($id){
        return $this->where('id='.$id)->delete();
    }

}