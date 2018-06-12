<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/6/13
 * Time: 17:11
 */
namespace Merchant\Model;
use Think\Model;
class ActivityRnrollModel extends Model
{
    protected $tableName = 'activity_enroll';

    public function activityenroll_list($mall_id)
    {
        if (!$mall_id) {
            return false;
        }
        return $this->field('id,name,host,start_time,end_time,place,contact,phone')->where('state=1')->select();
    }

    public function activity_info($id)
    {
        if (!$id) {
            return false;
        }
        return $this->field('id,img,name,host,start_time,end_time,place,contact,phone,detail')->select();
    }
}