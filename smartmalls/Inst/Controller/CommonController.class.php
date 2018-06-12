<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/8/11
 * Time: 14:53
 */
namespace Inst\Controller;
use Think\Controller;
class CommonController extends Controller
{
    public function _initialize()
    {
//       if(!isset($_SESSION['worker_id'])) {
//            Ret(array('code' => 2, 'info' => '请登录！'));
//        }
    }

    function getTakeout($id){
        return D('CourseTryout')->where('id='.$id)->field('start_time,end_time,room_number')->select();
    }

}