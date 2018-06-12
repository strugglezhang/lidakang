<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/8/27
 * Time: 12:05
 */

function uploadFiles($savePath){
    $mall_id = session('mall.mall_id');
    $dirName = DIRECTORY_SEPARATOR;
    $rootPath = APP_ROOT.$dirName.'Public'.$dirName.'Uploads'.$dirName.'Images'.$dirName.$savePath.$dirName;
    $config = array(
        'maxSize' => 3145728 ,// 设置附件上传大小
        'exts' => array('jpg','gif','png','jpeg'),// 设置附件上传类型
        'rootPath' => $rootPath, // 设置附件上传根目录
        'autoSub' => true,
        'subName' => $mall_id,
        'saveName' => array('uniqid',''),
        'savePath' => '',
    );
    $upload = new \Think\Upload($config);// 实例化上传类
    return $upload;

}
function filterTime($id,$newStarTime,$newEndTime){
    $model = D('ClassDiscount');
    $res = $model->filterTimime($id);
    if(!empty($res )){
        $boole = array();
        foreach($res as $key => $val) {
            if (($newStarTime == strtotime($val['start_time']) && ($newEndTime >= strtotime($val['end_time'])))) {
                array_push($boole, "false");
            }

            if(($newStarTime < strtotime($val['start_time'])) && ($newEndTime >= strtotime($val['start_time'])) ){
                array_push($boole, "false");
            }

            if ($newStarTime >= strtotime($val['start_time']) && $newEndTime <= strtotime($val['end_time'])) {
                array_push($boole, "false");
            }
            if($newStarTime == strtotime($val['end_time'])){
                array_push($boole,"false");
            }
        }
//        var_dump($res);die;
        if(in_array("false",$boole)){
            return false;
        } else{
            return true;
        }





//        $minStarTime = $res[0]['start_time'];
//        $minEndTime = $res[0]['end_time'];
//        foreach($res as $key => $val){
//           if(strtotime($minStarTime) > strtotime($val['start_time'])){
//               $minStarTime = $val['start_time'];
//           }
//            if(strtotime($minEndTime) > strtotime($val['end_time'])){
//                $minEndTime = $val['end_time'];
//            }
//
//        }
//        if($newStarTime > strtotime($minEndTime)){
//            return true;
//        } else {
//            return false;
//        }


    }
    return true;
}

function getCard($card_number){
    if($card_number){
        return D('Card')->where('card_number='.$card_number)->field('id,card_typeid,card_ownewneme,card_number,cardnumber_no,card_state,card_ownerid')->select();
    }
}

function getMoney($id){
    if($id){
        return D('CoursePlan')->where('id='.$id)->field('price')->select();
    }
}
function getChantStaff($card_number){
    if($card_number){
        return D('MerchantStaff')->where('card_number='.$card_number)->field('id,balance,cardnumber_no,card_number,name')->select();
    }
}

function updateChantBalance($res){
    return D('MerchantStaff')->save($res);
}
function updateInstBalance($res){
    return D('InstitutionStaff')->save($res);
}
function updateMemberBalance($res){
    return D('Member')->save($res);
}
function addMall($mallinfo){
    return D('MallRevenue')->add($mallinfo);
}

function getStaff($card_number){
    if($card_number){
        return D('InstitutionStaff')->where('card_number='.$card_number)->field('id,balance,cardnumber_no,card_number,name')->select();
    }
}
function getMember($card_number){
    if($card_number){
        return D('Member')->where('card_number='.$card_number)->field('id,balance,cardnumber_no,card_number,name')->select();
    }
}

function deletePlan($id){
    if(!$id){
        return false;
    }
    return D('CoursePlan')->delete($id);
}

function addCourse($data){
    if(!$data){
        return false;
    }
    return D('CoursePlan')->add($data);
}
function addRoom($data){
    if(!$data){
        return false;
    }
    return D('RoomReserve')->add($data);
}
function getCategory($category_id){
    if(!$category_id){
        return false;
    }
    return D('Room')->where('category_id='.$category_id)->field('position')->select();
}

function getReserve(){
    return D('RoomReserve')->field('room_id')->select();
}
function getCount($room_id){
    $condition['room_id']=$room_id;
    return D('RoomReserve')->where($condition)->count();
}
function getReserv(){
    return D('RoomReserve')->field('room_id,room_number')->select();
}

function getPassword($id){
    if(!$id){
        return false;
    }
    return D('MallStaff')->field('id,password')->where('id='.$id)->select();
}
function updatePassword($data,$id){
    return D('MallStaff')->where('id='.$id)->save($data);
}

