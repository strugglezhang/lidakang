<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/7/28
 * Time: 14:21
 */
namespace AppParents\Controller;
class CountController extends CommonController{
    //更新密码
    public function update_password(){
//    before_api();
//        var_dump($_SESSION['member_id']);die;
    checkLogin();
    checkAuth();
    $condition['phone']=I('phone');
    $password_new=I('password_new');
    $password_old=I('password_old');
    if(!$password_new){
        Ret(array('code' => 2, 'info' => '新密码不能为空！'));
    }
    if(!$password_old){
        Ret(array('code' => 2, 'info' => '旧密码不能为空！'));
    }

    if($password_new==$password_old){
        Ret(array('code' => 2, 'info' => '新旧密码不能一样！'));
    }

    $condition['app_pwd']=createPassword($password_old);
    $memberinfo=D('Login')->where($condition)->find();
    if(!$memberinfo){
        Ret(array('code' => 2, 'info' => '密码不正确，密码修改失败！'));
    }
    if(strlen($password_old)<6){
        Ret(array('code' => 2, 'info' => '密码长度不能小于六位数！'));
    }
    if($memberinfo){
        $data['password']=createPassword($password_new);
        D('Login')->where($condition)->save($data);
        Ret(array('code'=>1,'info'=>'密码修改成功！'));
    }
}
    public function app_update(){
//        var_dump($_SESSION['number']);die;
        //before_api();
        checkAuth();
        checkLogin();
        $condition['id']=I('id');
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



}