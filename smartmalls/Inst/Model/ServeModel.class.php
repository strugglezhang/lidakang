<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/6/20
 * Time: 9:32
 */

namespace Inst\Model;
use Think\Model;
class ServeModel extends Model
{
    protected $tableName = 'service';

    public function get_count($mall_id,$keyword)
    {
        if($mall_id){
            $condition['mall_id']=$mall_id;
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

    public function get_list($mall_id, $keyword = null, $page = '1,10', $fields = null)
    {
        if($mall_id){
            $condition['mall_id']=$mall_id;
        }
        if (!empty($keyword)) {
            if (preg_match("/^\d*$/", $keyword)) {
                $condition['id'] = $keyword;
            } else {
                $condition['name'] = array('LIKE', '%' . $keyword . '%');
            }
        }
//        var_dump($condition);die;

     return $this ->field('id,mall_id,name,service_catid,service_sub_catid,merchant_id,price,state,discount,submitter_id')
          ->where($condition)
          ->page($page)
          ->select();
    }
    public function add_serve($data){
        if(!$data){
            return false;
        }
        return $this->add($data);
    }
    public function update_serve($data){
        if(!$data){
            return false;
        }
        return $this->save($data);
    }

    public function del_serve($id){
        if($id){
            return $this->where('id='.$id)->delete();
        }
    }
    public function get_serve_by_id($id){
        return $this->field('name')->where('id='.$id)->select();
    }

    public function serve_project($mall_id = 0)
    {
        if ($mall_id !== 0) {
            $condition['mall_id'] = $mall_id;
        }
        return $this->field('id,name')->where($condition)->select();
    }

    public function get_serve_info($id,$fields){
        if(!$id){
            Ret(array('code' => 2, 'info' => '获取参数（id）失败！'));
        }
        return $this->field($fields)->where('id='.$id)->select();
    }

    public function get_serve_count($mall_id,$service_catid=0,$keyword)
    {
        if($mall_id){
            $condition['mall_id']=$mall_id;
        }
        if ($service_catid !== 0) {
            $condition['service_catid'] = array('LIKE', '%' . $service_catid . '%');
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

    public function get_serve_list($mall_id,$service_catid=0, $keyword = null, $page = '1,10', $fields = null)
    {
        if($mall_id){
            $condition['mall_id']=$mall_id;
        }
        if ($service_catid !== 0) {
            $condition['service_catid'] = array('LIKE', '%' . $service_catid . '%');
        }
        if (!empty($keyword)) {
            if (preg_match("/^\d*$/", $keyword)) {
                $condition['id'] = $keyword;
            } else {
                $condition['name'] = array('LIKE', '%' . $keyword . '%');
            }
        }
//        var_dump($condition);die;

        return $this ->field('id,mall_id,name,service_catid,service_sub_catid,price,state,discount,submitter,check_type')
            ->where($condition)
            ->page($page)
            ->select();
    }

    public function updateState($data)
    {
        return $this->where('id='.$data['id'])->setField('state',$data['state']);
    }
    public function getAllField($id)
    {
        return $this->where('id='.$id)->field('*')->select();
    }
}