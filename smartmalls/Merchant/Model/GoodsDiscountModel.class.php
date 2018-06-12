<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/7/4
 * Time: 10:37
 */

namespace Merchant\Model;
use Think\Model;
class GoodsDiscountModel extends Model{
    protected $tableName='goods_discount';
    public function get_count( $mall_id = 0,$goods_category_id=0,$keyword)
    {
        if ($mall_id !== 0) {
            $condition['mall_id'] = $mall_id;
        }
        if ($goods_category_id !== 0) {
            $condition['goods_category_id'] =  $goods_category_id;
        }
        if (!empty($keyword)) {

            if (preg_match("/^\d*$/", $keyword)) {
                $condition['id'] = $keyword;
            } else {;
                $key_where['name'] = array('LIKE', '%' . $keyword . '%');
                $key_where['_logic'] = 'OR';
                $condition['_complex'] = $key_where;
            }
        }
        return $this->where($condition)->count();

    }
    public function get_list($mall_id = 0,$goods_category_id=0,$keyword,$page = '1,10', $fields = null)
    {
        if ($mall_id !== 0) {
            $condition['mall_id'] = $mall_id;
        }
        if ($goods_category_id !== 0) {
            $condition['goods_category_id'] = $goods_category_id;
        }
        if (!empty($keyword)) {

            if (preg_match("/^\d*$/", $keyword)) {
                $condition['id'] = $keyword;
            } else {
                $key_where['name'] = array('LIKE', '%' . $keyword . '%');
                $key_where['_logic'] = 'OR';
                $condition['_complex'] = $key_where;
            }
        }

        return $this->field('id,check_type,goods_id,goods_category_id,course_sub_category_id,merchant_id,start_time,end_time,discount,state,submitter')->where($condition)->page($page)->select();
    }

    public function add_cost($data){
        if(!$data){
            return false;
        }
        return $this->add($data);
    }
    public function update_cost($data){
        if(!$data){
            return false;
        }
        return $this->where('id='.$data['id'])->save($data);
    }
    public function del_cost($id){
        if(!$id){
            return false;
        }
        return $this->where('id='.$id)->delete($id);
    }

    public function get_goods_info($goods_id,$fields){
        if(!$goods_id){
            return false;
        }
        return $this->where('id='.$goods_id)->field($fields)->find();
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