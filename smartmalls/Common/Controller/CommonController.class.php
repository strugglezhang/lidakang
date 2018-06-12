<?php
namespace Admin\Controller;
use Think\Controller;
class CommonController extends Controller {
    public function category_dml_api(){
        before_api();

        checkLogin();

        checkAuth();
        $flag = I('flag');
        switch ($flag) {
            case 'add':
                $data['name'] = I('category_name');
                if (empty($data['name'])) {
                    Ret(array('code' => 2, 'info' => '类别名称不能为空！'));
                }
                if (strlen($data['name']) > 50) {
                    Ret(array('code' => 2, 'info' => '类别名称长度不能超过50个字符！'));
                }
                $categoryModel = D('Category');
                if ($categoryModel->check_category($data['name'])) {
                    Ret(array('code' => 2, 'info' => '该类别已存在，不能重复添加！'));
                }
                if ($categoryModel->add_category($data)) {
                    Ret(array('code' => 1, 'info' => '添加成功！'));
                }else{
                    Ret(array('code' => 2, 'info' => '添加失败，系统出错！'));
                }
                break;
            case 'update':
                # code...
                break;
            case 'delete':
                # code...
                break;
            default:
                Ret(array('code' => 2, 'info' => 'Unknowed flag!'));
                break;
        }
    }

    public function classify_dml_api(){
        before_api();

        checkLogin();

        checkAuth();
        $flag = I('flag');
        switch ($flag) {
            case 'add':
                $data['category_id'] = I('category_id',0,'intval');
                if ($data['category_id'] === 0) {
                    Ret(array('code' => 2, 'info' => '类别ID(category_id)不能为空！'));
                }
                $data['name'] = I('classify_name');
                if (empty($data['name'])) {
                    Ret(array('code' => 2, 'info' => '分类名称不能为空！'));
                }
                if (strlen($data['name']) > 50) {
                    Ret(array('code' => 2, 'info' => '分类名称长度不能超过50个字符！'));
                }
                $classifyModel = D('Classify');
                if ($classifyModel->check_classify($data['name'])) {
                    Ret(array('code' => 2, 'info' => '该分类已存在，不能重复添加！'));
                }
                if ($classifyModel->add_classify($data)) {
                    Ret(array('code' => 1, 'info' => '添加成功！'));
                }else{
                    Ret(array('code' => 2, 'info' => '添加失败，系统出错！'));
                }
                break;
            case 'update':
                # code...
                break;
            case 'delete':
                # code...
                break;
            default:
                Ret(array('code' => 2, 'info' => 'Unknowed flag!'));
                break;
        }
    }

    public function industry_dml_api(){
        before_api();

        checkLogin();

        checkAuth();
        $flag = I('flag');
        switch ($flag) {
            case 'add':
                $data['classify_id'] = I('classify_id',0,'intval');
                if ($data['classify_id'] === 0) {
                    Ret(array('code' => 2, 'info' => '分类ID(classify_id)不能为空！'));
                }
                $data['name'] = I('industry_name');
                if (empty($data['name'])) {
                    Ret(array('code' => 2, 'info' => '行业名称不能为空！'));
                }
                if (strlen($data['name']) > 50) {
                    Ret(array('code' => 2, 'info' => '行业名称长度不能超过50个字符！'));
                }
                $industryModel = D('Industry');
                if ($industryModel->check_industry($data['name'])) {
                    Ret(array('code' => 2, 'info' => '该行业已存在，不能重复添加！'));
                }
                if ($industryModel->add_industry($data)) {
                    Ret(array('code' => 1, 'info' => '添加成功！'));
                }else{
                    Ret(array('code' => 2, 'info' => '添加失败，系统出错！'));
                }
                break;
            case 'update':
                # code...
                break;
            case 'delete':
                # code...
                break;
            default:
                Ret(array('code' => 2, 'info' => 'Unknowed flag!'));
                break;
        }
    }
    
