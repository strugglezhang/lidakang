<?php
namespace Member\Model;
use Think\Model;
class DistrictModel extends Model {
	protected $trueTableName = 'districts';

	public function get_list($city_id){
		return $this->field('id,name')->where(array('city_id' => $city_id))->select();
	}

	public function get_addr($district_id){
		return $this->field('id,name')->where(array('id' => $district_id))->select();
	}
}