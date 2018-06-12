<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/8/22
 * Time: 18:12
 */
namespace Inst\Model;
use Think\Model;
class CourseStudyDetailModel extends Model{
    protected $tableName = 'course_study_detail';
    public function addCourseStudyDetail($data)
    {
        return $this->add($data);
    }
}