<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/7/27
 * Time: 14:21
 */

namespace Mall\Controller;

class ShopDoorController extends CommonController
{

    /*
     * 门禁权限添加
     * input: type,shop_ids,shop_numbers,user_type,position,position_name,week,time
     * output:code,info
     */
    public function add()
    {
        $data['type'] = I('type');//类别
        $data['shop_ids'] = I('shop_ids');//门禁id
        $data['position']= I('shop_numbers');//门禁位置
        $data['shop_numbers'] = $this->convartNo2Ip($data['shop_ids']);//门禁IP地址
        //var_dump($data['shop_numbers']);die;
        $data['user_type'] = I('user_type');
        //$data['position'] = I('position');
        $data['position_name'] = I('position_name');

        $data['week'] = json_encode($_POST['week']);
        $data['sex']=I('sex');
        $model = D('ShopDoorRole');
        $searchDataOk = hasRepeatedData($data);
        if(!empty($data['shop_numbers'])) {
            if ($searchDataOk == false) {
                if ($doorId = $model->add($data)) {
                    $shopIdList = explode('-', $data['shop_ids']);
                    //$res = getInstitution($shopIdList);
                    //$ids = array_column($res, 'id');

                    if ($data['user_type'] == 1) {//机构
                        $positionModel = D("InstPosition");
                        $positionName = explode(",", $data['position_name']);
                        $names = "";
                        //var_dump($positionName);die;
                        foreach ($positionName as $key => $item) {
                            if (empty($item)) {
                                unset($positionName[$key]);
                                continue;
                            }
                            $names .= "'{$item}'" . ",";

                        }
                        $names = rtrim($names, ",");

                        $positionIds = $positionModel->where("name in ({$names})")->select();
                        //var_dump($positionIds);die;
                        //获取员工信息
                        $card = getStaffId($positionIds, $data['sex']);
                        //var_dump($card);die;
                        if ($card) {
                            foreach ($card as $key => $value) {
                                //$a = $this->findEqId($value['institution_id'], $res, 1);
                                $shopIpList = explode(",", $data['shop_numbers']);
                                $weekTime = json_decode($data['week'], 1);
                                $returnTime = $this->formatTime($value['card_number'], $weekTime[0], 1);
                                $positionName = $positionModel->where("id = {$value['position_id']}")->select();
                                foreach ($shopIpList as $k => $v) {
                                    $data['dataInfu'][$key] = array(
                                        'door_id' => $doorId,
                                        'memberId' => $value['id'],
                                        'memberName' => $value['name'],
                                        'cardNo' => $value['card_number'],
                                        'eqIp' => $v,
                                        'time' => $returnTime,
                                        'position_name' => $positionName[0]['name'],
                                        'state' => 0
                                        //'door_id'=>$doorId
                                    );
                                    $m = D('MemberDoorAuth');
                                    $m->add($data['dataInfu'][$key]);
                                }

                            }
                            Ret(array('code' => 1, 'info' => '添加成功！'));
                        }
                    } elseif ($data['user_type'] == 2)
                    {//商场
                        $positionName = explode(",", $data['position_name']);
                        $names = "";
                        //var_dump($positionName);die;
                        foreach ($positionName as $key => $item) {
                            if (empty($item)) {
                                unset($positionName[$key]);
                                continue;
                            }
                            $names .= "'{$item}'" . ",";

                        }
                        $names = rtrim($names, ",");
                        //var_dump($names);die;
                        $positionModel = D("MallPosition");
                        $positionIds = $positionModel->where("name in ({$names})")->select();

                        $card = getMerchant($positionIds, $data['sex']);
                        //var_dump($card);die;
                        if ($card) {
                            $shopArr = explode(",", $data['shop_numbers']);
                            foreach ($shopArr as $item=>$v) {
                                foreach ($card as $key => $value) {
                                    $weekTime = json_decode($data['week'], 1);
                                    $returnTime = $this->formatTime($value['card_number'], $weekTime[0], 2);
                                    $positionName = $positionModel->where("id = {$value['position_id']}")->select();
//                            $a = $this->findEqIdbyPosition();
                                    $data['dataInfu'][$key] = array(
                                        'door_id' => $doorId,
                                        'memberId' => $value['id'],
                                        'memberName' => $value['name'],
                                        'cardNo' => $value['card_number'],
                                        'eqIp' => $v,
                                        'time' => $returnTime,
                                        'position_name' => $positionName[0]['name'],
                                        'state' => 0
                                        //'door_id'=>$doorId
                                    );
                                    $m = D('MemberDoorAuth');
                                    $m->add($data['dataInfu'][$key]);
                                }
                            }

                            Ret(array('code' => 1, 'info' => '添加成功！'));
                        }
                    }
                    elseif($data['user_type'] == 3)
                    {
                        $memberModel=D('Member');
                        $card=$memberModel->where('sex='.$data['sex'])->select();
                        if ($card) {
                            $shopArr = explode(",", $data['shop_numbers']);
                            foreach ($shopArr as $item=>$v) {
                                foreach ($card as $key => $value) {
                                    $weekTime = json_decode($data['week'], 1);
                                    $returnTime = $this->formatTime($value['card_number'], $weekTime[0], 2);
                                    //$positionName = $positionModel->where("id = {$value['position_id']}")->select();
//                            $a = $this->findEqIdbyPosition();
                                    $data['dataInfu'][$key] = array(
                                        'door_id' => $doorId,
                                        'memberId' => $value['id'],
                                        'memberName' => $value['name'],
                                        'cardNo' => $value['card_number'],
                                        'eqIp' => $v,
                                        'time' => $returnTime,
                                        //'position_name' => $positionName[0]['name'],
                                        'state' => 0
                                        //'door_id'=>$doorId
                                    );
                                    $m = D('MemberDoorAuth');
                                    $m->add($data['dataInfu'][$key]);
                                }
                            }

                            Ret(array('code' => 1, 'info' => '添加成功！'));
                        }
                    }
                } else {
                    Ret(array('code' => 2, 'info' => '添加失败,系统出错！'));
                }
            } else {
                Ret(array('code' => 2, 'info' => '添加失败,已经有对应权限！'));
            }
        }
        else
        {
            Ret(array('code' => 2, 'info' => '商铺没有IP地址！'));
        }
    }

