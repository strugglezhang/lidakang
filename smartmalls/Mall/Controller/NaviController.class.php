<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/6/2
 * Time: 15:28
 */

namespace Mall\Controller;


class NaviController extends CommonController
{
    public function getFirst()
    {
        $roleId = session("mall.role_id");
        if (empty($roleId)) {
            Ret(array('code' => 2, 'info' => '重新登录', "data" => []));
        }
        $model = D("Navi");
        $all = $model->where("role_id=$roleId")->field("*")->find();
        if (empty($all)) {
            Ret(array('code' => 2, 'info' => '暂无权限', "data" => []));
        }

        $first = explode(",", $all['first_level']);
        $c = array();
        foreach ($first as $item) {
            $data['first'] = $item;
            array_push($c, $data);
        }
        Ret(array('code' => 1, 'info' => '请求成功', 'data' => $c));

    }

    public function getTherd()
    {
        $roleId = session("mall.role_id");
        if (empty($roleId)) {
            Ret(array('code' => 2, "info" => '重新登录', 'data' => []));
        }
        $model = D("Navi");
        $all = $model->where("role_id=$roleId")->field("*")->find();
        if (empty($all)) {
            Ret(array('code' => 2, 'info' => '暂无权限', "data" => []));
        }

        $first = explode(",", $all['therd_level']);
        $c = array();
        foreach ($first as $item) {
            $data['therd'] = $item;
            array_push($c, $data);
        }
        Ret(array('code' => 1, 'info' => "获取成功", 'data' => $c));

    }

    public function test()
    {
        $naviModel = D('NaviSecond');
        $a = $naviModel->field("*")->select();
        $str = "";
        foreach ($a as $item) {
            $str .= trim($item['name']) . ",";
        }
        echo substr($str, 0, strlen($str) - 1);
        die;

    }

    public function getSecond()
    {
        $roleId = session("mall.role_id");
        if (empty($roleId)) {
            Ret(array('code' => 2, 'info' => '重新登录', "data" => []));
        }
        $model = D("Navi");
        $all = $model->where("role_id=$roleId")->field("*")->find();
        if (empty($all)) {
            Ret(array('code' => 2, "info" => "暂无权限", 'data' => []));
        }

        $first = explode(",", $all['second_level']);
        $c = array();
        foreach ($first as $item) {
            $data['second'] = $item;
            array_push($c, $data);
        }
        Ret(array('code' => 1, "info" => "请求成功", 'data' => $c));

    }

