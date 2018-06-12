<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/7/27
 * Time: 16:19
 */

namespace Mall\Model;
use Think\Model;
class MallPositionModel extends Model {
    protected $trueTableName = 'mall_position';

    public function add_position($data){

        return $this->add($data);
    }

    public function check_position_by_name($mall_id,$dept_id,$position_name){
        if (empty($mall_id) || empty($position_name)) {
            return false;
        }
        if ($this->where(array('mall_id' => $mall_id, 'dept_id' => $dept_id, 'name' => $position_name))->find()) {
            return true;
        }else{
            return false;
        }
    }
    public function get_position_name_by_id($position_id){
        return $this->where(array('id' => $position_id))->getField('name');
    }

    public function get_list($mall_id,$dept_id){
        if (empty($mall_id) || empty($dept_id)) {
            return false;
        }
        return $this->field('id,name')->where(array('mall_id' => $mall_id, 'dept_id' => $dept_id))->select();
    }
}