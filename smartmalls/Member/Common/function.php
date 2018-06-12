<?php

function getCard($card_number)
{
    if ($card_number) {
         return D('Member')->where('card_number=' . $card_number)->field('id,card_number')->select();
    }
}
function getMember($card_number)
{
    if ($card_number) {
        return D('Member')->where('card_number=' . $card_number)->field('id,pic,name')->select();
    }
}
function getMall($card_number)
{
    if ($card_number) {
        return D('MallStaff')->where('card_number=' . $card_number)->field('id,pic,name')->select();
    }
}
function getInst($card_number)
{
    if ($card_number) {
        return D('InstitutionStaff')->where('card_number=' . $card_number)->field('id,pic,name')->select();
    }
}
function getMerchant($card_number)
{
    if ($card_number) {
        return D('MerchantStaff')->where('card_number=' . $card_number)->field('id,pic,name')->select();
    }
}
function get_member_by_card($card_number){
    if($card_number){
        return D('Member')->where('card_number='.$card_number)->field('id,pic,name,phone,card_number')->select();
    }
}

function get_mall_by_card($card_number){
    if($card_number){
        return D('MallStaff')->where('card_number='.$card_number)->field('id,pic,name,phone,card_number')->select();
    }
}
function get_inst_by_card($card_number){
    if($card_number){
        return D('InstitutionStaff')->where('card_number='.$card_number)->field('id,pic,name,phone,card_number')->select();
    }
}
function get_merchant_by_card($card_number){
    if($card_number){
        return D('MerchantStaff')->where('card_number='.$card_number)->field('id,pic,name,phone,card_number')->select();
    }
}

function get_member_card($keyword = null){
    if (!empty($keyword)) {
        if (preg_match("/^\d*$/", $keyword)) {
            $condition['id|id_number'] = $keyword;
        } else {
            $condition['name'] = array('LIKE', '%' . $keyword . '%');
        }
    }
//    echo $condition;die;
    return D('Member')->where($condition)->field('id,pic,name,phone,card_number,id_number')->select();
}
function get_mall_card($keyword = null){
    if (!empty($keyword)) {
        if (preg_match("/^\d*$/", $keyword)) {
            $condition['id|id_number'] = $keyword;
        } else {
            $condition['name'] = array('LIKE', '%' . $keyword . '%');
        }
    }
//    echo $condition;die;
    return D('MallStaff')->where($condition)->field('id,pic,name,phone,card_number,id_number')->select();
}
function get_inst_card($keyword = null){
    if (!empty($keyword)) {
        if (preg_match("/^\d*$/", $keyword)) {
            $condition['id|id_number'] = $keyword;
        } else {
            $condition['name'] = array('LIKE', '%' . $keyword . '%');
        }
    }
//    echo $condition;die;
    return D('InstitutionStaff')->where($condition)->field('id,pic,name,phone,card_number,id_number')->select();
}
function get_merchant_card($keyword = null){
    if (!empty($keyword)) {
        if (preg_match("/^\d*$/", $keyword)) {
            $condition['id|id_number'] = $keyword;
        } else {
            $condition['name'] = array('LIKE', '%' . $keyword . '%');
        }
    }
//    echo $condition;die;
    return D('MerchantStaff')->where($condition)->field('id,pic,name,phone,card_number,id_number')->select();
}

function get_member($card_number){
    if($card_number){
        return D('Member')->where('card_number='.$card_number)->field('balance')->select();
    }
}
function get_mall($card_number){
    if($card_number){
        return D('MallStaff')->where('card_number='.$card_number)->field('balance')->select();
    }
}
function get_inst($card_number){
    if($card_number){
        return D('InstitutionStaff')->where('card_number='.$card_number)->field('balance')->select();
    }
}
function get_merchant($card_number){
    if($card_number){
        return D('MerchantStaff')->where('card_number='.$card_number)->field('balance')->select();
    }
}
function getChantStaff($card_number){
    if($card_number){
        return D('MerchantStaff')->where('card_number='.$card_number)->field('id,balance,cardnumber_no,card_number,name')->select();
    }
}
function getInstStaff($card_number){
    if($card_number){
        return D('InstitutionStaff')->where('card_number='.$card_number)->field('id,balance,cardnumber_no,card_number,name')->select();
    }
}
function getMallStaff($card_number){
    if($card_number){
        return D('MallStaff')->where('card_number='.$card_number)->field('id,balance,cardnumber_no,card_number,name')->select();
    }
}
function getMemberStaff($card_number){
    if($card_number){
        return D('Member')->where('card_number='.$card_number)->field('id,balance,cardnumber_no,card_number,name')->select();
    }
}

