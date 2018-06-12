<?php
namespace Member\Model;
use Think\Model;
class CityModel extends Model {
	protected $trueTableName = 'city';

	public function get_list($province_id){
		return $this->field('id,name')->where(array('province_id' => $province_id))->select();
	}
	public function get_addr($city_id){
		return $this->field('id,name')->where(array('id' => $city_id))->select();
	}
}