    private function formatTime($carNumber, $weekTime,$typeId)
    {
        $m = array(
            0 => 7,
            1 => 1,
            2 => 2,
            3 => 3,
            4 => 4,
            5 => 5,
            6 => 6
        );

        $weekEndTime = 0;
        foreach ($m as $k => $v) {
            if ($k == $weekTime['week_end']) {
                $weekEndTime = $v;
            }
        }
        $t = "";
        if ($weekEndTime != 0) {
            for ($i = 0;  $i <$weekEndTime;$i++) {
                if($weekTime['time'][0]['time_start'] == "00:00"){
                    $weekTime['time'][0]['time_start'] = "0:00";
                }
                $t .= $i . "," . $weekTime['time'][0]['time_start']."-".$weekTime['time'][0]['time_end'] . ";";
            }
        }
        //商场员工的信息
        if($typeId==2) {
            $mallStaffModel = D("Worker");
            $res = $mallStaffModel->field("*")->where("card_number=$carNumber")->select();
        }
        //机构员工的信息
        if($typeId==1){
            $InstStaffModel = D("InstStaff");
            $res = $InstStaffModel->field("*")->where("card_number=$carNumber")->select();
        }
        //会员的信息
        if($typeId==3){
            $MemberModel = D("Member");
            $res = $MemberModel->field("*")->where("card_number=$carNumber")->select();
        }
        if (!empty($res)) {
            $startTime = $res[0]['join_time'];
            $endTime = $res[0]['contract_time'];
            $returnTime = date("Ymd",strtotime($startTime)) . "," . date("Ymd",strtotime($endTime)) . "#" . rand(1, 1000) . "]" . $t;
            return rtrim($returnTime,";");
        }
    }


