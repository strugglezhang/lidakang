<?php
namespace Member\Controller;
use Member\Model\MemberModel;

class IndexController extends CommonController{

    //会员列表
    public function index_api(){
        before_api();
        checkLogin();
        checkAuth();
        $mall_id = session('mall.mall_id');
        $state = I('state',0,'intval');
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
        $memberModel = D('Member');
        $count = $memberModel->get_count($mall_id,$state,$keyword);
        if ($count) {
            $fields = 'id,pic,name,phone,parent_phone,weight,height,card_number';
            $res = $memberModel->get_list($mall_id,$state,$keyword,$page,$fields);

            if (empty($res)) {

                Ret(array('code' => 2, 'info' => '查无相关数据！'));
            }else{
//                foreach ($res as $key => $value) {
//                    $card = $this->get_card($value['id']);
//                    $res[$key]['bond_card']=$card['card_number'];
//                    $res[$key]['number'] = $value['id'];
//                }
                Ret(array('code' => 1, 'data' => $res, 'total' => $count, 'page_count' => ceil($count/$pagesize)));
            }
        }else{
            Ret(array('code' => 2, 'info' => '查无相关数据！'));
        }
    }
    private function get_card($member_id){
        $card=D('Member')->get_card($member_id);
        if(!$card){
            return '';
        }
        return $card;
    }
//会员详情
    public function member_view_api(){
        before_api();
        checkLogin();
        checkAuth();
        $id = I('id');
        if ($id < 1) {
            Ret(array('code' => 2, 'info' => '参数（会员
            号id）有误！'));
        }
        $fields = 'id,state,name,phone,parent_phone,pic,sex,id_number,birth_province_id,birth_city_id,birth_district_id,birth_address,marriage,health,blood_type,education,major,school,birthdate,address,weight,height,card_number,relations';
        $res = D('Member')->get_member_infos($id,$fields);

//        writeLog($res);
        if ($res) {
//            $con['pid']=$id;
//            $con['state']='1';
//            $card= D('CardBond')->where($con)->field('card_number')->find();
//            $res[0]['card_number'] = $card['card_number'];

            $res[0]['state'] = get_state($res[0]['state']);
            $res[0]['sex_name'] = get_sex($res[0]['sex']);
            $res[0]['education'] = get_education($res[0]['education']);
            $res[0]['marriage'] = get_marriage($res[0]['marriage']);
            $res[0]['health']=get_health($res[0]['health']);
            $res[0]['birth_address'] = $this->get_address($res[0]['birth_province_id'],$res[0]['birth_city_id'],$res[0]['birth_district_id']);
//            $json = $res[0]['relations']; //json格式的数组转换成 php的数组
//              var_dump($json);die;
//            $res = (Array)json_decode($json);
//            var_dump($res);die;
            Ret(array('code' => 1, 'data' => $res[0]));
        }else{
            Ret(array('code' => 2, 'info' => '没有相关的信息！'));
        }
    }
    private function get_address($province_id,$city_id,$district_id){
        $provinceModel = D('Province');
        $province = $provinceModel->get_addr($province_id);
        $cityModel = D('City');
        $city = $cityModel->get_addr($city_id);
        $districtModel = D('District');
        $district = $districtModel->get_addr($district_id);
        return $province[0]['name'].$city[0]['name'].$district[0]['name'];
}

    /*
     * 获取家长信息
     */
    private function get_relations($id){
        $data=D('Parent')->get_info($id);
        if ($data) {
            return $data;
        }else{
            return '';
        }
    }

    //会员增删改

    /**
     *
     */
    public function member_dml_api(){
        before_api();

        checkLogin();

        checkAuth();
        $mall_id = session('mall.mall_id');

        $flag = I('flag');
        $memberModel = D('Member');
        switch ($flag) {
            case 'add':
                if(!$mall_id){
                    Ret(array('code' => 2, 'info' => '获取登录信息失败！'));
                }
                $data['name']=I('name',0);
                if(!$data['name']){
                    Ret(array('code' => 2, 'info' => '请输入会员姓名！'));
                }
                $data['phone']=I('phone',0);
                if(!$data['phone']){
                    Ret(array('code' => 2, 'info' => '请输入电话号码！'));
                }
                $chekPhone=$this->checkPhone($data['phone']);
                if($chekPhone){
                    Ret(array('code' => 2, 'info' => '此电话已经注册！'));
                }
                if(!isMobile($data['phone'])){
                    Ret(array('code' => 2, 'info' => '请输入手机号码格式不正确！'));
                }
                $data['parent_phone']=I('parent_phone',0);
                if(!$data['parent_phone']){
                    Ret(array('code' => 2, 'info' => '请输入家长电话！'));
                }
                if(!isMobile($data['parent_phone'])){
                    Ret(array('code' => 2, 'info' => '请输入手机号码格式不正确！'));
                }
                $data['pic']=I('pic');
                $data['sex']=I('sex');
                if(!$data['sex']){
                    Ret(array('code' => 2, 'info' => '请选择性别！'));
                }
                $data['id_number']=I('id_number');
//                $checkID=$this->checkIDNo($data['id_number']);
//                if($checkID){
//                    Ret(array('code' => 2, 'info' => '此身份证已经注册！'));
//                }
//                if(!$data['id_number']){
//                    Ret(array('code' => 2, 'info' => '请输入身份证号码！'));
//                }
//                if(!isCreditNo($data['id_number'])){
//                    Ret(array('code' => 2, 'info' => '请输入身份证号码格式不正确！'));
//                }
                $data['birth_province_id']=I('birth_province_id');
                $data['birth_city_id']=I('birth_city_id');
                $data['birth_province_id']=I('birth_province_id');
                $data['height']=I('height');
                $data['weight']=I('weight');
                $data['birth_district_id']=I('birth_district_id');
                if($data['birth_province_id'] || $data['birth_city_id'] || $data['birth_district_id']){
                    $data['birth_address'] = $this->get_address($data['birth_province_id'] , $data['birth_city_id'] , $data['birth_district_id']);
                }
                $data['address']=I('address');
                if(!$data['address']){
                    Ret(array('code' => 2, 'info' => '请输入现住址！'));
                }
                $data['marriage']=I('marriage');
                if(!$data['marriage']){
                    Ret(array('code' => 2, 'info' => '请选择婚姻状况！'));
                }
                $data['blood_type']=I('blood_type');
                $data['birthdate']=I('birthdate');
                $data['health']=I('health');
                $data['education']=I('education');
                if(!$data['education']){
                    Ret(array('code' => 2, 'info' => '请选择学历！'));
                }
                $data['major']=I('major');
                if(!$data['major']){
                    Ret(array('code' => 2, 'info' => '请选择专业！'));
                }
                $data['card_number'] = I('card_number');
                $data['relations']=json_encode($_POST['relations']);
                $data['cardState'] = 1;
                $data['school']=I('school');
                if(!$data['school']){
                    Ret(array('code' => 2, 'info' => '请选择学校！'));
                }
                $data['cardnumber_no']=I('cardnumber_no');
                $data['password']=createPassword('888888');
                $data['app_pwd']=createPassword('888888');
                $data['state']=1;
                $res=hasSameCard($data['card_number']);
                //var_dump($res);die;
                if(hasSameCard($data['card_number'])==true)
                {
                    Ret(array('code' => 2, 'info' => '此卡被他人使用或被冻结挂失！'));
                }
                else
                {
                    $res = $memberModel->add_member($data);
                    if ($res) {

//                     var_dump($a);die;
                        $number = $res + 600000;
                        $memberModel->save(array('id' => $res, 'number' => $number));
                        $card['card_number'] = I('card_number');
                        $a = $this->setcard($res, $data);
                        $this->memberCardAuth($data,$res);
                        $feedback = '会员账号：' . $number . ',' . '密码：888888';
                        Ret(array('code' => 1, 'info' => $feedback));
                    }
                    else
                    {
                        Ret(array('code' => 2, 'info' => '添加失败,系统出错！'));
                    }
                }
                break;
            case 'update':
                $data['id'] = I('id',0,'intval');
                if ($data['id'] < 1) {
                    Ret(array('code' => 2, 'info' => '参数（会员id）有误！'));
                }
     
                if(!$mall_id){
                    Ret(array('code' => 2, 'info' => '获取登录信息失败！'));
                }
                $data['name']=I('name',0);
                if(!$data['name']){
                    Ret(array('code' => 2, 'info' => '请输入会员姓名！'));
                }
                $data['phone']=I('phone',0);
                if(!$data['phone']){
                    Ret(array('code' => 2, 'info' => '请输入电话号码！'));
                }
                if(!isMobile($data['phone'])){
                    Ret(array('code' => 2, 'info' => '请输入请填写正确的手机号码！'));
                }
                $data['parent_phone']=I('parent_phone',0);
                if(!$data['parent_phone']){
                    Ret(array('code' => 2, 'info' => '请输入家长电话！'));
                }
                if(!isMobile($data['parent_phone'])){
                    Ret(array('code' => 2, 'info' => '请输入请填写正确的手机号码！'));
                }
                $data['pic']=I('pic');
                $data['sex']=I('sex');
                if(!$data['sex']){
                    Ret(array('code' => 2, 'info' => '请选择性别！'));
                }
                $data['id_number']=I('id_number');
                if(!$data['id_number']){
                    Ret(array('code' => 2, 'info' => '请输入身份证号码！'));
                }
                if(!isCreditNo($data['id_number'])){
                    Ret(array('code' => 2, 'info' => '请输入身份证号码非法！'));
                }
                $data['height']=I('height');
                $data['weight']=I('weight');
                $data['birth_province_id']=I('birth_province_id');
                $data['birth_city_id']=I('birth_city_id');
                $data['birth_district_id']=I('birth_district_id');
                if($data['birth_province_id'] || $data['birth_city_id'] || $data['birth_district_id']){
                    $data['birth_address'] = $this->get_address($data['birth_province_id'] , $data['birth_city_id'] , $data['birth_district_id']);
                }

                $data['address']=I('address');
                if(!$data['address']){
                    Ret(array('code' => 2, 'info' => '请输入现住址！'));
                }
                $data['marriage']=I('marriage');
                if(!$data['marriage']){
                    Ret(array('code' => 2, 'info' => '请选择婚姻状况！'));
                }
                $data['blood_type']=I('blood_type');
                $data['education']=I('education');
                if(!$data['education']){
                    Ret(array('code' => 2, 'info' => '请选择学历！'));
                }
                $data['major']=I('major');
                if(!$data['major']){
                    Ret(array('code' => 2, 'info' => '请选择专业！'));
                }
                $data['school']=I('school');
                if(!$data['school']){
                    Ret(array('code' => 2, 'info' => '请选择学校！'));
                }
                $data['card_number'] = I('card_number');
                $data['birthdate']=I('birthdate');
                $data['health']=I('health');
                $data['cardnumber_no'] = I('cardnumber_no');

                $data['state']=1;
                $data['relations']=json_encode($_POST['relations']);
                //$validateCardInfo=$this->getCardOwnerId($data['card_number']);
                $validateCardInfo=$this->getCardOwnerId($data['card_number']);
                //$cardIsOk=hasSameCard($data['card_number']);
                //var_dump($validateCardInfo);die;
                if(empty($validateCardInfo))
                {
                    $cardInfo=D('Card')->getCardInfoByNumber($data['card_number']);
                    //var_dump($cardInfo);die;
                    if(empty($cardInfo['id']))
                    {
                        $oldWorker=$memberModel->getMemberById($data['id']);
                        $res = $memberModel->save($data);
                            $worker_id = $data['id'];
                            //新建卡
                            $this->setcard($worker_id,$data);
                            //冻结原有的卡
                            D('Card')->setCardFrozenByNumber($oldWorker['card_number']);
                            //原有的权限删除了
                            $this->deleteWorkerDoorAuth($oldWorker['card_number']);
                            //设置会员卡的权限
                            $this->memberCardAuth($data,$res);
                            //$this->sendAuthByInst($institution_id, $worker_id, $data);

                            //$this->sendAuthByPost($data, $worker_id);

                            Ret(array('code' => 1, 'info' => '修改成功！'));
                    }
                    else
                    {
                        Ret(array('code' => 2, 'info' => '此卡已经被冻结或被挂失！'));
                    }
                }
                else
                {
                    //var_dump($validateCardInfo['id']);die;
                    if($validateCardInfo['id']==$data['id'])
                    {
                        $res = $memberModel->save($data);
                        $oldWorker=$memberModel->getMemberById($data['id']);

                            $worker_id = $data['id'];
                            //$this->sendAuthByInst($institution_id, $worker_id, $data);
                            //$this->sendAuthByPost($data, $worker_id);
                            //$this->setcard($data,$worker_id);
                            //冻结原有的卡
                            //D('Card')->setCardFrozenByNumber($oldWorker['card_number']);
                            //设置会员卡的权限
                            $this->memberCardAuth($data,$res);
                            Ret(array('code' => 1, 'info' => '修改成功！'));

                    }
                    else
                    {
                        Ret(array('code' => 2, 'info' => '此卡被他人使用！'));
                    }
                }
                /*
                if($validateCardInfo==null || $validateCardInfo['id']==$data['id'])
                {
                    $res = $memberModel->update_member($data);
                    if ($res) {
                        Ret(array('code' => 1, 'info' => '修改成功！'));
                    } elseif ($res === 0) {
                        Ret(array('code' => 3, 'info' => '未作任何修改！'));
                    } else {
                        Ret(array('code' => 2, 'info' => '修改失败，系统出错！'));
                    }
                }
                else
                {
                    Ret(array('code' => 2, 'info' => '此卡被他人使用，请换一张卡'));
                }*/
                break;
            case 'delete':
                $data['id'] = I('id',0,'intval');
                if ($data['id'] < 1) {
                    Ret(array('code' => 2, 'info' => '参数（id）有误！'));
                }
                $deleteWorker=$memberModel->where('id='.$data['id'])->select();
                if ($memberModel->delete_member($data['id']))
                {
                    $this->deleteWorkerDoorAuth($deleteWorker[0]['card_number']);
                    D('Card')->setCardFrozenByNumber($deleteWorker[0]['card_number']);
                    Ret(array('code' => 1, 'info' => '删除成功！'));
                }else{
                    Ret(array('code' => 2, 'info' => '删除失败！'));
                }
                break;
            default:
                Ret(array('code' => 2, 'info' => 'Unknowed flag!'));
                break;
        }
    }
   private function setcard($res,$data){
        if($data && $res){
            $cardModel =D('Test');
            $cardInfo['card_ownerid'] = $res;
            $cardInfo['card_ownewneme'] =$data['name'];
            $cardInfo['card_number'] = $data['card_number'];
            $cardInfo['cardnumber_no'] = $data['cardnumber_no'];
            $cardInfo['card_typeid'] = 1;
            $cardInfo['card_state'] = 1;
            return $cardModel->addCard($cardInfo);
        }
    }

     private function checkPhone($phone){
         if($phone){
             return D('Member')->where('phone='.$phone)->find();
         }
     }

    private function checkIDNo($idNo){
        if($idNo){
            return D('Member')->where('id_number='.$idNo)->find();
        }
    }

    /*
     * 验证卡号是否有效
     */
    public function check_card(){
        //before_api();
        $car_number=I('card_number');
        if(empty($car_number)){
            Ret(array('code' => 3, 'info' => '卡号不能为空！'));
        }

        $is_exit=checkCard($car_number);
        if($is_exit){
            Ret(array('code' => 2, 'info' => '卡号一存在，请换一张卡！'));
        }
        if(!$is_exit){
            Ret(array('code' => 1, 'info' => '此卡有效！','card_number'=>$car_number));
        }
    }

    /*
    * 验证卡号是否有效
    */
    private function card_check($car_number){
        if($car_number){
            return $is_exit=D('Member')->where('card_number='.$car_number)->select();
        }
    }

    /*
     * 家长信息增改
     */
    /*public function relation_dml_api(){
        before_api();
        checkLogin();
        checkAuth();
        $mall_id = session('mall.mall_id');
        $flag = I('flag');
        $memberModel = D('Member');
        switch ($flag) {
            case 'add':
                if(!$mall_id){
                    Ret(array('code' => 2, 'info' => '获取登录信息失败！'));
                }
                $data['retation']=I('retation',0);
                if(!$data['retation']){
                    Ret(array('code' => 2, 'info' => '请输入亲属关系！'));
                }
                $data['phone']=I('phone',0);
                if(!$data['phone']){
                    Ret(array('code' => 2, 'info' => '请输入家长电话号码！'));
                }
                $data['name']=I('name',0);
                if(!$data['name']){
                    Ret(array('code' => 2, 'info' => '请输入家长姓名！'));
                }
                $data['pic']=I('pic');
                $res = $memberModel->add_relation($data);
                if ($res) {
                    Ret(array('code' => 1, 'info' => '添加成功！'));
                }else{
                    Ret(array('code' => 2, 'info' => '添加失败,系统出错！'));
                }
                break;
            case 'update':
                $data['id'] = I('id',0,'intval');
                if ($data['id'] < 1) {
                    Ret(array('code' => 2, 'info' => '参数（会员id）有误！'));
                }
                $member_id=session('member_update.member_id',$data['id']);
                if(!$member_id){
                    Ret(array('code' => 2, 'info' => '获取会员信息失败！'));
                }
                if(!$mall_id){
                    Ret(array('code' => 2, 'info' => '获取登录信息失败！'));
                }
                $data['retation']=I('retation',0);
                if(!$data['retation']){
                    Ret(array('code' => 2, 'info' => '请输入亲属关系！'));
                }
                $data['phone']=I('phone',0);
                if(!$data['phone']){
                    Ret(array('code' => 2, 'info' => '请输入家长电话号码！'));
                }
                $data['name']=I('name',0);
                if(!$data['name']){
                    Ret(array('code' => 2, 'info' => '请输入家长姓名！'));
                }
                $data['pic']=I('pic');
                $res = $memberModel->update_relation($data);
                if ($res) {
                    Ret(array('code' => 1, 'info' => '修改成功！'));
                }elseif($res === 0){
                    Ret(array('code' => 3, 'info' => '未作任何修改！'));
                }else{
                    Ret(array('code' => 2, 'info' => '修改失败，系统出错！'));
                }
                break;
            default:
                Ret(array('code' => 2, 'info' => 'Unknowed flag!'));
                break;
        }
    }*/


    public function get_relation(){
        before_api();
        $a=array('id'=>'父亲','name'=>'父亲');
        $b=array('id'=>'母亲','name'=>'母亲');
        $c=array('id'=>'爷爷','name'=>'爷爷');
        $d=array('id'=>'奶奶','name'=>'奶奶');
        $e=array('id'=>'姥姥','name'=>'姥姥');
        $f=array('id'=>'姥爷','name'=>'姥爷');
        $g=array('id'=>'其他','name'=>'其他');
        $data=array($a,$b,$c,$d,$e,$f,$g);

        Ret(array('code' => 2, 'data' => $data));
    }
    public function hasSameCardInfo($card_number)
    {
        $typeArr = [1 => 'member', 2 => 'institution_staff', 3 => 'mall_staff', 4 => 'merchant_staff'];
        foreach ($typeArr as $key => $v) {
            $member = D($v);
            $obj = $member->where(['card_number' => $card_number])->find();
            if (!empty($obj)) {
                //Ret(array('code' => 2, 'info' => '此卡已被他人使用！'));
                return true;
            }
        }
        return false;
    }
    public function getCardOwnerId($card_number)
    {
        $typeArr = [1 => 'member', 2 => 'institution_staff', 3 => 'mall_staff', 4 => 'merchant_staff'];
        foreach ($typeArr as $key => $v) {
            $member = D($v);
            $obj = $member->where(['card_number' => $card_number])->find();
            if (!empty($obj)) {
                //Ret(array('code' => 2, 'info' => '此卡已被他人使用！'));
                return $obj;
            }
        }
        return null;
    }
    //发送员工卡授权
    private function memberCardAuth($data,$worker_id){
        //格式：eqNO,cardNO,Pin,name,starttime,endtime#timeID]week0,timezone1,timezone2,timezone3...;week1,timezone1,timezone2,timezone3..;.\r\n   you to me
        // 例子：12345,23456,2001,locost,20170730,20170731#23]1,9:30-11:00,10:30-12:00,16:30-18:00;2,9:30-11:00,10:30-12:00,16:30-18:00\r\n
        //$con['position'] = array('Like','%'.$position_id.'%');
        $con['user_type'] = 3;
        $doorIDs  = D('ShopDoorRole')->where($con)->select();
        $dauth = array();
        foreach($doorIDs as $k => $v){
            $dooids=explode('-',$v['position']);
                $dauth = $doorIDs[$k]['shop_numbers'];
        }
        if($dauth){
            $dooids=explode(',',$dauth);
            foreach($dooids as $k => $v){
                $data1['eqIp']=$v;
                $data1['cardNo']=$data['card_number'];
                $number=get_number_by_id($worker_id)+100000;
                $data1['memberId']='m'.$number;
                $data1['memberName']=$data['name'];
                $starttime = $data['join_time'];
                $endtime = $data['contract_time'];
                $weekTime = C('WORKER_AUTH_TIME');
                $data1['time']=$starttime.','.$endtime.'#'.rand(1,1000).']'.$weekTime;

                $con['memberId'] = $data1['memberId'];
                $con['eqIp'] = $data1['eqIp'];
                $check = D('MemberDoorAuth')->where($con)->find();
                if($check){
                    D('MemberDoorAuth')->where($con)->find($data1);
                }else{
                    D('MemberDoorAuth')->add($data1);
                }

            }
            return true;
        }
        return false;
    }
    private function deleteWorkerDoorAuth($cardNo)
    {
        $deleteInfo=D('MemberDoorAuth')->where('cardNo='.$cardNo)->select();
        //var_dump($deleteInfo);die;
        foreach($deleteInfo as $key=>$value)
        {
            $info['eqIp']=$value['eqip'];
            $info['cardNo']=$value['cardno'];
            $info['memberId']=$value['memberid'];
            $info['memberName']=$value['membername'];
            $info['time']=$value['time'];
            $info['state']=0;
            //var_dump($info);die;
            D('DoorAuthDelete')->add($info);
        }
        D('MemberDoorAuth')->where('cardNo='.$cardNo)->delete();

    }
}


