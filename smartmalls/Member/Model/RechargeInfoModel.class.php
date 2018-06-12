<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-07-12
 * Time: 5:57
 */
namespace Member\Model;
use Think\Model;

class RechargeInfoModel extends Model{
    protected $tableName='recharge_info';
    public function getRechargeInfoList($page = '1,10')
    {
        $condition['recharge_typeid']=4;
        return $this->where($condition)->order('recharge_info_id desc')->page($page)->select();
    }
    public function getRechargeInfoCount()
    {
        $condition['recharge_typeid']=4;
        return $this->where($condition)->count();
    }
    public function checkOKRechargeInfo($data)
    {
        return $this->save($data);
    }
    /*
     * 获取最近订单信息
     */
    public function getLastRechargeInfo()
    {
        return $this->order('recharge_info_id desc')->limit(1)->select();
    }



}