<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/7/26
 * Time: 11:34
 */

namespace Mall\Controller;

class EquipmentController extends CommonController
{
    //教室监控权限添加
    public function room_moniter_role()
    {
        checkLogin();
        before_api();
        checkAuth();

        $classDiscountModel = D('EquipmentMoniter');
        $data['type'] = I('type');
        $data['category_ids'] = I('category_ids');
        $data['room_ids'] = I('room_ids');
        $data['free_type'] = I('free_type');
        if ($data['free_type'] == null) {
            Ret(array('code' => 2, 'info' => '参数（free_type）错误!'));
        }
        $data['free_time'] = I('free_time');
        $result = $classDiscountModel->add($data);
        if ($result) {
            Ret(array('code' => 1, 'info' => '添加成功'));
        } else {
            Ret(array('code' => 2, 'info' => '添加失败，系统出错'));
        }
    }

//教室监控权限更新
    public function room_moniter_role_update()
    {
        checkLogin();
        before_api();
        checkAuth();

        $classDiscountModel = D('EquipmentMoniter');

        $data['id'] = I('id');
        if ($data['id'] == null) {
            Ret(array('code' => 2, 'info' => '参数（id）错误!'));
        }
        $data['type'] = I('type');
        $data['category_ids'] = I('category_ids');
        $data['room_ids'] = I('room_ids');
        $data['free_type'] = I('free_type');
        if ($data['free_type'] == null) {
            Ret(array('code' => 2, 'info' => '参数（free_type）错误!'));
        }
        $data['free_time'] = I('free_time');
        $result = $classDiscountModel->save($data);
        if ($result) {
            Ret(array('code' => 1, 'info' => '更新成功'));
        } else {
            Ret(array('code' => 2, 'info' => '更新失败，系统出错'));
        }
    }

//教室监控权限列表
    public function room_moniter_role_list()
    {
        checkLogin();
        before_api();
        checkAuth();

        $page = I('page', 1, 'intval');
        $pagesize = I('pagesize', 10, 'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page . ',' . $pagesize;

        $classDiscountModel = D('EquipmentMoniter');

        $count = $classDiscountModel->count();
        $data = $classDiscountModel->page($page)->select();

        foreach ($data as $k => $v) {
            $data[$k]['category_name'] = $this->get_cat_by_id($v['category_ids']);
            $data[$k]['class_number'] = $this->get_room_by_id($v['room_ids']);
        }

        if ($data) {
            Ret(array('code' => 1, 'data' => $data, 'total' => $count, 'page_count' => ceil($count / $pagesize)));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据！'));
        }
    }

    private function get_cat_by_id($id)
    {
        $array = explode('-', $id);
        foreach ($array as $k => $v) {
            $item = D('InstCat')->get_category($v);
            if ($item[0][name] != null) {
                $items[$k] = $item[0][name];
            }
            $data = implode(',', $items);
        }
        return $data;
    }

    private function get_room_by_id($id)
    {

        $array = explode('-', $id);
        foreach ($array as $k => $v) {
            $item = D('Class')->get_room($v);
            //var_dump($item);die;
            if ($item[0][position] != null) {
                $items[$k] = $item[0][position];
            }
            $data = implode(',', $items);
        }
        return $data;
    }

//教室监控权限详情
    public function room_moniter_role_detail()
    {
        checkLogin();
        before_api();
        checkAuth();
        $condition['id'] = I('id');
        $classDiscountModel = D('EquipmentMoniter');
        $data = $classDiscountModel->where($condition)->select();

        foreach ($data as $k => $v) {
            $data[$k]['category_name'] = $this->get_cat_by_id($v['category_ids']);
            $data[$k]['class_number'] = $this->get_room_by_id($v['room_ids']);
        }

        if ($data) {
            Ret(array('code' => 1, 'data' => $data));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据！'));
        }
    }

    //教室监控权限删除
    public function room_moniter_role_delete()
    {
        checkLogin();
        before_api();
        checkAuth();
        $condition['id'] = I('id');
        $classDiscountModel = D('EquipmentMoniter');
        $data = $classDiscountModel->where($condition)->delete();

        if ($data) {
            Ret(array('code' => 1, 'info' => '删除成功'));
        } else {
            Ret(array('code' => 2, 'info' => '删除失败，系统错误！'));
        }
    }

    //教室门禁体添加
    public function class_room_door_add()
    {
        checkLogin();
        before_api();
        checkAuth();
        $params = I("data");
        $params['id'] = 2;
        foreach ($params as $k => $v) {
            if (empty($v)) {
                Ret(array('code' => 2, 'info' => "{$k}不能为空"));
            }
        }

        $data['type'] = $params['type'];
        $data['shop_ids'] = $params['classroom_id'];
        $data['position'] = $params['position_id'];
        $mall_staff = D("mall_staff");

        $arr = [1 => 'InstPosition', 2 => '', 3 => 'MerchantPosition'];

        $mall_id = session('mall.mall_id');
        $classroomids = explode("-", $data['shop_ids']);        //房间
        $roleids = explode("-", $data['position']);             //角色
        $authDoor = D("member_door_auth");
        $shopNumber = '';
        $positionNames = '';
        foreach ($classroomids as $classroomid) {
            $cat = D('Class')->where("mall_id='{$mall_id}' and id='{$classroomid}'")->field("equip_ip,position")->find();
            $equip_ip = $cat['equip_ip'];
            $position = $cat['position'];
            $shopNumber .= $position . ",";
            foreach ($roleids as $roleid) {
                $mallPositon = D('MallPosition')->where("id='{$roleid}'")->getField("name");
                $stallInfo = $mall_staff->where("position_name='{$mallPositon}'")->field("id,name,card_number,join_time,contract_time")->find();
                $card_number = $stallInfo['card_number'];
                $name = $stallInfo['name'];
                $id = $stallInfo['id'];
                $join_time = $stallInfo['join_time'];
                $contract_time = $stallInfo['contract_time'];
                $time = $this->formatTimetest($join_time, $contract_time);

                $insert['eqIp'] = $equip_ip;
                $insert['cardNo'] = $card_number;
                $insert['memberId'] = $id;
                $insert['memberName'] = $name;
                $insert['time'] = $time;
                $authDoor->data($insert)->add();
            }
        }


        foreach (explode("-", $data['position']) as $value) {
            $positionNames .= D("MallPosition")->where('id=' . $value)->getField("name") . "-";
        }

        $data['shop_numbers'] = trim($shopNumber, ",");
        $data['position_name'] = trim($positionNames, "-");

        // 用户信息
        if ($data) {
            $res = D('ClassRoomDoor')->add($data);
            if ($res) {
                Ret(array('code' => 1, 'info' => '添加成功！'));
            } else {
                Ret(array('code' => 2, 'info' => '添加失败,系统出错！'));
            }
        }

    }


    private function formatTimetest($startTime, $endTime)
    {
        //0,0:00-23:59;1,0:00-23:59;2,0:00-23:59;3,0:00-23:59;4,0:00-23:59;5,0:00-23:59;6,0:00-23:59
        $str = "";
        $sTime = explode(" ", $startTime);
        $eTime = explode(" ", $endTime);
        $str .= $sTime[0] . "," . $eTime[0] . "#" . rand(1, 1000) . "]";
        for ($i = 0; $i <= 6; $i++) {
            $str .= "$i,0:00-23:59;";
        }
        return trim($str, ";");

    }

    /*
     * 教室门禁权限新增
     */
    public function class_room_door_update()
    {
        before_api();

        checkLogin();

        checkAuth();
        $data['id'] = I('id');
        if (!$data['id']) {
            Ret(array('code' => 2, 'info' => '参数（id）有误！'));
        }
        $data['type'] = I('type');
        $data['shop_ids'] = I('shop_ids');
        $data['shop_numbers'] = I('shop_numbers');
        $data['roomCatId'] = I('roomCatId');
        $data['roomCatName'] = I('roomCatName');
        $data['user_type'] = I('user_type');
        $data['position'] = I('position');
        $data['position_name'] = I('position_name');
        $data['time'] = I('time');
        if ($data) {
            $res = D('ClassRoomDoor')->save($data);
            if ($res) {
                Ret(array('code' => 1, 'info' => '修改成功！'));
            } else {
                Ret(array('code' => 2, 'info' => '修改失败,系统出错！'));
            }
        }
    }

    /*
     * 教室门禁权限详情
     */
    public function class_room_door_detail()
    {
        before_api();

        checkLogin();

        checkAuth();
        $condition['id'] = I('id');
        if (!$condition['id']) {
            Ret(array('code' => 2, 'info' => '参数（id）有误！'));
        }
        if ($condition) {
            $data = D('ClassRoomDoor')->where($condition)->find();
            $type = array('1' => '教室类别', '2' => '教室编号');
            $data['type'] = $type[$data['type']];
            if ($data) {
                Ret(array('code' => 1, 'data' => $data));
            } else {
                Ret(array('code' => 2, 'info' => '查询失败,系统出错！'));
            }

        }
    }

    /*
     * 教室门禁权限删除
     */
    public function class_room_door_del()
    {
        before_api();

        checkLogin();

        checkAuth();
        $condition['id'] = I('id');
        if (!$condition['id']) {
            Ret(array('code' => 2, 'info' => '参数（id）有误！'));
        }
        if ($condition) {
            $data = D('ClassRoomDoor')->where($condition)->delete();
            if ($data) {
                Ret(array('code' => 1, 'info' => '删除成功'));
            } else {
                Ret(array('code' => 2, 'info' => '删除失败,系统出错！'));
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

//教室门禁列表
    public function class_room_door_list()
    {
        checkLogin();
        before_api();
        checkAuth();

        $page = I('page', 1, 'intval');
        $pagesize = I('pagesize', 10, 'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page . ',' . $pagesize;

        $classDiscountModel = D('ClassRoomDoor');

        $count = $classDiscountModel->count();
        $data = $classDiscountModel->page($page)->select();
        $type = array('1' => '教室类别', '2' => '教室编号');

        foreach ($data as $k => $v) {
            $data[$k]['type'] = $type[$v['type']];

        }

        if ($data) {
            Ret(array('code' => 1, 'data' => $data, 'total' => $count, 'page_count' => ceil($count / $pagesize)));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据！'));
        }
    }

    public function getEquipmentList()
    {
        $equipMentModel = D("EquipmentDoor");
        Ret(array('code' => 1, 'data' => $equipMentModel->field("*")->select(), 'info' => "success"));
    }

    public function updateEquip()
    {

        $equipMentModel = D("EquipmentDoor");
        $eqIp=I('equip_id');
        if(!is_ip($eqIp))
        {
            Ret(array('code' => 2, 'info' => 'IP的格式错误！'));
            die;
        }
        $insertEquipMentData = array(
            "equip_type" => I('equip_type'),
           /* "owner_type" => $data['ownerType'],
            "owner_name" => $data['ownerName'],
            "room_type" => $data['roomType'],
            "bond_state" => $data['bondState'],
            "position" => $data['position'],*/
            "equip_ip" => $eqIp,
            "equip_name"=>I('equip_name'),
            "position" => I('equip_name')
            //"shop_id" => $data['shopId'],
        );
        //$id=I('')
        //update
        $id=I('id');
        if (isset($id)) {
            $equipMentModel->where("equip_id={$id}")->save($insertEquipMentData);
            Ret(array('code' => 1, 'data' => [], 'info' => "success"));
        }
        //add
        $equipMentModel->data($insertEquipMentData)->add();
        Ret(array('code' => 1, 'data' => [], 'info' => "success"));
    }

    public function deleteEquip()
    {
        $eqId = I("id");
        if (!empty($eqId)) {
            $equipMentModel = D("EquipmentDoor");
            //获取要删除设备所绑定的shop_id;
            $eqInfo=$equipMentModel->getEqInfoByEqId($eqId);
            $equipMentModel->where("equip_id=".$eqId)->delete();
            //重置商铺的绑定设备ID和IP
            if(!empty($eqInfo[0]['shop_id'])) {
                $eqList = $equipMentModel->getEqListByShopId($eqInfo[0]['shop_id']);
                if ($eqList) {
                    $shopEqIdList = array();
                    $shopEqIpList = array();
                    foreach ($eqList as $key => $value) {
                        $shopEqIdList[$key] = $value['equip_id'];
                        $shopEqIpList[$key] = $value['equip_ip'];
                    }
                    $shopEqId = implode(',', $shopEqIdList);
                    $shopEqIp = implode(',', $shopEqIpList);
                    D('Shop')->updateShopEqInfo($shopEqId, $shopEqIp, $eqInfo[0]['shop_id']);
                } else {
                    $shopEqId = '';
                    $shopEqIp = '';
                    D('Shop')->updateShopEqInfo($shopEqId, $shopEqIp, $eqInfo[0]['shop_id']);
                }
            }
            Ret(array('code' => 1, 'data' => [], 'info' => "success"));
        }
        else
        {
            Ret(array('code' => 2, 'info' => 'no query data！'));
        }

    }


}