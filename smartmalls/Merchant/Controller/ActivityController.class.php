<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/6/13
 * Time: 9:38
 */
namespace Merchant\Controller;
class ActivityController extends CommonController{
    public function index_api(){
        before_api();
        checkLogin();
        checkAuth();

        $mall_id =  session('mall.mall_id');
//        $institution_id = session('inst.institution_id');
//        $merchant_id = session('merchant.merchant_id');
//        if($mall_id){
//            $type = $mall_id;
//        }
//        if($institution_id){
//            $type = $institution_id;
//        }
//        if($institution_id){
//            $type = $merchant_id;
//        }
//
        $is_manager = true;
        if (!$is_manager) {
            $state = 2;
        }
        $keyword = I('keyword');
        $page = I('page',1,'intval');
        $pagesize = I('pagesize',10,'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page.','.$pagesize;
        $activityModel=D('Activity');
        $count=$activityModel->activity_count($mall_id,$state,$keyword);
        $fields=array('id,name,host_id,start_time,end_time,place,contact,phone,type');
        $res=$activityModel->activity_list($mall_id,$state,$keyword,$page,$fields);
        if($res){
            foreach ($res as $key => $value) {
                $type= $this->get_type($value['type']);
                $res[$key]['type']=$type;
                $host = $this->get_hostname_by_id($value['host_id'],$value['type']);
                $res[$key]['host']=$host[0]['name'];
            }
            Ret(array('code'=>1,'data'=>$res,'total' => $count, 'page_count' => ceil($count/$pagesize)));
        }else{
            Ret(array('code'=>2,'info'=>'没有活动信息'));

        }
    }
    //服务详情
    public function activity_view_api(){
        before_api();
        checkLogin();
        checkAuth();
        $id=I('id','');
        $activityModel=D('Activity');
        $data=$activityModel->activity_info($id);
        foreach ($data as $key => $value) {
            $type= $this->get_type($value['type']);
            $data[$key]['type']=$type;
            $data[$key]['type_id']=$value['type'];
            $host = $this->get_hostname_by_id($value['host_id'],$value['type']);
            $data[$key]['host']=$host[0]['name'];
        }
        if($data){
            Ret(array('code'=>1,'data'=>$data[0]));

        }else{
            Ret(array('code'=>2,'info'=>'没有活动信息'));
        }
    }

    public function activity_dml_api(){
        before_api();
        checkLogin();
        checkAuth();
        $flag = I('flag');
        $gongnengModel = D('Activity');
        // $mall_id = session('mall.mall_id');
        switch ($flag) {
            case 'add':
                $data['img']=I('img','');
                /*if($data['img']==null){
                    Ret(array('code'=>2,'info'=>'请上传活动图片'));
                }*/
                $data['type']=I('type_id',0);
                if($data['type']==null){
                    Ret(array('code'=>2,'info'=>'请选择单位类型'));
                }
                $data['name']=I('name','');
                if($data['name']==null){
                    Ret(array('code'=>2,'info'=>'请输入活动名称'));
                }
                $data['host_id']=I('host_id','');
                if($data['host_id']==null){
                    Ret(array('code'=>2,'info'=>'请输入筹办单位'));
                }
                $data['start_time']=I('start_time','');
                if($data['start_time']==null){
                    Ret(array('code'=>2,'info'=>'请选择开始时间'));
                }
                $data['end_time']=I('end_time','');
                if($data['end_time']==null){
                    Ret(array('code'=>2,'info'=>'请选择结束时间'));
                }
                $data['place']=I('place','');
                if($data['place']==null){
                    Ret(array('code'=>2,'info'=>'请输入活动地点'));
                }
                $data['contact']=I('contact','');
                if($data['contact']==null){
                    Ret(array('code'=>2,'info'=>'请输入活动联系人'));
                }
                $data['phone']=I('phone','');
                if($data['phone']==null){
                    Ret(array('code'=>2,'info'=>'请输入活动联系电话'));
                }
                $data['detail']=I('detail','');
                if($data['detail']==null){
                    Ret(array('code'=>2,'info'=>'请输入活动详情'));
                }
                $data['submit_time']=date("Y-m-d h:i:sa");;
                $data['state']=0;
                $data['check_type']=0;
                $data['submitter_id']=session('worker_id');
                $data['submitter']=session('worker_name');
                $result=$gongnengModel->add_activity($data);
                if($result){
                    Ret(array('code' => 1, 'info' => '保存成功!'));
                }else{
                    Ret(array('code' => 2, 'info' => '保存失败!'));
                }
                break;
            case update:
                $data['id']=I('id','');
                if($data['id']==null){
                    Ret(array('code'=>2,'info'=>'没有获取'));
                }
                $data['img']=I('img','');
               /* if($data['img']==null){
                    Ret(array('code'=>2,'info'=>'请上传活动图片'));
                }*/
                $data['type']=I('type_id','');
                if($data['type']==null){
                    Ret(array('code'=>2,'info'=>'请选择单位类型'));
                }
                $data['name']=I('name','');
                if($data['name']==null){
                    Ret(array('code'=>2,'info'=>'请输入活动名称'));
                }
                $data['host_id']=I('host_id','');
                if($data['host_id']==null){
                    Ret(array('code'=>2,'info'=>'请输入筹办单位'));
                }
                $data['start_time']=I('start_time','');
                if($data['start_time']==null){
                    Ret(array('code'=>2,'info'=>'请选择开始时间'));
                }
                $data['end_time']=I('end_time','');
                if($data['end_time']==null){
                    Ret(array('code'=>2,'info'=>'请选择结束时间'));
                }
                $data['place']=I('place','');
                if($data['place']==null){
                    Ret(array('code'=>2,'info'=>'请输入活动地点'));
                }
                $data['contact']=I('contact','');
                if($data['contact']==null){
                    Ret(array('code'=>2,'info'=>'请输入活动联系人'));
                }
                $data['phone']=I('phone','');
                if($data['phone']==null){
                    Ret(array('code'=>2,'info'=>'请输入活动联系电话'));
                }
                $data['detail']=I('detail','');
                if($data['detail']==null){
                    Ret(array('code'=>2,'info'=>'请选择结束时间'));
                }
                //$data['submitter']=$_SESSION['uid'];//提交人
                $data['submit_time']=date("Y-m-d h:i:sa");;
                $data['state']=0;
                $data['check_type']=4;
                $data['submitter']=session('worker_name');
                $data['submitter_id']=session('worker_id');
                $result=$gongnengModel->update_activity($data);
                if($result){
                    Ret(array('code' => 1, 'info' => '保存成功!'));
                }else{
                    Ret(array('code' => 2, 'info' => '保存失败!'));
                }
                break;
            case delete:
                $id = I('id', '');
                if ($id==null) {
                    Ret(array('code' => 2, 'info' => '获取id失败!'));
                }
                if ($gongnengModel->delete_activity($id)) {
                    Ret(array('code' => 1, 'info' => '删除成功!'));
                } else {
                    Ret(array('code' => 2, 'info' => '删除失败!'));
                }
                break;
            default:
                Ret(array('code' => 2, 'info' => 'Unknowed flag!'));
                break;
        }
    }

    private function get_type($type){
        $types=array('1'=>'机构','2'=>'商场','3'=>'商户');
        return isset($types[$type])?$types[$type]:'未知';
    }

    //获取筹办单位类别
    public function get_host_type(){
        before_api();
        $a1=array('id'=>'1','name'=>'机构');
        $a2=array('id'=>'2','name'=>'商场');
        $a3=array('id'=>'3','name'=>'商户');
        $type = array($a1, $a2,$a3);
        Ret(array('code'=>1,'data'=>$type));
    }
    //获取筹办单位
    public function get_host(){
        before_api();
        $type_id=I('type_id',3);
        switch($type_id){
            case 1:
                $inst=D('Inst')->get_institution();
                if(!$inst){
                    Ret(array('code'=>2,'info'=>'数据获取失败！'));
                }
                Ret(array('code'=>1,'data'=>$inst));
                break;
            case 2:
                $mall=D('Mall')->get_mall();
                if(!$mall){
                    Ret(array('code'=>2,'info'=>'数据获取失败！'));
                }
                Ret(array('code'=>1,'data'=>$mall));
                break;
            case 3:
                $merchant=D('Merchant')->getMerchant();
                if(!$merchant){
                    Ret(array('code'=>2,'info'=>'数据获取失败！'));
                }
                Ret(array('code'=>1,'data'=>$merchant));
                break;
        }
    }

    private function get_hostname_by_id($host_id,$type){
        switch($type){
            case 1:
                $instModel=D('Inst');
                return $instModel->get_institution_by_id($host_id);
                break;
            case 2:
                $instModel=D('Mall');
                return $instModel->get_mall_by_id($host_id);
                break;
            case 3:
                $instModel=D('Merchant');
                return $instModel->get_merchant_by_id($host_id);
                break;
        }
    }

    /*
     * 活动报名详情
     */
    public function activity_enroll_view_api(){

    }
    /*
    *
    *活动审核
    */
    public function activity_check_log(){
        before_api();
        checkAuth();
        checkLogin();


        $data['id'] = I('id');
        $data['state'] = I('state');
        if(intval($data['id']) <1 || intval($data['state']) <0){
            Ret(array('code' => 2, 'info' => '参数有误!'));
        }
        //step 2: 根据主键id 获取需要的字段信息
        $activityModel=D('Activity');
        //step 3 ；修改审核表状态
        $editInfo = $activityModel->updateState($data);
        if($editInfo){
            $info = $activityModel->getAllField($data['id']);
            foreach($info as $key =>$value){
                $info[$key]['state']=get_state($value['state']);
                $type= $this->get_type($value['type']);
                $info[$key]['type']=$type;
                $host = $this->get_hostname_by_id($value['host_id'],$value['type']);
                $info[$key]['host']=$host[0]['name'];
            }
            //step:4 保存日志

            $log = array(
                'check_time' => date('Y-m-d H:i:s'),
                'activity_name' => $info[0]['name'],
                'institution' => $info[0]['host'],
                'start_time' => $info[0]['start_time'],
                'end_time' => $info[0]['end_time'],
                'contact' => $info[0]['contact'],
                'hold_address' => $info[0]['place'],
                'phone' => $info[0]['phone'],
                'check_type' => $info[0]['check_type'],
                'submitter' => $info[0]['submitter'],
                'checker' => session('worker_name'),
                'check_state' => $info[0]['state'],
                'activity_id'=> $info[0]['id']
            );
            if($log['check_type']==0){
                $log['check_type']='活动信息新增';
            }
            if($log['check_type']==4){
                $log['check_type']='活动信息修改';
            }
            $activityCheckLogModel=D('ActivityCheckLog');
            if($activityCheckLogModel->insertLog($log)){
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
//        $logdata['activity_name'] = I('name');
//        if (!$logdata['activity_name']) {
//            Ret(array('code' => 2, 'info' => '参数（activity_name）有误!'));
//        }
//        $logdata['institution'] = I('host');
//        if (!$logdata['institution']) {
//            Ret(array('code' => 2, 'info' => '参数（institution）有误!'));
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
//            $logdata['check_type']='活动信息新增';
//        }
//        if($logdata['check_type']==4){
//            $logdata['check_type']='活动信息修改';
//        }
////        $logdata['checker'] = I('checker');
////        if (!$logdata['checker'] ) {
////            Ret(array('code' => 2, 'info' => '参数（checker）有误!'));
////        }
//        $logdata['start_time'] = I('start_time');
//        if (!$logdata['start_time'] ) {
//            Ret(array('code' => 2, 'info' => '参数（start_time）有误!'));
//        }
//        $logdata['end_time'] = I('end_time');
//        if (!$logdata['end_time'] ) {
//            Ret(array('end_time' => 2, 'info' => '参数（end_time）有误!'));
//        }
//        $logdata['activity_addr'] = I('place');
//        if (!$logdata['activity_addr'] ) {
//            Ret(array('code' => 2, 'info' => '参数（activity_addr）有误!'));
//        }
//        $logdata['contacter'] = I('contact');
//        if (!$logdata['contacter'] ) {
//            Ret(array('code' => 2, 'info' => '参数（contacter）有误!'));
//        }
//        $logdata['phone'] = I('phone');
//        if (!$logdata['phone'] ) {
//            Ret(array('code' => 2, 'info' => '参数（phone）有误!'));
//        }
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
//        $logdata['checker'] = session('worker_name');
//        $activityModel=D('Activity');
//        $activityModel->startTrans();//开启事务
//        $activityCheckLogModel=D('ActivityCheckLog');
//        $log=$activityCheckLogModel->add_log($logdata);
//        if($log){
//            $result=$activityModel->check($data);
//            $activityCheckLogModel->commit();//日志保存成功，提交
//            if($result){
//                Ret(array('code' => 1, 'info' => '审核成功'));
//            }else{
//                Ret(array('code' => 2, 'info' => '审核失败'));
//            }
//        }else{
//            $activityCheckLogModel->rollback();//日志保存失败，回滚
//            Ret(array('code' => 2, 'info' => '审核失败'));
//        }
    }
    /**
     *活动信息操作日志
     */
    public function activity_log(){
        before_api();
        checkAuth();
        checkLogin();
        $keyword= I('keyword');
        $page = I('page',1,'intval');
        $pagesize = I('pagesize',10,'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page.','.$pagesize;
        $activityCheckLogModel=D('ActivityCheckLog');
        $count = $activityCheckLogModel->get_count($keyword);
        if ($count) {
            $res = $activityCheckLogModel->get_list($keyword,$page,$fields=null);
            Ret(array('code' => 1, 'data' => $res, 'total' => $count, 'page_count' => ceil($count/$pagesize)));
        }else{
            Ret(array('code' => 2, 'info' => '查无相关数据！'));
        }
    }
    /**
     * 活动审核列表
     */
    public function activity_check_api(){
        before_api();
        checkAuth();
        checkLogin();
        $keyword= I('keyword');
        $page = I('page',1,'intval');
        $pagesize = I('pagesize',10,'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page.','.$pagesize;
        $activityModel=D('Activity');
        $count = $activityModel->get_count($keyword);
        $res = $activityModel ->get_list($keyword,$page,$fields=null);
        foreach($res as $key =>$value){
            $res[$key]['state']=get_state($value['state']);
            $type= $this->get_type($value['type']);
            $res[$key]['type']=$type;
            $host = $this->get_hostname_by_id($value['host_id'],$value['type']);
            $res[$key]['host']=$host[0]['name'];
            $res[$key]['nnn']='1';
        }
        if ($count) {
            Ret(array('code' => 1, 'data' => $res, 'total' => $count, 'page_count' => ceil($count/$pagesize)));
        }else{
            Ret(array('code' => 2, 'info' => '查无相关数据！'));
        }
    }
    /**
     * APP活动首页
     */
    public function app_activity_api()
    {
        before_api();
        checkLogin();
        checkAuth();
        $state = I('state', 0, 'intval');
        $is_manager = true;
        if ($state !== 2 && !$is_manager) {
            $state = 2;
        }
        $keyword = I('keyword');
        $page = I('page',1,'intval');
        $pagesize = I('pagesize',10,'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page.','.$pagesize;
        $activityModel = D('Activity');
        $instModel =D('Inst');
        $count = $activityModel->get_app_count($state,$keyword);
        $data = $activityModel->get_app_list($state,$keyword,$page, $fields=null);
        foreach($data as $key=>$item){
            $catname = $instModel->get_isnt_info($item['id']);
            $data[$key]['inst_name'] = $catname[0]['name'];
        }
        if ($data) {
            Ret(array('code' => 1, 'data' => $data, 'total' => $count,'page_count'=>ceil($count/$pagesize)));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据！'));
        }
    }
    /**
     * 活动详情
     */
    public function app_activity_view_api(){
        before_api();
        checkLogin();
        checkAuth();
        $id=I('id');
        $activityModel=D('Activity');
        $instModel = D('inst');
        $data=$activityModel->app_activity_info($id);
        if($data){
            $inst = $instModel->get_isnt_info($data[0]['id']);
            $data[0]['host_name']=$inst[0]['name'];
            Ret(array('code'=>1,'data'=>$data[0]));
        }else{
            Ret(array('code'=>2,'info'=>'没有活动信息'));
        }
    }
    /**
     * 活动报名
     */
    public function app_activity_reserve_api()
    {
        before_api();
        checkLogin();
        checkAuth();
        $activityReserveModel = D('ActivityReserve');
        $data['member_id'] = I('member_id');
        $data['institution_id'] = I('institution_id');
        $data['activity_id'] = I('activity_id');
        $data['time']=date('Y-m-d H:i:s');
        $result = $activityReserveModel->add_activity_reserve($data);
        if ($result) {
         Ret(array('code' => 1, 'info' => '报名已成功，请在我中查看报名详情!'));
        } else {
         Ret(array('code' => 2, 'info' => '报名失败!'));
        }
    }
    /**
     * 活动报名管理
     */
    public function reserveActivity(){
        before_api();
        checkLogin();
        checkAuth();
        $state = I('state', 2, 'intval');
        $is_manager = true;
        if ($state !== 2 && !$is_manager) {
            $state = 2;
        }
        $keyword = I('keyword');
        $start_time = I('start_time');
        $end_time = I('end_time');
        $page = I('page',1,'intval');
        $pagesize = I('pagesize',10,'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page.','.$pagesize;
        $model = D('Activity');
        $reserveModel=D('ActivityReserve');
        $count = $model->count_time($keyword,$start_time,$end_time);
        $res = $model->get_list_by_time($keyword, $start_time, $end_time,$page,$fields=null);
        $resData='';
        if ($res) {
            foreach ($res as $key => $value) {
                $resData[$key]['id']=$value['id'];
                $resData[$key]['name']=$value['name'];
                $resData[$key]['place']=$value['place'];
                $resData[$key]['contact']=$value['contact'];
                $resData[$key]['phone']=$value['phone'];
                $resData[$key]['start_time']=$value['start_time'];
                $resData[$key]['end_time']=$value['end_time'];
                $resData[$key]['acyivityReserve_number']=$reserveModel->get_activityReservceNumber($value['id']);
//                $resData[$key]['reserveRatio']=round( $resData[$key]['courseReserve_number']/$resData[$key]['max_member'] * 100 , 2) . "％";
                $host = $this->get_hostname_by_id($value['host_id'],$value['type']);
                $resData[$key]['host']=$host[0]['name'];
//                $res[$key]['reserveRatio']=$res[$key]['courseReserve_number']/$res[$key]['max_member'];
            }
            Ret(array('code' => 1, 'data' => $resData ,'total'=>$count ,'page_count' => ceil($count/$pagesize)));
        } else {
            Ret(array('code' => 2, 'info' => '没有活动报名信息'));
        }
    }
    /**
     * 活动报名详情
     */
    public function reserveActivityView(){
        before_api();
        checkLogin();
        checkAuth();
        $activity_id = I('id');
        $reserveModel=D('ActivityReserve');
        $memberModel = D('Member');
        $page = I('page',1,'intval');
        $pagesize = I('pagesize',10,'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page.','.$pagesize;
        $count =$reserveModel->get_activityReservceNumber($activity_id);
        $data=$reserveModel->get_list_by_activityId($activity_id,$page);
//        var_dump($data);die;
        foreach ($data as $key => $value) {
            $member =$memberModel->get_info($value['member_id']);
            $data[$key]['name'] =$member[0]['name'];
            $data[$key]['card_number'] =$member[0]['card_number'];
            $data[$key]['phone'] =$member[0]['phone'];
            $data[$key]['parent_phone'] =$member[0]['parent_phone'];
            $data[$key]['number'] =$member[0]['number'];
            $data[$key]['pic'] =$member[0]['pic'];
        }
        if($data){
            Ret(array('code'=>1,'data'=>$data,'total'=>$count,'page_count'=>ceil($count/$pagesize)));
        }else{
            Ret(array('code'=>2,'info'=>'没有数据'));
        }
    }


    public function activityList(){
        before_api();
        checkLogin();
        checkAuth();

//        $mall_id =  session('mall.mall_id');
        $institution_id = session('inst.institution_id');
//        $merchant_id = session('merchant.merchant_id');
//        if($mall_id){
//            $type = $mall_id;
//        }
//        if($institution_id){
//            $type = $institution_id;
//        }
//        if($institution_id){
//            $type = $merchant_id;
//        }
//
        $is_manager = true;
        if (!$is_manager) {
            $state = 2;
        }
        $keyword = I('keyword');
        $page = I('page',1,'intval');
        $pagesize = I('pagesize',10,'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page.','.$pagesize;
        $activityModel=D('Activity');
        $count=$activityModel->getCount($institution_id,$state,$keyword);
        $fields=array('id,name,host_id,start_time,end_time,place,contact,phone,type');
        $res=$activityModel->getList($institution_id,$state,$keyword,$page,$fields);
        if($res){
            foreach ($res as $key => $value) {
                $type= $this->get_type($value['type']);
                $res[$key]['type']=$type;
                $host = $this->get_hostname_by_id($value['host_id'],$value['type']);
                $res[$key]['host']=$host[0]['name'];
            }
            Ret(array('code'=>1,'data'=>$res,'total' => $count, 'page_count' => ceil($count/$pagesize)));
        }else{
            Ret(array('code'=>2,'info'=>'没有活动信息'));

        }
    }
}