<?php

namespace Inst\Controller;
class RoomController extends CommonController {

    /**
     * @desc 教室使用信息
     */
    public function class_use_info(){

        echo 111;


    }

    /**
     * @desc 上课会员信息
     */
    public function class_member_info(){
        echo 111;


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

