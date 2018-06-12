<?php
/**
 * Created by PhpStorm.
 * User: 44364
 * Date: 2017/10/24
 * Time: 13:29
 */
namespace Member\Model;
use Think\Model;

class ReimburseModel extends Model{
    protected $tableName='reimburse';
    public function getExpenseDetail($page='1,10'){

        return $this->field('id,member_name,pic,price,time,content')->page($page)->select();

    }
    public function getCoun(){
        return $this->count();
    }
}