    private function findEqIdbyPosition($id)
    {
        $model = D("EquipmentDoor");
        var_dump($id);
        die;
        $eqId = $model->field("*")->where("position = $id")->select();
        var_dump($eqId);
        die;
    }
    private function findEqId($id, $eqArr, $type)
    {
        $a = count($eqArr);
        for ($i = 0; $i < $a; $i++) {

            if ($id == $eqArr[$i]['id']) {
                if ($type == 1) {
                    $return = "position_idx";
                } elseif ($type == 2) {
                    $return = "shop_addr";
                } else {
                    $return = "";
                }
                return $eqArr[$i][$return];
            }
        }
    }
    //根据店铺获取IP地址
    private function convartNo2Ip($shopNos)
    {
        //var_dump($shopNos);die;
        if ($shopNos) {
            $data = explode('-', $shopNos);
            //var_dump($data);die;
            //$i=$data[2];
            //var_dump($i);die;
            if ($data) {
                foreach ($data as $k => $v) {
                        //echo('11111');die;
                        $con['id'] = $v;
                        $d = D('Shop')->where($con)->find();
                        //var_dump($d);die;
                        $equipIdList = explode('-', $d['eqip']);
                        $equipIdList = implode(',', $equipIdList);
                        //var_dump($equipIdList);die;
                        if(!empty($equipIdList)) {
                            $equipmentInfo = D('EquipmentDoor')->field('equip_ip')->where("equip_id in ({$equipIdList})")->select();
                            //var_dump($equipmentInfo);die;
                            foreach ($equipmentInfo as $j => $m) {
                                $equipInfo[$j] = $m['equip_ip'];
                            }
                            $equipIpList = implode(',', $equipInfo);
                            //var_dump($equipIpList);die;
                            $datas[$k] = $equipIpList;
                        }
                }
            }
        }
        //echo('2222');die;
        //var_dump($datas);die;
        return implode(',', $datas);
    }


    /*
     * 门禁权限修改
     * input: id,type,shop_ids,shop_numbers,user_type,position,position_name,week,time
     * output:code,info
     */

