<?php
namespace Admin\Controller;
use Think\Controller;
class LoginController extends Controller {
    public function index(){
        //$this->display('admin/login');
        $this->show('admin/login');
    }
    public function index_api(){
        header('Content-Type:text/html; charset=utf-8');//防止出现乱码
    	if (isset($_SESSION['uid'])) {
            jsonRet(array('code' => 1, 'info' => '您还已经登录！'),$quit_after_ret= false);
    	}
    	if (!empty($_POST)) {
            $keyword = I('keyword');
            if($keyword){
                switch($keyword){
                    case '商场':
                        $model=getLoginModel('mall_staff');
                        break;
                    case '商户':
                        $model=getLoginModel('merchant_staff');
                        break;
                    case '机构':
                        $model=getLoginModel('institution_staff');
                        break;
                }

                if(!I('username')){
                    jsonRet(array('code' => 2, 'info' => '请输入用户名'),$quit_after_ret=true);
                }
                if(@I('password')){
                    jsonRet(array('code' => 2, 'info' => '请输入密码'),$quit_after_ret=true);
                }
                if(I('username')&&I('password')){
                    $data['username']=I('username');
                    $data['password']=I('password');
                    $userinfo=$model->where('username='.$data['username'])->select();
                    if(!$userinfo){
                        jsonRet(array('code'=>2,'info'=>'用户名不存在'),$quit_after_ret=true);
                    }
                    if($userinfo&&$userinfo['password']!=$data['password']){
                        jsonRet(array('code'=>2,'info'=>'密码不正确'),$quit_after_ret=true);
                    }
                }

                $logmodel=getLoginModel('user_login_log');
                $logindata['username']=I('username');
                $logindata['dpt_id']=I('dpt_id');
                $logindata['ip']=get_client_ip();
                $logindata['time']=date("Y-m-d H:i:s");
                $code=$logmodel->save($logindata);

                Session::set('uid',$userinfo['id']);
                if($_SESSION['uid']&&$code){
                    jsonRet(array('code'=>1,'info'=>'登录成功'),$quit_after_ret=false);
                }

            }else{
                jsonRet(array('code' => 2, 'info' => '请选择机构种类！'),$quit_after_ret= false);
            }

        }
    }
}