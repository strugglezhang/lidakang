<?php

namespace Admin\Controller;

use Think\Controller;

class HomeController extends Controller
{
    public function index()
    {
        if (!isset($_SESSION['manager'])) {
            $this->redirect('Login/index');
        }
    }


    public function logout_api()
    {
        isset($_SESSION['manager']) && session(null);
        echo json_encode(array('code' => 1, 'info' => 'logout succeed.'));
    }

    public function logout()
    {
        isset($_SESSION['manager']) && session(null);
        $this->redirect('Login/index');
    }

    public function push_count_api()
    {
        if (isset($_SESSION['admin']) || isset($_SESSION['parent']) || isset($_SESSION['manager'])) {
            $times = I('times', 1, 'intval');
            $redis = new \Redis();
            $redis->connect('localhost', 6379);
            $redis->select(6);
            $key = date('Y-m-d', NOW_TIME) . '-rev-times';
            if ($times === 1) {
                $ret = $redis->INCR($key);
            } else {
                $ret = $redis->INCRBY($key, $times);
            }
            echo json_encode(array('code' => 1, 'key' => $key, 'times' => $ret));
        } else {
            echo json_encode(array('code' => 0, 'info' => '非法操作!'));
        }
    }

    public function getUserMenuList()
    {
        $number = I("number");

        if(empty($number)){
            Ret(array('code' => 2, 'info' => 'have a empty params'));die;
        }

        $model  = D('MallStaff');
        $roleId = $model->where("number= $number")->getField("role_id");

        if($roleId <1){
            Ret(array('code' => 2, 'info' => '暂无权限'));die;
        }

        $naviModel = D('Navi');
        $menuList = $naviModel->where("role_id = $roleId")->select();
        Ret(array('code' => 1, 'info' => '成功','data'=> $menuList));







//        header('Content-Type:text/html; charset=utf-8');//防止出现乱码
//        $number = $_REQUEST['number'];
//        $model  = D('MallStaff');
//        $userid = $model->where("number= $number")->getField('id');
//
//        if(empty($userid)){
//            echo json_encode(['errorCode' => 100, 'msg' => '没有该用户','data' => []]);
//            exit();
//        }
//
//        $roleid = $this->getUserRoleId($userid);
//        if (empty($roleid)) {
//            echo json_encode(['errorCode' => 100, 'msg' => '该用户没有角色','data' => []]);
//            exit();
//        }
//        $result = [];
//        $naviModel = D('Navi');
//        $firstLevelArr = $naviModel->getFirstMenu(['role_id' => $roleid]);
//
//        foreach ($firstLevelArr as $key => $val) {
//            $arr = $naviModel->getSecondMenu($val['first_level']);
//            foreach ($arr as $k => $v) {
//
//                if($v['second_level'] == ''){
//                    continue;
//                }
//                $therdMenu = $naviModel->getTherdMenu($v['second_level']);
//                $tmp = [];
//                foreach ($therdMenu as $value) {
//                    if ($value['therd_level']  == '') {
//                        continue;
//                    }
//                    array_push($tmp, $value['therd_level']);
//                }
//                $result[$val['first_level']][$v['second_level']] = $tmp;
//            }
//        }
//        echo json_encode(['errorCode' => 0, 'msg' => '请求成功','data' => $result]);
    }


    private function getUserRoleId($userid)
    {

        $model  = D('MallStaff');
        $userinfo = $model->where('number=' . $userid)->getfield('role_id');
        if (empty($userinfo)) {
            return false;
        }

        return $userinfo;
    }

    public function editMenuByRoleId()
    {
        $uniqId = I('uniqId'); //navi  Id
        $userId = I('userId'); //user Id
        $firstLevel = I('firstLevel');
        $secondLevel = I('secondLevel');
        $thredLevel = I('thredLevel');

        if(empty($userId) || empty($uniqId) || empty($firstLevel) || empty($secondLevel) || empty($thredLevel)){
            Ret(array('code' => 2, 'info' => 'have a empty params'));die;
        }

        $naviModel = D('Navi');
        $roleId = $this->getUserRoleId($userId);

        if(empty($roleId)){
            Ret(array('code' => 2, 'info' => 'no have this user'));die;
        }

        $menuData = array(
            'role_id'       => $roleId,
            'first_level'   => $firstLevel,
            'second_level'  => $secondLevel,
            'therd_level'   => $thredLevel
        );

        $ret = $naviModel->updateMenuByRoleId($uniqId, $menuData);
        if(1 == $ret){
            Ret(array('code' => 1, 'info' => '修改成功'));
        } else {
            Ret(array('code' => 2, 'info' => '修改失败'));
        }


    }

    public function addMenuByRoleId()
    {
        $userName = I('userName');
        $firstLevel = I('firstLevel');
        $secondLevel = I('secondLevel');
        $thirdLevel = I('therdLevel');

        if(empty($userName) || empty($firstLevel) || empty($secondLevel) || empty($thirdLevel)){
            Ret(array('code' => 2, 'info' => 'have a empty paramrs'));die;
        }
        $roleModel = D("Role");
        $userInfo =  $roleModel->where("rolename= '".$userName."'")->getField("id");

        if(empty($userInfo)){
            Ret(array('code' => 2, 'info' => 'no have this user'));die;
        }

        $naviModel = D('Navi');

        $menuData = array(
            'role_id' => $userInfo,
            'first_level' => $firstLevel,
            'second_level' => $secondLevel,
            'therd_level'  => $thirdLevel
        );

        $res = $naviModel->addMenuByRoleId($menuData);
        if ($res){
            Ret(array('code' => 1, 'info' => 'success'));
        } else {
            Ret(array('code' => 2, 'info' => 'faild'));
        }

    }

    public function delMenuByUniqId()
    {
        $uniqId = I('uniqId');
        $userId = I('userId');

        if(empty($uniqId) || empty($userId)){
            Ret(array('code' => 2, 'info' => 'have a empty paramrs'));die;
        }

        $naviModel = D('Navi');
        $model  = D('MallStaff');
        $res = $model->where("number = $userId")->setField("role_id","-1");
        $ret = $naviModel->where("id = $uniqId")->delete();
        if((false != $ret) && (false != $res)){
            Ret(array('code' => 1, 'info' => 'success'));die;
        }
        Ret(array('code' => 2, 'info' => 'faild'));
    }


    public function getAllMenu()
    {
        $firstMenuModel = D("NaviFirst");
        $secondMenuModel = D("NaviSecond");
        $thirdMenuModel = D("NaviThird");

        $ret = array(
            'firstLevel' => $firstMenuModel->select(),
            'secondLevel' => $secondMenuModel->select(),
            'thirdLevel' => $thirdMenuModel->select()
        );
        Ret(array('code' => 1, 'info' => 'success','data' => $ret));

    }
}