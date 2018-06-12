<?php
/**
 * Created by PhpStorm.
 * User: 44364
 * Date: 2017/9/23
 * Time: 21:30
 */
function getMember($cardnumber_no){
    if($cardnumber_no){
        return D('Member')->where('cardnumber_no='.$cardnumber_no)->field('id,name,phone,cardnumber_no')->find();
    }
}

function getCourse($course_id){
    if($course_id){
        return D('CoursePlan')->where('id='.$course_id)->field('start_time,end_time,room_number')->find();
    }
}

function getMemberCard(){
    return D('Member')->field('number')->select();
}


function getRoom($room_number){
    if($room_number){
        return D('Room')->where('position='.$room_number)->field('id,use,price,position,max_number')->select();
    }
}

function getCourseName($course_id){
    if($course_id){
        return D('Course')->where('id='.$course_id)->field('name')->select();
    }
}
function getStaff($card_number){
    if($card_number){
        return D('InstitutionStaff')->where('card_number='.$card_number)->select();
    }
}
function getChantStaff($card_number){
    if($card_number){
        return D('MerchantStaff')->where('card_number='.$card_number)->field('id,balance,cardnumber_no,card_number,name')->select();
    }
}

function updateBalance($res){
    return D('InstitutionStaff')->save($res);
}

function updateChantBalance($res){
    return D('MerchantStaff')->save($res);
}

function addFees($cads){
    return D('ExpenseDetail')->add($cads);
}
function addMall($mallinfo){
    return D('MallRevenue')->add($mallinfo);
}

function getCard($card_number){
    if($card_number){
        return D('Card')->where('card_number='.$card_number)->field('id,card_typeid,card_ownewneme,card_number,cardnumber_no,card_state,card_ownerid')->select();
    }
}

function getCoursePlan($plan_id){
    if($plan_id){
        return D('CoursePlan')->where('id='.$plan_id)->field('start_time,end_time,room_number')->find();
    }
}
function getTryout($id){
    if(!$id){
        return false;
    }
    return D('CourseTryout')->where('id='.$id)->field('*')->select();
}


function checkCard($carno)
{


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
function getCardInfoByNumber($carno)
{


    // 判断是什么用户
    $obj=array();
    $typeArr = ['member', 'institution_staff',  'mall_staff', 'merchant_staff'];
    foreach ($typeArr as $key => $v) {
        $member = D($v);
        $obj = $member->where(['card_number' => $carno])->find();
        if (!empty($obj)) {
            return $obj;
        }
    }

    return $obj;
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