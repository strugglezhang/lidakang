<?php
namespace Merchant\Controller;
class IndexController extends CommonController {

    public function index_api(){
        before_api();
        checkLogin();
        checkAuth();
        $mall_id = session('mall.mall_id');
        $merchant_catid = I('merchant_catid',0,'intval');
        $keyword = I('keyword');
        $page = I('page',1,'intval');
        $pagesize = I('pagesize',10,'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page.','.$pagesize;
        $merchantModel = D('Merchant');
        $count = $merchantModel->get_count($mall_id,$merchant_catid,$keyword);
        $fields='id,name,shop_id,shop_addr,incharge_person,merchant_category_id,phone,pic';
        $res = $merchantModel->get_list($mall_id,$merchant_catid,$keyword,$page,$fields);
        foreach ($res as $key => $value) {
            $merchant = $merchantModel->get_merchant($value['merchant_id']);
            $res[$key]['merchant_name']=$merchant[0]['name'];
            //$res[$key]['shop_addr']=$this->getShopAddr($value['shop_id']);
            $res[$key]['merchant_category']=$this->get_merchant_cat($value['merchant_category_id']);
        }
        if ($res) {
                Ret(array('code' => 1, 'data' => $res, 'total' => $count, 'page_count' => ceil($count/$pagesize)));
        }else{
            Ret(array('code' => 2, 'info' => '查无相关数据！'));
        }
    }
    private function getShopAddr($shopID){
        if($shopID){
            $condition['equip_id'] = $shopID;
            $data = D('Equipment')->where($condition)->field('position')->find();
            return $data['position'];
        }
    }
    public function index_dml_api(){
        before_api();
        checkLogin();
        checkAuth();
        $flag=I('flag');

        $merchantModel=D('Merchant');
        $mall_id=session('mall.mall_id');
        switch($flag){
            case add:
                /* if($data['pic']==null){
                             Ret(array('code' => 2, 'info' => '请输入商户图片!'));
                         }*/
                $data['name']=I('name');
                if($data['name']==null){
                    Ret(array('code' => 2, 'info' => '请输入商户名称!'));
                }
                $data['shop_id']=I('number');
                //var_dump($data['shop_id']);die;
                $shop_addr = $this->get_shop_by_id($data['shop_id']);
                //var_dump($shop_addr);die;
                $data['shop_addr']=$shop_addr['position'];
                //var_dump($data['shop_addr']);die;
                if($data['shop_id']==null){
                    Ret(array('code' => 2, 'info' => '请选择商铺!'));
                }
                $data['contract_number']=I('contract_number',0);
                if($data['contract_number']==0){
                    Ret(array('code' => 2, 'info' => '请输入合同号!'));
                }
                $data['contract_start_time']=I('contract_start_time',0);

                if($data['contract_start_time']==0){
                    Ret(array('code' => 2, 'info' => '请输入合同开始时间!'));
                }
                $data['contract_end_time']=I('contract_end_time',0);
                if($data['contract_end_time']==0){
                    Ret(array('code' => 2, 'info' => '请输入合同结束时间!'));
                }
                $data['license_addr']=I('license_addr');
                if(!$data['license_addr']){
                    Ret(array('code' => 2, 'info' => '请输入营业执照地址!'));
                }
                $data['merchant_category_id']=I('merchant_category_id',0);
                if($data['merchant_category_id']===0){
                    Ret(array('code' => 2, 'info' => '请输入商户类别id!'));
                }
                $data['merchant_classify_id']=I('merchant_classify_id',0);
                if($data['merchant_classify_id']==0){
                    Ret(array('code' => 2, 'info' => '请输入商户分类id!'));
                }
                $data['merchant_category']=I('merchant_category');
                $data['merchant_classify']=I('merchant_classify');
                $data['certificate_pic']=I('certificate_pic');
                $data['license_pic']=I('license_pic');
                $data['merchant_pic']=I('merchant_pic');
                $data['pic']=I('pic');


                /*if($data['passport_pic']==0){
                    Ret(array('code' => 2, 'info' => '请上传营业执照图片!'));
                }*/
                $data['incharge_person']=I('incharge_person');
                if(!$data['incharge_person']){
                    Ret(array('code' => 2, 'info' => '请输入商户负责人!'));
                }
                $data['phone']=I('phone',0);
                if($data['phone']==0){
                    Ret(array('code' => 2, 'info' => '请输入商户电话!'));
                }
                $data['email']=I('email',0);
                if($data['email']==0){
                    Ret(array('code' => 2, 'info' => '请输入商户邮箱!'));
                }
                $data['id_pic']=I('id_pic',0);

                /*if($data['ID_pic']==0){
                    Ret(array('code' => 2, 'info' => '请输入商户身份证图片!'));
                }*/
                $data['mall_id']=$mall_id;
                $data['state']=0;
                $data['check_type']=0;
                $data['submitter_id']=session('worker_id');
                $data['submitter']=session('worker_name');
                $merchantID=$merchantModel->add_merchant($data);
                if($merchantID){
//                    $workerData['merchant_id'] = $merchantID;
//                    $workerData['number'] = 'd'.$merchantID;
//                    $workerData['name'] = 'admin'.$merchantID;
//                    $workerData['password'] = createPassword('888888');
//                    D('Worker')->add($workerData);
                    D('Shop')->merchantBondShopIsOk($data['shop_id'],$merchantID,$data['name']);
                    Ret(array('code' => 1, 'info' => '保存成功!'));
                }else{
                    Ret(array('code' => 2, 'info' => '保存失败!'));
                }
                break;
            case update:
                $data['id']=I('id');
                if($data['id']==null){
                    Ret(array('code' => 2, 'info' => 'ID获取失败!'));
                }
                $data['name']=I('name');
                if($data['name']==null){
                    Ret(array('code' => 2, 'info' => '请输入商户名称!'));
                }
                $data['shop_id']=I('number');
                if($data['shop_id']==0){
                    Ret(array('code' => 2, 'info' => '请输入商铺!'));
                }
                //var_dump($data['shop_id']);die;
                $shop_addr = $this->get_shop_by_id($data['shop_id']);
                //var_dump($shop_addr);die;
                $data['shop_addr']=$shop_addr['position'];
                $data['contract_number']=I('contract_number',0);
                if($data['contract_number']==0){
                    Ret(array('code' => 2, 'info' => '请输入合同号!'));
                }
                $data['contract_start_time']=I('contract_start_time',0);
                if($data['contract_start_time']==0){
                    Ret(array('code' => 2, 'info' => '请输入合同开始时间!'));
                }
                $data['contract_end_time']=I('contract_end_time',0);
                if($data['contract_end_time']==0){
                    Ret(array('code' => 2, 'info' => '请输入合同结束时间!'));
                }
                $data['license_addr']=I('license_addr','0');
                if($data['license_addr']==null){
                    Ret(array('code' => 2, 'info' => '请输入营业执照地址!'));
                }
                $data['merchant_category_id']=I('merchant_category_id',0);
                if($data['merchant_category_id']==0){
                    Ret(array('code' => 2, 'info' => '请输入商户类别id!'));
                }
                $data['merchant_classify_id']=I('merchant_classify_id',0);
                if($data['merchant_classify_id']==0){
                    Ret(array('code' => 2, 'info' => '请输入商户分类id!'));
                }
                $data['merchant_category']=I('merchant_category');
                $data['merchant_classify']=I('merchant_classify');
                $data['incharge_person']=I('incharge_person');
                if(!$data['incharge_person']){
                    Ret(array('code' => 2, 'info' => '请输入商户负责人!'));
                }
                $data['phone']=I('phone',0);
                if($data['phone']==0){
                    Ret(array('code' => 2, 'info' => '请输入商户电话!'));
                }
                $data['email']=I('email');
                if(!$data['email']){
                    Ret(array('code' => 2, 'info' => '请输入商户邮箱!'));
                }
                $data['id_pic']=I('id_pic');
                $data['certificate_pic']=I('certificate_pic');
                $data['license_pic']=I('license_pic');
                $data['merchant_pic']=I('merchant_pic');
                $data['pic']=I('pic');


                $data['web_site']=I('web_site');
                $data['mall_id']=$mall_id;
                $data['state']=0;
                $data['check_type']=4;
                $data['submitter_id']=session('worker_id');
                $data['submitter']=session('worker_name');
                $oldMerchantInfo=$merchantModel->getAllField($data['id']);
                $result=$merchantModel->update_merchant($data);
                if($result){
                    if(empty($oldMerchantInfo[0]['shop_id']))
                    {
                        D('Shop')->merchantUnBondShopIsOk($oldMerchantInfo[0]['shop_id']);
                    }
                    D('Shop')->merchantBondShopIsOk($data['shop_id'],$data['id'],$data['name']);
                    Ret(array('code' => 1, 'info' => '修改成功!'));
                }else{
                    Ret(array('code' => 2, 'info' => '修改失败!'));
                }
                break;
            case delete:
                $id=I('id');
                if(!$id){
                    Ret(array('code'=>2,'info'=>'数据获取失败'));
                }
                $oldMerchantInfo=$merchantModel->getAllField($id);
                //var_dump($oldMerchantInfo);die;
                $result =$merchantModel ->delete_merchant($id);
                if($result){


                    //var_dump($oldMerchantInfo);die;
                    if(!empty($oldMerchantInfo[0]['shop_id'])) {
                        D('Shop')->merchantUnBondShopIsOk($oldMerchantInfo[0]['shop_id']);
                    }
                    D('Worker')->deleteWorkerByMerchantId($id);
                    //删除旗下的员工
                    D('Worker')->deleteWorkerByMerchantId($id);
                    //删除员工下的所有会员卡
                    $workerInfo=D('Worker')->getWorkerByMerchantId($id);
                    //删除所有旗下商品
                    D('Goods')->where('merchant_id='.$id)->delete();
                    D('Card')->setCardFrozen($workerInfo);
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
    public function merchant_view_api(){
        before_api();
        checkLogin();
        checkAuth();
        $mall_id=session('mall.mall_id');
        if(!$mall_id){
            Ret(array('code' => 2, 'info' => '登录数据获取失败！'));
        }
        $merchant_id=I('id','0','intval');
        if(!$merchant_id){
            Ret(array('code' => 2, 'info' => '机构数据获取失败！'));
        }
        $merchantModel=D('Merchant');
        $fields=array('id,state,name,contract_number,contract_start_time,contract_end_time,web_site,license_addr,incharge_person,phone,about,mall_id,certificate_pic,merchant_pic,merchant_category_id,merchant_classify,merchant_classify_id,pic,license_pic,id_pic,shop_id,shop_addr,email');
        $merchant=$merchantModel->view_merchant($merchant_id,$mall_id,$fields);
//        $merchantShopModel=D('MerchantShop');
//        $shop=$merchantShopModel->get_shop($merchant_id);
        //获取物业费和租金
//      $cost=$this->get_shop_cost($shop[0]['shop_id']);
        $merchant[0]['merchant_category']=$this->get_merchant_cat($merchant[0]['merchant_category_id']);
        $cost=$this->get_shop_cost($merchant[0]['shop_id']);
//        var_dump($cost);die;
        $merchant[0]['property_price']=$cost[0]['property_price'];
        $merchant[0]['rent_rate']=$cost[0]['rent_rate'];
        $merchant[0]['shop_addr']=$this->getShopAddr($merchant[0]['shop_id']);
        //管理费和宽带费
        $expense=$this->get_expense_by_merchant($merchant_id);
        foreach($expense as $k => $v){
            if($expense[$k]['cost_cat']==1){
                $merchant[0]['manage_cost']=$expense[$k]['cost'];
            }
            if($expense[$k]['cost_cat']==2){
                $merchant[0]['internet_cost']=$expense[$k]['cost'];
            }

        }
        $merchant[0]['merchant_pic'] = explode(',',$merchant[0]['merchant_pic']);
        $merchant[0]['certificate_pic'] = explode(',',$merchant[0]['certificate_pic']);
        if($merchant){
            Ret(array('code'=>1,'data'=>$merchant[0]));
        }else{
            Ret(array('code'=>2,'info'=>'没有数据！'));
        }

    }
    private function get_shop_cost($shop_id){
        $data=D('Shop')->get_shop_cost($shop_id);
        if($data){
            return $data;
        }
    }

    private function get_expense_by_merchant($merchant_id){
        $costList = $this->get_expense();
        foreach($costList as $key => $value){
            $merchants = explode('-',$value['merchant_arange']);
            if(in_array($merchant_id,$merchants) || $value['merchant_arange']==0){
                $merchantCost[$key]['cost_cat']=$value['cost_cat'];
                $merchantCost[$key]['cost']=$value['cost'];
            }
        }
        return $merchantCost;
    }


    /*
     * 商户计费列表
     */
    public function cost_api(){

        before_api();
        checkLogin();
        checkAuth();
        $mall_id = session('mall.mall_id');
        $page = I('page',1,'intval');
        $pagesize = I('pagesize',10,'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page.','.$pagesize;

        $merchantModel = D('MerchantCost');
        $count = $merchantModel->get_count($mall_id);
        if ($count) {
            $res = $merchantModel->get_list($mall_id,$page,$fields=null);
            if (empty($res)) {
                Ret(array('code' => 2, 'info' => '查无相关数据！'));
            }else{
                foreach ($res as $key => $value) {
                    $res[$key]['cost_cat']=$this->data[$value['cost_cat']-1]['name'];
                    $res[$key]['merchant_name'] = $this->get_merchant($value['merchant_arange']);
//                    $res[$key]['merchant_name']=$merchant;
                    $state=get_state($value['state']);
                    $res[$key]['state']=$state;
                }
//                var_dump($res);die();
                Ret(array('code' => 1, 'data' => $res, 'total' => $count, 'page_count' => ceil($count/$pagesize)));
            }
        }else{
            Ret(array('code' => 2, 'info' => '查无相关数据！'));
        }

    }



    public function get_merchant_cat($id){
        $model = D('MerchantCat');
        $array=explode('-',$id);
        foreach($array as $k => $v ) {
            $item = $model->get_category($v);
            if ($item[0][name] != null) {
                $items[$k] = $item[0][name];
            }
            $data= implode('-', $items);
        }
        return $data;

    }

    public function get_merchant($ids){
        $model = D('Merchant');
        $array=explode('-',$ids);
        foreach($array as $k => $v ) {
            $item = $model->get_merchant_by_id($v);
            if ($item[0][name] != null) {
                $items[$k] = $item[0][name];
            }
            $data= implode('-', $items);
        }
        return $data;

    }

    public function get_shop($ids){
        $model = D('Shop');
        $array=explode('-',$ids);
        foreach($array as $k => $v ) {
            $item = $model->get_shop_by_id($v);
            if ($item[0][name] != null) {
                $items[$k] = $item[0][name];
            }
            $data= implode(',', $items);
        }
        return $data;

    }
    public function get_shop_by_id($id){
        if($id){
            $condition['id']=$id;
            $item = D('Shop')->where($condition)->find();

        }

        return $item;

    }


    /*
     * 商户计费增删改
     */
    public function cost_dml_api(){
        before_api();
        checkLogin();
        checkAuth();
        $flag=I('flag');
        $merchantModel=D('MerchantCost');
        $mall_id=session('mall.mall_id');
        switch($flag){
            case add:
                $data['mall_id']=$mall_id;
                if($data['mall_id']==null){
                    Ret(array('code' => 2, 'info' => '登录信息（mall_id）获取失败!'));
                }
                $data['merchant_arange']=I('category_id');
                if($data['merchant_arange']==null){
                    Ret(array('code' => 2, 'info' => '请选择商户范围!'));
                }
                $data['cost_cat']=I('cost_cat');
                if($data['cost_cat']==null){
                    Ret(array('code' => 2, 'info' => '请选择费用类目!'));
                }
                $data['cost']=I('cost',0);
                if($data['cost']==0){
                    Ret(array('code' => 2, 'info' => '请输入价格!'));
                }
                $data['state']=0;
                $data['check_type']=0;
                $data['submitter']=session('worker_id');
                $data['submit_time']=date('Y-m-d H:i:s');
                $result=$merchantModel->add_cost($data);
                if($result){
                    Ret(array('code' => 1, 'info' => '保存成功!'));
                }else{
                    Ret(array('code' => 2, 'info' => '保存失败!'));
                }

                break;
            case update:
                $data['id']=I('id');
                if($data['id']==null){
                    Ret(array('code' => 2, 'info' => 'ID获取失败!'));
                }
                $data['mall_id']=$mall_id;
                if($data['mall_id']==null){
                    Ret(array('code' => 2, 'info' => '登录信息（mall_id）获取失败!'));
                }
                $data['merchant_arange']=I('category_id');
                if($data['merchant_arange']==null){
                    Ret(array('code' => 2, 'info' => '请选择商户范围!'));
                }
                $data['cost_cat']=I('cost_cat');
                if($data['cost_cat']==null){
                    Ret(array('code' => 2, 'info' => '请选择费用类目!'));
                }
                $data['cost']=I('cost',0);
                if($data['cost']==0){
                    Ret(array('code' => 2, 'info' => '请输入价格!'));
                }
                $data['state']=0;
                $data['check_type']=4;
                $data['submitter_id']=session('worker_id');
                $data['submit_time']=date('Y-m-d H:i:s');
                $result=$merchantModel->update_cost($data);
                if($result){
                    Ret(array('code' => 1, 'info' => '保存成功!'));
                }else{
                    Ret(array('code' => 2, 'info' => '保存失败!'));
                }
                break;
            case delete:
                $id=I('id');
                if(!$id){
                    Ret(array('code'=>2,'info'=>'数据获取失败'));
                }
                $result =$merchantModel ->del_cost($id);
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

    //获取商户计费（管理费，宽带费等）
    public function get_expense(){
        $mall_id = session('mall.mall_id');
        $merchantModel = D('MerchantCost');
        $count = $merchantModel->get_count($mall_id);
        $page = '1,100';
        if ($count) {
            $res = $merchantModel->get_list($mall_id,$page,$fields=null);
            if (empty($res)) {
                Ret(array('code' => 2, 'info' => '查无相关数据！'));
            }else{
                foreach ($res as $key => $value) {
                    $merchant = $this->get_merchant($value['merchant_arange']);
                    $res[$key]['merchant_name']=$merchant;
                    $state=get_state($value['state']);
                    $res[$key]['state']=$state;
                }
//                var_dump($res);die();
               return $res;
            }
        }else{
            Ret(array('code' => 2, 'info' => '查无相关数据！'));
        }
    }

    /*
     * 商户计费详情
     */
    public function merchant_cost_view_api()
    {
        before_api();
        checkLogin();
        checkAuth();
        $id = I('id');
        if (!$id) {
            Ret(array('code' => 2, 'info' => '参数（id）获取失败！'));
        }
        if ($id) {
            $data = D('MerchantCost')->where('id=' . $id)->find();
            $data['cost_cat'] = $this->data[$data['cost_cat'] - 1]['name'];
            if ($data) {
                Ret(array('code' => 1, 'data' => $data['0']));
            } else {
                Ret(array('code' => 2, 'data' => '查无相关数据！'));
            }
        }
    }
        public function cost_view_api(){
            before_api();
            checkLogin();
            checkAuth();
            $id = I('id');
            if(!$id){
                Ret(array('code' => 2, 'info' => '参数（id）获取失败！'));
            }
            $data = D('MerchantCost')->getInfo($id);
            $data[0]['cost_cat']=$this->data[$data[0]['cost_cat']-1]['name'];
            $merchant = $this->get_merchant($data[0]['merchant_arange']);
            $data[0]['merchant_name']=$merchant;
            if($data){
                Ret(array('code' => 1, 'data' =>$data[0]));
            }else{
                Ret(array('code' => 2, 'data' => '查无相关数据！'));
                }



    }

    /*
     * 获取商户类别
     */
    public function category(){
        before_api();
        $data=D('MerchantCat')->get_cat();
        if($data){
            Ret(array('code'=>1,'data'=>$data));
        }else{
            Ret(array('code'=>2,'info'=>'没有数据！'));
        }

    }

    var $data=array(array('id'=>1,'name'=>'管理计费'),array('id'=>2,'name'=>'宽带计费'));

    public function cost_cat(){
        before_api();
        $data= $this->data;
        if($data){
            Ret(array('code'=>1,'data'=>$data));
        }else{
            Ret(array('code'=>2,'info'=>'没有数据！'));
        }

    }
    /*
     * 获取商户子类
     */
    public function sub_category(){
        before_api();
        $data=D('MerchantSubCat')->get_cat();
        if($data){
            Ret(array('code'=>1,'data'=>$data));
        }else{
            Ret(array('code'=>2,'info'=>'没有数据！'));
        }
    }

    /*
    * 获取商铺列表
    */

    public function shops(){
        before_api();
        $data=D('Shop')->get_shop();
        foreach($data as $k => $v){
            $data[$k]['name']=$v['eqip'];
        }
        if($data){
            Ret(array('code'=>1,'data'=>$data));
        }else{
            Ret(array('code'=>2,'info'=>'没有数据！'));
        }
    }


    public function merchant_check_api(){
        before_api();
        checkAuth();
        checkLogin();

        $data['id'] = I('id');
        $data['state'] = I('state');
        if(intval($data['id']) <1 || intval($data['state']) <0){
            Ret(array('code' => 2, 'info' => '参数有误!'));
        }
        //step 2: 根据主键id 获取需要的字段信息
        $checkModel=D('Merchant');
        //step 3 ；修改审核表状态
        $editInfo = $checkModel->updateState($data);
        if($editInfo){
            $info = $checkModel->getAllField($data['id']);
            foreach ($info as $key => $value) {
                $state=get_state($value['state']);
                $info[$key]['state']=$state;
            }
            //step:4 保存日志
            $log = array(
                'check_time' => date('Y-m-d H:i:s'),
                'merchant' => $info[0]['name'],
                'check_type' => $info[0]['check_type'],
                'submitter' => $info[0]['submitter'],
                'checker' => session('worker_name'),
                'check_state' => $info[0]['state'],
                'goods_id'=> $info[0]['id']
            );
            if($log['check_type']==0){
                $log['check_type']='新增商户信息';
            }
            if($log['check_type']==4){
                $log['check_type']='修改商费信息';
            }
            $logModel=D('MerchantCheckLog');
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
//        $data['state'] = I('state');
//        if ($data['state'] < 1) {
//            Ret(array('code' => 2, 'info' => '参数（check_state）有误!'));
//        }
//        $checkModel=D('Merchant');
//        $res=  $checkModel->make_check($data);
//
//        if($data['state']==1){
//            $logdata['check_state']='通过';
//        }
//        if($data['state']==2){
//            $logdata['check_state']='未通过';
//        }
//        $logdata['check_type']=I('check_type');
//        if ($logdata['check_type'] == null) {
//            Ret(array('code' => 2, 'info' => '参数（state）有误!'));
//        }
//        if($logdata['check_type']==0){
//            $logdata['check_type']='新增商户信息';
//        }
//        if($logdata['check_type']==4){
//            $logdata['check_type']='修改商费信息';
//        }
//        $logdata['merchant']=I('name');
//        if($logdata['merchant']==null){
//            Ret(array('code' => 2, 'info' => '参数（merchant）有误!'));
//        }
//        $logdata['submitter'] = I('submitter');
//        if (!$logdata['submitter'] ) {
//            Ret(array('code' => 2, 'info' => '参数（submitter）有误!'));
//        }
//        $logdata['checker'] = session('worker_id');
//        $logdata['check_time'] = date('Y-m-d H:i:s');
//        $logModel=D('MerchantCheckLog');
//        $log=$logModel->add_log($logdata);
//        if(!$log){
//            Ret(array('code' => 2, 'info' => '日志保存失败'));
//        }
//        if($res){
//            Ret(array('code' => 1, 'info' => '审核成功'));
//        }else{
//            Ret(array('code' => 2, 'info' => '审核失败'));
//        }
    }
    /*
     * 商户审核
     */
//    public function merchant_check_api(){
//        before_api();
//        checkAuth();
//        checkLogin();
//        $data['id'] = I('id');
//        if ($data['id'] < 1) {
//            Ret(array('code' => 2, 'info' => '参数（id）有误!'));
//        }
//        $data['state']=I('state');
//        if ($data['state']==null) {
//            Ret(array('code' => 2, 'info' => '参数（state）有误!'));
//        }
//        if($data['state']==1){
//            $logdata['check_state']='通过';
//        }
//        if($data['state']==2){
//            $logdata['check_state']='未通过';
//        }
//        $logdata['check_type']=I('check_type');
//        if ($logdata['check_type'] == null) {
//            Ret(array('code' => 2, 'info' => '参数（state）有误!'));
//        }
//        if($logdata['check_type']==0){
//            $logdata['check_type']='新增商户计费信息';
//        }
//        if($logdata['check_type']==4){
//            $logdata['check_type']='修改商户计费信息';
//        }
//        $logdata['merchant']=I('name');
//        if($logdata['merchant']==null){
//            Ret(array('code' => 2, 'info' => '参数（merchant）有误!'));
//        }
//        $logdata['submitter'] = I('submitter');
//        if (!$logdata['submitter'] ) {
//            Ret(array('code' => 2, 'info' => '参数（submitter）有误!'));
//        }
//        $logdata['checker'] = session('worker_id');
//        $logdata['check_time'] = date('Y-m-d H:i:s');
//        $checkModel=D('Merchant');
//        $checkModel->startTrans();
//        $logModel=D('MerchantCheckLog');
//        $log=$logModel->add_log($logdata);
//        if($log){
//            $checkModel->make_check($data);
//            $result=$checkModel->commit();
//            if($result){
//                Ret(array('code' => 1, 'info' => '审核成功'));
//            }else{
//                $logModel->rollback();
//                Ret(array('code' => 2, 'info' => '审核失败'));
//            }
//        }else{
//            $logModel->rollback();
//            Ret(array('code' => 2, 'info' => '保存失败'));
//        }
//    }



    /*
     * 审核列表
     */
    public function inst_check_list()
    {

        before_api();
        checkLogin();
        checkAuth();
        $mall_id = session('mall.mall_id');
        $page = I('page', 1, 'intval');
        $pagesize = I('pagesize', 10, 'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page . ',' . $pagesize;
        $merchantModel = D('Merchant');
        $gcatModel = D('MerchantCat');
        $subCatModel = D('MerchantSubCat');

        $count = $merchantModel->get_check_count($mall_id);
        if ($count) {
            $res = $merchantModel->get_check_list($mall_id, $page, $fields = null);
            foreach ($res as $key => $value) {
                $res[$key]['state']= get_state($value['state']);
               $res[$key]['nnn']='1';
            }
            if (empty($res)) {
                Ret(array('code' => 2, 'info' => '查无相关数据！'));
            } else {
                Ret(array('code' => 1, 'data' => $res, 'total' => $count, 'page_count' => ceil($count / $pagesize)));
            }
        } else {
            Ret(array('code' => 2, 'info' => '查无相关数据！'));
        }
    }

    /*
     * 商户计费审核
     */
    public function merchant_cost_check(){
        before_api();
        checkLogin();
        checkAuth();
        $data['id'] = I('id');
        $data['state'] = I('state');
        if(intval($data['id']) <1 || intval($data['state']) <0){
            Ret(array('code' => 2, 'info' => '参数有误!'));
        }
        //step 2: 根据主键id 获取需要的字段信息
        $merchantModel=D('MerchantCost');
        //step 3 ；修改审核表状态
        $editInfo = $merchantModel->updateState($data);
        if($editInfo){
            $info = $merchantModel->getAllField($data['id']);
            foreach ($info as $key => $value) {
                $merchant = $this->get_merchant($value['merchant_arange']);
                $info[$key]['merchant_name']=$merchant;
                $state=get_state($value['state']);
                $info[$key]['state']=$state;
            }
            //step:4 保存日志
            $log = array(
                'check_time' => date('Y-m-d H:i:s'),
                'merchant' => $info[0]['merchant_name'],
                'check_type' => $info[0]['check_type'],
                'submitter' => $info[0]['submitter'],
                'checker' => session('worker_name'),
                'check_state' => $info[0]['state'],
                'merchant_id'=> $info[0]['id']
            );
            if($log['check_type']==0){
                $log['check_type']='新增商户计费信息';
            }
            if($log['check_type']==4){
                $log['check_type']='修改商户计费信息';
            }
            $logModel=D('MerchantCheckLog');
            if($logModel->insertLog($log)){
                Ret(array('code'=>1,'data'=>'审核成功'));
            }
        }else {
            Ret(array('code'=>2,'info'=>'审核失败'));
        }


//        $merchantModel=D('MerchantCost');
//        $merchantModel->startTrans();
//        $data['id']=I('id');
//        if($data['id']==null){
//            Ret(array('code' => 2, 'info' => 'ID获取失败!'));
//        }
//        if(!$data['id']){
//            Ret(array('code' => 2, 'info' => '没有数据'));
//        }
//        $data['state']=I('state');
//        if ($data['state'] < 1) {
//            Ret(array('code' => 2, 'info' => '参数（check_state）有误!'));
//        }
//        if($data['state']==1){
//            $logdata['check_state']='通过';
//        }
//        if($data['state']==2){
//            $logdata['check_state']='未通过';
//        }
//
//        $logdata['check_type']=I('check_type');
//        if ($logdata['check_type']==null) {
//            Ret(array('code' => 2, 'info' => '参数（state）有误!'));
//        }
//        if($logdata['check_type']==0){
//            $logdata['check_type']='新增商户计费信息';
//        }
//        if($logdata['check_type']==4){
//            $logdata['check_type']='修改商户计费信息';
//        }
//
//        $logdata['merchant']=I('merchant_name');
//        if($logdata['merchant']==null){
//            Ret(array('code' => 2, 'info' => '参数（merchant）有误!'));
//        }
//        $logdata['submitter'] = I('submitter');
//        if (!$logdata['submitter'] ) {
//            Ret(array('code' => 2, 'info' => '参数（submitter）有误!'));
//        }
//        $logdata['checker'] = session('worker_id');
//        $logdata['check_time'] = date('Y-m-d H:i:s');
//        $logModel=D('MerchantCheckLog');
//        $log=$logModel->add($logdata);
//        if($log){
//            $result=$merchantModel->update_cost($data);
//            $merchantModel->commit();
//            if($result){
//                Ret(array('code' => 1, 'info' => '审核成功'));
//            }else{
//                Ret(array('code' => 2, 'info' => '审核失败'));
//            }
//        }else{
//            $merchantModel->rollback();
//            Ret(array('code' => 2, 'info' => '保存失败'));
//        }



    }


    /*
     * 商户计费审核列表
     */
    public function merchant_cost_check_list(){
        before_api();
        checkLogin();
        checkAuth();
        $mall_id = session('mall.mall_id');
        $page = I('page',1,'intval');
        $pagesize = I('pagesize',10,'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page.','.$pagesize;

        $merchantModel = D('MerchantCost');
        $count = $merchantModel->get_check_count($mall_id);
        if ($count) {
            $res = $merchantModel->get_check_list($mall_id,$page,$fields=null);
            if (empty($res)) {
                Ret(array('code' => 2, 'info' => '查无相关数据！'));
            }else{
                foreach ($res as $key => $value) {
                    $res[$key]['cost_cat']=$this->data[$value['cost_cat']-1]['name'];
                    $merchant = $this->get_merchant($value['merchant_arange']);
                    $res[$key]['merchant_name']=$merchant;
                    $state=get_state($value['state']);
                    $res[$key]['state']=$state;
                    $res[$key]['nnn']='1';
                }
                Ret(array('code' => 1, 'data' => $res, 'total' => $count, 'page_count' => ceil($count/$pagesize)));
            }
        }else{
            Ret(array('code' => 2, 'info' => '查无相关数据！'));
        }

    }

    /*
     * 商户操作日志
     */
    public function merchant_check_log(){
        before_api();
        checkLogin();
        checkAuth();
        $keyword = I('keyword');
        $page = I('page',1,'intval');
        $pagesize = I('pagesize',10,'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page.','.$pagesize;

        $merchantModel = D('MerchantCheckLog');
        $count = $merchantModel->get_count($keyword);
        if ($count) {
            $res = $merchantModel->get_list($keyword,$page,$fields=null);
            Ret(array('code' => 1, 'data' => $res, 'total' => $count, 'page_count' => ceil($count/$pagesize)));
        }else{
            Ret(array('code' => 2, 'info' => '查无相关数据！'));
        }

    }

    /*
    * 获取机构
    */
    public function getMerchantList(){
        before_api();
        checkLogin();
        checkAuth();
        $data=D('Merchant')->field('id,name')->select();
        if($data){
            Ret(array('code'=>1,'data'=>$data));
        }else{
            Ret(array('code'=>2,'info'=>'没有数据！'));
        }
    }




    public function merchant_view(){
        before_api();
        checkLogin();
        checkAuth();
        $mall_id=session('mall.mall_id');
        if(!$mall_id){
            Ret(array('code' => 2, 'info' => '登录数据获取失败！'));
        }
        $merchant_id=I('id','0','intval');
        if(!$merchant_id){
            Ret(array('code' => 2, 'info' => '机构数据获取失败！'));
        }
        $merchantModel=D('Merchant');
        $fields=array('id,state,name,contract_number,contract_start_time,contract_end_time,web_site,license_addr,incharge_person,phone,about,mall_id,certificate_pic,merchant_pic,merchant_category_id,merchant_classify,merchant_classify_id,pic,license_pic,id_pic,shop_id,shop_addr,email');
        $merchant=$merchantModel->view_merchant($merchant_id,$mall_id,$fields);
//        $merchantShopModel=D('MerchantShop');
//        $shop=$merchantShopModel->get_shop($merchant_id);
        //获取物业费和租金
//      $cost=$this->get_shop_cost($shop[0]['shop_id']);

            //获取物业费和租金
            $cost = $this->get_shop_cost($merchant[0]['shop_id']);
        var_dump($cost);die;
            $data[0]['property_price'] = $cost[0]['property_price'];
            $data[0]['rent_rate'] = $cost[0]['rent_rate'];
            //管理费和宽带费
            $expense = $this->get_expense_by_merchant($merchant_id);
            foreach ($expense as $k => $v) {
                if ($expense[$k]['cost_cat'] == 1) {
                    $merchant[0]['manage_cost'] = $expense[$k]['cost'];
                }
                if ($expense[$k]['cost_cat'] == 2) {
                    $merchant[0]['internet_cost'] = $expense[$k]['cost'];
                }

            }

        $merchant[0]['merchant_pic'] = explode(',',$merchant[0]['merchant_pic']);
        $merchant[0]['certificate_pic'] = explode(',',$merchant[0]['certificate_pic']);
        if($merchant){
            Ret(array('code'=>1,'data'=>$merchant[0]));
        }else{
            Ret(array('code'=>2,'info'=>'没有数据！'));
        }

    }

    public function inst_view_api(){
        before_api();
        checkLogin();
        checkAuth();
        $mall_id=session('mall.mall_id');
        if(!$mall_id){
            Ret(array('code' => 2, 'info' => '登录数据获取失败！'));
        }
        $merchant_id=I('id','0','intval');
        if(!$merchant_id){
            Ret(array('code' => 2, 'info' => '机构数据获取失败！'));
        }
        $merchantModel=D('Merchant');
        $fields=array('id,state,name,contract_number,contract_start_time,contract_end_time,web_site,license_addr,incharge_person,phone,about,mall_id,certificate_pic,merchant_pic,merchant_category_id,merchant_classify,merchant_classify_id,pic,license_pic,id_pic,shop_id,shop_addr,email');
        $data=$merchantModel->view_merchant($merchant_id,$mall_id,$fields);
//        foreach($data as $key =>$value){
//            $data[$key]['category_name']=$this->getCats($value['category_id']);
//        }
        if($data){
            //获取物业费和租金
            $cost=$this->get_shop_cost($data[0]['shop_id']);
            $data[0]['property_price']=$cost[0]['property_price'];
            $data[0]['rent_rate']=$cost[0]['rent_rate'];
//            var_dump($data);die();
            //管理费和宽带费
            $expense = $this->get_expense_by_merchant($merchant_id);
            foreach ($expense as $k => $v) {
                if ($expense[$k]['cost_cat'] == 1) {
                    $merchant[0]['manage_cost'] = $expense[$k]['cost'];
                }
                if ($expense[$k]['cost_cat'] == 2) {
                    $merchant[0]['internet_cost'] = $expense[$k]['cost'];
                }

            }
//            $data[0]['shop_addr'] =$data[0]['position_idx'];

            $merchant[0]['merchant_pic'] = explode(',',$merchant[0]['merchant_pic']);
            $merchant[0]['certificate_pic'] = explode(',',$merchant[0]['certificate_pic']);
//            var_dump($data);die;
            Ret(array('code'=>1,'data'=>$data[0]));
        }else{
            Ret(array('code'=>2,'info'=>'没有数据！'));
        }
    }
//教室修改下拉选择列表
    public function get_update_merchant_shop_list()
    {
        before_api();
        //$m = new \Mall\Controller\Model();
        //$instUsedShopSql="select distinct shop_id from institution";
        //$instUsedShopList=$m->query($instUsedShopSql);

        //查询机构已经用过的店铺列表
        //echo('11111');
        //$condition['state']=1;
        $merchantId=I('merchant_id');

        $instUsedShopList=D('Institution')->distinct(true)->field('shop_id')->select();
        $instUsedShopArr=array();
        foreach ($instUsedShopList as $key=>$value)
        {
            if($value['shop_id']!=NULL)
            {
                $instUsedShopArr[$key]=$value['shop_id'];
            }
        }
        //查询商户已经用过的店铺列表
        $merchantUsedShopList=D('Merchant')->distinct(true)->where('id<>'.$merchantId)->select();
        $merchantUsedShopArr=array();
        foreach ($merchantUsedShopList as $key=>$value)
        {
            if($value['shop_id']!=NULL)
            {
                $merchantUsedShopArr[$key] = $value['shop_id'];
            }
        }
        //var_dump($merchantUsedShopArr);die;
        //查询教室已经用过的店铺列表
        $roomId=I('room_id');
        $roomUsedShopList=D('Room')->distinct(true)->field('shop_id')->select();
        $roomUsedShopArr=array();
        foreach ($roomUsedShopList as $key=>$value)
        {
            if($value['shop_id']!=NULL)
            {
                $roomUsedShopArr[$key]=$value['shop_id'];
            }
        }
        //var_dump($roomUsedShopArr);die;
        if(!empty($roomUsedShopArr))
        {
            $usedShopList=array_merge($instUsedShopArr,$merchantUsedShopArr,$roomUsedShopArr);
        }
        else
        {
            $usedShopList=array_merge($instUsedShopArr,$merchantUsedShopArr);
        }
        $arr_string = join(',', $usedShopList);
        //var_dump($arr_string);die;
        $m = D('Shop');
        $sql1="select * from shop where id not in (".$arr_string.") and state=1";
        $data=$m->query($sql1);
        //$data = D('Shop')->select();

        foreach ($data as $k => $v) {
            $res[$k]['id'] = $data[$k]['id'];
            $res[$k]['name'] = $data[$k]['position'];
        }
        if ($res) {
            Ret(array('code' => 1, 'data' => $res));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据！'));
        }
    }

}