<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/7/22
 * Time: 22:47
 */
namespace Inst\Model;
use Think\Model;
class ServiceCheckLogModel extends Model{
    protected $tableName='service_check_log';
    public function get_list($keyword,$page = '1,10'){
        if (!empty($keyword)) {
            if (preg_match("/^\d*$/", $keyword)) {
                $condition['id'] = $keyword;
            } else {
                $condition['service_name'] = array('LIKE', '%' . $keyword . '%');
            }
        }
        return $this->field('id,service_name,check_type,submitter,checker,check_state,service_category,service_subcategory,serve_id,check_time')->where($condition)->page($page)->select();
    }
    public function get_count($keyword){
        if (!empty($keyword)) {

            if (preg_match("/^\d*$/", $keyword)) {
                $condition['id'] = $keyword;
            } else {
                $condition['service_name'] = array('LIKE', '%' . $keyword . '%');
            }
        }
        return $this->where($condition)->count();
    }

    public function insertLog($data){
        return $this->add($data);
    }
    public function add_log($log){
        return $this->add($log);
    }

}