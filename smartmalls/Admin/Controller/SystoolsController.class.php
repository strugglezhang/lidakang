<?php
namespace Admin\Controller;
use Think\Controller;
class SystoolsController extends Controller {
    public function index(){
        if (!isset($_SESSION['manager'])) {
    		$this->redirect('Login/index');
    	}

        $this->display();
    }

    public function get_test_token(){
        checkLogin('manager');

        $phone = I('phone');
        if (!$phone || strlen($phone) != 11) {
            echo json_encode(array('code' => 0, 'info' => '电话号码有误!'));
            exit;
        }
        $type = I('type');
        if ($type !== 'p' && $type !== 't') {
            echo json_encode(array('code' => 0, 'info' => 'type有误!'));
            exit;
        }
        if ($type === 'p') {
            $parent = D('Common/Parent')->field('id,school_id')->where(array('phone' => $phone))->find();
            if ($parent) {
                $data['schoolid'] = $parent['school_id'];
                $data['id'] = $parent['id'];
                $data['type'] = 'p';
                $data['classesid'] = '';
                $classesid = D('Common/SlinkP')->getStudentsByParentId($data['id'],'students.id,students.class_id');
                $cids = array();
                if (!empty($classesid)) {
                    foreach ($classesid as $key => $value) {
                        if (!in_array($value['class_id'], $cids)) {
                            $cids[] = $value['class_id'];
                        }
                    }
                    $data['classesid'] = join(',',$cids);
                }
            }else{
                echo json_encode(array('code' => 0, 'info' => '该电话未注册！'));
                exit;
            }
        }else{
            $teacher = D('Common/Teacher')->field('id,school_id')->where(array('phone' => $phone))->find();
            if ($teacher) {
                $data['schoolid'] = $teacher['school_id'];
                $data['id'] = $teacher['id'];
                $data['type'] = 't';
                $data['classesid'] = '';
                $classesid = D('Common/ClinkT')->getClassesByTeacherId($data['id']);
                $cids = array();
                if (!empty($classesid)) {
                    foreach ($classesid as $key => $value) {
                        if (!in_array($value['class_id'], $cids)) {
                            $cids[] = $value['class_id'];
                        }
                    }
                    $data['classesid'] = join(',',$cids);
                }
            }else{
                echo json_encode(array('code' => 0, 'info' => '该电话未注册！'));
                exit;
            }
        }

        $rkey = generateAlphaNumToken(32);

        $redis = new \Redis();
        $redis->connect('localhost',6379);
        $redis->select(5);
        if($redis->hmset($rkey,$data)){
            $redis->expire($rkey, 10);
            echo json_encode(array('code' => 1, 'token' => $rkey));
        }else{
            echo json_encode(array('code' => 0, 'info' => 'redis error'));
        }
        $redis->close();
    }
}