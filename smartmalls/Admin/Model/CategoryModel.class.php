<?php
namespace Common\Model;
use Think\Model;
class CategoryModel extends Model {
	protected $trueTableName = 'category';

	public function add_category($data){
		
		return $this->add($data);
	}

	public function check_category($category_name){
		if (empty($category_name)) {
			return false;
		}
		if ($this->where(array('name' => $category_name))->find()) {
			return true;
		}else{
			return false;
		}
	}

	public function get_list(){
		return $this->select();
	}
}