    public function update()
    {
        $data['id'] = I('id');
        if (!$data['id']) {
            Ret(array('code' => 2, 'info' => '参数（id）有误！'));
        }
        $data['type'] = I('type');
        if (!$data['type']) {
            Ret(array('code' => 2, 'info' => '参数（type）有误！'));
        }
        $data['shop_ids'] = I('shop_ids');
        if (!$data['shop_ids']) {
            Ret(array('code' => 2, 'info' => '参数（shop_ids）有误！'));
        }
        $shopNos = I('shop_numbers');//门禁位置
        $data['shop_numbers'] = $this->convartNo2Ip($shopNos);
        $data['user_type'] = I('user_type');
        $data['position'] = I('position');
        $data['position_name'] = I('position_name');
        $data['week'] = json_encode($_POST['week']);
        $model = D('ShopDoorRole');
//        $res = $model->save($data);
//        echo $model->_sql();die;
//        var_dump($data);die;
        $oldShopRoleInfo=$model->where('id='. $data['id'])->select();
        if ($res = $model->save($data)) {
            if ($data['type'] == 1) {//机构
                //将所有的更新信息加入表中
                $positionModel = D("InstPosition");
                $positionName = explode(",", $data['position_name']);
                $names = "";
                //var_dump($positionName);die;
                foreach ($positionName as $key => $item) {
                    if (empty($item)) {
                        unset($positionName[$key]);
                        continue;
                    }
                    $names .= "'{$item}'" . ",";

                }
                $names = rtrim($names, ",");

                $positionIds = $positionModel->where("name in ({$names})")->select();
                //var_dump($positionIds);die;
                //获取员工信息
                $card = getStaffId($positionIds, $data['sex']);

                if ($card) {
                    $memModel = D('MemberDoorAuth');
                    foreach ($card as $key => $value) {
                        //$a = $this->findEqId($value['institution_id'], $res, 1);
                        $shopIpList = explode(",", $data['shop_numbers']);
                        foreach ($shopIpList as $k=>$v)
                        {
                            $data['dataInfu'][$key] = array(
                                'door_id' => $data['id'],
                                'memberId' => $value['id'],
                                'memberName' => $value['name'],
                                'cardNo' => $value['card_number'],
                                'eqIp' => $v,
                                'time' => $data['week'],
                                'position_name' => $data['position_name'],
                                'state' => 0
                            );
                            $memModel->add($data['dataInfu'][$key]);
                        }
                    }
                    Ret(array('code' => 1, 'info' => '添加成功！'));
                }
                //将老的信息都删掉
                $oldPositionModel = D("InstPosition");
                $oldPositionName = explode(",", $oldShopRoleInfo[0]['position_name']);
                $oldNames = "";
                //var_dump($positionName);die;
                foreach ($oldPositionName as $key => $item) {
                    if (empty($item)) {
                        unset($oldPositionName[$key]);
                        continue;
                    }
                    $oldNames .= "'{$item}'" . ",";

                }
                $oldNames = rtrim($oldNames, ",");

                $oldPositionIds = $oldPositionModel->where("name in ({$oldNames})")->select();
                //var_dump($positionIds);die;
                //获取员工信息
                $oldCard = getStaffId($oldPositionIds, $oldShopRoleInfo[0]['sex']);
                if ($oldCard) {
                    $deleteModel = D('DoorAuthDelete');
                    foreach ($card as $key => $value) {
                        //$a = $this->findEqId($value['institution_id'], $res, 1);
                        $oldShopIpList = explode(",", $oldShopRoleInfo[0]['shop_numbers']);
                        foreach ($oldShopIpList as $k=>$v)
                        {
                            $data['dataInfu'][$key] = array(
                                'door_id' => $data['id'],
                                'memberId' => $value['id'],
                                'memberName' => $value['name'],
                                'cardNo' => $value['card_number'],
                                'eqIp' => $v,
                                'time' => $data['week'],
                                'position_name' => $data['position_name'],
                                'state' => 0
                            );
                            $deleteModel->add($data['dataInfu'][$key]);
                        }
                    }
                    Ret(array('code' => 1, 'info' => '添加成功！'));
                }

            } elseif ($data['type'] == 2)
            {//商铺
                $positionName = explode(",", $data['position_name']);
                $names = "";

                foreach ($positionName as $key => $item) {
                    if (empty($item)) {
                        unset($positionName[$key]);
                        continue;
                    }
                    $names .= "'{$item}'" . ",";

                }
                $names = rtrim($names, ",");

                $positionModel = D("MallPosition");
                $positionIds = $positionModel->where("name in ({$names})")->select();
                $card = getMerchant($positionIds, $data['sex']);

                if ($card) {
                    $shopArr = explode(",", $data['shop_numbers']);
                    foreach ($shopArr as $item) {
                        foreach ($card as $key => $value) {
                            $weekTime = json_decode($data['week'], 1);
                            $returnTime = $this->formatTime($value['card_number'], $weekTime[0], 2);
                            $positionName = $positionModel->where("id = {$value['position_id']}")->select();
//                            $a = $this->findEqIdbyPosition();
                            $data['dataInfu'][$key] = array(
                                'door_id' => $data['id'],
                                'memberId' => $value['id'],
                                'memberName' => $value['name'],
                                'cardNo' => $value['card_number'],
                                'eqIp' => $item,
                                'time' => $returnTime,
                                'position_name' => $positionName[0]['name'],
                                'state' => 0
                                //'door_id'=>$doorId
                            );
                            $m = D('MemberDoorAuth');
                            $m->add($data['dataInfu'][$key]);
                        }
                    }

                    Ret(array('code' => 1, 'info' => '添加成功！'));
                }
                //将老的信息都删掉
                $oldPositionModel = D("InstPosition");
                $oldPositionName = explode(",", $oldShopRoleInfo[0]['position_name']);
                $oldNames = "";
                //var_dump($positionName);die;
                foreach ($oldPositionName as $key => $item) {
                    if (empty($item)) {
                        unset($oldPositionName[$key]);
                        continue;
                    }
                    $oldNames .= "'{$item}'" . ",";

                }
                $oldNames = rtrim($oldNames, ",");

                $oldPositionIds = $oldPositionModel->where("name in ({$oldNames})")->select();
                //var_dump($positionIds);die;
                //获取员工信息
                $oldCard = getStaffId($oldPositionIds, $oldShopRoleInfo[0]['sex']);
                if ($oldCard) {
                    $deleteModel = D('DoorAuthDelete');
                    foreach ($card as $key => $value) {
                        //$a = $this->findEqId($value['institution_id'], $res, 1);
                        $oldShopIpList = explode(",", $oldShopRoleInfo[0]['shop_numbers']);
                        foreach ($oldShopIpList as $k=>$v)
                        {
                            $data['dataInfu'][$key] = array(
                                'door_id' => $data['id'],
                                'memberId' => $value['id'],
                                'memberName' => $value['name'],
                                'cardNo' => $value['card_number'],
                                'eqIp' => $v,
                                'time' => $data['week'],
                                'position_name' => $data['position_name'],
                                'state' => 0
                            );
                            $deleteModel->add($data['dataInfu'][$key]);
                        }
                    }
                    Ret(array('code' => 1, 'info' => '添加成功！'));
                }
            }
            elseif($data['type'] == 3)
            {
                $memberModel=D('Member');
                $card=$memberModel->where('sex='.$data['sex'])->select();
                if ($card) {
                    $shopArr = explode(",", $data['shop_numbers']);
                    foreach ($shopArr as $item) {
                        foreach ($card as $key => $value) {
                            $weekTime = json_decode($data['week'], 1);
                            $returnTime = $this->formatTime($value['card_number'], $weekTime[0], 2);
                            //$positionName = $positionModel->where("id = {$value['position_id']}")->select();
//                            $a = $this->findEqIdbyPosition();
                            $data['dataInfu'][$key] = array(
                                'door_id' => $data['id'],
                                'memberId' => $value['id'],
                                'memberName' => $value['name'],
                                'cardNo' => $value['card_number'],
                                'eqIp' => $item,
                                'time' => $returnTime,
                                //'position_name' => $positionName[0]['name'],
                                'state' => 0
                                //'door_id'=>$doorId
                            );
                            $m = D('MemberDoorAuth');
                            $m->add($data['dataInfu'][$key]);
                        }
                    }

                    Ret(array('code' => 1, 'info' => '添加成功！'));
                }
                $oldMemberModel=D('Member');
                $oldCard=$oldMemberModel->where('sex='.$oldShopRoleInfo[0]['sex'])->select();
                if ($oldCard) {
                    $shopArr = explode(",", $oldShopRoleInfo[0]['shop_numbers']);
                    foreach ($shopArr as $item) {
                        foreach ($card as $key => $value) {
                            $weekTime = json_decode($oldShopRoleInfo[0]['week'], 1);
                            $returnTime = $this->formatTime($value['card_number'], $weekTime[0], 2);
                            //$positionName = $positionModel->where("id = {$value['position_id']}")->select();
//                            $a = $this->findEqIdbyPosition();
                            $data['dataInfu'][$key] = array(
                                'door_id' => $oldShopRoleInfo[0]['id'],
                                'memberId' => $value['id'],
                                'memberName' => $value['name'],
                                'cardNo' => $value['card_number'],
                                'eqIp' => $item,
                                'time' => $returnTime,
                                //'position_name' => $positionName[0]['name'],
                                'state' => 0
                                //'door_id'=>$doorId
                            );
                            $m = D('DoorAuthDelete');
                            $m->add($data['dataInfu'][$key]);
                        }
                    }

                    Ret(array('code' => 1, 'info' => '删除成功！'));
                }

            }
        } else {
            Ret(array('code' => 2, 'info' => '添加失败,系统出错！'));
        }
    }