    //导航菜单列表（一级菜单列表）
    public function index_api()
    {
        before_api();
        checklogin();
        checkAuth();
        $naviModel = D('NaviFirst');
        $roleModel = D('Role');

        //var_dump($role);die;
        //$mall_id=session('mall.mall_id');
        $page = I('page', 1, 'intval');
        $pagesize = I('pagesize', 10, 'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page . ',' . $pagesize;
        $count = $naviModel->count();
        $data = $naviModel->page($page)->select();
        foreach ($data as $key => $value) {
            $role = $roleModel->get_role_by_id($value['id']);
            $datas[$key]['role_name'] = $role[0]['rolename'];
            $datas[$key]['id'] = $data[$key]['id'];
            $datas[$key]['first_level'] = $data[$key]['first_level'];
        }

        if ($datas) {
            Ret(array('code' => 1, 'data' => $datas, 'total' => $count, 'page_count' => ceil($count / $pagesize)));
        } else {
            Ret(array('code' => 2, 'data' => '数据获取失败'));
        }
    }

    //导航菜单列表（二级菜单列表）
    public function second_navi_list()
    {
        before_api();
        checklogin();
        checkAuth();
//        $naviModel = D('NaviSecond');
//        $roleModel = D('Role');
//
//        //var_dump($role);die;
//        //$mall_id=session('mall.mall_id');
//        $page = I('page', 1, 'intval');
//        $pagesize = I('pagesize', 10, 'intval');
//        $pagesize = $pagesize < 1 ? 1 : $pagesize;
//        $pagesize = $pagesize > 50 ? 50 : $pagesize;
//        $page = $page . ',' . $pagesize;
//        $count = $naviModel->count();
//        $data = $naviModel->page($page)->select();
//        foreach ($data as $key => $value) {
//            $role = $roleModel->get_role_by_id($value['id']);
//            $data[$key]['role_name'] = $role[0]['rolename'];
//            $datas[$key]['id'] = $data[$key]['id'];
//            $datas[$key]['first_level'] = $data[$key]['first_level'];
//            $datas[$key]['second_level'] = $data[$key]['second_level'];
//        }
//
//        if ($data) {
//            Ret(array('code' => 1, 'data' => $data, 'total' => $count, 'page_count' => ceil($count / $pagesize)));
//        } else {
//            Ret(array('code' => 2, 'data' => '数据获取失败'));
//        }
        $secondModel = D("NaviSecond");
        Ret(array('code' => 1,'info' => '获取成功', 'data' => $secondModel->field("*")->select()));
    }

    //导航菜单列表( 三级菜单列表）
    public function third_navi_list()
    {
        before_api();
        checklogin();
        checkAuth();
        $naviModel = D('NaviThird');
        $roleModel = D('Role');

        //var_dump($role);die;
        //$mall_id=session('mall.mall_id');
        $page = I('page', 1, 'intval');
        $pagesize = I('pagesize', 10, 'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page . ',' . $pagesize;
        $count = $naviModel->count();
        $data = $naviModel->page($page)->select();
        foreach ($data as $key => $value) {
            $role = $roleModel->get_role_by_id($value['role_id']);
            $data[$key]['role_name'] = $role[0]['rolename'];
            $datas[$key]['id'] = $data[$key]['id'];
            $datas[$key]['first_level'] = $data[$key]['first_level'];
            $datas[$key]['second_level'] = $data[$key]['second_level'];
            $datas[$key]['third_level'] = $data[$key]['therd_level'];
        }

        if ($data) {
            Ret(array('code' => 1, 'data' => $data, 'total' => $count, 'page_count' => ceil($count / $pagesize)));
        } else {
            Ret(array('code' => 2, 'data' => '数据获取失败'));
        }
    }

    public function permissions()
    {
        $permissionsModel = D('Permissions');
        $data = $permissionsModel->get_permissions();
        if ($data) {
            Ret(array('code' => 1, 'data' => $data));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据，系统有误'));
        }
    }


    var $firstNavi = array(array('id' => 1, 'name' => '员工管理'), array('id' => 2, 'name' => '机构管理'), array('id' => 3, 'name' => '教室管理'), array('id' => 4, 'name' => '会员管理'));

    public function firstNavi()
    {
        before_api();
        $m = D("NaviFirst");
        Ret(array('code' => 1,'info' => '获取成功', 'data' => $m->field("*")->select()));


    }

    var $firstNavi1 = array(1 => '员工管理', 2 => '机构管理', 3 => '教室管理', 4 => '会员管理');

    public function firstNavi1()
    {
        before_api();
        Ret(array('code' => 1, 'data' => $this->firstNavi1));

    }

    private function get_firstNavi($id)
    {
        $array = explode(',', $id);
        foreach ($array as $k => $v) {
            $items[$k] = $this->firstNavi1[$v];
        }
        return implode(',', $items);
    }


    private function get_room_by_id($id)
    {

        $array = explode('-', $id);
        foreach ($array as $k => $v) {
            $item = D('Class')->get_room($v);
            if ($item[0][position] != null) {
                $items[$k] = $item[0][position];
            }
            $data = implode(',', $items);
        }
        return $data;
    }

    var $secondNavication = array(array('id' => 1, 'name' => '员工管理'), array('id' => 2, 'name' => '机构管理'), array('id' => 3, 'name' => '教室管理'), array('id' => 4, 'name' => '会员管理'));

    public function secondNavi()
    {
        before_api();
        Ret(array('code' => 1, 'data' => $this->secondNavication));

    }

    var $secondNavi = array(1 => '员工管理', 2 => '机构管理', 3 => '教室管理', 4 => '会员管理');

    public function secondNavi1()
    {
        before_api();
        Ret(array('code' => 1, 'data' => $this->secondNavi));

    }

    private function get_secondNavi($id)
    {
        $array = explode(',', $id);
        foreach ($array as $k => $v) {
            $items[$k] = $this->firstNavi1[$v];
        }
        return implode(',', $items);

    }

    //导航增删改api
    public function navi_dml_api()
    {
        before_api();
        $flag = I('flag');
        $roleid = I('roleid');
        $data = I('data');
        $naviModel = D('Navi');
        $navi = ['first' => "一级菜单", "second" => "二级菜单", "third" => "三级菜单"];
        switch ($flag) {
            case 'add':
                $id = $naviModel->where("role_id=$roleid")->getField("id");
                if ($id) {
                    Ret(array('code' => 2, 'info' => '角色已存在!'));
                }
                if ($roleid == null) {
                    Ret(array('code' => 2, 'info' => '请选角色!'));
                }

                foreach ($navi as $key => $value) {
                    if (!isset($data[$key])) {
                        Ret(array('code' => 1, 'info' => $value . '不能为空!'));
                    }
                }
                $insert['first_level'] = join(",", $data['first']);
                $insert['second_level'] = join(",", $data['second']);
                $insert['therd_level'] = join(",", $data['third']);
                $insert['role_id'] = $roleid;
                if ($naviModel->add_navi($insert)) {
                    Ret(array('code' => 1, 'info' => '添加导航成功!'));
                } else {
                    Ret(array('code' => 2, 'info' => '添加导航失败!'));
                }
                break;
            case 'update':
                $id = I('id');
                if (empty($id)) {
                    Ret(array('code' => 2, 'info' => '添加导航失败!'));
                    die;
                }
                if ($roleid == null) {
                    Ret(array('code' => 2, 'info' => '请选角色!'));
                }

                foreach ($navi as $key => $value) {
                    if (!isset($data[$key])) {
                        Ret(array('code' => 1, 'info' => $value . '不能为空!'));
                    }
                }
                $insert['first_level'] = join(",", $data['first']);
                $insert['second_level'] = join(",", $data['second']);
                $insert['therd_level'] = join(",", $data['third']);
                $insert['role_id'] = $roleid;
                $res = $naviModel->where("id=$id")->save($insert);
                if ($res) {
                    Ret(array('code' => 1, 'info' => '保存成功!'));
                } else {
                    Ret(array('code' => 2, 'info' => '更新失败!'));
                }
                break;
            case 'delete':
                $id = I('id', '');
                if (empty($id)) {
                    Ret(array('code' => 2, 'info' => '获取id失败!'));
                }
                if ($naviModel->delete_role($id)) {
                    Ret(array('code' => 1, 'info' => '删除成功!'));
                } else {
                    Ret(array('code' => 2, 'info' => '删除失败!'));
                }
                break;
            default:
                Ret(array('code' => 2, 'info' => 'Unknowed flag!'));
                break;
        }

    }


    public function navi_third_dml_api()
    {
        before_api();
        checkLogin();
        checkAuth();
        $flag = I('flag');
        $naviTihrdModel = D('NaviThird');
        // $mall_id = session('mall.mall_id');
        switch ($flag) {
            case 'add':
                $data['role_id'] = I('role_id');
                if ($data['role_id'] == null) {
                    Ret(array('code' => 2, 'info' => '请选角色!'));
                }
                $data['first_level'] = I('first_level');
                if ($data['first_level'] == null) {
                    Ret(array('code' => 2, 'info' => '请选一级菜单!'));
                }
                $data['second_level'] = I('second_level', '');
                if ($data['second_level'] == null) {
                    Ret(array('code' => 2, 'info' => '请选二级菜单!'));
                }
                $data['therd_level'] = I('therd_level', '');
                if ($data['therd_level'] == null) {
                    Ret(array('code' => 2, 'info' => '请选三级菜单!'));
                }
                if ($naviTihrdModel->add_navi($data)) {
                    Ret(array('code' => 1, 'info' => '添加导航成功!'));
                } else {
                    Ret(array('code' => 2, 'info' => '添加导航失败!'));
                }
                break;
            case 'update':
                $data['id'] = I('id');
                if ($data['id'] == null) {
                    Ret(array('code' => 2, 'info' => '获取id失败!'));
                }
                $data['role_id'] = I('role_id');
                if (!$data['role_id']) {
                    Ret(array('code' => 2, 'info' => '请选角色!'));
                }
                $data['first_level'] = I('first_level');
                if ($data['first_level'] == null) {
                    Ret(array('code' => 2, 'info' => '请选一级菜单!'));
                }
                $data['second_level'] = I('second_level', '');
                if ($data['second_level'] == null) {
                    Ret(array('code' => 2, 'info' => '请选二级菜单!'));
                }
                $data['therd_level'] = I('therd_level', '');
                if ($data['therd_level'] == null) {
                    Ret(array('code' => 2, 'info' => '请选三级菜单!'));
                }
                if ($naviTihrdModel->update_role($data)) {
                    Ret(array('code' => 1, 'info' => '保存成功!'));
                } else {
                    Ret(array('code' => 2, 'info' => '更新失败!'));
                }
                break;
            case 'delete':
                $id = I('id', '');
                if ($id == null) {
                    Ret(array('code' => 2, 'info' => '获取id失败!'));
                }
                if ($naviTihrdModel->delete_role($id)) {
                    Ret(array('code' => 1, 'info' => '删除成功!'));
                } else {
                    Ret(array('code' => 2, 'info' => '删除失败!'));
                }
                break;
            default:
                Ret(array('code' => 2, 'info' => 'Unknowed flag!'));
                break;
        }

    }


    public function getFirstNavi()
    {
        $page = I('page', 1, 'intval');
        $pagesize = I('pagesize', 10, 'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page . ',' . $pagesize;
        $naviThirdModel = D('Navi');
        $roleModel = D('Role');
        $count = $naviThirdModel->count();
        $data = $naviThirdModel->page($page)->select();
        foreach ($data as $key => $value) {
            $role = $roleModel->get_role_by_id($value['role_id']);

            $data[$key]['role_name'] = $role[0]['rolename'];
        }
        if ($data) {
            Ret(array('code' => 1, 'data' => $data, 'total' => $count, 'page_count' => ceil($count / $pagesize)));
        } else {
            Ret(array('code' => 2, 'data' => '数据获取失败'));
        }
    }


}