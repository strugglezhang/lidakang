<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/7/6
 * Time: 14:00
 */
namespace Merchant\Model;
use Think\Model;
class AttendenceTodayModel extends Model{
    protected $tableName='attendenc_detail';
    public function get_count(){
        return $this->count();

    }

    public function get_list($page, $fields=null){
        return $this->page($page)->select();

    }

}