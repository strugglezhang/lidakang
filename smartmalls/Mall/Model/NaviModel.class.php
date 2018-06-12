<?php
namespace Mall\Model;
use Think\Model;
class NaviModel extends Model{
    protected $tableName='navi';
    public function get_navi_info($role_id){
        if(!$role_id){
            return false;
        }
        return $this->field('id,first_level_navi_id,second_level_navi_id')->where('id='.$role_id)->select();
    }

    public function get_navi($page){

        return $this->field('id,first_level_navi_id,second_level_navi_id')->page($page)->select();
    }
    public function get_count(){

        return $this->count();
    }

    public function add_navi($data){
        return $this->add($data);

    }
    public function update_role($data){
        $id=$data['id'];
        return $this->where('id='.$id)->save($data);
    }
    public function delete_role($id){
        return $this->where('id='.$id)->delete();
    }

}