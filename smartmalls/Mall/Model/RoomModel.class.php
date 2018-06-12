<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/6/26
 * Time: 11:10
*/
namespace Mall\Model;
use Think\Model;
class RoomModel extends Model {
    protected $tableName='room';
    public function get_list($mall_id,$keyword, $page = '1,10',$fields = null){
        if(!$mall_id){
            return false;
        }
//        if($keyword!=0){
//            $condition['keyword']=$keyword;
//        }
        if (!empty($keyword)) {
                $condition['id'] = $keyword;
//                $key_where['name'] = array('LIKE','%'.$keyword.'%');
               // $key_where['_logic'] = 'AND';
               // $condition['_complex'] = $key_where;
//                var_dump($condition);die();
        }
        $condition['mall_id']=$mall_id;
        return $this->where($condition)->page($page)->select();
    }
    public function getCheckOkRoomList($mall_id,$keyword, $page = '1,10',$fields = null)
    {
        if (!empty($keyword)) {
            $key_where['id'] = $keyword;
//                $key_where['name'] = array('LIKE','%'.$keyword.'%');
           // $key_where['_logic'] = 'AND';
           // $condition['_complex'] = $key_where;
//                var_dump($condition);die();
        }
        $condition['state']=1;
        return $this->where($condition)->page($page)->select();
    }
    public function get_count($mall_id,$keyword){
        if(!$mall_id){
            return false;
        }
        if (!empty($keyword)) {
            $condition['id'] = $keyword;
//                $key_where['name'] = array('LIKE','%'.$keyword.'%');
            // $key_where['_logic'] = 'AND';
            // $condition['_complex'] = $key_where;
//                var_dump($condition);die();
        }
        $condition['mall_id']=$mall_id;
        return $this->where($condition)->count();
    }
    public function getCheckedRoomNum($mall_id,$keyword)
    {
        $condition['state']=1;
        $condition['mall_id']=$mall_id;
        if (!empty($keyword)) {
            $condition['id'] = $keyword;
//                $key_where['name'] = array('LIKE','%'.$keyword.'%');
           // $key_where['_logic'] = 'AND';
           // $condition['_complex'] = $key_where;
//                var_dump($condition);die();
        }
        return $this->where($condition)->count();
    }
    public function add_room($data){
        return $this->add($data);
    }
    public function update_room($data){
        return $this->save($data);
    }
    public function delete_room($id){
        return $this->delete($id);
    }

    public function get_class_infos($id){
            if(!$id){
                return false;
            }
            return $this->where('id='.$id)->select();
    }

    public function updateState($data)
    {
        return $this->where('id='.$data['id'])->setField('state',$data['state']);
    }
    public function getAllField($id)
    {
        return $this->where('id='.$id)->field('*')->select();
    }
    public function getNumber()
    {
        return $this->select();
    }
    public function getUnusedRoomInfoByRoomId($startTime,$endTime,$roomId)
    {
        $sql="";
        $sql="select * from room_reserve where room_id='$roomId' and (start_time >= '".$startTime."' and end_time <= '".$endTime."') ";
        //var_dump($sql);die;
        $m = new Model();
        //$usedClassroom=$m->query($sql);
        $sql1="select * from room_reserve where room_id='$roomId' and (start_time < '".$startTime."' and end_time > '".$endTime."') ";
        $sql2="select * from room_reserve where room_id='$roomId' and (start_time < '".$startTime."' and end_time <= '".$endTime."' and end_time >= '".$startTime."') ";
        $sql3="select * from room_reserve where room_id='$roomId' and (end_time > '".$endTime."' and start_time <= '".$endTime."' and start_time >= '".$startTime."') ";

        //var_dump($sql);die;
        $m = new Model();
        $usedClassroom=$m->query($sql);
        if(!empty($usedClassroom))
        {
            return $usedClassroom;
        }
        $usedClassroom1=$m->query($sql1);
        if(!empty($usedClassroom1))
        {
            return $usedClassroom1;
        }
        $usedClassroom2=$m->query($sql2);
        if(!empty($usedClassroom2))
        {
            return $usedClassroom2;
        }
        $usedClassroom3=$m->query($sql3);
        if(!empty($usedClassroom3))
        {
            return $usedClassroom3;
        }
        //$usedClassroom=array_merge($usedClassroom,$usedClassroom1,$usedClassroom2,$usedClassroom3);
        return $usedClassroom;

    }
    public function getUnusedRoomInfoByTimeAndCatelory($startTime,$endTime,$catogaryId)
    {
        //var_dump($startTime);die;
        $sql="";
        if($startTime && $endTime)
        {
            $sql="select distinct room_id from room_reserve where start_time > '".$endTime."' or end_time < '".$startTime."' ";
            //var_dump($sql);die;
        }
        else if(!$startTime && !$endTime)
        {
            $sql="select DISTINCT room_id from room_reserve where now() between start_time and end_time";
        }
        else
        {
            Ret(array('code' => 2, 'info' => '请完整选择开始时间和结束时间'));
        }
        //var_dump($sql);die;
        $m = new Model();
        $usedClassroom=$m->query($sql);
        foreach ($usedClassroom as $key=>$value)
        {
            $usedClassroomArr[$key]=$value['room_id'];
        }
        $arr_string = join(',', $usedClassroomArr);
        $m1 = new Model();
        if(!empty($catogaryId))
        {
            $sql1="select * from room where id in (".$arr_string.") and category_id=".$catogaryId."";
            //var_dump($sql1);die;
            return $m1->query($sql1);
        }
        else
        {
            $sql1="select * from room where id  in (".$arr_string.")";
            //var_dump($sql1);die;
            return $m1->query($sql1);
        }

    }

    public function getUnusedRoomNumber()
    {
        //$todayDay=date("Y-m-d");
        //$todayStartTime=
        $sql="select DISTINCT room_id from room_reserve where now() between start_time and end_time";
        $m = new Model();
        $usedClassroom= $m->query($sql);
        //var_dump($usedClassroom);die;
        $usedClassroomArr=array();
        foreach ($usedClassroom as $key=>$value)
        {
            $usedClassroomArr[$key]=$value['room_id'];
        }
        $arr_string = join(',', $usedClassroomArr);
        //var_dump($usedClassroomArr);
        $m1 = new Model();
        $sql1="select * from room where id not in (".$arr_string.")";
       // var_dump($sql1);die;
        return $m1->query($sql1);

        //return $this->where("id not in "."($usedClassroomArr)")->select();
       // $customer->where("id in "."($value)")->count();
        //var_dump($usedClassroom);die;

       // return $this->where()->select();
    }
    public function getRoomNumber($room_id)
    {

        return $this->where('id='.$room_id)->select();
    }
    public function getRoom($id){
        if(!$id){
            return false;
        }
        return $this->where('id='.$id)->select();
    }

}