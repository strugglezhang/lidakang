<?php
namespace Mall\Controller;

class CountController extends CommonController{
    public function index_api(){
        //账户管理
        before_api();
        checkLogin();
        checkAuth();
        $keyword=I('keyword');
        $dept_id = I('dept_id',0,'intval');
        $page = I('page',1,'intval');
        $pagesize = I('pagesize',10,'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page.','.$pagesize;
        $memberList=D('Count');
        $memberRole=D('MemberRole');
        $total=$memberList->get_count_total($keyword,$dept_id);
        $memberInfo=$memberList->get_count_info($keyword,$dept_id,$page);
        foreach ( $memberInfo as $key => $value) {
            $role=$memberRole->get_roleID_by_number($value['id']);
//            var_dump($role);die;
            $memberInfo[$key ]['role_id']=$role['role_id'];
            $memberInfo[$key ]['role']=$role['role'];
            //var_dump($role['sex']);die;
            $memberInfo[$key ]['sex']=get_sex($value['sex']);
            $memberInfo[$key ]['age']=getAgeByBirthday($value['birthday']);
            $memberInfo[$key ]['password']='******';
        }
        if($memberInfo){
            Ret(array('code'=>'1','data'=>$memberInfo, 'total' => $total, 'page_count' => ceil($total/$pagesize)));

        }else{
            Ret(array('code'=>'2','info'=>'信息读取错误！'));
        }
    }
//密码重置
    public function password_reset_api(){
        before_api();
        checkAuth();
        $memberList=D('Count');
        $data['number']=I('number');
        $data['password']=I('password');
        $result=$memberList->update($data);
        if($result){
            Ret(array('code'=>1,'info'=>'密码更新成功'));
        }
        if(!$result){
            Ret(array('code'=>2,'info'=>'密码更新失败'));
        }
    }

    /**
     * 修改角色
     */
    public function update_role(){
        $id =I('id');
        $role_id =I('role_id');
        $rolename =I('rolename');
        $memberRoleModel=D('MemberRole');
        $mall=$memberRoleModel->updateRole($id);
        $data['id'] =$mall[0]['id'];
        $data['role_id'] =$role_id;
        $data['role'] =$rolename;
        $res=$memberRoleModel->updateMallRole($data);
        if($res){
            Ret(array('code'=>1,'info'=>'修改成功'));
        }else{
            Ret(array('code'=>2,'info'=>'修改失败'));
        }
    }

    /**
     * 角色列表
     */
    public function getRole(){
        $data = D('Role')->field('id,rolename')->select();
        if($data){
            Ret(array('code'=>1,'data'=>$data));
        }else{
            Ret(array('code'=>2,'info'=>'没有数据'));
        }
    }


    /**
     * 重置密码
     */
    public function resetPassword(){
        $id = I('id');
		/**$mall= getPassword($id);
        $data['id']=$mall[0]['id'];
        $data['password']=$mall[0]['password'];
        if($data['password']==createPassword(888888)){
            Ret(array('code'=>2,'info'=>'密码已重置'));
        }***/
        $data['password']=createPassword(888888);
		$res = updatePassword($data,$id);
            Ret(array('code'=>1,'info'=>'重置成功'));
    }



}
