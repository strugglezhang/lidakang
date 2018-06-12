<?php
/**
 * Created by PhpStorm.
 * User: 44364
 * Date: 2017/10/16
 * Time: 17:47
 */
namespace AppParents\Model;
use Think\Model;

class ExpenseDetailModel extends Model{
    protected $tableName='expense_detail';
    public function get_by_count($member_id){
        $condition['card_ownerid'] = $member_id;
        //$condition['card_typeid'] = '1';
        return $this->where($condition)->count();
    }
    public function get_by_list($member_id,$page){
        $condition['card_ownerid'] = $member_id;
        //$condition['card_typeid'] = '1';
        //var_dump($member_id);die;
       return  $this->where($condition)->field('id,card_ownerid,card_rechargenum,card_rechargetime,cost_content_name')->page($page)->select();
        //echo $this->getLastSql();die;
    }
}