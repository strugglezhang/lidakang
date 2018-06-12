<?php

namespace Inst\Controller;
use Mall\Controller;
use Mall\Model;
class IndexController extends CommonController
{
    public function index()
    {
        phpinfo();
    }

    /*
     * 机构列表
     */
    public function index_api()
    {
        before_api();
        checkLogin();
        checkAuth();
        $mall_id = session('mall.mall_id');
        if ($mall_id) {
            $keyword = I('keyword');
            $category_id = I('category_id', 0, 'intval');
            $page = I('page', 1, 'intval');
            $pagesize = I('pagesize', 10, 'intval');
            $pagesize = $pagesize < 1 ? 1 : $pagesize;
            $pagesize = $pagesize > 50 ? 50 : $pagesize;
            $page = $page . ',' . $pagesize;
            $instModel = D('Institution');
            $count = $instModel->get_count($keyword, $category_id);
            $fields = array('id,name,incharge_person,phone,category_id,shop_id,position_idx,shop_addr');
            $res = $instModel->get_list($keyword, $page, $fields, $category_id);
            if ($res) {
                foreach ($res as $key => $value) {
//                    $cat=$this->get_cat_by_id($value['id']);
                    $res[$key]['category_name'] = $this->get_cat_id($value['category_id']);
//                    $res[$key]['category_name']=$cat[0]['name'];
                }

                Ret(array('code' => 1, 'data' => $res, 'total' => $count, 'page_count' => ceil($count / $pagesize)));
            } else {
                Ret(array('code' => 2, 'info' => '没有数据！'));
            }
        } else {
            Ret(array('code' => 2, 'info' => '请登录机构管理平台！'));
        }
    }
    /*
     * 被审核通过的机构列表
     */

