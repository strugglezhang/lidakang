<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/8/21
 * Time: 14:42
 */
namespace Member\Model;
use Think\Model;
class CourseStudyDetailModel extends Model{
    protected $tableName = 'course_study_detail';
    public function getCourseStudyDetailList($start_time, $end_time, $keyword, $page='1,10',$instId,$courseId)
    {
        if($end_time && $start_time)
        {
            $condition['course_time'] = array('elt', $end_time);
            $condition['course_time'] = array('egt', $start_time);
        }
        if($instId)
        {
            $condition['inst_id']=$instId;
        }
        if($courseId)
        {
            $condition['course_id']=$courseId;
        }
        if (!empty($keyword)) {
            if(preg_match("/^\d*$/",$keyword)){
                $condition['buyer_card_numberNo'] = $keyword;
            }
        }
        return $this->where($condition)->page($page)->select();

    }
    public function getCourseStudyDetailCount($start_time, $end_time, $keyword, $page='1,10',$instId,$courseId)
    {
        if($end_time && $start_time)
        {
            $condition['course_time'] = array('elt', $end_time);
            $condition['course_time'] = array('egt', $start_time);
        }
        if($instId)
        {
            $condition['inst_id']=$instId;
        }
        if($courseId)
        {
            $condition['course_id']=$courseId;
        }
        if (!empty($keyword)) {
            if(preg_match("/^\d*$/",$keyword)){
                $condition['buyer_card_numberNo'] = $keyword;
            }
        }
        return $this->where($condition)->count();
    }

    public function addCourseStudyDetail($data)
    {
        return $this->save($data);
    }
}