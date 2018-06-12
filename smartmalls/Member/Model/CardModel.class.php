<?php
namespace Member\Model;
use Think\Model;
class CardModel extends Model{
    //卡的状态：1为激活状态，2为冻结，3为挂失
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
    public function setCardFrozenByNumber($cardNumber)
    {
        $data['card_state']=2;
        $this->where('card_number='.$cardNumber)->save($data);
    }
    public function getCardInfoByNumber($cardNumber)
    {
        return $this->where('card_number='.$cardNumber)->find();
    }
}