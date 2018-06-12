<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-07-05
 * Time: 3:59
 */
namespace Inst\Model;
use Think\Model;
class CourseTryoutModel extends Model
{
    protected $tableName = 'course_tryout';

    public function add_course_tryout($data)
    {
        return $this->add($data);
    }

    public function get_count()
    {

        return $this->count();
    }

    public function get_list($page = '1,10', $fields = null)
    {
        return $this->field('id,course_id,member_id,institution_id,time,state')
            ->page($page)
            ->select();
//        echo $this->_sql();
    }

    public function get_audition($start_time,$end_time,$keyword,$page = '1,10', $fields = null,$instId)
    {
        if ($start_time || $end_time) {
            $key_where['end_time'] = array('elt', $end_time);
            $key_where['start_time'] = array('egt', $start_time);
            $key_where['_logic'] = 'AND';
            $condition['_complex'] = $key_where;
        }
        if (!empty($keyword)) {
            if(preg_match("/^\d*$/",$keyword)){
                $condition['id'] = $keyword;
            }else{
                $condition['name'] = array('LIKE','%'.$keyword.'%');
            }
        }
        if(!empty($instId))
        {
            $condition['institution_id']=$instId;
        }
        return $this->field('id,course_id,member_id,institution_id,time,state,phone,member_name,end_time,start_time,room_number')
            ->where($condition)->page($page)
            ->select();;
    }


    public function audition_count($start_time,$end_time,$keyword,$instId)
    {
        if ($start_time || $end_time) {
            $key_where['end_time'] = array('elt', $end_time);
            $key_where['start_time'] = array('egt', $start_time);
            $key_where['_logic'] = 'AND';
            $condition['_complex'] = $key_where;
        }
        if (!empty($keyword)) {
            if(preg_match("/^\d*$/",$keyword)){
                $condition['id'] = $keyword;
            }else{
                $condition['name'] = array('LIKE','%'.$keyword.'%');
            }
        }
        if(!empty($instId))
        {
            $condition['institution_id']=$instId;
        }
        return $this->where($condition)->count();
    }

    public function getTakeou($id)
    {
        if (!$id) {
            return false;
        }
        return $this->field('id,start_time,end_time,room_number')->where('id=' . $id)->select();
    }

    public function upda($data)
    {
        return $this->save($data);
    }

    public function getInfo($id){
        if(!$id){
            return false;
        }
        return $this->field('*')->where('id='.$id)->select();
    }

}