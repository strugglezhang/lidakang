<?php
namespace Common\Model;
use Think\Model;
class DistrictModel extends Model {
	protected $trueTableName = 'districts';

	public function get_list($city_id){
		return $this->field('id,name')->where(array('city_id' => $city_id))->select();
	}
}