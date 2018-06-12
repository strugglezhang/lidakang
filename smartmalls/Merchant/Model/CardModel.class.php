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
    public function setCardFrozen($merchantStaff)
    {
        foreach ($merchantStaff as $key=>$value)
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
}