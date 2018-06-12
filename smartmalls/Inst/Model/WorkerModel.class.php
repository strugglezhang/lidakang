<?php
namespace Inst\Model;
use Think\Model;
class WorkerModel extends Model {
	protected $trueTableName = 'institution_staff';
/*
state:员工状态（1：未审核，2在职(已审核)，3冻结，4已离职，5已删除）

*/
	public function checkAuth($worker_id,$institution_id,$fields = 'id'){
		$where['id'] = $worker_id;
		$where['institution_id'] = $institution_id;
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

	public function get_count($keyword = null,$institution_id=0){
		if ($institution_id !== 0) {
			$condition['institution_id'] = $institution_id;
		}
		if (!empty($keyword)) {
			if (preg_match("/^\d*$/", $keyword)) {
				$condition['id|cardNumber_NO'] = $keyword;
			}else{
				$condition['name'] = array('LIKE', '%' . $keyword . '%');
                $condition['number'] = array('LIKE', '%' . $keyword . '%');
                $condition['_logic'] = 'OR';
            }
		}
		return $this->where($condition)->count();
	}

	public function get_list($keyword = null,$institution_id=null, $page = '1,10',$fields=null){
		if ($institution_id !== 0) {
			$condition['institution_id'] = $institution_id;
		}
		if (!empty($keyword)) {
			if (preg_match("/^\d*$/", $keyword)) {
				$condition['id|cardnumber_nO'] = $keyword;
			}else{
				$condition['name'] = array('LIKE', '%' . $keyword . '%');
				$condition['number'] = array('LIKE', '%' . $keyword . '%');
                $condition['_logic'] = 'OR';
            }
		}
        $info = $this->field('id,pic,name,number,address,sex,birthday,phone,institution_id,card_number,cardnumber_nO')
			->where($condition)
			->page($page)
			->select();

		return $info;

	}


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
    public function deleteWorkerByInstId($instId)
    {
        $data['state']=2;
        return $this->where('institution_id='.$instId)->delete();

    }
    public function getWorkerByInstId($instId)
    {
        return $this->where('institution_id='.$instId)->select();
    }
    public function getWorkerById($id)
    {
        return $this->where('id='.$id)->select();
    }

}