<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/7/21
 * Time: 10:35
 */
namespace Merchant\Model;
use Think\Model;
class MerchantDepartmentModel extends Model{
    protected $tableName='merchant_dept';
    public function get_dept_by_id($dept_id){
        return $this->where(array('id' => $dept_id))->getField('name');
    }
}