    /*
    * 门禁权限详情
    */
    public function detail()
    {
        before_api();

        checkLogin();

        checkAuth();
        $condition['id'] = I('id');
        if (!$condition['id']) {
            Ret(array('code' => 2, 'info' => '参数（id）有误！'));
        }
        if ($condition) {
            $data = D('ShopDoorRole')->where($condition)->find();
            $data['week'] = json_decode($data['week']);
            if ($data) {
                Ret(array('code' => 1, 'data' => $data));
            } else {
                Ret(array('code' => 2, 'info' => '查询失败,系统出错！'));
            }

        }
    }

    /*
    * 门禁权限删除
    */
    public function del()
    {
        before_api();

        checkLogin();

        checkAuth();
        $condition['id'] = I('id');
        if (!$condition['id']) {
            Ret(array('code' => 2, 'info' => '参数（id）有误！'));
        }
        if ($condition)
        {
            $shopRoleInfo=D('ShopDoorRole')->where($condition)->select();
            $deleteStatus = D('ShopDoorRole')->where($condition)->delete();
            if ($shopRoleInfo[0]['user_type'] == 1)
            {//机构
                $positionModel = D("InstPosition");
                $positionName = explode(",", $shopRoleInfo[0]['position_name']);
                $names = "";
                //var_dump($positionName);die;
                foreach ($positionName as $key => $item) {
                    if (empty($item)) {
                        unset($positionName[$key]);
                        continue;
                    }
                    $names .= "'{$item}'" . ",";

                }
                $names = rtrim($names, ",");

                $positionIds = $positionModel->where("name in ({$names})")->select();
                //var_dump($positionIds);die;
                //获取员工信息
                $card = getStaffId($positionIds, $shopRoleInfo[0]['sex']);
                //var_dump($card);die;
                if ($card) {
                    foreach ($card as $key => $value) {
                        //$a = $this->findEqId($value['institution_id'], $res, 1);
                        $shopIpList = explode(",", $shopRoleInfo[0]['shop_numbers']);
                        $weekTime = json_decode($shopRoleInfo[0]['week'], 1);
                        $returnTime = $this->formatTime($value['card_number'], $weekTime[0], 1);
                        $positionName = $positionModel->where("id = {$value['position_id']}")->select();
                        foreach ($shopIpList as $k => $v) {
                            $data['dataInfu'][$key] = array(
                                'door_id' => $shopRoleInfo[0]['id'],
                                'memberId' => $value['id'],
                                'memberName' => $value['name'],
                                'cardNo' => $value['card_number'],
                                'eqIp' => $v,
                                'time' => $returnTime,
                                'position_name' => $positionName[0]['name'],
                                'state' => 0,
                                //'door_id'=>$doorId
                            );
                            $m = D('DoorAuthDelete');
                            $m->add($data['dataInfu'][$key]);
                        }

                    }
                    Ret(array('code' => 1, 'info' => '删除成功！'));
                }
            }
            elseif($shopRoleInfo[0]['user_type'] == 2)
            {
                $positionName = explode(",", $shopRoleInfo[0]['position_name']);
                $names = "";

                foreach ($positionName as $key => $item) {
                    if (empty($item)) {
                        unset($positionName[$key]);
                        continue;
                    }
                    $names .= "'{$item}'" . ",";

                }
                $names = rtrim($names, ",");

                $positionModel = D("MallPosition");
                $positionIds = $positionModel->where("name in ({$names})")->select();
                $card = getMerchant($positionIds, $shopRoleInfo[0]['sex']);

                if ($card) {
                    $shopArr = explode(",", $shopRoleInfo[0]['shop_numbers']);
                    foreach ($shopArr as $item) {
                        foreach ($card as $key => $value) {
                            $weekTime = json_decode($shopRoleInfo[0]['week'], 1);
                            $returnTime = $this->formatTime($value['card_number'], $weekTime[0], 2);
                            $positionName = $positionModel->where("id = {$value['position_id']}")->select();
//                            $a = $this->findEqIdbyPosition();
                            $data['dataInfu'][$key] = array(
                                'door_id' => $shopRoleInfo[0]['id'],
                                'memberId' => $value['id'],
                                'memberName' => $value['name'],
                                'cardNo' => $value['card_number'],
                                'eqIp' => $item,
                                'time' => $returnTime,
                                'position_name' => $positionName[0]['name'],
                                'state' => 0
                                //'door_id'=>$doorId
                            );
                            $m = D('DoorAuthDelete');
                            $m->add($data['dataInfu'][$key]);
                        }
                    }

                    Ret(array('code' => 1, 'info' => '删除成功！'));
                }
            }
            elseif($shopRoleInfo[0]['user_type'] == 3)
            {
                $memberModel=D('Member');
                $card=$memberModel->where('sex='.$shopRoleInfo[0]['sex'])->select();
                if ($card) {
                    $shopArr = explode(",", $shopRoleInfo[0]['shop_numbers']);
                    foreach ($shopArr as $item) {
                        foreach ($card as $key => $value) {
                            $weekTime = json_decode($shopRoleInfo[0]['week'], 1);
                            $returnTime = $this->formatTime($value['card_number'], $weekTime[0], 2);
                            //$positionName = $positionModel->where("id = {$value['position_id']}")->select();
//                            $a = $this->findEqIdbyPosition();
                            $data['dataInfu'][$key] = array(
                                'door_id' => $shopRoleInfo[0]['id'],
                                'memberId' => $value['id'],
                                'memberName' => $value['name'],
                                'cardNo' => $value['card_number'],
                                'eqIp' => $item,
                                'time' => $returnTime,
                                //'position_name' => $positionName[0]['name'],
                                'state' => 0
                                //'door_id'=>$doorId
                            );
                            $m = D('DoorAuthDelete');
                            $m->add($data['dataInfu'][$key]);
                        }
                    }

                    Ret(array('code' => 1, 'info' => '删除成功！'));
                }

            }

        }
    }

