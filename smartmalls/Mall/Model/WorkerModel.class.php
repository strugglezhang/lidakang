<?php
namespace Mall\Model;
use Think\Model;
class WorkerModel extends Model {
	protected $trueTableName = 'mall_staff';
/*
state:员工状态（1：未审核，2在职(已审核)，3冻结，4已离职，5已删除）

*/

	public function loginByPassword($username,$password){
			$password = createPassword($password);
			$con['password'] = $password;
			$con['number'] = $username;
			return $this->where($con)->find();
	}
	public function loginByToken($username,$token){
		$con['token'] = $token;
		$con['number'] = $username;
		return $this->where($con)->find();
	}
	public function add_worker($data){
		return $this->add($data);
	}
	public function checkAuth($worker_id,$mall_id,$fields = 'id'){
		$where['id'] = $worker_id;
		$where['mall_id'] = $mall_id;
		$res = $this->field($fields)->where($where)->find();
		return empty($res) ? false : $res;
	}

	public function update_worker($data){

		return $this->save($data);
	}

	public function delete_worker($worker_id){

		return $this->where('id='.$worker_id)->delete();
	}

	// public function checkUsername($username,$fields = 'id'){
	// 	return $this->field($fields)->where(array('username' => $username))->find();
	// }

	public function checkPhone($phone,$fields = 'id'){
		return $this->field($fields)->where(array('phone' => $phone))->find();
	}

	public function get_worker_infos($worker_id,$fields = null){
		if ($fields === null) {
			return $this->field('password',true)->where('id='.$worker_id)->find();
		}else{
			return $this->field($fields)->where('id='.$worker_id)->find();
		}
	}

	public function get_count($mall_id, $state = 0, $dept_id = 0, $keyword = null){
		if($mall_id) {
			$where['mall_id'] = $mall_id;
		}
		if ($state !== 0) {
			$where['state'] = $state;
		}
		if ($dept_id !== 0) {
			$where['dept_id'] = $dept_id;
		}

		if (!empty($keyword)) {
			if(is_numeric($keyword)){
				$where['number|cardNumber_NO'] = $keyword;
			}else{
				$where['name'] = array('LIKE','%'.$keyword.'%');
			}
		}
		return $this->where($where)->count();
}
    public function get_list($mall_id, $state = 0, $dept_id = 0, $keyword = null, $page = '1,10',$fields = null){
		if($mall_id){
			$where['mall_id'] = $mall_id;
		}
		if ($state !== 0) {
			$where['state'] = $state;
		}
		if ($dept_id !== 0) {
			$where['dept_id'] = $dept_id;
		}
		if (!empty($keyword)) {
			if(is_numeric($keyword)){
				$where['number|cardNumber_NO'] = $keyword;
			}else{
				$where['name'] = array('LIKE','%'.$keyword.'%');
			}
		}
		if ($fields === null) {
			return $this->field('password',true)->where($where)->page($page)->select();
		}else {
			return $this->field($fields)->where($where)->page($page)->select();
        }
	}
    public function getWorkerById($id)
    {
        return $this->where('id='.$id)->find();
    }

}
