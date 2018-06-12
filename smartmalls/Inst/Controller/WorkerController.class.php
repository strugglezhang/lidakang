<?php
namespace Inst\Controller;
class WorkerController extends CommonController {
    public function index(){
echo time();
    }

    public function getInstStaffListByInst()
    {
        before_api();
        checkLogin();
        checkAuth();
        $keyword = I('keyword');
        $page = I('page',1,'intval');
        $pagesize = I('pagesize',10,'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page.','.$pagesize;

        //$institution_id=0;
        $institution_id=session('inst.institution_id');
        $workerModel = D('Worker');
        $institutionModel=D('Institution');
        $count = $workerModel->get_count($keyword,$institution_id);

        $res = $workerModel->get_list($keyword,$institution_id,$page,$fields=null);
        foreach ($res as $key => $value) {
            $inst=$institutionModel->get_inst_by_id($value['institution_id']);
            $res[$key]['institution_name']=$inst[0]['name'];
            $res[$key]['sex_name'] = get_sex($value['sex']);
            $res[$key]['age'] = getAgeByBirthday($value['birthday']);
        }
        if ($count) {
            Ret(array('code' => 1, 'data' => $res, 'total' => $count, 'page_count' => ceil($count/$pagesize)));
        }else{
            Ret(array('code' => 2, 'info' => '查无相关数据！'));
        }
    }
    public function index_api(){
        before_api();
        checkLogin();
        checkAuth();
        $keyword = I('keyword');
        $page = I('page',1,'intval');
        $pagesize = I('pagesize',10,'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page.','.$pagesize;

        $institution_id =I('institution_id',0,'intval');
        $workerModel = D('Worker');
        $institutionModel=D('Institution');
        $count = $workerModel->get_count($keyword,$institution_id);

        $res = $workerModel->get_list($keyword,$institution_id,$page,$fields=null);
        foreach ($res as $key => $value) {
            $inst=$institutionModel->get_inst_by_id($value['institution_id']);
            $res[$key]['institution_name']=$inst[0]['name'];
            $res[$key]['sex_name'] = get_sex($value['sex']);
            $res[$key]['age'] = getAgeByBirthday($value['birthday']);
        }
        if ($count) {
            Ret(array('code' => 1, 'data' => $res, 'total' => $count, 'page_count' => ceil($count/$pagesize)));
        }else{
            Ret(array('code' => 2, 'info' => '查无相关数据！'));
        }
    }

    public function instWorker(){
        before_api();
        checkLogin();

        checkAuth();
        $state = I('state',2,'intval');
        $is_manager = true;
        if ($state !== 2 && !$is_manager) {
            $state = 2;
        }
        $keyword = I('keyword');
        $page = I('page',1,'intval');
        $pagesize = I('pagesize',10,'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page.','.$pagesize;

        $workerModel = D('Worker');
        $institutionModel=D('Institution');
        $count = $workerModel->get_count($keyword);
        $res = $workerModel->get_list($keyword,$page,$fields=null);
        foreach ($res as $key => $value) {
            $inst=$institutionModel->get_inst_by_id($value['id']);
            $res[$key]['institution_name']=$inst[0]['name'];
            $res[$key]['sex_name'] = get_sex($value['sex']);
            $res[$key]['age'] = getAgeByBirthday($value['birthday']);
        }
        if ($count) {
            Ret(array('code' => 1, 'data' => $res, 'total' => $count, 'page_count' => ceil($count/$pagesize)));
        }else{
            Ret(array('code' => 2, 'info' => '查无相关数据！'));
        }
    }



    public function worker_add() {
    	$this->show('add worker');
    }
         /*
         * 员工信息增删改
         */
    public function worker_dml_api(){
        before_api();

    	checkLogin();

    	checkAuth();

    	$institution_id = session('inst.institution_id');
        if(!isset($institution_id)){
            $institution_id = I('institution_id');
        }

    	$flag = I('flag');
    	$workerModel = D('Worker');
    	switch ($flag) {
    		case 'add':
                $data = $this->get_register_data();
                if(hasSameCard($data['card_number'])==true)
                {
                    Ret(array('code' => 2, 'info' => '此卡被他人使用或此卡被挂失冻结！'));
                }
                else
                {
                    $worker_id = $workerModel->add_worker($data);
                    if ($worker_id) {
                        $workerModel->update_worker(array('id' => $worker_id, 'number' => get_number_by_id($worker_id)+200000));
                        if($worker_id){
                            $this->setcard($data,$worker_id);
                        }

                        $this->sendAuthByInst($institution_id,$worker_id,$data);

                        $this->sendAuthByPost($data,$worker_id);

                        Ret(array('code' => 1, 'info' => '添加成功！'));
                    }else{
                        Ret(array('code' => 2, 'info' => '添加失败,系统出错！'));
                    }
                }

    			break;
    		case 'update':
                $institution_id = I('institution_id');
                if(!isset($institution_id)){
                    $institution_id = session('inst.institution_id');
                }
                $data['institution_id'] = $institution_id;

                $data['id'] = I('id');
                $data['course_id'] = $this->get_course_id();

                $imgs  = I('imgs',false);
                if ($imgs !== false) {
                    $data['imgs'] = $imgs;
                }
                $certificate_img  = I('certificate_img',false);
                if ($certificate_img !== false) {
                    $data['certificate_img'] = $certificate_img;
                }
                $data['position_id'] = $this->get_position();

                $data['name'] = $this->get_name();
                $data['phone'] =$this->get_phone();

                if (!empty($data['phone'])) {
                    $cphone = D('Worker')->checkPhone($data['phone']);
                    if ($cphone && $cphone['id'] != $data['id']) {
                        Ret(array('code' => 4, 'info' => '手机号码已注册!'));
                    }
                }
                $data['sex'] =  $this->get_sex();
                $data['birthday'] =$this->get_birthday() ;
                $data['pic'] = $this->get_pic();
                $data['id_number'] = $this->get_id_number() ;
                $data['birth_province_id'] = $this->get_birth_province_id() ;
                $data['birth_city_id'] =  $this->get_birth_city_id() ;
                $data['birth_district_id'] =  $this->get_birth_district_id() ;

                if (($data['birth_province_id'] || $data['birth_city_id'] || $data['birth_district_id']) && (!$data['birth_province_id'] || !$data['birth_city_id'] || !$data['birth_district_id'])) {
                    Ret(array('code' => 2, 'info' => '（birth_province_id，birth_city_id，birth_district_id）要么都提交，要么都不提交！'));
                }
                if ($data['birth_province_id']) {
                    $data['birth_address'] = getAddressById(array('province_id' => $data['birth_province_id'], 'city_id' => $data['birth_city_id'], 'district_id' => $data['birth_district_id']),2,'-');
                }

                $data['state'] =  $this->get_state() ;

                $data['join_time'] =  $this->get_join_time();
                $data['contract_time'] = $this->get_contract_time();
                $data['card_number'] = I('card_number');

                $health  = I('health',0,'intval');
                if ($health !== 0) {
                    $data['health'] = $health;
                }
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
                $dept_id  = I('dept_id',false);
                if ($dept_id !== false) {
                    $data['dept_id'] = $dept_id;
                }
                $nation = I('nation',false);
                if ($nation !== false) {
                    $data['nation'] = $nation;
                }
                $emergency_phone  = I('emergency_phone',false);
                if ($emergency_phone !== false) {
                    $data['emergency_phone'] = $emergency_phone;
                }
                $remarks  = I('remarks',false);
                if ($remarks !== false) {
                    $data['remarks'] = $remarks;
                }
                $position_id = I ('position_id',false);
                if ($position_id !== false) {
                    $data['position_id'] = $position_id;
                }
                //获取卡号的信息
                $validateCardInfo=$this->getCardOwnerId($data['card_number']);
                //$cardIsOk=hasSameCard($data['card_number']);

                if(empty($validateCardInfo))
                {
                      $cardInfo=getCardInfoByNumber($data['card_number']);
                      if($cardInfo['id']==NULL)
                      {
                          $oldWorker=$workerModel->getWorkerById($data['id']);
                           $workerModel->update_worker($data);
                              $worker_id = $data['id'];
                              //新建卡
                              $this->setcard($data,$worker_id);
                              //冻结原有的卡
                              setCardFrozenByNumber($oldWorker[0]['card_number']);

                              $this->sendAuthByInst($institution_id, $worker_id, $data);

                              $this->sendAuthByPost($data, $worker_id);

                              Ret(array('code' => 1, 'info' => '修改成功！'));

                      }
                      else
                      {
                          Ret(array('code' => 2, 'info' => '此卡已经被冻结或被挂失！'));
                      }
                }
                else
                {
                    //var_dump($validateCardInfo);die;
                    if($validateCardInfo['id']==$data['id'])
                    {
                        $workerModel->update_worker($data);
                        $worker_id = $data['id'];
                        $this->sendAuthByInst($institution_id, $worker_id, $data);
                        $this->sendAuthByPost($data, $worker_id);

                        Ret(array('code' => 1, 'info' => '修改成功！'));
                    }
                    else
                    {
                        Ret(array('code' => 2, 'info' => '此卡被他人使用！'));
                    }
                }

    			break;
    		case 'delete':
    			$data['id'] = I('worker_id');
    			if ($data['id'] < 1) {
    				Ret(array('code' => 2, 'info' => '参数（worker_id）有误！'));
    			}
//    			if (!$workerModel->checkAuth($data['id'],$institution_id)) {
//    				Ret(array('code' => 2, 'info' => C('NO_AUTH')));
//    			}
                $deleteWorker=D('Worker')->getWorkerById($data['id']);
    			if ($workerModel->delete_worker($data['id'])) {
    			    //把员工卡冻结
                    setCardFrozenByNumber($deleteWorker[0]['card_number']);
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
    private function sendAuthByPost($data,$worker_id){
        //格式：eqNO,cardNO,Pin,name,starttime,endtime#timeID]week0,timezone1,timezone2,timezone3...;week1,timezone1,timezone2,timezone3..;.\r\n   you to me
        // 例子：12345,23456,2001,locost,20170730,20170731#23]1,9:30-11:00,10:30-12:00,16:30-18:00;2,9:30-11:00,10:30-12:00,16:30-18:00\r\n
        $con['position'] = array('Like','%'.$data['position_id'].'%');
        $con['user_type'] = 1;
        $shopRoleInfo  = D('ShopDoorRole')->where($con)->select();

        foreach($shopRoleInfo as $k => $v){
            $dooids=explode('-',$v['position']);
            if(in_array($data['position_id'],$dooids)){
                $dauth = $shopRoleInfo[$k]['shop_numbers'];
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
        }
    }

////基于机构设置刷卡权限，对应机构的商铺开门
    private function sendAuthByInst($institution_ids,$worker_id,$data){

        $instInfoList = D('Institution')->where('id='.$institution_ids)->field('shop_addr')->find();
        $shopAddr = explode("-",$instInfoList['shop_addr']);
        $inId = "";
        foreach ($shopAddr as $ids){
            $inId .= $ids.",";
        }

        $inId = substr($inId,0,strlen($inId) -1);
        $eqModel = D("Equipment");

        $eqIps = $eqModel->where("position in ($inId)")->field("equip_ip,position")->select();

        foreach ($eqIps as $item){
            $auth['eqIp'] = $item['equip_ip'];
            $auth['cardNo'] = $data['card_number'];
            $auth['memberId'] = 'i'.$worker_id;
            $auth['memberName'] = $data['name'];
            $starttime = $data['join_time'];
            $endtime = $data['contract_time'];
            $weekTime = C('WORKER_AUTH_TIME');
            $auth['time']=$starttime.','.$endtime.'#'.rand(1,1000).']'.$weekTime;
            //$auth['time'] = date('Ymd',strtotime($t1)).','.date('Ymd',strtotime($t2)).'#'.$k.']'.date('w',strtotime($t1)).','.date('H:i',strtotime($t1)).'-'.date('H:i',strtotime($t2));
            D('MemberDoorAuth')->add($auth);

        }

//        foreach ($instInfoList as $item){
//            $auth['eqIp'] = $item['position_idx'];
//            $auth['cardNo'] = $data['card_number'];
//            $auth['memberId'] = 'i'.$worker_id;
//            $auth['memberName'] = $data['name'];
//            $starttime = $data['join_time'];
//            $endtime = $data['contract_time'];
//            $weekTime = C('WORKER_AUTH_TIME');
//            $auth['time']=$starttime.','.$endtime.'#'.rand(1,1000).']'.$weekTime;
//
//            //$auth['time'] = date('Ymd',strtotime($t1)).','.date('Ymd',strtotime($t2)).'#'.$k.']'.date('w',strtotime($t1)).','.date('H:i',strtotime($t1)).'-'.date('H:i',strtotime($t2));
//
//            D('MemberDoorAuth')->add($auth);
//        }

    }

    /*
       * 员工详情
       */

    public function worker_view_api() {
        before_api();
        
    	checkLogin();

    	checkAuth();
    	$worker_id = I('worker_id',0,'intval');
		if ($worker_id < 1) {
			Ret(array('code' => 2, 'info' => '参数（worker_id）有误！'));
		}
//		$workerModel = D('Worker');
//		if (!$workerModel->checkAuth($worker_id,$institution_id)) {
//			Ret(array('code' => 2, 'info' => C('NO_AUTH')));
//		}

		$res = D('Worker')->get_worker_infos($worker_id);
		if ($res) {
            $res['sex_name'] = get_sex($res['sex']);
            $res['marriage_name'] = get_marriage($res['marriage']);
            $res['health_name'] = get_health($res['health']);
            $res['education_name'] = get_education($res['education']);
            $res['dept_name'] = $this->get_dept_by_id($res['dept_id']);
            $res['position_name'] = $this->get_position_by_id($res['position_id']);
            $res['institution_name'] = $this->getInstById($res['institution_id']);
			Ret(array('code' => 1, 'data' => $res));
		}else{
			Ret(array('code' => 2, 'info' => '获取相关信息失败！'));
		}
    }
    private function getInstById($instID){
        if($instID){
            $instInfo = D('Institution')->where('id='.$instID)->field('name')->find();
            return $instInfo['name'];

        }
    }

    /*
         * 员工注册信息
         */
    public function get_register_data(){
        $institution_id = I('institution_id');
        if(!isset($institution_id)){
            $institution_id = session('inst.institution_id');
        }
        $data['institution_id'] = $institution_id;
        //$data['course_id'] = $this->get_course_id();
        $data['imgs'] = $this->get_imgs();
        $data['position_id'] = $this->get_position();
        $data['certificate_img'] = $this->get_certificate_img();

        $data['name'] = $this->get_name();
        $data['phone'] = $this->get_phone();
//        if (D('Worker')->checkPhone($data['phone'])) {
//            Ret(array('code' => 4, 'info' => '手机号码已注册!'));
//        }
        $data['sex'] = $this->get_sex();
        $data['birthday'] = $this->get_birthday();
        $data['pic'] = $this->get_pic();
        $data['id_number'] = $this->get_id_number();
        $password = C('DEFAULT_PASSWORD');
        $password = empty($password) ? '888888' : $password;
        $data['password'] = createPassword($password);
        $data['app_pwd'] = createPassword($password);
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
        $data['emergency_contacts']  = $this->get_emergency_contact();
        $data['emergency_phone']  = $this->get_emergency_phone();
        $data['remarks']  = $this->get_remarks();
        $data['dept_id'] = $this->get_dept_id();
        $data['nation'] = $this->get_nation();
        $data['emergence_contact'] = I('emergence_contact');
        $data['card_number'] = I('card_number');
        $data['cardnumber_no']= $this->get_cardNumber_NO();

        $data['birth_address'] = getAddressById(array('province_id' => $data['birth_province_id'], 'city_id' => $data['birth_city_id'], 'district_id' => $data['birth_district_id']),2,'-');

        return $data;
    }
    /*
         * 员工更新数据
         */
    public function get_update_data(){
        $data['id'] = I('worker_id');
        $institution_id = I('institution_id');
        if(!isset($institution_id)){
            $institution_id = session('inst.institution_id');
        }

        $data['institution_id'] = $institution_id;
        $data['course_id'] = $this->get_course_id();
        $imgs  = I('imgs',false);
        if ($imgs !== false) {
            $data['imgs'] = $imgs;
        }
        $certificate_img  = I('certificate_img',false);
        if ($certificate_img !== false) {
            $data['certificate_img'] = $certificate_img;
        }
        $data['position_id'] = $this->get_position();
        $data['name'] = $this->get_name();
        $data['phone'] =$this->get_phone();

        if (!empty($data['phone'])) {
            $cphone = D('Worker')->checkPhone($data['phone']);
            if ($cphone && $cphone['id'] != $data['id']) {
                Ret(array('code' => 4, 'info' => '手机号码已注册!'));
            }
        }
        $data['sex'] =  $this->get_sex();
        $data['birthday'] =$this->get_birthday() ;
        $data['pic'] = $this->get_pic();
        $data['id_number'] = $this->get_id_number() ;
        $data['birth_province_id'] = $this->get_birth_province_id() ;
        $data['birth_city_id'] =  $this->get_birth_city_id() ;
        $data['birth_district_id'] =  $this->get_birth_district_id() ;

        if (($data['birth_province_id'] || $data['birth_city_id'] || $data['birth_district_id']) && (!$data['birth_province_id'] || !$data['birth_city_id'] || !$data['birth_district_id'])) {
            Ret(array('code' => 2, 'info' => '（birth_province_id，birth_city_id，birth_district_id）要么都提交，要么都不提交！'));
        }
        if ($data['birth_province_id']) {
            $data['birth_address'] = getAddressById(array('province_id' => $data['birth_province_id'], 'city_id' => $data['birth_city_id'], 'district_id' => $data['birth_district_id']),2,'-');
        }

        $data['state'] =  $this->get_state() ;

        $data['join_time'] =  $this->get_join_time();
        $data['contract_time'] = $this->get_contract_time();
        $data['card_number'] = I('card_number');
        $health  = I('health',0,'intval');
        if ($health !== 0) {
            $data['health'] = $health;
        }
        $marriage = I('marriage',0,'intval');
        if ($marriage !== 0) {
            $data['marriage'] = $marriage;
        }

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
        $emergency_contact  = I('emergency_contact',false);
        if ($emergency_contact !== false) {
            $data['emergency_contact'] = $emergency_contact;
        }
        $dept_id  = I('dept_id',false);
        if ($dept_id !== false) {
            $data['dept_id'] = $dept_id;
        }
        $nation = I('nation',false);
        if ($nation !== false) {
            $data['nation'] = $nation;
        }
        $emergency_phone  = I('emergency_phone',false);
        if ($emergency_phone !== false) {
            $data['emergency_phone'] = $emergency_phone;
        }
        $remarks  = I('remarks',false);
        if ($remarks !== false) {
            $data['remarks'] = $remarks;
        }
        $data['card_number'] = I('card_number');

       /* foreach ($data as $key => $value) {
            if ($value === null) {
                unset($data[$key]);
            }
        }

        if (count($data) === 1) {
            Ret(array('code' => 3, 'info' => '未作任何修改！'));
        }*/
        return $data;
    }

  /*  private function get_worker_id($key = 'worker_id', $error_quit = true){
        $data = I($key);
        if ($data < 1) {
            fastRet(2,'参数（worker_id）有误！');
            $error_quit and die;
            return false;
        }
        return $data;
    }*/
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
            fastRet(2,'手机号码格式有误！');
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
        if (!isCreditNo($data)) {
            fastRet(2,'身份证号码格式有误！');
            $error_quit and die;
            return false;
        }
        return $data;
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

    private function get_dept_id($key = 'dept_id', $error_quit = true){
        $data = I($key);
        if (empty($data)) {
            fastRet(2,'部门ID(dept_id)为空！');
            $error_quit and die;
            return false;
        }
        return $data;
    }

/*
 * 根据id查部门
 */
    private function get_dept_by_id($dept_id){
       if($dept_id){
           return D('InstDepartment')->get_dept_by_id($dept_id);
       }
    }
    private function get_position_by_id($dept_id){
        if($dept_id){
            return D('InstPosition')->get_position_by_id($dept_id);
        }
    }

    private function get_position($key = 'position_id', $error_quit = true){
        $data = I($key);
        if (empty($data)) {
            fastRet(2,'职位ID(position_id)为空！');
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

    private function get_contract_time($key = 'c', $error_quit = true){
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

    private function get_cardNumber_NO($key = 'cardnumber_no'){
        $data = I($key);
        return $data;
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

    private function get_emergency_contact($key = 'emergency_contacts'){
        return I($key,null);
    }

    private function get_emergency_phone($key = 'emergency_phone'){
        $data = I($key);
        if(!isMobile($data)){
            Ret(array('code' => 2, 'info' => '请输入手机号码格式不正确！'));
        }
        return I($key,null);
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

//    private function get_course_name($key = 'course_name', $error_quit = true){
//    $data = I($key,false);
//    if (!$data) {
//        fastRet(2,'无效课程name(course_name)！');
//        $error_quit and die;
//        return false;
//    }
//    return $data;
//}

    private function get_imgs($key = 'imgs'){
        $data = I($key,'');
        return $data;
    }

    private function get_certificate_img($key = 'certificate_img'){
        $data = I($key,'');
        return $data;
    }
//    private function get_position($key = 'position_id'){
//        $data = I($key,'');
//        return $data;
//    }

    public function setcard($data,$worker_id){
        if($data && $worker_id){
            $info['card_ownerid'] = $worker_id;
            $info['cardnumber_no'] = $data['cardnumber_no'];
            $info['card_ownewneme'] =$data['name'];
            $info['card_number'] = $data['card_number'];
//            var_dump($data);die;
            $info['card_typeid'] = 3;
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