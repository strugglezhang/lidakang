<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/7/27
 * Time: 10:33
 */

namespace Inst\Model;

use Think\Model;

class InstCostModel extends Model
{
    protected $tableName = 'inst_cost';

    public function update($data)
    {
        if ($data) {
            return $this->save($data);
        }
    }

    public function get_inst_reback($page)
    {
        return $this->field('id,inst_ids,type,price,state,check_type')->page($page)->select();
    }

    public function get_inst_count()
    {
        return $this->count();
    }

    public function get_info($id)
    {
        if (!$id) {
            return false;
        }
        return $this->field('inst_ids,type,price ')->where('id=' . $id)->select();
    }

    public function check($data)
    {
        if ($data) {
            return $this->save($data);
        }
    }

    public function get_inst_list($page)
    {
        return $this->field('id,inst_ids,type,price,time_step,state,submitter')->page($page)->select();
    }

    public function get_count()
    {
        return $this->count();
    }

    public function updateState($data)
    {
        return $this->where('id=' . $data['id'])->setField('state', $data['state']);
    }

    public function getAllField($id)
    {
        return $this->where('id=' . $id)->field('*')->select();
    }

    public function institutionIsHave()
    {
        $data = $this->select();
        $r = [];
        foreach ($data as $value) {
            $institution = explode('-', $value['inst_ids']);
            foreach ($institution as $v) {
                if (in_array($v, $r)) continue;
                if(empty($v)) continue;
                array_push($r,$v);
            }
        }
        return $r;
    }
}