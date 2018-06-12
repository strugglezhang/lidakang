<?php
namespace Admin\Controller;
use Think\Controller;
class LogsController extends Controller {
    public function index(){
        if (!isset($_SESSION['manager'])) {
            $this->redirect('Login/index');
        }
    }

    public function equipment(){
        if (!isset($_SESSION['manager'])) {
            $this->redirect('Login/index');
        }
        
        $this->schools = D('Common/School')->field('id,name')->select();
        $this->display();
    }

    public function equipment_logs_api(){
        checkLogin('manager');

        $eid = I('eid',0,'intval');
        $sid = I('sid',0,'intval');
        $all = I('all',false);
        if ($eid < 1 && $sid < 1 && !$all) {
            echo json_encode(array('code' => 0, 'info' => '提交参数有误！'));
            exit;
        }


        $redis = new \Redis();
        $redis->connect("127.0.0.1",6379);
        $redis->select(4);
        $expire = 10;
        $count = 10;

        if ($all) {
            $redis->set('equ_logs_switch_all','1');
            $redis->expire('equ_logs_switch_all',$expire);

            $logs = $redis->exists('equ_logs_all');
            if ($logs) {
                for ($i=0; $i < $count; $i++) { 
                    $temp = $redis->lpop('equ_logs_all');
                    if ($temp) {
                        $data[] = unserialize($temp);
                    }else{
                        break;
                    }
                }
            }
            $redis->expire('equ_logs_all',$expire);
        }

        if ($eid) {
            $_switch = 'equ_logs_switch_'.$eid;
            $redis->set($_switch,'1');
            $redis->expire($_switch,$expire);

            $key = 'equ_logs_'.$eid;
            $logs = $redis->exists($key);
            if ($logs) {
                for ($i=0; $i < $count; $i++) { 
                    $temp = $redis->lpop($key);
                    if ($temp) {
                        $data[] = unserialize($temp);
                    }else{
                        break;
                    }
                }
            }
            $redis->expire($key,$expire);
        }
        
        if ($sid) {
            $_switch = 'equ_logs_switch_'.$sid;
            $redis->set($_switch,'1');
            $redis->expire($_switch,$expire);

            $key = 'equ_logs_'.$sid;
            $logs = $redis->exists($key);
            if ($logs) {
                for ($i=0; $i < $count; $i++) { 
                    $temp = $redis->lpop($key);
                    if ($temp) {
                        $data[] = unserialize($temp);
                    }else{
                        break;
                    }
                }
            }
            $redis->expire($key,$expire);
        }  

        if (empty($data)) {
            echo json_encode(array('code' => 2, 'info' => 'empty'));
        }else{
            echo json_encode(array('code' => 1, 'data' => $data));
        }
    }
}