<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/7/4
 * Time: 22:55
 */

namespace Member\Model;
use Think\Model;
class CardBondModel extends Model{
    protected $tableName='member';
    public function get_card($member_id){
        if(!$member_id){
            return false;
        }
        $condition['id']=$member_id;
        $conditon['cardState']=1;
        return $this->field('card_number')->where($condition)->find();
    }

    public function active($card_number){
        if($card_number){
            $data['card_number']=$card_number;
            $data['cardState']=1;
            $condition['card_number']=$card_number;
            $condition['cardState']=0;
            return $this->where($condition)->save($data);
        }

    }

    public function check_member_card($pid){
        if($pid){
            $condition['id']=$pid;
            $condition['cardState']=array('in','0,1');
            return $this->where($condition)->select();
        }
}

}