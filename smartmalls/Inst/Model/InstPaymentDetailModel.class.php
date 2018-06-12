<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/7/18
 * Time: 12:11
 */
namespace Inst\Model;
use Think\Model;
class InstPaymentDetailModel extends Model
{
    protected $tableName = 'inst_payment_detail';

    public function addInstPaymentDetail($data)
    {
        return $this->add($data);
    }

    public function getInstPaymentDetailById($id)
    {
        $condition['id'] = $id;
        return $this->where($condition)->select();
    }

    public function get_count($start_time, $end_time, $institution_id)
    {
        if ($institution_id !== 0) {
            $condition['institution_id'] = $institution_id;
        }
        $condition['state'] = 1;

        if ($start_time || $end_time) {
            $key_where['end_time'] = array('elt', $end_time);
            $key_where['start_time'] = array('egt', $start_time);
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
            $key_where['end_time'] = array('elt', $end_time);
            $key_where['start_time'] = array('egt', $start_time);
            $key_where['_logic'] = 'AND';
            $condition['_complex'] = $key_where;
        }
        return $this->where($condition)->page($page)->select();
    }
}
