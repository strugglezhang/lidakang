<?php
namespace Common\Model;
use Think\Model;
class ClassifyModel extends Model {
	protected $trueTableName = 'classify';

	public function add_classify($data){
		
		return $this->add($data);
	}

	public function check_classify($classify_name){
		if (empty($classify_name)) {
			return false;
		}
		if ($this->where(array('name' => $classify_name))->find()) {
			return true;
		}else{
			return false;
		}
	}

	public function get_list($category_id){
		return $this->field('id,name')->where(array('category_id' => $category_id))->select();
	}
}