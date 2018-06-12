<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/7/21
 * Time: 10:35
 */
namespace Inst\Model;
use Think\Model;
class InstDepartmentModel extends Model{
    protected $tableName='inst_dept';
    public function get_dept_by_id($dept_id){
        return $this->where(array('id' => $dept_id))->getField('name');
    }
}