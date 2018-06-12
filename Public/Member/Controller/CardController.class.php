<?php
namespace Member\Controller;
use Merchant\Controller\GoodsController;
use Think\Controller;
class CardController extends Controller{
    //卡片激活
    public function card_activity_api(){
        before_api();
        checkLogin();
        checkAuth();
        $data['card_number']=I('card_number','');
        if($data['card_number']==null){
            Ret(array('code'=>2,'info'=>'卡片信息获取失败'));
        }
       $cardModel=D('Card');
       $result=$cardModel->active($data['card_number']);
        if($result){
            $data=getCard($data['card_number']);
            $res['card_typeid'] =$data[0]['card_typeid'];
            $res['id'] =$data[0]['id'];
            $res['card_number'] =$data[0]['card_number'];
            if($res['card_typeid']==1){
                $memberinfo=getMember($res['card_number']);
                $member['pic']=$memberinfo[0]['pic'];
                $member['member_name']=$memberinfo[0]['name'];
                $member['card_number']=$res['card_number'];
            }
            if($res['card_typeid']==2){
                $memberinfo=getMall($res['card_number']);
                $member['pic']=$memberinfo[0]['pic'];
                $member['member_name']=$memberinfo[0]['name'];
                $member['card_number']=$res['card_number'];
            }
            if($res['card_typeid']==3){

                $memberinfo=getInst($res['card_number']);
                $member['pic']=$memberinfo[0]['pic'];
                $member['member_name']=$memberinfo[0]['name'];
                $member['card_number']=$res['card_number'];
            }
            if($res['card_typeid']==4){
                $memberinfo=getMerchant($res['card_number']);
                $member['pic']=$memberinfo[0]['pic'];
                $member['member_name']=$memberinfo[0]['name'];
                $member['card_number']=$res['card_number'];
            }
            Ret(array('code'=>1,'data'=>$member,'info'=>'卡片激活成功！'));
        }else{
            Ret(array('code' => 2, 'info' => '卡片信息激活失败'));
        }
    }

//充值详情
    public function recharge_view_api(){
        before_api();
        checkLogin();
        checkAuth();
        $page = I('page',1,'intval');

        $pagesize = I('pagesize',10,'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page.','.$pagesize;

        $rechargeModel=D('CardRecharge');
        $count=$rechargeModel->get_recharge_count();
        $data=$rechargeModel->get_recharge_list($page);
        $memberModel=D('Member');
        foreach($data as $k=>$v){
            $fields=array('name,pic');
            $memberinfo=$memberModel->get_member_infos($v['member_id'],$fields);
            $data[$k]['pic']=$memberinfo[0]['pic'];
            $data[$k]['member_name']=$memberinfo[0]['name'];
        }
        if($data){
            Ret(array('code'=>1,'data'=>$data,'total' => $count, 'page_count' => ceil($count/$pagesize)));
        }else{
            Ret(array('code'=>2,'info'=>'没有数据！'));
        }

    }

//充值查询
    public function recharge_search_api(){
        //checkLogin();
        //ckeckAuth();
        //$mall_id = session('mall.mall_id');
        $mall_id =2;
        $rechargeModel=D('CardRecharge');

        $keyword['member_id']=I('member_id','');
        if($keyword['member_id']==null){
            Ret(array('code'=>2,'info'=>'请输入会员号！'));
        }
        $keyword['begintime']=I('begintime','');
        if($keyword['begintime']==null){
            Ret(array('code'=>2,'info'=>'请选择开始时间！'));
        }
        $keyword['endtime']=I('endtime','');
        if($keyword['endtime']==null){
            Ret(array('code'=>2,'info'=>'请选择结束时间！'));
        }

        $data= $rechargeModel->get_recharge_by_keyword($mall_id,$keyword);

        if($data){
            Ret(array('code'=>1,'data'=>$data));
        }else{
            Ret(array('code'=>2,'info'=>'没有数据！'));
        }

    }


    /*
        * 会员卡挂失（列表）
        */
    public function card_lost_record(){
        before_api();
        checkLogin();
        checkAuth();
        $keyword=I('keyword');
        $page = I('page',1,'intval');
        $pagesize = I('pagesize',10,'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page.','.$pagesize;
        $cordBondModel=D('Member');
        $fields=array('id,pic,name,phone,card_number');
        $count=$cordBondModel->get_count_card_lost($keyword);
        $res = $cordBondModel->get_list_card_lost($keyword,$page,$fields);

        if($res){
            Ret(array('code'=>1,'data'=>$res,'total' => $count, 'page_count' => ceil($count/$pagesize)));
        }else{
            Ret(array('code'=>2,'info'=>'没有数据！'));
        }

    }




    /*
     * 会员卡挂失
     */

    public function card_lost_set(){
        before_api();
        checkLogin();
        checkAuth();
        $data['card_number']=I('card_number');
        if(!$data['card_number']){
            Ret(array('code'=>2,'info'=>'数据获取（card_number）失败！'));
        }
        $card=getCardIds( $data['card_number']);
        $data['id']=$card[0]['id'];
        $data['card_state']=3;
        $data['card_number']=$card[0]['card_number'];
        $res=D('Card')->save($data);
        if($res){
            Ret(array('code'=>1,'info'=>'挂失成功！'));
        }else{
            Ret(array('code'=>2,'info'=>'数据保存失败，挂失失败！'));
        }


    }
/*
 * 补卡
 */
    public function card_markup(){
        before_api();
        checkLogin();
        checkAuth();
        $data['card_number']=I('card_number');
        if($data['card_number']==null){
            Ret(array('code'=>2,'info'=>'参数（card_number）有误！'));
        }
        $data['member_id']=I('member_id');
       $member= getMemberCard($data['member_id']);
        $res['id']=$member[0]['id'];
        $res['card_number']=$data['card_number'];
        $res=D('Member')->save($res);
        if($res){
            $card=getCardId($data['member_id']);
            $cant['id']=$card[0]['id'];
            $cant['card_state']=$card[0]['card_state'];
            $cant['card_state']=0;
            $cant['card_number']=$data['card_number'];
            D('Card')->save($cant);
            Ret(array('code'=>1,'info'=>'补卡成功！'));
        }else{
            Ret(array('code'=>2,'info'=>'数据保存失败，补卡失败！'));
        }

    }

    /*
     * 检验当前会员是否还有有效卡
     */
    public function check_member_card(){
        before_api();
        checkLogin();
        checkAuth();

        $member_id=I('member_id');

        if(!$member_id){
            Ret(array('code'=>2,'info'=>'参数（member_id）有误！'));
        }
        //检查当前用户是否还有有效卡
        $check=D('Card')->check_member_card($member_id);
        if($check){
            Ret(array('code'=>2,'info'=>'当前用户还有有效卡(卡号：'.$check[0]['card_number'].')，请在挂失后，再进行补卡！','card_number'=>$check[0]['card_number']));
        }
        if(!$check){
            Ret(array('code'=>1,'info'=>'可以补卡！'));
        }
    }


    /*
     * 有卡充值(信息查询）
     */
    public function incharge_by_card(){
        before_api();
        checkLogin();
        checkAuth();
        $data['card_number']=I('card_number');
        $data=getCard($data['card_number']);
        $res['card_typeid'] =$data[0]['card_typeid'];
        $res['id'] =$data[0]['id'];
        $res['card_number'] =$data[0]['card_number'];
        $res['card_state'] =$data[0]['card_state'];

        if($res['card_typeid']==1 && $res['card_state']==1){
            $memberinfo=get_member_by_card($res['card_number']);
            $member['pic']=$memberinfo[0]['pic'];
            $member['name'] = $memberinfo[0]['name'];
            $member['phone'] = $memberinfo[0]['phone'];
            $member['card_number'] = $memberinfo[0]['card_number'];

        }
        if($res['card_typeid']==2 && $res['card_state']==1){
            $memberinfo= get_mall_by_card($res['card_number']);
            $member['pic']=$memberinfo[0]['pic'];
            $member['name'] = $memberinfo[0]['name'];
            $member['phone'] = $memberinfo[0]['phone'];
            $member['card_number'] = $memberinfo[0]['card_number'];
        }
        if($res['card_typeid']==3 && $res['card_state']==1){
            $memberinfo= get_inst_by_card($res['card_number']);
            $member['pic']=$memberinfo[0]['pic'];
            $member['name'] = $memberinfo[0]['name'];
            $member['phone'] = $memberinfo[0]['phone'];
            $member['card_number'] = $memberinfo[0]['card_number'];
        }
        if($res['card_typeid']==4 && $res['card_state']==1){
            $memberinfo= get_merchant_by_card($res['card_number']);
            $member['pic']=$memberinfo[0]['pic'];
            $member['name'] = $memberinfo[0]['name'];
            $member['phone'] = $memberinfo[0]['phone'];
            $member['card_number'] = $memberinfo[0]['card_number'];
        }
        if($member){
            Ret(array('code'=>1,'data'=>$member));
        }else{
            Ret(array('code'=>2,'info'=>'用户信息获取失败！'));
        }
    }

    /*
    * 无卡充值(信息查询）
    */
    public function incharge_by_noncard(){
        header("Content-Type: text/html; charset=utf8");
        $keyword=I('keyword');
        if(!$keyword){
            return false;
        }
        $res= get_member_card($keyword);
        $mall=get_mall_card($keyword);
        $inst=get_inst_card($keyword);
        $merchant=get_merchant_card($keyword);
        if($keyword=get_member_card($keyword)){
            $data['id'] = $res[0]['id'];
            $data['name'] = $res[0]['name'];
            $data['phone'] = $res[0]['phone'];
            $data['pic'] = $res[0]['pic'];
            if($data){
                Ret(array('code'=>1,'data'=>$data));
            }else{
                Ret(array('code'=>2,'info'=>'用户信息获取失败！'));
            }
        }elseif($keyword=get_mall_card($keyword)){
            foreach($mall as $k => $v){
                $data[$k] = $v;
            }

//            $data['id'] = $mall[0]['id'];
//            $data['name'] = $mall[0]['name'];
//            $data['phone'] = $mall[0]['phone'];
//            $data['pic'] = $mall[0]['pic'];
            if($data){
                Ret(array('code'=>1,'data'=>$data));
            }else{
                Ret(array('code'=>2,'info'=>'用户信息获取失败！'));
            }
        }elseif($keyword=get_inst_card($keyword)){

            $data['id'] = $inst[0]['id'];
            $data['name'] = $inst[0]['name'];
            $data['phone'] = $inst[0]['phone'];
            $data['pic'] = $inst[0]['pic'];
            if($data){
                Ret(array('code'=>1,'data'=>$data));
            }else{
                Ret(array('code'=>2,'info'=>'用户信息获取失败！'));
            }
        }elseif($keyword=get_merchant_card($keyword)){
            $data['id'] = $merchant[0]['id'];
            $data['name'] = $merchant[0]['name'];
            $data['phone'] = $merchant[0]['phone'];
            $data['pic'] = $merchant[0]['pic'];
            if($data){
                Ret(array('code'=>1,'data'=>$data));
            }else{
                Ret(array('code'=>2,'info'=>'用户信息获取失败！'));
            }
        }else{
            Ret(array('code'=>2,'info'=>'没有数据！'));
        }
//        $cordBondModel=D('Member');
//        if(preg_match("/[\x7f-\xff]/", $keyword)){
//            $member=$this->get_member_by_name($keyword);
//            if($member){
//                Ret(array('code'=>1,'data'=>$member));
//            }else{
//                Ret(array('code'=>2,'info'=>'用户信息获取失败！'));
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
//
//        }
    }

    private function get_member_by_IDnumber($idnumber){
        if($idnumber){
            $fields=array('id,pic,name,phone');
            $condition['id_number']=$idnumber;
            return D('Member')->field($fields)->where($condition)->select();
        }
    }

    private function get_member_by_name($name){
        if($name){
            $fields=array('id,pic,name,phone');
            $condition['name']=$name;
            return D('Member')->field($fields)->where($condition)->select();
        }
    }

    private function get_card_by_number($keyword){
        $key['card_number'] = $keyword;
        $key['id'] = $keyword;
        $key['_logic'] = 'OR';
        $condition['_complex'] = $key;
        $condition['cardState'] = array('in','0,1');
        return D('Member')->where($condition)->select();

    }


    /*
     * 充值
     */
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
//            $inoutdata['member_id']=$data['member_id'];
//            $inoutdata['income']=$data['recharge_monney'];
//            $inoutdata['time']=date('Y-m-d H:i:s');
//            $makebalance =D('InOutCome')->make_income($inoutdata);
//            if(!$makebalance){
//                $rechargeModel->where('id='.$res)->delete();
//                Ret(array('code'=>2,'info'=>'充值失败！'));
//            }
//            Ret(array('code'=>1,'info'=>'充值成功！'));
//        }else{
//            Ret(array('code'=>2,'info'=>'充值失败！'));
//        }
//
//
//    }

    /*
    * 验证卡号是否有效
    */
    public function card_check($car_number){
        before_api();
        if($car_number){
            $condition['card_number']=$car_number;
            $condition['cardState']=0;
            return D('Member')->where($condition)->select();
        }
    }

    /*
     * 绑定卡片时检验卡片是否有效
     */
    public function check_card(){
        before_api();
        $condition['card_number']=I('card_number');
        $res=D('Member')->where($condition)->count();
        if($res>=1){
            Ret(array('code'=>2,'info'=>'此卡无效！'));
        }else{
            Ret(array('code'=>1,'info'=>'此卡有效！'));
        }

    }

    private function get_member_by_id($member_id){
        if($member_id){
            $condition['id']=$member_id;
            return D('Member')->where($condition)->field('id,name,pic,phone')->find();
        }

    }

    private function get_card_by_member($member_id){
        if($member_id){
            $condition['id']=$member_id;
            $condition['cardState']=array('in','0,1');
            return D('Member')->where($condition)->field('card_number')->select();
        }
    }
    private function get_member_by_card($card_member){
        if($card_member){
            $condition['card_number']=$card_member;
            $condition['cardState'] = 1;
            $data = D('Member')->where($condition)->field('id,pic,name,phone,card_number')->find();
//            echo D('Member')->_sql();die;
            $data['member_id'] = $data['id'];
            $data['pic'] = $data['pic'];
            $data['name'] = $data['name'];
            $data['phone'] = $data['phone'];
            $data['card_number'] = $data['card_number'];
            return $data;
        }
    }


    /*
     * 退款
     */
    public function reback_money(){

        $data['id']=I('id');
        if($data['id']==null){
            Ret(array('code'=>2,'info'=>'参数（id）获取失败！'));
        }
        $expense=getExpense($data['id']); $card_typeid =$expense[0]['card_typeid'];
        $card_ownerid =$expense[0]['card_ownerid'];
        $member=getMembe($card_ownerid);
        $res['card_typeid'] =$card_typeid;
        $res['member_name'] =$member[0]['name'];
        $res['pic'] =$member[0]['pic'];
        $res['member_id'] =$card_ownerid;
        $res['price'] =$expense[0]['card_rechargenum'];
        $res['time'] =$expense[0]['card_rechargetime'];
        $res['content'] =$expense[0]['cost_type'];
        $res=D('Reimburse')->add($res);
        if($res){
            Ret(array('code'=>1,'info'=>'退款保存成功，请等待审核通过后返款！'));
        }else{
            Ret(array('code'=>2,'info'=>'退款保存失败，系统错误！'));
        }
    }


    /*
     * 退款审核（列表）
     */
    public function reback_money_check_list(){
        before_api();
        checkLogin();
        checkAuth();
        $page = I('page',1,'intval');
        $pagesize = I('pagesize',10,'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page.','.$pagesize;
        $count=D('Reimburse')->getCoun();
        $data=D('Reimburse')->getExpenseDetail($page);
        if($data){
            Ret(array('code'=>1,'data'=>$data,'total' => $count, 'page_count' => ceil($count/$pagesize)));
        }else{
            Ret(array('code'=>2,'info'=>'退款保存失败，系统错误！'));
        }
    }
    /*
     * 退款审核
     */
    public function reback_money_check()
    {
        $data['id'] = I('id');
        if ($data['id'] == null) {
            Ret(array('code' => 2, 'info' => '参数（id）获取失败！'));
        }
        $reimb = getReimbres($data['id']);
        $reimbInfo['member_id'] = $reimb[0]['member_id'];
        $reimbInfo['price'] = $reimb[0]['price'];
        $member = getMemb($reimbInfo['member_id']);
        $res['cardnumber_no'] = $member[0]['cardnumber_no'];
        $res['balance'] = $member[0]['balance'] + $reimbInfo['price'];
        $res['card_number'] = $member[0]['card_number'];
        $res['id'] = $member[0]['id'];
        $res['name'] = $member[0]['name'];
        if (updateMemberBalance($res)) {
            $cads['card_type'] = '会员';
            $cads['card_number'] = $res['card_number'];
            $cads['card_ownerid'] = $res['id'];
            $cads['recharge_typeid'] = 4;
            $cads['recharge_type'] = '课程退款';
            $cads['recharge_num'] =  $reimbInfo['price'];
            $cads['recharge_time'] = date('Y-m-d H:i:s');
            addFees($cads);
            $mallinfo['outcome_owenrtypeid'] = 3;
            $mallinfo['outcome_ownertype'] = '会员';
            $mallinfo['outcome_ownerid'] = $res['id'];
            $mallinfo['outcome_ownername'] = $res['name'];
            $mallinfo['outcome_typeid'] = 3;
            $mallinfo['outcome_type'] = '退订课程支出';
            $mallinfo['outcome_time'] = date('Y-m-d H:i:s');
            $mallinfo['outcome_num'] = $reimbInfo['price'];
            addMall($mallinfo);
        }
        if ($mallinfo) {
            D('Reimburse')->where('id='. $data['id'])->delete();
            Ret(array('code' => 1, 'info' => '审核成功！'));
        } else {
            Ret(array('code' => 2, 'info' => '审核失败，系统错误！'));
        }
    }


    /*
     * 退款列表
     */
    public function reback_list(){
        $page = I('page',1,'intval');
        $pagesize = I('pagesize',10,'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page.','.$pagesize;

        $card_number =I('keyword');
        $card=getCardNumber($card_number);
//        var_dump($card);die;
        $card_ownerid =$card[0]['card_ownerid'];
        $member=getMembe($card_ownerid);
        $expenseDetailModel =D('ExpenseDetail');
        $count=$expenseDetailModel->getCoun($card_ownerid);
        $data= $expenseDetailModel->getExpenseDetail($card_ownerid,$page);
        foreach($data as $key=>$value){
            $res[$key]['member_name']=$member[0]['name'];
            $res[$key]['pic']=$member[0]['pic'];
            $res[$key]['time']=$value['card_rechargetime'];
            $res[$key]['price']=$value['card_rechargenum'];
            $res[$key]['content']=$value['cost_type'];
            $res[$key]['id']=$value['id'];
        }
        if($res){
            Ret(array('code'=>1,'data'=>$res,'total'=>$count,'page_count' => ceil($count/$pagesize)));
        }else{
            Ret(array('code'=>2,'info'=>'没有数据'));
        }

    }

    /*
     * 余额查询
     */
    public function balance(){
        before_api();
        checkLogin();
        checkAuth();
        $card_number=I('card_number');
        $member=get_member($card_number);
        $mall=get_mall($card_number);
        $inst=get_inst($card_number);
        $merchant=get_merchant($card_number);
        if($card_number=$member){
            if($member){
                Ret(array('code'=>1,'data'=>$member[0]));
            }else{
                Ret(array('code'=>2,'info'=>'没有数据！'));
            }
        }
        if($card_number=$mall){
            if($mall){
                Ret(array('code'=>1,'data'=>$mall[0]));
            }else{
                Ret(array('code'=>2,'info'=>'没有数据！'));
            }
        }
        if($card_number=$inst){
            if($inst){
                Ret(array('code'=>1,'data'=>$inst[0]));
            }else{
                Ret(array('code'=>2,'info'=>'没有数据！'));
            }
        }
        if($card_number=$merchant){
            if($merchant){
                Ret(array('code'=>1,'data'=>$merchant[0]));
            }else{
                Ret(array('code'=>2,'info'=>'没有数据！'));
            }
        }

    }

    /*
     * 结账
     */
    public function checkoutList(){
        before_api();
        checkLogin();
        checkAuth();
        $data['count']=I('total');
        $data['card_number']=I('card_number');
        $data['detail']=json_encode($_POST['detail']);
        $member=$this->get_member_by_card($data['card_number']);
        if(!$member){
            Ret(array('code'=>2,'info'=>'卡片无效，或未激活！'));
        }
        $data['member_id']=$member["member_id"];
        $data['content']='购物';
        $data['list']=I('list');
        $data['time']=date('Y-m-d H:m:s');
        $data['mall_id']=session('mall_id');
        if($data){
            $res = D('Consume')->make_consume($data);

            $inoutdata['member_id']=$data['member_id'];
            $inoutdata['outcome']=$data['count'];
            $inoutdata['time']=date('Y-m-d H:i:s');
            $in_out_come=D('InOutCome')->make_outcome($inoutdata);


            $detail=$_POST['detail'];
            foreach($detail as $k => $v){
                $code = $v['code'];
                $count = $v['count'];
                D('Goods')->makeStoreModify($code,$count);
            }

            if(!$in_out_come){
                D('Consume')->where('id='.$res)->delete();
                Ret(array('code'=>2,'info'=>'结账数据保存失败，系统错误！'));
            }
            if($res){
                Ret(array('code'=>1,'info'=>'结账成功'));
            }else{
                Ret(array('code'=>2,'info'=>'结账数据保存失败，系统错误！'));
            }
        }

    }


    /*
     * app查看会员卡余额
     */

    public function app_member_balance(){
//        before_api();
        checkLogin();
        checkAuth();
        $member_id = I('member_id');
        $data=D('CardRecharge')->get_balance($member_id);
        if($data){
            Ret(array('code'=>1,'data'=>$data));
        }else{
            Ret(array('code'=>2,'info'=>'没有数据！'));
        }
    }

    //卡片二次激活
    public function makeSecCarAct(){
        before_api();
        checkLogin();
        checkAuth();
        $instID = session('inst.institution_id',1);

        $carNo = I('card_number');

        if(!$carNo){
            Ret(array('code'=>2,'info'=>'参数(card_number)错误！'));
        }
        $memInfo = $this->getMemIDByCard($carNo);
        $courInfo = $this ->getCourseByMemID($memInfo['id']);
        foreach($courInfo as $k => $v){
            //格式：eqNO,cardNO,Pin,name,starttime,endtime#timeID]week0,timezone1,timezone2,timezone3...;week1,timezone1,timezone2,timezone3..;.\r\n   you to me
           // 例子：12345,23456,2001,locost,20170730,20170731#23]1,9:30-11:00,10:30-12:00,16:30-18:00;2,9:30-11:00,10:30-12:00,16:30-18:00\r\n
            $time = $this->getDoorTimeByCourse($v['course_id']);
            $roomId= $time['room_id'];
            if($roomId){
                $eqNo = D('RoomNumber')->field('equip_ip')->where('id='.$roomId)->find();
            }
            $t1 = $time['start_time'];
            $t2 = $time['end_time'];

            $data['eqIp'] = $eqNo['equip_ip'];
            $data['cardNo'] = $carNo;
            $data['memberId'] = 's'.$memInfo['id'];
            $data['memberName'] = $memInfo['name'];
            $data['time'] = date('Ymd',strtotime($t1)).','.date('Ymd',strtotime($t2)).'#'.rand(1,1000).']'.date('w',strtotime($t1)).','.date('H:i',strtotime($t1)).'-'.date('H:i',strtotime($t2));

            if($data){
                $res=D('MemberDoorAuth')->add($data);
            }
            if($res){
                Ret(array('code'=>1,'info'=>'激活成功！'));
            }else{
                Ret(array('code'=>2,'info'=>'激活失败，系统错误！'));
            }


        }




    }

    private function getMemIDByCard($carNo){
        if($carNo){
           return D('Member')->field('id,name')->where('card_number='.$carNo)->find();
        }
    }

    private function getCourseByMemID($memID){
        if($memID){
            $con['member_id'] = $memID;
            return  D('CourseReserve')->where($con)->select();
        }
    }

    private function getDoorTimeByCourse($cosID){
        if($cosID){
            $con['course_id'] = $cosID;
            return D('CoursePlan')->where($con)->find();
        }
    }



    public function charge_api()
    {
        $data['card_number'] = I('card_number');
        $data['recharge_monney'] = I('recharge_money');
        $card = getCard($data['card_number']);
        $cardInfo['card_typeid'] = $card[0]['card_typeid'];
        $cardInfo['card_state'] = $card[0]['card_state'];
        $cardInfo['cardnumber_no'] = $card[0]['cardnumber_no'];
        $cardInfo['card_number'] = $card[0]['card_number'];
        if (!$cardInfo['card_number']) {
            Ret(array('code' => 2, 'info' => '卡号不存在'));
        }
        if ($cardInfo['card_typeid'] == 4) {
            $chant = getChantStaff($cardInfo['card_number']);
            $res['cardnumber_no'] = $chant[0]['cardnumber_no'];
            $res['balance'] = $chant[0]['balance']+$data['recharge_monney'];
            $res['card_number'] = $chant[0]['card_number'];
            $res['id'] = $chant[0]['id'];
            $res['name'] = $chant[0]['name'];
            if (updateChantBalance($res)) {
                $cads['card_type'] = '商户员工';
                $cads['card_number'] = $res['card_number'];
                $cads['card_ownerid'] =  $res['id'];
                $cads['recharge_typeid'] = 1;
                $cads['recharge_type'] = '充值';
                $cads['recharge_num'] =  $data['recharge_monney'];
                $cads['recharge_time'] = date('Y-m-d H:i:s');
                addFees($cads);
                Ret(array('code' => 1, 'info' => '充值成功'));
            } else {
                Ret(array('code' => 2, 'info' => '充值失败'));
            }
        }
        if ($cardInfo['card_typeid'] == 3) {
            $chant = getInstStaff($cardInfo['card_number']);
            $res['cardnumber_no'] = $chant[0]['cardnumber_no'];
            $res['balance'] = $chant[0]['balance']+$data['recharge_monney'];
            $res['card_number'] = $chant[0]['card_number'];
            $res['id'] = $chant[0]['id'];
            $res['name'] = $chant[0]['name'];
            if (updateBalance($res)) {
                $cads['card_type'] = '机构员工';
                $cads['card_number'] = $res['card_number'];
                $cads['card_ownerid'] = $res['id'];
                $cads['recharge_typeid'] = 1;
                $cads['recharge_type'] = '充值';
                $cads['recharge_num'] =  $data['recharge_monney'];
                $cads['recharge_time'] = date('Y-m-d H:i:s');
                addFees($cads);
                Ret(array('code' => 1, 'info' => '充值成功'));
            } else {
                Ret(array('code' => 2, 'info' => '充值失败'));
            }
        }
        if ($cardInfo['card_typeid'] == 2) {
            $chant = getMallStaff($cardInfo['card_number']);
            $res['cardnumber_no'] = $chant[0]['cardnumber_no'];
            $res['balance'] = $chant[0]['balance']+$data['recharge_monney'];
            $res['card_number'] = $chant[0]['card_number'];
            $res['id'] = $chant[0]['id'];
            $res['name'] = $chant[0]['name'];
            if (updateMallBalance($res)) {
                $cads['card_type'] = '商场员工';
                $cads['card_number'] = $res['card_number'];
                $cads['card_ownerid'] = $res['id'];
                $cads['recharge_typeid'] = 1;
                $cads['recharge_type'] = '充值';
                $cads['recharge_num'] =  $data['recharge_monney'];
                $cads['recharge_time'] = date('Y-m-d H:i:s');
                addFees($cads);
                Ret(array('code' => 1, 'info' => '充值成功'));
            } else {
                Ret(array('code' => 2, 'info' => '充值失败'));
            }
        }
        if ($cardInfo['card_typeid'] == 1) {
            $chant = getMemberStaff($cardInfo['card_number']);
            $res['cardnumber_no'] = $chant[0]['cardnumber_no'];
            $res['balance'] = $chant[0]['balance']+$data['recharge_monney'];
            $res['card_number'] = $chant[0]['card_number'];
            $res['id'] = $chant[0]['id'];
            $res['name'] = $chant[0]['name'];
            if (updateMemberBalance($res)) {
                $cads['card_type'] = '会员';
                $cads['card_number'] = $res['card_number'];
                $cads['card_ownerid'] = $res['id'];
                $cads['recharge_typeid'] = 1;
                $cads['recharge_type'] = '充值';
                $cads['recharge_num'] =  $data['recharge_monney'];
                $cads['recharge_time'] = date('Y-m-d H:i:s');
                addFees($cads);
                Ret(array('code' => 1, 'info' => '充值成功'));
            } else {
                Ret(array('code' => 2, 'info' => '充值失败'));
            }
        }
}
    public function checkout(){
        $total=I('total');
        $cout=$_POST['detail'];
        $name= $cout[0]['name'];
        $pic=getGoodsPic($name);
//        var_dump($pic);die;
        $res['pic'] =$pic[0]['pic'];
        $count= $cout[0]['count'];
        $data['card_number']=I('card_number');
        $data=getCard($data['card_number']);
        $res['card_typeid'] =$data[0]['card_typeid'];
        $res['id'] =$data[0]['id'];
        $res['card_number'] =$data[0]['card_number'];
        $res['card_state'] =$data[0]['card_state'];
        $memberModel = D('Member');
        if (!$res['card_number']) {
            Ret(array('code' => 2, 'info' => '卡号不存在'));
        }
        if($res['card_typeid']==1 && $res['card_state']==1){
            $memberinfo=$memberModel->memberStaff($res['card_number']);
            $member['id']=$memberinfo[0]['id'];
            $member['balance'] = $memberinfo[0]['balance'];
            $member['cardnumber_no']=$memberinfo[0]['cardnumber_no'];
            $member['name'] = $memberinfo[0]['name'];
            $member['card_number'] = $memberinfo[0]['card_number'];
            if($member['balance']< $data['total']){
                Ret(array('code' => 2, 'info' => '余额不足请充值'));
            }else{
                $member['balance'] =$member['balance']-$total;
            }
//            var_dump($member['balance']);die;
            if(updateMemberBalance($member)){
               $cads['cardnumber_no'] = $member['cardnumber_no'];
               $cads['card_ownerid'] = $member['id'];
               $cads['card_rechargetime'] = date('Y-m-d H:i:s');
               $cads['card_typeid'] = 1;
               $cads['cost_typeid'] = 8;
               $cads['cost_type'] = '商品购买费用';
               $cads['card_rechargenum'] = $total;
               $cads['goods_name'] =$name;
               $cads['totall'] = $count;
               $cads['pic'] = $res['pic'];
           }
            addExpense($cads);
            $mallinfo['income_ownerid'] =$member['id'];
            $mallinfo['income_ownername'] = $member['name'];
            $mallinfo['income_ownertypeid'] =2;
            $mallinfo['income_ownertype'] ='商户';
            $mallinfo['income_time'] = date('Y-m-d H:i:s');
            $mallinfo['income_typeid']=3;
            $mallinfo['income_type'] ='商品购买收入';
            $mallinfo['income_num'] =$total;
            addIncomeDetail($mallinfo);
            }
        if($cads){
            Ret(array('code'=>1,'info'=>'结账成功'));
        }else{
            Ret(array('code'=>2,'info'=>'结账数据保存失败，系统错误！'));
        }
        }
        }