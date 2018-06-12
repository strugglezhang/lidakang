<?php

namespace Inst\Controller;


class InstAppController extends CommonController
{

    // 机构app首页
    public function index()
    {
        // 查询分类
        $categoryId = I('cid', 'all');
        $page = I('page', 1);
        $pageSize = I('pagesize', 10);
        if ($categoryId != 'all') {
            $condition['category_id'] = $categoryId;
        }

        $pageSize = $pageSize > 50 ? 50 : $pageSize;
        $pageWhere = $page . ',' . $pageSize;


        // 计算分类数据
        $category = D('inst_category');
        $allCategory = $category->select();
        $categoryList = [];
        if (!empty($allCategory)) {
            foreach ($allCategory as $item) {
                $categoryList[$item['id']] = $item['name'];
            }
        }

        // 计算列表数据,以及总数
        $listModel = D('Institution');
        $list = $listModel->where($condition)->page($pageWhere)->select();
        $count = $listModel->where($condition)->count();
        $data = [];
        if (!empty($list)) {
            foreach ($list as $item) {
                $tmp['id'] = $item['id'];
                $tmp['name'] = $item['name'];
                $tmp['phone'] = $item['phone'];
                $tmp['logo'] = $_SERVER['SERVER_ADDR'] . $item['logo'];
                $tmp['address'] = $item['address'];
                array_push($data, $tmp);
            }
        }

        Ret(array(
            'code' => 1,
            'data' => $data,
            'total' => $count,
            'page' => $page,
            'page_count' => ceil($count / $pageSize)
        ));
    }

    // 机构图标
    public function instIcon()
    {
        $instId = I('instId');
        if (empty($instId)) {
            Ret(array('code' => 2, 'info' => '机构Id不能为空'));
        }

        $condition['id'] = $instId;
        $pic = D('Institution')->where($condition)->getField('logo');
        $data['logo'] = $_SERVER['SERVER_ADDR'] . $pic;
        $data['instId'] = $instId;
        $data['data'] = [
            '机构名称',
            '课程图标',
            '师资力量',
            '机构环境',
            '行业资质'
        ];
        Ret(['code' => 1, 'data' => $data]);
    }

    // 机构介绍
    public function instDesc()
    {
        $instId = I('instId');
        if (empty($instId)) {
            Ret(array('code' => 2, 'info' => '机构Id不能为空'));
        }

        // 获取信息
        $data = D('Institution')->field('id,about,address,name,web_site,phone,incharge_person')->where('id=' . $instId)->select();
        if (empty($data)) {
            Ret(array('code' => 2, 'info' => '机构不存在'));
        }

        Ret(['code' => 1, 'data' => $data[0]]);
    }


    // 课程图标
    public function instClass()
    {
        $instId = I('instId');
        if (empty($instId)) {
            Ret(array('code' => 2, 'info' => '机构Id不能为空'));
        }

        // 构造分页
        $page = I('page', 1);
        $pageSize = I('pagesize', 10);
        $pageSize = $pageSize > 50 ? 50 : $pageSize;
        $pageWhere = $page . ',' . $pageSize;

        // 组装数据
        $condition['institution_id'] = $instId;
        $instModel = D('Course')->field('id,name,course_catid,institution_id,course_time,pic')->where($condition)->page($pageWhere);
        $list = $instModel->select();
        $count = $instModel->count();
        foreach ($list as $key => $item) {
            $list[$key]['institution_name'] = D('Institution')->where('id=' . $instId)->getField('name');
        }

        // 确定返回值
        if ($list) {
            Ret(array('code' => 1, 'data' => $list, 'total' => $count, 'page_count' => ceil($count / $pageSize)));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据'));
        }

    }

    // 机构师资力量
    public function instTeach()
    {
        $instId = I('instId');
        if (empty($instId)) {
            Ret(array('code' => 2, 'info' => '机构Id不能为空'));
        }

        $condition['institution_id'] = $instId;
        $data = D('InstStaff')->where($condition)->select();

        $list = [];
        foreach ($data as $key => $value) {
            $tmp['id'] = $value['id'];
            $tmp['name'] = $value['name'];
            $tmp['pic'] = $value['pic'];
            $tmp['remarks'] = $value['remarks'];
            array_push($list, $tmp);
        }
        if ($list) {
            Ret(array('code' => 1, 'data' => $list));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据'));
        }

    }

    // 行业资质
    public function instCertificate()
    {
        $instId = I('instId');
        if (empty($instId)) {
            Ret(array('code' => 2, 'info' => '机构Id不能为空'));
        }

        $data = D('Institution')->field('id,certificate_img,name')->where('id=' . $instId)->select();
        $list = [];
        foreach ($data as $key => $value) {
            $tmp['id'] = $value['id'];
            $tmp['name'] = $value['name'];
            $tmp['certificate_img'] = explode(',', $value['certificate_img']);
            array_push($list, $tmp);
        }

        if ($list) {
            Ret(array('code' => 1, 'data' => $data[0]));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据'));
        }
    }

    // 机构环境
    public function instEnv()
    {
        $instId = I('instId');
        if (empty($instId)) {
            Ret(array('code' => 2, 'info' => '机构Id不能为空'));
        }

        $data = D('Institution')->field('id,imgs,name')->where('id=' . $instId)->select();
        $list = [];
        foreach ($data as $key => $value) {
            $tmp['id'] = $value['id'];
            $tmp['name'] = $value['name'];
            $tmp['imgs'] = $value['imgs'];
            array_push($list, $tmp);
        }

        if ($list) {
            Ret(array('code' => 1, 'data' => $list));
        } else {
            Ret(array('code' => 2, 'info' => '没有数据'));
        }
    }

}