<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/6/1
 * Time: 16:38
 */

namespace Mall\Controller;

use Inst\Model\MallRevenueModel;

class ClassController extends CommonController
{
    public function index()
    {
        set_time_limit(0);
        header('Connection: Keep-Alive');
        header('Proxy-Connection: Keep-Alive');
        for ($i = 0; $i < 1000; $i++) {
            echo 'hello world';
            flush();
            sleep(3);
            clearstatcache();
        }
    }

    /*
     * 教室信息管理页
     *
     */
    public function index_api()
    {

        before_api();
        checkLogin();
        checkAuth();
        $mall_id = session('mall.mall_id');
        $keyword = I('keyword');
        $page = I('page', 1, 'intval');
        $pagesize = I('pagesize', 10, 'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page . ',' . $pagesize;

        $roomModel = D('Room');
        $roomCatModel = D('RoomCat');
        $CategoryModel = D('InstCat');
        $discountModel = D('RoomDiscount');
        $count = $roomModel->get_count($mall_id, $keyword);
        if ($count)
        {
            $fields = 'id,mall_id,position,area,max_number,price,state,category_name';
            $res = $roomModel->get_list($mall_id, $keyword, $page, $fields);
            if (empty($res)) {
                Ret(array('code' => 2, 'info' => '查无相关数据！'));
            } else
            {
                //foreach ($res as $key => $value) {
                //    $res[$key]['state'] = get_state($value['state']);
                //}
                Ret(array('code' => 1, 'data' => $res, 'total' => $count, 'page_count' => ceil($count / $pagesize)));
            }
        }
        else
        {
            Ret(array('code' => 2, 'info' => '查无相关数据！'));
        }
    }
    //获得审核通过的教室列表
    public function getCheckedRoomList()
    {
        before_api();
        checkLogin();
        checkAuth();
        $mall_id = session('mall.mall_id');
        $keyword = I('keyword');
        $page = I('page', 1, 'intval');
        $pagesize = I('pagesize', 10, 'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page . ',' . $pagesize;
        $roomModel = D('Room');
        $count = $roomModel->getCheckedRoomNum($mall_id,$keyword);
        if ($count) {
            //$fields = 'id,mall_id,position,area,max_number,price,state,category_name';
            //$res = $roomModel->get_list($mall_id, $keyword, $page, $fields);
            $res = $roomModel->getCheckOkRoomList($mall_id,$keyword, $page,$fields = null);
            if (empty($res)) {
                Ret(array('code' => 2, 'info' => '查无相关数据！'));
            } else
            {
               // foreach ($res as $key => $value) {
                //    $res[$key]['state'] = get_state($value['state']);
               // }
                Ret(array('code' => 1, 'data' => $res, 'total' => $count, 'page_count' => ceil($count / $pagesize)));
            }
        }
        else
        {
            Ret(array('code' => 2, 'info' => '查无相关数据！'));
        }


    }
    public function getWeekDay()
    {
        $data = array(
            ['id' => 1, 'name' => '周一'],
            ['id' => 2, 'name' => '周二'],
            ['id' => 3, 'name' => '周三'],
            ['id' => 4, 'name' => '周四'],
            ['id' => 5, 'name' => '周五'],
            ['id' => 6, 'name' => '周六'],
            ['id' => 7, 'name' => '周日'],
        );
        Ret(array('code' => 1, 'data' => $data));

    }


    /* 教室信息管理页(详情)
    *
    */
    public function class_view_api()
    {
        before_api();

        checkLogin();

        checkAuth();
        $id = I('id', 0, 'intval');
        if ($id < 1) {
            Ret(array('code' => 2, 'info' => '参数（id）有误！'));
        }
        //$fields = 'id,mall_id,position,area,max_number,price,state,category_name,industry_name,classify_name,specify_name,category_id,industry_id,classify_id,specify_id,equipment_id,use,discount_rate,discount_time,time_price,member_price,institution_price,equip_ip';
        $res = D('Room')->get_class_infos($id);
        if ($res) {
            //$res[0]['equipment_id'] = $res[0]['equip_ip'];
            Ret(array('code' => 1, 'data' => $res[0]));
        } else {
            Ret(array('code' => 2, 'info' => '获取相关信息失败！'));
        }
    }

    public function getClassRoomInfoByID()
    {
        $id = I('id');
        if (!$id) {
            Ret(array('code' => 1, 'info' => '参数（id）错误！'));
        }
        if ($id) {
            $fields = 'id,position,area,max_number,price,use';
            $res = D('Room')->where('id=' . $id)->field($fields)->find();
            $res['institution_id'] = session("inst.institution_id");
            if ($res) {
                Ret(array('code' => 1, 'data' => $res));
            } else {
                Ret(array('code' => 2, 'info' => '获取相关信息失败！'));
            }
        }
    }

    private function getCats($id)
    {
        $instCatModel = D('InstCat');
        $array = explode('_', $id);
        foreach ($array as $k => $v) {
            $item = $instCatModel->get_cat($v);

        }
        return $item;
    }

    /*
     * 教室增删改api
     */

    public function class_dml_api()
    {

        before_api();
        checkLogin();
        checkAuth();

        $mall_id = session('mall.mall_id');

        $flag = I('flag');
        switch ($flag) {
            case 'add':
                $data['mall_id'] = $mall_id;
                if (!$data['mall_id']) {
                    Ret(array('code' => 2, 'info' => '数据获取失败！'));
                }
                $category_id = I('category_id');
                $data['category_id'] = rtrim($category_id, '_');
                if (!$data['category_id']) {
                    Ret(array('code' => 2, 'info' => '请填写教室类别！'));
                }
                $classify_id = I('classify_id');
                $data['classify_id'] = rtrim($classify_id, '_');
                if (!$data['classify_id']) {
                    Ret(array('code' => 2, 'info' => '请填写教室分类！'));
                }
                $industry_id = I('industry_id');
                $data['industry_id'] = rtrim($industry_id, '_');
                if (!$data['industry_id']) {
                    Ret(array('code' => 2, 'info' => '请填写教室行业！'));
                }
                $specify_id = I('specify_id');
                $data['specify_id'] = rtrim($specify_id, '_');
                if (!$data['industry_id']) {
                    Ret(array('code' => 2, 'info' => '请填写教室细类！'));
                }
                $category_name = I('category_name');
                $data['category_name'] = rtrim($category_name, '_');
                if (!$data['category_name']) {
                    Ret(array('code' => 2, 'info' => '请填写教室类别名！'));
                }
                $classify_name = I('classify_name');
                $data['classify_name'] = rtrim($classify_name, '_');
                if (!$data['classify_name']) {
                    Ret(array('code' => 2, 'info' => '请填写教室分类名！'));
                }
                $industry_name = I('industry_name');
                $data['industry_name'] = rtrim($industry_name, '_');
                if (!$data['industry_name']) {
                    Ret(array('code' => 2, 'info' => '请填写教室行业名！'));
                }
                $specify_name = I('specify_name');
                $data['specify_name'] = rtrim($specify_name, '_');
                if (!$data['industry_name']) {
                    Ret(array('code' => 2, 'info' => '请填写教室细类名！'));
                }
                $data['position'] = I('position');
                if (!$data['position']) {
                    Ret(array('code' => 2, 'info' => '请填写教室位置！'));
                }
                $data['shop_id'] = I('shop_id');
                //var_dump($data['shop_id']);die;
                if (!$data['shop_id']) {
                    Ret(array('code' => 2, 'info' => '请选择店铺！'));
                }

                //$eqInfo = $this->getEqIpById($data['equipment_id']);
               // $data['equip_ip'] = $eqInfo['equip_ip'];
                //$shopInfo=D('Shop')->getShopInfoByPos($eqInfo['position']);
                $shopInfo=D('Shop')->view_shop($data['shop_id']);
                $data['max_number'] = I('max_number');
                if (!$data['max_number']) {
                    Ret(array('code' => 2, 'info' => '请填写教室最大人数！'));
                }
                $data['area'] = I('area');
                if (!$data['area']) {
                    Ret(array('code' => 2, 'info' => '请填写教室面积！'));
                }
                //教室功能
                $data['use'] = I('use');
                if (!$data['use']) {
                    Ret(array('code' => 2, 'info' => '请填写教室教室功能！'));
                }
                $data['price'] = I('price');
                if (!$data['price']) {
                    Ret(array('code' => 2, 'info' => '请填写教室基准价格！'));
                }
                $data['discount_time'] = I('discount_time');
                if (!$data['discount_time']) {
                    Ret(array('code' => 2, 'info' => '请填写教室基准折扣的优惠时长！'));
                }
                $data['discount_rate'] = I('discount_rate');
                if (!$data['discount_rate']) {
                    Ret(array('code' => 2, 'info' => '请填写教室折扣！'));
                }
                if ($data['discount_rate'] > 1 || 0 > $data['discount_rate']) {
                    Ret(array('code' => 2, 'info' => '请正确填写教室折扣（大于0，小于1）！'));
                }
                $data['time_price'] = $_POST['time_price'];
                $data['institution_price'] = I('institution_price');
                $data['member_price'] = I('member_price');
                $data['state'] = 0;
                $data['check_type'] = 0;
                $data['submitter_id'] = session('worker_id');
                $data['submitter'] = session('worker_name');
                $data['shop_id']=$shopInfo[0]['id'];
                $data['shop_pos']=$shopInfo[0]['position'];
                $classModel = D('Room');
                //$condition['shop_id']=$data['shop_id'];
                //$check = $classModel->where($condition)->find();
                $check=hasUsedShop($data['shop_id']);
                //var_dump($check);die;
                $result=false;
                if($check)
                {
                    Ret(array('code' => 2, 'info' => '该店铺已经被使用！'));
                }
                else
                {
                    $result = $classModel->add_room($data);
                    //绑定商铺
                    //var_dump($shopInfo);die;

                    D('Shop')->classBondShopIsOk($data['shop_id'],$result,$data['position']);
                }

                if ($result) {
                    Ret(array('code' => 1, 'info' => '添加成功！'));
                } else {
                    Ret(array('code' => 2, 'info' => '添加失败,系统出错！'));
                }
                break;
            case 'update':
                $data['id'] = I('id');
                if (!$data['id']) {
                    Ret(array('code' => 2, 'info' => '数据(ID)获取失败！'));
                }
                $data['mall_id'] = $mall_id;
                if (!$data['mall_id']) {
                    Ret(array('code' => 2, 'info' => '登录数据获取失败！'));
                }
                $category_id = I('category_id');
                $data['category_id'] = rtrim($category_id, '_');
                if (!$data['category_id']) {
                    Ret(array('code' => 2, 'info' => '请填写教室类别！'));
                }
                $classify_id = I('classify_id');
                $data['classify_id'] = rtrim($classify_id, '_');
                if (!$data['classify_id']) {
                    Ret(array('code' => 2, 'info' => '请填写教室分类！'));
                }
                $industry_id = I('industry_id');
                $data['industry_id'] = rtrim($industry_id, '_');
                if (!$data['industry_id']) {
                    Ret(array('code' => 2, 'info' => '请填写教室行业！'));
                }
                $specify_id = I('specify_id');
                $data['specify_id'] = rtrim($specify_id, '_');
                if (!$data['industry_id']) {
                    Ret(array('code' => 2, 'info' => '请填写教室细类！'));
                }
                $category_name = I('category_name');
                $data['category_name'] = rtrim($category_name, '_');
                if (!$data['category_name']) {
                    Ret(array('code' => 2, 'info' => '请填写教室类别名！'));
                }
                $classify_name = I('classify_name');
                $data['classify_name'] = rtrim($classify_name, '_');
                if (!$data['classify_name']) {
                    Ret(array('code' => 2, 'info' => '请填写教室分类名！'));
                }
                $industry_name = I('industry_name');
                $data['industry_name'] = rtrim($industry_name, '_');
                if (!$data['industry_name']) {
                    Ret(array('code' => 2, 'info' => '请填写教室行业名！'));
                }
                $specify_name = I('specify_name');
                $data['specify_name'] = rtrim($specify_name, '_');
                if (!$data['industry_name']) {
                    Ret(array('code' => 2, 'info' => '请填写教室细类名！'));
                }
                $data['position'] = I('position');
                if (!$data['position']) {
                    Ret(array('code' => 2, 'info' => '请填写教室位置！'));
                }
                $data['shop_id'] = I('shop_id');
                //var_dump($data['shop_id']);die;
                if (!$data['shop_id']) {
                    Ret(array('code' => 2, 'info' => '请选择店铺！'));
                }
                //$eqInfo = $this->getEqIpById($data['equipment_id']);
                //var_dump($eqInfo);die;
                //$data['equip_ip'] = $eqInfo['equip_ip'];
                //$shopInfo=D('Shop')->getShopInfoByPos($eqInfo['position']);
                $shopInfo=D('Shop')->view_shop($data['shop_id']);
                $data['max_number'] = I('max_number');
                if (!$data['max_number']) {
                    Ret(array('code' => 2, 'info' => '请填写教室最大人数！'));
                }
                $data['area'] = I('area');
                if (!$data['area']) {
                    Ret(array('code' => 2, 'info' => '请填写教室面积！'));
                }
                $data['use'] = I('use');
                if (!$data['use']) {
                    Ret(array('code' => 2, 'info' => '请填写教室教室功能！'));
                }
                $data['price'] = I('price');
                if (!$data['price']) {
                    Ret(array('code' => 2, 'info' => '请填写教室基准价格！'));
                }
                $data['discount_time'] = I('discount_time');
                if (!$data['discount_time']) {
                    Ret(array('code' => 2, 'info' => '请填写教室基准折扣的优惠时长！'));
                }
                $data['discount_rate'] = I('discount_rate');
                if (!$data['discount_rate']) {
                    Ret(array('code' => 2, 'info' => '请填写教室折扣！'));
                }
                if ($data['discount_rate'] > 1 || 0 > $data['discount_rate']) {
                    Ret(array('code' => 2, 'info' => '请正确填写教室折扣（大于0，小于1）！'));
                }
//                $data['start_time']  = I('start_time');
//                $data['end_time']  = I('end_time');
//                $data['discount_rate']  = I('discount_rate');
                $data['time_price'] = $_POST['time_price'];
                $data['institution_price'] = I('institution_price');
                $data['member_price'] = I('member_price');
                $data['state'] = 0;
                $data['check_type'] = 4;
                $data['submitter_id'] = session('worker_id');
                $data['submitter'] = session('worker_name');
                $data['shop_id']=$shopInfo[0]['id'];
                $data['shop_pos']=$shopInfo[0]['position'];
                $classModel = D('Room');
                //$condition['equipment_id']=$data['equipment_id'];
                //$check = $classModel->where($condition)->find();
                //$condition['shop_id']=$data['shop_id'];
                //$check = $classModel->where($condition)->find();
                $check=hasUsedShop($data['shop_id']);
                $oldRoomInfo=$classModel->getRoom($data['id']);
                $result=false;
                if($check)
                {
                    if($oldRoomInfo[0]['shop_id']!=$data['shop_id'])
                    {
                        Ret(array('code' => 2, 'info' => '该店铺已经被使用！'));
                    }
                    else
                    {
                        $result = $classModel->update_room($data);
                        if(!empty($oldRoomInfo[0]['shop_id']))
                        {
                            D('Shop')->classUnBondShopIsOk($oldRoomInfo[0]['shop_id']);
                        }

                        D('Shop')->classBondShopIsOk($data['shop_id'],$data['id'],$data['position']);

                        if ($result) {
                            Ret(array('code' => 1, 'info' => '修改成功！'));
                        } else {
                            Ret(array('code' => 2, 'info' => '修改失败,系统出错！'));
                        }
                    }

                }
                else
                {

                    $result = $classModel->update_room($data);
                    if(!empty($oldRoomInfo[0]['shop_id']))
                    {
                        D('Shop')->classUnBondShopIsOk($oldRoomInfo[0]['shop_id']);
                    }

                    D('Shop')->classBondShopIsOk($data['shop_id'],$data['id'],$data['position']);

                    if ($result) {
                        Ret(array('code' => 1, 'info' => '修改成功！'));
                    } else {
                        Ret(array('code' => 2, 'info' => '修改失败,系统出错！'));
                    }
                }
                break;
            case 'delete':
                $id = I('id');
                if (!$id) {
                    Ret(array('code' => 2, 'info' => 'ID获取失败！'));
                }
                $classModel = D('Room');
                $roomInfo=$classModel->getRoom($id);
                $result = $classModel->delete_room($id);

                if(!empty($roomInfo[0]['shop_id'])) {
                    D('Shop')->bondShopInfoclassUnBondShopIsOk($roomInfo[0]['shop_id']);
                }
                //教室计划被清零
                if(!D('CoursePlan')->deleteCoursePlanByRoomId($id))
                {
                    Ret(array('code' => 2, 'info' => '课程计划清除失败！'));
                }
                //教室预订订单清除
                if(!D('RoomReserve')->deleteRoomReserveByRoomId($id))
                {
                    Ret(array('code' => 2, 'info' => '教室订单清除失败！'));
                }
                if ($result) {
                    Ret(array('code' => 1, 'info' => '删除成功！'));
                } else {
                    Ret(array('code' => 2, 'info' => '删除失败,系统出错！'));
                }
                break;
            default:
                Ret(array('code' => 2, 'info' => 'Unknowed flag!'));
                break;
        }
    }

    /*
     * 获取设备ip
     */
    private function getEqIpById($eqID)
    {
        if ($eqID) {
            return D('EquipmentDoor')->where('equip_id=' . $eqID)->find();
        }
    }

    /*
     * 教室折扣管理api
     * 折扣类型type：1（类别），2教室编号
     */
    public function class_discount_api()
    {
        before_api();
        checkLogin();
        checkAuth();
        $mall_id = session('mall.mall_id');
        $state = I('state', 0, 'intval');
        if ($state !== 2 && !$mall_id) {
            $state = 2;
        }
        $page = I('page', 1, 'intval');
        $pagesize = I('pagesize', 10, 'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page . ',' . $pagesize;
//           $classModel = D('Class');
        $classDiscountModel = D('ClassDiscount');
        $classCategoryModel = D('ClassCategory');
        $roomModel = D('Class');
        $categoryModel = D('InstCat');
        $count = $classDiscountModel->get_count($mall_id);
        $fields = ('id,mall_id,type,category_id,room_id,start_time,end_time,discount_rate,state');
        $data = $classDiscountModel->get_list($mall_id, $page, $fields);
        foreach ($data as $key => $item) {
            $data[$key]['state'] = get_state($item['state']);
            $data[$key]['category_name'] = $this->get_cat_by_id($item['category_id']);
            $data[$key]['class_number'] = $this->get_room_by_id($item['room_id']);
        }
        if ($data) {
            Ret(array('code' => 1, 'data' => $data, 'total' => $count, 'page_count' => ceil($count / $pagesize)));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据！'));
        }
    }

    private function get_cat_by_id($id)
    {
        $array = explode('_', $id);
        foreach ($array as $k => $v) {
            $item = D('InstCat')->get_category($v);
            if ($item[0][name] != null) {
                $items[$k] = $item[0][name];
            }
            $data = implode(',', $items);
        }
        return $data;
    }

    /**
     * @param $id
     * @return string
     * 机构分类
     */
    private function get_cls_by_id($id)
    {
        $array = explode('_', $id);
        foreach ($array as $k => $v) {
            $item = D('InstCls')->get_category($v);
            if ($item[0][name] != null) {
                $items[$k] = $item[0][name];
            }
            $data = implode(',', $items);
        }
        return $data;
    }

    /**
     * @param $id
     * @return string
     * 机构行业
     */
    private function get_ind_by_id($id)
    {
        $array = explode('_', $id);
        foreach ($array as $k => $v) {
            $item = D('InstInd')->get_category($v);
            if ($item[0][name] != null) {
                $items[$k] = $item[0][name];
            }
            $data = implode(',', $items);
        }
        return $data;
    }

    /**
     * @param $id
     * @return string
     * 机构细类
     */
    private function get_spe_by_id($id)
    {
        $array = explode('_', $id);
        foreach ($array as $k => $v) {
            $item = D('InstSpe')->get_category($v);
            if ($item[0][name] != null) {
                $items[$k] = $item[0][name];
            }
            $data = implode(',', $items);
        }
        return $data;
    }

    private function get_room_by_id($id)
    {

        $array = explode('-', $id);
        foreach ($array as $k => $v) {
            $item = D('Class')->get_room($v);
            //var_dump($item);die;
            if ($item[0][position] != null) {
                $items[$k] = $item[0][position];
            }
            $data = implode('_', $items);
        }
        return $data;
    }


    /*
    * 教室折扣增删改api
    */

    public function class_discount_dml_api()
    {
        checkLogin();
        before_api();
        checkAuth();
        $mall_id = session('mall.mall_id');
        $classDiscountModel = D('ClassDiscount');
        $flag = I('flag');
        switch ($flag) {
            case 'add':
                $data['mall_id'] = $mall_id;
                $data['type'] = I('type', 0);
                $data['category_id'] = I('category_id', 0);
                $data['room_id'] = I('room_id', 0);
                $data['start_time'] = I('start_time', 0);
                if ($data['start_time'] == null) {
                    Ret(array('code' => 2, 'info' => '请输入折扣开始时间!'));
                }
                $data['end_time'] = I('end_time', 0);
                if ($data['end_time'] == null) {
                    Ret(array('code' => 2, 'info' => '请输入折扣结束时间!'));
                }
                $data['discount_rate'] = I('discount_rate', 0);
                if ($data['discount_rate'] == null) {
                    Ret(array('code' => 2, 'info' => '请输入折扣系数！'));
                }
                if ($data['discount_rate'] > 1 || 0 > $data['discount_rate']) {
                    Ret(array('code' => 2, 'info' => '请正确输入折扣系数（大于0，小于1）！'));
                }
                $data['state'] = I('state', 0);
                $data['check_type'] = I('check_type', 0);
                $data['submitter_id'] = session('worker_id');
                $result = false;
                if ($data['category_id'] != 0) {
                    $filter = filterTime($data['category_id'], strtotime($data['start_time']), strtotime($data['end_time']));
                    if ($filter) {
                        $result = $classDiscountModel->add_room($data);
                    }
                } else {
                    $result = $classDiscountModel->add_room($data);
                }
                if ($result) {
                    Ret(array('code' => 1, 'info' => '添加成功'));
                } else {
                    Ret(array('code' => 2, 'info' => '添加失败,在此日期范围内已有优惠'));
                }
                break;


            case 'update':
                $data['id'] = I('id');
                $data['mall_id'] = $mall_id;
                $data['type'] = I('type', 0);
                $data['category_id'] = I('category_id', 0);
                $data['room_id'] = I('room_id', 0);
                $data['start_time'] = I('start_time', 0);
                if ($data['start_time'] == null) {
                    Ret(array('code' => 2, 'info' => '请输入折扣开始时间!'));
                }
                $data['end_time'] = I('end_time', 0);
                if ($data['end_time'] == null) {
                    Ret(array('code' => 2, 'info' => '请输入折扣结束时间!'));
                }
                $data['discount_rate'] = I('discount_rate', 0);
                if ($data['discount_rate'] == null) {
                    Ret(array('code' => 2, 'info' => '请输入折扣系数！'));
                }
                if ($data['discount_rate'] > 1 || 0 > $data['discount_rate']) {
                    Ret(array('code' => 2, 'info' => '请正确输入折扣系数（大于0，小于1）！'));
                }
                $data['state'] = I('state', 0);
                $data['check_type'] = I('check_type', 4);
                $data['submitter'] = session('worker_id');
                $result = $classDiscountModel->update_room($data);
                if ($result) {
                    Ret(array('code' => 1, 'info' => '修改成功'));
                } else {
                    Ret(array('code' => 2, 'info' => '修改失败,在此日期范围内已有优惠
!'));
                }
                break;
            case 'delete':
                $id = I('id');
                if (!$id) {
                    Ret(array('code' => 2, 'info' => '数据获取失败'));
                }
                $result = $classDiscountModel->del_room($id);
                if ($result) {
                    Ret(array('code' => 1, 'info' => '删除成功'));
                } else {
                    Ret(array('code' => 2, 'info' => '删除失败,系统出错'));
                }
                break;
            default:
                Ret(array('code' => 2, 'info' => 'Unknowed flag!'));
                break;
        }
    }


    /*
     * 教室折扣审核api
     */
    public function class_discount_check_api()
    {
        before_api();
        checkAuth();
        checkLogin();
        $data['id'] = I('id');
        $data['state'] = I('state');
        if (intval($data['id']) < 1 || intval($data['state']) < 0) {
            Ret(array('code' => 2, 'info' => '参数有误!'));
        }
        //step 2: 根据主键id 获取需要的字段信息
        $ClassDiscountCheckoutModel = D('ClassDiscount');
        //step 3 ；修改审核表状态
        $editInfo = $ClassDiscountCheckoutModel->updateState($data);
        if ($editInfo) {
            $info = $ClassDiscountCheckoutModel->getAllField($data['id']);
            foreach ($info as $key => $item) {
                $info[$key]['state'] = get_state($item['state']);
                $info[$key]['class_number'] = $this->get_room_by_id($item['room_id']);
            }
            //step:4 保存日志
            $log = array(
                'check_time' => date('Y-m-d H:i:s'),
                'class_name' => $info[0]['class_number'],
                'check_type' => $info[0]['check_type'],
                'submitter' => $info[0]['submitter'],
                'checker' => session('worker_name'),
                'check_state' => $info[0]['state'],
                'class_id' => $info[0]['id']
            );
            if ($log['check_type'] == 0) {
                $log['check_type'] = '教室折扣新增';
            }
            if ($log['check_type'] == 4) {
                $log['check_type'] = '教室折扣修改';
            }
            $logModel = D('RoomCheckLog');
            if ($logModel->insertLog($log)) {
                Ret(array('code' => 1, 'data' => '审核成功'));
            }
        } else {
            Ret(array('code' => 2, 'info' => '审核失败'));
        }


//        $data['id'] = I('id');
//        if ($data['id'] < 1) {
//            Ret(array('code' => 2, 'info' => '参数（id）有误!'));
//        }
//        $data['state'] = I('state');
//        if ($data['state'] < 1) {
//            Ret(array('code' => 2, 'info' => '参数（state）有误!'));
//        }
//        $ClassDiscountCheckoutModel = D('ClassDiscount');
//        $res= $ClassDiscountCheckoutModel->save($data);
//
////        $logdata['id']=$data['id'];
////        if (!$logdata['id']) {
////            Ret(array('code' => 2, 'info' => '参数（id）有误!'));
////        }
//        $logdata['class_name']=I('room_number');
//        if (!$logdata['class_name']) {
//            Ret(array('code' => 2, 'info' => '参数（clas_sname）有误!'));
//        }
//        $logdata['checker']=session('worker_name');
////        if (!$logdata['checker']) {
////            Ret(array('code' => 2, 'info' => '参数（checker）有误!'));
////        }
//        $logdata['check_type']=I('check_type');
//        if($data['check_type']==0){
//            $logdata['check_type']='教室折扣新增';
//        }
//        if($data['check_type']==4){
//            $logdata['check_type']='教室折扣修改';
//        }
//
//        $logdata['check_time']=date('Y-m-d H:i:s');
////        $logdata['check_time']=session('worker_id');
//        $logdata['submitter']=I('submitter');
//        if (!$logdata['submitter']) {
//            Ret(array('code' => 2, 'info' => '参数（submitter）有误!'));
//        }
//        $logdata['check_state']=I('state');
//        if($data['state']==1){
//            $logdata['result']='通过';
//        }
//        if($data['state']==2){
//            $logdata['result']='未通过';
//        }
//        $logModel=D('RoomCheckLog')->add($logdata);
//        if(!$logModel){
//            Ret(array('code' => 2, 'info' => '日志审核失败'));
//        }
//        if($res){
//            Ret(array('code' => 1, 'info' => '审核成功'));
//        }else{
//            Ret(array('code' => 2, 'info' => '审核失败'));
//        }
    }


    /*
     * 教室信息审核api
     */
    public function class_check_api()
    {
        before_api();
        checkAuth();
        checkLogin();
        $data['id'] = I('id');
        $data['state'] = I('state');
        if (intval($data['id']) < 1 || intval($data['state']) < 0) {
            Ret(array('code' => 2, 'info' => '参数有误!'));
        }
        //step 2: 根据主键id 获取需要的字段信息
        $roomCheckoutModel = D('Room');
        //step 3 ；修改审核表状态
        $editInfo = $roomCheckoutModel->updateState($data);
        //var_dump($editInfo);die;
        if ($editInfo) {
            $info = $roomCheckoutModel->getAllField($data['id']);
            foreach ($info as $key => $item) {
                $info[$key]['state'] = get_state($item['state']);
            }
            //step:4 保存日志
            $log = array(
                'check_time' => date('Y-m-d H:i:s'),
                'class_name' => $info[0]['position'],
                'check_type' => $info[0]['check_type'],
                'submitter' => $info[0]['submitter'],
                'checker' => session('worker_name'),
                'check_state' => $info[0]['state'],
                'class_id' => $info[0]['id']
            );
            if ($log['check_type'] == 0) {
                $log['check_type'] = '教室信息新增';
            }
            if ($log['check_type'] == 4) {
                $log['check_type'] = '教室信息修改';
            }
            $logModel = D('RoomCheckLog');
            if ($logModel->insertLog($log)) {
                Ret(array('code' => 1, 'data' => '审核成功'));
            }
        } else {
            Ret(array('code' => 2, 'info' => '审核失败'));
        }

//        $data['id'] = I('id');
//        if ($data['id'] < 1) {
//            Ret(array('code' => 2, 'info' => '参数（id）有误!'));
//        }
//        $data['state'] = I('state');
//        if ($data['state'] < 1) {
//            Ret(array('code' => 2, 'info' => '参数（check_state）有误!'));
//        }
//        $roomCheckoutModel = D('Room');
//        $res= $roomCheckoutModel->save($data);
//
//        $logdata['class_name']=I('position');
//        $logdata['check_type']=I('check_type');
//        if($logdata['check_type']==0){
//            $logdata['check_type']='教室信息新增';
//        }
//        if($logdata['check_type']==4){
//            $logdata['check_type']='教室信息修改';
//        }
//        $logdata['check_state']=I('state');
//        $logdata['checker']=session('worker_name');
//        $logdata['check_time']=date('Y-m-d H:i:s');
//        $logdata['submitter']=I('submitter');
//        if($data['state']==1){
//            $logdata['result']='通过';
//        }
//        if($data['state']==2){
//            $logdata['result']='未通过';
//        }
//        $logModel=D('RoomCheckLog')->add($logdata);
//        if(!$logModel){
//            Ret(array('code' => 2, 'info' => '日志保存失败'));
//        }
//        if($res){
//            Ret(array('code' => 1, 'info' => '审核成功'));
//        }else{
//            Ret(array('code' => 2, 'info' => '审核失败'));
//        }
    }


    /*
     * 获取机构类别
     */
    public function category()
    {
        before_api();
        $cat = D('InstCat')->get_cat();
        if (!$cat) {
            Ret(array('code' => 2, 'info' => '数据获取失败！'));
        }
        Ret(array('code' => 1, 'data' => $cat));
    }

    /*
    * 获取机构类别
    */
    public function classify()
    {
        before_api();
        $cat = D('InstCls')->get_cat();
        if (!$cat) {
            Ret(array('code' => 2, 'info' => '数据获取失败！'));
        }
        Ret(array('code' => 1, 'data' => $cat));
    }

    /*
    * 获取机构分类
    */
    public function industry()
    {
        before_api();
        $cat = D('InstInd')->get_cat();
        if (!$cat) {
            Ret(array('code' => 2, 'info' => '数据获取失败！'));
        }
        Ret(array('code' => 1, 'data' => $cat));
    }

    /*
    * 获取机构行业
    */
    public function specify()
    {
        before_api();
        $cat = D('InstSpe')->get_cat();
        if (!$cat) {
            Ret(array('code' => 2, 'info' => '数据获取失败！'));
        }
        Ret(array('code' => 1, 'data' => $cat));
    }

    //获取教室编号
    public function get_number()
    {
        before_api();
        $mall_id = session('mall.mall_id');
        $cat = D('Class')->get_list($mall_id);
        foreach ($cat as $k => $v) {
            $data[$k]['id'] = $cat[$k]['id'];
            $data[$k]['name'] = $cat[$k]['position'];
        }
        if (!$cat) {
            Ret(array('code' => 2, 'info' => '数据获取失败！'));
        }
        Ret(array('code' => 1, 'data' => $data));
    }


    /**
     * 教室退订计费管理
     */
    public function class_cost_api()
    {
        before_api();
        checkAuth();
        checkLogin();
        $mall_id = session('mall.mall_id');
        $state = I('state', 2, 'intval');
        $is_manager = true;
        if ($state !== 2 && !$is_manager) {
            $state = 2;
        }
        $page = I('page', 1, 'intval');
        $pagesize = I('pagesize', 10, 'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page . ',' . $pagesize;
        $keyword = I('keyword');
        $roomCheckoutModel = D('RoomCheckout');
        $roomCatModel = D('RoomCat');
        $data = $roomCheckoutModel->get_count($mall_id);
        $fields = 'id,mall_id,category_id,room_id,state,sevenday_rate,sixday_rate,fiveday_rate';
        $res = $roomCheckoutModel->get_list($mall_id, $keyword, $page, $fields);
        foreach ($res as $key => $item) {
            $res[$key]['state'] = get_state($item['state']);
            $res[$key]['category'] = $this->get_cat_by_id($item['category_id']);
            $res[$key]['room_number'] = $this->get_room_by_id($item['room_id']);
        }
        if ($res) {
            Ret(array('code' => 1, 'data' => $res, 'total' => $data, 'page_count' => ceil($data / $pagesize)));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据！'));
        }
    }

    /**
     * 教室计费详情
     * 计费类型type：1（类别），2教室编号
     */
    public function class_cost_view_api()
    {
        before_api();
        checkLogin();
        checkAuth();
        $id = I('id');
        if ($id < 1) {
            Ret(array('code' => 2, 'info' => '参数有误'));
        }
        $res = D('RoomCheckout')->get_info($id);
        if ($res) {
            $res[0]['state'] = get_state($res[0]['state']);
            $res[0]['category'] = $this->get_cat_by_id($res[0]['category_id']);
            Ret(array('code' => 1, 'data' => $res[0]));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据'));
        }

    }

    /**
     * 教室计费增删改
     */
    public function class_cost_dml_api()
    {
        before_api();
        checkAuth();
        checkLogin();
        $mall_id = session('mall.mall_id');
        $roomCheckoutModel = D('RoomCheckout');
        $flag = I('flag');
        switch ($flag) {
            case 'add':
                $data['mall_id'] = $mall_id;
                $data['category_id'] = I('category_id', 0);
                $data['room_number'] = I('room_number', 0);
                $data['oneday_rate'] = I('oneday_rate', 0);
                $data['twoday_rate'] = I('twoday_rate', 0);
                $data['threeday_rate'] = I('threeday_rate', 0);
                $data['fourday_rate'] = I('fourday_rate', 0);
                $data['fiveday_rate'] = I('fiveday_rate', 0);
                $data['sixday_rate'] = I('sixday_rate', 0);
                $data['sevenday_rate'] = I('sevenday_rate', 0);
//                if($data['category_id']==null){
//                    Ret(array('code'=>2,'info'=>'请选择教室类型!'));
//                }
//                if($data['room_number']==null){
//                    Ret(array('code'=>2,'info'=>'请选择教室编号!'));
//                }
                if ($data['oneday_rate'] > 1 || 0 > $data['oneday_rate']) {
                    Ret(array('code' => 2, 'info' => '请正确输入折扣系数（大于0，小于1）！'));
                }
                if ($data['twoday_rate'] > 1 || 0 > $data['twoday_rate']) {
                    Ret(array('code' => 2, 'info' => '请正确输入折扣系数（大于0，小于1）！'));
                }
                if ($data['threeday_rate'] > 1 || 0 > $data['threeday_rate']) {
                    Ret(array('code' => 2, 'info' => '请正确输入折扣系数（大于0，小于1）！'));
                }
                if ($data['fourday_rate'] > 1 || 0 > $data['fourday_rate']) {
                    Ret(array('code' => 2, 'info' => '请正确输入折扣系数（大于0，小于1）！'));
                }
                if ($data['fiveday_rate'] > 1 || 0 > $data['fiveday_rate']) {
                    Ret(array('code' => 2, 'info' => '请正确输入折扣系数（大于0，小于1）！'));
                }
                if ($data['sixday_rate'] > 1 || 0 > $data['sixday_rate']) {
                    Ret(array('code' => 2, 'info' => '请正确输入折扣系数（大于0，小于1）！'));
                }
                if ($data['sevenday_rate'] > 1 || 0 > $data['sevenday_rate']) {
                    Ret(array('code' => 2, 'info' => '请正确输入折扣系数（大于0，小于1）！'));
                }
                $data['state'] = I('state', 0);
                $data['check_type'] = I('check_type', 0);
                $data['submitter_id'] = session('worker_id');
                $data['submitter'] = session('worker_name');
                $data['submit_time'] = date('Y-m-d H:i:s');
                $result = $roomCheckoutModel->add_room($data);
                if ($result) {
                    Ret(array('code' => 1, 'info' => '添加成功'));
                } else {
                    Ret(array('code' => 2, 'info' => '添加失败，系统出错'));
                }
                break;
            case 'update':
                $data['id'] = I('id');
                $data['mall_id'] = $mall_id;
                $data['category_id'] = I('category_id', 0);
                $data['room_number'] = I('room_number', 0);
                $data['oneday_rate'] = I('oneday_rate', 0);
                $data['twoday_rate'] = I('twoday_rate', 0);
                $data['threeday_rate'] = I('threeday_rate', 0);
                $data['fourday_rate'] = I('fourday_rate', 0);
                $data['fiveday_rate'] = I('fiveday_rate', 0);
                $data['sixday_rate'] = I('sixday_rate', 0);
                $data['sevenday_rate'] = I('sevenday_rate', 0);
                if (!$data['id']) {
                    Ret(array('code' => 2, 'info' => '数据（ID）获取失败！'));
                }
//                if($data['category_id']==null){
//                    Ret(array('code'=>2,'info'=>'请选择教室类型!'));
//                }
//                if($data['room_number']==null){
//                    Ret(array('code'=>2,'info'=>'请选择教室编号!'));
//                }
                if ($data['oneday_rate'] > 1 || 0 > $data['oneday_rate']) {
                    Ret(array('code' => 2, 'info' => '请正确输入折扣系数（大于0，小于1）！'));
                }
                if ($data['twoday_rate'] > 1 || 0 > $data['twoday_rate']) {
                    Ret(array('code' => 2, 'info' => '请正确输入折扣系数（大于0，小于1）！'));
                }
                if ($data['threeday_rate'] > 1 || 0 > $data['threeday_rate']) {
                    Ret(array('code' => 2, 'info' => '请正确输入折扣系数（大于0，小于1）！'));
                }
                if ($data['fourday_rate'] > 1 || 0 > $data['fourday_rate']) {
                    Ret(array('code' => 2, 'info' => '请正确输入折扣系数（大于0，小于1）！'));
                }
                if ($data['fiveday_rate'] > 1 || 0 > $data['fiveday_rate']) {
                    Ret(array('code' => 2, 'info' => '请正确输入折扣系数（大于0，小于1）！'));
                }
                if ($data['sixday_rate'] > 1 || 0 > $data['sixday_rate']) {
                    Ret(array('code' => 2, 'info' => '请正确输入折扣系数（大于0，小于1）！'));
                }
                if ($data['sevenday_rate'] > 1 || 0 > $data['sevenday_rate']) {
                    Ret(array('code' => 2, 'info' => '请正确输入折扣系数（大于0，小于1）！'));
                }
                $data['state'] = I('state', 0);
                $data['check_type'] = I('check_type', 4);
                $data['submitter_id'] = session('worker_name');
                $data['submit_time'] = date('Y-m-d H:i:s');
                $result = $roomCheckoutModel->update_room($data);
                if ($result) {
                    Ret(array('code' => 1, 'info' => '修改成功'));
                } else {
                    Ret(array('code' => 2, 'info' => '修改失败，系统出错'));
                }
                break;
            case 'delete':
                $id = I('id');
                if (!$id) {
                    Ret(array('code' => 2, 'info' => '数据获取失败'));
                }
                $result = $roomCheckoutModel->delete_room($id);
                if ($result) {
                    Ret(array('code' => 1, 'info' => '删除成功'));
                } else {
                    Ret(array('code' => 2, 'info' => '删除失败，系统出错！'));
                }
                break;
            default:
                Ret(array('code' => 2, 'info' => 'Unknowed flag!'));
                break;
        }
    }


    /*
     * 教室计费审核api
     */
    public function class_cost_check_api()
    {
        before_api();
        checkAuth();
        checkLogin();
        $data['id'] = I('id');
        $data['state'] = I('state');
        if (intval($data['id']) < 1 || intval($data['state']) < 0) {
            Ret(array('code' => 2, 'info' => '参数有误!'));
        }
        //step 2: 根据主键id 获取需要的字段信息
        $roomCheckoutModel = D('RoomCheckout');
        //step 3 ；修改审核表状态
        $editInfo = $roomCheckoutModel->updateState($data);
        if ($editInfo) {
            $info = $roomCheckoutModel->getAllField($data['id']);
            foreach ($info as $key => $item) {
                $info[$key]['state'] = get_state($item['state']);
                $info[$key]['room_number'] = $this->get_room_by_id($item['room_id']);
            }
            //step:4 保存日志
            $log = array(
                'check_time' => date('Y-m-d H:i:s'),
                'class_name' => $info[0]['room_number'],
                'check_type' => $info[0]['check_type'],
                'submitter' => $info[0]['submitter'],
                'checker' => session('worker_name'),
                'check_state' => $info[0]['state'],
                'class_id' => $info[0]['id']
            );
            if ($log['check_type'] == 0) {
                $log['check_type'] = '教室退费计费新增';
            }
            if ($log['check_type'] == 4) {
                $log['check_type'] = '教室退费计费修改';
            }
            $logModel = D('RoomCheckLog');
            if ($logModel->insertLog($log)) {
                Ret(array('code' => 1, 'data' => '审核成功'));
            }
        } else {
            Ret(array('code' => 2, 'info' => '审核失败'));
        }


//        $data['id'] = I('id');
//        if ($data['id'] < 1) {
//            Ret(array('code' => 2, 'info' => '参数（id）有误!'));
//        }
//        $data['state'] = I('state');
//        if ($data['state'] < 1) {
//            Ret(array('code' => 2, 'info' => '参数（state）有误!'));
//        }
//        $roomCheckoutModel = D('RoomDiscount');
//        $res= $roomCheckoutModel->save($data);
//
//        $logdata['class_name']=I('class_number');
//        $logdata['check_type']=I('check_type');
//        if($logdata['check_type']==0){
//            $logdata['check_type']='教室退费计费新增';
//        }
//        if($logdata['check_type']==4){
//            $logdata['check_type']='教室退费计费修改';
//        }
//        $logdata['check_time']=date('Y-m-d H:i:s');
//        $logdata['checker']=session('worker_name');
//        $logdata['submitter']=I('submitter');
//        if($data['state']==1){
//            $logdata['result']='通过';
//        }
//        if($data['state']==2){
//            $logdata['result']='未通过';
//        }
//        $logdata['check_state']=I('state');
//        $logModel=D('RoomCheckLog')->add($logdata);
//        if(!$logModel){
//            Ret(array('code' => 2, 'info' => '日志审核失败'));
//        }
//        if($res){
//            Ret(array('code' => 1, 'info' => '审核成功'));
//        }else{
//            Ret(array('code' => 2, 'info' => '审核失败'));
//        }
    }

    public function getFavorablePrice()
    {
        $position = I("position"); //教室id
        $startTime = I("start_time");
        $endTime = I("end_time");
        $area = I("area");
        $institutionId = session("inst.institution_id");

        if (empty($position) || empty($startTime) || empty($endTime) || empty($area)) {
            Ret(array('code' => 2, 'info' => '参数错误'));
            die;
        }

        if (empty($institutionId)) {
            Ret(array('code' => 2, 'info' => '请换号登录'));
            die;
        }
        $startTime = strtotime($startTime);
        $endTime = strtotime($endTime);

        $differHour = ceil(($endTime - $startTime) / 3600);
        $roomModel = D("Room");
        //查看当前时间是否有优惠
        $roomList = $roomModel->where("position = '".$position."' ")->field("*")->select();
        $timePrice = array();
        if (!empty($roomList[0]['time_price'])) {
            $timePrice = explode(",", $roomList[0]['time_price']);
        }

        $weekArr = array();

        foreach ($timePrice as $key => $val) {
            $weekArr[$key] = explode("-", $val);
        }

        $res = $this->formatTime($weekArr);
        //时段价格
        if (!empty($res)) {
            $price = $res['time_price'];
        } else {
            $price = $roomList[0]['price'];
        }

        $classDiscountModel = D('ClassDiscount');
        //折扣价格
        $roomId = $roomList[0]['id'];
        $classList = $classDiscountModel->where("room_id = $roomId")->field("*")->find();

        $classStarTime = strtotime($classList['start_time']);
        $classEndTime = strtotime($classList['end_time']);
        $time = time();
        //是否有时段优惠价格
        $discountPrice = sprintf("%.2f", $roomList[0]['price'] * $differHour * $area);

        if (($time >= $classStarTime) && ($time <= $classEndTime)) {
            $discountPrice = sprintf("%.2f", ($price * $classList['discount_rate'] * $differHour * $area));
        } else {
            //统计时长
            $coursePanModel = D("CoursePlan");
            $coursePanList = $coursePanModel->where("institution_id = $institutionId")->select();
//            $coursePanList = $coursePanModel->where("institution_id = 166")->select();
            if (!empty($coursePanList)) {
                //有时长
                $totalTime = 0;

                foreach ($coursePanList as $key => $val) {
                    $diffCourseTime = ceil((strtotime($val['end_time']) - strtotime($val['start_time'])) / 3600);
                    $totalTime += $diffCourseTime;
                }
                //判断是否够时长优惠
                $disCountTime = $roomList[0]['discount_time'];
                if ($totalTime >= $disCountTime) {
                    $discountPrice = sprintf("%.2f", ($roomList[0]['discount_rate'] * $roomList[0]['price'] * $differHour * $area));
                }

            }
        }

        Ret(array('code' => 1, 'info' => '', 'data' => ['price' => $discountPrice]));

    }

    /*
     * 教室预定
     */
    public function class_date()
    {
        before_api();
        checkAuth();
        checkLogin();
        $data['institution_id'] = session('inst.institution_id');
        $startTime = I("start_time");
        $endTime = I("end_time");
        $mall_id = session("mall.mall_id");
        $roomId = I("id");
        $courseId = I("course_id");
        //$maxNumber = I("max_number");
        $roomArea=I('area');
        //$courseName = I("course_name");
        //教室功能
        $use = I("use");
        $maxNumber = I("max_number");
        $price = I("price");
        //$position = I("position");
        $nowTime=date("Y-m-d H:i:s");
        if(strtotime($startTime)>=strtotime($nowTime) && strtotime($endTime)>=strtotime($nowTime)) {
            $usedRoomInfo=D('Room')->getUnusedRoomInfoByRoomId($startTime,$endTime,$roomId);
            if(empty($usedRoomInfo)) {
                $roomInfo = D('Room')->getRoomNumber($roomId);
                //var_dump($roomInfo);die;
                //$shopInfo = D('Shop')->getShopInfoByEqip($roomInfo[0]['equipment_id']);
                //var_dump($shopInfo);die;
                $courseInfo = D('Course')->get_list_by_activityId($courseId);
                //var_dump($courseInfo);die;
                if (empty($startTime) || empty($endTime)) {
                    Ret(array('code' => 2, 'info' => '请选择时间'));
                    die;
                }

                if (empty($data['institution_id'])) {
                    Ret(array('code' => 2, 'info' => '请重新登录'));
                    die;
                }
                $timeLengh = get_time($startTime, $endTime);
                //教室预订接口存教室预订表
                $roomReserveData = array(
                    "mall_id" => $mall_id,
                    "institution_id" => $data['institution_id'],
                    "room_id" => $roomId,
                    "course_id" => $courseId,
                    //教室编号
                    "room_number" => $roomInfo[0]['position'],
                    "course_name" => $courseInfo[0]['name'],
                    "start_time" => $startTime,
                    "end_time" => $endTime,
                    "reserve_time" => date("Y-m-d H:i:s"),
                    "state" => 0,
                    "use" => $use,
                    "max_number" => $maxNumber,
                    "price" => $price,
                    "category" => $roomInfo[0]['category_name'],
                    "category_id" => $roomInfo[0]['category_id'],
                    //报名人数
                    "register_num" => 0,
                    //上课人数
                    "study_num" => 0,
                    "submitter_id" => session('worker_id'),
                    "submitter" => session('worker_name'),
                    "time_long" => $timeLengh,
                    "time_long_unit" => "分"
                );

                $roomReserveModel = D('RoomReserve');
                $res = $roomReserveModel->data($roomReserveData)->add();


                //机构消费录入
                $institutionOutcomeModel = D("InstitutionOutcome");
                $institutionModel = D("Institution");
                $id = $data['institution_id'];
                $institutionName = $institutionModel->where("id = $id")->getField("name");

                $institutionInsertData = array(
                    "institution_id" => $data['institution_id'],
                    "institution_name" => $institutionName,
                    "time" => date("Y-M-d H:s:i", time()),
                    "category" => "类别",
                    "content" => "教室预定",
                    "start_time" => $startTime,
                    "end_time" => $endTime,
                    "time_long" => $timeLengh,
                    "money" => $price,
                    "start_time" => $startTime,
                    "end_time" => $endTime,
                    "classroom_pos" => $roomInfo[0]['position'],
                    "cost_type" => "教室预定",
                    "cost_typeid" => 5,
                    "submitter_id" => session('worker_id'),
                    "submitter" => session('worker_name'),
                    "time_long_unit" => "分",
                    "cost_content_id" => $roomId,
                    "cost_content_name" => $roomInfo[0]['position'],
                    "room_reserve_id" => $res
                );

                $institutionRes = $institutionOutcomeModel->data($institutionInsertData)->add();

                //机构教室缴费明细录入
                $instRoomPaymentData=array(
                    "institution_id"=>$data['institution_id'],
                    "inst_name" => $institutionName,
                    "room_id"=>$roomId,
                    "room_number"=>$roomInfo[0]['position'],
                    "room_area"=>$roomInfo[0]['area'],
                    "room_price"=>$roomInfo[0]['price'],
                    "reserve_start_time"=>$startTime,
                    "reserve_end_time"=>$endTime,
                    "time_long"=>$timeLengh,
                    "sum_price"=>$price,
                    "reserve_time"=>date("Y-M-d H:s:i", time()),
                    "reserve_room_id"=>$res
                );
                $instRoomPaymentRes=D('InstRoomPayment')->data($instRoomPaymentData)->add();
                //课程计划表
                $coursePlanModel = D('CoursePlan');
                $coursePlanInserData = array(
                    "course_id" => $courseId,
                    "institution_id" => $data['institution_id'],
                    "start_time" => $startTime,
                    "end_time" => $endTime,
                    "max_member" => $maxNumber,
                    "room_id" => $roomId,
                    "price" => $price,
                    "use" => $use,
                    "room_number" => $roomInfo[0]['position']
                );

                $coursePlanRes = $coursePlanModel->data($coursePlanInserData)->add();

                //商场收入表
                $MallRevenueModel = D('MallRevenue');
                //income_ownertypeid 5->机构，6->商户
                $MallRevenueInfo = Array(
                    "income_ownerid" => $data['institution_id'],
                    "income_ownername" => $institutionName,
                    "income_typeid" => 5,
                    "income_type" => "教室预定",
                    "income_num" => $price,
                    "income_time" => date("Y-M-d H:s:i", time()),
                    "income_ownertypeid" => 5,
                    "income_ownertype" => '机构',
                    "income_content_id" => $roomId,
                    "income_content_name" => $roomInfo[0]['position'],
                    "start_time" => $startTime,
                    "end_time" => $endTime,
                    "time_long" => $timeLengh,
                    "time_long_unit" => "分",
                    "submitter_id" => session('worker_id'),
                    "submitter" => session('worker_name'),
                    "link_id" => $res
                );
                $MallRevenueModel->add($MallRevenueInfo);
                $institutionStaffModel = D("InstStaff");
                $insId = $data['institution_id'];
                $institutionStaffList = $institutionStaffModel->where("institution_id = $insId")->field("id,card_number,name")->select();
                $roomModel = D("Room");
                foreach ($institutionStaffList as $key => $val) {
                    $roomList = $roomModel->where("position = '" . $roomInfo[0]['position'] . "'")->field("*")->find();
                    $equip = $roomList['equip_ip'];

                    $memberDoorAuthModel = D("MemberDoorAuth");
                    $memberDoorAuthInsertData = array(
                        "eqIp" => $equip,
                        "cardNo" => $val['card_number'],
                        "memberId" => $val['id'],
                        "memberName" => $val['name'],
                        "time" => $this->formatTimetest($startTime, $endTime)
                    );

                    $memberDoorAuthModel->data($memberDoorAuthInsertData)->add();
                }


                if ($res && $institutionRes && $coursePlanRes && $instRoomPaymentRes) {
                    Ret(array('code' => 1, 'info' => '教室预定成功'));
                } else {
                    Ret(array('code' => 2, 'info' => '教室预定失败'));
                }
            }
            else
            {
                Ret(array('code' => 2, 'info' => '教室该时间被占用'));
            }
        }
        else
        {
            Ret(array('code' => 2, 'info' => '预订时间应晚于当前时间'));
        }

    }
    private function formatTimetest($startTime, $endTime)
    {
        //0,0:00-23:59;1,0:00-23:59;2,0:00-23:59;3,0:00-23:59;4,0:00-23:59;5,0:00-23:59;6,0:00-23:59
        $str = "";
        $sTime = explode(" ", $startTime);
        $week = date("w", strtotime($sTime[0]));
        $eTime = explode(" ", $endTime);
        $str .= $sTime[0] . "," . $eTime[0] . "#" . rand(1, 1000) . "]";
        $strTime = "";

        for ($i = 0; $i <= 6; $i++) {
            $sHour = explode(":", $sTime[1]);
            $eHour = explode(":", $eTime[1]);
            if ($sHour[0] == "00：00") {
                $sTime1 = "0:00";
            } else {
                $sTime1 = $sHour[0] . ":" . $sHour[1];
            }

            if ($i == $week) {
                $strTime .= $i . "," . $sTime1 . "-" . $eHour[0] . ":" . $eHour[1] . ";";
                continue;
            }
            $strTime .= $i . ",23:59-23:59;";
        }
        return $str . substr($strTime, 0, strlen($strTime) - 1);

    }


        private function formatTime($timeArr)
    {
        $res = array();
        $now = date("w");

        foreach ($timeArr as $key => $val) {
            switch ($val[0]) {
                case  "周一":
                    $starWeekDay = 1;
                    break;
                case  "周二":
                    $starWeekDay = 2;
                    break;
                case  "周三":
                    $starWeekDay = 3;
                    break;
                case  "周四":
                    $starWeekDay = 4;
                    break;
                case  "周五":
                    $starWeekDay = 5;
                    break;
                case  "周六":
                    $starWeekDay = 6;
                    break;
                case  "周日":
                    $starWeekDay = 0;
                    break;
                default:
                    $starWeekDay = -1;
            }

            switch ($val[1]) {
                case  "周一":
                    $endWeekDay = 1;
                    break;
                case  "周二":
                    $endWeekDay = 2;
                    break;
                case  "周三":
                    $endWeekDay = 3;
                    break;
                case  "周四":
                    $endWeekDay = 4;
                    break;
                case  "周五":
                    $endWeekDay = 5;
                    break;
                case  "周六":
                    $endWeekDay = 6;
                    break;
                case  "周日":
                    $endWeekDay = 0;
                    break;
                default:
                    $endWeekDay = -1;
            }

            if ($endWeekDay == -1 || $starWeekDay == -1) {
                continue;
            }

            if (!($now >= $starWeekDay) && !($now <= $endWeekDay)) {
                continue;
            }

            $nowTime = date("H");
            $startNowTime = explode(":", $val[2]);
            $startNowTime = $startNowTime[0];
            $endNowTime = explode(":", $val[3]);
            $endNowTime = $endNowTime[0];

            if (($nowTime <= $startNowTime) || ($nowTime >= $endNowTime)) {
                continue;
            }

            if (($now >= $starWeekDay) && ($now <= $endWeekDay) && ($nowTime >= $startNowTime) && ($nowTime <= $endNowTime)) {
                $res['time_price'] = $val[4];
            }
        }

        return $res;

    }

    public function test()
    {
        $this->sumTotalTime();
    }

    private function sumTotalTime()
    {
        $id = I("id");
        $startTime = I("startTime");
        $endTime = I("endTime");

        if (empty($id) || empty($startTime) || empty($endTime)) {
            Ret(array("code" => 2, "info" => "参数错误"));
            die;
        }

        $roomDistcountModel = D("ClassDiscount");
        $response = $roomDistcountModel->field("*")->where("room_id = $id")->select();

        if (!empty($response)) {
            $time = time();
            $stTime = strtotime($response[0]['start_time']);
            $enTime = strtotime($response[0]['end_time']);
            if (($time >= $stTime) && ($time <= $enTime)) {
                $discountRate = $response[0]['discount_rate'];
            }
        }


        $courseModel = D("CoursePlan");
        $nowMonthTime = strtotime(date('Y-m', time()) . '-01 00:00:00');
        $res = $courseModel->query("select id, UNIX_TIMESTAMP(start_time) as stTime, start_time,end_time from course_plan HAVING stTime>= $nowMonthTime");
        $ids = "";
        if (!empty($res)) {
            foreach ($res as $key => $val) {
                $ids .= $val['id'] . ",";
            }
        }
        $ids = rtrim($ids, ",");
        $data = array();
        foreach ($res as $key => $val) {
            $countCourseTimeSql = "select id, timestampdiff(MINUTE ,'{$val['start_time']}','{$val['end_time']}') as t from course_plan WHERE  id 
                                  IN ($ids)";
            $data = $courseModel->query($countCourseTimeSql);
        }
        $min = null;
        if (!empty($data)) {
            foreach ($data as $key => $val) {
                $min += $val['t'];
            }
        }
        return ceil($min / 60);

    }

    /*
     * 教室预定动态
     */
    public function class_dated_api()
    {
        before_api();
        $mall_id = session('mall.mall_id');
        $date = I('date');
        $category_id = I('category_id');
        $roomModel = D('Class');
        $roomList = $roomModel->get_list($mall_id);
        $roomReserve = D('RoomReserve');
        foreach ($roomList as $k => $v) {
            $result[$k]['count'] = $roomReserve->get_dated_list($v['id'], $date, $category_id);
            $result[$k]['room'] = $v['position'];
        }

        if ($result) {
            Ret(array('code' => 1, 'data' => $result));
        } else {
            Ret(array('code' => 2, 'info' => '系统出错！'));
        }
    }

    /*
     * 教室使用动态
     */
    public function class_used_api()
    {
        before_api();
        checkAuth();
        checkLogin();
        $type = I('type');
        $people = I('people');
        if (empty($type)) {
            Ret(array('code' => 2, 'info' => '类型！'));
        }

        if (empty($people)) {
            Ret(array('code' => 3, 'info' => '人数不能为空！'));
        }

        $roomList = D('Class')->where()->select();
        $starTime = date("Y-m-d H:00:00");
        $endTime = date("Y-m-d H:59:59");

        if ($roomList) {
            foreach ($roomList as $k => $v) {
                $con['thedate'] = date('Y-m-d');

                $count = D("all_attendance_detail")->where("position='{$arr[0]}' and time>='$starTime' and time <= '$endTime'")->count();
                preg_match('/\d+/', $v['position'], $arr);
                $data[$k]['count'] = $count;
                $con['room_id'] = $v['id'];
                $data[$k]['room_number'] = $v['position'];
                $data[$k]['room_id'] = $v['id'];
                $isHave = D("course_plan")->where("room_number='{$arr[0]}' and start_time>='$starTime' and end_time <= '$endTime'")->find();
                $data[$k]['isHaveClass'] = 0;

                if (!empty($isHave)) {
                    $data[$k]['isHaveClass'] = 1;
                }
            }

            if ($data) {
                Ret(array('code' => 1, 'data' => $data));
            } else {
                Ret(array('code' => 2, 'info' => '系统出错！'));
            }
        }
    }


    public function class_used_nav()
    {
        $people = [
            0 => '全部',
            1 => '0-5',
            2 => '6-10',
            3 => '11-15',
            4 => '15-20'
        ];

        $r = [];
        $list = D('inst_category')->select();
        foreach ($list as $key => $value) {
            $r[$key]['id'] = $value['id'];
            $r[$key]['name'] = $value['name'];
        }
        $data['people'] = $people;
        $data['cate'] = $r;
        Ret(array('code' => 0, 'data' => $r));


    }

    public function class_used_detail()
    {
        $roomId = I('roomId');
        if (empty($roomId)) {
            Ret(array('code' => 1, 'info' => '房间id不能为空！'));
        }

        $roomList = D('Class')->where(['id' => $roomId])->find();
        $starTime = date("Y-m-d H:00:00");
        $endTime = date("Y-m-d H:59:59");

        preg_match('/\d+/', $roomList['position'], $arr);
        $roomList['count'] = D("all_attendance_detail")->where("position='{$arr[0]}' and time>='$starTime' and time <= '$endTime'")->count();

        Ret(array('code' => 0, 'data' => $roomList));

    }

//课程折扣详情
    public function class_discount_view_api()
    {
        before_api();
        checkAuth();
        checkLogin();
        $id = I('id', 0);
        if ($id == 0) {
            Ret(array('code' => 2, 'info' => '数据（id）获取失败！'));
        }
        $discountModel = D('ClassDiscount');
        $data = $discountModel->get_discount_info($id);
        if ($data) {
            $data[0]['category'] = $this->get_cat_by_id($data[0]['category_id']);
            $data[0]['classroom'] = $this->get_room_by_id($data[0]['room_id']);
            Ret(array('code' => 1, 'data' => $data[0]));
        } else {
            Ret(array('code' => 2, 'info' => '没有相关数据！'));
        }


    }

    /**
     * 教室信息日志
     */
    /*
     * 教室审核日志
     */
    public function class_check_log()
    {
        before_api();
        checkLogin();
        checkAuth();
        $keyword = I('keyword');
        $page = I('page', 1, 'intval');
        $pagesize = I('pagesize', 10, 'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page . ',' . $pagesize;
        $roomCheckLogModel = D('RoomCheckLog');
        $data = $roomCheckLogModel->get_list(0, $page, $keyword);
        $count = $roomCheckLogModel->get_count(0, $keyword);
        if ($data) {
            Ret(array('code' => 1, 'data' => $data, 'total' => $count, 'page_count' => ceil($count / $pagesize)));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据！'));
        }
    }

//课程折扣列表
    public function class_discount_list()
    {
        before_api();
        checkLogin();
        checkAuth();
        $mall_id = session('mall.mall_id');
        $state = I('state', 0, 'intval');
        if ($state !== 2 && !$mall_id) {
            $state = 2;
        }
        $page = I('page', 1, 'intval');
        $pagesize = I('pagesize', 10, 'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page . ',' . $pagesize;
//           $classModel = D('Class');
        $classDiscountModel = D('ClassDiscount');
        $classCategoryModel = D('ClassCategory');
        $roomModel = D('Class');
        $categoryModel = D('InstCat');
        $count = $classDiscountModel->get_count($mall_id);
        $fields = ('id,mall_id,type,category_id,room_id,start_time,end_time,discount_rate,state,submitter_id,check_type');
        $data = $classDiscountModel->get_list($mall_id, $page, $fields);
        foreach ($data as $key => $item) {
            $data[$key]['state'] = get_state($item['state']);
            $data[$key]['category_name'] = $this->get_cat_by_id($item['category_id']);
            $data[$key]['class_number'] = $this->get_room_by_id($item['room_id']);
            $data[$key]['nnn'] = '1';
        }
        if ($data) {
            Ret(array('code' => 1, 'data' => $data, 'total' => $count, 'page_count' => ceil($count / $pagesize)));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据！'));
        }
    }

    /**
     * 教室退订计费审核列表
     */
    public function class_cost_list()
    {
        before_api();
        checkAuth();
        checkLogin();
        $mall_id = session('mall.mall_id');
        $state = I('state', 2, 'intval');
        $is_manager = true;
        if ($state !== 2 && !$is_manager) {
            $state = 2;
        }
        $page = I('page', 1, 'intval');
        $pagesize = I('pagesize', 10, 'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page . ',' . $pagesize;
        $keyword = I('keyword');
        $roomCheckoutModel = D('RoomCheckout');
        $data = $roomCheckoutModel->get_count($mall_id);
        $fields = 'id,mall_id,category_id,room_id,state,sevenday_rate,sixday_rate,fiveday_rate,submitter,submit_time,check_type';
        $res = $roomCheckoutModel->get_list($mall_id, $keyword, $page, $fields);
//        var_dump($res);die;
        foreach ($res as $key => $item) {
            $res[$key]['state'] = get_state($item['state']);
            $res[$key]['category'] = $this->get_cat_by_id($item['category_id']);
            $res[$key]['room_number'] = $this->get_room_by_id($item['room_id']);
            $res[$key]['nnn'] = '1';
        }
//        var_dump($res);die;
        if ($res) {
            Ret(array('code' => 1, 'data' => $res, 'total' => $data, 'page_count' => ceil($data / $pagesize)));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据！'));
        }
    }

    /**
     * 教室信息审核列表
     */
    public function class_check_list()
    {

        before_api();
        checkLogin();
        checkAuth();
        $mall_id = session('mall.mall_id');
        $keyword = I('keyword');
        $page = I('page', 1, 'intval');
        $pagesize = I('pagesize', 10, 'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page . ',' . $pagesize;

        $roomModel = D('Room');
        $roomCatModel = D('RoomCat');
        $CategoryModel = D('InstCat');
        $discountModel = D('RoomDiscount');
        $count = $roomModel->get_count($mall_id, $keyword);
        if ($count) {
            $fields = 'id,mall_id,position,area,max_number,price,state,category_id,submitter,check_type,time_price';
            $res = $roomModel->get_list($mall_id, $keyword, $page, $fields);
            if (empty($res)) {
                Ret(array('code' => 2, 'info' => '查无相关数据！'));
            } else {
                foreach ($res as $key => $value) {
                    $res[$key]['state'] = get_state($value['state']);
                    $cat = $value['category_id'];
                    $cats = explode('-', $cat);
                    foreach ($cats as $k => $v) {
                        $cate = $CategoryModel->get_category($v);
                        if ($cate[0][name] != null) {
                            $catess[$k] = $cate[0][name];
                        }

                    }
                    $res[$key]['category'] = implode(',', $catess);
//                    $res[$key]['discount']=$discountModel->get_discount($value['id']);
                    $res[$key]['nnn'] = '1';
                }
                Ret(array('code' => 1, 'data' => $res, 'total' => $count, 'page_count' => ceil($count / $pagesize)));
            }
        } else {
            Ret(array('code' => 2, 'info' => '查无相关数据！'));
        }
    }

    // 门禁设备
    public function getEq()
    {
        before_api();
        checkLogin();
        $shopModel = D('EquipmentDoor');
        $fields = array('equip_id,equip_ip');
        $res = $shopModel->field($fields)->select();
        if ($res) {
            foreach ($res as $k => $v) {
                $re[$k]['name'] = $v['equip_ip'];
                $re[$k]['id'] = $v['equip_id'];
            }
            Ret(array('code' => 1, 'data' => $re));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据！'));
        }

    }

    /**
     *  教室预订中的未使用教室列表
     */
    public function getUnusedRoomList()
    {
        checkLogin();
        before_api();
        $data = D('Room')->getUnusedRoomNumber();
        if ($data) {
            Ret(array('code' => 1, 'data' => $data));
        } else {
            Ret(array('code' => 2, 'info' => '无教室可预订'));
        }
    }
    /**
     *  根据日期和类别查询教室的接口
     */
    public function getUnusedRoomListByTimeAndCatelory()
    {
        $startTime=I('start_time');
        $endTime=I('end_time');
        $catogaryId=I('room_type_id');
        $data=D('Room')->getUnusedRoomInfoByTimeAndCatelory($startTime,$endTime,$catogaryId);
        //var_dump($data);die;
        if (count($data)!=0) {
            Ret(array('code' => 1, 'data' => $data));
        } else {
            Ret(array('code' => 2, 'info' => '无教室可预订'));
            die;
        }
    }
    /**
     * 获取教室列表
     */
    public function getNumber()
    {
        checkLogin();
        before_api();
        $data = D('Room')->getNumber();
        if ($data) {
            Ret(array('code' => 1, 'data' => $data));
        } else {
            Ret(array('code' => 2, 'info' => '无教室数据'));
        }

    }

    /**
     * 根据教室Id 获取教室数据
     */
    public function reserveRoomView()
    {
        before_api();
        checkLogin();
        checkAuth();
        $id = I('room_id');
        $roomModel = D('Room');
//        var_dump($data);die;
        $data = $roomModel->getRoom($id);
        if ($data) {
            Ret(array('code' => 1, 'data' => $data[0]));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据'));
        }
    }

    /**
     * 添加教室预定
     */
    public function reserveDml()
    {
        $data['institution_id'] = session('worker_id');
        $data['room_id'] = I('id');
        if (!$data['roo_id'] == 0) {
            Ret(array('code' => 2, 'info' => '请选择教室'));
        }
        $data['room_number'] = I('position');
        $data['course_id'] = I('course_id');
        if ($data['course_id'] == 0) {
            Ret(array('code' => 2, 'info' => '请选择课程'));
        }
        $course = $this->getCourseNameByID($data['course_id']);
        $data['course_name'] = $course['name'];
        $data['start_time'] = I('start_time');
        if ($data['start_time'] == 0) {
            Ret(array('code' => 2, 'info' => '请选择开始时间'));
        }
        $data['end_time'] = I('end_time');
        if ($data['end_time'] == 0) {
            Ret(array('code' => 2, 'info' => '请选择结束时间'));
        }
        $data['use'] = I('use');
        $data['area'] = I('area');
        $data['max_number'] = I('max_number');
        $data['price'] = I('price');
        $data['reserve_time'] = date('Y-m-d H:i:s');
        $data['card_number'] = I('card_number');
        $card = getCard($data['card_number']);
        $cardInfo['card_typeid'] = $card[0]['card_typeid'];
        $cardInfo['card_state'] = $card[0]['card_state'];
        $cardInfo['cardnumber_no'] = $card[0]['cardnumber_no'];
        $cardInfo['card_number'] = $card[0]['card_number'];
        if (!$cardInfo['card_number']) {
            Ret(array('code' => 2, 'info' => '卡号不存在'));
        }
        if ($cardInfo['card_typeid'] == 4) {
            $chant = getChantStaff($cardInfo['card_number']);
//            var_dump($chant);die;
            $res['cardnumber_no'] = $chant[0]['cardnumber_no'];
            $res['balance'] = $chant[0]['balance'];
            $res['card_number'] = $chant[0]['card_number'];
            $res['id'] = $chant[0]['id'];
            $res['name'] = $chant[0]['name'];
//            var_dump($res);die;
            if ($res['balance'] < $data['price']) {
                Ret(array('code' => 2, 'info' => '余额不足,请充值'));
            } else {
                $res['balance'] = $res['balance'] - $data['price'];
            }
            if (updateChantBalance($res)) {
                $mallinfo['income_ownerid'] = $res['id'];
                $mallinfo['income_ownername'] = $res['name'];
                $mallinfo['income_time'] = date('Y-m-d H:i:s');
                $mallinfo['income_ownertypeid'] = 2;
                $mallinfo['income_ownertype'] = '商户';
                $mallinfo['income_typeid'] = 6;
                $mallinfo['income_type'] = '教室预定费收入';
                $mallinfo['income_num'] = $data['price'];
//            var_dump($mallinfo);die;
                addMall($mallinfo);
            }
        }

        if ($cardInfo['card_typeid'] == 3) {
            $chant = getStaff($cardInfo['card_number']);
            $res['cardnumber_no'] = $chant[0]['cardnumber_no'];
            $res['balance'] = $chant[0]['balance'];
            $res['card_number'] = $chant[0]['card_number'];
            $res['id'] = $chant[0]['id'];
            $res['name'] = $chant[0]['name'];
//            var_dump($res);die;
            if ($res['balance'] < $data['price']) {
                Ret(array('code' => 2, 'info' => '余额不足,请充值'));
            } else {
                $res['balance'] = $res['balance'] - $data['price'];
            }
            if (updateChantBalance($res)) {
                $mallinfo['income_ownerid'] = $res['id'];
                $mallinfo['income_ownername'] = $res['name'];
                $mallinfo['income_time'] = date('Y-m-d H:i:s');
                $mallinfo['income_ownertypeid'] = 1;
                $mallinfo['income_ownertype'] = '机构';
                $mallinfo['income_typeid'] = 6;
                $mallinfo['income_type'] = '教室预定费收入';
                $mallinfo['income_num'] = $data['price'];
//            var_dump($mallinfo);die;
                addMall($mallinfo);
            }

        }

        if ($cardInfo['card_typeid'] == 1) {
            $chant = getMember($cardInfo['card_number']);
            $res['cardnumber_no'] = $chant[0]['cardnumber_no'];
            $res['balance'] = $chant[0]['balance'];
            $res['card_number'] = $chant[0]['card_number'];
            $res['id'] = $chant[0]['id'];
            $res['name'] = $chant[0]['name'];
//            var_dump($res);die;
            if ($res['balance'] < $data['price']) {
                Ret(array('code' => 2, 'info' => '余额不足,请充值'));
            } else {
                $res['balance'] = $res['balance'] - $data['price'];
            }
            if (updateChantBalance($res)) {
                $mallinfo['income_ownerid'] = $res['id'];
                $mallinfo['income_ownername'] = $res['name'];
                $mallinfo['income_time'] = date('Y-m-d H:i:s');
                $mallinfo['income_ownertypeid'] = 3;
                $mallinfo['income_ownertype'] = '会员';
                $mallinfo['income_typeid'] = 6;
                $mallinfo['income_type'] = '教室预定费收入';
                $mallinfo['income_num'] = $data['price'];
//            var_dump($mallinfo);die;
                addMall($mallinfo);
            }
        }
        addRoom($data);
        $result = addCourse($data);

        if ($result) {
            Ret(array('code' => 1, 'info' => '添加成功'));
        } else {
            Ret(array('code' => 2, 'info' => '添加失败'));
        }

    }

    private function getCourseNameByID($courseID)
    {
        if ($courseID) {
            return D('Course')->where('id=' . $courseID)->field('name')->find();
        }
    }

    private function getRootNameByID($room_id)
    {
        if ($room_id) {
            return D('Room')->where('id=' . $room_id)->field('position,price,use')->find();
        }
    }

    private function addCoursePlan($data)
    {
        if ($data) {
            $course = $this->getCourseNameByID($data['course_id']);
            $room = $this->getRootNameByID($data['room_id']);
            $planData['course_name'] = $course['name'];
            $planData['course_id'] = $data['course_id'];
            $planData['room_number'] = $room['position'];
            $planData['price'] = $room['price'];
            $planData['use'] = $room['use'];
            $planData['start_time'] = $data['start_time'];
            $planData['end_time'] = $data['end_time'];
            $planData['room_id'] = $data['room_id'];
            $planData['max_member'] = $data['max_number'];

//            $planData['course_name'] = $data['max_number'];
            $planData['institution_id'] = $data['institution_id'];

            D('CoursePlan')->add($planData);

        }
    }

    public function getCoureInfoByID($courseID)
    {
        if ($courseID) {
            return D('Course')->where('id=' . $courseID)->field('name,single_time_price')->find();
        }
    }

    /**
     * 教室预定订单列表
     */
    public function roomReserveList()
    {
        $institution_id = session('inst.institution_id');
        $state = I('state', 0, 'intval');
        if ($state !== 2 && !$institution_id) {
            $state = 2;
        }
        $keyword = I('keyword');
        $page = I('page', 1, 'intval');
        $pagesize = I('pagesize', 10, 'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page . ',' . $pagesize;
        $reserveModel = D('RoomReserve');
        $count = $reserveModel->getClassCount($institution_id, $keyword, $state);
        //$fields = 'id,room_number,course_id,start_time,end_time,price';
        $data = $reserveModel->getList($institution_id, $keyword, $state, $page);
        foreach ($data as $key => $value) {
            $data[$key]['start_time'] = $data[$key]['start_time'].'——'.$data[$key]['end_time'];
            $start_time = strtotime($value['start_time']);
            $end_time = strtotime($data[$key]['end_time']);
            //传分钟数
            $data[$key]['hour'] = ceil(($end_time - $start_time)  / 60).'分钟';
            //var_dump($value['course_id']);
            //$courseName=D('Course')->where('id=' . $value['course_id'])->field('name')->find();
            $courseName=D('Course')->get_list_by_activityId($value['course_id']);
            //var_dump($courseName);die;
            $data[$key]['course_name']=$courseName[0]['name'];
        }
        if ($data) {
            Ret(array('code' => 1, 'data' => $data, 'total' => $count, 'page_count' => ceil($count / $pagesize)));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据，系统出错'));
        }
    }

    public function deleteReserve()
    {
        $id = I('id');
        if (!$id) {
            Ret(array('code' => 2, 'info' => 'ID获取失败！'));
        }
        $reserveModel = D('RoomReserve');
        $result = $reserveModel->delete_room($id);
        if ($result) {
            Ret(array('code' => 1, 'info' => '删除成功！'));
        } else {
            Ret(array('code' => 2, 'info' => '删除失败,系统出错！'));
        }
    }
    //获取退订金额
    public function getUnsubscribePrice()
    {
        //预定时间
        $end_time=I('subscribe_time');
        //预定教室ID
        $room_reserve_id=I('room_reserve_id');
        $start_time=date('Y-m-d H:i:s');
        $dayNum=getDayNum($start_time, $end_time, '-');
        $roomReserveInfo=D('RoomReserve')->getRoomReserveById($room_reserve_id);
        $roomCheckOutInfo=D('RoomCheckout')->getInfoByRoomId($roomReserveInfo['room_id']);
        if($dayNum<0)
        {
            $resultPrice=$roomReserveInfo['price'];
            Ret(array('code' => 1, 'cancel_price' => $resultPrice));
        }
        $resultPrice=0;
        switch ($dayNum)
        {
            case 0:
                $resultPrice=$roomReserveInfo['price'];
                //var_dump($roomReserveInfo['price']);die;
                break;
            case 1:
                $resultPrice=$roomReserveInfo['price']*$roomCheckOutInfo['oneday_rate'];
                break;
            case 2:
                $resultPrice=$roomReserveInfo['price']*$roomCheckOutInfo['twoday_rate'];
                break;
            case 3:
                $resultPrice=$roomReserveInfo['price']*$roomCheckOutInfo['threeday_rate'];
                break;
            case 4:
                $resultPrice=$roomReserveInfo['price']*$roomCheckOutInfo['fourday_rate'];
                break;
            case 5:
                $resultPrice=$roomReserveInfo['price']*$roomCheckOutInfo['fiveday_rate'];
                break;
            case 6:
                $resultPrice=$roomReserveInfo['price']*$roomCheckOutInfo['sixday_rate'];
                break;
            case 7:
                $resultPrice=$roomReserveInfo['price']*$roomCheckOutInfo['sevenday_rate'];
                break;
            default:
                $resultPrice=0;
                break;
        }
        Ret(array('code' => 1, 'cancel_price' => $resultPrice));

    }
     //退订
    public function unsubscribeClassRoom()
    {
        $id=I('room_reserve_id');
        if (!$id) {
            Ret(array('code' => 2, 'info' => 'ID获取失败！'));
        }
        $cancel_price=I('cancel_price');
        $instId=I('institution_id');
        $instInfo=D('Inst')->getInstInfoById($id);
        $roomReserveInfo=D('RoomReserve')->getRoomReserveById($id);
        //$res = getMoney($id);
        //$data['price'] = $res[0]['price'];
        $condition['reserve_room_id']=$id;
        $instRoomPaymentInfo=D('InstRoomPayment')->where($condition)->find();
        $instRoomPaymentData['cancel_time']=date('Y-m-d H:i:s');
        $instRoomPaymentData['cancel_price']=$cancel_price;
        D('InstRoomPayment')->where($condition)->save($instRoomPaymentData);

        $mallinfo['income_ownerid'] = $instId;
        $mallinfo['income_ownername'] = $instInfo['name'];
        $mallinfo['income_time'] = date('Y-m-d H:i:s');
        $mallinfo['income_ownertypeid'] = 1;
        $mallinfo['income_ownertype'] = '机构';
        $mallinfo['income_typeid'] = 6;
        $mallinfo['income_type'] = '教室预定费收入';
        $mallinfo['income_num'] = $cancel_price;
        addMall($mallinfo);

        //把教室退订消息删除
        D('RoomReserve')->delete_room($id);
        


        Ret(array('code' => 1, 'info' => '退订成功！'));

       /* $id = I('id');
        if (!$id) {
            Ret(array('code' => 2, 'info' => 'ID获取失败！'));
        }
        $card_number = I('card_number');
        $res = getMoney($id);
        $data['price'] = $res[0]['price'];
        $card = getCard($card_number);
        $cardInfo['card_typeid'] = $card[0]['card_typeid'];
        $cardInfo['card_state'] = $card[0]['card_state'];
        $cardInfo['cardnumber_no'] = $card[0]['cardnumber_no'];
        $cardInfo['card_number'] = $card[0]['card_number'];
        if (!$cardInfo['card_number']) {
            Ret(array('code' => 2, 'info' => '卡号不存在'));
        }
        if ($cardInfo['card_typeid'] == 4) {
            $chant = getChantStaff($cardInfo['card_number']);
            $res['cardnumber_no'] = $chant[0]['cardnumber_no'];
            $res['balance'] = $chant[0]['balance'];
            $res['card_number'] = $chant[0]['card_number'];
            $res['id'] = $chant[0]['id'];
            $res['name'] = $chant[0]['name'];
            $res['balance'] = $chant[0]['balance'];
            $res['balance'] = $res['balance'] + $data['price'] * 0.7;
            $mony = $data['price'] * 0.3;
//            var_dump($res);die;
            if (updateChantBalance($res)) {
                $mallinfo['income_ownerid'] = $res['id'];
                $mallinfo['income_ownername'] = $res['name'];
                $mallinfo['income_time'] = date('Y-m-d H:i:s');
                $mallinfo['income_ownertypeid'] = 2;
                $mallinfo['income_ownertype'] = '商户';
                $mallinfo['income_typeid'] = 6;
                $mallinfo['income_type'] = '教室预定费收入';
                $mallinfo['income_num'] = $mony;
//            var_dump($mallinfo);die;
                addMall($mallinfo);
            }
//            echo 'sfsfasf';die;
        }
        if ($cardInfo['card_typeid'] == 3) {
            $chant = getStaff($cardInfo['card_number']);
            $res['cardnumber_no'] = $chant[0]['cardnumber_no'];
            $res['balance'] = $chant[0]['balance'];
            $res['card_number'] = $chant[0]['card_number'];
            $res['id'] = $chant[0]['id'];
            $res['name'] = $chant[0]['name'];
            $res['balance'] = $chant[0]['balance'];
            $res['balance'] = $data['price'] * 0.7;
            if (updateInstBalance($res)) {
                $mallinfo['income_ownerid'] = $res['id'];
                $mallinfo['income_ownername'] = $res['name'];
                $mallinfo['income_time'] = date('Y-m-d H:i:s');
                $mallinfo['income_ownertypeid'] = 1;
                $mallinfo['income_ownertype'] = '机构';
                $mallinfo['income_typeid'] = 6;
                $mallinfo['income_type'] = '教室预定费收入';
                $mallinfo['income_num'] = $res['balance'] / 0.3;
                addMall($mallinfo);
            }

        }
        if ($cardInfo['card_typeid'] == 1) {
            $chant = getMember($cardInfo['card_number']);
            $res['cardnumber_no'] = $chant[0]['cardnumber_no'];
            $res['balance'] = $chant[0]['balance'];
            $res['card_number'] = $chant[0]['card_number'];
            $res['id'] = $chant[0]['id'];
            $res['name'] = $chant[0]['name'];
            $res['balance'] = $chant[0]['balance'];
            $res['balance'] = $data['price'] * 0.7;
            if (updateMemberBalance($res)) {
                $mallinfo['income_ownerid'] = $res['id'];
                $mallinfo['income_ownername'] = $res['name'];
                $mallinfo['income_time'] = date('Y-m-d H:i:s');
                $mallinfo['income_ownertypeid'] = 3;
                $mallinfo['income_ownertype'] = '会员';
                $mallinfo['income_typeid'] = 6;
                $mallinfo['income_type'] = '教室预定费收入';
                $mallinfo['income_num'] = $res['balance'] / 0.3;
                addMall($mallinfo);
            }
        }

        $result = deletePlan($id);
        if ($result) {Ret(array('code' => 1, 'info' => '退订成功！'));

        } else {
            Ret(array('code' => 2, 'info' => '退订失败,系统出错！'));
        }*/
    }


    /*
    * 教室使用动态
    */
    public function class_used_List()
    {
        before_api();
        checkAuth();
        checkLogin();
        $category_id = I('category_id', 0, 'intval');
        $five = I('five', 0, 'intval');
        $ten = I('ten', 0, 'intval');
        $fifteen = I('fifteen', 0, 'intval');
        $twenty = I('twenty', 0, 'intval');
        $entire = I('entire', 0, 'intval');
        $data = getCategory($category_id);
        $res = getReserve();
        foreach ($res as $key => $value) {
            $data = getReserv($value['room_id']);
            $data[$key]['room_count'] = getCount($value['room_id']);
        }
//        var_dump($data[$key]['room_count']);die;
        if ($data[$key]['room_count'] <= 5) {
            $five = $data;
        }
//        var_dump($five);die;
        if ($five) {
            Ret(array('code' => 1, 'data' => $five));
        } else {
            Ret(array('code' => 2, 'info' => '系统出错！'));
        }
    }
    //教室修改下拉选择列表
    public function get_update_class_shop_list()
    {
        before_api();
        //$m = new \Mall\Controller\Model();index_api
        //$instUsedShopSql="select distinct shop_id from institution";
        //$instUsedShopList=$m->query($instUsedShopSql);

        //查询机构已经用过的店铺列表
        //echo('11111');
        //$condition['state']=1;
        $roomId=I('room_id');

        $instUsedShopList=D('Institution')->distinct(true)->field('shop_id')->select();
        $instUsedShopArr=array();
        foreach ($instUsedShopList as $key=>$value)
        {
            if($value['shop_id']!=NULL)
            {
                $instUsedShopArr[$key]=$value['shop_id'];
            }

        }
        //查询商户已经用过的店铺列表
        $merchantUsedShopList=D('Merchant')->distinct(true)->field('shop_id')->select();
        $merchantUsedShopArr=array();
        foreach ($merchantUsedShopList as $key=>$value)
        {
            if($value['shop_id']!=NULL)
            {
                $merchantUsedShopArr[$key] = $value['shop_id'];
            }
        }
        //var_dump($merchantUsedShopArr);die;
        //查询教室已经用过的店铺列表
        //$roomId=I('room_id');
        $roomUsedShopList=D('Room')->distinct(true)->field('shop_id')->where('id<>'.$roomId)->select();
        $roomUsedShopArr=array();
        foreach ($roomUsedShopList as $key=>$value)
        {
            if($value['shop_id']!=NULL)
            {
                $roomUsedShopArr[$key]=$value['shop_id'];
            }
        }
        //var_dump($roomUsedShopArr);die;
        if(!empty($roomUsedShopArr))
        {
            $usedShopList=array_merge($instUsedShopArr,$merchantUsedShopArr,$roomUsedShopArr);
        }
        else
        {
            $usedShopList=array_merge($instUsedShopArr,$merchantUsedShopArr);
        }
        $arr_string = join(',', $usedShopList);
        //var_dump($arr_string);die;
        $m = D('Shop');
        $sql1="select * from shop where id not in (".$arr_string.") and state=1";
        $data=$m->query($sql1);
        //$data = D('Shop')->select();

        foreach ($data as $k => $v) {
            $res[$k]['id'] = $data[$k]['id'];
            $res[$k]['name'] = $data[$k]['position'];
        }
        if ($res) {
            Ret(array('code' => 1, 'data' => $res));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据！'));
        }
    }
    public function inst_room_payment_list()
    {
        $start_time = I('start_time');
        $end_time = I('end_time');
        $institution_id = I('institution_id', 0, 'intval');
        $page = I('page', 1, 'intval');
        $pagesize = I('pagesize', 10, 'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page . ',' . $pagesize;
        $instRoomPaymentModel = D('InstRoomPayment');
        $InstModel = D('Institution');
        $InstStaff = D('InstStaff');
        $count = $instRoomPaymentModel->get_count($start_time, $end_time, $institution_id);
        //var_dump($count);die;
        $res = $instRoomPaymentModel->get_list($start_time, $end_time, $institution_id, $page);
        if ($res) {
            Ret(array('code' => 1, 'data' => $res, 'total' => $count, 'page_count' => ceil($count / $pagesize)));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据'));
        }
    }

}