    public function specify_dml_api(){
        before_api();

        checkLogin();

        checkAuth();
        $flag = I('flag');
        switch ($flag) {
            case 'add':
                $data['industry_id'] = I('industry_id',0,'intval');
                if ($data['industry_id'] === 0) {
                    Ret(array('code' => 2, 'info' => '行业ID(industry_id)不能为空！'));
                }
                $data['name'] = I('specify_name');
                if (empty($data['name'])) {
                    Ret(array('code' => 2, 'info' => '细类名称不能为空！'));
                }
                if (strlen($data['name']) > 50) {
                    Ret(array('code' => 2, 'info' => '细类名称长度不能超过50个字符！'));
                }
                $specifyModel = D('Specify');
                if ($specifyModel->check_specify($data['name'])) {
                    Ret(array('code' => 2, 'info' => '该细类已存在，不能重复添加！'));
                }
                if ($specifyModel->add_specify($data)) {
                    Ret(array('code' => 1, 'info' => '添加成功！'));
                }else{
                    Ret(array('code' => 2, 'info' => '添加失败，系统出错！'));
                }
                break;
            case 'update':
                # code...
                break;
            case 'delete':
                # code...
                break;
            default:
                Ret(array('code' => 2, 'info' => 'Unknowed flag!'));
                break;
        }
    }

    public function category_api(){
        before_api();

        checkLogin();

        checkAuth();

        $categoryModel = D('Category');
        $res = $categoryModel->get_list();
        if ($res) {
            Ret(array('code' => 1, 'data' => $res));
        }else{
            Ret(array('code' => 2, 'info' => '查无相关数据！'));
        }
    }

    public function classify_api(){
        before_api();

        checkLogin();

        checkAuth();

        $category_id = I('category_id',0,'intval');
        if ($category_id === 0) {
            Ret(array('code' => 2, 'info' => '分类所属的类别ID(category_id)不能为空！'));
        }
        $classifyModel = D('Classify');
        $res = $classifyModel->get_list($category_id);
        if ($res) {
            Ret(array('code' => 1, 'data' => $res));
        }else{
            Ret(array('code' => 2, 'info' => '查无相关数据！'));
        }
    }

    public function industry_api(){
        before_api();

        checkLogin();

        checkAuth();

        $classify_id = I('classify_id',0,'intval');
        if ($classify_id === 0) {
            Ret(array('code' => 2, 'info' => '行业所属的分类ID(classify_id)不能为空！'));
        }
        $industryModel = D('Industry');
        $res = $industryModel->get_list($classify_id);
        if ($res) {
            Ret(array('code' => 1, 'data' => $res));
        }else{
            Ret(array('code' => 2, 'info' => '查无相关数据！'));
        }
    }

    public function specify_api(){
        before_api();

        checkLogin();

        checkAuth();

        $industry_id = I('industry_id',0,'intval');
        if ($industry_id === 0) {
            Ret(array('code' => 2, 'info' => '细类所属的行业ID(industry_id)不能为空！'));
        }
        $specifyModelp = D('Specify');
        $res = $specifyModelp->get_list($industry_id);
        if ($res) {
            Ret(array('code' => 1, 'data' => $res));
        }else{
            Ret(array('code' => 2, 'info' => '查无相关数据！'));
        }
    }

    public function address_api(){
        before_api();
        
        checkLogin();

        checkAuth();
        $type = I('type');
        switch ($type) {
            case 'province':
                $provinceModel = D('Province');
                $res = $provinceModel->get_list();
                if ($res) {
                    Ret(array('code' => 1, 'data' => $res));
                }else{
                    Ret(array('code' => 2, 'info' => '查无相关数据！'));
                }
                break;
            case 'city':
                $province_id = I('pid',0,'intval');
                if ($province_id === 0) {
                    Ret(array('code' => 2, 'info' => '请提交市所属省的ID！'));
                }
                $cityModel = D('City');
                $res = $cityModel->get_list($province_id);
                if ($res) {
                    Ret(array('code' => 1, 'data' => $res));
                }else{
                    Ret(array('code' => 2, 'info' => '查无相关数据！'));
                }
                break;
            case 'district':
                $city_id = I('pid',0,'intval');
                if ($city_id === 0) {
                    Ret(array('code' => 2, 'info' => '请提交区所属市的ID！'));
                }
                $districtModel = D('District');
                $res = $districtModel->get_list($city_id);
                if ($res) {
                    Ret(array('code' => 1, 'data' => $res));
                }else{
                    Ret(array('code' => 2, 'info' => '查无相关数据！'));
                }
                break;
            default:
                Ret(array('code' => 3, 'info' => 'Unknowed type'));
                break;
        }
    }
}
