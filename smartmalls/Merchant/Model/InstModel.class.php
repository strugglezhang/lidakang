<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/6/22
 * Time: 14:25
 */
namespace Merchant\Model;
use Think\Model;
class InstModel extends Model
{
    protected $tableName='institution';

    public function get_institution(){
        return $this->field('id,name')->select();
    }
    public function get_institution_by_id($host_id){
        return $this->field('id,name')->where('id='.$host_id)->select();
    }
   public function get_inst_info($id){
       if(!$id){
           return false;
       }
       return $this->field('name')->where('id='.$id)->select();
}
}
