<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/6/2
 * Time: 15:26
 */

namespace Mall\Controller;

class ManuController extends CommonController
{
    //菜单列表
    public function index_api(){
        before_api();
        checklogin();
        checkAuth();
        $gongnengModel=D('Manu');
        $roleModel=D('Role');
        $role=$roleModel->get_list();
        $mall_id = session('mall.mall_id');
        $data=$gongnengModel->get_info($mall_id);
        foreach($data as $key => $value){
            foreach($role as $k=>$v){
                if($value['role_id']==$v['id']){
                    $data[$key]['role_name']=$v['rolename'];
                }
            }
        }

        if($data){
            Ret(array('code'=>1,'data'=>$data));
        }else{
            Ret(array('code'=>2,'data'=>'数据获取失败'));
        }
    }
    //功能增删改api
    public function gongneng_dml_api()
    {
        checkLogin();
        checkAuth();
        $flag = I('flag');
        $gongnengModel = D('Gongneng');
        $mall_id = session('mall.mall_id');
        switch ($flag) {
            case 'add':
                $data['role_id'] = I('role_id');
                if (!$data['role_id']) {
                    Ret(array('code' => 2, 'info' => '请选角色!'));
                }
                $data['project'] = I('project');
                if ($data['project']==null) {
                    Ret(array('code' => 2, 'info' => '请选功能项目!'));
                }
                $data['auth'] = I('auth','');
                if ($gongnengModel->add_gongneng($data)) {
                    Ret(array('code' => 1, 'info' => '添加功能成功!'));
                } else {
                    Ret(array('code' => 2, 'info' => '添加功能失败!'));
                }
                break;
            case 'update':
                $data['id'] = I('id');
                if ($data['id']==null) {
                    Ret(array('code' => 2, 'info' => '获取id失败!'));
                }
                $data['role_id'] = I('role_id');
                if (!$data['role_id']) {
                    Ret(array('code' => 2, 'info' => '请选角色!'));
                }
                $data['role_id'] = I('role_id');
                if ($data['role_id']==null) {
                    Ret(array('code' => 2, 'info' => '获取导航名ID失败!'));
                }
                $data['project'] = I('project');
                if ($data['project']==null) {
                    Ret(array('code' => 2, 'info' => '请选功能项目!'));
                }
                $data['auth'] = I('auth','');
                if ($data['auth']==null) {
                    Ret(array('code' => 2, 'info' => '请选二级菜单!'));
                }
                if ($gongnengModel->update_gongneng($data)) {
                    Ret(array('code' => 1, 'info' => '保存成功!'));
                } else {
                    Ret(array('code' => 2, 'info' => '更新失败!'));
                }
                break;
            case 'delete':
                $id = I('id', '');
                if ($id==null) {
                    Ret(array('code' => 2, 'info' => '获取id失败!'));
                }
                if ($gongnengModel->delete_gongneng($id)) {
                    Ret(array('code' => 1, 'info' => '删除成功!'));
                } else {
                    Ret(array('code' => 2, 'info' => '删除失败!'));
                }
                break;
            default:
                Ret(array('code' => 2, 'info' => 'Unknowed flag!'));
                break;
        }



}

}