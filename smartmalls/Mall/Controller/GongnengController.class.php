<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/6/2
 * Time: 15:28
 */

namespace Mall\Controller;


class GongnengController extends CommonController
{
    //导航功能列表
    public function index_api(){
        before_api();
        checklogin();
        checkAuth();
        $gongnengModel=D('Gongneng');
        $roleModel=D('Role');
        $page = I('page',1,'intval');
        $pagesize = I('pagesize',10,'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page.','.$pagesize;
        $role=$gongnengModel->get_gongneng_list();
        //var_dump($role);die;
        $mall_id = session('mall.mall_id');
        $data=$gongnengModel->get_gongneng_info($mall_id);
        $count = $gongnengModel->get_gongneng_count($page);
        foreach($data as $key => $value){
            $data[$key]['project']=$this->get_project($value['project']);
            $data[$key]['auth']=$this->get_auth($value['auth']);
            $role=$roleModel->get_role_by_id($value['id']);
            $data[$key]['role_name']=$role[0]['rolename'];
        }
        if($data){
            Ret(array('code'=>1,'data'=>$data, 'total' => $count,'page_count'=>ceil($count/$pagesize)));
        }else{
            Ret(array('code'=>2,'data'=>'数据获取失败'));
        }
    }

    var $project=array(array('id'=>1,'name'=>'员工'),array('id'=>2,'name'=>'机构'),array('id'=>3,'name'=>'教室'),array('id'=>4,'name'=>'会员'));
    public function project(){
        before_api();
        Ret(array('code'=>1,'data'=>$this->project));

    }
    var $auth=array(array('id'=>1,'name'=>'新增'),array('id'=>2,'name'=>'修改'),array('id'=>3,'name'=>'删除'),array('id'=>4,'name'=>'审核'),array('id'=>5,'name'=>'查看'));
    public function auth(){
        before_api();
        Ret(array('code'=>1,'data'=>$this->auth));

    }


   var $project1=array(1=>'员工',2=>'机构',3=>'教室',4=>'会员');
    public function project1(){
        before_api();
        Ret(array('code'=>1,'data'=>$this->project1));

    }
    var $auth1=array(1=>'新增',2=>'修改',3=>'删除',4=>'审核',5=>'查看');
    public function auth1(){
        before_api();
        Ret(array('code'=>1,'data'=>$this->auth1));

    }
    private function get_project($id){
        $array = explode(',', $id);
        foreach ($array as $k => $v) {
            $items[$k] = $this->project1[$v];
        }
        return implode(',', $items);
    }
    private function get_auth($id){
        $array = explode(',', $id);
        foreach ($array as $k => $v) {
            $items[$k] = $this->auth1[$v];
        }
        return implode(',', $items);
    }
    //功能增删改api
    public function gongneng_dml_api()
    {
        before_api();
        checkLogin();
        checkAuth();
        $flag = I('flag');
        $gongnengModel = D('Gongneng');

        $roleModel = D('Role');
        switch ($flag) {
            case 'add':
                $data['role_neme'] = I('role_name');
                if (!$data['role_neme']) {
                    Ret(array('code' => 2, 'info' => '请选角色!'));
                }
                $data['project'] = I('project');
                if ($data['project']==null) {
                    Ret(array('code' => 2, 'info' => '请选功能项目!'));
                }
                $data['auth'] = I('auth','');
               $data= $gongnengModel->add_gongneng($data);
                $data=$roleModel->add_role($data);
                if ($data) {
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