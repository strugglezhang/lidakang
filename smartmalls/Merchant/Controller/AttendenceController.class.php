<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/7/5
 * Time: 16:48
 */
namespace Merchant\Controller;
class AttendenceController extends CommonController{

    /*
     * 获取已审核和未审核考勤数
     */
    public function getAttCheckCount(){
        before_api();

        checkLogin();

        checkAuth();
        $atModel=D('AttendenceDetail');
        $con['thedate']=date("Y-m-d",strtotime("-1 day"));
        $conUion['punch_in_state']=0;
        $conUion['punch_out_state']=0;
        $conUion['_logic'] = 'OR';
        $con['_complex'] = $conUion;
        $data['all'] = $atModel->where($con)->count();

        $con1['thedate']=date("Y-m-d",strtotime("-1 day"));
        $con1['check_state']=1;
        $data['check']=$atModel->where($con1)->count();

        $data['noncheck']=$data['all'] - $data['check'];
        if($data){
            Ret(array('code'=>1,'data'=>$data));
        }else{
            Ret(array('code'=>2,'info'=>'没有数据！'));
        }

    }



    /*
     * 考勤设定
     */
    public function attendance_set_api(){
        before_api();

        checkLogin();

        checkAuth();
        //attendance_owner_typ=0为商场，1为机构
        $type=I('attendance_owner_typ');
        $data['in_time'] = I('in_time');
        $data['out_time'] = I('out_time');
        if($type==0)
        {
            $mallId=session('mall.mall_id');
            $model=D('AttendenceSet');
            $condition['mall_id']=$mallId;
            $check = $model->where($condition)->find();

            if(!$data['in_time'] ){
                Ret(array('code'=>2,'info'=>'参数（in_time）错误！'));
            }
            if(!$data['out_time'] ){
                Ret(array('code'=>2,'info'=>'参数（out_time）错误！！'));
            }
            $data['mall_id']=$mallId;
            if($check){
                $data['id']=$check['id'];
                $res = $model->where($condition)->save($data);
            }else{
                $res = $model->add($data);
            }
            if($res){
                Ret(array('code'=>1,'提交成功！'));
            }else{
                Ret(array('code'=>2,'info'=>'提交失败,没做任何修改！'));
            }
        }
        else
        {
            $instId=session('inst.institution_id');
            $model=D('AttendenceSet');
            $condition['inst_id']=$instId;
            $check = $model->where($condition)->find();

            if(!$data['in_time'] ){
                Ret(array('code'=>2,'info'=>'参数（in_time）错误！'));
            }
            if(!$data['out_time'] ){
                Ret(array('code'=>2,'info'=>'参数（out_time）错误！！'));
            }
            $data['inst_id']=$instId;
            if($check){
                $data['id']=$check['id'];
                $res = $model->where($condition)->save($data);
            }else{
                $res = $model->add($data);
            }
            if($res){
                Ret(array('code'=>1,'提交成功！'));
            }else{
                Ret(array('code'=>2,'info'=>'提交失败,没做任何修改！'));
            }

        }
        //$instId=session('inst.institution_id');
        //$mallId=session('mall.mall_id');

        /*$data['in_time'] = I('in_time');
        if(!$data['in_time'] ){
            Ret(array('code'=>2,'info'=>'参数（in_time）错误！'));
        }
        $data['out_time'] = I('out_time');
        if(!$data['in_time'] ){
            Ret(array('code'=>2,'info'=>'参数（out_time）错误！！'));
        }


        $model=D('AttendenceSet');
        $condition['mall_id']=$mallId;
        $condition['inst_id']=$instId;
        $condition['_logic']='OR';
        $check = $model->where($condition)->find();
        $data['mall_id']=$mallId;
        $data['inst_id']=$instId;
        if($check){
            $data['id']=$check['id'];
            $res = $model->save($data);
        }else{
            $res = $model->add($data);
        }
        if($res){
            Ret(array('code'=>1,'提交成功！'));
        }else{
            Ret(array('code'=>2,'info'=>'提交失败,没做任何修改！'));
        }
        */
    }

    /*
     * 获取考勤时间
     */
    public function getTimes(){
        before_api();

        checkLogin();

        checkAuth();
        $type=I('attendance_owner_typ');
        if($type==0)
        {
            $model = D('AttendenceSet');
            $condition['mall_id']=session('mall.mall_id');
            $res = $model->where($condition)->find();
        }
        if($type==1)
        {
            $model = D('AttendenceSet');
            session('inst.institution_id');
            $condition['inst_id']=session('inst.institution_id');
            $res = $model->where($condition)->find();
        }
        if($res){
            Ret(array('code'=>1,'data'=>$res));
        }else{
            Ret(array('code'=>2,'info'=>'系统错误，查询失败！'));
        }

    }

