<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/8/19
 * Time: 22:58
 */
namespace Mall\Model;
use Think\Model;
class InstRoomPaymentModel extends Model{
    protected $tableName = 'inst_room_payment';
    public function get_count($start_time, $end_time, $institution_id)
    {
        if ($institution_id !== 0) {
            $condition['institution_id'] = $institution_id;
        }
        $condition['state'] = 1;

        if ($start_time || $end_time) {
            $key_where['reserve_time'] = array('elt', $end_time);
            $key_where['reserve_time'] = array('egt', $start_time);
            $key_where['_logic'] = 'AND';
            $condition['_complex'] = $key_where;

        }
        return $this->where($condition)->count();
    }

    public function get_list($start_time, $end_time, $institution_id, $page = '1,10')
    {
        if ($institution_id !== 0) {
            $condition['institution_id'] = $institution_id;
        }
//        $condition['state'] = 1;
        if ($start_time || $end_time) {
            $key_where['reserve_time'] = array('elt', $end_time);
            $key_where['reserve_time'] = array('egt', $start_time);
            $key_where['_logic'] = 'AND';
            $condition['_complex'] = $key_where;
        }
        return $this->where($condition)->page($page)->select();
    }
}