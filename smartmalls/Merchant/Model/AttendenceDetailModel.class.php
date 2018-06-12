<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/7/6
 * Time: 14:36
 */
namespace Merchant\Model;
use Think\Model;
class AttendenceDetailModel extends Model{
    protected $tableName='attendenc_detail';
    public function get_count(){
        return $this->count();

    }

    public function get_list($page, $fields=null){
        return $this->page($page)->select();

    }

    public function make_check($data){
        if($data){
            return $this->lock(true)->save($data);
        }
    }

    public function count_check(){
        $condition['am_remark']=array('NEQ','NULL');
        $condition['pm_remark']=array('NEQ','NULL');
        return $this->where($condition)->count();

    }
}