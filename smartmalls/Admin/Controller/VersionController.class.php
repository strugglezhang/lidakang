<?php
namespace Admin\Controller;
use Think\Controller;
class VersionController extends Controller {
	private $string_delimiter = '<-|->';
    public function index(){
        
    }

    private function get_app_info($type,$flag){
    	$fields = 'version,size,url,remark';
    	switch ($flag) {
    		case 'string':
    			$ret = D('Common/Version')->new_version($type,$fields);
    			return $ret ? $ret['version'].$this->string_delimiter.$ret['size'].$this->string_delimiter.$ret['url'].$this->string_delimiter.$ret['remark'] : '';
    			break;
    		case 'json':
    			$ret = D('Common/Version')->new_version($type,$fields);
    			return $ret ? json_encode(array('code' => 1, 'data' => $ret)) : json_encode(array('code' => 0, 'info' => '查无相关信息！'));
    			break;
    		default:
    			return '';
    			break;
    	}
    }

    public function parent_api(){
    	$flag = I('flag','json');
    	echo $this->get_app_info(3,$flag);
    }

    public function teacher_api(){
    	$flag = I('flag','json');
    	echo $this->get_app_info(1,$flag);
    }

    public function leader_api(){
    	$flag = I('flag','json');
    	echo $this->get_app_info(4,$flag);
    }

    public function rfid_api(){
    	$flag = I('flag','string');
    	echo $this->get_app_info(5,$flag);
    }
}