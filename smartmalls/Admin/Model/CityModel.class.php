<?php
namespace Common\Model;
use Think\Model;
class CityModel extends Model {
	protected $trueTableName = 'city';

	public function get_list($province_id){
		return $this->field('id,name')->where(array('province_id' => $province_id))->select();
	}
}