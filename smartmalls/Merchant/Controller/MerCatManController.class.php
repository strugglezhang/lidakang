<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/8/16
 * Time: 15:09
 */

namespace Merchant\Controller;
use Think\Controller;
class MerCatManController extends CommonController
{
    //获取类别列表
    public function getCats()
    {
        checkLogin();
        checkAuth();
        $data = D('MerchantCat')->select();
        if ($data) {
            Ret(array('code' => 1, 'data' => $data));
        } else {
            Ret(array('code' => 2, 'info' => '查无相关数据！'));
        }
    }

    //获取分类列表
    public function getCls()
    {

        $category_id = I('id');
        if(!$category_id ){
            Ret(array('code' => 2, 'info' => '参数（category_id）错误！'));
        }
        $merchantSubcattModel = D('MerchantSubCat');
        $data = $merchantSubcattModel->get_cls($category_id);
//        $data = D('MerchantSubCat')->select();
        if ($data) {
            Ret(array('code' => 1, 'data' => $data));
        } else {
            Ret(array('code' => 2, 'info' => '查无相关数据！'));
        }
    }


    //修改类别
    public function updateCats()
    {
        checkLogin();
        checkAuth();
        $data['id'] = I('id');
        if (!$data['id']) {
            Ret(array('code' => 2, 'info' => '参数（id）错误！'));
        }
        $data['name'] = I('name');
        if (!$data['name']) {
            Ret(array('code' => 2, 'info' => '参数（name）错误！'));
        }


        $res = D('MerchantCat')->save($data);
        if ($res) {
            Ret(array('code' => 1, 'data' => '修改成功！'));
        } else {
            Ret(array('code' => 2, 'info' => '修改失败！'));
        }
    }

    //修改分类
    public function updateCls()
    {
        checkLogin();
        checkAuth();
        $data['id'] = I('id');
        if (!$data['id']) {
            Ret(array('code' => 2, 'info' => '参数（id）错误！'));
        }
        $data['name'] = I('name');
        if (!$data['name']) {
            Ret(array('code' => 2, 'info' => '参数（name）错误！'));
        }
//        $data['category_id'] = I('category_id');
//        if (!$data['category_id']) {
//            Ret(array('code' => 2, 'info' => '参数（category_id）错误！'));
//        }
        $res = D('MerchantSubCat')->save($data);
        if ($res) {
            Ret(array('code' => 1, 'data' => '修改成功！'));
        } else {
            Ret(array('code' => 2, 'info' => '修改失败！'));
        }

    }


    //删除类别
    public function deleteCats()
    {
        checkLogin();
        checkAuth();
        $data['id'] = I('id');
        if (!$data['id']) {
            Ret(array('code' => 2, 'info' => '参数（id）错误！'));
        }

        $res = D('MerchantCat')->where('id=' . $data['id'])->delete();
        if ($res) {
            Ret(array('code' => 1, 'data' => '删除成功！'));
        } else {
            Ret(array('code' => 2, 'info' => '删除失败！'));
        }
    }

    //删除分类
    public function deleteCls()
    {
        checkLogin();
        checkAuth();
        $data['id'] = I('id');
        if (!$data['id']) {
            Ret(array('code' => 2, 'info' => '参数（id）错误！'));
        }

        $res = D('MerchantSubCat')->where('id=' . $data['id'])->delete();
        if ($res) {
            Ret(array('code' => 1, 'data' => '删除成功！'));
        } else {
            Ret(array('code' => 2, 'info' => '删除失败！'));
        }

    }

    /**
     * 新增类别
     */
    public function addCats()
    {
        checkLogin();
        checkAuth();
        $data['name']=I('name');
        if(!$data['name']){
            Ret(array('code' => 2, 'info' => '参数（name）错误！'));
        }
        $data = D('MerchantCat')->add($data);
        if ($data) {
            Ret(array('code' => 1, 'data' => '添加成功！'));
        } else {
            Ret(array('code' => 2, 'info' => '添加失败！'));
        }
    }

    //新增分类列表
    public function addCls()
    {
        checkLogin();
        checkAuth();
        $data['name']=I('name');
        if(!$data['name']){
            Ret(array('code' => 2, 'info' => '参数（name）错误！'));
        }
        $data['category_id']=I('category_id');
        if(!$data['category_id']){
            Ret(array('code' => 2, 'info' => '参数（category_id）错误！'));
        }
        $data = D('MerchantSubCat')->add($data);
        if ($data) {
            Ret(array('code' => 1, 'data' => '添加成功！'));
        } else {
            Ret(array('code' => 2, 'info' => '添加失败！'));
        }
    }

    /**
     * 商户分级列表
     */
    public function Cls()
    {
        checkLogin();
        checkAuth();
        $category_id=I('id');
        if(!$category_id){
            Ret(array('code' => 2, 'info' => '参数（category_id）错误！'));
        }
        $merchantSubCat = D('MerchantSubCat');
        $data=$merchantSubCat->get_cls($category_id);
        if ($data) {
            Ret(array('code' => 1, 'data' => $data));
        } else {
            Ret(array('code' => 2, 'info' => '查无相关数据！'));
        }
    }


    public function Cat()
    {
        checkLogin();
        checkAuth();
        $data = D('MerchantCat')->field('id,name')->select();
        if ($data) {
            Ret(array('code' => 1, 'data' => $data));
        } else {
            Ret(array('code' => 2, 'info' => '查无相关数据！'));
        }
    }

}