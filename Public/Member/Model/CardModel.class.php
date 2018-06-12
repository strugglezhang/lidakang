<?php
namespace Member\Model;
use Think\Model;
class CardModel extends Model{
    protected $tableName='Card';
    public function active($card_number){
        if($card_number){
            $data['card_number'] = $card_number;
            $data['card_state'] = 1;

            $condition['card_number'] = $card_number;

            return $this->where($condition)->save($data);
        }
        
    }
    public function check_member_card($pid){
        if($pid){
            $condition['card_ownerid']=$pid;
            $condition['card_state']=array('in','0,1');
            return $this->where($condition)->select();
        }
    }
}