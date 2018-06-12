<?php
namespace Common\Model;
use Think\Model;
class ProvinceModel extends Model {
	protected $trueTableName = 'provinces';

	public function get_list(){
		return $this->select();
	}
}