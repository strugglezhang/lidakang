<?php
namespace Admin\Controller;
use Think\Controller;
class SysinfoController extends Controller {
    public function index(){
        if (!isset($_SESSION['manager'])) {
    		$this->redirect('Login/index');
    	}

        $redis = new \Redis();
        $redis->connect('127.0.0.1',6379);
        $this->info = $redis->info();

        $this->web_serverinfo = '<pre>'.shell_exec('ps -ajx|grep "websocket_server.php"').'</pre>';

        $rkey = generateAlphaNumToken(32);

        $data['schoolid'] = 1;
        $data['classesid'] = '';
        $data['id'] = 1;
        $data['type'] = 't';

        $redis->select(5);
        if($redis->hmset($rkey,$data)){
            $redis->expire($rkey, 10);
            $this->token = $rkey;
        }

        $redis->select(6);
        $time = NOW_TIME;
        for ($i=0; $i < 5; $i++) { 
            $date = date('Y-m-d',$time);
            $time = $time - 86400;
            $pkey = $date.'-push-times';
            $rkey = $date.'-rev-times';
            $push = $redis->get($pkey);
            $rev = $redis->get($rkey);
            $pcount[$date]['push'] = $push ? $push : 0;
            $pcount[$date]['rev'] = $rev ? $rev : 0;
        }

        $this->push_count = $pcount;

        $redis->close();
        $this->display();
    }

    public function phpinfo(){
        phpinfo();
    }

}