function updateBalance($res){
    return D('InstitutionStaff')->save($res);
}

function updateChantBalance($res){
    return D('MerchantStaff')->save($res);
}
function updateMallBalance($res){
    return D('MallStaff')->save($res);
}
function updateMemberBalance($res){
    return D('Member')->save($res);
}



function updateMemberNewBalance($blanece,$id,$table){
    $Model = D();
    try{
        $Model->execute("UPDATE $table SET  balance = '$blanece' WHERE id = '$id'");
        return 1;
    }catch (Exception $e){
        return 0;
    }
}

function addFees($cads){
    return D('RechargeDetail')->add($cads);
}

function addMall($cads){
    return D('MallSpending')->add($cads);
}

function getRecharge($res){
    $condition['card_type'] = $res;
    return D('RechargeDetail')->where($condition)->field('id,card_ownerid,recharge_num,recharge_time,recharge_typeid')->select();
}
function getInstDetail(){
    return D('ExpenseDetail')->field('card_typeid')->select();
}
function getInstRecharge($data){
    $condition['card_typeid'] = $data;
    return D('ExpenseDetail')->where($condition)->field('id,card_ownerid,cost_type,card_rechargenum,card_rechargetime')->select();
}
function getIncomeDetail(){
    return D('InstitutionsMallRevenue')->field('income_ownertypeid')->select();
}

function addExpense($cads){
    return D('ExpenseDetail')->add($cads);
}
function addIncomeDetail($mallinfo){
    return D('InstitutionsMallRevenue')->add($mallinfo);
}
function getGoodsPic($name){
    $condition['name'] = $name;
        return D('Goods')->where($condition)->field('pic')->select();

}
function getCardIds($card_number){
    $condition['card_number'] = $card_number;
    return D('Card')->where($condition)->field('id,card_number,card_state')->select();

}


function getCardId($card_number){
    $condition['card_ownerid'] = $card_number;
    return D('Card')->where($condition)->field('id,card_number,card_state')->select();

}
function getMemberCard($member_id){
    $condition['id'] = $member_id;
    return D('Member')->where($condition)->field('id,card_number')->select();

}
function getCardMember($member_id){
    $condition['card_ownerid'] = $member_id;
    return D('Card')->where($condition)->field('id,card_number,card_state')->select();

}


function getCardNumber($card_number){
    $condition['card_number'] = $card_number;
    $condition['card_typeid'] = 1;
    return D('Card')->where($condition)->field('id,card_ownerid,card_number,card_typeid')->select();

}

function getMembe($card_ownerid){
    $condition['id'] = $card_ownerid;
    return D('Member')->where($condition)->field('id,name,pic')->select();

}
function getInstit($card_ownerid){
    $condition['id'] = $card_ownerid;
    return D('InstitutionStaff')->where($condition)->field('id,name,pic')->select();

}
function getMerchan($card_ownerid){
    $condition['id'] = $card_ownerid;
    return D('MerchantStaff')->where($condition)->field('id,name,pic')->select();

}
function getMallStaf($card_ownerid){
    $condition['id'] = $card_ownerid;
    return D('MerchantStaff')->where($condition)->field('id,name,pic')->select();

}

function getExpense($id){
    $condition['id'] = $id;
    $condition['card_typeid'] = 1;
    return D('ExpenseDetail')->where($condition)->field('card_ownerid,card_rechargenum,card_rechargetime,cost_type,card_typeid')->select();

}
function getReimbres($id){
    $condition['id'] = $id;
    return D('Reimburse')->where($condition)->field('member_id,price')->select();

}

function getMemb($member_id){
    if($member_id){
        return D('Member')->where('id='.$member_id)->field('id,balance,cardnumber_no,card_number,name')->select();
    }
}


function checkCard($carno){


    // 判断是什么用户
    $typeArr = ['member', 'institution_staff',  'mall_staff', 'merchant_staff'];
    $obj = array();
    foreach ($typeArr as $key => $v) {
        $member = D($v);
        $obj = $member->where('cardnumber_no='.$carno)->find();
        if (!empty($obj)) {
            return false;
        }
    }
    //var_dump($obj);
    return $obj;

}