function getInstitution($data){
    $where['shop_id']=array('in',$data);
    return D('Institution')->field('id,position_idx')->where($where)->select();
}
function getStaffId($posId,$sex){


    $ids = "";
    foreach ($posId as $item){
        $ids.=$item['id'].",";
    }
    $ids = rtrim($ids,",");
    //var_dump($ids);die;
    $d = D('InstitutionStaff');
    if($sex==0 || $sex==null)
    {
        return $d->field('id,name,card_number,institution_id')->where(" position_id in({$ids}) ")->select();
    }
    else
    {
        $info=$d->field('id,name,card_number,institution_id')->where("position_id in({$ids}) and sex={$sex}")->select();
        //var_dump($info);die;
        return $info;
    }

//    return D('InstitutionStaff')->field('id,name,card_number,institution_id')->where($where)->select();
}

function addAuth($dataInfu){

    return D('MemberDoorAuth')->add($dataInfu);
}

function getShop($data){
    $where['shop_id']=array('in',$data);
    return D('Merchant')->field('id,shop_addr')->where($where)->select();
}
function getMerchant($posId,$sex){

    $ids = null;
    //var_dump($posId);die;
    foreach ($posId as $item){
        $ids.=$item['id'].",";
    }
    $ids = rtrim($ids,",");
    //var_dump($ids);die;
    $d = D('mall_staff');
    if($sex==0)
    {
        $res=$d->field('id,name,card_number,mall_id,position_id')->where("position_id in ({$ids})")->select();
    }
    else
    {
        $res = $d->field('id,name,card_number,mall_id,position_id')->where("position_id in ({$ids}) and sex={$sex}")->select();
    }
    //echo $d->getLastSql();die;
    return $res;
}

function getMemberDoor($door_id){
    if(!$door_id){
        return false;
    }
    return D('MemberDoorAuth')->field('id')->where('door_id='.$door_id)->select();
}


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
function hasUsedShop($shopId)
{
     //echo('11111');die;
     $condition['shop_id']=array('like','%'.$shopId.'%');
     $instShopInfo=D('Inst')->where($condition)->select();

     if($instShopInfo)
     {
         return true;
     }
     //$condition['shop_id']=array('like','%'.$shopId.'%');
     $merchantShopInfo=D('Merchant')->where($condition)->select();

     if($merchantShopInfo)
     {
         return true;
     }
     $where['shop_id']=$shopId;
     $classShopInfo=D('Room')->where($where)->select();
     //var_dump($classShopInfo);die;
     if($classShopInfo)
     {
         return true;
     }
     return false;
}
function is_ip($gonten){
    $ip = explode(".",$gonten);
    for($i=0;$i<count($ip);$i++)
    {
        if($ip[$i]>255){
            return (0);
        }
    }
    return ereg("^[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}$",$gonten);
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
function hasRepeatedData($data)
{
    $shopIdList=explode("-",$data['shop_ids']);
    $positionList=explode("-",$data['position_name']);
   // var_dump($positionList);die;
    foreach ($shopIdList as $key=>$value)
    {
        $m=D('ShopDoorRole');
        //$shopRoleInfo=$m->query($value);
        $sql="select * from shop_door_role where shop_ids like '%.$value.%'";
        $shopRoleInfo=$m->query($sql);
        //var_dump($value);die;
        foreach ($shopRoleInfo as $k=>$v)
        {
            $shopRoleId=$v['id'];
            foreach ($positionList as $j=>$m)
            {
                $model=D('ShopDoorRole');
                $sql1="select * from shop_door_role where id=.$shopRoleId. and position_name like '%.$m.%'";
                $shopRole=$model->query($sql1);
                if($shopRole)
                {
                    return false;
                }
                else
                {
                    return true;
                }
            }
        }
    }
    return false;
}
function unbondShop($ownerId,$typeId)
{
    switch ($typeId)
    {
        case 1:
            //$InstInfo=D('Inst')->where('id='.$ownerId)->find();
            $data['id']=$ownerId;
            $data['shop_id']='';
            $data['position_idx']='';
            $data['shop_addr']='';
            return D('Inst')->save($data);
            break;
        case 2:
            $data['id']=$ownerId;
            $data['shop_id']='';
            //$data['position_idx']='';
            $data['shop_addr']='';
            return D('Merchant')->save($data);
            break;
        case 3:
            $data['id']=$ownerId;
            $data['shop_id']='';
            //$data['position_idx']='';
            $data['shop_pos']='';
            $data['equip_ip']='';
            $data['equip_id']='';
            return D('Room')->save($data);
            break;
        default:
            break;
    }


}
