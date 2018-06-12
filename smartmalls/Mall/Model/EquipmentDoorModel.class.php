<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/7/29
 * Time: 10:35
 */
namespace Mall\Model;
use Think\Model;
class EquipmentDoorModel extends Model{
    protected $tableName = 'equipment';
    public function getInfo(){
        //$condition['equip_type']='门禁设备';
        return $this->field('equip_id,equip_type,bond_state,position')->select();
    }

    public function getUnusedEqList()
    {
        $m=new Model();
        $sql="select * from equipment where ISNULL(shop_id) and equip_type='门禁设备'";
        return $m->query($sql);
    }
    public function getEqInfoByEqId($id)
    {
        return $this->where('equip_id='.$id)->select();
    }
    public function isUsedIp($ip)
    {
        $m=new Model();
        $sql="select * from equipment where equip_ip='".$ip."'";
        $res=$m->query($sql);
        if($res)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    public function hasUsedEq($eqIdList)
    {
        foreach ($eqIdList as $key=>$value)
        {
            $eqInfo=$this->getEqInfoByEqId($value);
            if($eqInfo[0]['bond_state']==1)
            {
                return true;
            }
        }
        return false;
    }

    public function getEqIpList($eqIdList)
    {
        $data=array();
        foreach ($eqIdList as $key=>$value)
        {
            $eqInfo=$this->getEqInfoByEqId($value);
            $data[$key]=$eqInfo[0]['equip_ip'];
        }
        return $data;
    }

    public function updateEquipInfo($data)
    {

        $equipMentModel = D("EquipmentDoor");
        $insertEquipMentData = array(
           // "equip_type" => I('equip_type'),
             "owner_type" => $data['ownerType'],
             "owner_name" => $data['ownerName'],
             //"room_type" => $data['roomType'],
             "bond_state" => $data['bondState'],
             //"position" => $data['position'],
            //"equip_ip" => I('equip_id'),
            //"equip_name"=>I('equip_name'),
            //"position" => I('equip_name')
             "shop_id" => $data['shop_id']
        );
        //$id=I('')
        //update
        //var_dump($data);die;
        $id=$data['equipId'];
        if (isset($id)) {
            $equipMentModel->where("equip_id=".$id)->save($insertEquipMentData);
            return true;
        }
        else
        {
            return false;
        }
        /*//add
        $equipMentModel->data($insertEquipMentData)->add();
        Ret(array('code' => 1, 'data' => [], 'info' => "success"));*/
    }
    public function getEqListByShopId($shopId)
    {
        return $this->where('shop_id='.$shopId)->select();
    }
    public function getEqPosList($eqIdList)
    {
        $data=array();
        foreach ($eqIdList as $key=>$value)
        {
            $eqInfo=$this->getEqInfoByEqId($value);
            $data[$key]=$eqInfo[0]['position'];
        }
        return $data;
    }
}