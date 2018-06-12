<?php
namespace AppParents\Controller;
use Think\Controller;
class CardController extends Controller{


    /*
   * app查看会员卡余额
   */

    public function app_member_balance(){
//        before_api();
        checkLogin();
        checkAuth();
        $member_id = I('member_id');
        $data=D('Member')->get_balance($member_id);
        if($data){
            Ret(array('code'=>1,'data'=>$data[0]));
        }else{
            Ret(array('code'=>2,'info'=>'没有数据！'));
        }
    }










    //卡片激活
//    public function card_activity_api(){
//        before_api();
//        checkLogin();
//        checkAuth();
//        $data['card_number']=I('card_number','');
//        if($data['card_number']==null){
//            Ret(array('code'=>2,'info'=>'卡片信息获取失败'));
//        }
//       $cardModel=D('Card');
//       $result=$cardModel->active($data['card_number']);
//
//        if($result){
//            $condition['card_number']=$data['card_number'];
//            $data= $cardModel->where($condition)->find();
//
//            $fields=array('name,pic');
//            $memberinfo=D('Member')->get_member_infos($data['id'],$fields);
//            $member['pic']=$memberinfo[0]['pic'];
//            $member['member_name']=$memberinfo[0]['name'];
//            $member['card_number']=$data['card_number'];
//            Ret(array('code'=>1,'data'=>$member,'info'=>'卡片激活成功！'));
//        }else{
//            Ret(array('code'=>2,'info'=>'卡片信息激活失败'));
//        }
//    }
//
////充值详情
//    public function recharge_view_api(){
//        before_api();
//        checkLogin();
//        checkAuth();
//        $page = I('page',1,'intval');
//
//        $pagesize = I('pagesize',10,'intval');
//        $pagesize = $pagesize < 1 ? 1 : $pagesize;
//        $pagesize = $pagesize > 50 ? 50 : $pagesize;
//        $page = $page.','.$pagesize;
//
//        $rechargeModel=D('CardRecharge');
//        $count=$rechargeModel->get_recharge_count();
//        $data=$rechargeModel->get_recharge_list($page);
//        $memberModel=D('Member');
//        foreach($data as $k=>$v){
//            $fields=array('name,pic');
//            $memberinfo=$memberModel->get_member_infos($v['member_id'],$fields);
//            $data[$k]['pic']=$memberinfo[0]['pic'];
//            $data[$k]['member_name']=$memberinfo[0]['name'];
//        }
//        if($data){
//            Ret(array('code'=>1,'data'=>$data,'total' => $count, 'page_count' => ceil($count/$pagesize)));
//        }else{
//            Ret(array('code'=>2,'info'=>'没有数据！'));
//        }
//
//    }
//
////充值查询
//    public function recharge_search_api(){
//        //checkLogin();
//        //ckeckAuth();
//        //$mall_id = session('mall.mall_id');
//        $mall_id =2;
//        $rechargeModel=D('CardRecharge');
//
//        $keyword['member_id']=I('member_id','');
//        if($keyword['member_id']==null){
//            Ret(array('code'=>2,'info'=>'请输入会员号！'));
//        }
//        $keyword['begintime']=I('begintime','');
//        if($keyword['begintime']==null){
//            Ret(array('code'=>2,'info'=>'请选择开始时间！'));
//        }
//        $keyword['endtime']=I('endtime','');
//        if($keyword['endtime']==null){
//            Ret(array('code'=>2,'info'=>'请选择结束时间！'));
//        }
//
//        $data= $rechargeModel->get_recharge_by_keyword($mall_id,$keyword);
//
//        if($data){
//            Ret(array('code'=>1,'data'=>$data));
//        }else{
//            Ret(array('code'=>2,'info'=>'没有数据！'));
//        }
//
//    }
//
//
//    /*
//        * 会员卡挂失（列表）
//        */
//    public function card_lost_record(){
//        before_api();
//        checkLogin();
//        checkAuth();
//        $keyword=I('keyword');
//        $page = I('page',1,'intval');
//        $pagesize = I('pagesize',10,'intval');
//        $pagesize = $pagesize < 1 ? 1 : $pagesize;
//        $pagesize = $pagesize > 50 ? 50 : $pagesize;
//        $page = $page.','.$pagesize;
//        $cordBondModel=D('Member');
//        $fields=array('id,pic,name,phone,card_number');
//        $count=$cordBondModel->get_count_card_lost($keyword);
//        $res = $cordBondModel->get_list_card_lost($keyword,$page,$fields);
//
//        if($res){
//            Ret(array('code'=>1,'data'=>$res,'total' => $count, 'page_count' => ceil($count/$pagesize)));
//        }else{
//            Ret(array('code'=>2,'info'=>'没有数据！'));
//        }
//
//    }
//
//
//
//
//    /*
//     * 会员卡挂失
//     */
//
//    public function card_lost_set(){
//        before_api();
//        checkLogin();
//        checkAuth();
//        $data['card_number']=I('card_number');
//        $data['	cardState']=3;
//        if(!$data['card_number']){
//            Ret(array('code'=>2,'info'=>'数据获取（card_number）失败！'));
//        }
//        $condition['card_number']=$data['card_number'];
//        $res=D('Member')->where($condition)->save($data);
//        if($res){
//            Ret(array('code'=>1,'info'=>'挂失成功！'));
//        }else{
//            Ret(array('code'=>2,'info'=>'数据保存失败，挂失失败！'));
//        }
//
//
//    }
///*
// * 补卡
// */
//    public function card_markup(){
//        before_api();
//        checkLogin();
//        checkAuth();
//        $data['card_number']=I('card_number');
//        if($data['card_number']==null){
//            Ret(array('code'=>2,'info'=>'参数（card_number）有误！'));
//        }
//        $data['id']=I('member_id');
//        if($data['id']==null){
//            Ret(array('code'=>2,'info'=>'参数（member_id）有误！'));
//        }
//        $data['	cardState']=0;
//        $res=D('Member')->add($data);
//        if($res){
//            Ret(array('code'=>1,'info'=>'补卡成功！'));
//        }else{
//            Ret(array('code'=>2,'info'=>'数据保存失败，补卡失败！'));
//        }
//    }
//
//    /*
//     * 检验当前会员是否还有有效卡
//     */
//    public function check_member_card(){
//        before_api();
//        checkLogin();
//        checkAuth();
//
//        $member_id=I('member_id');
//        if(!$member_id){
//            Ret(array('code'=>2,'info'=>'参数（member_id）有误！'));
//        }
//        //检查当前用户是否还有有效卡
//        $check=D('Member')->check_member_card($member_id);
//        if($check){
//            Ret(array('code'=>2,'info'=>'当前用户还有有效卡(卡号：'.$check[0]['card_number'].')，请在挂失后，再进行补卡！','card_number'=>$check[0]['card_number']));
//        }
//        if(!$check){
//            Ret(array('code'=>1,'info'=>'可以补卡！'));
//        }
//    }
//
//
//    /*
//     * 有卡充值(信息查询）
//     */
//    public function incharge_by_card(){
//        before_api();
//        checkLogin();
//        checkAuth();
//        $data['card_number']=I('card_number');
//        $member=$this->get_member_by_card($data['card_number']);
//        if($member){
//            Ret(array('code'=>1,'data'=>$member));
//        }else{
//            Ret(array('code'=>2,'info'=>'用户信息获取失败！'));
//        }
//
//    }
//
//    /*
//    * 无卡充值(信息查询）
//    */
//    public function incharge_by_noncard(){
//        before_api();
//        checkLogin();
//        checkAuth();
//        $keyword=I('keyword');
//        $cordBondModel=D('Member');
//        if(preg_match("/[\x7f-\xff]/", $keyword)){
//            $member=$this->get_member_by_name($keyword);
//            if($member){
//                Ret(array('code'=>1,'data'=>$member));
//            }else{
//                Ret(array('code'=>2,'info'=>'用户信息获取失败！'));
//
//            }
//        }
//        if((is_numeric($keyword) && strlen($keyword)==18)||(is_numeric($keyword) && strlen($keyword)==15)){
//            $member=$this->get_member_by_IDnumber($keyword);
//            $data['pic']=$member[0]['pic'];
//            $data['phone']=$member[0]['phone'];
//            $data['name']=$member[0]['name'];
//            $data['id']=$member[0]['id'];
//            if($data){
//                Ret(array('code'=>1,'data'=>$data));
//            }else{
//                Ret(array('code'=>2,'info'=>'用户信息获取失败！'));
//            }
//        }
//
//        if((is_numeric($keyword) && strlen($keyword)!=18)||(is_numeric($keyword) && strlen($keyword)!=15)){
//            $card=$this->get_card_by_number($keyword);
//            $member_id=$card[0]['pid'];
//            $data=$this->get_member_by_id($member_id);
//            if($data){
//                Ret(array('code'=>1,'data'=>$data));
//            }else{
//                Ret(array('code'=>2,'info'=>'用户信息获取失败！'));
//            }
//        }
//
//
//    }
//
//    private function get_member_by_IDnumber($idnumber){
//        if($idnumber){
//            $fields=array('id,pic,name,phone');
//            $condition['id_number']=$idnumber;
//            return D('Member')->field($fields)->where($condition)->select();
//        }
//    }
//
//    private function get_member_by_name($name){
//        if($name){
//            $fields=array('id,pic,name,phone');
//            $condition['name']=$name;
//            return D('Member')->field($fields)->where($condition)->select();
//        }
//    }
//
//    private function get_card_by_number($keyword){
//        $key['card_number'] = $keyword;
//        $key['id'] = $keyword;
//        $key['_logic'] = 'OR';
//        $condition['_complex'] = $key;
//        $condition['cardState'] = array('in','0,1');
//        return D('Member')->where($condition)->select();
//
//    }
//
//
//    /*
//     * 充值
//     */
//    public function charge_api(){
//        before_api();
//        checkLogin();
//        checkAuth();
//        $data['card_number']=I('card_number');
//        $data['member_id']=I('member_id');
//        if($data['member_id']==null){
//            Ret(array('code'=>2,'info'=>'参数（member_id）获取失败！'));
//        }
//        $data['member_name']=I('member_name');
//        if($data['member_name']==null){
//            Ret(array('code'=>2,'info'=>'参数（member_name）获取失败！'));
//        }
//        $data['recharge_monney']=I('recharge_money');
//        if($data['recharge_monney']==null){
//            Ret(array('code'=>2,'info'=>'参数（recharge_money）获取失败！'));
//        }
//        $data['time']=date('Y-m-d H:i:s');
//        $data['submitter_id']=session('worker_id');
//        $rechargeModel=D('CardRecharge');
//        $res=$rechargeModel->make_charge($data);
//        if($res){
//
//            $inoutdata['member_id']=$data['member_id'];
//            $inoutdata['income']=$data['recharge_monney'];
//            $inoutdata['time']=date('Y-m-d H:i:s');
//            $in_out_come=D('InOutCome')->make_income($inoutdata);
//
//
//
//            Ret(array('code'=>1,'info'=>'充值成功！'));
//        }else{
//            Ret(array('code'=>2,'info'=>'充值失败！'));
//        }
//
//
//    }
//
//    /*
//    * 验证卡号是否有效
//    */
//    public function card_check($car_number){
//        before_api();
//        if($car_number){
//            $condition['card_number']=$car_number;
//            $condition['cardState']=0;
//            return D('Member')->where($condition)->select();
//        }
//    }
//
//    /*
//     * 绑定卡片时检验卡片是否有效
//     */
//    public function check_card(){
//        before_api();
//        $condition['card_number']=I('card_number');
//        $res=D('Member')->where($condition)->count();
//        if($res>=1){
//            Ret(array('code'=>2,'info'=>'此卡无效！'));
//        }else{
//            Ret(array('code'=>1,'info'=>'此卡有效！'));
//        }
//
//    }
//
//    private function get_member_by_id($member_id){
//        if($member_id){
//            $condition['id']=$member_id;
//            return D('Member')->where($condition)->field('id,name,pic,phone')->find();
//        }
//
//    }
//
//    private function get_card_by_member($member_id){
//        if($member_id){
//            $condition['id']=$member_id;
//            $condition['cardState']=array('in','0,1');
//            return D('Member')->where($condition)->field('card_number')->select();
//        }
//    }
//    private function get_member_by_card($card_member){
//        if($card_member){
//            $condition['card_number']=$card_member;
//            $condition['cardState'] = 1;
//            return D('Member')->where($condition)->field('id,pic,name,phone')->find();
//        }
//    }
//
//
//    /*
//     * 退款
//     */
//    public function reback_money(){
//        before_api();
//        checkLogin();
//        checkAuth();
//
//        $data['id']=I('id');
//        if($data['id']==null){
//            Ret(array('code'=>2,'info'=>'参数（id）获取失败！'));
//        }
//        $data['reback_money']=I('money');
//        if($data['reback_money']==null){
//            Ret(array('code'=>2,'info'=>'参数（money）获取失败！'));
//        }
//        $data['reback_submitter']=session('worker_id');
//        $data['reback_time']=date('Y-m-d H:i:s');
//        $data['reback_state']=0;
//        $res=D('Consume')->make_reback($data);
//        if($res){
//            Ret(array('code'=>1,'info'=>'退款保存成功，请等待审核通过后返款！'));
//        }else{
//            Ret(array('code'=>2,'info'=>'退款保存失败，系统错误！'));
//        }
//    }
//
//
//    /*
//     * 退款审核（列表）
//     */
//    public function reback_money_check_list(){
//        before_api();
//        checkLogin();
//        checkAuth();
//
//        $page = I('page',1,'intval');
//        $pagesize = I('pagesize',10,'intval');
//        $pagesize = $pagesize < 1 ? 1 : $pagesize;
//        $pagesize = $pagesize > 50 ? 50 : $pagesize;
//        $page = $page.','.$pagesize;
//
//        $count=D('Consume')->check_count();
//        $fields=array('id,member_id,time,price,reback_submitter_id,content');
//        $data=D('Consume')->check_list($page,$fields);
//
//        if($data){
//            foreach($data as $k => $v){
//                $fieldss=array('name,id,pic');
//                $member=D('member')->get_member_infos($v['member_id'],$fieldss);
//                $data[$k]['member_name']=$member[0]['name'];
//                $data[$k]['pic']=$member[0]['pic'];
//                $submitter=D('member')->get_member_infos($v['reback_submitter_id'],$fieldss);
//                $data[$k]['submitter']=$submitter[0]['name'];
//            }
//            Ret(array('code'=>1,'data'=>$data,'total' => $count, 'page_count' => ceil($count/$pagesize)));
//        }else{
//            Ret(array('code'=>2,'info'=>'退款保存失败，系统错误！'));
//        }
//
//    }
//    /*
//     * 退款审核
//     */
//    public function reback_money_check(){
//        before_api();
//        checkLogin();
//        checkAuth();
//        $data['id']=I('id');
//        if($data['id']==null){
//            Ret(array('code'=>2,'info'=>'参数（id）获取失败！'));
//        }
//        $data['reback_state']=I('check_state');
//        if($data['reback_state']==null){
//            Ret(array('code'=>2,'info'=>'参数（check_state）获取失败！'));
//        }
//        $data['reback_chacker_id']=session('worker_id');
//        $data['reback_chacker_time']=date('Y-m-d H:i:s');
//        if($data){
//            $check=D('Consume')->save($data);
//
//            $rebackinfo = D('Consume')->where('id='.$data['id'])->find();
//            $rebackinfo['money']=$rebackinfo['count']*$rebackinfo['price'];
//            $inoutdata['member_id']=$rebackinfo['member_id'];
//            $inoutdata['income']=$rebackinfo['money'];
//            $inoutdata['time']=date('Y-m-d H:i:s');
//            $in_out_come=D('InOutCome')->make_income($inoutdata);
//
//            if($check){
//                Ret(array('code'=>1,'info'=>'审核成功！'));
//            }else{
//                Ret(array('code'=>2,'info'=>'审核失败，系统错误！'));
//            }
//        }
//    }
//
//    /*
//     * 退款列表
//     */
//
//    public function reback_list(){
//        before_api();
//        checkLogin();
//        checkAuth();
//        $page = I('page',1,'intval');
//
//        $pagesize = I('pagesize',10,'intval');
//        $pagesize = $pagesize < 1 ? 1 : $pagesize;
//        $pagesize = $pagesize > 50 ? 50 : $pagesize;
//        $page = $page.','.$pagesize;
//
//        $card_number = I('keyword');
//        $member_id=$this->get_member_by_card($card_number);
//        $consumeModel=D('Consume');
//        $count=$consumeModel->get_consume_reback_cout($member_id[0]['pid']);
//
//        $data=$consumeModel->get_consume_reback_list($member_id[0]['pid'],$page);
//        $memberModel=D('Member');
//        foreach($data as $k=>$v){
//            $fields=array('pic,name');
//            $memberinfo=$memberModel->get_member_infos($v['member_id'],$fields);
//            $data[$k]['pic']=$memberinfo[0]['pic'];
//            $data[$k]['member_name']=$memberinfo[0]['name'];
//        }
//        if($data){
//            Ret(array('code'=>1,'data'=>$data,'total' => $count, 'page_count' => ceil($count/$pagesize)));
//        }else{
//            Ret(array('code'=>2,'info'=>'没有数据！'));
//        }
//
//    }
//
//    /*
//     * 余额查询
//     */
//    public function balance(){
//        before_api();
//        checkLogin();
//        checkAuth();
//        $card_number=I('card_number');
//        $member=$this->get_member_by_card($card_number);
//        $member_id=$member['pid'];
//        $data=D('InOutCome')->get_balance($member_id);
//        if($data){
//            Ret(array('code'=>1,'data'=>$data));
//        }else{
//            Ret(array('code'=>2,'info'=>'没有数据！'));
//        }
//    }
//
//    /*
//     * 结账
//     */
//    public function checkout(){
//        before_api();
//        checkLogin();
//        checkAuth();
//        $data['count']=I('total');
//        $data['card_number']=I('card_number');
//        $data['detail']=json_encode($_POST['detail']);
//        $member=$this->get_member_by_card($data['card_number']);
//        if(!$member){
//            Ret(array('code'=>2,'info'=>'卡片无效，或未激活！'));
//        }
//        $data['member_id']=$member["pid"];
//        $data['content']='购物';
//        $data['list']=I('list');
//        $data['time']=date('Y-m-d H:m:s');
//        $data['mall_id']=session('mall_id');
//        if($data){
//            $res = D('Consume')->make_consume($data);
//
//            $inoutdata['member_id']=$data['member_id'];
//            $inoutdata['outcome']=$data['count'];
//            $inoutdata['time']=date('Y-m-d H:i:s');
//            $in_out_come=D('InOutCome')->make_outcome($inoutdata);
//            if($res){
//                Ret(array('code'=>1,'info'=>'结账成功'));
//            }else{
//                Ret(array('code'=>2,'info'=>'结账数据保存失败，系统错误！'));
//            }
//        }
//
//    }




