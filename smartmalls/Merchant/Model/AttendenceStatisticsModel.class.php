<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/7/6
 * Time: 14:40
 */
namespace Merchant\Model;
use Think\Model;
class AttendenceStatisticsModel extends Model{
    protected $tableName='attendenc_statistics';
    public function get_count($time,$dept_id){
        if($time && $dept_id){
            $condition['time']=$time;
            $condition['dept_id']=$dept_id;
            return $this->where($condition)->count();
        }
    }

    public function get_list($time,$dept_id,$page, $fields=null){
        if($time && $dept_id){
            $condition['time']=$time;
            $condition['dept_id']=$dept_id;
            return $this->where($condition)->page($page)->select();
        }
    }
}