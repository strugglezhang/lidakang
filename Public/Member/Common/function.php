<?php

function getCard($card_number)
{
    if ($card_number) {
        return D('Card')->where('card_number=' . $card_number)->field('id,card_typeid,card_ownewneme,card_number,cardnumber_no,card_state,card_ownerid')->select();
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




