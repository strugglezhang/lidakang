<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/6/13
 * Time: 9:48
 */
namespace Merchant\Model;
use Think\Model;
class ActivityModel extends Model
{
    protected $tableName = 'activity';

    public function activity_list($mall_id = 0, $state = 0, $keyword = 0, $page = '1,10', $fields)
    {
        if ($mall_id != 0) {
            $condition['mall_id'] = $mall_id;
        }
        if ($state !== null) {
            $condition['state'] = $state;
        }
        if (!empty($keyword)) {
            if (preg_match("/^\d*$/", $keyword)) {
                $condition['id'] = $keyword;
            } else {

                $condition['name'] = array('LIKE', '%' . $keyword . '%');

            }
        }
        return $this->field($fields)->where($condition)->page($page)->select();
    }

    public function activity_count($mall_id = 0, $state = 0, $keyword)
    {
        if ($mall_id != 0) {
            $condition['mall_id'] = $mall_id;
        }
        if ($state !== null) {
            $condition['state'] = $state;
        }
        if (!empty($keyword)) {
            if (preg_match("/^\d*$/", $keyword)) {
                $condition['id'] = $keyword;
            } else {
                $condition['name'] = array('LIKE', '%' . $keyword . '%');
            }
        }
        return $this->where($condition)->count();
    }

    public function activity_info($id)
    {
        if (!$id) {
            return false;
        }
        return $this->field('id,img,name,host_id,start_time,end_time,place,contact,phone,detail,type')->where('id=' . $id)->select();
    }


    public function add_activity($data)
    {
        return $this->add($data);
    }

    public function update_activity($data)
    {
        $map = array('id' => $data['id']);
        return $this->where($map)->save($data);
    }

    public function delete_activity($id)
    {
        return $this->where('id=' . $id)->delete();
    }


    public function get_app_count($state)
    {
        if ($state !== 0) {
            $condition['state'] = $state;
        }
        return $this->where($condition)->count();

    }

    public function get_app_list($state, $keyword, $page = '1,10', $fields = null)
    {
        if ($state !== 0) {
            $condition['state'] = $state;
        }

        if (!empty($keyword)) {
            if (preg_match("/^\d*$/", $keyword)) {
                $key_where['id'] = $keyword;
            } else {
                $key_where['id'] = $keyword;
                $key_where['name'] = array('LIKE', '%' . $keyword . '%');
                $key_where['_logic'] = 'OR';
                $condition['_complex'] = $key_where;
            }
        }

        return $this->field('id,name,img,start_time,end_time')->where($condition)->page($page)->select();
    }

    public function app_activity_info($id)
    {
        if (!$id) {
            return false;
        }
        return $this->field('id,img,name,start_time,end_time,place,contact,phone,detail')->where('id=' . $id)->select();
    }

    public function check($data)
    {
        return $this->save($data);
    }

    public function get_list($keyword, $page = '1,10', $fields = null)
    {
        if (!empty($keyword)) {
            if(preg_match("/^\d*$/",$keyword)){
                $condition['id'] = $keyword;
            }else{
                $condition['name'] = array('LIKE','%'.$keyword.'%');
            }
        }
        return $this->field('id,name,host_id,start_time,end_time,place,contact,phone,submit_time,state,submitter,check_type,type')->where($condition)->page($page)->select();
    }

    public function get_count($keyword)
    {

        if (!empty($keyword)) {
        if(preg_match("/^\d*$/",$keyword)){
            $condition['id'] = $keyword;
        }else{
            $condition['name'] = array('LIKE','%'.$keyword.'%');
        }
    }
        return $this->where($condition)->count();
    }

    public function updateState($data)
    {
        return $this->where('id=' . $data['id'])->setField('state', $data['state']);
    }

    public function getAllField($id)
    {
        return $this->where('id=' . $id)->field('*')->select();
    }


    public function count_time($keyword, $start_time, $end_time)
    {
        if (!empty($keyword)) {
            if(preg_match("/^\d*$/",$keyword)){
                $condition['id'] = $keyword;
            }else{
                $condition['name'] = array('LIKE','%'.$keyword.'%');
            }
        }

        if ($start_time || $end_time) {
            $key_where['end_time'] = array('elt', $end_time);
            $key_where['start_time'] = array('egt', $start_time);
            $key_where['_logic'] = 'AND';
            $condition['_complex'] = $key_where;
        }
        return $this->where($condition)->count();
    }

    public function get_list_by_time($keyword, $start_time, $end_time, $page = '1,10', $fields = null)
    {
        if (!empty($keyword)) {
            if(preg_match("/^\d*$/",$keyword)){
                $condition['id'] = $keyword;
            }else{
                $condition['name'] = array('LIKE','%'.$keyword.'%');
            }
        }
        if ($start_time || $end_time) {
            $key_where['end_time'] = array('elt', $end_time);
            $key_where['start_time'] = array('egt', $start_time);
            $key_where['_logic'] = 'AND';
            $condition['_complex'] = $key_where;
        }
        //var_dump($page);die;
        return $this->field('id,name,host_id,start_time,end_time,place,contact,phone,type')
            ->where($condition)
            ->page($page)
            ->select();
    }
    public function getinfo($id){
        if(!$id){
            return false;
        }
        return $this->where('id=')->select();
    }


    public function getList($institution_id = 0, $state = 0, $keyword = 0, $page = '1,10', $fields)
    {
        if ($institution_id != 0) {
            $condition['institution_id'] = $institution_id;
        }
        if ($state !== null) {
            $condition['state'] = $state;
        }
        if (!empty($keyword)) {
            if (preg_match("/^\d*$/", $keyword)) {
                $condition['id'] = $keyword;
            } else {

                $condition['name'] = array('LIKE', '%' . $keyword . '%');

            }
        }
        return $this->field($fields)->where($condition)->page($page)->select();
    }

    public function getCount($institution_id = 0, $state = 0, $keyword)
    {
        if ($institution_id != 0) {
            $condition['institution_id'] = $institution_id;
        }
        if ($state !== null) {
            $condition['state'] = $state;
        }
        if (!empty($keyword)) {
            if (preg_match("/^\d*$/", $keyword)) {
                $condition['id'] = $keyword;
            } else {
                $condition['name'] = array('LIKE', '%' . $keyword . '%');
            }
        }
        return $this->where($condition)->count();
    }

}