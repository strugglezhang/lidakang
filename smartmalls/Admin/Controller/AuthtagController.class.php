<?php
namespace Admin\Controller;
use Think\Controller;
class AuthtagController extends Controller {
    public function index(){
        if (!isset($_SESSION['manager'])) {
    		$this->redirect('Login/index');
    	}
    	$this->schools = D('Common/School')->field('id,name')->select();
    	$this->display();
    }

    public function readtags_api(){
        checkLogin('manager');

        $eid = I('eid',0,'intval');
        if ($eid < 1) {
            echo json_encode(array('code' => 0, 'info' => '提交参数(eid)有误！'));
            exit;
        }

        $redis = new \Redis();
        $redis->connect("127.0.0.1",6379);
        $redis->select(4);
        $tags = $redis->get('equ_tags_'.$eid);
        if (empty($tags)) {
            echo json_encode(array('code' => 2, 'info' => '查无相关数据！'));
        }else{
            if (strpos($tags, '-')) {
                $tags_arr = explode('-', $tags);
                foreach ($tags_arr as $value) {
                	$ret[$value] = '未入库';
                }
                $res = D('Common/Authtag')->field('tag,type')->where(array('tag' => array('IN', $tags_arr)))->select();
                if ($res) {
                    foreach ($res as $key => $value) {
                        $ret[$value['tag']] = '已入库';
                    }
                }
            }else{
                $res = D('Common/Authtag')->field('tag,type')->where(array('tag' => $tags))->find();
                $ret[$tags] = $res ? '已入库' : '未入库';
            }
            echo json_encode(array('code' => 1, 'data' => $ret));
        }
    }

    public function equipment_list_api(){
    	checkLogin('manager');

    	$schoolid = I('schoolid',0,'intval');
    	if ($schoolid < 1) {
    		echo json_encode(array('code' => 0, 'info' => '参数(schoolid)有误！'));
    		exit;
    	}
    	$res = D('Common/Equipment')->field('id,name')->where(array('school_id' => $schoolid, 'time' => array('GT',0)))->select();
    	if ($res) {
    		echo json_encode(array('code' => 1, 'data' => $res));
    	}else{
    		echo json_encode(array('code' => 2, 'info' => '查无相关数据！'));
    	}
    }

    public function update_tags_api(){
    	checkLogin('manager');

    	$tags = I('tags');
    	if (empty($tags)) {
    		echo json_encode(array('code' => 0, 'info' => '提交空数据，未处理!'));
    		exit;
    	}
    	if (is_array($tags)) {
    		foreach ($tags as $key => $value) {
    			if (strlen($key) != 24) {
    				$failed[] = $key;
    			}else{
    				$data[]['tag'] = $key;
    				$succeed[] = $key;
    			}
    		}
    		echo empty($data) ? json_encode(array('code' => 1, 'data' => array('failed' => $failed, 'succeed' => array()))) : (D('Common/Authtag')->addAll($data) ? json_encode(array('code' => 1, 'data' => array('succeed' => $succeed, 'failed' => $failed))) : json_encode(array('code' => 0, 'info' => '入库失败')));
    	}else{
    		echo json_encode(array('code' => 0, 'info' => '未识别数据类型!'));
    	}
    }
}