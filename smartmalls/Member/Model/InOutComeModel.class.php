<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/7/28
 * Time: 10:38
 */
namespace Member\Model;
use Think\Model;
class InOutComeModel extends Model{
    protected $tableName='member_income_outcome_balance';
    public function make_income($data){
        if($data){
            $d['member_id'] = $data['member_id'];

            $condition['member_id']=$data['member_id'];

            $p = $this->where($condition)->find();
            if(!$p){
                $d['income'] = $data['income'];
                return $this->where($condition)->add($data);
            }
            if($p){
                $d['income'] = $data['income']+$p['income'];
                return $this->where($condition)->save($d);
            }
        }

    }
    public function make_outcome($data){
        if($data){
            $d['member_id'] = $data['member_id'];

            $condition['member_id']=$data['member_id'];

            $p = $this->where($condition)->find();
            if(!$p){
                $d['outcome'] = $data['outcome'];
                return $this->where($condition)->add($data);
            }
            if($p){
                $d['outcome'] = $data['outcome']+$p['outcome'];
                return $this->where($condition)->save($d);
            }
        }


    }

    public function get_balance($member_id){
        if($member_id){
            $condition['member_id']=$member_id;
            $d = $this->where($condition)->find();
            $balance = $d['income']-$d['outcome'];
            return $balance;
        }
    }

}