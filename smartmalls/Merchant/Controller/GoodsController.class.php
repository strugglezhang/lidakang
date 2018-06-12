<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/6/2
 * Time: 15:32
 */

namespace Merchant\Controller;

class GoodsController extends CommonController
{
    //商品信息管理
    public function index(){
        $this->show('goods index');
    }
	
	//商品信息管理api
	public function index_api(){
        before_api();
        checkLogin();
        checkAuth();
        $merchant_id=session('merchant.merchant_id');
        if(!isset($merchant_id)){
            $merchant_id = I('merchant_id');
        }
        $goods_catid = I('goods_catid',0,'intval');
        $keyword = I('keyword');
        $page = I('page',1,'intval');
        $pagesize = I('pagesize',10,'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page.','.$pagesize;

        $goodsModel = D('Goods');
        $merchangtModel =D('Merchant');
        $gcatModel = D('GoodsCat');
        $subCatModel=D('GoodsSubCat');
        $count = $goodsModel->get_count($merchant_id,$goods_catid,$keyword);
        if ($count) {
            $fields = 'id,number,goods_catid,goods_sub_catid,name,price,count,state,discount,merchant_id';
            $res = $goodsModel->get_list($merchant_id,$goods_catid,$keyword,$page,$fields);
            if (empty($res)) {
                Ret(array('code' => 2, 'info' => '查无相关数据！'));
            }else{
                foreach ($res as $key => $value) {
                    $cat = $gcatModel->get_cat($value['goods_catid']);
                    $res[$key]['catigory_name']=$cat['name'];
                    $state=get_state($value['state']);
                    $res[$key]['state']=$state;
                    $subcat = $subCatModel->get_sub_cat($value['goods_sub_catid']);
                    $res[$key]['sub_category_name']=$subcat['name'];
                    $merchnt = $merchangtModel->get_cat_by_id($value['merchant_id']);
                    $res[$key]['merchant_name']=$merchnt['name'];
                }
                Ret(array('code' => 1, 'data' => $res, 'total' => $count, 'page_count' => ceil($count/$pagesize)));
            }
        }else{
            Ret(array('code' => 2, 'info' => '查无相关数据！'));
        }
	
	}

    public function goods_view_api(){
        before_api();
        checkLogin();
        checkAuth();

        $id=I('id');
        if(!$id){
            Ret(array('code' => 2, 'info' => '获取参数（id）失败！'));
        }
        $serveModel = D('Goods');
        $goodsCategoryModel = D('GoodsCat');
        $goodsSubCategoryModel = D('GoodsSubCat');
        $fields=('id,mall_id,state,merchant_id,pic,number,code,goods_catid,goods_sub_catid,name,price,count,discount,submitter_id');
        $data=$serveModel->get_goods_info($id,$fields);
        $cate = $goodsCategoryModel->get_cat_by_id($data['goods_catid']);
        $data['goods_catname']=$cate['name'];
        $subcate = $goodsSubCategoryModel->get_sub_cat_by_id($data['goods_catid']);
        $data['goods_sub_catname']=$subcate['name'];
        if ($data) {
            Ret(array('code' => 1, 'data' => $data));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据！'));
        }
    }

    /*
     * 商品折扣计费
     */
    public function cost_index(){
        before_api();
        checkLogin();
        checkAuth();
        $mall_id=session('mall.mall_id');
        $state =I('state',0,'intval');
        if($state !==2 && !$mall_id){
            $state =2;
        }
        $keyword = I('keyword');
        $goods_category_id = I('goods_category_id',0,'intval');
        $page = I('page',1,'intval');
        $pagesize =I('pagesize',10,'intval');
        $pagesize =$pagesize < 1 ? 1:$pagesize;
        $pagesize =$pagesize > 50 ? 50 :$pagesize;
        $page =$page.','.$pagesize;
//        $goodsModel = D('Goods');
        $goodsDiscountModel = D('GoodsDiscount');
        $count =$goodsDiscountModel->get_count($mall_id,$goods_category_id,$keyword);
        $data =$goodsDiscountModel->get_list($mall_id,$goods_category_id,$keyword,$page,$fields = null);
//        var_dump($data);die;
        foreach($data as $key =>$item){
            $data[$key]['state'] =get_state($item['state']);
            $data[$key]['category'] =$this->get_cat_by_id($item['goods_category_id']);
            $data[$key]['sub_category']=$this->get_sub_cat($item['course_sub_category_id']);
            $data[$key]['goods_name']=$this->get_goods_by_id($item['goods_id']);
            $data[$key]['merchant_name']=$this->get_merchant_by_id($item['merchant_id']);
        }
        if($data){
            Ret(array('code'=>1,'data' =>$data,'total' =>$count,'page_count'=>ceil($count/$pagesize)));
        }else{
            Ret(array('code' =>2,'info'=>'没有数据！'));
        }

        }
    private function get_cat_by_id($id){
        $goodsCategoryModel = D('GoodsCat');
        $cats=explode('-',$id);
        foreach($cats as $k => $v ) {
            $cate = $goodsCategoryModel->get_cat_by_id($v);
            if ($cate[name] != null) {
                $catess[$k] = $cate[name];
            }
            $data= implode(',', $catess);
        }
        return $data;
    }
    private function get_sub_cat($id){
        $goodsSubCategoryModel = D('GoodsSubCat');
        $array=explode('-',$id);
        foreach($array as $k => $v ) {
            $item = $goodsSubCategoryModel->get_sub_cat_by_id($v);
            if ($item[name] != null) {
                $items[$k] = $item[name];
            }
            $data= implode(',', $items);
        }
        return $data;
    }


    private function get_goods_by_id($id){
        $goodsModel = D('Goods');
        $array=explode('-',$id);
        foreach($array as $k => $v ) {
            $item = $goodsModel->get_goods_by_id($v);
            if ($item[name] != null) {
                $items[$k] = $item[name];
            }
            $data= implode(',', $items);
        }
        return $data;
    }
    private function get_merchant_by_id($id){
        $merchantModel = D('Merchant');
        $array=explode('-',$id);
        foreach($array as $k => $v ) {
            $item = $merchantModel->get_cat_by_id($v);
            if ($item[name] != null) {
                $items[$k] = $item[name];
            }
            $data= implode(',', $items);
        }
        return $data;
    }



    //商品信息增删改api
    public function index_dml_api(){
        before_api();
        checkLogin();
        checkAuth();
        $mall_id=session('mall.mall_id');
        $flag=I('flag');
        $goodsModel = D('Goods');
        switch($flag){
            case 'add':
                $data['mall_id']=$mall_id;
                if(!$data['mall_id']){
                    Ret(array('code' => 2, 'info' => '登录数据获取失败！'));
                }
                $data['merchant_id']=I('merchant_id');
                if(!$data['merchant_id']){
                    Ret(array('code' => 2, 'info' => '登录数据获取失败！'));
                }
                $data['pic']=I('pic');
                $data['code']=I('code');
                if(!$data['code']){
                    Ret(array('code' => 2, 'info' => '请输入商品条码！'));
                }
                $data['goods_catid']=I('goods_catid');
                if(!$data['goods_catid']){
                    Ret(array('code' => 2, 'info' => '请选择商品类别！'));
                }
                $data['goods_sub_catid']=I('goods_sub_catid');
                if(!$data['goods_sub_catid']){
                    Ret(array('code' => 2, 'info' => '请选择商品子类！'));
                }
                $data['name']=I('name');
                if(!$data['name']){
                    Ret(array('code' => 2, 'info' => '请选择商品名！'));
                }
                $data['price']=I('price');
                if(!$data['price']){
                    Ret(array('code' => 2, 'info' => '请选择商品价格！'));
                }
                $data['count']=I('count');
                if(!$data['count']){
                    Ret(array('code' => 2, 'info' => '请选择商品数量！'));
                }
                $data['discount']=I('discount');
                if(!$data['discount']){
                    Ret(array('code' => 2, 'info' => '请选择商品会员折扣！'));
                }
                $data['state']=0;
                $data['check_type']=0;
                $data['submitter'] =session('worker_name');
                $data['submitter_id'] =session('worker_id');
                $res=$goodsModel->add_goods($data);
                if ($res) {
                    Ret(array('code' => 1, 'info' => '添加成功！'));
                }else{
                    Ret(array('code' => 2, 'info' => '添加失败,系统出错！'));
                }
                break;

            case 'update':
                $data['id']=I('id',0,'intval');
                if(!$data['id']){
                    Ret(array('code' => 2, 'info' => '数据获取失败！'));
                }
                $data['merchant_id']=I('merchant_id');
                if(!$data['merchant_id']){
                    Ret(array('code' => 2, 'info' => '登录数据获取失败！'));
                }
                $data['pic']=I('pic');
                $data['code']=I('code');
                if(!$data['code']){
                    Ret(array('code' => 2, 'info' => '请输入商品条码！'));
                }
                $data['goods_catid']=I('goods_catid');
                if(!$data['goods_catid']){
                    Ret(array('code' => 2, 'info' => '请选择商品类别！'));
                }
                $data['goods_sub_catid']=I('goods_sub_catid');
                if(!$data['goods_sub_catid']){
                    Ret(array('code' => 2, 'info' => '请选择商品子类！'));
                }
                $data['name']=I('name');
                if(!$data['name']){
                    Ret(array('code' => 2, 'info' => '请选择商品名！'));
                }
                $data['price']=I('price');
                if(!$data['price']){
                    Ret(array('code' => 2, 'info' => '请选择商品价格！'));
                }
                $data['count']=I('count');
                if(!$data['count']){
                    Ret(array('code' => 2, 'info' => '请选择商品数量！'));
                }
                $data['discount']=I('discount');
                if(!$data['discount']){
                    Ret(array('code' => 2, 'info' => '请选择商品会员折扣！'));
                }
                $data['state']=4;
                $data['check_type']=4;
                $data['submitter'] =session('worker_naem');
                $data['submitter_id'] =session('worker_id');
                $res=$goodsModel->update_goods($data);
                if ($res) {
                    Ret(array('code' => 1, 'info' => '修改成功！'));
                }else{
                    Ret(array('code' => 2, 'info' => '修改失败,系统出错！'));
                }
                break;

            case 'delete':
                $id=I('id');
                if($id==0){
                    Ret(array('code' => 2, 'info' => '数据获取失败！'));
                }
                $res=$goodsModel->delete_goods($id);
                if ($res) {
                    Ret(array('code' => 1, 'info' => '删除成功！'));
                }else{
                    Ret(array('code' => 2, 'info' => '删除失败,系统出错！'));
                }
                break;

            default:
                Ret(array('code' => 2, 'info' => 'Unknowed flag!'));
                break;
        }

    }

    /*
     * 商品折扣管理(增删改)
     */
    public function cost_dml_api(){
        checkLogin();
        before_api();
        checkAuth();
        $mall_id =session('mall.mall_id');
        $goodsDiscountModel = D('GoodsDiscount');
        $flag = I('flag');
        switch ($flag){
            case 'add':
                $data['merchant_id'] =$mall_id;
                if($data['merchant_id']==null){
                    Ret(array('code'=>2,'info'=>'请选择商户!'));
                }
                $data['goods_category_id'] = I('goods_category_id',0);
                $data['course_sub_category_id'] = I('course_sub_category_id',0);
                $data['goods_id'] = I('goods_id',0);

                $data['start_time'] = I('start_time',0);
                if($data['start_time']==null){
                    Ret(array('code'=>2,'info'=>'请输入折扣开始时间!'));
                }
                $data['end_time'] = I('end_time',0);
                if($data['end_time']==null){
                    Ret(array('code'=>2,'info'=>'请输入折扣结束时间!'));
                }
                $data['discount'] = I('discount',0);
                if($data['discount']==null){
                    Ret(array('code' =>2,'info'=>'请输入折扣系数！'));
                }
                if($data['discount']>1||0>$data['discount']){
                    Ret(array('code' =>2,'info'=>'请正确输入折扣系数（大于0，小于1）！'));
                }
                $data['state']  = I('state',0);
                $data['check_type']  = 0;
                $data['submitter'] =session('worker_name');
                $data['submitter_id'] =session('worker_id');
                $result =$goodsDiscountModel ->add_cost($data);
                if($result){
                    Ret(array('code' =>1,'info'=>'添加成功'));
                }else{
                    Ret(array('code' =>2,'info'=>'添加失败，系统出错'));
                }
                break;
            case 'update':
                $data['id'] =I('id');
                $data['merchant_id'] = $mall_id;
                if($data['merchant_id']==null){
                    Ret(array('code'=>2,'info'=>'请选择商户!'));
                }
                $data['goods_category_id'] = I('goods_category_id',0);
                $data['course_sub_category_id'] = I('course_sub_category_id',0);
                $data['goods_id'] = I('goods_id',0);
                $data['start_time'] = I('start_time',0);
                if($data['start_time']==null){
                    Ret(array('code'=>2,'info'=>'请输入折扣开始时间!'));
                }
                $data['end_time'] = I('end_time',0);
                if($data['end_time']==null){
                    Ret(array('code'=>2,'info'=>'请输入折扣结束时间!'));
                }
                $data['discount'] = I('discount',0);
                if($data['discount']==null){
                    Ret(array('code' =>2,'info'=>'请输入折扣系数！'));
                }
                if($data['discount']>1||0>$data['discount']){
                    Ret(array('code' =>2,'info'=>'请正确输入折扣系数（大于0，小于1）！'));
                }
                $data['state']  = I('state',0);
                $data['check_type']  = 4;
                $data['submitter'] =session('worker_naem');
                $data['submitter_id'] =session('worker_id');
                $result = $goodsDiscountModel->update_cost($data);
                if($result){
                    Ret(array('code'=>1,'info'=>'修改成功'));
                }else{
                    Ret(array('code'=>2,'info'=>'修改失败，系统出错！'));
                }
                break;
            case 'delete':
                $id=I('id');
                if(!$id){
                    Ret(array('code'=>2,'info'=>'数据获取失败'));
                }
                $result =$goodsDiscountModel ->del_cost($id);
                if($result){
                    Ret(array('code'=>1,'info'=>'删除成功'));
                }else{
                    Ret(array('code'=>2,'info'=>'删除失败，系统出错！'));
                }
                break;
            default:
                Ret(array('code' => 2, 'info' => 'Unknowed flag!'));
                break;
        }

    }

    /*
     * 商品信息审核
     */

//    public function goods_check_api(){
//        before_api();
//        checkAuth();
//        checkLogin();
//        $mall_id =session('mall.mall_id');
//        $data['id'] = I('id');
//        if ($data['id'] < 1) {
//            Ret(array('code' => 2, 'info' => '参数（id）有误!'));
//        }
//        $data['state'] = I('check_state');
//        if ($data['state'] < 1) {
//            Ret(array('code' => 2, 'info' => '参数（check_state）有误!'));
//        }
//        $logdata['goods_name']=I('goods_name');
////        if (!$data['goods_name']) {
////            Ret(array('code' => 2, 'info' => '参数（goods_name）有误!'));
////        }
//        $logdata['goods_category']=I('goods_category');
////        if (!$data['goods_category']) {
////            Ret(array('code' => 2, 'info' => '参数（goods_category）有误!'));
////        }
//
//        $logdata['goods_subcategory']=I('goods_subcategory');
////        if (!$data['goods_subcategory']) {
////            Ret(array('code' => 2, 'info' => '参数（goods_subcategory）有误!'));
////        }
//
//        $logdata['check_type']=I('state');
//        if ($data['check_type']==null) {
//            Ret(array('code' => 2, 'info' => '参数（state）有误!'));
//        }
//        if($logdata['check_type']==0){
//            $logdata['check_type']='商品信息新增';
//        }
//        if($logdata['check_type']==4){
//            $logdata['check_type']='商品信息修改';
//        }
//        $logdata['check_time']=date('Y-m-d H:i:s');
//        $logdata['checker']=session('worker_id');
//        $logdata['submitter']=I('submitter');
//        if($data['state']==1){
//            $logdata['result']='通过';
//        }
//        if($data['state']==2){
//            $logdata['result']='未通过';
//        }
//        $logModel=D('GoodsCheckLog')->add($logdata);
//
//        $roomCheckoutModel = D('Goods');
//        $res= $roomCheckoutModel->save($data);
//        if($res){
//            Ret(array('code' => 1, 'info' => '保存成功'));
//        }else{
//            Ret(array('code' => 2, 'info' => '保存失败'));
//        }
//    }

    /*
     * 商品信息审核
     */
    public function goods_check_api(){
        before_api();
        checkAuth();
        checkLogin();
        $data['id'] = I('id');
        $data['state'] = I('state');
        if(intval($data['id']) <1 || intval($data['state']) <0){
            Ret(array('code' => 2, 'info' => '参数有误!'));
        }
        //step 2: 根据主键id 获取需要的字段信息
        $roomCheckoutModel = D('Goods');
        //step 3 ；修改审核表状态
        $editInfo = $roomCheckoutModel->updateState($data);
        if($editInfo){
            $info = $roomCheckoutModel->getAllField($data['id']);
            foreach($info as $key =>$item){
                $info[$key]['state'] =get_state($item['state']);
                $info[$key]['category'] =$this->get_cat_by_id($item['goods_catid']);
                $info[$key]['sub_category']=$this->get_sub_cat($item['goods_sub_catid']);
            }
            //step:4 保存日志
            $log = array(
                'check_time' => date('Y-m-d H:i:s'),
                'goods_name' => $info[0]['name'],
                'goods_category' => $info[0]['category'],
                'goods_subcategory' => $info[0]['sub_category'],
                'check_type' => $info[0]['check_type'],
                'submitter' => $info[0]['submitter'],
                'checker' => session('worker_name'),
                'check_state' => $info[0]['state'],
                'goods_id'=> $info[0]['id']
            );
            if($log['check_type']==0){
                $log['check_type']='商品信息新增';
            }
            if($log['check_type']==4){
                $log['check_type']='商品信息修改';
            }
            $logModel=D('GoodsCheckLog');
            if($logModel->insertLog($log)){
                Ret(array('code'=>1,'data'=>'审核成功'));
            }
        }else {
            Ret(array('code'=>2,'info'=>'审核失败'));
        }



//        $data['id'] = I('id');
//        if ($data['id'] < 1) {
//            Ret(array('code' => 2, 'info' => '参数（id）有误!'));
//        }
////        if ($data['worker_id'] < 1) {
////            Ret(array('code' => 2, 'info' => '参数（worker_id）有误!'));
////        }
//        $logdata['goods_name'] = I('name');
//        if (!$logdata['goods_name']) {
//            Ret(array('code' => 2, 'info' => '参数（goods_name）有误!'));
//        }
//        $logdata['goods_category'] = I('goods_catname');
//        if (!$logdata['goods_category']) {
//            Ret(array('code' => 2, 'info' => '参数（goods_category）有误!'));
//        }
//        $logdata['goods_subcategory'] = I('goods_sub_catname');
//        if (!$logdata['goods_subcategory']) {
//            Ret(array('code' => 2, 'info' => '参数（goods_subcategory）有误!'));
//        }
//        $logdata['submitter'] = I('submitter');
//        if (!$logdata['submitter'] ) {
//            Ret(array('code' => 2, 'info' => '参数（submitter）有误!'));
//        }
//        $logdata['check_type'] = I('check_type');
//        if ($logdata['check_type'] == null ) {
//            Ret(array('code' => 2, 'info' => '参数（check_type）有误!'));
//        }
//        if($logdata['check_type']==0){
//            $logdata['check_type']='商品信息新增';
//        }
//        if($logdata['check_type']==4){
//            $logdata['check_type']='商品信息修改';
//        }
//        $data['state'] = I('state');
//        if ($data['state'] < 1) {
//            Ret(array('code' => 2, 'info' => '参数（check_state）有误!'));
//        }
//        if($data['state']==1){
//            $logdata['check_state']='通过';
//        }
//        if($data['state']==2){
//            $logdata['check_state']='未通过';
//        }
//        $logdata['check_time'] = date('Y-m-d H:i:s');
//        $logdata['checker'] = session('worker_name');
//        $roomCheckoutModel = D('Goods');
//        $roomCheckoutModel->startTrans();//开启事务
//        $logModel=D('GoodsCheckLog');
//        $log=$logModel->add($logdata);
//        if($log){
//            $result=$roomCheckoutModel->save($data);
//            $logModel->commit();//日志保存成功，提交
//            if($result){
//                Ret(array('code' => 1, 'info' => '审核成功'));
//            }else{
//                Ret(array('code' => 2, 'info' => '审核失败'));
//            }
//        }else{
//            $logModel->rollback();//日志保存失败，回滚
//            Ret(array('code' => 2, 'info' => '审核失败'));
//        }
    }


    /*
     * 商品计费审核
     */
    public function cost_check_api(){
        before_api();
        checkAuth();
        checkLogin();
        $mall_id =session('mall.mall_id');
        $data['id'] = I('id');
        $data['state'] = I('state');
        if(intval($data['id']) <1 || intval($data['state']) <0){
            Ret(array('code' => 2, 'info' => '参数有误!'));
        }
        //step 2: 根据主键id 获取需要的字段信息
        $roomCheckoutModel = D('GoodsDiscount');
        //step 3 ；修改审核表状态
        $editInfo = $roomCheckoutModel->updateState($data);
        if($editInfo){
            $info = $roomCheckoutModel->getAllField($data['id']);
            foreach($info as $key =>$item){
                $info[$key]['state'] =get_state($item['state']);
                $info[$key]['category'] =$this->get_cat_by_id($item['goods_category_id']);
                $info[$key]['sub_category']=$this->get_sub_cat($item['course_sub_category_id']);
                $info[$key]['goods_name']=$this->get_goods_by_id($item['goods_id']);
            }
            //step:4 保存日志
            $log = array(
                'check_time' => date('Y-m-d H:i:s'),
                'goods_name' => $info[0]['goods_name'],
                'goods_category' => $info[0]['category'],
                'goods_subcategory' => $info[0]['sub_category'],
                'check_type' => $info[0]['check_type'],
                'submitter' => $info[0]['submitter'],
                'checker' => session('worker_name'),
                'check_state' => $info[0]['state'],
                'goods_id'=> $info[0]['id']
            );
            if($log['check_type']==0){
                $log['check_type']='商品计费新增';
            }
            if($log['check_type']==4){
                $log['check_type']='商品计费修改';
            }
            $logModel=D('GoodsCheckLog');
            if($logModel->insertLog($log)){
                Ret(array('code'=>1,'data'=>'审核成功'));
            }
        }else {
            Ret(array('code'=>2,'info'=>'审核失败'));
        }



//        $data['id'] = I('id');
//        if ($data['id'] < 1) {
//            Ret(array('code' => 2, 'info' => '参数（id）有误!'));
//        }
////        if ($data['worker_id'] < 1) {
////            Ret(array('code' => 2, 'info' => '参数（worker_id）有误!'));
////        }
//        $logdata['goods_name'] = I('goods_name');
//        if (!$logdata['goods_name']) {
//            Ret(array('code' => 2, 'info' => '参数（goods_name）有误!'));
//        }
//        $logdata['goods_category'] = I('category');
//        if (!$logdata['goods_category']) {
//            Ret(array('code' => 2, 'info' => '参数（goods_category）有误!'));
//        }
//        $logdata['goods_subcategory'] = I('sub_category');
//        if (!$logdata['goods_subcategory']) {
//            Ret(array('code' => 2, 'info' => '参数（goods_subcategory）有误!'));
//        }
//        $logdata['submitter'] = I('submitter');
//        if (!$logdata['submitter'] ) {
//            Ret(array('code' => 2, 'info' => '参数（submitter）有误!'));
//        }
//        $logdata['check_type'] = I('check_type');
//        if ($logdata['check_type'] == null ) {
//            Ret(array('code' => 2, 'info' => '参数（check_type）有误!'));
//        }
//        if($logdata['check_type']==0){
//            $logdata['check_type']='商品计费新增';
//        }
//        if($logdata['check_type']==4){
//            $logdata['check_type']='商品计费修改';
//        }
//        $data['state'] = I('state');
//        if ($data['state'] < 1) {
//            Ret(array('code' => 2, 'info' => '参数（check_state）有误!'));
//        }
//        if($data['state']==1){
//            $logdata['check_state']='通过';
//        }
//        if($data['state']==2){
//            $logdata['check_state']='未通过';
//        }
//        $logdata['check_time'] = date('Y-m-d H:i:s');
//        $logdata['checker'] = session('worker_name');
//        $roomCheckoutModel = D('GoodsDiscount');
//        $roomCheckoutModel->startTrans();//开启事务
//        $logModel=D('GoodsCheckLog');
//        $log=$logModel->add($logdata);
//        if($log){
//            $result=$roomCheckoutModel->save($data);
//            $logModel->commit();//日志保存成功，提交
//            if($result){
//                Ret(array('code' => 1, 'info' => '审核成功'));
//            }else{
//                Ret(array('code' => 2, 'info' => '审核失败'));
//            }
//        }else{
//            $logModel->rollback();//日志保存失败，回滚
//            Ret(array('code' => 2, 'info' => '审核失败'));
//        }
    }

    public function cat(){
        before_api();
        $model=D('GoodsCat');
        $data=$model->get_list();
        if ($data) {
            Ret(array('code' => 1, 'data' => $data));
        }else{
            Ret(array('code' => 2, 'info' => '获取商品类别失败,系统出错！'));
        }
    }
    public function sub_cat(){
        before_api();
        $cat_id=I('goods_catid','');
        if(!$cat_id){
            Ret(array('code' => 2, 'info' => '获取商品类别id失败！'));
        }
        $model=D('GoodsSubCat');
        $data=$model->get_list($cat_id);
        if ($data) {
            Ret(array('code' => 1, 'data' => $data));
        }else{
            Ret(array('code' => 2, 'info' => '获取商品子类失败,系统出错！'));
        }
    }

    public function sub_cat_list(){
        before_api();
        $model=D('GoodsSubCat');
        $data=$model->get_sub_cat_list();
        if ($data) {
            Ret(array('code' => 1, 'data' => $data));
        }else{
            Ret(array('code' => 2, 'info' => '获取商品子类失败,系统出错！'));
        }
    }

    public function goods_list(){
        before_api();

        $model=D('Goods');
        $merchant_id=session('mall.mall_id');
        $data=$model->goods_list($merchant_id);
        if ($data) {
            Ret(array('code' => 1, 'data' => $data));
        }else{
            Ret(array('code' => 2, 'info' => '获取商品子类失败,系统出错！'));
        }
    }

    /**
     * 商品计费审核
     */
    public function goods_cost_list(){
        before_api();
        checkLogin();
        checkAuth();
        $mall_id =session('mall.mall_id');
        $state =I('state',0,'intval');
        if($state !==2 && !$mall_id){
            $state =2;
        }
        $keyword = I('keyword');
        $goods_category_id = I('goods_category_id',0,'intval');
        $page = I('page',1,'intval');
        $pagesize =I('pagesize',10,'intval');
        $pagesize =$pagesize < 1 ? 1:$pagesize;
        $pagesize =$pagesize > 50 ? 50 :$pagesize;
        $page =$page.','.$pagesize;
        $goodsModel = D('Goods');
        $goodsDiscountModel = D('GoodsDiscount');
        $count =$goodsDiscountModel->get_count($mall_id,$goods_category_id,$keyword);
        $data =$goodsDiscountModel->get_list($mall_id,$goods_category_id,$keyword,$page,$fields = null);
        foreach($data as $key =>$item){
            $data[$key]['state'] =get_state($item['state']);
            $data[$key]['category'] =$this->get_cat_by_id($item['goods_category_id']);
            $data[$key]['sub_category']=$this->get_sub_cat($item['course_sub_category_id']);
            $data[$key]['goods_name']=$this->get_goods_by_id($item['goods_id']);
            $data[$key]['nnn']='1';
        }
        if($data){
            Ret(array('code'=>1,'data' =>$data,'total' =>$count,'page_count'=>ceil($count/$pagesize)));
        }else{
            Ret(array('code' =>2,'info'=>'没有数据！'));
        }

    }

    /**
     * 商品信息审核列表
     */
    public function goods_check_list(){
        before_api();
        checkLogin();
        checkAuth();
        $mall_id = session('mall.mall_id');
        $goods_catid = I('goods_catid',0,'intval');
        $keyword = I('keyword');
        $page = I('page',1,'intval');
        $pagesize = I('pagesize',10,'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page.','.$pagesize;
        $goodsModel = D('Goods');
        $gcatModel = D('GoodsCat');
        $subCatModel=D('GoodsSubCat');
        $count = $goodsModel->get_count($mall_id,$goods_catid,$keyword);
//        var_dump($count);die;
        if ($count) {
            $fields = 'id,number,check_type,goods_catid,goods_sub_catid,name,price,count,state,discount';
            $res = $goodsModel->get_list($mall_id,$goods_catid,$keyword,$page,$fields);
            if (empty($res)) {
                Ret(array('code' => 2, 'info' => '查无相关数据！'));
            }else{
                foreach ($res as $key => $value) {
                    $cat = $gcatModel->get_cat($value['goods_catid']);
                    $res[$key]['catigory_name']=$cat['name'];
                    $state=get_state($value['state']);
                    $res[$key]['state']=$state;
                    $subcat = $subCatModel->get_sub_cat($value['goods_sub_catid']);
                    $res[$key]['sub_category_name']=$subcat['name'];
                    $res[$key]['nnn']='1';
                }
                Ret(array('code' => 1, 'data' => $res, 'total' => $count, 'page_count' => ceil($count/$pagesize)));
            }
        }else{
            Ret(array('code' => 2, 'info' => '查无相关数据！'));
        }
    }

    /**
     * 商品信息审核详情
     */
public function goods_check_view_api(){
    before_api();
    checkLogin();

    checkAuth();
    $id=I('id');
    if(!$id){
        Ret(array('code' => 2, 'info' => '获取参数（id）失败！'));
    }
    $goodsModel = D('Goods');
    $goodsCategoryModel = D('GoodsCat');
    $goodsSubCategoryModel = D('GoodsCat');
    $fields=('id,state,goods_catid,goods_sub_catid,name,price,count,discount,submitter');
    $data=$goodsModel->get_goods_info($id,$fields);
    $data['state'] =get_state($data['state']);
    $cate = $goodsCategoryModel->get_cat_by_id($data['goods_catid']);
    $data['goods_catname']=$cate['name'];
    $subcate = $goodsSubCategoryModel->get_sub_cat_by_id($data['goods_catid']);
    $data['goods_sub_catname']=$subcate['name'];

    if ($data) {
        Ret(array('code' => 1, 'data' => $data));
    } else {
        Ret(array('code' => 2, 'info' => '没有数据！'));
    }


}

    /**
     * 商品会员计费审核（详情）
     */
    public function goods_cost_view_api(){
        before_api();
        checkLogin();

        checkAuth();
        $id=I('id');
        if(!$id){
            Ret(array('code' => 2, 'info' => '获取参数（id）失败！'));
        }
        $goodsDiscountModel = D('GoodsDiscount');
//        $goodsCategoryModel = D('GoodsCat');
//        $goodsSubCategoryModel = D('GoodsSubCat');
        $fields=('id,goods_id,goods_category_id,course_sub_category_id,merchant_id,start_time,end_time,discount,state');
        $data=$goodsDiscountModel->get_goods_info($id,$fields);
        $data['category'] =$this->get_cat_by_id($data['goods_category_id']);
        $data['sub_category']=$this->get_sub_cat($data['course_sub_category_id']);
        $data['goods_name']=$this->get_goods_by_id($data['goods_id']);
        $state=get_state($data['state']);
        $data['state']=$state;
        if ($data) {
            Ret(array('code' => 1, 'data' => $data));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据！'));
        }


    }



    /*
     * 根据条形码查询商品
     */
    public function shoot_goods(){
        before_api();
        checkLogin();
        checkAuth();
        $code=I('code');
        $goods=D('Goods')->get_goods_by_code($code);;
        if($goods){
            $goods['price']=floatval($goods['price']);
            Ret(array('code'=>1,'data' =>$goods));
        }
        if(!$goods){
            Ret(array('code'=>2));
        }
    }

}