<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/7/12
 * Time: 15:20
 */
namespace Mall\Model;
use Think\Model;
class RoomReserveModel extends Model {
    protected $tableName = 'room_reserve';
    public function get_dated_list($mall_id = 0 ,$date = 0 ,$cat = 0){
        if(!$mall_id){
            return false;
        }
        $condition['mall_id'] = $mall_id;
        if($date!=0){
            $condition['start_time'] = array( array('egt',$date), array('lt',$date.'+1 day'));
        }

        if($cat!=0){
            $condition['category_id'] = $cat;
        }
        return $this->where($condition)->count();
    }

    public function get_count($mall_id = 0 ,$date = 0 ){
        if(!$mall_id){
            return false;
        }
        $condition['mall_id'] = $mall_id;
        if($date!=0){
            $condition['start_time'] = array( array('egt',$date), array('lt',$date.'+1 day'));
        }
        return $this->where($condition)->field('members_use')->select();
    }

    public function date_classroom($data){
        if($data){
            return $this->add($data);
        }
    }

    public function get_list_by_activityId($room_id){
        if(!$room_id){
            return false;
        }
        $condition['room_id']=$room_id;
//        var_dump($condition);die;
//        $condition['institution_id']=$institution_id;
      return $this->where($condition)->field('room_id')->select();

    }
//    public function get_activityReservceNumber($room_id,$institution_id){
//        if(!$room_id){
//            return false;
//        }
//        $condition['room_id']=$room_id;
//        $condition['institution_id']=$institution_id;
//        return $this->where($condition)->count();
//    }

    public function getList($institution_id=0,$keyword=null,$state=0,$page='1,10',$fields){
        if($institution_id!=0){
            $condition['institution_id']=$institution_id;
        }
        if (!empty($keyword)) {
            if(is_numeric($keyword)){
                $where['course_id'] = $keyword;
            }else{
                $where['course_name'] = $keyword;

            }
        }
        return $this->where($condition)->page($page)->select();
    }
    public function getClassCount($institution_id=0,$keyword=null,$state=0){
        if($institution_id){
            $condition['institution_id']=$institution_id;
        }
        if (!empty($keyword)) {
            if (is_numeric($keyword)) {
                $where['course_id'] = $keyword;
            } else {
                $where['course_name'] = $keyword;

            }
        }
        return $this->where($condition)->count();
    }

    public function delete_room($id){
        if(!$id){
            return false;
        }
        return $this->where('id='.$id)->delete($id);
    }
    public function deleteRoomReserveByRoomId($roomId)
    {
        return $this->where('room_id='.$roomId)->delete();
    }
    public function getRoomReserveById($roomReserveId)
    {
        return $this->where('id='.$roomReserveId)->find();
    }
}