<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/6/26
 * Time: 17:44
 */
namespace Mall\Controller;

class ShopController extends CommonController{
    //商铺列表
    public function index_api(){
        before_api();
        checkLogin();
        checkAuth();
        $keyword = I('keyword');
        $page = I('page',1,'intval');
        $pagesize = I('pagesize',10,'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page.','.$pagesize;
        $shopModel=D('shop');
        $count = $shopModel->get_count($keyword);
        $res=$shopModel->get_list($keyword,$page,$fields = null);
        if(!$res){
            Ret(array('code'=>2,'info'=>'没有数据！'));
        }else{
            foreach ($res as $key => $value) {
                $type = $this->get_shop_type($value['type']);
                $res[$key]['type']=$type['name'];
                $res[$key]['state']=get_state($value['state']);
                $res[$key]['nnn']='1';
            }
            Ret(array('code'=>1,'data'=>$res,'total' => $count, 'page_count' => ceil($count/$pagesize)));
        }
    }


    //商铺增删改
    public function shop_dml_api(){
        before_api();
        checkLogin();
        checkAuth();
        $mall_id=session('mall.mall_id');
        $flag = I('flag');
        switch ($flag) {
            case 'add':
                $data['mall_id'] =$mall_id;
                if(!$data['mall_id']){
                    Ret(array('code' => 2, 'info' => '数据获取失败！'));
                }
                $data['position']  = I('position');
                if(!$data['position']){
                    Ret(array('code' => 2, 'info' => '请填写商铺位置！'));
                }
                $data['type']  = I('type_id');
                if(!$data['type']){
                    Ret(array('code' => 2, 'info' => '请选择类型！'));
                }
                //传的设备ID
                $data['eqip']  = I('eqip');
                if(!$data['eqip']){
                    Ret(array('code' => 2, 'info' => '请选择门禁设备！'));
                }
                $data['area']  = I('area');
                if(!$data['area']){
                    Ret(array('code' => 2, 'info' => '请填写面积！'));
                }
                if(!is_numeric($data['area'])){
                    Ret(array('code' => 2, 'info' => '请填写数字！'));
                }
                $data['rent_rate'] = I('rent_rate');
                if(!$data['rent_rate']){
                    Ret(array('code' => 2, 'info' => '请填写租用基准价格！'));
                }
                if(!is_numeric($data['rent_rate'])){
                    Ret(array('code' => 2, 'info' => '请输入数字！'));
                }
                $data['property_price'] = I('property_price');
                if(!$data['property_price']){
                    Ret(array('code' => 2, 'info' => '请填物业基准价格！'));
                }
                if(!is_numeric($data['property_price'])){
                    Ret(array('code' => 2, 'info' => '请输入数字！'));
                }
                $data['state'] = 0;
                $data['check_type'] = 0;
                $data['submitter_id'] =session('worker_id');
                $data['submitter'] =session('worker_name');
                $data['number'] = 0;
                $shopModel=D('shop');
                $equipmentIdList=explode('-',$data['eqip']);

                $check = D('EquipmentDoor')->hasUsedEq($equipmentIdList);
                $result=false;
               if($check){
                   //$result=$shopModel->add_shop($data,$condition);
                   Ret(array('code' => 2, 'info' => '该门禁已经绑定到了其他商铺上了！'));
//                    echo 'dsgsdg';
                }else{
                   //店铺里eqip是设备ID
                    //$data['eqip']= $data['eqip'];
                   //设备ID用逗号间隔
                    $data['eqip']=implode(',', $equipmentIdList);

                    //获取设备IP列表

                    $eqIpList = D('EquipmentDoor')->getEqIpList($equipmentIdList);
                    //equipment_id为设备IP
                    $data['equipment_ip']=implode(',', $eqIpList);
                    $eqPosList=D('EquipmentDoor')->getEqPosList($equipmentIdList);
                   $data['position_idx']=implode(',',$eqPosList);
                    $result=$shopModel->add($data);

                    /*//更新设备表状态
                    foreach ($equipmentIdList as $key=>$value)
                    {
                        $eqInfo['equipId']=$value;
                        $eqInfo['ownerType']='商铺';
                        $eqInfo['ownerName']=$data['position'];
                        $eqInfo['bondState']=1;
                        $eqInfo['shop_id']=$result;
                        if(!D('EquipmentDoor')->updateEquipInfo($data))
                        {
                            Ret(array('code' => 2, 'info' => '更新设备信息失败！'));
                        }
                    }*/


                }
                if ($result) {
                   // var_dump($result);die;
                    foreach ($equipmentIdList as $key=>$value)
                    {
                        $eqInfo['equipId']=$value;
                        $eqInfo['ownerType']='商铺';
                        $eqInfo['ownerName']=$data['position'];
                        $eqInfo['bondState']=1;
                        $eqInfo['shop_id']=$result;
                        if(!D('EquipmentDoor')->updateEquipInfo($eqInfo))
                        {
                            Ret(array('code' => 2, 'info' => '更新设备信息失败！'));
                        }
                    }

                    Ret(array('code' => 1, 'info' => '添加成功！'));
                }else{
                    Ret(array('code' => 2, 'info' => '添加失败,系统出错！'));
                }
                break;
            case 'update':
                $data['mall_id'] =$mall_id;
                if(!$data['mall_id']){
                    Ret(array('code' => 2, 'info' => '数据获取失败！'));
                }
                $data['id']  = I('id');
                if(!$data['id']){
                    Ret(array('code' => 2, 'info' => 'ID获取失败！'));
                }
                $data['position']  = I('position');
                if(!$data['position']){
                    Ret(array('code' => 2, 'info' => '数据获取失败！'));
                }
                $data['type']  = I('type_id');
                if(!$data['type']){
                    Ret(array('code' => 2, 'info' => '请选择类型！'));
                }
                //设备ID
                $data['eqip']  = I('eqip');
                if(!$data['eqip']){
                    Ret(array('code' => 2, 'info' => '请选择设备！'));
                }
                $data['area']  = I('area');
                if(!$data['area']){
                    Ret(array('code' => 2, 'info' => '请输入面积！'));
                }
                if(!is_numeric($data['area'])){
                    Ret(array('code' => 2, 'info' => '请填写数字！'));
                }
                $data['rent_rate'] = I('rent_rate');

                if(!$data['rent_rate']){
                    Ret(array('code' => 2, 'info' => '请输入租用基准价格！'));
                }
                if(!is_numeric($data['rent_rate'])){
                    Ret(array('code' => 2, 'info' => '请输入数字11！'));
                }
                $data['property_price'] = I('property_price');
                if(!$data['property_price']){
                    Ret(array('code' => 2, 'info' => '请输入物业基准价格！'));
                }
                if(!is_numeric($data['property_price'])){
                    Ret(array('code' => 2, 'info' => '请输入数字22！'));
                }
                $data['state'] = 4;
                $data['check_type'] = 4;
                $data['number'] = 0;
                $data['submitter_id'] =session('worker_id');
                $data['submitter'] =session('worker_name');

                $shopModel=D('shop');
                $equipmentIdList=explode('-',$data['eqip']);

                $check = D('EquipmentDoor')->hasUsedEq($equipmentIdList);
                $result=false;
                if($check){
                    //$result=$shopModel->add_shop($data,$condition);
                    Ret(array('code' => 2, 'info' => '该门禁已经绑定到了其他商铺上！'));
//                    echo 'dsgsdg';
                }
                else
                {

                    $data['eqip']=implode(',', $equipmentIdList);

                    //获取设备IP列表

                    $eqIpList = D('EquipmentDoor')->getEqIpList($equipmentIdList);

                    //equipment_id为设备IP
                    $data['equipment_ip']=implode(',', $eqIpList);
                    $result = $shopModel->update_shop($data);
                }
                if ($result) {
                    foreach ($equipmentIdList as $key=>$value)
                    {
                        $eqInfo['equipId']=$value;
                        $eqInfo['ownerType']='商铺';
                        $eqInfo['ownerName']=$data['position'];
                        $eqInfo['bondState']=1;
                        $eqInfo['shop_id']=$data['id'];
                        if(!D('EquipmentDoor')->updateEquipInfo($eqInfo))
                        {
                            Ret(array('code' => 2, 'info' => '更新设备信息失败！'));
                        }
                    }
                    Ret(array('code' => 1, 'info' => '修改成功！'));
                }else{
                    Ret(array('code' => 2, 'info' => '修改失败！'));
                }
                break;
            case 'delete':
                $id  = I('id');
                if(!$id){
                    Ret(array('code' => 2, 'info' => 'ID获取失败！'));
                }
                $shopModel=D('shop');
                $shopInfo=$shopModel->view_shop($id);
                //将商铺绑定的设备解除绑定
               // var_dump($equipmentIdList);die;
                //var_dump($shopInfo);die;
                $equipmentIdList=explode(',',$shopInfo[0]['eqip']);
                if(!empty($shopInfo[0]['eqip']))
                {
                    foreach ($equipmentIdList as $key => $value) {
                        $eqInfo['equipId'] = $value;
                        $eqInfo['ownerType'] = '';
                        $eqInfo['ownerName'] = '';
                        $eqInfo['bondState'] = 0;
                        $eqInfo['shop_id'] = '';
                        //var_dump($eqInfo);die;
                        if (!D('EquipmentDoor')->updateEquipInfo($eqInfo)) {
                            Ret(array('code' => 2, 'info' => '更新设备信息失败！'));
                        }
                    }
                }
                //将绑定到该商铺的教室或者店铺清除掉
                unbondShop($shopInfo[0]['owner_id'],$shopInfo[0]['ownertype_id']);
                //删除店铺信息
                $result=$shopModel->delete_shop($id);
                if ($result) {
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
    //商铺详情
    public function shop_view_api(){
        before_api();
        checkLogin();
        checkAuth();
        $mall_id=session('mall.mall_id');
        if(!$mall_id){
            Ret(array('code' => 2, 'info' => '登录数据获取失败！'));
        }
        $id=I('id');
        if(!$id){
            Ret(array('code' => 2, 'info' => '商铺数据获取失败！'));
        }
        $shopModel=D('shop');
        //$fields=array('id,position,type,equipment_id,area,rent_rate,property_price,submitter,eqip');
        $data=$shopModel->view_shop($id);
        if($data){
            $type = $this->get_shop_type($data[0]['type']);
            $data[0]['type']=$type['name'];
            $coun=$this->getEqIpByPost($data[0]['position']);
//            var_dump($coun);die;
            $data[0]['equipment_id']=$coun;
            Ret(array('code'=>1,'data'=>$data[0]));
        }else{
            Ret(array('code'=>2,'info'=>'没有数据！'));
        }
    }
    private function getEqIpByPost($position){
        if($position){
            $condition['position']=$position;
            $eqinfo = D('EquipmentDoor')->where($condition)->field('equip_ip')->find();
//            var_dump($eqinfo);die;
            return $eqinfo['equip_ip'];
        }

}
    private function getEqIpByPos($equip_id){
        if($equip_id){
            $condition['equip_id']=$equip_id;
            $eqinfo = D('EquipmentDoor')->where($condition)->field('equip_ip')->find();
            return $eqinfo['equip_ip'];
        }

    }

    //获取商铺类型
    public function get_type(){
        before_api();
        $a1=array('id'=>'1','name'=>'商户');
        $a2=array('id'=>'2','name'=>'创业工位');
        $a3=array('id'=>'3','name'=>'机构');
        $type = array($a1, $a2,$a3);

        Ret(array('code'=>1,'data'=>$type));
    }
    public function get_shop_type($type_id){
        $a1=array('id'=>1,'name'=>'商户');
        $a2=array('id'=>2,'name'=>'创业工位');
        $a3=array('id'=>3,'name'=>'机构');

        $type = array('1' => $a1, '2' => $a2,'3' => $a3);

        return isset($type[$type_id]) ? $type[$type_id] : '';

    }

    /*
     * 商铺信息审核
     */
    public function shop_check_api(){
        before_api();
        checkAuth();
        checkLogin();
        $data['id'] = I('id');
        $data['state'] = I('state');
        if(intval($data['id']) <1 || intval($data['state']) <0){
            Ret(array('code' => 2, 'info' => '参数有误!'));
        }
        //step 2: 根据主键id 获取需要的字段信息
        $shopModel=D('Shop');
        //step 3 ；修改审核表状态
        $editInfo = $shopModel->updateState($data);
        if($editInfo){
            $info = $shopModel->getAllField($data['id']);
            foreach ($info as $key => $value) {
                $state=get_state($value['state']);
                $info[$key]['state']=$state;
            }
            //step:4 保存日志
            $log = array(
                'check_time' => date('Y-m-d H:i:s'),
                'shop_position' => $info[0]['position'],
                'check_type' => $info[0]['check_type'],
                'submitter' => $info[0]['submitter'],
                'checker' => session('worker_name'),
                'check_state' => $info[0]['state'],
                'shop_id'=> $info[0]['id']
            );
            if($log['check_type']==0){
                $log['check_type']='商铺信息新增';
            }
            if($log['check_type']==4){
                $log['check_type']='商铺信息修改';
            }
            $ShopCheckLogModel=D('ShopCheckLog');
            if($ShopCheckLogModel->insertLog($log)){
                Ret(array('code'=>1,'data'=>'审核成功'));
            }
        }else {
            Ret(array('code'=>2,'info'=>'审核失败'));
        }



//        $data['id'] = I('id');
//        if ($data['id'] < 1) {
//            Ret(array('code' => 2, 'info' => '参数（id）有误!'));
//        }
//        $data['worker_id'] = session('worker_id');
//        $logdata['shop_position'] = I('position');
//        if (!$logdata['shop_position']) {
//            Ret(array('code' => 2, 'info' => '参数（shop_position）有误!'));
//        }
//        $logdata['check_type'] = I('check_type');
//        if($logdata['check_type']==0){
//            $logdata['check_type']='商铺信息新增';
//        }
//        if($logdata['check_type']==4){
//            $logdata['check_type']='商铺信息修改';
//        }
//        $logdata['submitter'] = I('submitter');
//        if (!$logdata['submitter'] ) {
//            Ret(array('code' => 2, 'info' => '参数（submitter）有误!'));
//        }
//        $logdata['checker'] = session('worker_id');
//        $data['state'] = I('state');
//        if ($data['state'] < 1) {
//            Ret(array('code' => 2, 'info' => '参数（state）有误!'));
//        }
//        if($data['state']==1){
//            $logdata['check_state']='通过';
//        }
//        if($data['state']==2){
//            $logdata['check_state']='未通过';
//        }
//        $logdata['check_time'] = date('Y-m-d H:i:s');
//        $shopModel=D('Shop');
//        $shopModel->startTrans();//开启事务
//        $ShopCheckLogModel=D('ShopCheckLog');
//        $log=$ShopCheckLogModel->add_log($logdata);
//        if($log){
//            $result=$shopModel->check($data);
//            $ShopCheckLogModel->commit();//日志保存成功，提交
//            if($result){
//                Ret(array('code' => 1, 'info' => '审核成功'));
//            }else{
//                Ret(array('code' => 2, 'info' => '审核失败'));
//            }
//        }else{
//            $ShopCheckLogModel->rollback();//日志保存失败，回滚
//            Ret(array('code' => 2, 'info' => '审核失败'));
//        }
    }


    /*
     * 商铺信息审核列表
     */
    public function shop_check_list(){
        before_api();
        checkAuth();
        checkLogin();
        $data['id'] = I('id');
        if ($data['id'] < 1) {
            Ret(array('code' => 2, 'info' => '参数（id）有误!'));
        }
        $data['state'] = I('state');
        if ($data['state'] < 1) {
            Ret(array('code' => 2, 'info' => '参数（state）有误!'));
        }
        $roomCheckoutModel = D('Shop');
        $res= $roomCheckoutModel->save($data);
        if($res){
            Ret(array('code' => 1, 'info' => '保存成功'));
        }else{
            Ret(array('code' => 2, 'info' => '保存失败'));
        }
    }
//商铺日志
    public function shop_check_log(){
        before_api();
        checkLogin();
        checkAuth();
        $keyword = I('keyword');

        $page = I('page',1,'intval');
        $pagesize = I('pagesize',10,'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page.','.$pagesize;

        $shopCheckLog = D('ShopCheckLog');
       $count =$shopCheckLog->getCount($keyword);
        $res =$shopCheckLog->getList($keyword,$page);
        if($res){
            Ret(array('code'=>1,'data'=>$res,'total'=>$count,'page_count'=>ceil($count/$pagesize)));
        }else{
            Ret(array('code'=>2,'info'=>'没有数据！'));
        }
    }
// 门禁设备
    public function getEq(){
        before_api();
        checkLogin();

        $mall_id=session('mall.mall_id');
        $shopModel=D('EquipmentDoor');
        $fields = array('equip_id,position');
        $res=$shopModel->field($fields)->select();
        if($res){
            foreach($res as $k => $v){
                $re[$k]['name']=$v['position'];
                $re[$k]['id']=$v['equip_id'];
            }
            Ret(array('code'=>1,'data'=>$re));
        }else{
            Ret(array('code'=>2,'info'=>'没有数据！'));
        }

    }

//商铺列表
    public function getShopList(){
        before_api();
        checkLogin();
        checkAuth();
        $mall_id=session('mall.mall_id');
        $shopModel=D('shop');
        $keyword = I('keyword');
        $page = I('page',1,'intval');
        $pagesize = I('pagesize',10,'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page.','.$pagesize;
        $count = $shopModel->get_count($mall_id,$keyword);
        $res=$shopModel->get_list($mall_id,$keyword, $page,$fields = null);

        if(!$res){
            Ret(array('code'=>2,'info'=>'没有数据！'));
        }else{
            foreach ($res as $key => $value) {
                $type = $this->get_shop_type($value['type']);
                $res[$key]['type']=$type['name'];
                $res[$key]['state']=get_state('state');
            }
            Ret(array('code'=>1,'data'=>$res,'total' => $count, 'page_count' => ceil($count/$pagesize)));
        }
    }

    /*private function addEquipmentBond($shopPosition,$eqId,$sdata,$shopId){
        if($shopPosition && $eqId){
            $data['equip_id']=$eqId;
            $data['position']=$shopPosition;
            $data['equip_type']='门禁设备';
            $data['owner_type']=$sdata['type'];
            $data['owner_name']=$shopPosition;
            $data['bond_state']=1;
            $data['shop_id']=$shopId;
            D('EquipmentDoor')->where('equip_id='.$eqId) ->save($data);
        }
    }*/


}