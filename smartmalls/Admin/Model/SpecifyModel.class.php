<?php
namespace Common\Model;
use Think\Model;
class SpecifyModel extends Model {
	protected $trueTableName = 'specify';

	public function add_specify($data){
		
		return $this->add($data);
	}

	public function check_specify($specify_name){
		if (empty($specify_name)) {
			return false;
		}
		if ($this->where(array('name' => $specify_name))->find()) {
			return true;
		}else{
			return false;
		}
	}

	public function get_list($industry_id){
		return $this->field('id,name')->where(array('industry_id' => $industry_id))->select();
	}
}