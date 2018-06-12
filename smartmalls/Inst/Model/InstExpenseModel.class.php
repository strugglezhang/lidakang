<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/7/18
 * Time: 12:11
 */
namespace Inst\Model;
use Think\Model;
class InstExpenseModel extends Model{
    protected $tableName='institution_expense';
    public function get_list($page)
    {
        return $this->field('id,institution_id,expense_id,price')->page($page)->select();
    }
}