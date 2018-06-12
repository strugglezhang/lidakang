<?php
namespace Merchant\Model;
use Think\Model;
class DeptModel extends Model {
	protected $trueTableName = 'mall_dept';

	public function add_dept($data){
		
		return $this->add($data);
	}

	public function check_dept_by_name($mall_id,$dept_name){
		if (empty($mall_id) || empty($dept_name)) {
			return false;
		}
		if ($this->where(array('mall_id' => $mall_id, 'name' => $dept_name))->find()) {
			return true;
		}else{
			return false;
		}
	}

	public function check_dept_by_id($mall_id,$dept_id){
		if (empty($mall_id) || empty($dept_id)) {
			return false;
		}
		if ($this->where(array('mall_id' => $mall_id, 'id' => $dept_id))->find()) {
			return true;
		}else{
			return false;
		}
	}

	public function get_dept_name_by_id($dept_id){
		return $this->where('id='.$dept_id)->field('id,name')->select();
	}

	public function get_list($mall_id){
		return $this->field('id,name')->where(array('mall_id' => $mall_id))->select();
	}
}