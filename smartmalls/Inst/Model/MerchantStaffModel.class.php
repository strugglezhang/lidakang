<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/8/20
 * Time: 19:57
 */
namespace Inst\Model;
use Think\Model;

class MerchantStaffModel extends Model{
    protected $tableName='merchant_staff';
    public function get_info_by_id($staffId)
    {
        return $this->where('id='.$staffId)->find();
    }


}
