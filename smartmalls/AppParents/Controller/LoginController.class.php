<?php
namespace AppParents\Controller;
use AppParents\Model\LoginModel;
use Think\Controller;
class LoginController extends Controller{
    public function app_index_api(){
        $username = I('username');
        $password = I('password');
        if (empty($username)) {
            Ret(array('code' => 2, 'info' => '用户名为空！'));
        }
        if (empty($password)) {
            Ret(array('code' => 2, 'info' => '密码为空！'));
        }
//        if (!$username) {
//            Ret(array('code' => 2, 'info' => '用户不正确！'));
//        }




        $res = $this->Login($username,$password);
        $data =$this->GetMeInfo($res);
        if($data){
            Ret(array('code'=>1,'data'=>$data));
        }else{
            Ret(array('code'=>2,'info'=>'帐号密码错误'));
        }
//        $memberModel = D('Login');
//        $member = $memberModel->login($username,$password);
//        if ($member) {
//            $data['number'] = $member['number'];
//            $data['last_login_time'] = $member['last_login_time'] = NOW_TIME;
//            D('Member')->where('number='.$member['number'])->save($data);
//            session('member_id', $member['id']);
//            session('number', $member['number']);
//            unset($member['password']);
//
//
//            $output['id'] = $member['id'];
//            $output['name'] = $member['name'];
//            $output['pic'] = $member['pic'];
//            $output['card_number'] = $member['card_number'];
//            $output['tryout'] = 2;
//            $output['reserve'] =2;
//            $output['activity'] = 3;
//            Ret(array('code' => 1, 'data' =>$output));
//        }
//        else{
//            Ret(array('code' => 2, 'info' => '账号或密码错误！'));
//        }


    }
    /*
     * 安全退出
     */
    public function quit(){
        session_destroy();
    }
    private function GetMeInfo($id)
    {
        if($id.ob_get_length() < 10)
        {
            Ret(array('code'=>3,'info'=>'Id号长度不能小于10位'));
        }
        $res = D('Login')->get_app_info($id);
        $courseTryoutModel = D('CourseTryout');
        $courseOrderModel = D('CourseOrder');
        $activityReserveModel = D('ActivityReserve');
        if($res){
            $card =D('CardBond')->get_card($id);
            $res[0]['card_number'] = $card['card_number'];
            $cour =$courseTryoutModel->get_count_info($id);
            $res[0]['tryout']=$cour;
            $reserve =$courseOrderModel->get_reserve_info($id);
            $res[0]['reserve']=$reserve;
            $activity= $activityReserveModel->get_activity_info($id);
            $res[0]['activity']=$activity;
            return $res;
        }else{
            Ret(array('code'=>2,'info'=>'没有数据'));
        }
    }
    private function Login($username,$password)
    {
        $memberModel = D('Login');
        $member = $memberModel->login($username,$password);
        if ($member) {
            $data['number'] = $member['number'];
            $data['last_login_time'] = $member['last_login_time'] = NOW_TIME;
            D('Member')->where('number='.$member['number'])->save($data);
            session('member_id', $member['id']);
            session('number', $member['number']);
            unset($member['password']);
            return $member['id'];
        }
        else{
                Ret(array('code' => 2, 'info' => '账号或密码错误！'));
            }

    }


    public function app_update(){
//        var_dump($_SESSION['number']);die;
        //before_api();
        checkAuth();
        checkLogin();
        $condition['number']=I('number');
        $password_new=I('password_new');
        $password = 888888;
        if(!$password_new){
            Ret(array('code' => 2, 'info' => '新密码不能为空！'));
        }

        if($password_new==$password){
            Ret(array('code' => 2, 'info' => '新旧密码不能一样！'));
        }
        $data['password']=createPassword($password_new);
        $res=D('Login')->where($condition)->save($data);
        if($res){
            Ret(array('code'=>1,'info'=>'密码修改成功！'));
        }else{
            Ret(array('code'=>2,'info'=>'密码修改失败，系统错误！'));
        }
    }

    public function getForGetCode()
    {
       phpinfo();die;
        $array = ['code' => 2, 'msg' => '参数错误','data'=>[]];
        $phone = I('phone');

        if(preg_match("/^1[34578]\d{9}$/", $phone) && !empty($phone)){
            $model = D('UserSession');
            $status = $model->getCode($phone);
            if(empty($status)){
                $code = randCode();
                $m = new LoginModel();
                if(!empty($m->getPwd($phone))){
                   $res = json_decode(curlData($code,$phone),1);
                    if($res['success'] === true){
                        $val = [
                            'userPhone' => $phone,
                            'userCode' => $code,
                        ];
                        $model->addUser($val);
                        Ret(['code' => 1, 'msg' => '请求成功','data'=>['发送成功']]);
                    }else{
                        Ret(['code' => 2, 'msg' => '请稍后再试','data'=>[]]);
                    }
                }
            }
        }else {
            Ret(['code' => 2, 'msg' => '手机格式错误','data'=>['请输入正确的手机号']]);
        }
        Ret($array);
    }


    public function getValidationCode()
    {
        $phone = I('phone');
        if(preg_match("/^1[34578]\d{9}$/", $phone) && !empty($phone) && (strlen($phone)== "11")) {
            S(array('type'=>'file','expire'=>300));
            if(empty(S($phone))){
                $model = D('Member');
                if(!empty($model->getPwd($phone))){
                    $code = randCode();
                    $res = json_decode(curlData($code,$phone),1);
                    if($res['success'] === true){
                        S($phone,$code);
                        Ret(array('code' => 1, 'info' => '发送成功'));
                    } else {
                        Ret(array('code' => 2, 'info' => '请稍后再试'));
                    }
                }else{
                    Ret(array('code' => 2, 'info' => '暂无该用户信息'));
                }
            }else {
                Ret(array('code' => 2, 'info' => '请稍后再试'));
            }
        } else {
            Ret(array('code' => 2, 'info' => '请输入正确的手机号'));
        }

    }

    public function forgetPwd()
    {
        $phone = I('phone');
        $code = I('code');
        $newPwd = I('newPwd');
        $array = ['code' => 2, 'msg' => '参数错误','data'=>[]];
        if(empty($phone) || empty($code) || empty($newPwd)){
            Ret($array);
        }
        if(preg_match('/^1[34578]\d{9}$/', $phone) && (strlen($phone)== ' 11')){
            $m = D('Member');
            $pwd = $m->getPwd($phone);
            if(md5($newPwd.'pwdSalt') == $pwd){
                Ret(['code' => 2,'info' => '新密码不能和旧密码一样',]);
            }else {
                if(!empty(S($phone)) && (S($phone) == $code)){
                    if($res = $m->updatePwdByPhone($phone,md5($newPwd.'pwdSalt'))){
                        Ret(['code' => 1,'info' => '修改成功']);
                    }else {
                        Ret(['code' => 2,'info' => '修改失败']);
                    }
                }else{
                    Ret(['code' => 2,'info' => '验证码错误或验证码已失效']);
                }
            }
        } else {
            Ret(['code'=>2,'info'=> '请输入正确的手机号']);
        }

    }



}