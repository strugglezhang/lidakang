<?php
namespace Merchant\Model;
use Think\Model;
class WorkerModel extends Model {
	protected $trueTableName = 'merchant_staff';
/*
state:员工状态（1：未审核，2在职(已审核)，3冻结，4已离职，5已删除）

*/

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

	public function checkAuth($worker_id,$merchant_id,$fields = 'id'){
		$where['id'] = $worker_id;
		$where['merchant_id'] = $merchant_id;
		$res = $this->field($fields)->where($where)->find();
		return empty($res) ? false : $res;
	}

	public function checkPhone($phone,$fields = 'id'){
		return $this->field($fields)->where(array('phone' => $phone))->find();
	}

	public function add_worker($data){
		
		return $this->add($data);
	}

	public function update_worker($data){

		return $this->save($data);
	}

	public function delete_worker($worker_id){

		return $this->where('id='.$worker_id)->delete();
	}

	public function get_worker_infos($worker_id,$fields = null){
		if ($fields === null) {
			return $this->field('password',true)->where('id='.$worker_id)->find();
		}else{
			return $this->field($fields)->where('id='.$worker_id)->find();
		}
	}

	public function get_count($pept_id,$merchant_id, $state = 0,$keyword = null){
		if($pept_id){
			$where['pept_id'] = $pept_id;
		}
		if($merchant_id){
			$where['merchant_id'] = $merchant_id;
		}
		if ($state !== 0) {
			$where['state'] = $state;
		}
		if (!empty($keyword)) {
			$key_where['number'] = $keyword;
			$key_where['name'] = array('LIKE','%'.$keyword.'%');
			$key_where['_logic'] = 'OR';
			$where['_complex'] = $key_where;
		}

		return $this->where($where)->count();
	}

	public function get_list($pept_id,$merchant_id, $state = 0, $keyword = null, $page = '1,10',$fields = null){
		if($pept_id){
			$where['pept_id'] = $pept_id;
		}
		if($merchant_id){
			$where['merchant_id'] = $merchant_id;
		}
		if ($state !== 0) {
			$where['state'] = $state;
		}
		if (!empty($keyword)) {
			$key_where['number'] = $keyword;
			$key_where['name'] = array('LIKE','%'.$keyword.'%');
			$key_where['_logic'] = 'OR';
			$where['_complex'] = $key_where;
		}
		return $this->where($where)->page($page)->select();
	}
    public function deleteWorkerByMerchantId($mechantId)
    {
        $data['state']=2;
        $this->where('merchant_id='.$mechantId)->save($data);

    }
    public function getWorkerByMerchantId($merchantId)
    {
        return $this->where('merchant_id='.$merchantId)->select();
    }
    public function getWorkerById($staffId)
    {
        return $this->where('id='.$staffId)->select();
    }
}