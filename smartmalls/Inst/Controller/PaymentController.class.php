<?php
/**
 * Created by PhpStorm.
 * User: Zhoujie
 * Date: 2017/9/11
 * Time: 15:24
 */

namespace Inst\Controller;

use Inst\Model\InstitutionModel;
use Think\Controller;

class PaymentController extends Controller
{

    public function index()
    {
        var_dump($_SESSION);

    }
    public function getLoginInstitution()
    {
        $institutionModel = D('institution');
        $inst_id=session("inst.institution_id");
        $data = $institutionModel->get_inst_by_id($inst_id);
        if ($data) {
            Ret(array('code' => 1, 'data' => $data));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据'));
        }
    }
    /*
     * 获取机构列表
     */
    public function getInstitutionList()
    {
        $institutionModel = D('institution');
        $data = $institutionModel->get_inst_id();
        if ($data) {
            Ret(array('code' => 1, 'data' => $data));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据'));
        }
    }

    /*
     * 获取收费项目
     */
    public function getPaymentProject()
    {
        $payProject = getInstitutionPayment();
        Ret(array('code' => 1, 'data' => $payProject));


    }

    /*
     * 获取收费
     * input:id，instID
     * output：data
     */
    public function getPayment()
    {
        $id = I('id');
        $instID = I('instID');
        if ($id && $instID) {
            switch ($id) {
                case 1://物业费
                    $instModel = D('Institution');
                    $data = $instModel->get_inst_info($instID);
                    $shopdata = D('Shop')->get_shop_cost($data[0]['shop_id']);
                    if ($shopdata) {
                        Ret(array('code' => 1, 'data' => $shopdata[0]['property_price']));
                    }

                    break;
                case 2://宽带费
                    $conditin['institution_id'] = $instID;
                    $conditin['type'] = 2;
                    $data = D('InstCost')->where($conditin)->field('price')->find();
                    if ($data) {
                        Ret(array('code' => 1, 'data' => $data['price']));
                    }

                    break;
                case 3://房租费
                    $instModel = D('Institution');
                    $data = $instModel->get_inst_info($instID);
                    $shopdata = D('Shop')->get_shop_cost($data[0]['shop_id']);
                    if ($shopdata) {
                        Ret(array('code' => 1, 'data' => $shopdata[0]['rent_rate']));
                    }
                    break;
                case 4://管理费
                    $conditin['institution_id'] = $instID;
                    $conditin['type'] = 2;
                    $data = D('InstCost')->where($conditin)->field('price')->find();
                    if ($data) {
                        Ret(array('code' => 1, 'data' => $data['price']));
                    }

                    break;
            }

        }
    }


    /*
     * 计费计算
     * input:start_time,end_time,ids(多选，用‘-’连接),instID
     * output：data(property_price:物业费；netPrice：网费；rent_rate：租金；total：总金额）
     */
    public function makeComput()
    {

        $start_time = I('start_time');
        session('instFeeStartTime',$start_time);
        $end_time = I('end_time');
        session('instFeeEndTime',$end_time);
        $month = ceil(getMonthNum($start_time, $end_time));
        $day=ceil(getDayNum($start_time, $end_time));
        session('instFeeTimeLengthDay',$day);
        session('instFeeTimeLengthMonth',$month);

        if ($month < 1) {
            $month = 1;
        }
        $ids = I('ids');
        if ($ids) {
            $ids = explode('-', $ids);

            foreach ($ids as $k => $v) {
                $id = $v;
                $instID = I('instID');

                if ($id == 1) {
                    $instModel = D('Institution');
                    $data = $instModel->get_inst_info($instID);
                    $shopdata = D('Shop')->get_shop_cost($data[0]['shop_id']);
            //        var_dump($shopdata);
            //        die;
                    $property_price = $shopdata[0]['property_price'] * $shopdata[0]['area'];
                }
                //物业费
                if ($id == 2) {
                    $instModel = D('Institution');
                    $data = $instModel->get_inst_info($instID);
                    $shopdata = D('Shop')->get_shop_cost($data[0]['shop_id']);
                    $rent_rate = $shopdata[0]['rent_rate'] * $shopdata[0]['area'];
                }
                //房租费
                if ($id == 3) {
                    $data = D('InstCost')->where("type=2 and inst_ids like '%{$instID}%'")->limit(1)->find();
                    //var_dump($data);
                    $netPrice = $data['price'];
                }//宽带费
                if ($id == 4) {
                    $data = D('InstCost')->where("type=1 and inst_ids like '%{$instID}%'")->limit(1)->find();
                    //$shopdata = D('Shop')->get_shop_cost($data[0]['shop_id']);
                    //var_dump($data);

                    $managePrice = $data['price'];

                }//管理费
            }
        }

        $res['property_price'] = $property_price * $day;
        $res['netPrice'] = $netPrice * $month;
        $res['rent_rate'] = $rent_rate * $day;
        $res['managePrice'] = $managePrice * $month;
        $res['total'] = $res['property_price'] + $res['netPrice'] + $res['rent_rate'] + $res['managePrice'];


        $instID = I('instID');
        $instInfo = $instModel->get_inst_info($instID);
        $instPayment['institution_id']=(int)$instID;
        $instPayment['institution_name']=$instInfo[0]['name'];
        $instPayment['start_time']=$start_time;
        $instPayment['end_time']=$end_time;
        $instPayment['property_price']=(double)$res['property_price'];
        $instPayment['net_price']=(double)$res['netPrice'];
        $instPayment['rent_price']=(double)$res['rent_rate'];
        $instPayment['management_price']=(double)$res['managePrice'];
        $instPayment['sum_price']=(double)$res['total'];
        $instPayment['sum_payment']=0;
        $instPayment['payment_state']=0;
        $instPayment['debt_payment']=$instPayment['sum_price'];
        //var_dump($instPayment);
        //die;
        $instPaymentRes=D('InstPaymentDetail')->addInstPaymentDetail($instPayment);

        if ($res) {
            Ret(array('code' => 1, 'data' => $res,'instPaymentDetailId'=>$instPaymentRes));
        } else {
            Ret(array('code' => 2, 'info' => '创建订单失败'));
        }



    }

    /*
     * 机构支付
     * input:
     * output:
     */
    public function chargeInst()
    {

    }


    /**
     * 课程缴费
     */
    public function courseComput()
    {
        $card = I('card');
        $hte = I('hte');
        $ids = I('ids');
        $inst = I('inst');
        if(empty($ids))
            Ret(array('code' => 2, 'data' => "ids不能为空"));

        $ids = explode('-', $ids);
        $conditin['id'] = $ids[0];
        $data = D('CourseCard');
        $price = $data->where($conditin)->field('course_price')->find();

        $res['cardNumber'] = $card;
        $res['total'] = $price['course_price'] * $hte;
        Ret(array('code' => 1, 'data' => $res));
    }


