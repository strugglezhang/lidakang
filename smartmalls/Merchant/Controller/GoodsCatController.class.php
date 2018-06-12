<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/8/22
 * Time: 11:43
 */
namespace Merchant\Controller;
use Think\Controller;
class GoodsCatController extends CommonController{
    public function addCats(){
        checkLogin();
        checkAuth();
        $data['name'] = I('name');
        if(!$data['name'] ){
            Ret(array('code' => 2, 'info' => '参数（name）错误！'));
        }
        $data = D('GoodsCat')->add($data);
        if($data){
            Ret(array('code' => 1, 'data' => '添加成功'));
        }else{
            Ret(array('code' => 2, 'info' => '添加失败！'));
        }
    }

    //获取分类列表
    public function addSubCat(){
        checkLogin();
        checkAuth();
        $data['name'] = I('name');
        if(!$data['name'] ){
            Ret(array('code' => 2, 'info' => '参数（name）错误！'));
        }
        $data['goods_catid'] = I('goods_catid');
        if(!$data['goods_catid'] ){
            Ret(array('code' => 2, 'info' => '参数（goods_catid）错误！'));
        }
        $data = D('GoodsSubCat')->add($data);
        if($data){
            Ret(array('code' => 1, 'data' => '添加成功'));
        }else{
            Ret(array('code' => 2, 'info' => '添加失败！'));
        }
    }



    //修改类别
    public function updateCats(){
        checkLogin();
        checkAuth();
        $data['id'] = I('id');
        if(!$data['id'] ){
            Ret(array('code' => 2, 'info' => '参数（id）错误！'));
        }
        $data['name'] = I('name');
        if(!$data['name'] ){
            Ret(array('code' => 2, 'info' => '参数（name）错误！'));
        }


        $res = D('GoodsCat')->save($data);
        if($res){
            Ret(array('code' => 1, 'data' => '修改成功！'));
        }else{
            Ret(array('code' => 2, 'info' => '修改失败！'));
        }
    }

    //修改分类
    public function updateCls()
    {
        checkLogin();
        checkAuth();
        $data['id'] = I('id');
        if(!$data['id'] ){
            Ret(array('code' => 2, 'info' => '参数（id）错误！'));
        }
        $data['name'] = I('name');
        if(!$data['name'] ){
            Ret(array('code' => 2, 'info' => '参数（name）错误！'));
        }
//        $data['goods_catid'] = I('goods_catid');
//        if(!$data['goods_catid'] ){
//            Ret(array('code' => 2, 'info' => '参数（goods_catid）错误！'));
//        }


        $res = D('GoodsSubCat')->save($data);
        if($res){
            Ret(array('code' => 1, 'data' => '修改成功！'));
        }else{
            Ret(array('code' => 2, 'info' => '修改失败！'));
        }

    }
    //删除类别
    public function deleteCats(){
        checkLogin();
        checkAuth();
        $data['id'] = I('id');
        if(!$data['id'] ){
            Ret(array('code' => 2, 'info' => '参数（id）错误！'));
        }

        $res = D('GoodsCat')->where('id='.$data['id'])->delete();
        if($res){
            Ret(array('code' => 1, 'data' => '删除成功！'));
        }else{
            Ret(array('code' => 2, 'info' => '删除失败！'));
        }
    }

    //删除分类
    public function deleteCls()
    {
        checkLogin();
        checkAuth();
        $data['id'] = I('id');
        if(!$data['id'] ){
            Ret(array('code' => 2, 'info' => '参数（id）错误！'));
        }

        $res = D('GoodsSubCat')->where('id='.$data['id'])->delete();
        if($res){
            Ret(array('code' => 1, 'data' => '删除成功！'));
        }else{
            Ret(array('code' => 2, 'info' => '删除失败！'));
        }

    }





    public function Cats(){
        checkLogin();
        checkAuth();
        $data = D('GoodsCat')->field('id,name')->select();
        if($data){
            Ret(array('code' => 1, 'data' => $data));
        }else{
            Ret(array('code' => 2, 'info' => '查无相关数据！'));
        }
    }

    //获取分类列表
    public function SubCat(){
        checkLogin();
        checkAuth();
        $goods_catid = I('id');
        if(!$goods_catid ){
            Ret(array('code' => 2, 'info' => '参数（goods_catid）错误！'));
        }
        $goodsSubCatModel = D('GoodsSubCat');
        $data = $goodsSubCatModel->get_cls($goods_catid);
        if($data){
            Ret(array('code' => 1, 'data' =>$data));
        }else{
            Ret(array('code' => 2, 'info' => '查无相关数据！'));
        }
    }
}