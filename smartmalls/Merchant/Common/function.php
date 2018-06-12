<?php
/**
 * Created by PhpStorm.
 * User: qs
 * Date: 2017/11/12
 * Time: 17:36
 */

function checkCard($carno){


    // 判断是什么用户
    $typeArr = ['member', 'institution_staff',  'mall_staff', 'merchant_staff'];
    foreach ($typeArr as $key => $v) {
        $member = D($v);
        $obj = $member->where(['card_number' => $carno])->find();
        if (!empty($obj)) {
            return false;
        }
    }

    return true;

}
function hasSameCard($card_number)
{
    $typeArr = [1 => 'member', 2 => 'institution_staff', 3 => 'mall_staff', 4 => 'merchant_staff'];
    foreach ($typeArr as $key => $v) {
        $member = D($v);
        $obj = $member->where(['card_number' => $card_number])->find();
        if (!empty($obj)) {
            //Ret(array('code' => 2, 'info' => '此卡已被他人使用！'));
            return true;
        }
    }
    $cardInfo=getCardInfoByNumber($card_number);
    if(!$cardInfo || $cardInfo['id']==null)
    {
        return false;
    }
    else
    {
        return true;
    }
    return false;
}
function setCardFrozenByNumber($cardNumber)
{
    $data['card_state']=2;
    D('Card')->where('card_number='.$cardNumber)->save($data);
}
function getCardInfoByNumber($card_number)
{
    return D('Card')->where('card_number='.$card_number)->find();
}