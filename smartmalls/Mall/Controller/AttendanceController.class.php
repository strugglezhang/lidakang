<?php
/**
 * Created by PhpStorm.
 * User: qs
 * Date: 2017/11/12
 * Time: 13:35
 */

namespace Mall\Controller;

use Think\Controller;

class AttendanceController extends CommonController
{
    public function Attendance()
    {
        $mallId = I('mallId');
        $eqip = I('eqip');
        $carno = I('carno');
        $time = I('time');

        if (empty($mallId)){
            Ret(array('code' => 2, 'info' => '商场Id不能为空！'));
        }

        if (empty($eqip)){
            Ret(array('code' => 3, 'info' => '设备ip不能为空！'));
        }

        if (empty($carno)){
            Ret(array('code' => 4, 'info' => '卡号不能为空！'));
        }

        if (empty($time)){
            Ret(array('code' => 5, 'info' => '打卡时间不能为空！'));

        }

        // 获取商户位置
        $equipment = D('equipment');
        $position = $equipment->where(['equip_ip' => $eqip])->getField('position');

        // 通过卡号判断是谁
        $shopObj = D('shop');
        $shopInfo = $shopObj->where(['position' => $position])->select();
        $type = 1;
        if (empty($shopInfo)) {
            $type = 2;
            $roomObj = D('room');
            $roomInfo = $roomObj->where(['position' => $position])->select();
            if (empty($roomInfo)) {
                Ret(array('code' => 5, 'info' => '商铺不存在！'));
            }
        }

        // 判断是什么用户
        $typeArr = [1 => 'member', 2 => 'institution_staff', 3 => 'mall_staff', 4 => 'merchant_staff'];
        $roomType = 1;
        foreach ($typeArr as $key => $v) {
            $member = D($v);
            $obj = $member->where(['card_number' => $carno])->find();
            if (!empty($obj)) {
                $roomType = $key;
                break;
            }
        }

        $all_attendance_detail = D('all_attendance_detail');
        $date = date("Y-m-d H:i:s");
        $result['type'] = $type;
        $result['time'] = $time;
        $result['mallId'] = $mallId;
        $result['create_time'] = $date;
        $result['ip'] = $eqip;
        $result['carno'] = $carno;
        $result['roomtype'] = $roomType;
        $result['position'] = $position;
        if ($all_attendance_detail->add($result)) {
            Ret(array('code' => 5, 'info' => '打卡成功！'));
        }
        Ret(array('code' => 5, 'info' => '打卡失敗！'));
    }


}
