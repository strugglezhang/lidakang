<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/7/31
 * Time: 19:22
 */
namespace Mall\Model;
use Think\Model;
class CardModel extends Model{
    protected $tableName = 'card';
    public function getCardInfoByNumber($cardNumber)
    {
        return $this->where('card_number='.$cardNumber)->find();
    }
    public function setCardFrozenByNumber($cardNumber)
    {
        $data['card_state']=2;
        $this->where('card_number='.$cardNumber)->save($data);
    }
}