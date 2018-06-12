<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/6/20
 * Time: 9:21
 */


namespace Inst\Controller;
class ServeController extends CommonController
{
    /*
     * 机构服务列表
     */
    public function index_api()
    {
        before_api();
        checkLogin();
        checkAuth();
        $mall_id =session('mall.mall_id');
        $keyword = I('keyword');
        $state = I('state', 2, 'intval');
        $is_manager = true;
        if ($state !== 2 && !$is_manager) {
            $state = 2;
        }
//        $keyword = I('keyword');
        $page = I('page', 1, 'intval');
        $pagesize = I('pagesize', 10, 'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page . ',' . $pagesize;
       $serveModel = D('Serve');
        $catModel=D('ServiceCategory');
        $scatModel=D('ServiceSubCategory');
        $merchantModel=D('Merchant');
        $count = $serveModel->get_count($mall_id,$keyword);
        $data = $serveModel->get_list($mall_id,$keyword , $page, $fields = null);
        foreach ($data as $key=>$item){
            $cat=$catModel->get_cat($item['service_catid']);
            $data[$key]['service_cat']=$cat[0]['name'];
            $scat=$scatModel->get_sub_cat($item['service_sub_catid']);
            $data[$key]['service_sub_cat']=$scat[0]['name'];
            $data[$key]['state']=get_state($item['state']);
            $merchant=$merchantModel->get_cat_by_id($item['merchant_id']);
            $data[$key]['merchant_name']=$merchant[0]['name'];
        }
        if ($data) {
            Ret(array('code' => 1, 'data' => $data, 'total' => $count, 'page_count' => ceil($count/$pagesize)));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据！'));
        }
    }
    /*
     * 机构服务增删改
     */
    public function serve_dml_api()
    {
        before_api();
        checkLogin();
        checkAuth();
        $mall_id =session('mall.mall_id');
        $serveModel = D('serve');
        $flag = I('flag');
        switch ($flag) {
            case add:
                $data['service_catid'] = I('service_catid', 0, 'intval');
//                if ($data['service_catid'] == 0) {
//                    Ret(array('code' => 2, 'info' => '请选择类别！'));
//                }
                $data['service_sub_catid'] = I('service_sub_catid', 0, 'intval');
//                if ($data['service_sub_catid'] == 0) {
//                    Ret(array('code' => 2, 'info' => '请选择子类！'));
//                }
                $data['name'] = I('name', '');
                if ($data['name'] == null) {
                    Ret(array('code' => 2, 'info' => '请输入名称！'));
                }
                $data['price'] = I('price', 0);
                if ($data['price'] == 0) {
                    Ret(array('code' => 2, 'info' => '请输入市场价格！'));
                }
                $data['merchant_id'] = I('merchant_id',0, 'intval');
//                if($data['merchant_id']=0){
//                    Ret(array('code'=>2,'indo'=>'请选择商户'));
//                }
                $data['discount'] = I('discount', 1);
                // $data['mall_id'] = session('mall.mall_id');
                // $data['merchant_id'] = session('merchant.merchant_id');
                $data['mall_id'] = 1;
                $data['state'] = 0;
                $data['check_type'] = 0;
                $data['submitter_id'] =session('worker_id');
                $data['submitter'] =session('worker_name');
                $result = $serveModel->add_serve($data);
                if ($result) {
                    Ret(array('code' => 1, 'info' => '添加成功！'));
                } else {
                    Ret(array('code' => 2, 'info' => '添加失败,系统出错！'));
                }
                break;
            case update:
                $data['id'] = I('id');
                if ($data['id'] == null) {
                    Ret(array('code' => 2, 'info' => 'ID获取失败！'));
                }
                $data['service_catid'] = I('service_catid', 0, 'intval');
//                if ($data['service_catid']) {
//                    Ret(array('code' => 2, 'info' => '请选择类别！'));
//                }
                $data['service_sub_catid'] = I('service_sub_catid', 0, 'intval');
//                if ($data['service_sub_catid'] == 0) {
//                    Ret(array('code' => 2, 'info' => '请选择子类！'));
//                }
                $data['merchant_id'] = I('merchant_id',0, 'intval');
                $data['name'] = I('name', '');
                if ($data['name'] == null) {
                    Ret(array('code' => 2, 'info' => '请输入名称！'));
                }
                $data['price'] = I('price', 0);
                if ($data['price'] == 0) {
                    Ret(array('code' => 2, 'info' => '请输入市场价格！'));
                }
                $data['discount'] = I('discount', 1);
                if ($data['discount'] > 1) {
                    Ret(array('code' => 2, 'info' => '折扣不能大于1！'));
                }
                $data['state'] = 0;
                $data['check_type'] = 4;
                $data['submitter_id'] =session('worker_id');
                $data['submitter'] =session('worker_name');
                $result = $serveModel->update_serve($data);
                if ($result) {
                    Ret(array('code' => 1, 'info' => '更新成功！'));
                } else {
                    Ret(array('code' => 2, 'info' => '更新失败,系统出错！'));
                }
                break;
            case delete:
                $id = I('id');
                if($id<1) {
                    Ret(array('code' => 2, 'info' => '数据获取失败！'));
                }
                $result = $serveModel->del_serve($id);
                if ($result) {
                    Ret(array('code' => 1, 'info' => '删除成功！'));
                } else {
                    Ret(array('code' => 2, 'info' => '删除失败,系统出错！'));
                }
                break;
            default :
                Ret(array('code' => 2, 'info' => 'Unknowed flag!'));
                break;
        }

    }


    /*
     * 服务详情
     */
    public function serve_view_api(){
        before_api();
        checkLogin();

        checkAuth();
        $id=I('id');
        if(!$id){
            Ret(array('code' => 2, 'info' => '获取参数（id）失败！'));
        }
        $serveModel = D('serve');
        $categoryModel = D('ServiceCategory');
        $subCategoryModel = D('ServiceSubCategory');
        $fields=('id,mall_id,merchant_id,state,service_catid,service_sub_catid,name,price,discount,submitter_id');
        $data=$serveModel->get_serve_info($id,$fields);
        foreach ($data as $key =>$value) {
            $cate= $categoryModel->get_cat($value['service_catid']);
            $data[$key]['service_catname'] =$cate[0]['name'];
            $sbuCat = $subCategoryModel->get_sub_cat($value['service_sub_catid']);
            $data[$key]['service_sub_catname']=$sbuCat[0]['name'];
        }
        if ($data) {
            Ret(array('code' => 1, 'data' => $data[0]));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据！'));
        }


    }



    /*
     * 服务折扣计费
     */
    public function cost_api(){
        before_api();
        checkLogin();
        checkAuth();
        $mall_id =session('mall.mall_id');
        $state =I('state',0,'intval');
        if($state !==2 && !$mall_id){
            $state =2;
        }
        $keyword = I('keyword');
        $page = I('page',1,'intval');
        $pagesize =I('pagesize',10,'intval');
        $pagesize =$pagesize < 1 ? 1:$pagesize;
        $pagesize =$pagesize > 50 ? 50 :$pagesize;
        $page =$page.','.$pagesize;
        $serveDiscountModel = D('ServeDiscount');
        $count =$serveDiscountModel->get_count($mall_id,$keyword);
        $data =$serveDiscountModel->get_list($mall_id,$keyword,$page,$fields = null);
        foreach($data as $key =>$item){
            $data[$key]['state'] =get_state($item['state']);
            $data[$key]['category'] =$this->get_cat_by_id($item['service_catid']);
            $data[$key]['sub_category']=$this->get_sub_cat($item['service_sub_catid']);
            $data[$key]['service_name']=$this->get_serve_by_id($item['service_id']);
            $data[$key]['merchant_name']=$this->get_merchant_by_id($item['merchant_id']);

        }
        if($data){
            Ret(array('code'=>1,'data' =>$data,'total' =>$count,'page_count'=>ceil($count/$pagesize)));
        }else{
            Ret(array('code' =>2,'info'=>'没有数据！'));
        }

    }
    /*
     * 获取机构分类
     */
    private function get_cat_by_id($id){
        $goodsCategoryModel = D('ServiceCategory');
        $cats=explode('_',$id);
        foreach($cats as $k => $v ) {
            $cate = $goodsCategoryModel->get_cat($v);
            if ($cate[0][name] != null) {
                $catess[$k] = $cate[0][name];
            }
            $data= implode(',', $catess);

        }
        return $data;
    }
    /*
     * 获取机构子类
     */
    private function get_sub_cat($id){
        $goodsSubCategoryModel = D('ServiceSubCategory');
        $array=explode('_',$id);
        foreach($array as $k => $v ) {
            $item = $goodsSubCategoryModel->get_sub_cat($v);
            if ($item[0][name] != null) {
                $items[$k] = $item[0][name];
            }
            $data= implode(',', $items);
        }
        return $data;
    }
    private function get_serve_by_id($id){
        $serveModel = D('Serve');
        $array=explode('_',$id);
        foreach($array as $k => $v ) {
            $item = $serveModel->get_serve_by_id($v);
            if ($item[0][name] != null) {
                $items[$k] = $item[0][name];
            }
            $data= implode(',', $items);
        }
        return $data;
    }
    private function get_merchant_by_id($id){
        $merchantModel = D('Merchant');
        $array=explode('_',$id);
        foreach($array as $k => $v ) {
            $item = $merchantModel->get_cat_by_id($v);
            if ($item[name] != null) {
                $items[$k] = $item[name];
            }
            //var_dump($item[name]);die;
            $data= implode(',', $items);
            //var_dump($data);die;

        }
        return $data;
    }
    /*
        * 服务折扣管理(增删改)
        */
    public function cost_dml_api(){
        checkLogin();
        before_api();
        checkAuth();
        $mall_id = session('mall.mall_id');
        $serveDiscountModel = D('ServeDiscount');
        $flag = I('flag');
        switch ($flag){
            case 'add':
                $data['mall_id'] = $mall_id;

                $data['service_catid'] = I('service_catid',0);
                if($data['service_catid']==0){
                    Ret(array('code'=>2,'info'=>'参数（service_catid）错误!'));
                }
                $data['service_sub_catid'] = I('service_sub_catid',0);
                if($data['service_sub_catid']==0){
                    Ret(array('code'=>2,'info'=>'参数（service_sub_catid）错误!'));
                }
                $data['service_id'] = I('service_id',0);
                $data['start_time'] = I('start_time',0);
                $data['service_name'] = I('service_name');
                $data['sub_category'] = I('sub_category');
                $data['category'] = I('category');
                if($data['start_time']==null){
                    Ret(array('code'=>2,'info'=>'请输入折扣开始时间!'));
                }
                $data['end_time'] = I('end_time',0);
                if($data['end_time']==null){
                    Ret(array('code'=>2,'info'=>'请输入折扣结束时间!'));
                }
                $data['rate'] = I('rate',0);
                if($data['rate']==null){
                    Ret(array('code' =>2,'info'=>'请输入折扣系数！'));
                }
                if($data['rate']>1||0>$data['rate']){
                    Ret(array('code' =>2,'info'=>'请正确输入折扣系数（大于0，小于1）！'));
                }
                $data['state']  = I('state',0);
                $data['check_type']  = I('check_type',0);
                $data['submitter'] =session('worker_name');
                $result =$serveDiscountModel ->add_serve($data);
                if($result){
                    Ret(array('code' =>1,'info'=>'添加成功'));
                }else{
                    Ret(array('code' =>2,'info'=>'添加失败，系统出错'));
                }
                break;
            case 'update':
                $data['id'] =I('id');
                $data['mall_id'] = $mall_id;
                $data['service_catid'] = I('service_catid',0);
                $data['service_sub_catid'] = I('service_sub_catid',0);
                $data['service_id'] = I('service_id',0);
                $data['start_time'] = I('start_time',0);
                if($data['start_time']==null){
                    Ret(array('code'=>2,'info'=>'请输入折扣开始时间!'));
                }
                $data['end_time'] = I('end_time',0);
                if($data['end_time']==null){
                    Ret(array('code'=>2,'info'=>'请输入折扣结束时间!'));
                }
                $data['rate'] = I('rate',0);
                if($data['rate']==null){
                    Ret(array('code' =>2,'info'=>'请输入折扣系数！'));
                }
                if($data['rate']>1||0>$data['rate']){
                    Ret(array('code' =>2,'info'=>'请正确输入折扣系数（大于0，小于1）！'));
                }
                $data['state']  = I('state',0);
                $data['check_type']  = I('check_type',4);
                $data['submitter'] =session('worker_name');
                $result = $serveDiscountModel->update_serve($data);
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
                $result =$serveDiscountModel->del_serve($id);
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
     * 获取服务类别
     */
    public function category(){
        before_api();
        $data=D('ServiceCategory')->get_cat_list();
        if($data){
            Ret(array('code'=>1,'data'=>$data));
        }else{
            Ret(array('code'=>2,'info'=>'没有数据！'));
        }

    }
    /*
     * 获取服务类别
     */
    public function sub_category(){
        before_api();
        $data=D('ServiceSubCategory')->get_sub_cat_list();
        if($data){
            Ret(array('code'=>1,'data'=>$data));
        }else{
            Ret(array('code'=>2,'info'=>'没有数据！'));
        }
    }

    /*
     * 获取项目
     */
    public function serve_project(){
        before_api();
        $mall_id = session('mall.mall_id');
        $data=D('Serve')->serve_project($mall_id);
        if($data){
            Ret(array('code'=>1,'data'=>$data));
        }else{
            Ret(array('code'=>2,'info'=>'没有数据！'));
        }

    }

    /*
     *
     *服务信息审核
     */
    public function serve_check_api(){
        before_api();
        checkAuth();
        checkLogin();
        $data['id'] = I('id');
        $data['state'] = I('state');
        if(intval($data['id']) <1 || intval($data['state']) <0){
            Ret(array('code' => 2, 'info' => '参数有误!'));
        }
        //step 2: 根据主键id 获取需要的字段信息
        $roomCheckoutModel = D('Serve');
        //step 3 ；修改审核表状态
        $editInfo = $roomCheckoutModel->updateState($data);
        if($editInfo){
            $info = $roomCheckoutModel->getAllField($data['id']);
            foreach($info as $key =>$item){
                $info[$key]['state'] =get_state($item['state']);
                $info[$key]['category'] =$this->get_cat_by_id($item['service_catid']);
                $info[$key]['sub_category']=$this->get_sub_cat($item['service_sub_catid']);
            }
            //step:4 保存日志
            $log = array(
                'check_time' => date('Y-m-d H:i:s'),
                'service_name' => $info[0]['name'],
                'service_category' => $info[0]['category'],
                'service_subcategory' => $info[0]['sub_category'],
                'check_type' => $info[0]['check_type'],
                'submitter' => $info[0]['submitter'],
                'checker' => session('worker_name'),
                'check_state' => $info[0]['state'],
                'serve_id'=> $info[0]['id']
            );
            if($log['check_type']==0){
                $log['check_type']='服务信息新增';
            }
            if($log['check_type']==4){
                $log['check_type']='服务信息修改';
            }
            $logModel=D('ServiceCheckLog');
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
//        $logdata['service_name'] = I('name');
//        if (!$logdata['service_name']) {
//            Ret(array('code' => 2, 'info' => '参数（service_name）有误!'));
//        }
//        $logdata['service_category'] = I('service_cat');
//        if (!$logdata['service_category']) {
//            Ret(array('code' => 2, 'info' => '参数（service_category）有误!'));
//        }
//        $logdata['service_subcategory'] = I('service_sub_cat');
//        if (!$logdata['service_subcategory']) {
//            Ret(array('code' => 2, 'info' => '参数（service_subcategory）有误!'));
//        }
//
//        $logdata['check_type'] = I('check_type');
//        if ($logdata['check_type'] == null ) {
//            Ret(array('code' => 2, 'info' => '参数（check_type）有误!'));
//        }
//        if($logdata['check_type']==0){
//            $logdata['check_type']='服务信息新增';
//        }
//        if($logdata['check_type']==4){
//            $logdata['check_type']='服务信息修改';
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
//        $logdata['submitter'] = I('submitter');
//        if ($logdata['submitter'] == null ) {
//            Ret(array('code' => 2, 'info' => '参数（submitter）有误!'));
//        }
//        $logdata['check_time'] = date('Y-m-d H:i:s');
//        $logdata['checker'] = session('worker_name');
//        $roomCheckoutModel = D('Serve');
//        $roomCheckoutModel->startTrans();//开启事务
//        $logModel=D('ServiceCheckLog');
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
    *
    *服务会员计费审核
    */
    public function cost_check_api(){
        before_api();
        checkAuth();
        checkLogin();

        $data['id'] = I('id');
        $data['state'] = I('state');
        if(intval($data['id']) <1 || intval($data['state']) <0){
            Ret(array('code' => 2, 'info' => '参数有误!'));
        }
        //step 2: 根据主键id 获取需要的字段信息
        $roomCheckoutModel = D('ServeDiscount');
        //step 3 ；修改审核表状态
        $editInfo = $roomCheckoutModel->updateState($data);
        if($editInfo){
            $info = $roomCheckoutModel->getAllField($data['id']);
            foreach($info as $key =>$item){
                $info[$key]['state'] =get_state($item['state']);
                $info[$key]['category'] =$this->get_cat_by_id($item['service_catid']);
                $info[$key]['sub_category']=$this->get_sub_cat($item['service_sub_catid']);
                $info[$key]['service_name']=$this->get_serve_by_id($item['service_id']);
            }
            //step:4 保存日志
            $log = array(
                'check_time' => date('Y-m-d H:i:s'),
                'service_name' => $info[0]['service_name'],
                'service_category' => $info[0]['category'],
                'service_subcategory' => $info[0]['sub_category'],
                'check_type' => $info[0]['check_type'],
                'submitter' => $info[0]['submitter'],
                'checker' => session('worker_name'),
                'check_state' => $info[0]['state'],
                'serve_id'=> $info[0]['id']
            );
            if($log['check_type']==0){
                $log['check_type']='服务会员计费新增';
            }
            if($log['check_type']==4){
                $log['check_type']='服务会员计费修改';
            }
            $logModel=D('ServiceCheckLog');
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
//        $logdata['service_name'] = I('service_name');
//        if (!$logdata['service_name']) {
//            Ret(array('code' => 2, 'info' => '参数（service_name）有误!'));
//        }
//        $logdata['service_category'] = I('category');
//        if (!$logdata['service_category']) {
//            Ret(array('code' => 2, 'info' => '参数（service_category）有误!'));
//        }
//        $logdata['service_subcategory'] = I('sub_category');
//        if (!$logdata['service_subcategory']) {
//            Ret(array('code' => 2, 'info' => '参数（service_subcategory）有误!'));
//        }
//
//        $logdata['check_type'] = I('check_type');
//        if ($logdata['check_type'] == null ) {
//            Ret(array('code' => 2, 'info' => '参数（check_type）有误!'));
//        }
//        if($logdata['check_type']==0){
//            $logdata['check_type']='服务会员计费新增';
//        }
//        if($logdata['check_type']==4){
//            $logdata['check_type']='服务会员计费修改';
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
//        $logdata['submitter'] = I('submitter');
//        $logdata['check_time'] = date('Y-m-d H:i:s');
//        $logdata['checker'] = session('worker_name');
//        $roomCheckoutModel = D('ServeDiscount');
//        $roomCheckoutModel->startTrans();//开启事务
//        $logModel=D('ServiceCheckLog');
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

    public function serve_log()
    {
        before_api();
        $keyword=I('keyword');
        $page = I('page', 1, 'intval');
        $pagesize = I('pagesize', 10, 'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page . ',' . $pagesize;
        $serviceCheckLogModel = D('ServiceCheckLog');
        $data=$serviceCheckLogModel->get_list($keyword,$page);
        $count = $serviceCheckLogModel->get_count($keyword);
        if ($data) {
            Ret(array('code' => 1, 'data' => $data, 'total' => $count, 'page_count' => ceil($count / $pagesize)));
        } else {
            Ret(array('code' => 2, 'info' => '数据获取失败'));
        }
    }

    /**
     * 服务会员计费审核列表
     */
    public function cost_check_list(){
        before_api();
        checkLogin();
        checkAuth();
       $mall_id =session('mall.mall_id');
        $state =I('state',0,'intval');
        if($state !==2 && !$mall_id){
            $state =2;
        }
        $keyword = I('keyword');
        $page = I('page',1,'intval');
        $pagesize =I('pagesize',10,'intval');
        $pagesize =$pagesize < 1 ? 1:$pagesize;
        $pagesize =$pagesize > 50 ? 50 :$pagesize;
        $page =$page.','.$pagesize;
        $serveDiscountModel = D('ServeDiscount');
        $count =$serveDiscountModel->get_count($mall_id,$keyword);
        $data =$serveDiscountModel->get_list($mall_id,$keyword,$page,$fields = null);
        foreach($data as $key =>$item){
            $data[$key]['state'] =get_state($item['state']);
            $data[$key]['category'] =$this->get_cat_by_id($item['service_catid']);
            $data[$key]['sub_category']=$this->get_sub_cat($item['service_sub_catid']);
            $data[$key]['service_name']=$this->get_serve_by_id($item['service_id']);
            $data[$key]['nnn']='1';
        }
        if($data){
            Ret(array('code'=>1,'data' =>$data,'total' =>$count,'page_count'=>ceil($count/$pagesize)));
        }else{
            Ret(array('code' =>2,'info'=>'没有数据！'));
        }
    }

    /**
     * 服务信息审核列表
     */
    public function serve_check_list()
    {
        before_api();
        checkLogin();
        checkAuth();
        $mall_id = session('mall.mall_id');
        $state = I('state', 2, 'intval');
        $is_manager = true;
        if ($state !== 2 && !$is_manager) {
            $state = 2;
        }
        $keyword = I('keyword');
        $service_catid = I('service_catid',0,'intval');
        $page = I('page', 1, 'intval');
        $pagesize = I('pagesize', 10, 'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page . ',' . $pagesize;
        $serveModel = D('Serve');
        $catModel=D('ServiceCategory');
        $scatModel=D('ServiceSubCategory');
        $count = $serveModel->get_serve_count($mall_id,$service_catid,$keyword);
        $data = $serveModel->get_serve_list($mall_id,$service_catid,$keyword , $page, $fields = null);
//        var_dump($data);die;
        foreach ($data as $key=>$item) {
            $cat=$catModel->get_cat($item['service_catid']);
            $data[$key]['service_cat']=$cat[0]['name'];
            $scat=$scatModel->get_sub_cat($item['service_catid']);
            $data[$key]['service_sub_cat']=$scat[0]['name'];
            $data[$key]['state']=get_state($item['state']);
            $data[$key]['nnn']='1';
        }
        if ($data) {
            Ret(array('code' => 1, 'data' => $data, 'total' => $count, 'page_count' => ceil($count / $pagesize)));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据！'));
        }
    }

    /**
     * 服务折扣计费详情
     */
    public function serve_cost_view_api()
    {
        before_api();
        checkLogin();

        checkAuth();
        $id = I('id');
        if (!$id) {
            Ret(array('code' => 2, 'info' => '获取参数（id）失败！'));
        }
        $serveDiscountModel = D('ServeDiscount');
        $data = $serveDiscountModel->get_serve_info($id);
        foreach($data as $key =>$item){
            $data[$key]['category'] =$this->get_cat_by_id($item['service_catid']);
            $data[$key]['sub_category']=$this->get_sub_cat($item['service_sub_catid']);
            $data[$key]['service_name']=$this->get_serve_by_id($item['service_id']);
            $data[$key]['merchant_name']=$this->get_merchant_by_id($item['merchant_id']);
        }
        if ($data) {
            Ret(array('code' => 1, 'data' => $data[0]));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据！'));
        }

    }

}