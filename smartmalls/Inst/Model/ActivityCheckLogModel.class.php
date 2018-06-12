<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/7/22
 * Time: 22:51
 */
namespace Inst\Model;
use Think\Model;
class ActivityCheckLogModel extends Model{
    protected $tableName='activity_check_log';
    public function get_list($page,$keyword){
        return $this->page($page)->select();
    }
    public function get_count(){
        return $this->count();
    }

}