    /**
     * 获取课程收费
     */
    public function getCoursePayment()
    {
        $id = I('id');
        $course_id = I('course_id');
        if ($id && $course_id) {
            switch ($id) {
                case 1://单次价格
                    $conditin['course_id'] = $course_id;
                    $conditin['price_typeid'] = 1;
                    $data = D('CourseCard')->where($conditin)->field('course_price')->find();
                    if ($data) {
                        Ret(array('code' => 1, 'data' => $data['course_price']));
                    }
                    break;
                case 2://月价格
                    $conditin['course_id'] = $course_id;
                    $conditin['price_typeid'] = 2;
                    $data = D('CourseCard')->where($conditin)->field('course_price')->find();
                    if ($data) {
                        Ret(array('code' => 1, 'data' => $data['course_price']));
                    }

                    break;
                case 3://季度价格
                    $conditin['course_id'] = $course_id;
                    $conditin['price_typeid'] = 3;
                    $data = D('CourseCard')->where($conditin)->field('course_price')->find();
                    if ($data) {
                        Ret(array('code' => 1, 'data' => $data['course_price']));
                    }

                    break;
                case 4://年价格
                    $conditin['course_id'] = $course_id;
                    $conditin['price_typeid'] = 4;
                    $data = D('CourseCard')->where($conditin)->field('course_price')->find();
                    if ($data) {
                        Ret(array('code' => 1, 'data' => $data['course_price']));
                    }

                    break;

            }

        }
    }

    public function instCourse()
    {
        $institution_id = I('id');
        $data = D('Course')->get_course($institution_id);
        if ($data) {
            Ret(array('code' => 1, 'data' => $data));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据'));
        }
    }

    public function getPrice()
    {
        $id = I('id');
        $courseCardModel = D('CourseCard');
        $data = $courseCardModel->getPrice($id);
        foreach ($data as $key => $value) {
            $data[$key]['price_typeid'] = get_typeid($value['price_typeid']);
        }
        if ($data) {
            Ret(array('code' => 1, 'data' => $data));
        } else {
            Ret(array('code' => 2, 'ifno' => '没有数据'));
        }
    }

