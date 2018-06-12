<?php
/**
 * Created by PhpStorm.
 * User: 44364
 * Date: 2017/10/16
 * Time: 17:54
 */
namespace Member\Model;
use Think\Model;

class InstitutionsMallRevenueModel extends Model{
    protected $tableName='institutions_mall_revenue';
    public function getIncomeRecharge($start_time,$end_time,$institution_id=0,$page='1,10'){
        $condition['card_rechargetime'] = array('elt', $end_time);
        $condition['card_rechargetime'] = array('egt', $start_time);
        /*if (!empty($merchant_id)) {
            if($merchant_id){
                $condition['income_ownerid'] = $institution_id;
            }
        }*/

        /*$condition['income_ownertypeid'] = '1';
        if($condition['income_ownertypeid']==null){
            return false;
        }*/
        return D('InstitutionsMallRevenue')->where($condition)->page($page)->select();
    }
    public function getCount($start_time,$end_time,$institution_id=0){
        $condition['card_rechargetime'] = array('elt', $end_time);
        $condition['card_rechargetime'] = array('egt', $start_time);
       /* if (!empty($merchant_id)) {
            if($merchant_id){
                $condition['income_ownerid'] = $institution_id;
            }
        }*/

       /* $condition['income_ownertypeid'] = '1';
        if($condition['income_ownertypeid']==null){
            return false;
        }*/
        return $this->where($condition)->count();
    }

    public function getList($start_time,$end_time,$merchant_id=0,$page='1,10'){
        $condition['card_rechargetime'] = array('elt', $end_time);
        $condition['card_rechargetime'] = array('egt', $start_time);
        if (!empty($merchant_id)) {
            if($merchant_id){
                $condition['income_ownerid'] = $merchant_id;
            }
        }

        $condition['income_ownertypeid'] = '2';
        if($condition['income_ownertypeid']==null){
            return false;
        }
        return D('InstitutionsMallRevenue')->where($condition)->field('id,income_ownertype,income_ownername,income_type,income_num,income_time')->page($page)->select();
    }
    public function get_count($start_time,$end_time,$merchant_id=0){
        $condition['card_rechargetime'] = array('elt', $end_time);
        $condition['card_rechargetime'] = array('egt', $start_time);
        if (!empty($merchant_id)) {
            if($merchant_id){
                $condition['income_ownerid'] = $merchant_id;
            }
        }

        $condition['income_ownertypeid'] = '2';
        if($condition['income_ownertypeid']==null){
            return false;
        }
        return $this->where($condition)->count();
    }
}