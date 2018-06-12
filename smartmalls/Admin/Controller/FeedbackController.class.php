<?php
namespace Admin\Controller;
use Think\Controller;
class FeedbackController extends Controller {
    public function index(){
        if (!isset($_SESSION['manager'])) {
    		$this->redirect('Login/index');
    	}

        $this->display();
    }

    public function info_api(){
        checkLogin('manager');

        switch ($_POST['flag']) {
            case 'news':
                $new_id = D('Common/Feedback')->field('id')->order('id desc')->find();
                if ($new_id) {
                    echo json_encode(array('code' => 1, 'eid' => $new_id['id']));
                }else{
                    echo json_encode(array('code' => 2, 'info' => '查无相关数据！'));
                }
                break;
            case 'list':
                //type: 2->园长, 3->教师, 4->家长
                $size = I('size',10,'intval');
                $type = I('type','');
                if (!empty($type)) {
                    $where['type'] = $type;
                }
                $order = 'id desc';
                $eid = I('eid',0,'intval');
                if ($eid) {
                    $dd = I('dd');
                    if ($dd && $dd === 'new') {
                        $where['id'] = array('GT',$eid);
                        $order = 'id asc';
                    }else{
                        $where['id'] = array('LT',$eid);
                    }
                }
                $list = D('Common/Feedback')->where($where)->order($order)->limit($size)->select();
                // echo $list;die;
                if (empty($list)) {
                    echo json_encode(array('code' => 2, 'info' => '查无相关数据！'));
                }else{
                    foreach ($list as $key => $value) {
                        $list[$key]['time'] = date('Y-m-d H:i:s',$value['time']);
                    }
                    echo json_encode(array('code' => 1, 'data' => $list));
                }
                break;
            case 'view':
                $id = I('id',0,'intval');
                if ($id < 1) {
                    echo json_encode(array('code' => 0, 'info' => 'wrong id'));
                    exit;
                }
                $action = I('action',0,'intval');
                if ($action != 1 && $action !=2) {
                    echo json_encode(array('code' => 0, 'info' => 'wrong action'));
                    exit;
                }
                if (D('Common/Feedback')->save(array('id' => $id, 'view' => $action))) {
                    echo json_encode(array('code' => 1));
                }else{
                    echo json_encode(array('code' => 0, 'info' => 'fail or none changed'));
                }
                break;
            default:
                echo json_encode(array('code' => 3, 'info' => 'Unknowned flag'));
                break;
        }
    }
}