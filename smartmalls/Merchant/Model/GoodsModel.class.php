<?php

/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/6/5
 * Time: 17:12
 */
namespace Merchant\Model;
use Think\Model;
class GoodsModel extends Model
{
   protected $tableName='goods';

    public function add_goods($data){
    return $this->add($data);
}
    public function update_goods($data){
        return $this->save($data);
    }
    public function delete_goods($id){
        if($id){
            return $this->where('id='.$id)->delete();
        }
    }

    public function get_count($mall_id, $goods_catid = 0,$state = 0, $keyword = null){
        if ($mall_id) {
            $where['mall_id'] = $mall_id;
        }
        if ($goods_catid !== 0) {
            $where['goods_catid'] = array('LIKE', '%' . $goods_catid . '%');
        }
        if (!empty($keyword)) {
            if (preg_match("/^\d*$/", $keyword)) {
                $where['id'] = $keyword;
            } else {
                $where['name'] = array('LIKE', '%' . $keyword . '%');
            }
        }
        return $this->where($where)->count();
    }

    public function get_list($mall_id, $goods_catid = 0,  $keyword = null, $page = '1,10',$fields = null){
        if($mall_id){
            $where['mall_id'] = $mall_id;
        }
        if ($goods_catid !== 0) {
            $where['goods_catid'] = array('LIKE', '%' . $goods_catid . '%');
        }
        if (!empty($keyword)) {
            if (preg_match("/^\d*$/", $keyword)) {
                $where['id'] = $keyword;
            } else {
                $where['name'] = array('LIKE', '%' . $keyword . '%');
            }
        }
        if ($fields === null) {
            return $this->field('password',true)->where($where)->page($page)->select();
        }else{
            return $this->field($fields)->where($where)->page($page)->select();
        }
    }

    public function get_goods_by_id($goods_id){
        if(!$goods_id){
            return false;
        }
        return $this->where('id='.$goods_id)->field('id,name')->find();
    }

    public function goods_list($mall_id = 0)
    {
        if ($mall_id !== 0) {
            $condition['mall_id'] = $mall_id;
        }
        return $this->field('id,name')->where($condition)->select();
    }
    public function get_goods_info($goods_id,$fields){
        if(!$goods_id){
            return false;
        }
        return $this->where('id='.$goods_id)->field($fields)->find();
    }

    public function get_goods_by_code($code){
        if($code){
            $condition['code']=$code;
            return $this->where($condition)->field('name,price')->find();
        }
    }
    public function check($data){
        if($data){
            return $this->save($data);
        }

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