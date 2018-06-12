<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/7/11
 * Time: 21:45
 */
namespace Inst\Model;
use Think\Model;
class CardBondModel extends Model{
    protected $tableName='card';
    public function getCardByMemberID($id){
        if(!id){
            return false;
        }
        return $this->field('card_number')->where('pid='.$id)->select();
    }
    public function setCardFrozenByInst($instStaff)
    {
        foreach ($instStaff as $key=>$value)
        {
            $data['card_state']=2;
            $this->where('card_number='.$value['card_number'])->save($data);
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