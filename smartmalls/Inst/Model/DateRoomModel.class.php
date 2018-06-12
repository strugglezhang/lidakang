<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/7/10
 * Time: 16:42
 */
namespace Inst\Model;
use Think\Model;
class DateRoomModel extends Model{
    protected $tableName='room_reserve';

    public function dateroom($data){
        if(!$data){
            return false;
        }
        return $this->add($data);
    }



    public function get_count($institution_id=0,$state = 0,  $keyword = null,$fields = null){
        if ($institution_id !== 0) {
            $condition['institution_id'] = $institution_id;
        }
        if ($state !== 0) {
            $condition['state'] = $state;
        }
        if (!empty($keyword)) {
            if(preg_match("/^\d*$/",$keyword)){
                $key_where['id'] = $keyword;
            }else{
                $key_where['course_id'] = $keyword;
                $key_where['course_name'] = array('LIKE','%'.$keyword.'%');
                $key_where['_logic'] = 'OR';
                $condition['_complex'] = $key_where;
            }
        }
        return $this->where($condition)->count();

    }
    public function get_list( $institution_id=0,$state = 0, $keyword , $page = '1,10',$fields = null){
        if ($institution_id !== 0) {
            $condition['institution_id'] = $institution_id;
        }
        if ($state !== 0) {
            $condition['state'] = $state;
        }
        if (!empty($keyword)) {
            if(preg_match("/^\d*$/",$keyword)){
                $key_where['id'] = $keyword;
            }else{
                $key_where['course_id'] = $keyword;
                $key_where['course_name'] = array('LIKE','%'.$keyword.'%');
                $key_where['_logic'] = 'OR';
                $condition['_complex'] = $key_where;
            }
        }
        return $this->field('id,room_id,institution_id,course_id,real_count,reserve_time,reserve_time,course_name,start_time,end_time')->where($condition)->page($page)->select();
    }

    public function get_list_by_time( $institution_id ,$start_time,$end_time,$page='1,10'){
        if ($institution_id !== 0) {
            $condition['institution_id'] = $institution_id;
        }
            $condition['state'] = 1;
        if($start_time||$end_time){
            $key_where['end_time'] = array('lt',$end_time);
            $key_where['start_time'] = array('gt',$start_time);
            $key_where['_logic'] = 'AND';
            $condition['_complex'] = $key_where;

        }
        return $this->field('id,room_id,institution_id,course_id,real_count,reserve_time,reserve_time,course_name,start_time,end_time')->where($condition)->page($page)->select();
    }
}


