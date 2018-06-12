<?php
namespace Common\Model;
use Think\Model;
class NaviModel extends Model {
	protected $tableName= 'navi';

	public function getFirstMenu($where)
	{
		return $this->field('first_level')->where($where)->select();
	}
	
	public function getSecondMenu($firstMenu)
	{
		return $this->field('second_level')->where(array('first_level'=>$firstMenu))->select();
	}
	
	public function getTherdMenu($secondMenu)
	{
		return $this->field('therd_level')->where(array('second_level'=>$secondMenu))->select();
	}

    public function getMenuById($uniqId)
    {
        return $this->field('id')->where(array('id' => $uniqId))->select();
	}

	public function updateMenuByRoleId($roleId,$menuData){

	    return $this->where(array('id' => $roleId))->save($menuData);
    }

    public function addMenuByRoleId($menuData)
    {
        return $this->add($menuData);
    }
}
