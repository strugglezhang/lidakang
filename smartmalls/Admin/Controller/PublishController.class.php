<?php
namespace Admin\Controller;
use Think\Controller;
class PublishController extends Controller {
    public function app() {
    	if (!isset($_SESSION['manager'])) {
    		$this->redirect('Login/index');
    	}elseif (!empty($_POST)) {
    		$data['remark'] = I('remark');
    		if (empty($data['remark'])) {
    			$this->error('备注不能为空！');
    		}
    		$data['type'] = I('type',0,'intval');
    		if ($data['type'] < 1 || $data['type'] > 5) {
    			$this->error('非法类型！');
    		}
    		$data['version'] = I('version',false);

    		$versionModel = D('Common/Version');
    		if ($data['version']) {
    			$latest_version = $versionModel->get_latest_version($data['type']);
    			if ($latest_version && version_compare($data['version'], $latest_version, '<=')) {
    				$this->error('版本号低于最新版本!');
    			}
    		}else{
    			$this->error('版号不能为空！');
    		}

    		$data['time'] = NOW_TIME;
    		$url = I('url');
    		if (empty($url)) {
    			switch ($data['type']) {
	    			case 1:
	    				$savename = 'HuiBaoBei_Teacher_'.$data['version'];
	    				break;
	    			case 2:
	    				$savename = generateToken(16).$data['version'];
	    				break;
	    			case 3:
	    				$savename = 'HuiBaoBei_Parent_'.$data['version'];
	    				break;
	    			case 4:
	    				$savename = 'HuiBaoBei_Leader_'.$data['version'];
	    				break;
	    			case 5:
	    				$savename = 'HuiBaoBei_RFID_'.$data['version'];
	    				break;
	    			default:
	    				# code...
	    				break;
	    		}
	    		$rootPath = APP_ROOT.'/smartbaby/Public/Uploads/App/';
		    	$config = array(
		    		'maxSize' => 20971520 ,// 设置附件上传大小,20MB
		    		'exts' => array('apk','exe','jpg'),// 设置附件上传类型
		    		'rootPath' => $rootPath, // 设置附件上传根目录
		    		'autoSub' => true,
		    		'subName' => '',
		    		'savePath' => '',
		    		'saveName' => $savename,
		    	);
			    $upload = new \Think\Upload($config);// 实例化上传类

			    $info   =   $upload->uploadOne($_FILES['appfile']);
			    if(!$info) {
			    	$this->error($upload->getError());
			    	exit;
			    }else{
			    	$data['url'] = 'http://'.$_SERVER['SERVER_NAME'].'/smartbaby/Public/Uploads/App/'.$savename.'.'.$info['ext'];
			    	$data['size'] = $info['size'];
			    }
    		}else{
    			$data['url'] = $url;
    			$data['size'] = I('size',0,'intval');
    		}

	    	if ($versionModel->addNewVersion($data)) {
		    	$this->success('版本更新成功！','/Admin/Publish/history');
	    	}else{
	    		$this->error('版本更新失败！');
	    	}
    	}else{
	    	$this->display();
    	}
    }

    public function app_check(){
        checkLogin('manager');

        $data['remark'] = I('remark');
        if (empty($data['remark'])) {
            echo json_encode(array('code' => 0, 'info' => '备注不能为空！'));
            exit;
        }
        $data['type'] = I('type',0,'intval');
        if ($data['type'] < 1 || $data['type'] > 5) {
            echo json_encode(array('code' => 0, 'info' => '非法类型！'));
            exit;
        }
        $data['version'] = I('version',false);

        $versionModel = D('Common/Version');
        if ($data['version']) {
            $latest_version = $versionModel->get_latest_version($data['type']);
            if ($latest_version && version_compare($data['version'], $latest_version, '<=')) {
                echo json_encode(array('code' => 0, 'info' => '版本号低于最新版本!'));
                exit;
            }
        }else{
            echo json_encode(array('code' => 0, 'info' => '版号不能为空！'));
            exit;
        }
        echo json_encode(array('code' => 1));
    }


    public function history() {
    	if (!isset($_SESSION['manager'])) {
    		$this->redirect('Login/index');
    	}
    	$this->display();
    }

    public function recycle_bin() {
    	if (!isset($_SESSION['manager'])) {
    		$this->redirect('Login/index');
    	}
    	$this->display();
    }

    public function history_api(){
    	checkLogin('manager');

    	$type = I('type');
    	$page = I('page',1,'intval');
    	$params['where']['state'] = I('state',false,'intval');
    	if ($params['where']['state'] !== 0 && $params['where']['state'] !== 1) {
    		echo json_encode(array('code' => 0, 'info' => '参数（state）有误!'));
    		exit;
    	}

    	$params['page'] = $page.',10';
    	if (!empty($type)) {
    		$params['where']['type'] = $type;
    	}

    	$ret = D('Common/Version')->publish_history($params);
    	if ($ret) {
    		foreach ($ret as $key => $value) {
    			$ret[$key]['size'] = get_byte($value['size']);
    			$ret[$key]['time'] = date('Y-m-d H:i:s',$value['time']);
    		}
    		echo json_encode(array('code' => 1, 'data' => $ret));
    	}else{
    		echo json_encode(array('code' => 2, 'info' => '查无相关数据！'));
    	}
    }

    public function history_dml_api(){
    	checkLogin('manager');

    	$id = I('id',0,'intval');
    	if ($id < 1) {
    		echo json_encode(array('code' => 0, 'info' => '参数（id）有误!'));
    		exit;
    	}
    	$flag = I('flag',false);
    	if ($flag !== 'delete' && $flag !== 'recover') {
    		echo json_encode(array('code' => 0, 'info' => '参数（flag）有误!'));
    		exit;
    	}
    	$state = $flag === 'delete' ? 0 : 1;
    	$ret = D('Common/Version')->where(array('id' => $id))->save(array('state' => $state));
    	if ($ret) {
    		echo json_encode(array('code' => 1));
    	}else{
    		echo json_encode(array('code' => 0, 'info' => '操作失败'));
    	}
    }

    
}