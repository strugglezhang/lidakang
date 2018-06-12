<?php

namespace Member\Controller;

use Merchant\Controller\GoodsController;
use Think\Controller;

class CardController extends Controller
{
    //卡片激活
    public function card_activity_api()
    {
        before_api();
        checkLogin();
        checkAuth();
        $data['card_number'] = I('card_number', '');
        if ($data['card_number'] == null) {
            Ret(array('code' => 2, 'info' => '卡片信息获取失败'));
        }
        $cardModel = D('Card');
        $result = $cardModel->active($data['card_number']);
        if ($result) {
            $data = getCard($data['card_number']);
            $res['card_typeid'] = $data[0]['card_typeid'];
            $res['id'] = $data[0]['id'];
            $res['card_number'] = $data[0]['card_number'];
            if ($res['card_typeid'] == 1) {
                $memberinfo = getMember($res['card_number']);
                $member['pic'] = $memberinfo[0]['pic'];
                $member['member_name'] = $memberinfo[0]['name'];
                $member['card_number'] = $res['card_number'];
            }
            if ($res['card_typeid'] == 2) {
                $memberinfo = getMall($res['card_number']);
                $member['pic'] = $memberinfo[0]['pic'];
                $member['member_name'] = $memberinfo[0]['name'];
                $member['card_number'] = $res['card_number'];
            }
            if ($res['card_typeid'] == 3) {

                $memberinfo = getInst($res['card_number']);
                $member['pic'] = $memberinfo[0]['pic'];
                $member['member_name'] = $memberinfo[0]['name'];
                $member['card_number'] = $res['card_number'];
            }
            if ($res['card_typeid'] == 4) {
                $memberinfo = getMerchant($res['card_number']);
                $member['pic'] = $memberinfo[0]['pic'];
                $member['member_name'] = $memberinfo[0]['name'];
                $member['card_number'] = $res['card_number'];
            }
            Ret(array('code' => 1, 'data' => $member, 'info' => '卡片激活成功！'));
        } else {
            Ret(array('code' => 2, 'info' => '卡片信息激活失败'));
        }
    }

//充值详情
    public function recharge_view_api()
    {
        before_api();
        checkLogin();
        checkAuth();
        $page = I('page', 1, 'intval');

        $pagesize = I('pagesize', 10, 'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page . ',' . $pagesize;

        $rechargeModel = D('CardRecharge');
        $count = $rechargeModel->get_recharge_count();
        $data = $rechargeModel->get_recharge_list($page);
        $memberModel = D('Member');
        foreach ($data as $k => $v) {
            $fields = array('name,pic');
            $memberinfo = $memberModel->get_member_infos($v['member_id'], $fields);
            $data[$k]['pic'] = $memberinfo[0]['pic'];
            $data[$k]['member_name'] = $memberinfo[0]['name'];
        }
        if ($data) {
            Ret(array('code' => 1, 'data' => $data, 'total' => $count, 'page_count' => ceil($count / $pagesize)));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据！'));
        }

    }

//充值查询
    public function recharge_search_api()
    {
        //checkLogin();
        //ckeckAuth();
        //$mall_id = session('mall.mall_id');
        $mall_id = 2;
        $rechargeModel = D('CardRecharge');

        $keyword['member_id'] = I('member_id', '');
        if ($keyword['member_id'] == null) {
            Ret(array('code' => 2, 'info' => '请输入会员号！'));
        }
        $keyword['begintime'] = I('begintime', '');
        if ($keyword['begintime'] == null) {
            Ret(array('code' => 2, 'info' => '请选择开始时间！'));
        }
        $keyword['endtime'] = I('endtime', '');
        if ($keyword['endtime'] == null) {
            Ret(array('code' => 2, 'info' => '请选择结束时间！'));
        }

        $data = $rechargeModel->get_recharge_by_keyword($mall_id, $keyword);

        if ($data) {
            Ret(array('code' => 1, 'data' => $data));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据！'));
        }

    }


    /*
        * 会员卡挂失（列表）
        */
    public function card_lost_record()
    {
        before_api();
        checkLogin();
        checkAuth();
        $keyword = I('keyword');
        $page = I('page', 1, 'intval');
        $pagesize = I('pagesize', 10, 'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page . ',' . $pagesize;
        //$cordBondModel = D('Member');
        $fields = array('id,pic,name,phone,card_number');
        //$count = $cordBondModel->get_count_card_lost($keyword);
        //$res = $cordBondModel->get_list_card_lost($keyword, $page, $fields);
         $count=getCardOwnerNum($keyword);
         $res=getCardOwnerInfo($keyword,$fields);
        if ($res) {
            Ret(array('code' => 1, 'data' => $res, 'total' => $count, 'page_count' => ceil($count / $pagesize)));
        } else {
            Ret(array('code' => 2, 'info' => '会员卡不存在或被挂失！'));
        }
    }


    /*
     * 会员卡挂失
     */

    public function card_lost_set()
    {
        before_api();
        checkLogin();
        checkAuth();
        $data['card_number'] = I('card_number');
        if (!$data['card_number']) {
            Ret(array('code' => 2, 'info' => '数据获取（card_number）失败！'));
        }
        //var_dump($data['card_number']);die;
        $card = getCardIds($data['card_number']);
        //var_dump($card);die;
        $data['id'] = $card[0]['id'];
        //$data['card_state'] = $card[0]['card_state'];
        $data['card_state'] = 3;
        //$data['card_number'] = $card[0]['card_number'];
        //var_dump($data);die;

        $res = D('Card')->save($data);

        //将会员卡的所有者名下的卡清空。解除绑定
        $isUnbondOK=setUnbondCardByCardNo($data['card_number']);
        if(!$isUnbondOK)
        {
            Ret(array('code' => 2, 'info' => '卡号解除绑定失败！'));
        }
        if ($res) {
            Ret(array('code' => 1, 'info' => '挂失成功！'));
        } else {
            Ret(array('code' => 2, 'info' => '数据保存失败，挂失失败！'));
        }


    }

    /*
     * 补卡
     */
    public function card_markup()
    {
        before_api();
        checkLogin();
        checkAuth();
        $cardNumber=I('card_number');
        $ownerId=session('card_owner_id');
        $cardNumberNo=I('card_number_no');
        //将其卡号填写到绑定者信息中
        //var_dump($ownerId);die;
        $isSetOk=setActivateCard($cardNumber,$ownerId,$cardNumberNo);
        if($isSetOk)
        {
            Ret(array('code' => 1, 'info' => '补卡成功！'));
        }
        else
        {
            Ret(array('code' => 2, 'info' => '补卡失败！'));
        }

    }
    public function setcard($data,$workerID){
        if($data && $workerID){
            $data['card_ownerid'] = $workerID;
            $data['card_ownewneme'] =$data['name'];
            $data['card_number'] = $data['card_number'];
            //$data['cardnumber_no'] = $data['cardnumber_no'];
            $data['card_typeid'] = 2;
            $data['card_state'] = 0;
            D('Card')->add($data);
        }
    }
    /*
     * 检验当前会员是否还有有效卡
     */
    public function check_member_card()
    {
        before_api();
        checkLogin();
        checkAuth();

        $member_id = I('member_id');

        if (!$member_id) {
            Ret(array('code' => 2, 'info' => '参数（member_id）有误！'));
        }

        //检查当前用户是否还有有效卡
        //$check = D('Card')->check_member_card($member_id);
        $cardOwnerInfo=getCardOwnerInfoById($member_id);
        if($cardOwnerInfo)
        {
            $check=$cardOwnerInfo['card_number'];
            if($check==NULL or $check=='' or $check==0)
            {
                session('card_owner_id',$member_id);
                //session('card_owner_type',$cardOwnerInfo['owner_type']);
                Ret(array('code' => 1, 'info' => '可以补卡！'));

            }
            else
            {
                Ret(array('code' => 2, 'info' => '当前用户还有有效卡(卡号：' . $cardOwnerInfo['card_number'] . ')，请在挂失后，再进行补卡！', 'card_number' => $cardOwnerInfo['card_number']));
            }
        }
        else
        {
            Ret(array('code' => 2,'info'=>'无此用户'));
        }
    }


    /*
     * 有卡充值(信息查询）
     */
    public function incharge_by_card()
    {
        before_api();
        checkLogin();
        checkAuth();
        $card_number = I('card_number');

        // 判断是什么用户
        $typeArr = ['member', 'institution_staff',  'mall_staff', 'merchant_staff'];
        foreach ($typeArr as $key => $v) {
            $member = D($v);
            $obj = $member->where('card_number='.$card_number)->find();

            if (!empty($obj) ) {
                $data = $obj;
            }
        }
        $result['pic'] = $data['pic'];
        $result['name'] = $data['name'];
        $result['phone'] = $data['phone'];
        $result['card_number'] = $data['card_number'];

        if ($result['name']!=null) {
            Ret(array('code' => 1, 'data' => $result));
        } else {
            Ret(array('code' => 2, 'info' => '卡号不存在或已挂失！'));
        }
    }
    /*
    * 根据身份证号，卡号，会员号查询充值人员信息
    */
    public function get_charger_info()
    {
        before_api();
        checkLogin();
        checkAuth();
        $keyInfo = I('key_info');

        // 判断是什么用户
        $typeArr = ['member', 'institution_staff',  'mall_staff', 'merchant_staff'];
        foreach ($typeArr as $key => $v) {
            $member = D($v);
            $obj = $member->where('card_number='.$keyInfo)->find();

            if (!empty($obj) ) {
                $data = $obj;
            }
            else
            {
                $obj = $member->where('cardnumber_no='.$keyInfo)->find();
                if(!empty($obj) )
                {
                    $data = $obj;
                }
                else
                {
                    $obj = $member->where('id_number='.$keyInfo)->find();
                    if(!empty($obj) )
                    {
                        $data = $obj;
                    }
                }
            }
        }
        $result['pic'] = $data['pic'];
        $result['name'] = $data['name'];
        $result['phone'] = $data['phone'];
        $result['card_number'] = $data['card_number'];
        $result['id']=$data['id'];
        if ($result['name']!=null) {
            Ret(array('code' => 1, 'data' => $result));
        } else {
            Ret(array('code' => 2, 'info' => '卡号不存在或已挂失！'));
        }
    }
    /*
    * 无卡充值(信息查询）
    */
    public function incharge_by_noncard()
    {
        $keyword = I('keyword');
        if (!$keyword) {
            return false;
        }
        $res = get_member_card($keyword);
        $mall = get_mall_card($keyword);
        $inst = get_inst_card($keyword);
        $merchant = get_merchant_card($keyword);
        if ($keyword = get_member_card($keyword)) {
            $data['id'] = $res[0]['id'];
            $data['name'] = $res[0]['name'];
            $data['phone'] = $res[0]['phone'];
            $data['pic'] = $res[0]['pic'];
            if ($data) {
                Ret(array('code' => 1, 'data' => $data));
            } else {
                Ret(array('code' => 2, 'info' => '卡号不存在！'));
            }
        } elseif ($keyword = get_mall_card($keyword)) {
            $data['id'] = $mall[0]['id'];
            $data['name'] = $mall[0]['name'];
            $data['phone'] = $mall[0]['phone'];
            $data['pic'] = $mall[0]['pic'];
            if ($data) {
                Ret(array('code' => 1, 'data' => $data));
            } else {
                Ret(array('code' => 2, 'info' => '卡号不存在！'));
            }
        } elseif ($keyword = get_inst_card($keyword)) {
            $data['id'] = $inst[0]['id'];
            $data['name'] = $inst[0]['name'];
            $data['phone'] = $inst[0]['phone'];
            $data['pic'] = $inst[0]['pic'];
            if ($data) {
                Ret(array('code' => 1, 'data' => $data));
            } else {
                Ret(array('code' => 2, 'info' => '卡号不存在！'));
            }
        } elseif ($keyword = get_merchant_card($keyword)) {
            $data['id'] = $merchant[0]['id'];
            $data['name'] = $merchant[0]['name'];
            $data['phone'] = $merchant[0]['phone'];
            $data['pic'] = $merchant[0]['pic'];
            if ($data) {
                Ret(array('code' => 1, 'data' => $data));
            } else {
                Ret(array('code' => 2, 'info' => '卡号不存在！'));
            }
        } else {
            Ret(array('code' => 2, 'info' => '没有数据！'));
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

    private function get_member_by_IDnumber($idnumber)
    {
        if ($idnumber) {
            $fields = array('id,pic,name,phone');
            $condition['id_number'] = $idnumber;
            return D('Member')->field($fields)->where($condition)->select();
        }
    }

    private function get_member_by_name($name)
    {
        if ($name) {
            $fields = array('id,pic,name,phone');
            $condition['name'] = $name;
            return D('Member')->field($fields)->where($condition)->select();
        }
    }

    private function get_card_by_number($keyword)
    {
        $key['card_number'] = $keyword;
        $key['id'] = $keyword;
        $key['_logic'] = 'OR';
        $condition['_complex'] = $key;
        $condition['cardState'] = array('in', '0,1');
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
    public function card_check($car_number)
    {
        before_api();
        if ($car_number) {
            $condition['card_number'] = $car_number;
            $condition['cardState'] = 0;
            return D('Member')->where($condition)->select();
        }
    }

    /*
     * 绑定卡片时检验卡片是否有效
     */
    public function check_card()
    {
        before_api();
        $cardNumber=check_card();

    }

    private function get_member_by_id($member_id)
    {
        if ($member_id) {
            $condition['id'] = $member_id;
            return D('Member')->where($condition)->field('id,name,pic,phone')->find();
        }

    }

    private function get_card_by_member($member_id)
    {
        if ($member_id) {
            $condition['id'] = $member_id;
            $condition['cardState'] = array('in', '0,1');
            return D('Member')->where($condition)->field('card_number')->select();
        }
    }

    private function get_member_by_card($card_member)
    {
        if ($card_member) {
            $condition['card_number'] = $card_member;
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
    public function reback_money()
    {

        $data['id'] = I('id');
        if ($data['id'] == null) {
            Ret(array('code' => 2, 'info' => '参数（id）获取失败！'));
        }
        $expense = getExpense($data['id']);
        $card_typeid = $expense[0]['card_typeid'];
        $card_ownerid = $expense[0]['card_ownerid'];
        $member = getMembe($card_ownerid);
        $res['card_typeid'] = $card_typeid;
        $res['member_name'] = $member[0]['name'];
        $res['pic'] = $member[0]['pic'];
        $res['member_id'] = $card_ownerid;
        $res['price'] = $expense[0]['card_rechargenum'];
        $res['time'] = $expense[0]['card_rechargetime'];
        $res['content'] = $expense[0]['cost_type'];
        $res = D('Reimburse')->add($res);
        if ($res) {
            Ret(array('code' => 1, 'info' => '退款保存成功，请等待审核通过后返款！'));
        } else {
            Ret(array('code' => 2, 'info' => '退款保存失败，系统错误！'));
        }
    }


    /*
     * 退款审核（列表）
     */
    public function reback_money_check_list()
    {
        before_api();
        checkLogin();
        checkAuth();
        $page = I('page', 1, 'intval');
        $pagesize = I('pagesize', 10, 'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page . ',' . $pagesize;
        $count = D('Reimburse')->getCoun();
        $data = D('Reimburse')->getExpenseDetail($page);
        if ($data) {
            Ret(array('code' => 1, 'data' => $data, 'total' => $count, 'page_count' => ceil($count / $pagesize)));
        } else {
            Ret(array('code' => 2, 'info' => '退款保存失败，系统错误！'));
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
            $cads['recharge_num'] = $reimbInfo['price'];
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
            D('Reimburse')->where('id=' . $data['id'])->delete();
            Ret(array('code' => 1, 'info' => '审核成功！'));
        } else {
            Ret(array('code' => 2, 'info' => '审核失败，系统错误！'));
        }
    }


    /*
     * 退款列表
     */
    public function reback_list()
    {
        $page = I('page', 1, 'intval');
        $pagesize = I('pagesize', 10, 'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page . ',' . $pagesize;

        $card_number = I('keyword');
        $card = getCardNumber($card_number);
//        var_dump($card);die;
        $card_ownerid = $card[0]['card_ownerid'];
        $member = getMembe($card_ownerid);
        $expenseDetailModel = D('ExpenseDetail');
        $count = $expenseDetailModel->getCoun($card_ownerid);
        $data = $expenseDetailModel->getExpenseDetail($card_ownerid, $page);
//        echo $expenseDetailModel->_sql();die;
        foreach ($data as $key => $value) {
            $res[$key]['member_name'] = $member[0]['name'];
            $res[$key]['pic'] = $member[0]['pic'];
            $res[$key]['time'] = $value['card_rechargetime'];
            $res[$key]['price'] = $value['card_rechargenum'];
            $res[$key]['content'] = $value['cost_type'];
            $res[$key]['id'] = $value['id'];
        }
        if ($res) {
            Ret(array('code' => 1, 'data' => $res, 'total' => $count, 'page_count' => ceil($count / $pagesize)));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据'));
        }

    }

    /*
     * 余额查询
     */
    public function balance()
    {
        before_api();
        checkLogin();
        checkAuth();
        $card_number = I('card_number');
        $member = get_member($card_number);
        $mall = get_mall($card_number);
        $inst = get_inst($card_number);
        $merchant = get_merchant($card_number);
        if ($card_number = $member) {
            if ($member) {
                Ret(array('code' => 1, 'data' => $member[0]));
            } else {
                Ret(array('code' => 2, 'info' => '没有数据！'));
            }
        }
        if ($card_number = $mall) {
            if ($mall) {
                Ret(array('code' => 1, 'data' => $mall[0]));
            } else {
                Ret(array('code' => 2, 'info' => '没有数据！'));
            }
        }
        if ($card_number = $inst) {
            if ($inst) {
                Ret(array('code' => 1, 'data' => $inst[0]));
            } else {
                Ret(array('code' => 2, 'info' => '没有数据！'));
            }
        }
        if ($card_number = $merchant) {
            if ($merchant) {
                Ret(array('code' => 1, 'data' => $merchant[0]));
            } else {
                Ret(array('code' => 2, 'info' => '没有数据！'));
            }
        }

    }

    /*
     * 结账
     */
    public function checkoutList()
    {
        before_api();
        checkLogin();
        checkAuth();
        $data['count'] = I('total');
        $data['card_number'] = I('card_number');
        $data['detail'] = json_encode($_POST['detail']);
        $member = $this->get_member_by_card($data['card_number']);
        if (!$member) {
            Ret(array('code' => 2, 'info' => '卡片无效，或未激活！'));
        }
        $data['member_id'] = $member["member_id"];
        $data['content'] = '购物';
        $data['list'] = I('list');
        $data['time'] = date('Y-m-d H:m:s');
        $data['mall_id'] = session('mall_id');
        if ($data) {
            $res = D('Consume')->make_consume($data);

            $inoutdata['member_id'] = $data['member_id'];
            $inoutdata['outcome'] = $data['count'];
            $inoutdata['time'] = date('Y-m-d H:i:s');
            $in_out_come = D('InOutCome')->make_outcome($inoutdata);


            $detail = $_POST['detail'];
            foreach ($detail as $k => $v) {
                $code = $v['code'];
                $count = $v['count'];
                D('Goods')->makeStoreModify($code, $count);
            }

            if (!$in_out_come) {
                D('Consume')->where('id=' . $res)->delete();
                Ret(array('code' => 2, 'info' => '结账数据保存失败，系统错误！'));
            }
            if ($res) {
                Ret(array('code' => 1, 'info' => '结账成功'));
            } else {
                Ret(array('code' => 2, 'info' => '结账数据保存失败，系统错误！'));
            }
        }

    }


    /*
     * app查看会员卡余额
     */

    public function app_member_balance()
    {
//        before_api();
        checkLogin();
        checkAuth();
        $member_id = I('member_id');
        $data = D('CardRecharge')->get_balance($member_id);
        if ($data) {
            Ret(array('code' => 1, 'data' => $data));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据！'));
        }
    }

    //卡片二次激活
    public function makeSecCarAct()
    {
        before_api();
        checkLogin();
        checkAuth();
        $instID = session('inst.institution_id', 1);

        $carNo = I('card_number');

        if (!$carNo) {
            Ret(array('code' => 2, 'info' => '参数(card_number)错误！'));
        }
        $memInfo = $this->getMemIDByCard($carNo);
        $courInfo = $this->getCourseByMemID($memInfo['id']);
        foreach ($courInfo as $k => $v) {
            //格式：eqNO,cardNO,Pin,name,starttime,endtime#timeID]week0,timezone1,timezone2,timezone3...;week1,timezone1,timezone2,timezone3..;.\r\n   you to me
            // 例子：12345,23456,2001,locost,20170730,20170731#23]1,9:30-11:00,10:30-12:00,16:30-18:00;2,9:30-11:00,10:30-12:00,16:30-18:00\r\n
            $time = $this->getDoorTimeByCourse($v['course_id']);
            $roomId = $time['room_id'];
            if ($roomId) {
                $eqNo = D('RoomNumber')->field('equip_ip')->where('id=' . $roomId)->find();
            }
            $t1 = $time['start_time'];
            $t2 = $time['end_time'];

            $data['eqIp'] = $eqNo['equip_ip'];
            $data['cardNo'] = $carNo;
            $data['memberId'] = 's' . $memInfo['id'];
            $data['memberName'] = $memInfo['name'];
            $data['time'] = date('Ymd', strtotime($t1)) . ',' . date('Ymd', strtotime($t2)) . '#' . rand(1, 1000) . ']' . date('w', strtotime($t1)) . ',' . date('H:i', strtotime($t1)) . '-' . date('H:i', strtotime($t2));

            if ($data) {
                $res = D('MemberDoorAuth')->add($data);
            }
            if ($res) {
                Ret(array('code' => 1, 'info' => '激活成功！'));
            } else {
                Ret(array('code' => 2, 'info' => '激活失败，系统错误！'));
            }


        }


    }


    private function getMemIDByCard($carNo)
    {
        if ($carNo) {
            return D('Member')->field('id,name')->where('card_number=' . $carNo)->find();
        }
    }

    private function getCourseByMemID($memID)
    {
        if ($memID) {
            $con['member_id'] = $memID;
            return D('CourseReserve')->where($con)->select();
        }
    }

    private function getDoorTimeByCourse($cosID)
    {
        if ($cosID) {
            $con['course_id'] = $cosID;
            return D('CoursePlan')->where($con)->find();
        }
    }
    //新卡片充值接口
    public function charge_card()
    {
        before_api();
        checkLogin();
        checkAuth();
        $card_number = I('card_number');
        $recharge_money = I('recharge_money');
        $recharge_method=I('recharge_method');
        //recharge_method=1:微信充值 2：支付宝支付 3：现金充值

        // 判断是什么用户
        $typeArr = ['member', 'institution_staff',  'mall_staff', 'merchant_staff'];
        $flag = false;
        foreach ($typeArr as $key => $v) {
            $member = D($v);
            $obj = $member->where(['card_number' => $card_number])->find();
            $cardInfo=D('Card')->where('card_number='.$card_number)->find();
            //var_dump($cardInfo);die;
            if (!empty($obj) && $cardInfo['card_state']==1) {
                $flag = true;
                $member->execute("update $v set balance=balance+$recharge_money where card_number='$card_number'");
                $rechargeInfo['card_type']='会员卡';
                $rechargeInfo['card_type_id']=1;
                if($v=='institution_staff')
                {
                    $rechargeInfo['card_type']='机构员工卡';
                    $rechargeInfo['card_type_id']=2;
                }
                if($v=='mall_staff')
                {
                    $rechargeInfo['card_type']='商场员工卡';
                    $rechargeInfo['card_type_id']=3;
                }
                if($v=='merchant_staff')
                {
                    $rechargeInfo['card_type']='商户员工卡';
                    $rechargeInfo['card_type_id']=4;
                }
                $rechargeInfo['card_number']=$card_number;
                $rechargeInfo['card_ownerid']=$obj['id'];
                $rechargeInfo['card_owner_name']=$obj['name'];
                $rechargeInfo['recharge_num']=$recharge_money;
                $rechargeInfo['recharge_time']=date("Y-m-d H:i:s");

                //var_dump(session("worker_name"));die;
                $rechargeInfo['submitter']=session("worker_name");
                //var_dump($rechargeInfo['submitter']);die;
                $rechargeInfo['submitter_id']=session("worker_id");
                $rechargeInfo['recharge_method']=$recharge_method;
                //var_dump($rechargeInfo);die;
                D('RechargeDetail')->add($rechargeInfo);
                Ret(array('code' => 1, 'info' => '充值成功'));
            }
            else
            {
                Ret(array('code' => 2, 'info' => '卡号不存在！或卡被挂失冻结'));
            }

        }
    }
    //旧卡片充值接口
    public function charge_api()
    {

        before_api();
        checkLogin();
        checkAuth();
        $card_number = I('card_number');
        $recharge_money = I('recharge_money');
        //$recharge_method=I('recharge_method');
        //recharge_method=1:微信充值 2：支付宝支付 3：现金充值

        // 判断是什么用户
        $typeArr = ['member', 'institution_staff',  'mall_staff', 'merchant_staff'];
        $flag = false;
        foreach ($typeArr as $key => $v) {
            $member = D($v);
            $obj = $member->where(['card_number' => $card_number])->find();
            $cardInfo=D('Card')->where('card_number='.$card_number)->find();
            //var_dump($cardInfo);die;
            if (!empty($obj) && $cardInfo['card_state']==1) {
                $flag = true;
                $member->execute("update $v set balance=balance+$recharge_money where card_number='$card_number'");
                $rechargeInfo['card_type']='会员卡';
                $rechargeInfo['card_type_id']=1;
                if($v=='institution_staff')
                {
                    $rechargeInfo['card_type']='机构员工卡';
                    $rechargeInfo['card_type_id']=2;
                }
                if($v=='mall_staff')
                {
                    $rechargeInfo['card_type']='商场员工卡';
                    $rechargeInfo['card_type_id']=3;
                }
                if($v=='merchant_staff')
                {
                    $rechargeInfo['card_type']='商户员工卡';
                    $rechargeInfo['card_type_id']=4;
                }
                $rechargeInfo['card_number']=$card_number;
                $rechargeInfo['card_ownerid']=$obj['id'];
                $rechargeInfo['card_owner_name']=$obj['name'];
                $rechargeInfo['recharge_num']=$recharge_money;
                $rechargeInfo['recharge_time']=date("Y-m-d H:i:s");

                //var_dump(session("worker_name"));die;
                $rechargeInfo['submitter']=session("worker_name");
                //var_dump($rechargeInfo['submitter']);die;
                $rechargeInfo['submitter_id']=session("worker_id");
                //$rechargeInfo['recharge_method']=$recharge_method;
                //var_dump($rechargeInfo);die;
                D('RechargeDetail')->add($rechargeInfo);
                Ret(array('code' => 1, 'info' => '充值成功'));
            }
            else
            {
                Ret(array('code' => 2, 'info' => '卡号不存在！或卡被挂失冻结'));
            }

        }


    }

    public function checkout()
    {
        $total = I('total');
        $cout = $_POST['detail'];
        $name = $cout[0]['name'];
        $pic = getGoodsPic($name);
        $res['pic'] = $pic[0]['pic'];
        //$count = $cout[0]['count'];
        $data['card_number'] = I('card_number');

        // 判断是什么用户
        $typeArr = [1 => 'member', 2 => 'institution_staff', 3 => 'mall_staff', 4 => 'merchant_staff'];

        $cardInfo = false;
        $type = false;
        $table = false;
        foreach ($typeArr as $key => $v) {
            $member = D($v);
            $obj = $member->where(['card_number' => $data['card_number']])->find();
            if (!empty($obj)) {
                $cardInfo = $obj;
                $table = $v;
                $type = $key;
                break;
            }
        }

        if ($type === false || $cardInfo === false) {
            Ret(array('code' => 2, 'info' => '卡号不存在'));
        }

        if (!isset($cardInfo['type']) || $cardInfo['state'] != 1) {
            $state = $cardInfo['type'];
        }

        $res['card_typeid'] = $type;
        $res['id'] = $cardInfo['id'];
        $res['card_number'] = $cardInfo['card_number'];
        $res['card_state'] = $state;

        if (($cardInfo['balance'] - $total) < 0) {
            Ret(array('code' => 2, 'info' => '余额不足请充值'));
        }

        if (!$res['card_number']) {
            Ret(array('code' => 2, 'info' => '卡号不存在'));
        }

        $balance = $cardInfo['balance'] - $total;
        if (updateMemberNewBalance($balance, $cardInfo['id'], $table)) {
            foreach ($cout as $key=>$value)
            {
                $cads['cardnumber_no'] = $cardInfo['cardnumber_no'];
                $cads['card_ownerid'] = $cardInfo['id'];
                $cads['card_rechargetime'] = date('Y-m-d H:i:s');
                $cads['card_typeid'] = $res['card_typeid'];
                $cads['cost_typeid'] = 8;
                $cads['cost_type'] = '商品购买费用';
                $cads['card_rechargenum'] = $value['price'];
                $cads['goods_name'] = $name;
                $cads['totall'] = $total;
                $cads['pic'] = $res['pic'];
                $cads['card_owner_name'] = $cardInfo['name'];
                $cads['submit'] = session('worker_name');
                $cads['submit_id'] = session('worker_id');
                $cads['card_number'] = $res['card_number'];
                $cads['cost_type']='商品';
                $cads['cost_content_id']=$value['code'];
                $cads['cost_content_name']=$value['name'];
                $cads['number']=$value['count'];
                $goodsInfo=D('Goods')->where('code='.$value['code'])->find();
                $cads['expense_owner_id']=$goodsInfo['merchant_id'];
                $merchantInfo=D('Merchant')->get_inst_by_id($goodsInfo['merchant_id']);
                $cads['expense_owner_name']=$merchantInfo[0]['name'];
                $cads['expense_owner_typeid']=3;
                $cads['expense_owner_type']='商户';
                addExpense($cads);
            }
        }

        $mallinfo['income_ownerid'] = $cardInfo['id'];
        $mallinfo['income_ownername'] = $cardInfo['name'];
        $mallinfo['income_ownertypeid'] = 2;
        $mallinfo['income_ownertype'] = '商户';
        $mallinfo['income_time'] = date('Y-m-d H:i:s');
        $mallinfo['income_typeid'] = 3;
        $mallinfo['income_type'] = '商品购买收入';
        $mallinfo['income_num'] = $total;
        addIncomeDetail($mallinfo);
        if ($cads) {
            Ret(array('code' => 1, 'info' => '结账成功'));
        } else {
            Ret(array('code' => 2, 'info' => '结账数据保存失败，系统错误！'));
        }
    }
    //补卡信息
    public function activateCard()
    {
        $cardNumber=I('card_number');
        $res=hasSameCard($cardNumber);
        if(!$res)
        {
            Ret(array('code' => 1, 'info' => '此卡有效'));
        }
        else
        {
            Ret(array('code' => 2, 'info' => '此卡被人使用或被冻结挂失'));
        }
    }



    /*
     * 充值订单号
     */
    public function addRechargeInfo()
    {
        //充值人的ID
        $data['recharger_id']=I('recharger_id');
        //充值人的卡号
        $data['recharger_card_number']=I('recharger_card_number');
        //充值人的名字
        $data['recharger_name']=I('recharger_name');
        //充值人的图片
        $data['recharger_pic']=I('recharger_pic');
        //充值时间
        $data['recharge_time']=date('Y-m-d H:i:s');
        $data['recharge_date']=date('Y-m-d');
        //提交人ID
        $data['submitter_id']=I('submitter_id');
        //提交人名字
        $data['submitter_name']=I('submitter_name');
        //提交订单状态 0:代表未知
        $data['recharge_state']=0;
        $data['check_state']=0;
        //recharge_typeid:1为微信支付 2:支付宝支付 3：刷卡支付 4: 现金充值
       // $data['recharge_typeid']=I('recharge_typeid');
       // $data['recharge_type']=I('recharge_type');
        //$data['recharge']=
        $lastInfo=D('RechargeInfo')->getLastRechargeInfo();

        if(empty($lastInfo))
        {
            $data['traceId']='000001';
            $data['referenceId']='000000000001';
            $data['Ircode']=strval(rand(100,999));
            //var_dump($data);
            //die;
        }
        else
        {
            $newTraceId= (int)$lastInfo[0]['traceId']+1;
            $data['traceId'] = substr('000000' . $newTraceId, -6);
            $newReferenceId=(int)$lastInfo[0]['traceId']+1;
            $data['referenceId']=substr('000000000000'.$newReferenceId, -12);
            $data['Ircode']=strval(rand(100,999));

        }
        $res=D('RechargeInfo')->add($data);
        if($res)
        {
            Ret(array('code' => 1, 'rechargeInfoId'=>$res,'traceId' =>$data['traceId'],'referenceId'=>$data['referenceId'],'Ircode'=>$data['Ircode'],'Recharge_time'=>$data['recharge_time']));
        }
        else
        {
            Ret(array('code' => 2, 'info' => '创建订单失败'));
        }

    }
/*
 * 更新支付订单信息
 */
    public function updateRechargeInfo()
    {
        //支付订单ID
        $data['recharge_info_id']=I('recharge_info_id');
        //0:未知；1：支付成功；2：支付失败
        $data['recharge_state']=I('recharge_state');
        //支付金额
        $data['recharge_num']=I('recharge_num');
        //apptype
        $card_number=I('recharger_card_number');

        $data['apptype']=I('apptype');
        //var_dump($card_number);die;
        //posid
        $data['posid']=I('posid');
        $data['operid']=I('operid');
        $data['trans']=I('trans');
        $data['amount']=I('amount');
        $data['qrcode']=I('qrcode');
        $data['recharge_typeid']=I('recharge_typeid');
        $data['recharge_type']=I('recharge_type');
        if($data['recharge_typeid']!=4)
        {
            //echo('1111');
            $recharge_money=$data['recharge_num'];
            $data['check_state']=1;
            $typeArr = ['member', 'institution_staff',  'mall_staff', 'merchant_staff'];
            $flag = false;
            foreach ($typeArr as $key => $v) {
                //var_dump($v);
                $member = D($v);
                $obj = $member->where('card_number=' . $card_number)->find();
                //echo $member->getLastSql();
                $cardInfo = D('Card')->where('card_number=' . $card_number)->find();
                //var_dump($obj);die;
                if (!empty($obj) && $cardInfo['card_state'] == 1) {
                    $flag = true;
                    //var_dump($obj);die;
                    $recharge_sum=$obj['balance']+$recharge_money;
                    $member->execute("update $v set balance=$recharge_sum where card_number='$card_number'");
                   // echo $member->getLastSql();
                    $rechargeInfo['card_type'] = '会员卡';
                    $rechargeInfo['card_type_id'] = 1;
                    if ($v == 'institution_staff') {
                        $rechargeInfo['card_type'] = '机构员工卡';
                        $rechargeInfo['card_type_id'] = 2;
                    }
                    if ($v == 'mall_staff') {
                        $rechargeInfo['card_type'] = '商场员工卡';
                        $rechargeInfo['card_type_id'] = 3;
                    }
                    if ($v == 'merchant_staff') {
                        $rechargeInfo['card_type'] = '商户员工卡';
                        $rechargeInfo['card_type_id'] = 4;
                    }
                    $rechargeInfo['card_number'] = $card_number;
                    $rechargeInfo['card_ownerid'] = $obj['id'];
                    $rechargeInfo['card_owner_name'] = $obj['name'];
                    $rechargeInfo['recharge_num'] = $recharge_money;
                    $rechargeInfo['recharge_time'] = date("Y-m-d H:i:s");
                    $rechargeInfo['recharge_typeid'] = $data['recharge_typeid'];
                    $rechargeInfo['recharge_type'] = $data['recharge_type']=I('recharge_type');
                    //var_dump(session("worker_name"));die;
                    $rechargeInfo['submitter'] = session("worker_name");
                    //var_dump($rechargeInfo['submitter']);die;
                    $rechargeInfo['submitter_id'] = session("worker_id");
                    //$rechargeInfo['recharge_method']=$recharge_method;
                    //var_dump($rechargeInfo);die;
                    D('RechargeDetail')->add($rechargeInfo);
                    D('RechargeInfo')->checkOKRechargeInfo($data);
                    Ret(array('code' => 1, 'info' => '充值成功'));
                }
            }
            Ret(array('code' => 2, 'info' => '卡号不存在！或卡被挂失冻结'));

        }
        else
        {
            if($data['recharge_num']<=500)
            {
                $res = D('RechargeInfo')->checkOKRechargeInfo($data);
                //var_dump($res);
                if ($res == FALSE) {
                    Ret(array('code' => 2, 'info' => '保存失败', 'rechargeInfoId' => $data['recharge_info_id']));
                } else {
                    Ret(array('code' => 1, 'info' => '保存成功', 'rechargeInfoId' => $data['recharge_info_id']));
                }
            }
            else
            {
                Ret(array('code' => 2, 'info' => '现金只能充小于等于500元的金额'));
            }
        }
        //var_dump($data);
        //die;

    }
    /*
     * 获取充值订单列表
     */
    public function getRechargeInfoList()
    {
        //页面所显示个数
        $pagesize=I('pagesize');
        //页面页数
        $pageNo=I('page');
        $res=D('RechargeInfo')->getRechargeInfoList($pageNo);
        $count=D('RechargeInfo')->getRechargeInfoCount();
        if(!$res){
            Ret(array('code'=>2,'info'=>'没有数据！'));
        }else{
            Ret(array('code'=>1,'data'=>$res,'total' => $count, 'page_count' => ceil($count/$pagesize)));
        }
    }
    /*
     * 通过充值审核不通过
     */
    public function checkNoForRecharge()
    {
        //充值订单ID
        $data['recharge_info_id']=I('recharge_info_id');
        //审核者ID
        $data['checker_id']=I('checker_id');
        //审核者姓名
        $data['checker_name']=I('checker_name');
        //审核时间
        $data['check_time']=date('Y-m-d H:i:s');
        //recharge_state:1代表通过 2代表不通过
        $data['check_state']=2;
        //更新充值订单状态
        $data['recharge_typeid']=4;
        $data['recharge_type']='现金';
        D('RechargeInfo')->checkOKRechargeInfo($data);
        Ret(array('code'=>1,'info'=>'审核不通过！'));
    }
    /*
     * 通过现金充值审核，将所充金额加入卡中。
     */
    public function checkOkForRecharge()
    {
        //充值金额
        $recharge_money=I('recharge_num');
        //充值者卡号
        $card_number=I('recharger_card_number');
        //充值订单ID
        $data['recharge_info_id']=I('recharge_info_id');
        //审核者ID
        $data['checker_id']=I('checker_id');
        //审核者姓名
        $data['checker_name']=I('checker_name');
        //审核时间
        $data['check_time']=date('Y-m-d H:i:s');
        //recharge_state:1代表通过 2代表不通过
        $data['check_state']=1;
        //更新充值订单状态
        $data['recharge_typeid']=4;
        $data['recharge_type']='现金';


        //将钱充到会员卡中
        // 判断是什么用户
        $typeArr = ['member', 'institution_staff',  'mall_staff', 'merchant_staff'];
        $flag = false;
        foreach ($typeArr as $key => $v) {
            //var_dump($v);
            $member = D($v);
            $obj = $member->where('card_number='.$card_number)->find();
            //echo $member->getLastSql();
            $cardInfo=D('Card')->where('card_number='.$card_number)->find();
            //var_dump($obj);die;
            if (!empty($obj) && $cardInfo['card_state']==1) {
                $flag = true;
                //var_dump($obj);die;
                $recharge_sum=$obj['balance']+$recharge_money;
                $member->execute("update $v set balance=$recharge_sum where card_number='$card_number'");
                //echo $member->getLastSql();
                $rechargeInfo['card_type']='会员卡';
                $rechargeInfo['card_type_id']=1;
                if($v=='institution_staff')
                {
                    $rechargeInfo['card_type']='机构员工卡';
                    $rechargeInfo['card_type_id']=2;
                }
                if($v=='mall_staff')
                {
                    $rechargeInfo['card_type']='商场员工卡';
                    $rechargeInfo['card_type_id']=3;
                }
                if($v=='merchant_staff')
                {
                    $rechargeInfo['card_type']='商户员工卡';
                    $rechargeInfo['card_type_id']=4;
                }
                $rechargeInfo['card_number']=$card_number;
                $rechargeInfo['card_ownerid']=$obj['id'];
                $rechargeInfo['card_owner_name']=$obj['name'];
                $rechargeInfo['recharge_num']=$recharge_money;
                $rechargeInfo['recharge_time']=date("Y-m-d H:i:s");
                $rechargeInfo['recharge_typeid']=4;
                $rechargeInfo['recharge_type']='现金充值';
                //var_dump(session("worker_name"));die;
                $rechargeInfo['submitter']=session("worker_name");
                //var_dump($rechargeInfo['submitter']);die;
                $rechargeInfo['submitter_id']=session("worker_id");
                //$rechargeInfo['recharge_method']=$recharge_method;
                //var_dump($rechargeInfo);die;
                D('RechargeDetail')->add($rechargeInfo);
                D('RechargeInfo')->checkOKRechargeInfo($data);
                Ret(array('code' => 1, 'info' => '充值成功'));
            }

        }
        //var_dump($obj);die;
        Ret(array('code' => 2, 'info' => '卡号不存在！或卡被挂失冻结'));

    }
    /*
     * 非现金不审核充值
     */
    public function noCheckForRecharge()
    {
        $data['recharge_info_id']=I('recharge_info_id');
        $data['check_state']=1;
        $rechargeTypeId=I('recharge_typeid');
        $rechargeType=I('recharge_type');

        //更新充值订单状态
        D('RechargeInfo')->checkOKRechargeInfo($data);
        //充值金额
        $recharge_money=I('recharge_num');
        //充值者的卡号
        $card_number=I('recharger_card_number');
        //recharge_typeid:1为微信支付 2:支付宝支付 3：刷卡支付 4: 现金充值

        //$rechargeInfoId=I('recharge_info_id');
        $typeArr = ['member', 'institution_staff',  'mall_staff', 'merchant_staff'];
        $flag = false;
        foreach ($typeArr as $key => $v) {
            $member = D($v);
            $obj = $member->where(['card_number' => $card_number])->find();
            $cardInfo=D('Card')->where('card_number='.$card_number)->find();
            //var_dump($cardInfo);die;
            if (!empty($obj) && $cardInfo['card_state']==1) {
                $flag = true;
                $member->execute("update $v set balance=balance+$recharge_money where card_number='$card_number'");
                $rechargeInfo['card_type']='会员卡';
                $rechargeInfo['card_type_id']=1;
                if($v=='institution_staff')
                {
                    $rechargeInfo['card_type']='机构员工卡';
                    $rechargeInfo['card_type_id']=2;
                }
                if($v=='mall_staff')
                {
                    $rechargeInfo['card_type']='商场员工卡';
                    $rechargeInfo['card_type_id']=3;
                }
                if($v=='merchant_staff')
                {
                    $rechargeInfo['card_type']='商户员工卡';
                    $rechargeInfo['card_type_id']=4;
                }
                $rechargeInfo['card_number']=$card_number;
                $rechargeInfo['card_ownerid']=$obj['id'];
                $rechargeInfo['card_owner_name']=$obj['name'];
                $rechargeInfo['recharge_num']=$recharge_money;
                $rechargeInfo['recharge_time']=date("Y-m-d H:i:s");
                $rechargeInfo['recharge_typeid']=$rechargeTypeId;
                $rechargeInfo['recharge_type']=$rechargeType;
                //var_dump(session("worker_name"));die;
                $rechargeInfo['submitter']=session("worker_name");
                //var_dump($rechargeInfo['submitter']);die;
                $rechargeInfo['submitter_id']=session("worker_id");
                //$rechargeInfo['recharge_method']=$recharge_method;
                //var_dump($rechargeInfo);die;
                D('RechargeDetail')->add($rechargeInfo);
                Ret(array('code' => 1, 'info' => '充值成功'));
            }
            else
            {
                Ret(array('code' => 2, 'info' => '卡号不存在！或卡被挂失冻结'));
            }

        }
    }


}