function getCardOwnerNum($keyword)
{
    if($keyword){
        if(is_numeric($keyword)){
            $key['id'] = $keyword;
            $key['phone'] = $keyword;
            $key['_logic'] = 'OR';
            $condition['_complex'] = $key;
        }elseif(!is_numeric($keyword)){
            $condition['name'] = array('LIKE','%'.$keyword.'%');
        }
        $typeArr = ['member', 'institution_staff',  'mall_staff', 'merchant_staff'];
        $count=0;
        foreach ($typeArr as $key => $v) {
            $member = D($v);
            $count = $count+$member->where($condition)->count();
        }
        return $count;
    }
    return 0;
}
function getCardOwnerInfo($keyword,$fields)
{
    $obj = array();
    if($keyword){
        if(is_numeric($keyword)){
            $key['id'] = $keyword;
            $key['phone'] = $keyword;
            $key['_logic'] = 'OR';
            $condition['_complex'] = $key;
        }elseif(!is_numeric($keyword)){
            $condition['name'] = array('LIKE','%'.$keyword.'%');
        }
        //$condition
        $typeArr = ['member', 'institution_staff',  'mall_staff', 'merchant_staff'];

        foreach ($typeArr as $key => $v) {
            $member = D($v);
            $resultList= $member->where($condition)->field($fields)->select();
            $obj=array_merge($obj, $resultList);
        }
        return $obj;
    }
    return $obj;
}
function setUnbondCardByCardNo($carno)
{
    // 判断是什么用户

    $typeArr = ['member', 'institution_staff',  'mall_staff', 'merchant_staff'];
    $obj = array();
    foreach ($typeArr as $key => $v) {
        $member = D($v);
        $obj = $member->where('card_number='.$carno)->find();
        if (!empty($obj)) {
            $cardOwnerUpdateData['id']=$obj['id'];
            $cardOwnerUpdateData['card_number']='';
            $cardOwnerUpdateData['cardnumber_no']='';
            //var_dump($obj);die;
            $member->save($cardOwnerUpdateData);
            return true;
        }
    }
    return false;
}
//验证是否有有效卡
function getCardOwnerInfoById($ownerId)
{
    $typeArr = ['member', 'institution_staff',  'mall_staff', 'merchant_staff'];
    $obj = array();
    foreach ($typeArr as $key => $v) {
        $member = D($v);
        $obj = $member->where(['id='.$ownerId])->find();
        if (!empty($obj)) {
            //$obj['owner_type']=$v;
            return $obj;
        }
    }
    return $obj;
}

function setActivateCard($cardNumber,$ownerId,$cardNumberNo)
{
    $typeArr = ['member','mall_staff', 'institution_staff','merchant_staff'];
    $obj = array();
    foreach ($typeArr as $key => $v) {
        $member = D($v);
        $obj = $member->where('id='.$ownerId)->find();
        if (!empty($obj)) {
            //$obj['owner_type']=$v;
            $cardOwnerUpdateData['id']=$ownerId;
            $cardOwnerUpdateData['card_number']=$cardNumber;
            $cardOwnerUpdateData['cardState']=1;
            $cardOwnerUpdateData['cardnumber_no']=$cardNumberNo;
            $member->save($cardOwnerUpdateData);
            $data['card_ownerid'] = $ownerId;
            $data['card_ownewneme'] =$obj['name'];
            $data['card_number'] = $cardNumber;
            $data['cardnumber_no'] = $cardNumberNo;
            $data['card_typeid'] = $key+1;
            $data['card_state'] = 1;
            //var_dump($data);die;
            //D('Card')->add()
            D('Card')->add($data);
            return true;
        }
    }
    return false;
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
    $cardInfo=D('Card')->getCardInfoByNumber($card_number);
    //var_dump($cardInfo);die;
    //$test='1111';
    //$test2='22222';

    if(empty($cardInfo) || $cardInfo['id']==null )
    {
        //var_dump($test);die;
        //echo '1111';die;
        return false;
    }
    else
    {
        //echo '2222';die;
        //var_dump($test2);die;
        return true;
    }
    return false;
}