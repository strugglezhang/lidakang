<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/7/20
 * Time: 18:04
 */
namespace Merchant\Model;
use Think\Model;
class MerchantCheckLogModel extends Model{
    protected $tableName='merchant_check_log';
    public function get_count($keyword){
        if (!empty($keyword)) {
            if (preg_match("/^\d*$/", $keyword)) {
                $condition['id'] = $keyword;
            } else {
                $condition['merchant'] = array('LIKE', '%' . $keyword . '%');
            }
        }
        return $this->where($condition)->count();
    }
    public function get_list($keyword,$page,$fields){
        if (!empty($keyword)) {
            if (preg_match("/^\d*$/", $keyword)) {
                $condition['id'] = $keyword;
            } else {
                $condition['merchant'] = array('LIKE', '%' . $keyword . '%');
            }
        }
        return $this->where($condition)->page($page)->select();
    }

        public function add_log($data){
            return $this->add($data);
        }

    public function insertLog($data){
        return $this->add($data);
    }

}