<?php

namespace Inst\Controller;
class RoomController extends CommonController {

    /**
     * @desc 教室使用信息
     */
    public function class_use_info(){
        $start_time = I("request.start_time");
        $end_time = I("request.end_time");
        $category_id = I("request.category_id");
        $course_id = I("request.course_id");
        $pageno = I("request.pageno",1);
        $pagenum = I("request.pagenum",10);

        $model = M('RoomReserve');
        $signModel = D("MemberSign");
        // 测试账号
        $condition = [];
        if(!empty($start_time) && !empty($end_time)){
            $condition['start_time'] = ['elt',$start_time];
            $condition['end_time'] = ['egt',$end_time];
        }

        // 按照分类查询
        if(!empty($category_id)){
            $condition['category_id'] = $category_id;

        }

        // 按照所属查询
        if(!empty($course_id)){
            $condition['course_id'] = $course_id;
        }

        $count = $model->where($condition)->count();
        // 计算分页开始
        $total = ceil($count / $pagenum);
        // 分页下限违法
        if($pageno <= 0 || $count == 0){
            $pageno = 1;
        }
        // 分页上限违法
        if($pageno > $total) {
            $pageno = $total;
        }
        //判断无数据
        if($total == 0){
            $pageno = 1;
        }
        //获取数据
        $start = ($pageno -1 ) * $pagenum;
        $data = $model->where($condition)->order('id desc')->limit($start.','.$pagenum)->select();
        $res = [];
        if(!empty($data)){
            foreach($data as $v){
                $tmp['id'] = $v['id'];
                $tmp['course_id'] = $v['course_id'];
                $tmp['course_name'] = $v['course_name'];
                $tmp['start_time'] = $v['start_time'];
                $tmp['end_time'] = $v['end_time'];
                $where['roomid'] = $v['room_id'];
                $where['sign_time'] = array(array('gt',$v['start_time']),array('lt',$v['end_time']), 'and') ;
                $pnum = $signModel->where($where)->count();
                //echo $signModel->getLastSql();
                $tmp['count'] = $pnum;
                array_push($res,$tmp);
            }
        }
        Ret(array('code' => 1, 'data' => $res, 'count' => $count));
    }

    /**
     * @desc 上课会员信息
     */
    public function class_member_info(){
        $start_time = I("request.start_time");
        $end_time = I("request.end_time");
        $course_id= I("request.course_id");
        $signModel = D("MemberSign");
        $memberModel = D("Member");
        $courseModel = D("Course");
        $CourseBuyDetailModel = D("CourseBuyDetail");
        $where['sign_time'] = array(array('gt',$start_time),array('lt',$end_time), 'and') ;
        $where['course_id'] = $course_id;
        $data = $signModel->where($where)->select();
        $count = $signModel->where($where)->count();
        // 获取课程
        $course_info = $courseModel->where("id=$course_id")->find();
        // 获取会员类型
        $ret = [];
        if($data){
            foreach($data as $key=>$item){
                // 获取会员信息
                $member_info = $memberModel->where("id={$item['member_id']}")->find();
                $ret[$key]['name'] = $member_info['name'];
                $ret[$key]['course_id'] = $course_id;
                $ret[$key]['room_id'] = $item['roomid'];
                $ret[$key]['coure_name'] = $coure_info['name'];
                $ret[$key]['start_time'] = $start_time;
                $ret[$key]['end_time'] = $end_time;
                $buy_info = $CourseBuyDetailModel->where("course_id=$course_id and buyer_id='{$item['member_id']}'")->find();
                //echo $CourseBuyDetailModel->_sql();
                $ret[$key]['type'] = $buy_info['buyer_type'];
            }
        }
        Ret(array('code' => 1, 'data' => $ret, 'count' => $count));
    }


    /*
     * @desc 房间信息
     */
    public function class_room_info(){
        $classify_id = I("request.classify_id");
        $pageno = I("request.pageno",1);
        $pagenum = I("request.pagenum",10);

        $model = M('Room');
        $condition = new \StdClass();
        if(!empty($classify_id)){
            $condition->classify_id = $classify_id;
        }
        $res = [];
        // 记录总数,按条件获取数据
        $count = $model->where($condition)->count();
        // 计算分页开始
        $total = ceil($count / $pagenum);
        // 分页下限违法
        if($pageno <= 0 || $count == 0){
            $pageno = 1;
        }
        // 分页上限违法
        if($pageno > $total) {
            $pageno = $total;
        }
        //判断无数据
        if($total == 0){
            $pageno = 1;
        }
        //获取数据
        $start = ($pageno -1 ) * $pagenum;
        $data = $model->where($condition)->order('id')->limit($start.','.$pagenum)->select();
        if(!empty($data)){
            foreach($data as $v){
                $tmp['id'] = $v['id'];
                $tmp['name'] = $v['industry_name'];
                $tmp['category_name'] = $v['category_name'];
                $tmp['classify_name'] = $v['classify_name'];
                $tmp['max_number'] = $v['max_number'];
                array_push($res,$tmp);
            }
        }

        Ret(array('code' => 1, 'data' => $res, 'count' => $count));
    }
}

