<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-06-25
 * Time: 18:59
 */
namespace Mall\Model;
use Think\Model;
class ClassDiscountModel extends Model{
    protected $trueTableName ='room_discount';
    public function get_count( $mall_id = 0)
    {
        if ($mall_id !== 0) {
            $condition['mall_id'] = $mall_id;
        }
        return $this->where($condition)->count();

    }
    public function get_list($mall_id = 0,$page = '1,10', $fields = null)
    {
        if ($mall_id !== 0) {
            $condition['mall_id'] = $mall_id;
        }
        return $this->field($fields)->where($condition)->page($page)->select();
    }
    public function add_room($data){
        if(!$data){
            return false;
        }
        return $this->add($data);
    }
    public function update_room($data){
        if(!$data){
            return false;
        }
        return $this->where('id='.$data['id'])->save($data);
    }
    public function del_room($id){
        if(!$id){
            return false;
        }
        return $this->where('id='.$id)->delete($id);

    }

    public function get_discount_info($id){
        if($id){
            return $this->where('id='.$id)->select();
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

    public function filterTimime($id)
    {
        $where = "category_id = '{$id}'";
        return $this->where($where)->field('id,start_time,end_time')->select();
    }

}
