<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/8/19
 * Time: 10:50
 */
namespace Merchant\Controller;
class DepartmentController extends CommonController{
    /*机构部门添加
     * input:name(职位名称）
     */
    public function addDepartment(){
        checkLogin();
        before_api();
        $data['name']=I('dept_name');
        if(!$data['name']){
            Ret(array('code' => 2, 'info' => '参数（dept_name）错误！'));
        }
        $result = D('MerchantDepartment')->add($data);
        if ($result) {
            Ret(array('code' => 1, 'info' => '添加成功！'));
        }else{
            Ret(array('code' => 2, 'info' => '添加失败,系统出错！'));
        }
    }

    //获取部门列表
    /*
     * output:id,name
     */
    public function getDepartment(){
        checkLogin();
        before_api();
        $data = D('MerchantDepartment')->select();
        if ($data) {
            Ret(array('code' => 1, 'data' => $data));
        }else{
            Ret(array('code' => 2, 'info' => '数据获取失败,系统出错！'));
        }

    }
}