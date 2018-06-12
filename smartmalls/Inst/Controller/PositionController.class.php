<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/8/19
 * Time: 10:51
 */
namespace Inst\Controller;
class PositionController extends CommonController{
    //机构职位添加
    /*
     * input:name(职位名称）
     */
    public function addPosition(){
        checkLogin();
        before_api();
        $data['dept_id']=I('dept_id');
        $data['name']=I('position_name');
        if(!$data['name']){
            Ret(array('code' => 2, 'info' => '参数（position_name）错误！'));
        }
        $result = D('InstPosition')->add($data);
        if ($result) {
            Ret(array('code' => 1, 'info' => '添加成功！'));
        }else{
            Ret(array('code' => 2, 'info' => '添加失败,系统出错！'));
        }
    }

    //获取职位列表
    /*
     * output:id,name
     */
    public function getPost(){
        checkLogin();
        before_api();
        $deptid=I('dept_id');
        $data = D('InstPosition')->where('dept_id='.$deptid)->select();
        if ($data) {
            Ret(array('code' => 1, 'data' => $data));
        }else{
            Ret(array('code' => 2, 'info' => '数据获取失败,系统出错！'));
        }

    }
}