    /**
     * 机构缴费（刷卡计费）
     */
    public function feesPay()
    {
        $data['card_number'] = I('card_number');
        $data['property_price'] = I('property_price');
        $data['netPrice'] = I('netPrice');
        $data['rent_rate'] = I('rent_rate');
        $data['managePrice'] = I('managePrice');
        $data['total'] = I('total');
        $card = getCard($data['card_number']);
//        var_dump($card);die;
        $cardInfo['card_typeid'] = $card[0]['card_typeid'];
        $cardInfo['card_state'] = $card[0]['card_state'];
        $cardInfo['cardnumber_no'] = $card[0]['cardnumber_no'];
        $cardInfo['card_number'] = $card[0]['card_number'];
//        var_dump($cardInfo);die;
        if (!$cardInfo['card_number']) {
            Ret(array('code' => 2, 'info' => '卡号不存在'));
        }
//        if($cardInfo['card_typeid']==4){
//          $chant= getChantStaff($cardInfo['card_number']);
////            var_dump($chant);die;
//            $res['cardnumber_no'] = $chant[0]['cardnumber_no'];
//            $res['balance'] = $chant[0]['balance'];
//            $res['card_number'] = $chant[0]['card_number'];
//            $res['id'] = $chant[0]['id'];
//            $res['name'] = $chant[0]['name'];
////            var_dump($res);die;
//            if($res['balance']< $data['total']){
//                Ret(array('code'=>2,'info'=>'余额不足'));
//            }else{
//                $res['balance'] = $res['balance']- $data['total'];
//            }
//           if(updateChantBalance($res)){
//                   $cads['cardnumber_no'] = $res['cardnumber_no'];
//                   $cads['card_ownerid'] =  $res['id'];
//                   $cads['card_rechargetime'] = date('Y-m-d H:i:s');
//                   $cads['card_typeid'] = 3;
//                   if($cads['cost_typeid'] = $data['property_price']){
//                       $cads['cost_typeid']=1;
//                       $cads['cost_type']='物业费用';
//                       $cads['card_rechargenum']=$data['property_price'];
//                   }
//                   addFees($cads);
//                   $data['cost_typeid'] = $data['netPrice'];
//                   if($cads['cost_typeid'] = $data['netPrice']){
//                       $cads['cost_typeid']=2;
//                       $cads['cost_type']='宽带费用';
//                       $cads['card_rechargenum']=$data['netPrice'];
//                   }
//                   addFees($cads);
//                   $data['cost_typeid'] = $data['rent_rate'];
//                   if($cads['cost_typeid'] = $data['rent_rate']){
//                       $cads['cost_typeid']=3;
//                       $cads['cost_type']='租赁费用';
//                       $cads['card_rechargenum']=$data['rent_rate'];
//                   }
//                   addFees($cads);
//                   $data['cost_typeid'] = $data['managePrice'];
//                   if($cads['cost_typeid'] = $data['managePrice']){
//                       $cads['cost_typeid']=4;
//                       $cads['cost_type']='管理费用';
//                       $cads['card_rechargenum']=$data['managePrice'];
//                   }
//                   addFees($cads);
//
//
//
//                   $mallinfo['income_ownerid'] = $res['id'];
//                   $mallinfo['income_ownername'] = $res['name'];
//                   $mallinfo['income_time'] = date('Y-m-d H:i:s');
//                   $mallinfo['income_ownertypeid'] = 2;
//                   $mallinfo['income_ownertype'] = '商户';
//                   if($mallinfo['income_typeid'] = $data['property_price']){
//                       $mallinfo['income_typeid']=1;
//                       $mallinfo['income_type'] ='物业费收入';
//                       $mallinfo['income_num'] =$data['property_price'];
//                   }
//                   addMall($mallinfo);
//                   $data['income_typeid'] = $data['netPrice'];
//                   if($mallinfo['income_typeid'] = $data['netPrice']){
//                       $mallinfo['income_typeid']=2;
//                       $mallinfo['income_type'] ='宽带费收入';
//                       $mallinfo['income_num']=$data['netPrice'];
//                   }
//                   addMall($mallinfo);
//                   $mallinfo['income_typeid'] = $data['rent_rate'];
//                   if($mallinfo['income_typeid'] = $data['rent_rate']){
//                       $mallinfo['income_typeid']=3;
//                       $mallinfo['income_type'] ='租赁费收入';
//                       $mallinfo['income_num']=$data['rent_rate'];
//                   }
//                   addMall($mallinfo);
//                   $data['income_typeid'] = $data['managePrice'];
//                   if($mallinfo['income_typeid'] = $data['managePrice']){
//                       $mallinfo['income_typeid']=4;
//                       $mallinfo['income_type'] ='管理费收入';
//                       $mallinfo['income_num']=$data['managePrice'];
//                   }
//                   addMall($mallinfo);
//
//
//
//               Ret(array('code'=>1,'info'=>'缴费成功'));
//            }else{
//                Ret(array('code'=>2,'info'=>'缴费失败'));
//            }
//
//        }
        //card_typeid=3 为机构员工卡
        //var_dump($cardInfo['card_typeid']);die;
        if ($cardInfo['card_typeid'] == 3) {
            //echo('123');
            $staff = getStaff($cardInfo['card_number']);

            $res['cardnumber_no'] = $staff[0]['cardnumber_no'];
            $res['balance'] = $staff[0]['balance'];
            $res['card_number'] = $staff[0]['card_number'];
            $res['id'] = $staff[0]['id'];
            $res['name'] = $staff[0]['name'];
            if ($res['balance'] < $data['total']) {
                Ret(array('code' => 2, 'info' => '余额不足'));
            } else {
                $res['balance'] = $res['balance'] - $data['total'];
            }
            $instInfo=D('Institution')->where('id='.$staff[0]['institution_id'])->select();

            if (updateBalance($res)) {
                $cads['cardnumber_no'] = $res['cardnumber_no'];
                $cads['card_number'] = $res['card_number'];
                $cads['card_ownerid'] = $res['id'];
                $cads['card_owner_name']=$res['name'];
                //$cads['institution_id']=$staff[0]['institution_id']
                $cads['time'] = date('Y-m-d H:i:s');
                $cads['card_typeid'] = 3;
                $cads['submitter_id']=session("worker_id");
                $cads['submitter']=session("worker_name");
                $cads['institution_id']=$staff[0]['institution_id'];
                $cads['institution_name']=$instInfo[0]['name'];
                $cads['start_time']=session('instFeeStartTime');
                $cads['end_time']=session('instFeeEndTime');

                if ($data['property_price']!=0) {
                    $cads['cost_typeid'] = 1;
                    $cads['cost_type'] = '物业费用';
                    $cads['content']='物业费用';
                    $cads['money'] = $data['property_price'];
                    $cads['cost_content_id'] = 1;
                    $cads['cost_content_name'] = '物业费用';
                    $cads['time_long']=session('instFeeTimeLengthDay');
                    $cads['time_long_unit']='天';
                    D('InstitutionOutcome')->add($cads);
                }
                //addFees($cads);
                //$data['cost_typeid'] = $data['netPrice'];
                if ($data['netPrice']!=0) {
                    $cads['cost_typeid'] = 2;
                    $cads['cost_type'] = '宽带费用';
                    $cads['money'] = $data['netPrice'];
                    $cads['content']='宽带费用';
                    $cads['cost_content_id'] = 2;
                    $cads['cost_content_name'] = '宽带费用';
                    $cads['time_long']=session('instFeeTimeLengthMonth');
                    $cads['time_long_unit']='月';
                    D('InstitutionOutcome')->add($cads);
                }
                //addFees($cads);
                //$data['cost_typeid'] = $data['rent_rate'];
                if ($data['rent_rate']!=0) {
                    $cads['cost_typeid'] = 3;
                    $cads['cost_type'] = '租赁费用';
                    $cads['money'] = $data['rent_rate'];
                    $cads['content']='租赁费用';
                    $cads['cost_content_id'] = 3;
                    $cads['cost_content_name'] = '租赁费用';
                    $cads['time_long']=session('instFeeTimeLengthDay');
                    $cads['time_long_unit']='天';
                    D('InstitutionOutcome')->add($cads);
                }
               // addFees($cads);
               // $data['cost_typeid'] = $data['managePrice'];
                if ($data['managePrice']!=0) {
                    $cads['cost_typeid'] = 4;
                    $cads['cost_type'] = '管理费用';
                    $cads['money'] = $data['managePrice'];
                    $cads['content']='管理费用';
                    $cads['cost_content_id'] = 3;
                    $cads['cost_content_name'] = '管理费用';
                    $cads['time_long']=session('instFeeTimeLengthMonth');
                    $cads['time_long_unit']='月';
                    D('InstitutionOutcome')->add($cads);
                }
                //addFees($cads);


                $mallinfo['income_ownerid'] = $staff[0]['institution_id'];
                $mallinfo['income_ownername'] =$instInfo[0]['name'];
                $mallinfo['income_time'] = date('Y-m-d H:i:s');
                $mallinfo['income_ownertypeid'] = 2;
                $mallinfo['income_ownertype'] = '机构';
                $mallinfo['start_time']=session('instFeeStartTime');
                $mallinfo['end_time']=session('instFeeEndTime');
                //$mallinfo['time_long']=session('instFeeTimeLength');
                $mallinfo['cardnumber_no'] = $res['cardnumber_no'];
                $mallinfo['card_number'] = $res['card_number'];
                $mallinfo['card_ownerid'] = $res['id'];
                $mallinfo['card_owner_name']=$res['name'];
                $mallinfo['card_typeid'] = 3;
                if ( $data['property_price']!=0) {
                    $mallinfo['income_typeid'] = 1;
                    $mallinfo['income_type'] = '物业费收入';
                    $mallinfo['income_num'] = $data['property_price'];
                    $mallinfo['submit_id'] = session("worker_id");
                    $mallinfo['submitter'] = session("worker_name");
                    $mallinfo['income_content_id']=1;
                    $mallinfo['income_content_name']='物业费收入';
                    $mallinfo['time_long']=session('instFeeTimeLengthDay');
                    $mallinfo['time_long_unit']='天';
                    addMall($mallinfo);
                }

                //$data['income_typeid'] = $data['netPrice'];
                if ($data['netPrice']!=0) {
                    $mallinfo['income_typeid'] = 2;
                    $mallinfo['income_type'] = '宽带费收入';
                    $mallinfo['income_num'] = $data['netPrice'];
                    $mallinfo['submit_id'] = session("worker_id");
                    $mallinfo['submitter'] = session("worker_name");
                    $mallinfo['income_content_id']=2;
                    $mallinfo['income_content_name']='宽带费收入';
                    $mallinfo['time_long']=session('instFeeTimeLengthMonth');
                    $mallinfo['time_long_unit']='月';
                    addMall($mallinfo);
                }
                //addMall($mallinfo);
                //$mallinfo['income_typeid'] = $data['rent_rate'];
                if ($data['rent_rate']!=0) {
                    $mallinfo['income_typeid'] = 3;
                    $mallinfo['income_type'] = '租赁费收入';
                    $mallinfo['income_num'] = $data['rent_rate'];
                    $mallinfo['submit_id'] = session("worker_id");
                    $mallinfo['submitter'] = session("worker_name");
                    $mallinfo['income_content_id']=3;
                    $mallinfo['income_content_name']='租赁费收入';
                    $mallinfo['time_long']=session('instFeeTimeLengthDay');
                    $mallinfo['time_long_unit']='天';
                    addMall($mallinfo);
                }
                //addMall($mallinfo);
                //$data['income_typeid'] = $data['managePrice'];
                if ($data['managePrice']!=0) {
                    $mallinfo['income_typeid'] = 4;
                    $mallinfo['income_type'] = '管理费收入';
                    $mallinfo['income_num'] = $data['managePrice'];
                    $mallinfo['submit_id'] = session("worker_id");
                    $mallinfo['submitter'] = session("worker_name");
                    $mallinfo['income_content_id']=4;
                    $mallinfo['income_content_name']='管理费收入';
                    $mallinfo['time_long']=session('instFeeTimeLengthMonth');
                    $mallinfo['time_long_unit']='月';
                    addMall($mallinfo);
                }
                Ret(array('code' => 1, 'info' => '缴费成功'));
               // addFees($cads);
            } else {
                Ret(array('code' => 2, 'info' => '缴费失败'));
            }
        }
    }

