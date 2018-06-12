<?php
namespace Common\Model;
use Think\Model;
class IndustryModel extends Model {
	protected $trueTableName = 'industry';

	public function add_industry($data){
		
		return $this->add($data);
	}

	public function check_industry($industry_name){
		if (empty($industry_name)) {
			return false;
		}
		if ($this->where(array('name' => $industry_name))->find()) {
			return true;
		}else{
			return false;
		}
	}

	public function get_list($classify_id){
		return $this->field('id,name')->where(array('classify_id' => $classify_id))->select();
	}
}