    /*
     * 考勤审核列表
     */
    public function getAttCheckList(){
        before_api();

        checkLogin();

        checkAuth();

        $flag= I('flag');
        if($flag==1){
            $con['check_state'] = $flag;
        }
        if($flag==2){
            $con['check_state'] = 0;
        }

        $page = I('page',1,'intval');
        $pagesize = I('pagesize',10,'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page.','.$pagesize;
        $atModel=D('AttendenceDetail');
        $con['thedate']=date("Y-m-d",strtotime("-1 day"));
        $conUion['punch_in_state']=0;
        $conUion['punch_out_state']=0;
        $conUion['_logic'] = 'OR';
        $con['_complex'] = $conUion;
        //var_dump($con);exit;
        $count = $atModel->where($con)->count();
        //$fields=array('pic,name,addr,deptName,position,phone,punchIn,punchOutState,punchOut,punchOutState');
        $data=$atModel->where($con)->page($page)->select();
        if($data){
            foreach($data as $k => $v){
                $data[$k]['punch_in_state'] = $this->get_state($v['punch_in_state']);
                $data[$k]['punch_out_state']= $this->get_state($v['punch_out_state']);
                $data[$k]['am_remark']= $this->get_remarks($v['am_remark']);
                $data[$k]['pm_remark']= $this->get_remarks($v['pm_remark']);
            }
            Ret(array('code'=>1,'data'=>$data,'total' => $count, 'page_count' => ceil($count/$pagesize)));
        }else{
            Ret(array('code'=>2,'info'=>'没有数据！'));
        }
    }
    /*
    * 考勤审核
    */
    public function attendance_check_api(){
        before_api();
        checkLogin();
        checkAuth();
        $condition='';

        $condition['id'] = I('id',0,'intval');
        if($condition['id']==0){
            Ret(array('code'=>2,'info'=>'数据（id）获取失败！'));
        }
        $data['am_remark']=I('am_remark',0);
        $data['pm_remark']=I('pm_remark',0);
        $data['check_state']=1;
        $model=D('AttendenceDetail');
        $res=$model->where($condition)->save($data);
        if($res){
            $adInfo = D('AttendenceDetail')->where($condition)->find();

            $adInfo['am_remark'] = $data['am_remark'];
            $adInfo['pm_remark'] = $data['pm_remark'];
            $this->makeStatic($adInfo);

            Ret(array('code'=>1,'审核成功！'));
        }else{
            Ret(array('code'=>2,'info'=>'审核失败，系统错误！'));
        }

    }

    private function makeStatic($data){
        $condition['data']=$data['thedate'];
        $condition['pid']=$data['worker_id'];
        $isExist = D('AttendenceStatistics') ->where($condition)->find();
        if($isExist){
            switch($data['am_remark']){
                case 1:
                   $sd['attendenc_unnormal'] = $isExist['attendenc_unnormal']+1;
                   $sd['out_for_business'] = $isExist['out_for_business']+1;
                    D('AttendenceStatistics') ->where($condition)->save($sd);
                    break;
                case 2:
                    $sd['attendenc_unnormal'] = $isExist['attendenc_unnormal']+1;
                    $sd['leave_for_private'] = $isExist['leave_for_private']+1;
                    D('AttendenceStatistics') ->where($condition)->save($sd);
                    break;
                case 3:
                    $sd['attendenc_unnormal'] = $isExist['attendenc_unnormal']+1;
                    $sd['leave_for_ill'] = $isExist['leave_for_ill']+1;
                    D('AttendenceStatistics') ->where($condition)->save($sd);
                    break;
            }
            switch($data['pm_remark']){
                case 1:
                    $sd['attendenc_unnormal'] = $isExist['attendenc_unnormal']+1;
                    $sd['out_for_business'] = $isExist['out_for_business']+1;
                    D('AttendenceStatistics') ->where($condition)->save($sd);
                    break;
                case 2:
                    $sd['attendenc_unnormal'] = $isExist['attendenc_unnormal']+1;
                    $sd['leave_for_private'] = $isExist['leave_for_private']+1;
                    D('AttendenceStatistics') ->where($condition)->save($sd);
                    break;
                case 3:
                    $sd['attendenc_unnormal'] = $isExist['attendenc_unnormal']+1;
                    $sd['leave_for_ill'] = $isExist['leave_for_ill']+1;
                    D('AttendenceStatistics') ->where($condition)->save($sd);
                    break;
            }
            D('AttendenceStatistics') ->where($condition)->save($data);
        }else{
            $datas['date'] = $data['thedate'];
            $datas['pid'] = $data['worker_id'];
            $datas['dept_id']=$this->getDepartmentID($data['eployee_department']);
            $datas['eployee_pic'] = $data['eployee_pic'];
            $datas['eployee_name'] = $data['eployee_name'];
            $datas['eployee_address'] = $data['eployee_address'];
            $datas['eployee_department'] = $data['eployee_department'];
            if($data['punch_in_late_the_time']){
                $datas['punch_in_late_the_time'] = 1;
            }else{
                $datas['punch_in_late_the_time'] = 0;
            }
            if($data['punch_in_normal']){
                $datas['punch_in_normal'] = 1;
            }else{
                $datas['punch_in_normal'] = 0;
            }
            if($data['punch_in_late_the_time']){
                $datas['punch_out_befor_the_time'] = 1;
            }else{
                $datas['punch_out_befor_the_time'] = 0;
            }
            if($data['punch_out_normal']){
                $datas['punch_out_normal'] = 1;
            }else{
                $datas['punch_out_normal'] = 0;
            }
            switch($data['am_remark']){
                case 1:
                    $datas['attendenc_unnormal'] = 1;
                    $datas['out_for_business'] = 1;
                    break;
                case 2:
                    $datas['attendenc_unnormal'] = 1;
                    $datas['leave_for_private'] = 1;
                    break;
                case 3:
                    $datas['attendenc_unnormal'] = 1;
                    $datas['leave_for_ill'] = 1;
                    break;
            }
            switch($data['pm_remark']){
                case 1:
                    $datas['attendenc_unnormal'] = 1;
                    $datas['out_for_business'] = 1;
                    break;
                case 2:
                    $datas['attendenc_unnormal'] = 1;
                    $datas['leave_for_private'] = 1;
                    break;
                case 3:
                    $datas['attendenc_unnormal'] = 1;
                    $datas['leave_for_ill'] = 1;
                    break;
            }
            D('AttendenceStatistics')->add($datas);
        }

    }
    private function getDepartmentID($DeptName){
        if($DeptName){
            $condition['name']=$DeptName;
            $data = D('MallDept')->where($condition)->field('id')->find();
            if($data){
                return $data['id'];
            }
        }
    }


    /*
     * 今日考勤
     */
    public function getTodayAtt(){
        before_api();
        checkLogin();
        checkAuth();
        $atModel=D('AttendenceDetail');

        //$year=I('year');
        //$month=I('month');
        //$keyword = I('keyword');
        $page = I('page',1,'intval');
        $pagesize = I('pagesize',10,'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page.','.$pagesize;


        $con['thedate']=date("Y-m-d");
        //var_dump($con['thedate']);die;
        /*if(is_numeric($keyword)){
            $con['worker_id']=$keyword;
        }else{
            $con['eployee_name']=$keyword;
        }*/
        $count = $atModel->where($con)->count();
        $data=$atModel->where($con)->page($page)->select();

        if($data){
            Ret(array('code'=>1,'data'=>$data,'total' => $count, 'page_count' => ceil($count/$pagesize)));
        }else{
            Ret(array('code'=>2,'info'=>'没有数据！'));
        }






    }

    private function get_state($state_id = 0){
        $state = array('0' => '异常', '1' => '正常');
        return isset($state[$state_id]) ? $state[$state_id] : '';
    }
    private function get_remarks($state_id = 0){
        $state = array('1' => '公出', '2' => '事假','3' => '病假');
        return isset($state[$state_id]) ? $state[$state_id] : '';
    }

        /*
      * 考勤明细
      */
    public function attendance_view_api(){
        before_api();
        checkLogin();
        checkAuth();
        $atModel=D('AttendenceDetail');

        $year=I('year');
        $month=I('month');
        $keyword = I('keyword');
        $page = I('page',1,'intval');
        $pagesize = I('pagesize',10,'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page.','.$pagesize;
        if(!empty($year) && !empty($month)) {
            $tstr1 = $year . '-' . $month;
            $time1 = strtotime($tstr1);
            $m2 = $month + 1;
            $tstr2 = $year . '-' . $m2;
            $time2 = strtotime($tstr2);
            $t1 = date('Y-m', $time1);
            $t2 = date('Y-m-0', $time2);
            $con['thedate'] = array(array('gt', $t1), array('lt', $t2));
        }
        if(!empty($keyword)) {
            if (is_numeric($keyword)) {
                $con['worker_id'] = $keyword;
            } else {
                $con['eployee_name'] = $keyword;
            }
        }
        $count = $atModel->where($con)->count();
        $data=$atModel->where($con)->page($page)->select();
        if($data){
            Ret(array('code'=>1,'data'=>$data,'total' => $count, 'page_count' => ceil($count/$pagesize)));
        }else{
            Ret(array('code'=>2,'info'=>'没有数据！'));
        }


    }

    /*
     * 输出exl
     */
    public function outputAttDetailToExl(){

        $con=cookie('atc');

        $xlsName  = "eqConState";
        $xlsCell  = array(
            array('name','姓名'),
            array('addr','地址'),
            array('dept','部门'),
            array('position','职位'),
            array('phone','电话'),
            array('punchIn','上班时间'),
            array('punchInState','上班考勤'),
            array('punchOut','下班时间'),
            array('punchOutState','下班考勤'),
            array('amRemark','上班备注'),
            array('pmRemark','下班备注'),

        );

        $fields=array('name,addr,deptName,position,phone,punchIn,punchInState,punchOut,punchOutState,amRemark,pmRemark');
        $xlsData=D('Attendence')->where($con)->field($fields)->select();

        exportExcel($xlsName,$xlsCell,$xlsData);

    }

    //考勤统计
    public function getAttStc(){
        before_api();
        checkLogin();
        checkAuth();
        $count = 1;
        $page = I('page',1,'intval');
        $pagesize = I('pagesize',10,'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page.','.$pagesize;

        $year = I('year');
        $month = I('month');
        if($year && $month){
            $deptID=I('dept_id');
            if($deptID){
                $con['dept_id'] = $deptID;
            }
            $keyword = I('keyword');
            if($keyword){
                if(is_numeric($keyword)){
                    $con['woker_id']=$keyword;
                }else{
                    $con['worker_name']=$keyword;
                }
            }

            $tstr1 = $year.'-'.$month;
            $time1=strtotime($tstr1);
            $m2 = $month+1;
            $tstr2= $year.'-'.$m2;
            $time2=strtotime($tstr2);
            $t1 = date('Y-m',$time1);
            $t2 = date('Y-m-0',$time2);
            $con['thedate']=array(array('gt',$t1),array('lt',$t2));
            $data = D('AttendenceStatistics')->where($con)->page($page)->select();
        }else{
            $data = D('AttendenceStatistics')->page($page)->select();
        }
        if($data){
            Ret(array('code'=>1,'data'=>$data,'total' => $count, 'page_count' => ceil($count/$pagesize)));
        }else{
            Ret(array('code'=>2,'info'=>'没有数据！'));
        }

    }


    public function outputAttStaticToEex(){

        $con=cookie('ats');

        $xlsName  = "eqConState";
        $xlsCell  = array(
            array('name','姓名'),
            array('addr','地址'),
            array('dept','部门'),
            array('position','职位'),
            array('phone','电话'),
            array('punchIn','上班时间'),
            array('punchInState','上班考勤'),
            array('punchOut','下班时间'),
            array('punchOutState','下班考勤'),
            array('amRemark','上班备注'),
            array('pmRemark','下班备注'),

        );

        $fields=array('name,addr,deptName,position,phone,punchIn,punchInState,punchOut,punchOutState,amRemark,pmRemark');
        $xlsData=D('Attendence')->where($con)->field($fields)->select();

        exportExcel($xlsName,$xlsCell,$xlsData);
    }

    public function getMemAttDetail(){
        $id=I('id');
        if($id){
            $workerID=$this->getPID($id);
            $atModel=D('AttendenceDetail');
            $page = I('page',1,'intval');
            $pagesize = I('pagesize',10,'intval');
            $pagesize = $pagesize < 1 ? 1 : $pagesize;
            $pagesize = $pagesize > 50 ? 50 : $pagesize;
            $page = $page.','.$pagesize;
            $con['worker_id']=$workerID;
            $count = $atModel->where($con)->count();
            $data=$atModel->where($con)->page($page)->select();
            if($data){
                Ret(array('code'=>1,'data'=>$data,'total' => $count, 'page_count' => ceil($count/$pagesize)));
            }else{
                Ret(array('code'=>2,'info'=>'没有数据！'));
            }


        }

    }

    private function getPID($id){
        if($id){
            $condition['id']=$id;
            $data= D('AttendenceStatistics')->where($condition)->field('pid')->find();
            return $data['pid'];
        }
    }

}
