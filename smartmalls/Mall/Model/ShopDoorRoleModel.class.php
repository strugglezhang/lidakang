<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/7/27
 * Time: 14:51
 */
namespace Mall\Model;
use Think\Model;
class ShopDoorRoleModel extends Model{
    protected $tableName='shop_door_role';

    public function searchTheRepeatedData(array $data)
    {
        $where = array(
            'shop_ids' => $data['shop_ids'],
            'position' => $data['position']
        );
        return $this->field('id')->where($where)->select();
    }
}