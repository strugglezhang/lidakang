<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/8/13
 * Time: 6:20
 */
namespace Inst\Controller;
use Think\Controller;
class InstCatManController extends CommonController{
    //获取类别列表
    public function getCats(){
        checkLogin();
        checkAuth();
        $data['name'] = I('name');
        if(!$data['name'] ){
            Ret(array('code' => 2, 'info' => '参数（name）错误！'));
        }
        $data = D('InstCat')->add($data);
        if($data){
            Ret(array('code' => 1, 'data' => '添加成功'));
        }else{
            Ret(array('code' => 2, 'info' => '添加失败'));
        }
    }

    //获取分类列表
    public function getCls()
    {
        checkLogin();
        checkAuth();
        $data['name'] = I('name');
        if(!$data['name'] ){
            Ret(array('code' => 2, 'info' => '参数（name）错误！'));
        }
        $data['category_id'] = I('category_id');
        if(!$data['category_id'] ){
            Ret(array('code' => 2, 'info' => '参数（category_id）错误！'));
        }
        $data = D('InstCls')->add($data);
        if ($data) {
            Ret(array('code' => 1, 'data' => '添加成功'));
        } else {
            Ret(array('code' => 2, 'info' => '添加失败！'));
        }
    }

    //获取行业列表e
    public function getIdt(){
        checkLogin();
        checkAuth();
        $data['name'] = I('name');
        if(!$data['name'] ){
            Ret(array('code' => 2, 'info' => '参数（name）错误！'));
        }
        $data['classify_id'] = I('classify_id');
        if(!$data['classify_id'] ){
            Ret(array('code' => 2, 'info' => '参数（classify_id）错误！'));
        }
        $data = D('InstIdt')->add($data);
        if ($data) {
            Ret(array('code' => 1, 'data' => '添加成功'));
        } else {
            Ret(array('code' => 2, 'info' => '添加失败！'));
        }

    }
    //获取细分列表
    public function getSpf(){
        checkLogin();
        checkAuth();
        $data['name'] = I('name');
        if(!$data['name'] ){
            Ret(array('code' => 2, 'info' => '参数（name）错误！'));
        }

        $data['industry_id'] = I('industry_id');
        if(!$data['industry_id'] ){
            Ret(array('code' => 2, 'info' => '参数（industry_id）错误！'));
        }
        $data = D('InstSpf')->add($data);
        if ($data) {
            Ret(array('code' => 1, 'data' => '添加成功'));
        } else {
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
        $res = D('InstCat')->save($data);
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
//        $data['category_id'] = I('category_id');
//        if(!$data['category_id'] ){
//            Ret(array('code' => 2, 'info' => '参数（category_id）错误！'));
//        }
//        var_dump($res);die;
        $res = D('InstCls')->save($data);
        if($res){
            Ret(array('code' => 1, 'data' => '修改成功！'));
        }else{
            Ret(array('code' => 2, 'info' => '修改失败！'));
        }

    }

    //修改行业
    public function updateIdt(){

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
//        $data['classify_id'] = I('classify_id');
//        if(!$data['classify_id'] ){
//            Ret(array('code' => 2, 'info' => '参数（classify_id）错误！'));
//        }
//        var_dump($data);die;
        $data = D('InstIdt')->save($data);
        if($data){
            Ret(array('code' => 1, 'data' => '修改成功！'));
        }else{
            Ret(array('code' => 2, 'info' => '修改失败！'));
        }


    }

    //修改细类
    public function updateSpf(){

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
//        $data['industry_id'] = I('industry_id');
//        if(!$data['industry_id'] ){
//            Ret(array('code' => 2, 'info' => '参数（industry_id）错误！'));
//        }
        $data = D('InstSpf')->save($data);
        if($data){
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

        $res = D('InstCat')->where('id='.$data['id'])->delete();
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

        $res = D('InstCls')->where('id='.$data['id'])->delete();
        if($res){
            Ret(array('code' => 1, 'data' => '删除成功！'));
        }else{
            Ret(array('code' => 2, 'info' => '删除失败！'));
        }

    }

    //删除行业
    public function deleteIdt(){

        checkLogin();
        checkAuth();
        $data['id'] = I('id');
        if(!$data['id'] ){
            Ret(array('code' => 2, 'info' => '参数（id）错误！'));
        }

        $res = D('InstIdt')->where('id='.$data['id'])->delete();
        if($res){
            Ret(array('code' => 1, 'data' => '删除成功！'));
        }else{
            Ret(array('code' => 2, 'info' => '删除失败！'));
        }


    }


    //删除细类
    public function deleteSpf(){

        checkLogin();
        checkAuth();
        $data['id'] = I('id');
        if(!$data['id'] ){
            Ret(array('code' => 2, 'info' => '参数（id）错误！'));
        }

        $res = D('InstSpf')->where('id='.$data['id'])->delete();
        if($res){
            Ret(array('code' => 1, 'data' => '删除成功！'));
        }else{
            Ret(array('code' => 2, 'info' => '删除失败！'));
        }

    }

    /**
     * 分级列表
     */
    public function Cats(){
        checkLogin();
        checkAuth();
        $data = D('InstCat')->field('id,name')->select();
        if($data){
            Ret(array('code' => 1, 'data' => $data));
        }else{
            Ret(array('code' => 2, 'info' => '查无相关数据！'));
        }
    }
    public function Cls()
    {
        checkLogin();
        checkAuth();
        $category_id= I('id');
        $instClsModel = D('InstCls');
        $data = $instClsModel->get_Spf($category_id);
        if ($data) {
            Ret(array('code' => 1,'data' => $data));
        } else {
            Ret(array('code' => 2, 'info' => '查无相关数据！'));
        }
    }

    //获取行业列表e
    public function Idt(){
        checkLogin();
        checkAuth();
        $classify_id= I('id');
        if(!$classify_id ){
            Ret(array('code' => 2, 'info' => '参数（classify_id）错误！'));
        }
//        $category_id= I('category_id');
//        if(!$category_id ){
//            Ret(array('code' => 2, 'info' => '参数（category_id）错误！'));
//        }
        $instIdtModel = D('InstIdt');
        $data = $instIdtModel->get_Spf($classify_id);
        if ($data) {
            Ret(array('code' => 1, 'data' => $data));
        } else {
            Ret(array('code' => 2, 'info' => '查无相关数据！'));
        }

    }
    //获取细分列表
    public function Spf(){
        checkLogin();
        checkAuth();

        $industry_id= I('id');
        if(!$industry_id ){
            Ret(array('code' => 2, 'info' => '参数（industry_id）错误！'));
        }
        $instSpfModel = D('InstSpf');
        $data = $instSpfModel->get_Spf($industry_id);
        if ($data) {
            Ret(array('code' => 1, 'data' => $data));
        } else {
            Ret(array('code' => 2, 'info' => '查无相关数据！'));
        }
    }
}