    /**
     * 课程缴费
     */
    public function courseFeesPay()
    {
        $data['card_number'] = I('card_number');
        //缴费收入
        $data['total'] = I('total');
        $data['institutions_id'] = I('institutions_id');
        $data['institutions_name'] = I('institutions_name');
        $data['course_name'] = I('course_name');
        //var_dump($data['course_name']);die;
        $data['course_id'] = I('course_id');
        $data['course_card_id'] = I('course_card_id');
        $data['number'] = I('number');
        $data['course_card_name'] = I('course_card_name');
        $data['start_time']=I('start_time');
        $data['end_time']=I('end_time');

        foreach ($data as $k => $val){
            if(empty($val)){
                Ret(array('code' => 3, 'info' => $k .'不能为空！'));
            }
        }
        $card = getCard($data['card_number']);
        $cardInfo['card_typeid'] = $card[0]['card_typeid'];
        $cardInfo['card_state'] = $card[0]['card_state'];
        $cardInfo['cardnumber_no'] = $card[0]['cardnumber_no'];
        $cardInfo['card_number'] = $card[0]['card_number'];
        if (!$cardInfo['card_number']) {
            Ret(array('code' => 2, 'info' => '卡号不存在'));
        }

        $typeArr = [1 => 'member', 2 => 'institution_staff', 3 => 'mall_staff', 4 => 'merchant_staff'];
        foreach ($typeArr as $key => $v) {
            $member = D($v);
            $obj = $member->where(['card_number' => $data['card_number']])->find();
            if (!empty($obj)) {
                $card = $v;
                $type = $key;
                $model = $obj;
            }
        }

        if(($model['balance'] - $data['total']) < 0) {
            Ret(array('code' => 2, 'info' => '余额不足'));
        }

        if(!session("worker_id")){
            Ret(array('code' => 3, 'info' => '请登录！'));
        }
        $carModel = D($card);
        $amount = $model['balance'] - $data['total'];
        $res = $carModel->where(['card_number' => $data['card_number']])->save(['balance'=>$amount]);

        //获取课程卡的信息
        $courseCardInfo=D('CourseCard')->where('id='.$data['course_card_id'])->find();
        //var_dump($courseCardInfo);die;
        $timeLength=getDayNum($data['start_time'], $data['end_time']);
        //存入购买课程信息表中
        $courseBuyInfo['buyer_id']=$model['id'];
        $courseBuyInfo['buyer_type_id']=1;
        $courseBuyInfo['buyer_type']='会员';
        $courseBuyInfo['buyer_name']=$model['name'];
        if($card=='institution_staff')
        {
            $courseBuyInfo['buyer_type_id']=3;
            $courseBuyInfo['buyer_type']='机构员工';
        }
        if($card=='mall_staff')
        {
            $courseBuyInfo['buyer_type_id']=2;
            $courseBuyInfo['buyer_type']='商场员工';
        }
        if($card=='merchant_staff')
        {
            $courseBuyInfo['buyer_type_id']=4;
            $courseBuyInfo['buyer_type']='商户员工';
        }
        $courseBuyInfo['buyer_card_number']=$cardInfo['card_number'];
        $courseBuyInfo['buyer_card_numberNo']=$cardInfo['cardnumber_no'];
        $courseBuyInfo['course_id']=$data['course_id'];
        $courseBuyInfo['course_name']=$data['course_name'];
        $courseBuyInfo['course_card_id']=$data['course_card_id'];
        $courseBuyInfo['course_card_name']=$data['course_card_name'];
        $courseBuyInfo['course_card_typeid']=$courseCardInfo['price_typeid'];
        $courseBuyInfo['course_card_type']=$courseCardInfo['price_type'];
        $courseBuyInfo['validate_start_time']=$data['start_time'];
        $courseBuyInfo['validate_end_time']=$data['end_time'];
        $courseValidateTimes=$data['number']*$courseCardInfo['all_count'];
        $courseBuyInfo['validate_times']=$courseValidateTimes;
        $courseBuyInfo['used_times']=0;
        $courseBuyInfo['unused_times']=$courseValidateTimes;
        $courseBuyInfo['fee_times']=$courseCardInfo['validity_ttimes']*$data['number'];
        $courseBuyInfo['gift_times']=$courseCardInfo['gifts']*$data['number'];
        $courseBuyInfo['buy_num']=$data['number'];

        //该课程是否为年卡
        if($courseCardInfo['price_typeid']==4)
        {
            $coursePriceValue=$data['total']*0.7/$courseBuyInfo['fee_times'];
            //var_dump($coursePriceValue);die;
            $coursePrice=round($coursePriceValue,2);
            //var_dump($coursePrice);die;
            $courseBuyInfo['course_price']=$coursePrice;
            //var_dump($courseBuyInfo);die;
            //课程总价格
            $courseBuyInfo['course_sum']=$data['total'];
            //课程累计消费金额
            $courseBuyInfo['course_cost_sum']=$data['total']*0.3;
            //课程累计剩余金额
            $courseBuyInfo['course_remain_sum']=$data['total']*0.7;
            D('CourseBuyDetail')->add($courseBuyInfo);
            //$Model = M();
            //$sql="select * from course_buy_detail order by desc limit 1";
            $newCourseBuyInfo=D('CourseBuyDetail')->max('id');
            //var_dump($newCourseBuyInfo);die;
            // 机构收入
            $institutionsMallRevenueModel = D('InstitutionsMallRevenue');
            $institutions['income_ownertypeid'] = $courseBuyInfo['buyer_type_id'];
            $institutions['income_ownertype'] = $courseBuyInfo['buyer_type'];
            $institutions['income_ownerid'] = $model['id'];
            $institutions['income_ownername'] = $model['name'];
            $institutions['income_typeid'] = 2;
            $institutions['income_type'] = "课程包收入";
            $institutions['income_time'] = date("Y-m-d H:i:s");
            //机构收入金额
            //$institutions['income_num'] = $data['total'];
            //实际收入金额
            $institutions['income_num'] = $data['total']*0.3;
            $institutions['institutions_id'] = $data['institutions_id'];
            $institutions['institutions_name'] = $data['institutions_name'];
            $institutions['course_card_name'] = $data['course_card_name'];
            $institutions['course_card_id'] = $data['course_card_id'];
            $institutions['course_id'] = $data['course_id'];
            $institutions['course_name'] = $data['course_name'];;
            $institutions['submit_id'] = session("worker_id");
            $institutions['submitter'] = session("worker_name");
            //课程包ID
            $institutions['number'] = $data['number'];
            $institutions['validation_start_time']=$data['start_time'];
            $institutions['validation_end_time']=$data['end_time'];
            //课程包包含的总次数（包含购买次数+赠送次数）
            $institutions['validate_course_times']=$courseCardInfo['all_count'];
            //课程包的单次价格
            $institutions['course_price']=$coursePrice;
            //机构应收金额
            $institutions['income_receive_num']=$data['total'];
            $institutions['card_number']=$data['card_number'];
            $institutions['card_numberNo']=$cardInfo['cardnumber_no'];
            $institutions['course_buy_detail_id']=$newCourseBuyInfo[0]['id'];

            //会员卡消费
            $expenseDetailModel = D('ExpenseDetail');
            $expenseDetail['cardnumber_no'] = $cardInfo['cardnumber_no'];
            $expenseDetail['cost_typeid'] = 5;
            $expenseDetail['cost_type'] = "课程包费用-" . $data['course_card_name'];
            $expenseDetail['card_ownerid'] = $model['id'];
            $expenseDetail['card_owner_name'] = $model['name'];
            $expenseDetail['card_rechargenum'] = $data['total'];
            $expenseDetail['card_rechargetime'] = date("Y-m-d H:i:s");
            $expenseDetail['card_typeid'] = $courseBuyInfo['buyer_type_id'];
            $expenseDetail['submit_id'] = session("worker_id");
            $expenseDetail['submitter'] = session("worker_name");
            //课程购买数量
            $expenseDetail['number'] = $data['number'];
            $expenseDetail['cost_content_id']=$data['course_card_id'];
            $expenseDetail['cost_content_name']=$data['course_card_name'];
            $expenseDetail['card_type']=$courseBuyInfo['buyer_type'];
            $expenseDetail['course_buy_detail_id']=$newCourseBuyInfo[0]['id'];
            $expenseDetail['card_number']=$data['card_number'];
            $expenseDetail['expense_owner_id']=$data['institutions_id'];
            $expenseDetail['expense_owner_name']=$data['institutions_name'];
            $expenseDetail['expense_owner_typeid']=1;
            //1为机构，2 为商户
            $expenseDetail['expense_owner_type']='机构';

            $res1 = $institutionsMallRevenueModel->data($institutions)->add();
            $res2 = $expenseDetailModel->data($expenseDetail)->add();

            //商场收入
            //缴费会员ID
            $mallRevenueInfo['income_ownerid']=$model['id'];
            //缴费会员名字
            $mallRevenueInfo['income_ownername']=$model['name'];
            $mallRevenueInfo['income_typeid']=5;
            $mallRevenueInfo['income_type']="课程包费收入";
            //缴费收入金额
            $mallRevenueInfo['income_num']=$data['total']*0.7;
            $mallRevenueInfo['income_time']=date("Y-m-d H:i:s");
            //收入对象的ID
            $mallRevenueInfo['income_content_id']=$data['course_card_id'];
            //收入对象的名称
            $mallRevenueInfo['income_content_name']=$data['course_card_name'];
            $mallRevenueInfo['income_ownertypeid']=$courseBuyInfo['buyer_type_id'];
            $mallRevenueInfo['income_ownertype']=$courseBuyInfo['buyer_type'];
            $mallRevenueInfo['start_time']=$data['start_time'];
            $mallRevenueInfo['end_time']=$data['end_time'];

            $mallRevenueInfo['time_long']=$timeLength;
            $mallRevenueInfo['time_long_unit']="天";
            $mallRevenueInfo['cardnumber_no']=$cardInfo['cardnumber_no'];
            $mallRevenueInfo['card_number']=$data['card_number'];
            $mallRevenueInfo['card_typeid']=$courseBuyInfo['buyer_type_id'];
            $mallRevenueInfo['card_ownerid']=$model['id'];
            $mallRevenueInfo['card_owner_name']=$model['name'];
            $mallRevenueInfo['submitter']=session("worker_name");
            $mallRevenueInfo['submitter_id']=session("worker_id");
            $mallRevenueInfo['course_buy_detail_id']=$newCourseBuyInfo[0]['id'];

            D('MallRevenue')->add($mallRevenueInfo);

            if ($res && $res1 && $res2) {
                Ret(array('code' => 1, 'info' => '缴费成功！'));
            }
            Ret(array('code' => 2, 'info' => '缴费失败！'));
        }
        else
        {
            $coursePriceValue=$data['total']/$courseBuyInfo['fee_times'];
            //var_dump($coursePriceValue);die;
            $coursePrice=round($coursePriceValue,2);
            //var_dump($coursePrice);die;
            $courseBuyInfo['course_price']=$coursePrice;
            //var_dump($courseBuyInfo);die;
            //课程总价格
            $courseBuyInfo['course_sum']=$data['total'];
            //课程累计消费金额
            $courseBuyInfo['course_cost_sum']=0;
            //课程累计剩余金额
            $courseBuyInfo['course_remain_sum']=$data['total'];
            D('CourseBuyDetail')->add($courseBuyInfo);
            //$Model = M();
            //$sql="select * from course_buy_detail order by desc limit 1";
            //$newCourseBuyInfo=$Model->query($sql);
            $newCourseBuyInfo=D('CourseBuyDetail')->max('id');
            //var_dump($newCourseBuyInfo);die;
            // 机构收入
            $institutionsMallRevenueModel = D('InstitutionsMallRevenue');
            $institutions['income_ownertypeid'] = $courseBuyInfo['buyer_type_id'];
            $institutions['income_ownertype'] = $courseBuyInfo['buyer_type'];
            $institutions['income_ownerid'] = $model['id'];
            $institutions['income_ownername'] = $model['name'];
            $institutions['income_typeid'] = 2;
            $institutions['income_type'] = "课程包收入";
            $institutions['income_time'] = date("Y-m-d H:i:s");
            //机构消费金额
            //$institutions['income_num'] = $data['total'];
            $institutions['income_num'] = 0;
            $institutions['institutions_id'] = $data['institutions_id'];
            $institutions['institutions_name'] = $data['institutions_name'];
            $institutions['course_card_name'] = $data['course_card_name'];
            $institutions['course_card_id'] = $data['course_card_id'];
            $institutions['course_id'] = $data['course_id'];
            $institutions['course_name'] = $data['course_name'];
            $institutions['submit_id'] = session("worker_id");
            $institutions['submitter'] = session("worker_name");
            $institutions['number'] = $data['number'];
            $institutions['validation_start_time']=$data['start_time'];
            $institutions['validation_end_time']=$data['end_time'];
            //课程包包含的总次数（包含购买次数+赠送次数）
            $institutions['validate_course_times']=$courseCardInfo['all_count'];
            //课程包的单次价格
            $institutions['course_price']=$coursePrice;
            //机构应收金额
            $institutions['income_receive_num']=$data['total'];
            $institutions['card_number']=$data['card_number'];
            $institutions['card_numberNo']=$cardInfo['cardnumber_no'];
            $institutions['course_buy_detail_id']=$newCourseBuyInfo[0]['id'];

            //会员卡消费
            $expenseDetailModel = D('ExpenseDetail');
            $expenseDetail['cardnumber_no'] = $cardInfo['cardnumber_no'];
            $expenseDetail['cost_typeid'] = 5;
            $expenseDetail['cost_type'] = "课程包费用-" . $data['course_card_name'];
            $expenseDetail['card_ownerid'] = $model['id'];
            $expenseDetail['card_owner_name'] = $model['name'];
            $expenseDetail['card_rechargenum'] = $data['total'];
            $expenseDetail['card_rechargetime'] = date("Y-m-d H:i:s");
            $expenseDetail['card_typeid'] = $courseBuyInfo['buyer_type_id'];
            $expenseDetail['submit_id'] = session("worker_id");
            $expenseDetail['submitter'] = session("worker_name");
            //课程购买数量
            $expenseDetail['number'] = $data['number'];
            $expenseDetail['cost_content_id']=$data['course_card_id'];
            $expenseDetail['cost_content_name']=$data['course_card_name'];
            $expenseDetail['card_type']=$courseBuyInfo['buyer_type'];
            $expenseDetail['course_buy_detail_id']=$newCourseBuyInfo[0]['id'];
            $expenseDetail['card_number']=$data['card_number'];
            $expenseDetail['expense_owner_id']=$data['institutions_id'];
            $expenseDetail['expense_owner_name']=$data['institutions_name'];
            $expenseDetail['expense_owner_typeid']=1;
            //1为机构，2 为商户
            $expenseDetail['expense_owner_type']='机构';

            $res1 = $institutionsMallRevenueModel->data($institutions)->add();
            $res2 = $expenseDetailModel->data($expenseDetail)->add();

            //商场收入
            //缴费会员ID
            $mallRevenueInfo['income_ownerid']=$model['id'];
            //缴费会员名字
            $mallRevenueInfo['income_ownername']=$model['name'];
            $mallRevenueInfo['income_typeid']=5;
            $mallRevenueInfo['income_type']="课程包费收入";
            //缴费收入金额
            $mallRevenueInfo['income_num']=$data['total'];
            $mallRevenueInfo['income_time']=date("Y-m-d H:i:s");
            //收入对象的ID
            $mallRevenueInfo['income_content_id']=$data['course_card_id'];
            //收入对象的名称
            $mallRevenueInfo['income_content_name']=$data['course_card_name'];
            $mallRevenueInfo['income_ownertypeid']=$courseBuyInfo['buyer_type_id'];
            $mallRevenueInfo['income_ownertype']=$courseBuyInfo['buyer_type'];
            $mallRevenueInfo['start_time']=$data['start_time'];
            $mallRevenueInfo['end_time']=$data['end_time'];

            $mallRevenueInfo['time_long']=$timeLength;
            $mallRevenueInfo['time_long_unit']="天";
            $mallRevenueInfo['cardnumber_no']=$cardInfo['cardnumber_no'];
            $mallRevenueInfo['card_number']=$data['card_number'];
            $mallRevenueInfo['card_typeid']=$courseBuyInfo['buyer_type_id'];
            $mallRevenueInfo['card_ownerid']=$model['id'];
            $mallRevenueInfo['card_owner_name']=$model['name'];
            $mallRevenueInfo['submitter']=session("worker_name");
            $mallRevenueInfo['submitter_id']=session("worker_id");
            $mallRevenueInfo['course_buy_detail_id']=$newCourseBuyInfo[0]['id'];

            D('MallRevenue')->add($mallRevenueInfo);


            if ($res && $res1 && $res2) {
                Ret(array('code' => 1, 'info' => '缴费成功！'));
            }
            Ret(array('code' => 2, 'info' => '缴费失败！'));
        }

        /*** if ($cardInfo['card_typeid'] == 4) {
            $chant = getChantStaff($cardInfo['card_number']);
            var_dump($chant);die;
            $res['cardnumber_no'] = $chant[0]['cardnumber_no'];
            $res['balance'] = $chant[0]['balance'];
            $res['card_number'] = $chant[0]['card_number'];
            $res['id'] = $chant[0]['id'];
            $res['name'] = $chant[0]['name'];
//            var_dump($res);die;
            if ($res['balance'] < $data['total']) {
                Ret(array('code' => 2, 'info' => '余额不足'));
            } else {
                $res['balance'] = $res['balance'] - $data['total'];
            }
            if (updateChantBalance($res)) {
                $cads['cardnumber_no'] = $res['cardnumber_no'];
                $cads['card_ownerid'] = $res['id'];
                $cads['card_rechargetime'] = date('Y-m-d H:i:s');
                $cads['card_typeid'] = 2;
                $cads['cost_typeid'] = 5;
                $cads['cost_type'] = '课程包费用';
                $cads['card_rechargenum'] = $data['total'];
                addFees($cads);
                $mallinfo['income_ownerid'] = $res['id'];
                $mallinfo['income_ownername'] = $res['name'];
                $mallinfo['income_time'] = date('Y-m-d H:i:s');
                $mallinfo['income_ownertypeid'] = 2;
                $mallinfo['income_ownertype'] = '商户';
                $mallinfo['income_typeid'] = 5;
                $mallinfo['income_type'] = '课程包费用';
                $mallinfo['income_num'] = $data['total'];
                addMall($mallinfo);
                Ret(array('code' => 1, 'info' => '缴费成功'));
            } else {
                Ret(array('code' => 2, 'info' => '缴费失败'));
            }

        }
        if ($cardInfo['card_typeid'] == 3) {
            $staff = getStaff($cardInfo['card_number']);
//            var_dump($staff);die;
            $res['cardnumber_no'] = $staff[0]['cardnumber_no'];
            $res['balance'] = $staff[0]['balance'];
            $res['card_number'] = $staff[0]['card_number'];
            $res['id'] = $staff[0]['id'];
            $res['name'] = $staff[0]['name'];
            if ($res['balance'] < $data['total']) {

            if (updateBalance($res)) {
                $cads['cardnumber_no'] = $res['cardnumber_no'];
                $cads['card_ownerid'] = $res['id'];
                $cads['card_rechargetime'] = date('Y-m-d H:i:s');
                $cads['card_typeid'] = 3;
                $cads['cost_typeid'] = 5;
                $cads['cost_type'] = '课程包费用';
                $cads['card_rechargenum'] = $data['total'];

                addFees($cads);

                $mallinfo['income_ownerid'] = $res['id'];
                $mallinfo['income_ownername'] = $res['name'];
                $mallinfo['income_time'] = date('Y-m-d H:i:s');
                $mallinfo['income_ownertypeid'] = 1;
                $mallinfo['income_ownertype'] = '机构';
                $mallinfo['income_typeid'] = 5;
                $mallinfo['income_type'] = '课程包费用';
                $mallinfo['income_num'] = $data['total'];
                addMall($mallinfo);
                Ret(array('code' => 1, 'info' => '缴费成功'));
            } else {
                Ret(array('code' => 2, 'info' => '缴费失败'));
            }
        }

        if ($cardInfo['card_typeid'] == 1) {
            $staff = getStaff($cardInfo['card_number']);
//            var_dump($staff);die;
            $res['cardnumber_no'] = $staff[0]['cardnumber_no'];
            $res['balance'] = $staff[0]['balance'];
            $res['card_number'] = $staff[0]['card_number'];
            $res['id'] = $staff[0]['id'];
            $res['name'] = $staff[0]['name'];
            if ($res['balance'] < $data['total']) {
                Ret(array('code' => 2, 'info' => '余额不足'));
            } else {
                $res['balance'] = $res['balance'] - $data['total'];
            }
            if (updateBalance($res)) {
                $cads['cardnumber_no'] = $res['cardnumber_no'];
                $cads['card_ownerid'] = $res['id'];
                $cads['card_rechargetime'] = date('Y-m-d H:i:s');
                $cads['card_typeid'] = 3;
                $cads['cost_typeid'] = 5;
                $cads['cost_type'] = '课程包费用';
                $cads['card_rechargenum'] = $data['total'];
                addFees($cads);
                $mallinfo['income_ownerid'] = $res['id'];
                $mallinfo['income_ownername'] = $res['name'];
                $mallinfo['income_time'] = date('Y-m-d H:i:s');
                $mallinfo['income_ownertypeid'] = 3;
                $mallinfo['income_ownertype'] = '会员';
                $mallinfo['income_typeid'] = 5;
                $mallinfo['income_type'] = '课程包费用';
                $mallinfo['income_num'] = $data['total'];
                addMall($mallinfo);
                Ret(array('code' => 1, 'info' => '缴费成功'));
            } else {
                Ret(array('code' => 2, 'info' => '缴费失败'));
            }
        }**/
    }

