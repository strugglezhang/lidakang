<?php

namespace Mall\Controller;

use Think\Controller;


class LoginController extends Controller
{

//员工登录
    public function index()
    {
        $this->start_session(99999999);
        before_api();
        $username = I('username');
        if (empty($username)) {
            Ret(array('code' => 2, 'info' => '用户名为空！'));
        }
        $token = cookie('token');
        $remMe = I('remMe');
        if ($token) {
            $workerModel = D('Worker');
            if ($remMe == 1) {
                $data['token'] = md5($username . date('His') . 'cenk');
                cookie('token', $data['token']);
            } else {
                unset($_COOKIE['token']);
            }
            $worker = $workerModel->loginByToken($username, $token);
        } elseif ($remMe == 1) {
            $workerModel = D('Worker');
            $password = I('password');
            if (empty($password)) {
                Ret(array('code' => 2, 'info' => '密码为空！'));
            }
            $data['token'] = md5($username . date('His') . 'cenk');
            cookie('token', $data['token']);
            $worker = $workerModel->loginByPassword($username, $password);
        } else {
            $workerModel = D('Worker');
            $password = I('password');
            if (empty($password)) {
                Ret(array('code' => 2, 'info' => '密码为空！'));
            }
            unset($_COOKIE['token']);
            $worker = $workerModel->loginByPassword($username, $password);
        }
        if ($worker) {
            /*$data['id'] = $worker['id'];
            $data['last_login_time'] = $worker['last_login_time'] = date('Y-m-d H:i:s');
            $workerModel->update_worker($data);*/
            /*switch ($key) {
                case 'i':
                    session('inst', $worker);
                    break;
                case 'd';
                    session('merchant', $worker);
                    break;
                case 'm';
                    session('mall', $worker);
                    break;
                case '2':
                    session('inst', $worker);
                    break;
                case '4';
                    session('merchant', $worker);
                    break;
                case '1';
                    session('mall', $worker);
                default:
                    session('mall', $worker);
            }*/

            session('mall', $worker);
            session('worker_id', $worker['id']);
            session('worker_name', $worker['name']);
            session('dept_id', $worker['dept_id']);
            session('position_id', $worker['position_id']);
            //$data['worker_id']=$worker['id'];
            //$data['worker_name']=$worker['id'];
            unset($worker['password']);
            Ret(array('code' => 1, 'info' => '登录成功！','data'=>$worker));
        } else {
            Ret(array('code' => 2, 'info' => '账号或密码错误或者非商场员工登录！'));
        }
    }

    function start_session($expire = 0)
    {
        if ($expire == 0) {
            $expire = ini_get('session.gc_maxlifetime');
        } else {
            ini_set('session.gc_maxlifetime', $expire);
        }
        if (empty($_COOKIE['PHPSESSID'])) {
            session_set_cookie_params($expire);
            session_start();
        } else {
            session_start();
            setcookie('PHPSESSID', session_id(), time() + $expire);
        }
    }

//一级菜单
    public function auth_first_list()
    {
        before_api();
        checkLogin();
        checkAuth();

        $worker_id = session('worker_id');
        $role = $this->get_role_by_worker_id($worker_id);
        $condition['role_id'] = $role['role_id'];
        $navi = D('Permissions')->where($condition)->field('first_level')->select();
        //session('role_id',$role['role_id']);
        if ($navi) {
            Ret(array('code' => 1, 'data' => $navi));
        } else {
            Ret(array('code' => 2, 'info' => '权限获取失败,系统出错！'));
        }

    }

//二级菜单
    public function auth_second_list()
    {
        before_api();
        checkLogin();
        checkAuth();

        $worker_id = session('worker_id');
        $role = $this->get_role_by_worker_id($worker_id);
        $condition['role_id'] = $role['role_id'];
        $condition['first_level'] = I('first_level');
        $navi = D('Permissions')->where($condition)->field('second_level')->select();
        if ($navi) {
            Ret(array('code' => 1, 'data' => $navi));
        } else {
            Ret(array('code' => 2, 'info' => '权限获取失败,系统出错！'));
        }

    }

//三级菜单
    public function auth_third_list()
    {
        before_api();
        checkLogin();
        checkAuth();


        $worker_id = session('worker_id');
        $role = $this->get_role_by_worker_id($worker_id);
        $condition['role_id'] = $role['role_id'];
        $condition['first_level'] = I('first_level');
        $condition['second_level'] = I('second_level');
        $navi = D('Permissions')->where($condition)->field('therd_level')->select();
//		var_dump($navi);die;
//		echo '$navi';die;
        if ($navi) {
            Ret(array('code' => 1, 'data' => $navi));
        } else {
            Ret(array('code' => 2, 'info' => '权限获取失败,系统出错！'));
        }
    }


