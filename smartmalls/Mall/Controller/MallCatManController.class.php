<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/8/17
 * Time: 10:20
 */
namespace Mall\Controller;
class MallCatManController extends CommonController{

    //新增部门
    public function addDept(){
        $data['name'] = I('name');
        if(!$data['name']){
            Ret(array('code'=>2,'info'=>'参数（name）错误！'));
        }
        $data['mall_id'] = session('mall.mall_id');
        $res = D('Dept')->add($data);
        if($res){
            Ret(array('code'=>1,'info'=>'添加成功！'));
        }else{
            Ret(array('code'=>2,'info'=>'添加失败！'));
        }
    }

    //新增职位
    public function addPost(){
        $data['dept_id'] = I('dept_id');
        if(!$data['dept_id']){
            Ret(array('code'=>2,'info'=>'参数部门ID（dept_id）错误！'));
        }
        $data['name'] = I('name');
        if(!$data['name']){
            Ret(array('code'=>2,'info'=>'参数（name）错误！'));
        }
        $data['mall_id'] = session('mall.mall_id');
        $res = D('MallPosition')->add($data);
        if($res){
            Ret(array('code'=>1,'info'=>'添加成功！'));
        }else{
            Ret(array('code'=>2,'info'=>'添加失败！'));
        }
    }

    //删除部门
    public function delDept(){
        $id = I('id');
        if($id<1){
            Ret(array('code'=>2,'info'=>'参数（name）错误！'));
        }
        $res = D('Dept')->where('id='.$id)->delete();
        if($res){
            Ret(array('code'=>1,'info'=>'删除成功！'));
        }else{
            Ret(array('code'=>2,'info'=>'删除失败！'));
        }
    }

    //删除职位
    public function delPost(){
        $id = I('id');
        if($id<1){
            Ret(array('code'=>2,'info'=>'参数（name）错误！'));
        }
        $res = D('MallPosition')->where('id='.$id)->delete();
        if($res){
            Ret(array('code'=>1,'info'=>'删除成功！'));
        }else{
            Ret(array('code'=>2,'info'=>'删除失败！'));
        }
    }

    /**
     * 修改部门
     */
    public function updateDept(){
        $data['id'] = I('id');
        if(!$data['id'] ){
            Ret(array('code' => 2, 'info' => '参数（id）错误！'));
        }
        $data['name'] = I('name');
        if(!$data['name'] ){
            Ret(array('code' => 2, 'info' => '参数（name）错误！'));
        }
        $res = D('Dept')->save($data);
        if($res){
            Ret(array('code'=>1,'info'=>'修改成功！'));
        }else{
            Ret(array('code'=>2,'info'=>'修改失败！'));
        }
    }

    /**
     * 修改职位
     */
    public function updatePost(){
        $data['id'] = I('id');
        if(!$data['id'] ){
            Ret(array('code' => 2, 'info' => '参数（id）错误！'));
        }
        $data['name'] = I('name');
        if(!$data['name'] ){
            Ret(array('code' => 2, 'info' => '参数（name）错误！'));
        }
        $res = D('MallPosition')->save($data);
        if($res){
            Ret(array('code'=>1,'info'=>'修改成功！'));
        }else{
            Ret(array('code'=>2,'info'=>'修改失败！'));
        }
    }


    public function dept_api(){
        $deptModel = D('Dept');
        $res = $deptModel->get_dept();
        if ($res) {
            Ret(array('code' => 1, 'data' => $res));
        }else{
            Ret(array('code' => 2, 'info' => '查无相关数据！'));
        }
    }

    public function position_api(){
        $dept_id = I('id');
        if ($dept_id === 0) {
            Ret(array('code' => 2, 'info' => '请提交部门ID(dept_id)！'));
        }
        $positionModel = D('Position');
        $res = $positionModel->get_position($dept_id);
        if ($res) {
            Ret(array('code' => 1, 'data' => $res));
        }else{
            Ret(array('code' => 2, 'info' => '查无相关数据！'));
        }
    }
}