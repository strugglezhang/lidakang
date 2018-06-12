<?php
/**
 * Created by PhpStorm.
 * User: 44364
 * Date: 2017/9/19
 * Time: 15:37
 */

namespace Inst\Controller;

class CourseCardController extends CommonController{
    public function courseCard(){
//        before_api();

        checkLogin();
        checkAuth();
        $page = I('page',1,'intval');
        $pagesize = I('pagesize',10,'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page.','.$pagesize;
        $courseCardModel =D('CourseCard');
        $count = $courseCardModel->get_count();
        $data=$courseCardModel->get_list($page, $fields = null);
        foreach ($data as $key=>$value) {
            $validit = $value['validitytimes'];
            $price = $value['course_price'];
//            var_dump($validit);die;
            $min = $value['quantity'];
            $sift = $value['gifts'];
            if($validit <= $min){
                $data[$key]['course_price'] = $min*$price;
            }else{
                $data[$key]['course_price']=$validit*$price;
            }
            $data[$key]['validitytimes']=$validit+$sift;

        }
        if($data){
            Ret(array('code'=>1,'data'=>$data,'total' => $count, 'page_count' => ceil($count/$pagesize)));
        }else{
            Ret(array('code'=>2,'info'=>'没有数据！'));
        }

    }


    public function course_dml(){
        $courseCardModel =D('CourseCard');
        $flag=I('flag');
        switch($flag){
            case add:
                $data['course_id']=I('course_id',0,'intval');
                if($data['course_id']==0){
                    Ret(array('code' => 2, 'info' => '请选择课程类别！'));
                }
                $data['name']=I('name','');
                if($data['name']==null){
                    Ret(array('code' => 2, 'info' => '课程卡名！'));
                }
                $data['priceTypeId']=I('priceTypeId',0,'intval');
                if($data['priceTypeId']==0){
                    Ret(array('code' => 2, 'info' => '请选择计费类别ID！'));
                }
                $data['priceType']=I('priceType','');
                if($data['priceType']==null){
                    Ret(array('code' => 2, 'info' => '请选择计费类别！'));
                }
                $data['validity']=I('validity',0);
                if($data['validity']==0){
                    Ret(array('code' => 2, 'info' => '请选择有效期限！'));
                }
                $data['validityTimes']=I('validityTimes',0);
                if($data['validity']==0){
                    Ret(array('code' => 2, 'info' => '请选择卡有效次数！'));
                }
                $data['course_price']=I('course_price',0);
                if($data['validity']==0){
                    Ret(array('code' => 2, 'info' => '请输入基准价格！'));
                }
                $data['quantity']=I('quantity',0);
                $data['gifts']=I('gifts',0);
                $data['course_time']=I('course_time',0);
                if($data['course_time']==0){
                    Ret(array('code' => 2, 'info' => '请输入课程时长！'));
                }
                $result = $courseCardModel->add_course($data);
                if ($result) {
                    Ret(array('code' => 1, 'info' => '添加成功！'));
                }else{
                    Ret(array('code' => 2, 'info' => '添加失败,系统出错！'));
                }
                break;
            case update:
                $data['id'] = I('id');
                $data['course_id']=I('course_id',0,'intval');
                if($data['course_id']==0){
                    Ret(array('code' => 2, 'info' => '请选择课程类别！'));
                }
                $data['name']=I('name','');
                if($data['name']==null){
                    Ret(array('code' => 2, 'info' => '课程卡名！'));
                }
                $data['priceTypeId']=I('priceTypeId',0,'intval');
                if($data['priceTypeId']==0){
                    Ret(array('code' => 2, 'info' => '请选择计费类别ID！'));
                }
                $data['priceType']=I('priceType','');
                if($data['priceType']==null){
                    Ret(array('code' => 2, 'info' => '请选择计费类别！'));
                }
                $data['validity']=I('validity',0);
                if($data['validity']==0){
                    Ret(array('code' => 2, 'info' => '请选择有效期限！'));
                }
                $data['validityTimes']=I('validityTimes',0);
                if($data['validity']==0){
                    Ret(array('code' => 2, 'info' => '请选择卡有效次数！'));
                }
                $data['course_price']=I('course_price',0);
                if($data['validity']==0){
                    Ret(array('code' => 2, 'info' => '请输入基准价格！'));
                }
                $data['quantity']=I('quantity',0);
                $data['gifts']=I('gifts',0);
                $data['course_time']=I('course_time',0);
                if($data['course_time']==0){
                    Ret(array('code' => 2, 'info' => '请输入课程时长！'));
                }
                $result = $courseCardModel->update_course($data);
                if ($result) {
                    Ret(array('code' => 1, 'info' => '修改成功！'));
                }else{
                    Ret(array('code' => 2, 'info' => '修改失败！'));
                }
                break;
            case delete:
                $id=I('id');
                if($id<1){
                    Ret(array('code' => 2, 'info' => '数据获取失败！'));
                }
                $result = $courseCardModel->del_course($id);
                if ($result) {
                    Ret(array('code' => 1, 'info' => '删除成功！'));
                }else{
                    Ret(array('code' => 2, 'info' => '删除失败,系统出错！'));
                }
                break;
            default :
                Ret(array('code' => 2, 'info' => 'Unknowed flag!'));
                break;
        }
    }

    public function updateCoureCard()
    {
        $params['id'] = I('id');                  // 修改的id
        $params['name'] = I('name');                  // 要修改的名字
        $params['price_typeid'] = I('price_typeid');  // 价格类型
        $params['validity'] = I('validity');          // 有效期
        $params['all_count'] = I('all_count');        // 总计使用次数
        $params['course_price'] = I('course_price');  // 课程价格
        $params['quantity'] = I("quantity");          // 最低次数
        $params['gifts'] = I("gifts");                // 赠送次数

        if(empty($params)){
            Ret(array('code' => 2, 'info' => '数据获取失败！'));
        }

        // 验证参数是否为空
        foreach ($params as $k =>$v){
          if($v == ''){
              Ret(array('code' => 3, 'info' => $k .'不能为空！'));
          }
        }

        $model = D("CourseCard");
        $model->updateCoureCard($params);
        Ret(array('code' => 0, 'info' => '修改成功！'));
    }

    public function addCoureCard(){

        var_dump($_REQUEST);
    }
}