    //卡片二次激活
//    public function makeSecCarAct(){
//        before_api();
//        checkLogin();
//        checkAuth();
//        $instID = session('inst.institution_id',1);
//
//        $carNo = I('card_number');
//
//        if(!$carNo){
//            Ret(array('code'=>2,'info'=>'参数(card_number)错误！'));
//        }
//        $memInfo = $this->getMemIDByCard($carNo);
//        $courInfo = $this ->getCourseByMemID($memInfo['id']);
//        foreach($courInfo as $k => $v){
//            //格式：eqNO,cardNO,Pin,name,starttime,endtime#timeID]week0,timezone1,timezone2,timezone3...;week1,timezone1,timezone2,timezone3..;.\r\n   you to me
//           // 例子：12345,23456,2001,locost,20170730,20170731#23]1,9:30-11:00,10:30-12:00,16:30-18:00;2,9:30-11:00,10:30-12:00,16:30-18:00\r\n
//            $time = $this->getDoorTimeByCourse($v['course_id']);
//            $roomId= $time['room_id'];
//            if($roomId){
//                $eqNo = D('RoomNumber')->field('equip_ip')->where('id='.$roomId)->find();
//            }
//            $t1 = $time['start_time'];
//            $t2 = $time['end_time'];
//
//            $data['eqIp'] = $eqNo['equip_ip'];
//            $data['cardNo'] = $carNo;
//            $data['memberId'] = 's'.$memInfo['id'];
//            $data['memberName'] = $memInfo['name'];
//            $data['time'] = date('Ymd',strtotime($t1)).','.date('Ymd',strtotime($t2)).'#'.rand(1,1000).']'.date('w',strtotime($t1)).','.date('H:i',strtotime($t1)).'-'.date('H:i',strtotime($t2));
//
//            if($data){
//                $res=D('MemberDoorAuth')->add($data);
//            }
//            if($res){
//                Ret(array('code'=>1,'info'=>'激活成功！'));
//            }else{
//                Ret(array('code'=>2,'info'=>'激活失败，系统错误！'));
//            }
//
//
//        }
//
//
//
//
//    }
//
//    private function getMemIDByCard($carNo){
//        if($carNo){
//           return D('Member')->field('id,name')->where('card_number='.$carNo)->find();
//        }
//    }
//
//    private function getCourseByMemID($memID){
//        if($memID){
//            $con['member_id'] = $memID;
//            return  D('CourseReserve')->where($con)->select();
//        }
//    }
//
//    private function getDoorTimeByCourse($cosID){
//        if($cosID){
//            $con['course_id'] = $cosID;
//            return D('CoursePlan')->where($con)->find();
//        }
//    }




}