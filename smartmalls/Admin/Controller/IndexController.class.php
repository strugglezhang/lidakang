<?php
namespace Admin\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
        if (!isset($_SESSION['manager'])) {
    		$this->redirect('Login/index');
    	}
    	$this->display();
    }
}