    /*
        * 机构职位
        */
    public function get_inst_postion()
    {
        before_api();

        checkLogin();

        checkAuth();
        $id = I('id');
        switch ($id) {
            case 1;
                $data = D('InstPosition')->field('id,name')->select();
                if ($data) {
                    Ret(array('code' => 1, 'data' => $data));
                } else {
                    Ret(array('code' => 2, 'info' => '添加失败,系统出错！'));
                }
                break;

            case 2;
                $data = D('MallPosition')->field('id,name')->select();
                if ($data) {
                    Ret(array('code' => 1, 'data' => $data));
                } else {
                    Ret(array('code' => 2, 'info' => '添加失败,系统出错！'));
                }
                break;

            case 3;
                $data = D('MerchantPosition')->field('id,name')->select();
                if ($data) {
                    Ret(array('code' => 1, 'data' => $data));
                } else {
                    Ret(array('code' => 2, 'info' => '添加失败,系统出错！'));
                }
                break;
        }

    }

    /*
    * 门禁权限列表
    */
    public function shop_door_list()
    {
        checkLogin();
        before_api();
        checkAuth();

        $page = I('page', 1, 'intval');
        $pagesize = I('pagesize', 10, 'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page . ',' . $pagesize;

        $classDiscountModel = D('ShopDoorRole');

        $count = $classDiscountModel->count();
        //var_dump($count);die;

        $data = $classDiscountModel->page($page)->select();

        $weekNo2W = array(0 => '星期日', 1 => '星期一', 2 => '星期二', 3 => '星期三', 4 => '星期四', 5 => '星期五', 6 => '星期六');

        foreach ($data as $k => $v) {
            $weekTime = json_decode($v['week']);
            $wstr = '';
            foreach ($weekTime as $key => $value) {
                $w = get_object_vars($value);
                $time = $w['time'];
                $tstr = '';
                foreach ($time as $k1 => $v1) {
                    $t = get_object_vars($v1);
                    $tstr = $tstr . '从：' . $t['time_start'] . '到' . $t['time_end'];
                }
                $wstr[$k] = $wstr[$k] . '从：' . $weekNo2W[$w['week_start']] . '到' . $weekNo2W[$w['week_end']] . $tstr;
            }
            $data[$k]['week'] = implode(';', $wstr);
        }

        if ($data) {
            Ret(array('code' => 1, 'data' => $data, 'total' => $count, 'page_count' => ceil($count / $pagesize)));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据！'));
        }
    }
}
