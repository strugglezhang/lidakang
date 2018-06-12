<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/7/23
 * Time: 16:34
 */
namespace Merchant\Model;
use Think\Model;
class GoodsCheckLogModel extends Model{
    protected $tableName='goods_check_log';
    public function get_count(){
        return $this->count();
    }
    public function get_list($page,$fields){
        return $this->page($page)->select();
    }

    public function insertLog($data){
        return $this->add($data);
    }
    public function add_log($log){
        return $this->add($log);
    }
}