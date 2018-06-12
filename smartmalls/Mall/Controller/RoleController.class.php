<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/6/2
 * Time: 15:00
 */

namespace Mall\Controller;

class RoleController extends CommonController
{
    //角色列表
    public function index_api(){
        before_api();
        checklogin();
        checkAuth();
        $keyword=I('keyword');
        $page = I('page',1,'intval');
        $pagesize = I('pagesize',10,'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page.','.$pagesize;

        $roleModel=D('Role');
        if($keyword){
            $data=$roleModel->get_role_by_id($keyword);
        }else{
            $count=$roleModel->get_role_count();
            $data=$roleModel->get_role_list($page);
        }
        foreach ($data as $k=>$item) {
            $data[$k]['name']=$data[$k]['rolename'];
        }
        if($data){
            Ret(array('code'=>1,'data'=>$data,'total'=>$count,'page_count'=>ceil($count/$pagesize)));
        }else{
            Ret(array('code'=>2,'data'=>'数据获取失败'));
        }


    }
    //角色增删改api
    public function role_dml_api()
    {
        before_api();
        checkLogin();
        checkAuth();
        $roleModel = D('Role');
        $flag = I('flag');
       $mall_id = session('mall.mall_id');
        switch($flag) {
            case 'add':
                $data['rolename']=I('rolename');
                if (!$data['rolename']) {
                    Ret(array('code' => 2, 'info' => '获取角色名失败!'));
                }
                $data=$roleModel->add_role($data);
                if ($data) {
                    Ret(array('code' => 1, 'info' => '添加角色名成功!'));
                } else {
                    Ret(array('code' => 2, 'info' => '添加角色名失败!'));
                }
                break;
            case 'update':
                $data['id'] = I('id');
                if (!$data['id']) {
                    Ret(array('code' => 2, 'info' => '获取角色id失败!'));
                }
                $data['rolename'] = I('rolename');
                if (!$data['rolename']) {
                    Ret(array('code' => 2, 'info' => '获取角色名失败!'));
                }
                if ($roleModel->update_role($data)) {
                    Ret(array('code' => 1, 'info' => '修改角色名成功!'));
                } else {
                    Ret(array('code' => 2, 'info' => '修改角色名失败!'));
                }
                break;
            case 'delete':
                $id = I('id', '', 'intval');
                if (!$id) {
                    Ret(array('code' => 2, 'info' => '获取角色id失败!'));
                }
                if ($roleModel->delete_role($id)) {
                    Ret(array('code' => 1, 'info' => '删除角色名成功!'));
                } else {
                    Ret(array('code' => 2, 'info' => '删除角色名失败!'));
                }
                break;
            default:
                Ret(array('code' => 2, 'info' => 'Unknowed flag!'));
                break;
        }

    }
    public function login_role(){
        before_api();
        checkLogin();
        checkAuth();
        //$role_id = session('role.role_id');
        $role_id=4;
        $data = D('Role')->get_role_by_id($role_id);
        foreach($data as $key=>$value){
            $data[$key]['first_level_navi_id'] = $this->get_navi($value['id']);
            $data[$key]['second_level_navi_id']=$this->get_navi($value['id']);
            $data[$key]['project'] =$this->get_gongneng($value['id']);
            $data[$key]['auth']=$this->get_gongneng($value['id']);
        }
        if($data){
            Ret(array('code'=>1,'data'=>$data[0]));
        }else{
            Ret(array('code'=>2,'info'=>'没有数据'));
        }

    }
    var $project1=array(1=>'员工',2=>'机构',3=>'教室',4=>'会员');
    public function project1(){
        before_api();
        Ret(array('code'=>1,'data'=>$this->project1));

    }
    private function get_navi($id){
        $naviModel = D('Navi');
        $cats=explode('-',$id);
        foreach($cats as $k=> $v ) {
//            $this->project1($v);
            $cate = $naviModel->get_navi_info($v);

            //var_dump($cate);die();
            if ($cate[0]['first_level_navi_id'] != null && $cate[0]['second_level_navi_id'] != null) {
                $catess=explode(',',$cate[0]['first_level_navi_id']);
            }else{
                $catess=explode(',',$cate[0]['second_level_navi_id']);
            }
        }
        return $catess;
    }
    private function get_gongneng($id){
        $GongnengModel = D('Gongneng');
        $cats=explode('-',$id);
        foreach($cats as $k=> $v ) {
            $cate = $GongnengModel->get_gongneng_info($v);
            if ($cate['project'] != null && $cate['auth'] != null) {
                $catess =explode(',',$cate[0]['project']);
            }else{
                $catess=explode(',',$cate[0]['auth']);
            }
        }
        return $catess;
    }



    public function roleList(){
        $page = I('page',1,'intval');
        $pagesize = I('pagesize',10,'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page.','.$pagesize;
        $roleModel=D('Role');
        $count=$roleModel->get_role_count();
        $data=$roleModel->get_role_list($page);
        if($data){
            Ret(array('code'=>1,'data'=>$data,'total'=>$count,'page_count'=>ceil($count/$pagesize)));
        }else{
            Ret(array('code'=>2,'info'=>'没有数据'));
        }
    }
}