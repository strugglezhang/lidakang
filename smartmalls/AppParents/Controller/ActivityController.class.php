<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/9/5
 * Time: 15:25
 */

namespace AppParents\Controller;
class ActivityController extends CommonController
{
    public function getAllActivity()
    {
        before_api();
        checkLogin();
        checkAuth();
        $state = 1;
        $pagesize = I('pagesize', 10, 'intval');
        $activityModel = D('Activity');
        $count = $activityModel->get_app_count($state);
        $data = $activityModel->select();
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $type = $this->get_type($value['type']);
                $data[$key]['type'] = $type;
                $host = $this->get_hostname_by_id($value['host_id'], $value['type']);
                $data[$key]['host'] = $host[0]['name'];
            }
        }
        Ret(array('code' => 1, 'data' => $data, 'total' => $count, 'page_count' => ceil($count / $pagesize)));
    }

    /**
     * 活动详情
     */
    public function app_activity_view_api()
    {
        before_api();
        checkLogin();
        checkAuth();
        $id = I('id');
        $activityModel = D('Activity');
        $data = $activityModel->app_activity_info($id);
        foreach ($data as $key => $value) {
            $type = $this->get_type($value['type']);
            $data[$key]['type'] = $type;
            $host = $this->get_hostname_by_id($value['host_id'], $value['type']);
            $data[$key]['host'] = $host[0]['name'];
        }
        if ($data) {
//            $inst = $instModel->get_isnt_info($data[0]['id']);
//            $data[0]['host_name']=$inst[0]['name'];
            Ret(array('code' => 1, 'data' => $data[0]));
        } else {
            Ret(array('code' => 2, 'info' => '没有活动信息'));
        }
    }

    /**
     * 活动报名
     */
    public function app_activity_reserve_api()
    {
        before_api();
        checkLogin();
        checkAuth();
        $activityReserveModel = D('ActivityReserve');
        $data['member_id'] = I('member_id');
        $data['institution_id'] = I('institution_id');
        $data['activity_id'] = I('activity_id');
        $data['time'] = date('Y-m-d H:i:s');
        $result = $activityReserveModel->add_activity_reserve($data);
        if ($result) {
            Ret(array('code' => 1, 'info' => '报名已成功，请在我中查看报名详情!'));
        } else {
            Ret(array('code' => 2, 'info' => '报名失败!'));
        }
    }


    private function get_type($type)
    {
        $types = array('1' => '机构', '2' => '商场', '3' => '商户');
        return isset($types[$type]) ? $types[$type] : '未知';
    }

    private function get_hostname_by_id($host_id, $type)
    {
        switch ($type) {
            case 1:
                $model = D('institution');
                break;
            case 2:
                $model = D('Mall');
                break;
            case 3:
                $model = D('Merchant');
                break;
        }
        return $model->where("id=$host_id")->select();
    }
}