    public function getCourseCard()
    {
        $course_id = I('course_id');
        if (empty($course_id)) {
            Ret(array('code' => 2, 'info' => 'course_id不能为空！'));
        }

        $result = D("CourseCard")->where("course_id={$course_id}")->select();

        $data = [];
        if (!empty($result)) {
            foreach ($result as $v) {
                $tmp = ['id' => $v['id'],'name' => $v['name']];
                array_push($data,$tmp);
            }
        }
        Ret(array('code' => 1, 'data' => $data));

    }

    //机构缴费明细
    public function inst_payment_list()
    {
        $start_time = I('start_time');
        $end_time = I('end_time');
        $institution_id = I('institution_id', 0, 'intval');
        $page = I('page', 1, 'intval');
        $pagesize = I('pagesize', 10, 'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page . ',' . $pagesize;
        $instPaymentDetailModel = D('InstPaymentDetail');
        $InstModel = D('Institution');
        $InstStaff = D('InstStaff');
        $count = $instPaymentDetailModel->get_count($start_time, $end_time, $institution_id);
        //var_dump($count);die;
        $res = $instPaymentDetailModel->get_list($start_time, $end_time, $institution_id, $page);
        if ($res) {
            Ret(array('code' => 1, 'data' => $res, 'total' => $count, 'page_count' => ceil($count / $pagesize)));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据'));
        }
    }
    //机构缴费支付接口
    public function inst_payment()
    {
        //机构缴费订单ID
        $data['id'] = I('instPaymentDetailId');
        //机构缴费类别 1:微信 2：支付宝 3：刷卡 4：现金 5:转账
        $instPaymentMethod=I('instPaymentMethod');
        //机构缴费金额
        $instPayment=I('instPayment');
        $instPaymentDetailInfo=D('InstPaymentDetail')->getInstPaymentDetailById($data['id']);
        if(!empty($instPayment) && $instPayment!=0)
        {
            if($instPaymentMethod==1)
            {
                if ($instPaymentDetailInfo[0]['sum_payment'] == 0)
                {
                    $data['weixin_payment'] = $instPayment;
                    $data['sum_payment']=$instPayment;
                    if ($instPayment == $instPaymentDetailInfo[0]['sum_price'])
                    {
                        //0:未支付 1：支付完全 2：支付部分
                        $data['payment_state'] =1;
                    }
                    elseif($instPayment>0 && $instPayment<$instPaymentDetailInfo[0]['sum_price'])
                    {
                        $data['payment_state'] =2;
                    }
                    $data['debt_payment']=$instPaymentDetailInfo[0]['sum_price']-$data['sum_payment'];
                }
                else
                {
                    $data['weixin_payment'] = $instPayment;
                    $data['sum_payment']=$instPaymentDetailInfo[0]['sum_payment']+$data['weixin_payment'];
                    $data['debt_payment']=$instPaymentDetailInfo[0]['sum_price']-$data['sum_payment'];
                    if($data['sum_payment']==$instPaymentDetailInfo[0]['sum_price'])
                    {
                        $data['payment_state'] =1;
                    }

                }
            }
            else if($instPaymentMethod==2)
            {
                if ($instPaymentDetailInfo[0]['sum_payment'] == 0)
                {
                    $data['alipay_payment'] = $instPayment;
                    $data['sum_payment']=$instPayment;
                    if ($instPayment == $instPaymentDetailInfo[0]['sum_price'])
                    {
                        //0:未支付 1：支付完全 2：支付部分
                        $data['payment_state'] =1;
                    }
                    elseif($instPayment>0 && $instPayment<$instPaymentDetailInfo[0]['sum_price'])
                    {
                        $data['payment_state'] =2;
                    }
                    $data['debt_payment']=$instPaymentDetailInfo[0]['sum_price']-$data['sum_payment'];
                }
                else
                {
                    $data['alipay_payment'] = $instPayment;
                    $data['sum_payment']=$instPaymentDetailInfo[0]['sum_payment']+$data['alipay_payment'];
                    $data['debt_payment']=$instPaymentDetailInfo[0]['sum_price']-$data['sum_payment'];
                    if($data['sum_payment']==$instPaymentDetailInfo[0]['sum_price'])
                    {
                        $data['payment_state'] =1;
                    }
                }
            }
            else if($instPaymentMethod==3)
            {
                if ($instPaymentDetailInfo[0]['sum_payment'] == 0)
                {
                   // echo ('11111');
                    $data['card_payment'] = $instPayment;
                    $data['sum_payment']=$instPayment;
                    if ($instPayment == $instPaymentDetailInfo[0]['sum_price'])
                    {
                        //0:未支付 1：支付完全 2：支付部分
                        $data['payment_state'] =1;
                    }
                    elseif($instPayment>0 && $instPayment<$instPaymentDetailInfo[0]['sum_price'])
                    {
                        $data['payment_state'] =2;
                    }
                    $data['debt_payment']=$instPaymentDetailInfo[0]['sum_price']-$data['sum_payment'];
                }
                else
                {
                    $data['card_payment'] = $instPayment;
                    $data['sum_payment']=$instPaymentDetailInfo[0]['sum_payment']+$data['card_payment'];
                    $data['debt_payment']=$instPaymentDetailInfo[0]['sum_price']-$data['sum_payment'];
                    if($data['sum_payment']==$instPaymentDetailInfo[0]['sum_price'])
                    {
                        $data['payment_state'] =1;
                    }
                }
            }
            else if($instPaymentMethod==4)
            {
                if ($instPaymentDetailInfo[0]['sum_payment'] == 0)
                {
                    $data['cash_payment'] = $instPayment;
                    $data['sum_payment']=$instPayment;
                    if ($instPayment == $instPaymentDetailInfo[0]['sum_price'])
                    {
                        //0:未支付 1：支付完全 2：支付部分
                        $data['payment_state'] =1;
                    }
                    elseif($instPayment>0 && $instPayment<$instPaymentDetailInfo[0]['sum_price'])
                    {
                        $data['payment_state'] =2;
                    }
                    $data['debt_payment']=$instPaymentDetailInfo[0]['sum_price']-$data['sum_payment'];
                }
                else
                {
                    $data['cash_payment'] = $instPayment;
                    $data['sum_payment']=$instPaymentDetailInfo[0]['sum_payment']+$data['cash_payment'];
                    $data['debt_payment']=$instPaymentDetailInfo[0]['sum_price']-$data['sum_payment'];
                    if($data['sum_payment']==$instPaymentDetailInfo[0]['sum_price'])
                    {
                        $data['payment_state'] =1;
                    }
                }
            }
            else
            {
                if ($instPaymentDetailInfo[0]['sum_payment'] == 0)
                {
                    $data['exchange_payment'] = $instPayment;
                    $data['sum_payment']=$instPayment;
                    if ($instPayment == $instPaymentDetailInfo[0]['sum_price'])
                    {
                        //0:未支付 1：支付完全 2：支付部分
                        $data['payment_state'] =1;
                    }
                    elseif($instPayment>0 && $instPayment<$instPaymentDetailInfo[0]['sum_price'])
                    {
                        $data['payment_state'] =2;
                    }
                    $data['debt_payment']=$instPaymentDetailInfo[0]['sum_price']-$data['sum_payment'];
                }
                else
                {
                    $data['exchange_payment'] = $instPayment;
                    $data['sum_payment']=$instPaymentDetailInfo[0]['sum_payment']+$data['exchange_payment'];
                    $data['debt_payment']=$instPaymentDetailInfo[0]['sum_price']-$data['sum_payment'];
                    if($data['sum_payment']==$instPaymentDetailInfo[0]['sum_price'])
                    {
                        $data['payment_state'] =1;
                    }
                }
            }
            $data['payment_time']=date('Y-m-d H:i:s');
            $res=D('InstPaymentDetail')->save($data);
            if($res)
            {
                Ret(array('code' => 1, 'info' => '支付成功'));
            }
            else
            {
                Ret(array('code' => 2, 'info' => '支付失败'));
            }

        }
        else
        {

            Ret(array('code' => 2, 'info' => '支付无效'));
        }


      /*  //机构物业费用
        $data['property_price'] = I('property_price');
        //机构宽带费用
        $data['netPrice'] = I('netPrice');
        //机构租赁费用
        $data['rent_rate'] = I('rent_rate');
        //机构管理费用
        $data['managePrice'] = I('managePrice');
        //机构合计
        $data['total'] = I('total');
        //charge_type_id:1为微信支付 2:支付宝支付 3：刷卡支付 4: 现金充值
        $data['charge_type_id']=I('charge_type_id');
        $data['charge_type']=I('charge_type');

        $mallinfo['income_ownerid'] = $data['inst_id'];
        $instName=D('Institution')->get_inst_by_id($data['inst_id']);
        $mallinfo['income_ownername'] =$instName[0]['name'];
        $mallinfo['income_time'] = date('Y-m-d H:i:s');
        $mallinfo['income_ownertypeid'] = 2;
        $mallinfo['income_ownertype'] = '机构';
        $mallinfo['start_time']=session('instFeeStartTime');
        $mallinfo['end_time']=session('instFeeEndTime');
        //将这些数据存到机构支出和商场收入明细中
        //将物业费用存入机构支出和商场收入明细中

        //$instPayment['sum_price']=$data['total'];
        if ($data['property_price']!=0) {
            $cads['cost_typeid'] = 1;
            $cads['cost_type'] = '物业费用';
            $cads['content']='物业费用';
            $cads['money'] = $data['property_price'];
            $cads['cost_content_id'] = 1;
            $cads['cost_content_name'] = '物业费用';
            $cads['time_long']=session('instFeeTimeLengthDay');
            $cads['time_long_unit']='天';
            $cads['charge_type_id']=$data['charge_type_id'];
            $cads['charge_type']=$data['charge_type'];
            D('InstitutionOutcome')->add($cads);




            $mallinfo['income_typeid'] = 1;
            $mallinfo['income_type'] = '物业费收入';
            $mallinfo['income_num'] = $data['property_price'];
            $mallinfo['submit_id'] = session("worker_id");
            $mallinfo['submitter'] = session("worker_name");
            $mallinfo['income_content_id']=1;
            $mallinfo['income_content_name']='物业费收入';
            $mallinfo['time_long']=session('instFeeTimeLengthDay');
            $mallinfo['time_long_unit']='天';
            $mallinfo['charge_type_id']=$data['charge_type_id'];
            $mallinfo['charge_type']=$data['charge_type'];
            addMall($mallinfo);
        }
        //将宽带费用存入机构支出明细中
        if ($data['netPrice']!=0) {
            $cads['cost_typeid'] = 2;
            $cads['cost_type'] = '宽带费用';
            $cads['money'] = $data['netPrice'];
            $cads['content']='宽带费用';
            $cads['cost_content_id'] = 2;
            $cads['cost_content_name'] = '宽带费用';
            $cads['time_long']=session('instFeeTimeLengthMonth');
            $cads['time_long_unit']='月';
            $cads['charge_type_id']=$data['charge_type_id'];
            $cads['charge_type']=$data['charge_type'];
            D('InstitutionOutcome')->add($cads);

            $mallinfo['income_typeid'] = 2;
            $mallinfo['income_type'] = '宽带费收入';
            $mallinfo['income_num'] = $data['netPrice'];
            $mallinfo['submit_id'] = session("worker_id");
            $mallinfo['submitter'] = session("worker_name");
            $mallinfo['income_content_id']=2;
            $mallinfo['income_content_name']='宽带费收入';
            $mallinfo['time_long']=session('instFeeTimeLengthMonth');
            $mallinfo['time_long_unit']='月';
            $mallinfo['charge_type_id']=$data['charge_type_id'];
            $mallinfo['charge_type']=$data['charge_type'];
            addMall($mallinfo);

            //$instPayment['net_price']=$data['netPrice'];

        }
        //将租赁费用存入机构支出明细中
        if ($data['rent_rate']!=0) {
            $cads['cost_typeid'] = 3;
            $cads['cost_type'] = '租赁费用';
            $cads['money'] = $data['rent_rate'];
            $cads['content']='租赁费用';
            $cads['cost_content_id'] = 3;
            $cads['cost_content_name'] = '租赁费用';
            $cads['time_long']=session('instFeeTimeLengthDay');
            $cads['time_long_unit']='天';
            $cads['charge_type_id']=$data['charge_type_id'];
            $cads['charge_type']=$data['charge_type'];
            D('InstitutionOutcome')->add($cads);

            $mallinfo['income_typeid'] = 3;
            $mallinfo['income_type'] = '租赁费收入';
            $mallinfo['income_num'] = $data['rent_rate'];
            $mallinfo['submit_id'] = session("worker_id");
            $mallinfo['submitter'] = session("worker_name");
            $mallinfo['income_content_id']=3;
            $mallinfo['income_content_name']='租赁费收入';
            $mallinfo['time_long']=session('instFeeTimeLengthDay');
            $mallinfo['time_long_unit']='天';
            $mallinfo['charge_type_id']=$data['charge_type_id'];
            $mallinfo['charge_type']=$data['charge_type'];
            addMall($mallinfo);

           // $instPayment['rent_price']=$data['rent_rate'];
        }
        //将管理费用存入机构支出明细中
        if ($data['managePrice']!=0) {
            $cads['cost_typeid'] = 4;
            $cads['cost_type'] = '管理费用';
            $cads['money'] = $data['managePrice'];
            $cads['content']='管理费用';
            $cads['cost_content_id'] = 3;
            $cads['cost_content_name'] = '管理费用';
            $cads['time_long']=session('instFeeTimeLengthMonth');
            $cads['time_long_unit']='月';
            $cads['charge_type_id']=$data['charge_type_id'];
            $cads['charge_type']=$data['charge_type'];
            D('InstitutionOutcome')->add($cads);

            $mallinfo['income_typeid'] = 4;
            $mallinfo['income_type'] = '管理费收入';
            $mallinfo['income_num'] = $data['managePrice'];
            $mallinfo['submit_id'] = session("worker_id");
            $mallinfo['submitter'] = session("worker_name");
            $mallinfo['income_content_id']=4;
            $mallinfo['income_content_name']='管理费收入';
            $mallinfo['time_long']=session('instFeeTimeLengthMonth');
            $mallinfo['time_long_unit']='月';
            $mallinfo['charge_type_id']=$data['charge_type_id'];
            $mallinfo['charge_type']=$data['charge_type'];
            addMall($mallinfo);

            //$instPayment['management_price']=$data['managePrice'];
        }
        if($instPayment['management_price'] && $instPayment['rent_price'] && $instPayment['net_price'] && $instPayment['property_price'])
        {

        }
        if($instPayment['management_price'] || $instPayment['rent_price'] || $instPayment['net_price'] || $instPayment['property_price'])
        {
            D('InstPaymentDetail')
        }
        //$mallinfo['time_long']=session('instFeeTimeLength');
        Ret(array('code' => 1, 'info' => '缴费成功'));
        */
    }
    //

}