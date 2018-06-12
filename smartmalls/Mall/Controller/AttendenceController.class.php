<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/8/2
 * Time: 18:08
 */
namespace Mall\Controller;

class AttendenceController extends CommonController{
    //测试用
    public function record(){
        $res = D('EquipmentDoor')->field('post')->select();
        foreach($res as $k => $v){
            $d=json_decode($res[$k]["post"]);
            $i=get_object_vars($d);
            $s = explode(';',$i["attendance"]);
            foreach($s as $k => $v){
                $k = explode(',',$v);
                var_dump($k);die;
            }
        }

    }

    /*
     * 考勤统计（列表）
     */
    public function index_api(){
        before_api();

        checkLogin();

        checkAuth();

        $time = I('time');
        $dept_id = I('dept_id');
        $page = I('page',1,'intval');
        $pagesize = I('pagesize',10,'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page.','.$pagesize;
        $atModel=D('AttendenceStatistics');
        $count = $atModel->get_count($time,$dept_id);
        $data=$atModel->get_list($time,$dept_id,$page);
        if($data){
            Ret(array('code'=>1,'data'=>$data,'total' => $count, 'page_count' => ceil($count/$pagesize)));
        }else{
            Ret(array('code'=>2,'info'=>'没有数据！'));
        }

    }




    /*
     * 考勤明细
     */
    public function attendance_view_api(){
        before_api();
        checkLogin();
        checkAuth();
        $id=I('id');
        $atModel=D('Attendence');
        if($id){
            $con['id'] = $id;
            $data=$atModel->where($con)->find();
            if($data){
                Ret(array('code'=>1,'data'=>$data,'total' => 1, 'page_count' => 1));
            }else{
                Ret(array('code'=>2,'info'=>'没有数据！'));
            }
        }else{
            $year=I('year');
            $month=I('month');
            $keyword = I('keyword');
            $page = I('page',1,'intval');
            $pagesize = I('pagesize',10,'intval');
            $pagesize = $pagesize < 1 ? 1 : $pagesize;
            $pagesize = $pagesize > 50 ? 50 : $pagesize;
            $page = $page.','.$pagesize;


            $time1=strtotime($year.'-'.$month);
            $time2=strtotime($year.'-'.$month+1);
            $con['thedate']=array('between',array(date('Y-m-0',$time1),date('Y-m-0',$time2)));
            $where['worker_id']=$keyword;
            $where['eployee_name']=$keyword;
            $where['_logic']='OR';
            $con['_complex']=$where;

            $count = $atModel->where($con)->count();
            $data=$atModel->where($con)->page($page)->select();
            if($data){
                Ret(array('code'=>1,'data'=>$data,'total' => $count, 'page_count' => ceil($count/$pagesize)));
            }else{
                Ret(array('code'=>2,'info'=>'没有数据！'));
            }
        }

    }


    /*
     * 获取已审核和未审核考勤数
     */
    public function get_check_count(){
        before_api();

        checkLogin();

        checkAuth();
        $atModel=D('AttendenceDetail');
        $data['all'] = $atModel->get_count();
        $data['check']=$atModel->count_check();
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

        $data['in_time'] = I('in_time');
        if(!$data['in_time'] ){
            Ret(array('code'=>2,'info'=>'参数（in_time）错误！'));
        }
        $data['out_time'] = I('out_time');
        if(!$data['in_time'] ){
            Ret(array('code'=>2,'info'=>'参数（out_time）错误！！'));
        }

        $model=D('AttendenceSet');
        $res = $model->add($data);
        if($res){
            Ret(array('code'=>1,'提交成功！'));
        }else{
            Ret(array('code'=>2,'info'=>'提交失败！'));
        }

    }
    /*
    * 考勤审核
    */
    public function attendance_check_api(){
        before_api();

        checkLogin();

        checkAuth();

        $data['id'] = I('id',0,'intval');
        if($data['id']==0){
            Ret(array('code'=>2,'info'=>'数据（id）获取失败！'));
        }
        $data['am_remark']=I('am_remark');
        if($data['am_remark']){
            $data['am_remark']=$data['am_remark'];
        }
        $data['pm_remark']=I('pm_remark');
        if($data['pm_remark']){
            $data['pm_remark']=$data['pm_remark'];
        }
        $model=D('Attendence');
        $res=$model->make_check($data);
        if($res){
            Ret(array('code'=>1,'提交成功！'));
        }else{
            Ret(array('code'=>2,'info'=>'提交失败！'));
        }

    }

    /*
     * 今日考勤
     */
    public function getTodayAtt(){
        before_api();
        checkLogin();
        checkAuth();
        $keyword = I('keyword');
        $page = I('page',1,'intval');
        $pagesize = I('pagesize',10,'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page.','.$pagesize;
        $atModel=D('AttendenceToday');
        $con['thedate']=date('Y-m-d');
        $count = $atModel->where($con)->count();
        $fields=array('eployee_pic,eployee_name,	eployee_address,eployee_department,position,phone,punch_in,punch_in_state,punch_out,punch_out_state');
        $data=$atModel->where($con)->field($fields)->page($page)->select();
        if($data){
            Ret(array('code'=>1,'data'=>$data,'total' => $count, 'page_count' => ceil($count/$pagesize)));
        }else{
            Ret(array('code'=>2,'info'=>'没有数据！'));
        }

    }

    public function getAttDetail(){
        before_api();
        checkLogin();
        checkAuth();
        $year=I('y');
        $month=I('m');
        $keyword = I('keyword');
        $page = I('page',1,'intval');
        $pagesize = I('pagesize',10,'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page.','.$pagesize;
        $atModel=D('Attendence');

        $time1=strtotime($year.'-'.$month);
        $time2=strtotime($year.'-'.$month+1);
        $con['theday']=array('between',array(date('Y-m-0',$time1),date('Y-m-0',$time2)));
        $where['pid']=$keyword;
        $where['name']=$keyword;
        $where['_logic']='OR';
        $con['_complex']=$where;

        cookie('atc',$con);
        $count = $atModel->where($con)->count();
        $fields=array('pic,name,addr,deptName,position,phone,punchIn,punchInState,punchOut,punchOutState,amRemark,pmRemark');
        $data=$atModel->where($con)->field($fields)->page($page)->select();
        if($data){
            Ret(array('code'=>1,'data'=>$data,'total' => $count, 'page_count' => ceil($count/$pagesize)));
        }else{
            Ret(array('code'=>2,'info'=>'没有数据！'));
        }
    }


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
        $year=I('year');
        if(!$year){
            Ret(array('code'=>2,'info'=>'请选择年！'));
        }
        $month=I('month');
        if(!$month){
            Ret(array('code'=>2,'info'=>'请选择月！'));
        }
        $keyword = I('deptID');
        if(!$keyword){
            Ret(array('code'=>2,'info'=>'请选择部门！'));
        }
        $page = I('page',1,'intval');
        $pagesize = I('pagesize',10,'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page.','.$pagesize;
        $atModel=D('Attendence');
        if($keyword){
            $getMemInfo = D('Worker') ->where('dept_id='.$keyword)->field('id')->select();

            $time1=strtotime($year.'-'.$month);
            $time2=strtotime($year.'-'.$month+1);
            $con['theday']=array('between',array(date('Y-m-0',$time1),date('Y-m-0',$time2)));


            foreach($getMemInfo as $k => $v){
                $con['pid']=$v['id'];

                $wokInfo=D('Worker')->where('id='.$v['id'])->find();
                $data[$k]=$wokInfo;


                 //上午考勤异常
                $con1=$con;
                $con1['punch_in_state']=1;
                $data[$k]['inNomal']=D('Attendence')->where($con1)->count();
                $con['punch_in_state']=2;
                $data[$k]['inUnnomal']=D('Attendence')->where($con1)->count();
                 //下午考勤异常
                $con2=$con;
                $con2['punch_out_state']=1;
                $data[$k]['outNomal']=D('Attendence')->where($con2)->count();
                $con['punch_out_state']=2;
                $data[$k]['outUnnomal']=D('Attendence')->where($con2)->count();
                 //公出
                $con3=$con;
                $con3['in_remarks']=1;
                $data[$k]['yichang']=D('Attendence')->where($con3)->count();
                $con['in_remarks']=2;
                $data[$k]['gongchu']=D('Attendence')->where($con3)->count();
                $con['in_remarks']=3;
                $data[$k]['shijia']=D('Attendence')->where($con3)->count();
                $con['in_remarks']=4;
                $data[$k]['bingjia']=D('Attendence')->where($con3)->count();


                //事假
                $con4=$con;
                $con2['out_remarks']=1;
                $data[$k]['yichang']=D('Attendence')->where($con4)->count();
                $con['out_remarks']=2;
                $data[$k]['gongchu']=D('Attendence')->where($con4)->count();
                $con2['out_remarks']=3;
                $data[$k]['shijia']=D('Attendence')->where($con4)->count();
                $con['out_remarks']=4;
                $data[$k]['bingjia']=D('Attendence')->where($con4)->count();

            }
        }



        $time1=strtotime($year.'-'.$month);
        $time2=strtotime($year.'-'.$month+1);
        $con['theday']=array('between',array(date('Y-m-0',$time1),date('Y-m-0',$time2)));
        $where['pid']=$keyword;
        $where['name']=$keyword;
        $where['_logic']='OR';
        $con['_complex']=$where;

        cookie('ats',$con);
        $count = $atModel->where($con)->count();
        $fields=array('pic,name,addr,deptName,position,phone,punchIn,punchInState,punchOut,punchOutState,amRemark,pmRemark');
        $data=$atModel->where($con)->field($fields)->page($page)->select();
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

    /*
     * 考勤评审
     */
    public function makeArrCheck(){
        checkLogin();
        $data['id']=I('id');
        if(!$data['id']){
            Ret(array('code'=>2,'info'=>'参数（id）！'));
        }
        $data['in_remarks']=I('in_remarks');
        if(!$data['in_remarks']){
            Ret(array('code'=>2,'info'=>'参数（in_remarks）！'));
        }
        $data['out_remarks']=I('out_remarks');
        if(!$data['out_remarks']){
            Ret(array('code'=>2,'info'=>'参数（out_remarks）！'));
        }
        if($data){
            $res = D('Attendence')->save($data);
            if($res){
                Ret(array('code'=>1,'info'=>'审核成功！'));
            }else{
                Ret(array('code'=>2,'info'=>'数据保存失败！系统错误'));
            }
        }
    }

    /*
     * 考勤记录
     */
    public function attendence_record(){
        $card_number=I('card_number');
        $time=date('Y-m-d H:i:s');
    }


    /*
     * 上班时间、下班时间
     */
    private function workTime(){
        return D('AttendencSet')->find();

    }


    public function getTime(){
        $year=I('year');
        $month=I('month');
        $keyword =I('keyword');

    }

}