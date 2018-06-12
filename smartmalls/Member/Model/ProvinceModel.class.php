<?php
namespace Member\Model;
use Think\Model;
class ProvinceModel extends Model {
	protected $trueTableName = 'provinces';

	public function get_list(){
		return $this->select();
	}

	public function get_addr($province_id){
		return $this->field('id,name')->where(array('id' => $province_id))->select();
	}
}