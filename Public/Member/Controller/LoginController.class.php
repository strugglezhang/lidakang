<?php
namespace Member\Controller;
use Think\Controller;
class LoginController extends Controller{
    public function index_api(){
        $username = I('username');
        if (empty($username)) {
            Ret(array('code' => 2, 'info' => '用户名为空！'));
        }
        $password = I('password');
        if (empty($password)) {
            Ret(array('code' => 2, 'info' => '密码为空！'));
        }
        $memberModel = D('Login');
        $member = $memberModel->login($username,$password);
        if ($member) {
            $data['number'] = $member['number'];
            $data['last_login_time'] = $member['last_login_time'] = NOW_TIME;
//            session('member',$member);
            D('Member')->where('number='.$member['number'])->save($data);
            session('member_id',$member['id']);
            session('number',$member['number']);
            unset($member['password']);
            Ret(array('code' => 1, 'data' =>'登录成功'));
        }else{
            Ret(array('code' => 2, 'info' => '账号或密码错误！'));
        }
}
    public function app_index_api(){
        $username = I('username');
        if (empty($username)) {
            Ret(array('code' => 2, 'info' => '用户名为空！'));
        }
        $password = I('password');
        if (empty($password)) {
            Ret(array('code' => 2, 'info' => '密码为空！'));
        }


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
        $courseReserveModel = D('CourseReserve');
        $activityReserveModel = D('ActivityReserve');
        if($res){
            $card =D('CardBond')->get_card($id);
            $res[0]['card_number'] = $card['card_number'];
            $cour =$courseTryoutModel->get_count_info($id);
            $res[0]['tryout']=$cour;
            $reserve =$courseReserveModel->get_reserve_info($id);
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
}