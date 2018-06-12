<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/6/15
 * Time: 17:44
 */
namespace Member\Model;
use Think\Model;
class InstitutionModel extends Model{
    protected $tableName='institution';
    public function get_institution_info($id){
        return $this->field('name,logo')->where('id='.$id)->select();
    }
    public function get_inst_by_id($inst_id){
        if($inst_id){
            return $this->where('id='.$inst_id)->field('id,name')->select();
        }
    }
	 public function  get_info($institution_id){
         if(!$institution_id){
             return false;
         }
        return $this->field('name')->where('id='.$institution_id)->select();
    }
    public function get_isnt_info($id){
        if(!$id){
            return false;
        }
        return $this->field('name')->where('id='.$id)->select();
    }
    public function get_app_info($institution_id){
        if(!$institution_id){
            return false;
        }
        return $this->field('name,imgs')->where('id='.$institution_id)->select();
    }

    public function get_member_infos($id,$fields){
        if($id){
            return $this->field($fields)->where('id='.$id)->select();
        }else{
            return false;
        }
    }
}