    private function get_role_by_worker_id($worker_id)
    {
        if ($worker_id) {
            $condition['number'] = $worker_id;
            return D('MemberRole')->get_roleID_by_number($worker_id);
        }
    }

    private function get_navi_by_role($role_id)
    {
        if ($role_id) {
            $condition['role_id'] = $role_id;
            return D('Navi')->where($condition)->field('first_level_navi_name')->select();
        }
    }

    private function get_function_by_role($role_id)
    {
        if ($role_id) {
            $condition['role_id'] = $role_id;
            return D('Gongneng')->where($condition)->field('project,auth')->select();
        }
    }

    public function quit()
    {
        session_unset();
        session_destroy();
    }

    /**
     * 三级菜单
     */
    public function auth_thir()
    {
        $data['second_id'] = I('first_id');
        $first_list = explode('-', $data['second_id']);
        $where['second_id'] = array('in', $first_list);
        $data = D('NaviThird')->where($where)->field('id,name')->select();
        if ($data) {
            Ret(array('code' => 1, 'data' => $data));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据！'));
        }
    }

    /**
     * 二级菜单
     */
    public function auth_second()
    {
        before_api();
        checkLogin();
        checkAuth();
        $data['first_id'] = I('first_id');
        $first_list = explode('-', $data['first_id']);
        $where['first_id'] = array('in', $first_list);
        $data = D('NaviSecond')->where($where)->field('name')->select();
        if ($data) {
            Ret(array('code' => 1, 'data' => $data));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据！'));
        }

    }

    /**
     * 一级菜单
     */
    public function auth_first()
    {
        before_api();
        checkLogin();
        checkAuth();
        $navi = D('NaviFirst')->field('id,name')->select();
        if ($navi) {
            Ret(array('code' => 1, 'data' => $navi));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据！'));
        }

    }


    public function auth_third()
    {
        $data = D('NaviSecind')->field('id,name')->select();
        if ($data) {
            Ret(array('code' => 1, 'data' => $data));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据！'));
        }
    }

    public function authAdd()
    {
        $role_id = I('id');
        $auth = $_POST['auth'];
        $data['first_level'] = $auth['first_level'];
        $first_list = explode('-', $data['first_level']);
        $data['second_level'] = $auth['second_level'];
        $second_list = explode('-', $data['second_level']);
        $data['therd_level'] = $auth['therd_level'];
        $therd_list = explode('-', $data['therd_level']);
        for ($i = 0; $i < count($second_list); $i++) {
            //$first_list[$i] 为二级菜单名称
            $condition['name'] = $first_list[$i];
            $firstMenuId = D('NaviSecind')->where($condition)->field('first_id')->select();
            $condition['id'] = $firstMenuId;
            //$firstMenuName为一级菜单名称
            $firstMenuName = D('NaviFirst')->where($condition)->field('name')->select();
            for ($j = 0; $j < count($therd_list); $j++) {
                //$therd_list[$j]为三级菜单名称
                $data['first_level'] = $first_list[$i];
                $data['second_level'] = $firstMenuName;
                $data['therd_level'] = $therd_list;
                $data['role_id'] = $role_id;
                $res = D('Permissions')->add($data);
            }
        }
        if ($res) {
            Ret(array('code' => 1, 'info' => '添加成功'));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据！'));
        }

    }


}
