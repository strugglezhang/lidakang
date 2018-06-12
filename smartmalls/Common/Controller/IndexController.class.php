<?php
namespace Common\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index()
    {

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
        if (!checkPhoneFormat($data)) {
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
        if (!checkIdNumber($data)) {
            astRet(2,'身份证号码格式有误！');
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

    private function get_position_id($key = 'position_id', $error_quit = true){
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

    private function get_nation($key = 'nation', $default = 1){
        return I($key,$default,'intval');
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

    private function get_emergency_contacts($key = 'emergency_contact'){
        return I($key,null);
    }

    private function get_emergency_phone($key = 'emergency_phone'){
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

    private function get_imgs($key = 'imgs'){
        $data = I($key,'');
        return $data;
    }

    private function get_certificate_img($key = 'certificate_img'){
        $data = I($key,'');
        return $data;
    }

    public function get_register_data($type){
        switch ($type) {
            case 'Mall':
                $data['mall_id'] = session('mall.mall_id');
                $data['dept_id']  = $this->get_dept_id();
                $data['position_id']  = $this->get_position_id();
                $data['dept_name'] = D('Mall/Dept')->get_dept_name_by_id($data['dept_id']);
                if (!$data['dept_name']) {
                    Ret(array('code' => 2, 'info' => '所选的部门不存在！'));
                }
                $data['position_name'] = D('Mall/Position')->get_position_name_by_id($data['position_id']);
                if (!$data['position_name']) {
                    Ret(array('code' => 2, 'info' => '所选的职位不存在！'));
                }
                $data['nation']  = $this->get_nation();
                break;
            case 'Inst':
                $institution_id = session('inst.institution_id');
                $data['institution_id'] = $institution_id;
                $data['course_id'] = $this->get_course_id();
                $data['imgs'] = $this->get_imgs();
                $data['certificate_img'] = $this->get_certificate_img();
                break;
            case 'Merchant':
                $data['merchant_id'] = session('merchant.merchant_id');
                break;
            default:
                return false;
                break;
        }
        $data['name'] = $this->get_name();
        $data['phone'] = $this->get_phone();
        if (D($type.'/Worker')->checkPhone($data['phone'])) {
            Ret(array('code' => 4, 'info' => '手机号码已注册!'));
        }
        $data['sex'] = $this->get_sex();
        $data['birthday'] = $this->get_birthday();
        $data['pic'] = $this->get_pic();
        $data['id_number'] = $this->get_id_number();
        $password = C('DEFAULT_PASSWORD');
        $password = empty($password) ? '888888' : $password;
        $data['password'] = createPassword($password);
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

        $data['birth_address'] = getAddressById(array('province_id' => $data['birth_province_id'], 'city_id' => $data['birth_city_id'], 'district_id' => $data['birth_district_id']),2,'-');
        
        return $data;
    }

    public function get_update_data($type){
        $data['id'] = $this->get_worker_id();
        switch ($type) {
            case 'Mall':
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
                $nation  = I('nation',0,'intval');
                if ($nation !== 0) {
                    $data['nation'] = $nation;
                }
                break;
            case 'Inst':
                $pid = $institution_id = session('inst.institution_id');
                $data['course_id'] = isset($_POST['course_id']) ? $this->get_course_id() : null;
                if (!empty($data['course_id']) && !D('Inst/Course')->checkCourse($institution_id,$data['course_id'])) {
                    Ret(array('code' => 2, 'info' => '无效课程ID!'));
                }
                $imgs  = I('imgs',false);
                if ($imgs !== false) {
                    $data['imgs'] = $imgs;
                }
                $certificate_img  = I('certificate_img',false);
                if ($certificate_img !== false) {
                    $data['certificate_img'] = $certificate_img;
                }
                break;
            case 'Merchant':
                $pid = $merchant_id = session('merchant.merchant_id');
                break;
            default:
                return false;
                break;
        }
        if (!D($type.'/Worker')->checkAuth($data['id'],$pid)) {
            Ret(array('code' => 2, 'info' => C('NO_AUTH')));
        }
        $data['name'] = isset($_POST['name']) ? $this->get_name() : null;
        $data['phone'] = isset($_POST['phone']) ? $this->get_phone() : null;
        if (!empty($data['phone'])) {
            $cphone = D($type.'/Worker')->checkPhone($data['phone']);
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
        $emergency_contacts  = I('emergency_contact',false);
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

        foreach ($data as $key => $value) {
            if ($value === null) {
                unset($data[$key]);
            }
        }

        if (count($data) === 1) {
            Ret(array('code' => 3, 'info' => '未作任何修改！'));
        }
        return $data;
    }
}