    /*
     * 机构增删改
     */
    public function inst_dml_api()
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
                    Ret(array('code' => 2, 'info' => '数据登录获取失败！'));
                }
                $data['name'] = I('name');
                if (!$data['name']) {
                    Ret(array('code' => 2, 'info' => '请填写机构名称！'));
                }
                $data['contract_number'] = I('contract_number');
                if (!$data['contract_number']) {
                    Ret(array('code' => 2, 'info' => '请填写合同号！'));
                }
                $data['contract_start_time'] = I('contract_start_time');
                if (!$data['contract_start_time']) {
                    Ret(array('code' => 2, 'info' => '请填写同号开始时间！'));
                }
                $data['contract_end_time'] = I('contract_end_time');
                if (!$data['contract_end_time']) {
                    Ret(array('code' => 2, 'info' => '请填写同号结束时间！'));
                }
                //获取所在地区省市区
                $data['provice_id'] = I('provice_id');
                $data['city_id'] = I('city_id');
                $data['district_id'] = I('district_id');
                $data['web_site'] = I('web_site');
                $data['shop_id'] = I('shop_id');
                $shop_addr = $this->get_shop_by_id($data['shop_id']);
                $idx = "";
                $addr = "";
                foreach ($shop_addr as $item){
                    $idx .= $item['equipment_ip']."-";
                    $addr .= $item['position']."-";
                }
                $data['position_idx'] = substr($idx,0,strlen($idx) -1 );
                $data['shop_addr'] = substr($addr,0,strlen($addr) -1);

                $data['address'] = I('address');
                if (!$data['address']) {
                    Ret(array('code' => 2, 'info' => '请填写营业执照地址！'));
                }
                $category_id = I('category_id', 0);
                $data['category_id'] = rtrim($category_id, '_');
                if (!$data['category_id']) {
                    Ret(array('code' => 2, 'info' => '请选择类别！'));
                }
                $classify_id = I('classify_id', 0);
                $data['classify_id'] = rtrim($classify_id, '_');
                if (!$data['classify_id']) {
                    Ret(array('code' => 2, 'info' => '请选择分类！'));
                }
                $industry_id = I('industry_id', 0);
                $data['industry_id'] = rtrim($industry_id, '_');
                if (!$data['industry_id']) {
                    Ret(array('code' => 2, 'info' => '请选择行业！'));
                }
                $specify_id = I('specify_id', 0);
                $data['specify_id'] = rtrim($specify_id, '_');
                if (!$data['specify_id']) {
                    Ret(array('code' => 2, 'info' => '请选择细类！'));
                }
                $data['incharge_person'] = I('incharge_person');
                if (!$data['incharge_person']) {
                    Ret(array('code' => 2, 'info' => '请填写机构负责人！'));
                }
                $data['email'] = I('email');
                $data['phone'] = I('phone');
                $data['about'] = I('about');
                $data['license_img'] = I('license_img');
                $data['id_img'] = I('id_img');
                $data['certificate_img'] = I('certificate_img');
                $data['logo'] = I('logo');
                $data['imgs'] = I('imgs');
                $data['state'] = 0;
                $data['number'] = 0;
                $data['submit_time'] = date('Y-m-d H:i:s');
                $data['check_type'] = 4;
                $data['submitter'] = session('worker_name');
                $instModel = D('Institution');
                $instId = $instModel->add_inst($data);
                if ($instId) {
                    //绑定商铺
                    D('Shop')->instBondShopIsOk($data['shop_id'],$instId,$data['name']);
                    Ret(array('code' => 1, 'info' => '添加成功'));
                } else {
                    Ret(array('code' => 2, 'info' => '添加失败,系统出错！'));
                }
                break;
            case 'update':
                $data['id'] = I('id');
                if (!$data['id']) {
                    Ret(array('code' => 2, 'info' => '数据(id)获取失败！'));
                }
                $data['mall_id'] = $mall_id;
                if (!$data['mall_id']) {
                    Ret(array('code' => 2, 'info' => '数据获取失败！'));
                }
                $data['name'] = I('name');
                if (!$data['name']) {
                    Ret(array('code' => 2, 'info' => '请填写机构名称！'));
                }
                $data['shop_id'] = I('shop_id');
                $shop_addr = $this->get_shop_by_id($data['shop_id']);
                //var_dump($shop_addr);die;
                /*$data['position_idx'] = $shop_addr['equip_ip'];
                $data['shop_addr'] = $shop_addr['position'];*/
                $idx = "";
                $addr = "";
                foreach ($shop_addr as $item){
                    $idx .= $item['equipment_ip']."-";
                    $addr .= $item['position']."-";
                }
                $data['position_idx'] = substr($idx,0,strlen($idx) -1 );
                $data['shop_addr'] = substr($addr,0,strlen($addr) -1);

                $data['contract_number'] = I('contract_number');
                if (!$data['contract_number']) {
                    Ret(array('code' => 2, 'info' => '请填写合同号！'));
                }
                $data['contract_start_time'] = I('contract_start_time');
                if (!$data['contract_start_time']) {
                    Ret(array('code' => 2, 'info' => '请填写同号开始时间！'));
                }
                $data['contract_end_time'] = I('contract_end_time');
                if (!$data['contract_end_time']) {
                    Ret(array('code' => 2, 'info' => '请填写同号结束时间！'));
                }
                //获取所在地区省市区
                $data['provice_id'] = I('provice_id');
                $data['city_id'] = I('city_id');
                $data['district_id'] = I('district_id');
                $data['web_site'] = I('web_site');

                $data['address'] = I('address');
                if (!$data['address']) {
                    Ret(array('code' => 2, 'info' => '请填写营业执照地址！'));
                }
                $data['category_id'] = I('category_id', 0);
                if (!$data['category_id']) {
                    Ret(array('code' => 2, 'info' => '请选择类别！'));
                }
                $data['classify_id'] = I('classify_id', 0);
                if (!$data['classify_id']) {
                    Ret(array('code' => 2, 'info' => '请选择分类！'));
                }
                $data['industry_id'] = I('industry_id', 0);
                if (!$data['industry_id']) {
                    Ret(array('code' => 2, 'info' => '请选择行业！'));
                }
                $data['specify_id'] = I('specify_id', 0);
                if (!$data['specify_id']) {
                    Ret(array('code' => 2, 'info' => '请选择细类！'));
                }
                $data['incharge_person'] = I('incharge_person');
                if (!$data['incharge_person']) {
                    Ret(array('code' => 2, 'info' => '请填写机构负责人！'));
                }
                $data['phone'] = I('phone');
                $data['email'] = I('email');
                $data['about'] = I('about');
                $data['license_img'] = I('license_img');
                $data['id_img'] = I('id_img');
                $data['certificate_img'] = I('certificate_img');
                $data['logo'] = I('logo');
                $data['imgs'] = I('imgs');
                $data['state'] = 4;//4表修改
                $data['submit_time'] = date('Y-m-d H:i:s');
                $data['submitter'] = session('worker_name');
                $data['check_type'] = 4;
                $InstModel = D('Institution');
                $oldInstInfo=$InstModel->getAllField($data['id']);
                $result = $InstModel->update_inst($data);
                if ($result) {
                    if(!empty($oldInstInfo[0]['shop_id']))
                    {
                        D('Shop')->instUnBondShopIsOk($oldInstInfo[0]['shop_id']);
                    }
                    D('Shop')->instBondShopIsOk($data['shop_id'],$data['id'],$data['name']);
                    Ret(array('code' => 1, 'info' => '修改成功！'));
                } else {
                    Ret(array('code' => 2, 'info' => '修改失败,系统出错！'));
                }
                break;
            case 'delete':
                $id = I('id');
                if (!$id) {
                    Ret(array('code' => 2, 'info' => 'ID获取失败！'));
                }
                $InstModel = D('Institution');
                $deleteInstInfo=$InstModel->getAllField($id);
                $result = $InstModel->delete_inst($id);

                if ($result) {
                    //解绑商铺
                    if(!empty($deleteInstInfo[0]['shop_id']))
                    {
                        D('Shop')->instUnBondShopIsOk($deleteInstInfo[0]['shop_id']);
                    }
                    //删除旗下的所有员工信息
                    D('Worker')->deleteWorkerByInstId($id);
                    //旗下的所有员工卡冻结
                    $workerInfo=D('Worker')->getWorkerByInstId($id);
                    D('Card')->setCardFrozen($workerInfo);
                    //删除机构的所有课程
                    D('Course')->where('institution_id='.$id)->delete();
                    //删除机构的所有课程计划
                    D('CoursePlan')->where('institution_id='.$id)->delete();
                    //删除机构的所有预订教室
                    D('DateRoom')->where('institution_id='.$id)->delete();
                    
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

    public function get_shop_by_id($id)
    {
        $ids = explode("-",$id);
        $shopId = "";
        foreach ($ids as $sid){
            $shopId .= $sid.",";
        }
        $shopId = substr($shopId ,0,strlen($shopId)-1);
        return D('Shop')->where("id in ($shopId)")->field('equipment_ip,position')->select();

    }

    /*
     * 机构详情
     */
    public function inst_view_api()
    {
        before_api();
        checkLogin();
        checkAuth();
        $inst_id = I('id');
        if (!$inst_id) {
            Ret(array('code' => 2, 'info' => '机构数据获取失败！'));
        }
        $instModel = D('Institution');
        $data = $instModel->get_inst_info($inst_id);
//        foreach($data as $key =>$value){
//            $data[$key]['category_name']=$this->getCats($value['category_id']);
//        }
        if ($data) {
            //获取物业费和租金
            $cost = $this->get_shop_cost($data[0]['shop_id']);
            $data[0]['property_price'] = $cost[0]['property_price'];
            $data[0]['rent_rate'] = $cost[0]['rent_rate'];
//            var_dump($data);die();
            //管理费和宽带费
            $expense = $this->get_expense_by_merchant($inst_id);
            //var_dump($expense);die;
            if ($expense[0]['expense_id'] == 1) {
                $data[0]['manage_cost'] = $expense[0]['price'];
            }
            if ($expense[1]['expense_id'] == 2) {
                $data[0]['internet_cost'] = $expense[1]['price'];
            }
//            $data[0]['shop_addr'] =$data[0]['position_idx'];

            $data[0]['category_name'] = $this->getCats($data[0]['category_id']);
//            $data[0]['category_name'] =$cast[0]['name'];
//            var_dump($data);die();
            $data[0]['classify_name'] = $this->getClas($data[0]['classify_id']);
            $data[0]['industry_name'] = $this->getIndustry($data[0]['industry_id']);
            $data[0]['specify_name'] = $this->getInstspf($data[0]['specify_id']);
            $data[0]['imgs'] = explode(',', $data[0]['imgs']);
            $data[0]['certificate_img'] = explode(',', $data[0]['certificate_img']);
//            var_dump($data);die;
            Ret(array('code' => 1, 'data' => $data[0]));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据！'));
        }
    }


//    private function getCats($id){
//        $instCatModel = D('InstCat');
//        $array=explode('-',$id);
//        foreach($array as $k => $v ) {
//            $item = $instCatModel->get_cat($v);
//
//        }
//        return $item;
//    }

    private function getCats($cat)
    {
        $d = explode('-', $cat);

        $category = '';
        foreach ($d as $k => $v) {
            $catName[$k] = D('InstCat')->field('name')->where('id=' . $v)->find();
            $category = $catName[$k]['name'] . '-' . $category;
//            $res['name'] =$catName;
        }

        return $category;
    }

    private function getClas($clas)
    {
        $d = explode('-', $clas);
        $category = '';
        foreach ($d as $k => $v) {

            $catName[$k] = D('InstCls')->where('id=' . $v)->find();
            $data['name'] = $catName;

            $catName[$k] = D('InstCls')->where('id=' . $v)->find();

            $catName[$k] = D('InstCls')->field('name')->where('id=' . $v)->find();
            $category = $catName[$k]['name'] . '-' . $category;

        }

        return $category;

    }

    /**
     * @param $clas
     * @return string
     *机构行业
     */

    private function getIndustry($indu)
    {
        $d = explode('-', $indu);
        $category = '';
        foreach ($d as $k => $v) {

            $catName[$k] = D('InstIdt')->where('id=' . $v)->find();
            $data['name'] = $catName;

            $catName[$k] = D('InstIdt')->where('id=' . $v)->find();

            $catName[$k] = D('InstIdt')->field('name')->where('id=' . $v)->find();
            $category = $catName[$k]['name'] . '-' . $category;

        }

        return $category;

    }

    /**
     * @param $indu
     * @return string
     * 机构细类
     */
    private function getInstspf($indu)
    {
        $d = explode('-', $indu);
        $category = '';
        foreach ($d as $k => $v) {

            $catName[$k] = D('InstSpf')->where('id=' . $v)->find();
            $data['name'] = $catName;

            $catName[$k] = D('InstSpf')->where('id=' . $v)->find();

            $catName[$k] = D('InstSpf')->field('name')->where('id=' . $v)->find();
            $category = $catName[$k]['name'] . '-' . $category;
        }
        return $category;
    }

    //获取机构一级分类
    public function getInstCat()
    {
        $data = D('InstCat')->select();
        if ($data) {
            Ret(array('code' => 1, 'data' => $data));
        }
    }


    private function get_shop_cost($shop_id)
    {
        if ($shop_id) {
            $data = D('Shop')->get_shop_cost($shop_id);
            if ($data) {
                return $data;
            }
        }

    }

    /*
     * 获取商户消费
     */
    private function get_expense_by_merchant($merchant_id)
    {
        if ($merchant_id) {
            $costList = $this->get_expense();
            foreach ($costList as $key => $value) {
                $merchants = explode('-', $value['institution_id']);
                if (in_array($merchant_id, $merchants) || $value['institution_id'] == 0) {
                    $merchantCost[$key]['expense_id'] = $value['expense_id'];
                    $merchantCost[$key]['price'] = $value['price'];
                }
            }
            return $merchantCost;
        }

    }

    //获取商户计费（管理费，宽带费等）
    public function get_expense()
    {
        $mall_id = session('mall.mall_id');
        $merchantModel = D('InstExpense');
        $page = '1,100';
        $res = $merchantModel->get_list($mall_id, $page, $fields = null);
        return $res;
    }

    public function get_institution($ids)
    {
        $model = D('Institution');
        $array = explode('-', $ids);
        foreach ($array as $k => $v) {
            if ($v) {
                $item = $model->where('id=' . $v)->field('name')->find();
            }
            if ($item['name'] != null) {
                $items[$k] = $item['name'];
            }
            $data = implode('-', $items);
        }
        if ($data != null) {
            return $data;
        } else {
            return '';
        }


    }

    public function get_cat_id($ids)
    {
        $model = D('InstCat');
        $array = explode('-', $ids);
        foreach ($array as $k => $v) {
            $item = $model->get_inst_by_id($v);
            if ($item[0]['name'] != null) {
                $items[$k] = $item[0]['name'];
            }
            $data = implode('-', $items);
        }
        if ($data != null) {
            return $data;
        } else {
            return '';
        }

    }

    public function get_expense_cat()
    {
        return D('InstExpenseCat')->select();
    }

    private function get_shop($shop_id)
    {
        if ($shop_id) {
            return D('Shop')->get_shop_by_id($shop_id);
        }
    }

    public function get_shops()
    {
        before_api();
        $data = D('Equipment')->select();
        foreach ($data as $k => $v) {
            $data[$k]['name'] = $data[$k]['position'];
            $data[$k]['id'] = $data[$k]['equip_id'];
        }
        if ($data) {
            Ret(array('code' => 1, 'data' => $data));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据！'));
        }
        return $data;
    }
    //获取所有被审核通过的商铺列表
    public function getAllCheckShopList()
    {
        $keyword = I('keyword');
        $page = I('page',1,'intval');
        $pagesize = I('pagesize',10,'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page.','.$pagesize;
        //$condition['state']=1;
        $count = D('Shop')->get_check_list_num($keyword);
        $res=D('Shop')->get_check_list($keyword, $page);
        if ($count) {
            Ret(array('code' => 1, 'data' => $res,'total' => $count, 'page_count' => ceil($count/$pagesize)));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据！'));
        }
    }
    //获取所有被审核通过的下拉商铺列表
    public function getAllCheckDownShopList()
    {

        $data=D('Shop')->where('state=1')->select();
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
    //获取所有商铺列表
    public function getAllShopList()
    {
        $keyword = I('keyword');
        $page = I('page',1,'intval');
        $pagesize = I('pagesize',10,'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page.','.$pagesize;
        $count = D('Shop')->get_list_num($keyword);
        $res=D('Shop')->get_list($keyword,$page);
        if ($count) {
            Ret(array('code' => 1, 'data' => $res,'total' => $count, 'page_count' => ceil($count/$pagesize)));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据！'));
        }
    }
        //获取所有审核通过且未绑定的
    public function get_shopList()
    {
        before_api();
        //$m = new \Mall\Controller\Model();
        //$instUsedShopSql="select distinct shop_id from institution";
        //$instUsedShopList=$m->query($instUsedShopSql);

        //查询机构已经用过的店铺列表
        //echo('11111');
       //$condition['state']=1;
        $instUsedShopList=D('Institution')->distinct(true)->field('shop_id')->select();
        $instUsedShopArr=array();
        foreach ($instUsedShopList as $key=>$value)
        {
            if($value['shop_id']!=NULL)
            {
                $instUsedShopArr[$key]=$value['shop_id'];
            }

        }
        //print_r($instUsedShopArr);
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
        $roomUsedShopList=D('Room')->distinct(true)->field('shop_id')->select();
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
    public function get_update_shop_list()
    {
        before_api();
        //$m = new \Mall\Controller\Model();
        //$instUsedShopSql="select distinct shop_id from institution";
        //$instUsedShopList=$m->query($instUsedShopSql);

        //查询机构已经用过的店铺列表
        //echo('11111');
        //$condition['state']=1;
        $instId=I('institution_id');
        $instUsedShopList=D('Institution')->distinct(true)->field('shop_id')->where('id<>'.$instId)->select();
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
        $roomUsedShopList=D('Room')->distinct(true)->field('shop_id')->select();
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
    private function get_cat_by_id($id)
    {
        if ($id) {
            $model = D('InstCat');
            return $model->get_cat($id);
        }
    }

    /*
     * 获取服务类别
     */
    public function category()
    {
        before_api();
        $data = D('ServiceCategory')->get_cat_list();
        if ($data) {
            Ret(array('code' => 1, 'data' => $data));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据！'));
        }

    }

    /*
     * 获取服务类别
     */
    public function get_classify()
    {
        before_api();
        $data = D('ServiceSubCategory')->get_cat_list();
        if ($data) {
            Ret(array('code' => 1, 'data' => $data));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据！'));
        }
    }

    /*
    * 获取服务类别
    */
    public function get_industry()
    {
        before_api();
        $data = D('ServiceCategory')->get_cat_list();
        if ($data) {
            Ret(array('code' => 1, 'data' => $data));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据！'));
        }
    }

    /*
     * 获取服务类别
     */
    public function sub_specify()
    {
        before_api();
        $data = D('ServiceSubCategory')->get_sub_cat_list();
        if ($data) {
            Ret(array('code' => 1, 'data' => $data));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据！'));
        }
    }

    /*
   * 机构首页
   */
    public function app_inst_course_api()
    {
        before_api();
        checkLogin();
        checkAuth();
        $state = I('state', 0, 'intval');
        $is_manager = true;
        if ($state !== 2 && !$is_manager) {
            $state = 2;

        }
        $keyword = I('keyword');
        $page = I('page', 1, 'intval');
        $pagesize = I('pagesize', 10, 'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page . ',' . $pagesize;
        $instModel = D('Institution');
        $count = $instModel->get_app_count($state, $keyword);
        $data = $instModel->get_app_list($state, $keyword, $page, $fields = null);
        if ($data) {
            Ret(array('code' => 1, 'data' => $data, 'total' => $count, 'page_count' => ceil($count / $pagesize)));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据！'));
        }
    }

    /**
     * 课程
     */
    public function app_course_api()
    {
        before_api();
        checkLogin();
        checkAuth();
        $state = I('state', 0, 'intval');
        $is_manager = true;
        if ($state != 2 && !$is_manager) {
            $state = 2;
        }
        $course_catid = I('course_id', 0, 'intval');
        $keyword = I('keyword');
        $page = I('page', 1, 'intval');
        $pagesize = I('pagesize', 10, 'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page . ',' . $pagesize;
        $courseModel = D('Course');
        $instModel = D('Institution');
        $count = $courseModel->get_count($state, $keyword, $course_catid);
        $data = $courseModel->get_by_list($state, $keyword, $page, $course_catid, $fields = null);
        foreach ($data as $key => $item) {
            $institution = $instModel->get_inst_by_id($item['id']);
            $data[$key]['institution_name'] = $institution[0]['name'];
        }
        if ($data) {
            Ret(array('code' => 1, 'data' => $data, 'total' => $count, 'page_count' => ceil($count / $pagesize)));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据'));
        }
    }

    /*
     * 机构详情
     */
    public function app_inst_view_api()
    {
        before_api();
        checkAuth();
        checkLogin();
        $id = I('id');
        if ($id < 1) {
            Ret(array('code' => 2, 'info' => '参数（id）有误!'));
        }
        $res = D('Institution')->app_inst_info($id);
        $instStaffModel = D('InstStaff');
        if ($res) {
            foreach ($res as $key => $value) {
                $count = $instStaffModel->get_count($id);
                $insti = $instStaffModel->get_info($id);
                $res[$key]['teacher'] = $insti;
            }
            Ret(array('code' => 1, 'data' => $res, 'total' => $count));
        } else {
            Ret(array('code' => 2, 'info' => '获取相关数据失败'));
        }
    }

    /*
     * 机构审核
     */
    public function inst_check_api()
    {
        before_api();
        checkAuth();
        checkLogin();
        //step 1:获取需要的字段
        $data['id'] = I('id');
        $data['state'] = I('state');
        if (intval($data['id']) < 1 || intval($data['state']) < 0) {
            Ret(array('code' => 2, 'info' => '参数有误!'));
        }
        //step 2: 根据主键id 获取需要的字段信息
        $model = D('Institution');
        //step 3 ；修改审核表状态
        $editInfo = $model->updateState($data);
        if ($editInfo) {
            $info = $model->getAllField($data['id']);
            foreach ($info as $key => $value) {
                $info[$key]['state'] = get_state($value['state']);
            }
            //step:4 保存日志
            $log = array(
                'check_time' => date('Y-m-d H:i:s'),
                'institution' => $info[0]['name'],
                'check_type' => $info[0]['check_type'],
                'submitter' => $info[0]['submitter'],
                'checker' => session('worker_name'),
                'check_state' => $info[0]['state'],
                'inst_id' => $info[0]['id']
            );
            if ($log['check_type'] == 0) {
                $log['check_type'] = '新增机构信息';
            }
            if ($log['check_type'] == 4) {
                $log['check_type'] = '修改机构信息';
            }
            $logModel = D('InstCheckLog');
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
//        $data['worker_id'] = session('worker_id');
//        $logdata['checker_id'] = session('worker_id');
//        $logdata['checker'] = session('worker_name');
//        if ($data['worker_id'] < 1) {
//            Ret(array('code' => 2, 'info' => '参数（worker_id）有误!'));
//        }
//        $logdata['institution'] = I('name');
//        if (!$logdata['institution']) {
//            Ret(array('code' => 2, 'info' => '参数（institution）有误!'));
//        }
//        $logdata['submitter'] = I('submitter');
//        if (!$logdata['submitter'] ) {
//            Ret(array('code' => 2, 'info' => '参数（submitter）有误!'));
//        }
//        $logdata['check_type'] = I('check_type');
//        if ($logdata['check_type'] == null ) {
//            Ret(array('code' => 2, 'info' => '参数（check_type）有误!'));
//        }
//        if($logdata['check_type']==0){
//            $logdata['check_type']='新增机构信息';
//        }
//        if($logdata['check_type']==4){
//            $logdata['check_type']='修改机构信息';
//        }
//        $data['state'] = I('state');
//        if ($data['state'] < 1) {
//            Ret(array('code' => 2, 'info' => '参数（check_state）有误!'));
//        }
//        if($data['state']==1){
//            $logdata['check_state']='通过';
//        }
//        if($data['state']==2){
//            $logdata['check_state']='未通过';
//        }
//        $logdata['check_time'] = date('Y-m-d H:i:s');
//        $checkModel=D('Institution');
//        $checkModel->startTrans();//开启事务
//        $logModel=D('InstCheckLog');
//        $log=$logModel->add_log($logdata);
//        if($log){
//            $result=$checkModel->check($data);
//            $logModel->commit();//日志保存成功，提交
//            if($result){
//                Ret(array('code' => 1, 'info' => '审核成功'));
//            }else{
//                Ret(array('code' => 2, 'info' => '审核失败'));
//            }
//        }else{
//            $logModel->rollback();//日志保存失败，回滚
//            Ret(array('code' => 2, 'info' => '审核失败'));
//        }
    }

    /*
     * 机构审核列表
     */
    public function inst_check_list()
    {
        before_api();
        checkLogin();
        checkAuth();

        $shopModel = D('Institution');
        $page = I('page', 1, 'intval');
        $pagesize = I('pagesize', 10, 'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page . ',' . $pagesize;
        $count = $shopModel->get_check_count();
        $fields = array('id,logo,name,submitter,submit_time,state,check_type');
        $res = $shopModel->get_check_list($page, $fields);
        if ($res) {
            foreach ($res as $key => $value) {
//                $shop=$this->get_shop($value['id']);
//                $res[$key]['shop_addr']=$shop[0]['position'];
                $cat = $this->get_cat_by_id($value['id']);
                $res[$key]['category_name'] = $cat[0]['name'];
                $res[$key]['state'] = get_state($value['state']);
                $res[$key]['nnn'] = '1';
            }
            Ret(array('code' => 1, 'data' => $res, 'total' => $count, 'page_count' => ceil($count / $pagesize)));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据！'));
        }
    }

    /*
     * 机构审核列表
     */
    public function check_list()
    {
        before_api();
        checkLogin();
        checkAuth();
        $mall_id = session('mall.mall_id');
        $state = I('state', 0, 'intval');
        $is_manager = true;
        if ($state !== 2 && !$is_manager) {
            $state = 2;
        }
        $shopModel = D('Institution');
        $category_id = I('category_id');
        $keyword = I('keyword');
        $page = I('page', 1, 'intval');
        $pagesize = I('pagesize', 10, 'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page . ',' . $pagesize;
        $count = $shopModel->get_count($state = 0, $keyword = null, $category_id = null);
        $fields = 'id,logo,name,incharge_person,number,phone,category_id';
        $res = $shopModel->get_list($state, $keyword = null, $category_id = null, $page, $fields);
        if ($res) {
            foreach ($res as $key => $value) {
                $shop = $this->get_shop($value['number']);
                $res[$key]['shop_addr'] = $shop[0]['position'];
                $cat = $this->get_cat_by_id($value['category_id']);
                $res[$key]['category_name'] = $cat[0]['name'];
                $res[$key]['nnn'] = '1';
            }
            Ret(array('code' => 1, 'data' => $res, 'total' => $count, 'page_count' => ceil($count / $pagesize)));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据！'));
        }
    }

    /**
     * 机构消费明细
     */
    public function outcome_view_api()
    {
        before_api();
        checkLogin();
        checkAuth();
        $institution_id = session('institution.institution_id');
        $institution_id = 1;
        $page = I('page', 1, 'intval');
        $pagesize = I('pagesize', 10, 'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page . ',' . $pagesize;
        $institutionOutcomeModel = D('InstitutionOutcome');
        $count = $institutionOutcomeModel->get_inst_count($institution_id);
        $data = $institutionOutcomeModel->get_inst_reback($institution_id, $page, $fields = null);
        if ($data) {
            Ret(array('code' => 1, 'data' => $data, 'total' => $count, 'page_count' => ceil($count / $pagesize)));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据！'));
        }
    }

    /**
     * 机构收入明细
     */
    public function income_view_api()
    {
        before_api();
        checkLogin();
        checkAuth();
        $institution_id = session('institution.institution_id');
        $page = I('page', 1, 'intval');
        $pagesize = I('pagesize', 10, 'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page . ',' . $pagesize;
        $institutionIncomeModel = D('InstitutionIncome');
        $count = $institutionIncomeModel->get_inst_count($institution_id);
        $data = $institutionIncomeModel->get_inst_reback($institution_id, $page, $fields = null);
        if ($data) {
            Ret(array('code' => 1, 'data' => $data, 'total' => $count, 'page_count' => ceil($count / $pagesize)));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据！'));
        }
    }
    /*
     * 机构计费管理(新增）
     */
    /**
     *
     */
    public function cost_add_api()
    {
        checkLogin();
        before_api();
        checkAuth();
        $classDiscountModel = D('InstCost');

        $data['type'] = I('type');
        if ($data['type'] == null) {
            Ret(array('code' => 2, 'info' => '参数（type）错误!'));
        }
        $data['inst_ids'] = I('inst_ids');
        if ($data['inst_ids'] == null) {
            Ret(array('code' => 2, 'info' => '参数（inst_ids）错误!'));
        }
        $data['price'] = I('price');
        if ($data['price'] == null) {
            Ret(array('code' => 2, 'info' => '参数（price）错误!'));
        }
        $data['time'] = date('Y-m-d H:i:s');
        $data['submitter_id'] = session('worker_id');
        $data['submitter'] = session('worker_name');
        $data['state'] = 0;
        $data['check_type'] = 0;
        $data['time_step'] = '月';
        $result = $classDiscountModel->add($data);
        if ($result) {
            Ret(array('code' => 1, 'info' => '添加成功'));
        } else {
            Ret(array('code' => 2, 'info' => '添加失败，系统出错'));
        }


    }

    /**
     * 机构计费管理详情
     */
    public function isnt_view_api()
    {
        checkLogin();
        before_api();
        checkAuth();
        $id = I('id');
        if ($id < 1) {
            R(array('code' => 2, 'info' => '参数ID有误'));
        }
        $classDiscountModel = D('InstCost');
        $data = $classDiscountModel->get_info($id);
        foreach ($data as $key => $value) {
            $data[$key]['type_name'] = $this->secondNavi[$value['type']];
            $data[$key]['inst_name'] = $this->get_institution($value['inst_ids']);
        }
        if ($data) {
            Ret(array('code' => 1, 'data' => $data['0']));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据'));
        }
    }

    /*
     * 机构计费管理(修改）
     */
    public function cost_update_api()
    {
        checkLogin();
        before_api();
        checkAuth();
        $classDiscountModel = D('InstCost');
        $data['id'] = I('id');
        if (!$data['id']) {
            Ret(array('code' => 2, 'info' => '参数（id）有误'));
        }
        $data['type'] = I('type');
        if ($data['type'] == null) {
            Ret(array('code' => 2, 'info' => '参数（type）错误!'));
        }
        $data['inst_ids'] = I('inst_ids');
        if ($data['inst_ids'] == null) {
            Ret(array('code' => 2, 'info' => '参数（inst_ids）错误!'));
        }
        $data['price'] = I('price');
        if ($data['price'] == null) {
            Ret(array('code' => 2, 'info' => '参数（price）错误!'));
        }
        $data['time'] = date('Y-m-d H:i:s');
        $data['submitter_id'] = session('worker_id');
        $data['state'] = 0;
        $data['check_type'] = 4;
        $result = $classDiscountModel->update($data);
        if ($result) {
            Ret(array('code' => 1, 'info' => '修改成功'));
        } else {
            Ret(array('code' => 2, 'info' => '修改失败失败，系统出错'));
        }
    }

    var $secondNavi = array(1 => '机构管理费用', 2 => '机构宽带费用');

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

    /**
     * 机构计费管理列表
     */
    public function inst_index_api()
    {
        before_api();
        checkLogin();
        checkAuth();
        $page = I('page', 1, 'intval');
        $pagesize = I('pagesize', 10, 'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page . ',' . $pagesize;
        $instCostModel = D('InstCost');
        $count = $instCostModel->get_inst_count();
        $data = $instCostModel->get_inst_reback($page);
        foreach ($data as $key => $value) {
            $data[$key]['type_name'] = $this->secondNavi[$value['type']];
            $data[$key]['state'] = get_state($value['state']);
            $data[$key]['inst_name'] = $this->get_institution($value['inst_ids']);
        }
//        var_dump($data);die;
        if ($data) {
            Ret(array('code' => 1, 'data' => $data, 'total' => $count, 'page_count' => ceil($count / $pagesize)));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据！'));
        }
    }

    /**
     * 机构计费管理列表
     */
    public function instCostDetail()
    {
        before_api();
        checkLogin();
        checkAuth();

        $id = I('id', 1, 'intval');
        if (!$id) {
            Ret(array('code' => 2, 'info' => '参数（id）错误！'));
        }
        $data = D('InstCost')->where('id=' . $id)->find();
        $data['type_name'] = $this->secondNavi[$data['type']];
        $data['inst_name'] = $this->get_institution($data['inst_ids']);
        $data['state'] = get_state($data['state']);
        if ($data) {
            Ret(array('code' => 1, 'data' => $data));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据！'));
        }
    }


    var $secondNavi11 = array(1 => array('id' => 1, 'name' => '机构管理费用'), 2 => array('id' => 2, 'name' => '机构宽带费用'));

    public function cost_list()
    {
        before_api();
        checkLogin();
        checkAuth();
        Ret(array('code' => 1, 'data' => $this->secondNavi11));
    }

    /**
     * 机构计费审核列表
     */
    public function inst_cost_check_list()
    {
        before_api();
        checkLogin();
        checkAuth();
        $mall_id = session('mall.mallid');
        $page = I('page', 1, 'intval');
        $pagesize = I('pagesize', 10, 'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page . ',' . $pagesize;
        $instCostModel = D('InstCost');
        $count = $instCostModel->get_count();
        $data = $instCostModel->get_inst_list($page, $fields = null);
        foreach ($data as $key => $value) {
            $data[$key]['inst_name'] = $this->get_institution($value['inst_ids']);
//            $submitter=$this->getWorkerById($value['submitter_id']);
//            $data[$key]['submitter']=$submitter['name'];
            $data[$key]['state'] = get_state($value['state']);
            $data[$key]['type_name'] = $this->secondNavi[$value['type']];
            $data[$key]['nnn'] = '1';
        }
        if ($data) {
            Ret(array('code' => 1, 'data' => array_values($data), 'total' => $count, 'page_count' => ceil($count / $pagesize)));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据！'));
        }
    }


    /*
     * 机构计费审核
     */
    public function cost_check_api()
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
        $checkModel = D('InstCost');
        //step 3 ；修改审核表状态
        $editInfo = $checkModel->updateState($data);
        if ($editInfo) {
            $info = $checkModel->getAllField($data['id']);
            foreach ($info as $key => $value) {
                $info[$key]['inst_name'] = $this->get_institution($value['inst_ids']);
                $info[$key]['state'] = get_state($value['state']);

//                $info[$key]['type_name']= $this->secondNavi[$value['type']];
            }
            //step:4 保存日志
            $log = array(
                'check_time' => date('Y-m-d H:i:s'),
                'institution' => $info[0]['inst_name'],
                'check_type' => $info[0]['check_type'],
                'submitter' => $info[0]['submitter'],
                'checker' => session('worker_name'),
                'check_state' => $info[0]['state'],
                'inst_id' => $info[0]['id']
            );
            if ($log['check_type'] == 0) {
                $log['check_type'] = '新增机构计费信息';
            }
            if ($log['check_type'] == 4) {
                $log['check_type'] = '修改机构计费信息';
            }
            $logModel = D('InstCheckLog');
            if ($logModel->insertLog($log)) {
                Ret(array('code' => 1, 'data' => '审核成功'));
            }
        } else {
            Ret(array('code' => 2, 'info' => '审核失败'));
        }


//        $data['id'] = I('id');
//
//        if ($data['id'] ==null ) {
//            Ret(array('code' => 2, 'info' => '参数（id）有误!'));
//        }
//       // $data['worker_id'] = session('worker_id');
//        $logdata['checker_id'] = session('worker_id');
//        $logdata['checker'] = session('worker_name');
////        if ($logdata['checker']< 1) {
////            Ret(array('code' => 2, 'info' => '参数（worker_id）有误!'));
////        }
//        $logdata['institution'] = I('inst_name');
//        if (!$logdata['institution']) {
//            Ret(array('code' => 2, 'info' => '参数（institution）有误!'));
//        }
//        $logdata['submitter'] = I('submitter');
//        if (!$logdata['submitter'] ) {
//            Ret(array('code' => 2, 'info' => '参数（submitter）有误!'));
//        }
//        $logdata['check_type'] = I('check_type');
//        if ($logdata['check_type'] == null ) {
//            Ret(array('code' => 2, 'info' => '参数（check_type）有误!'));
//        }
//        if($logdata['check_type']==0){
//            $logdata['check_type']='新增机构计费信息';
//        }
//        if($logdata['check_type']==4){
//            $logdata['check_type']='修改机构计费信息';
//        }
//        $data['state'] = I('state');
//        if ($data['state'] < 1) {
//            Ret(array('code' => 2, 'info' => '参数（check_state）有误!'));
//        }
//        if($data['state']==1){
//            $logdata['check_state']='通过';
//        }
//        if($data['state']==2){
//            $logdata['check_state']='未通过';
//        }
//        $logdata['check_time'] = date('Y-m-d H:i:s');
//        $checkModel=D('InstCost');
//        $checkModel->startTrans();//开启事务
//        $logModel=D('InstCheckLog');
//        $log=$logModel->add_log($logdata);
//        if($log){
//            $result=$checkModel->check($data);
//            $logModel->commit();//日志保存成功，提交
//            if($result){
//                Ret(array('code' => 1, 'info' => '审核成功'));
//            }else{
//                Ret(array('code' => 2, 'info' => '审核失败'));
//            }
//        }else{
//            $logModel->rollback();//日志保存失败，回滚
//            Ret(array('code' => 2, 'info' => '审核失败'));
//        }
    }


    /*
     *
     * 机构收入统计
     */
    public function inst_income_statistics()
    {
        before_api();
        checkLogin();
        checkAuth();
        $start_time = I('start_time');
        $end_time = I('end_time');
        $inst_id = I('inst_id');
        $instIncomeModel = D('InstIncome');
        $datas = $instIncomeModel->get_statistics($start_time, $end_time, $inst_id);
        $data['inst_name'] = $datas[0]['inst_name'];
        $money = 0;
        foreach ($datas as $item) {
            $money = $money + $item['money'];
        }
        $data['money'] = $money;
        $data['time'] = $start_time . '-' . $end_time;
        if ($data) {
            Ret(array('code' => 1, 'data' => $data));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据！'));
        }
    }

    private function getWorkerById($id)
    {
        if ($id) {
            return D('MallWorker')->field('name')->where('id=' . $id)->find();
        }
    }

    /*
     *
     * 机构消费统计
     */
    public function inst_consume_statistics()
    {
        before_api();
        checkLogin();
        checkAuth();
        $start_time = I('start_time');
        $end_time = I('end_time');
        $inst_id = I('inst_id');
        $instConsumeModel = D('InstConsume');
        $datas = $instConsumeModel->get_statistics($start_time, $end_time, $inst_id);
        $data['inst_name'] = $datas[0]['inst_name'];
        $money = 0;
        foreach ($datas as $item) {
            $money = $money + $item['money'];
        }
        $data['money'] = $money;
        $data['time'] = $start_time . '-' . $end_time;
        if ($data) {
            Ret(array('code' => 1, 'data' => $data));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据！'));
        }

    }


    /*
     * 获取机构
     */
    public function institution(){
        before_api();
        checkLogin();
        checkAuth();
        $data=D('institution')->field('id,name')->select();
        if($data){
            Ret(array('code'=>1,'data'=>$data));
        }else{
            Ret(array('code'=>2,'info'=>'没有数据！'));
        }
    }


    public function canUpdate(){

        $id = I('id');
        $data = D('InstCost')->where(['id'=>$id])->find();
        $canUpdate = explode('-',trim($data['inst_ids'],'-'));
        $r = [];
        foreach ($canUpdate as $v){
            $name=D('institution')->where(['id'=>$v])->getField('name');
            $r[$v] = $name;
        }


        if ($data) {
            Ret(array('code' => 1, 'data' => $r));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据！'));
        }

    }

    public function institutionIsHave()
    {
        before_api();
        checkLogin();
        checkAuth();
        $data = D('institution')->field('id,name')->select();


        $haveData = D('InstCost')->institutionIsHave();
        foreach ($data as $key => $v) {
            if (!in_array($v['id'], $haveData)) {
                unset($data[$key]);
            }
        }
        if ($data) {
            Ret(array('code' => 1, 'data' => $data));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据！'));
        }
    }


    /**
     * 机构管理平台
     *  机构员工信息管理
     */

    public function instWorker()
    {
        before_api();
        checkLogin();
        checkAuth();
        $mall_id = session('mall.mall_id');
        if ($mall_id) {
            $state = I('state', 0, 'intval');
            $is_manager = true;
            if ($state !== 2 && !$is_manager) {
                $state = 2;
            }
            $keyword = I('keyword');
            $category_id = I('category_id', 0, 'intval');
            $page = I('page', 1, 'intval');
            $pagesize = I('pagesize', 10, 'intval');
            $pagesize = $pagesize < 1 ? 1 : $pagesize;
            $pagesize = $pagesize > 50 ? 50 : $pagesize;
            $page = $page . ',' . $pagesize;
            $shopModel = D('Institution');
            $count = $shopModel->get_count($keyword, $category_id);
            $fields = array('id,name,incharge_person,phone,category_id,shop_id,position_idx,shop_addr');
            $res = $shopModel->get_list($keyword, $page, $fields, $category_id);
            if ($res) {
                foreach ($res as $key => $value) {
                    $cat = $this->get_cat_by_id($value['id']);
                    $res[$key]['category_name'] = $cat[0]['name'];
                    $res[$key]['category_name'] = $this->get_cat_id($value['category_id']);
                }

                Ret(array('code' => 1, 'data' => $res, 'total' => $count, 'page_count' => ceil($count / $pagesize)));
            } else {
                Ret(array('code' => 2, 'info' => '没有数据！'));
            }
        } else {
            Ret(array('code' => 2, 'info' => '请登录商场管理平台！'));
        }
    }

    public function getInstListById()
    {
        before_api();
        checkLogin();
        checkAuth();
        $institution_id = session('inst.institution_id');
        $keyword = I('keyword');
        $category_id = I('category_id', 0, 'intval');
        $page = I('page', 1, 'intval');
        $pagesize = I('pagesize', 10, 'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page . ',' . $pagesize;
        $instModel = D('Institution');
        //$count = $instModel->get_checked_count($keyword, $category_id);
        //$fields = array('id,name,incharge_person,phone,category_id,shop_id,shop_addr');
        //$res = $instModel->get_checked_list($keyword, $page, $fields, $category_id);
        $res=$instModel->get_info($institution_id);
        //var_dump($res);
        if ($res) {
            foreach ($res as $key => $value) {
                $cat = $this->get_cat_by_id($value['id']);
                $res[$key]['category_name'] = $cat[0]['name'];
                $res[$key]['category_name'] = $this->get_cat_id($value['category_id']);
            }

            Ret(array('code' => 1, 'data' => $res, 'total' =>1, 'page_count' => 1));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据！'));
        }
    }
    /**
     * 机构管理平台
     *
     *  机构信息管理
     */
    public function indexList()
    {
        before_api();
        checkLogin();
        checkAuth();
        //$institution_id = session('inst.institution_id');
        //if ($institution_id) {
            $state = I('state', 0, 'intval');
            $is_manager = true;
            if ($state !== 2 && !$is_manager) {
                $state = 2;
            }
            $keyword = I('keyword');
            $category_id = I('category_id', 0, 'intval');
            $page = I('page', 1, 'intval');
            $pagesize = I('pagesize', 10, 'intval');
            $pagesize = $pagesize < 1 ? 1 : $pagesize;
            $pagesize = $pagesize > 50 ? 50 : $pagesize;
            $page = $page . ',' . $pagesize;
            $instModel = D('Institution');
            $count = $instModel->get_count($keyword, $category_id);
            $fields = array('id,name,incharge_person,phone,category_id,shop_id,shop_addr');
            $res = $instModel->get_list($keyword, $page, $fields, $category_id);
            if ($res) {
                foreach ($res as $key => $value) {
                    $cat = $this->get_cat_by_id($value['id']);
                    $res[$key]['category_name'] = $cat[0]['name'];
                    $res[$key]['category_name'] = $this->get_cat_id($value['category_id']);
                }

                Ret(array('code' => 1, 'data' => $res, 'total' => $count, 'page_count' => ceil($count / $pagesize)));
            } else {
                Ret(array('code' => 2, 'info' => '没有数据！'));
            }
    }
    public function getCheckedInstList()
    {
        before_api();
        checkLogin();
        checkAuth();
        //$institution_id = session('inst.institution_id');
        //if ($institution_id) {
        //$state = I('state', 0, 'intval');
        //$is_manager = true;
        /*if ($state !== 2 && !$is_manager) {
            $state = 2;
        }*/
        //$institution_id = session('inst.institution_id');
        $keyword = I('keyword');
        $category_id = I('category_id', 0, 'intval');
        $page = I('page', 1, 'intval');
        $pagesize = I('pagesize', 10, 'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page . ',' . $pagesize;
        $instModel = D('Institution');
        $count = $instModel->get_checked_count($keyword, $category_id);
        $fields = array('id,name,incharge_person,phone,category_id,shop_id,shop_addr');
        $res = $instModel->get_checked_list($keyword, $page, $fields, $category_id);
        if ($res) {
            foreach ($res as $key => $value) {
                $cat = $this->get_cat_by_id($value['id']);
                $res[$key]['category_name'] = $cat[0]['name'];
                $res[$key]['category_name'] = $this->get_cat_id($value['category_id']);
            }

            Ret(array('code' => 1, 'data' => $res, 'total' => $count, 'page_count' => ceil($count / $pagesize)));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据！'));
        }
    }
    public function instDmlApi()
    {
        before_api();
        checkLogin();
        checkAuth();
        $institution_id = session('inst.institution_id');
        $flag = I('flag');
        switch ($flag) {
            case 'add':
                $data['institution_id'] = $institution_id;
                if (!$data['institution_id']) {
                    Ret(array('code' => 2, 'info' => '数据登录获取失败！'));
                }
                $data['name'] = I('name');
                if (!$data['name']) {
                    Ret(array('code' => 2, 'info' => '请填写机构名称！'));
                }
                $data['contract_number'] = I('contract_number');
                if (!$data['contract_number']) {
                    Ret(array('code' => 2, 'info' => '请填写合同号！'));
                }
                $data['contract_start_time'] = I('contract_start_time');
                if (!$data['contract_start_time']) {
                    Ret(array('code' => 2, 'info' => '请填写同号开始时间！'));
                }
                $data['contract_end_time'] = I('contract_end_time');
                if (!$data['contract_end_time']) {
                    Ret(array('code' => 2, 'info' => '请填写同号结束时间！'));
                }
                //获取所在地区省市区
                $data['provice_id'] = I('provice_id');
                $data['city_id'] = I('city_id');
                $data['district_id'] = I('district_id');
                $data['web_site'] = I('web_site');
                $data['shop_id'] = I('number');
                $shop_addr = $this->get_shop_by_id($data['shop_id']);
                $data['position_idx'] = $shop_addr['equip_ip'];
                $data['shop_addr'] = $shop_addr['position'];

                $data['address'] = I('address');
                if (!$data['address']) {
                    Ret(array('code' => 2, 'info' => '请填写营业执照地址！'));
                }
                $category_id = I('category_id', 0);
                $data['category_id'] = rtrim($category_id, '_');
                if (!$data['category_id']) {
                    Ret(array('code' => 2, 'info' => '请选择类别！'));
                }
                $classify_id = I('classify_id', 0);
                $data['classify_id'] = rtrim($classify_id, '_');
                if (!$data['classify_id']) {
                    Ret(array('code' => 2, 'info' => '请选择分类！'));
                }
                $industry_id = I('industry_id', 0);
                $data['industry_id'] = rtrim($industry_id, '_');
                if (!$data['industry_id']) {
                    Ret(array('code' => 2, 'info' => '请选择行业！'));
                }
                $specify_id = I('specify_id', 0);
                $data['specify_id'] = rtrim($specify_id, '_');
                if (!$data['specify_id']) {
                    Ret(array('code' => 2, 'info' => '请选择细类！'));
                }
                $data['incharge_person'] = I('incharge_person');
                if (!$data['incharge_person']) {
                    Ret(array('code' => 2, 'info' => '请填写机构负责人！'));
                }
                $data['email'] = I('email');
                $data['phone'] = I('phone');
                $data['about'] = I('about');
                $data['license_img'] = I('license_img');
                $data['id_img'] = I('id_img');
                $data['certificate_img'] = json_encode($_POST['certificate_img']);

                $data['logo'] = I('logo');
                $data['imgs'] = json_encode($_POST['imgs']);
                $data['state'] = 0;
                $data['number'] = 0;
                $data['submit_time'] = date('Y-m-d H:i:s');
                $data['check_type'] = 4;
                $data['submitter'] = session('worker_name');
                $instModel = D('Institution');
                $instId = $instModel->add_inst($data);
                if ($instId) {
                    $workerData['institution_id'] = $instId;
                    $workerData['number'] = 'i' . $instId;
                    $workerData['name'] = 'admin' . $instId;
                    $workerData['password'] = createPassword('888888');

                    D('Worker')->add($workerData);


                    $outputStr = '机构超级管理员帐号：' . $workerData['number'] . ',' . '密码：' . '888888';
                    Ret(array('code' => 1, 'info' => $outputStr));
                } else {
                    Ret(array('code' => 2, 'info' => '添加失败,系统出错！'));
                }
                break;
            case 'update':
                $data['id'] = I('id');
                if (!$data['id']) {
                    Ret(array('code' => 2, 'info' => '数据(id)获取失败！'));
                }
                $data['institution_id'] = $institution_id;
                if (!$data['institution_id']) {
                    Ret(array('code' => 2, 'info' => '数据获取失败！'));
                }
                $data['name'] = I('name');
                if (!$data['name']) {
                    Ret(array('code' => 2, 'info' => '请填写机构名称！'));
                }
                $data['shop_id'] = I('number');
                $shop_addr = $this->get_shop_by_id($data['shop_id']);
                $data['position_idx'] = $shop_addr['equip_ip'];
                $data['shop_addr'] = $shop_addr['position'];
                $data['contract_number'] = I('contract_number');
                if (!$data['contract_number']) {
                    Ret(array('code' => 2, 'info' => '请填写合同号！'));
                }
                $data['contract_start_time'] = I('contract_start_time');
                if (!$data['contract_start_time']) {
                    Ret(array('code' => 2, 'info' => '请填写同号开始时间！'));
                }
                $data['contract_end_time'] = I('contract_end_time');
                if (!$data['contract_end_time']) {
                    Ret(array('code' => 2, 'info' => '请填写同号结束时间！'));
                }
                //获取所在地区省市区
                $data['provice_id'] = I('provice_id');
                $data['city_id'] = I('city_id');
                $data['district_id'] = I('district_id');
                $data['web_site'] = I('web_site');

                $data['address'] = I('address');
                if (!$data['address']) {
                    Ret(array('code' => 2, 'info' => '请填写营业执照地址！'));
                }
                $data['category_id'] = I('category_id', 0);
                if (!$data['category_id']) {
                    Ret(array('code' => 2, 'info' => '请选择类别！'));
                }
                $data['classify_id'] = I('classify_id', 0);
                if (!$data['classify_id']) {
                    Ret(array('code' => 2, 'info' => '请选择分类！'));
                }
                $data['industry_id'] = I('industry_id', 0);
                if (!$data['industry_id']) {
                    Ret(array('code' => 2, 'info' => '请选择行业！'));
                }
                $data['specify_id'] = I('specify_id', 0);
                if (!$data['specify_id']) {
                    Ret(array('code' => 2, 'info' => '请选择细类！'));
                }
                $data['incharge_person'] = I('incharge_person');
                if (!$data['incharge_person']) {
                    Ret(array('code' => 2, 'info' => '请填写机构负责人！'));
                }
                $data['email'] = I('email');
                $data['about'] = I('about');
                $data['license_img'] = I('license_img');
                $data['id_img'] = I('id_img');
                $data['certificate_img'] = json_encode($_POST['certificate_img']);
                $data['logo'] = I('logo');
                $data['imgs'] = json_encode($_POST['imgs']);
                $data['state'] = 4;//4表修改
                $data['submit_time'] = date('Y-m-d H:i:s');
                $data['submitter'] = session('worker_name');
                $data['check_type'] = 4;
                $InstModel = D('Institution');
                $result = $InstModel->update_inst($data);
                if ($result) {
                    Ret(array('code' => 1, 'info' => '修改成功！'));
                } else {
                    Ret(array('code' => 2, 'info' => '修改失败,系统出错！'));
                }
                break;
            case 'delete':
                $id = I('id');
                if (!$id) {
                    Ret(array('code' => 2, 'info' => 'ID获取失败！'));
                }
                $InstModel = D('Institution');
                $result = $InstModel->delete_inst($id);
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

    public function inst_list()
    {
        $institutionModel = D('institution');
        $data = $institutionModel->get_inst_id();
        if ($data) {
            Ret(array('code' => 1, 'data' => $data));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据'));
        }
    }

    public function get_sky()
    {
        $start_time = I('start_time');
        $end_time = I('end_time');
        $start_time = strtotime($start_time); //当前时间  ,注意H 是24小时 h是12小时
        $end_time = strtotime($end_time);  //过年时间，不能写2014-1-21 24:00:00  这样不对
        $time = ceil(($end_time - $start_time) / 60 / 60 / 24 / 30);
        if ($time) {
            Ret(array('code' => 1, 'data' => $time));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据'));
        }

    }


    public function getMonth()
    {
        $date1 = I('end_time');
        $date2 = I('start_time');
        $data = getMonthNum($date2, $date1);
        if ($data) {
            Ret(array('code' => 1, 'data' => $data));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据'));
        }
    }




//        $inst_id = I('id');
//        if (!$inst_id) {
//            Ret(array('code' => 2, 'info' => '机构数据获取失败！'));
//        }
//        $start_time= I('start_time',0,'intval');
//        $end_time=I('end_time',0,'intval');
//        $instModel = D('Institution');
//        $data = $instModel->get_info($inst_id);
////        foreach($data as $key =>$value){
////            $data[$key]['category_name']=$this->getCats($value['category_id']);
////        }
//        if ($data) {
//            $time = get_sky($start_time,$end_time);
//            //获取物业费和租金
//            $cost = $this->get_shop_cost($data[0]['shop_id']);
//            $data[0]['property_price'] = $cost[0]['property_price'];
//            $data[0]['property_total price'] =bcmul($data[0]['property_price'], $time);
////            var_dump($data);die;
//            $data[0]['rent_rate'] = $cost[0]['rent_rate'];
//            $data[0]['rent_total price'] =bcmul($data[0]['rent_rate'], $time);
//            var_dump($data);die();
//            //管理费和宽带费
//            $expense = $this->get_expense_by_merchant($inst_id);
//            //var_dump($expense);die;
//            if ($expense[0]['expense_id'] == 1) {
//                $data[0]['manage_cost'] = $expense[0]['price'];
//            }
//            if ($expense[1]['expense_id'] == 2) {
//                $data[0]['internet_cost'] = $expense[1]['price'];
//            }
////            $data[0]['shop_addr'] =$data[0]['position_idx'];
//
//            $data[0]['category_name'] = $this->getCats($data[0]['category_id']);
////            $data[0]['category_name'] =$cast[0]['name'];
////            var_dump($data);die();
//            $data[0]['classify_name'] = $this->getClas($data[0]['classify_id']);
//            $data[0]['industry_name'] = $this->getIndustry($data[0]['industry_id']);
//            $data[0]['specify_name'] = $this->getInstspf($data[0]['specify_id']);
//            Ret(array('code' => 1, 'data' => $data[0]));
//        } else {
//            Ret(array('code' => 2, 'info' => '没有数据！'));
//        }
//    }

    public function exportExcel()
    {
        $data = array(
            array(NULL, 2010, 2011, 2012),
            array('Q1', 12, 15, 21),
            array('Q2', 56, 73, 86),
            array('Q3', 52, 61, 69),
            array('Q4', 30, 32, 0),
        );
        $data = createXLS($data, $filename = 'simple.xls');
        var_dump($data);
        die;


    }


}
