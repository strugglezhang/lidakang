<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/8/22
 * Time: 18:12
 */
namespace Inst\Model;
use Think\Model;
class EquipmentModel extends Model{
    protected $tableName = 'equipment';
    public function getEqInfoByIp($ip)
    {
        $m=new Model();
        $sql="select * from equipment where equip_ip='".$ip."'";
        $res=$m->query($sql);
        return $res;
    }
}