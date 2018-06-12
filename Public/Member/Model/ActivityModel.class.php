<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-07-12
 * Time: 5:57
 */
namespace Member\Model;
use Think\Model;

class ActivityModel extends Model{
    protected $tableName='activity';
    public function get_app_info( $activity_id ){
        if (!$activity_id) {
            return false;
        }
        return $this->field('id,name,img,start_time,end_time')->where('id='.$activity_id)->select();
    }
}