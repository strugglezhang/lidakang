<?php
namespace Mall\Controller;
use Think\Controller;
class CommonController extends Controller {
    public function _initialize() {
//        if(!isset($_SESSION['worker_id'])) {
//            Ret(array('code' => 2, 'info' => '请登录！'));
//        }
    }
    public function dept_dml_api(){
        before_api();

        checkLogin();

        checkAuth();
        $mall_id = session('mall.mall_id');
        $flag = I('flag');
        switch ($flag) {
            case 'add':
                $data['name'] = I('dept_name');
                if (empty($data['name'])) {
                    Ret(array('code' => 2, 'info' => '部门名称不能为空！'));
                }
                if (strlen($data['name']) > 50) {
                    Ret(array('code' => 2, 'info' => '部门名称长度不能超过50个字符！'));
                }
                $data['mall_id'] = 1;
                $deptModel = D('Dept');
                if ($deptModel->check_dept_by_name($mall_id,$data['name'])) {
                    Ret(array('code' => 2, 'info' => '该部门已存在，不能重复添加！'));
                }
                if ($deptModel->add_dept($data)) {
                    Ret(array('code' => 1, 'info' => '添加成功！'));
                }else{
                    Ret(array('code' => 2, 'info' => '添加失败，系统出错！'));
                }
                break;
            case 'update':
                # code...
                break;
            case 'delete':
                # code...
                break;
            default:
                Ret(array('code' => 2, 'info' => 'Unknowed flag!'));
                break;
        }
    }

    public function position_dml_api(){
        before_api();

        checkLogin();

        checkAuth();
        $mall_id = session('mall.mall_id');
        // $mall_id = 1;
        $flag = I('flag');
        switch ($flag) {
            case 'add':
                $data['mall_id'] = $mall_id;
                $data['name'] = I('position_name');
                if (empty($data['name'])) {
                    Ret(array('code' => 2, 'info' => '职位名称不能为空！'));
                }
                if (strlen($data['name']) > 50) {
                    Ret(array('code' => 2, 'info' => '职位名称长度不能超过50个字符！'));
                }
                $data['dept_id'] = I('dept_id',0,'intval');
                if ($data['dept_id'] === 0) {
                    Ret(array('code' => 2, 'info' => '部门ID(dept_id)不能为空！'));
                }
                if (!D('Dept')->check_dept_by_id($mall_id,$data['dept_id'])) {
                    Ret(array('code' => 2, 'info' => '部门不存在!'));
                }
                
                $positionModel = D('Position');
                if ($positionModel->check_position_by_name($mall_id,$data['dept_id'],$data['name'])) {
                    Ret(array('code' => 2, 'info' => '该职位已存在，不能重复添加！'));
                }
                if ($positionModel->add_position($data)) {
                    Ret(array('code' => 1, 'info' => '添加成功！'));
                }else{
                    Ret(array('code' => 2, 'info' => '添加失败，系统出错！'));
                }
                break;
            case 'update':
                # code...
                break;
            case 'delete':
                # code...
                break;
            default:
                Ret(array('code' => 2, 'info' => 'Unknowed flag!'));
                break;
        }
    }
    
    public function dept_api(){
        before_api();

        checkLogin();

        checkAuth();
        $mall_id = session('mall.mall_id');;

        $deptModel = D('Dept');
        $res = $deptModel->get_list($mall_id);
        if ($res) {
            Ret(array('code' => 1, 'data' => $res));
        }else{
            Ret(array('code' => 2, 'info' => '查无相关数据！'));
        }
    }

    public function position_api(){
        before_api();
        
        checkLogin();

        checkAuth();
        $mall_id = session('mall.mall_id');

        $dept_id = I('dept_id',0,'intval');
        if ($dept_id === 0) {
            Ret(array('code' => 2, 'info' => '请提交部门ID(dept_id)！'));
        }
        $positionModel = D('Position');
        $res = $positionModel->get_list($mall_id,$dept_id);
        if ($res) {
            Ret(array('code' => 1, 'data' => $res));
        }else{
            Ret(array('code' => 2, 'info' => '查无相关数据！'));
        }
    }

}