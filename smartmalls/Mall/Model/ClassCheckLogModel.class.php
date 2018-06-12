<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/7/22
 * Time: 15:14
 */
namespace Mall\Model;
use Think\Model;
class ClassCheckLog extends Model{
    protected $tableName='class_check_log';
    public function get_count(){
        return $this->count();

    }
    public function get_list($page,$keyword){
        if (!empty($keyword)) {
            $key_where['number'] = $keyword;
            $key_where['name'] = array('LIKE','%'.$keyword.'%');
            $key_where['_logic'] = 'OR';
            $where['_complex'] = $key_where;
        }
        return $this->where($where)->page($page)->select();

    }
}