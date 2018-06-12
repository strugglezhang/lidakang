<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/7/7
 * Time: 16:47
 */
namespace Merchant\Model;
use Think\Model;
class MerchantCostModel extends Model{
    protected $tableName='merchant_cost';

    public function get_count( $mall_id = 0)
    {
        if ($mall_id !== 0) {
            $condition['mall_id'] = $mall_id;
        }
        return $this->where($condition)->count();

    }
    public function getInfo($id){
        return $this->field('id,merchant_arange,cost_cat,cost')->where('id='.$id)->select();
    }
    public function get_list($mall_id,$page,$fields=null)
    {
        if ($mall_id !== 0) {
            $condition['mall_id'] = $mall_id;
        }

        return $this->field('id,merchant_arange,cost_cat,cost,state')->where($condition)->page($page)->select();
    }

    public function get_check_count( $mall_id = 0)
    {
        if ($mall_id !== 0) {
            $condition['mall_id'] = $mall_id;
        }
        //$condition['state']=array(array('eq',0),array('eq',4),'or');
        return $this->where($condition)->count();

    }
    public function get_check_list($mall_id,$page,$fields=null)
    {
        if ($mall_id !== 0) {
            $condition['mall_id'] = $mall_id;
        }
        //$condition['state']=array(array('eq',0),array('eq',4),'or');
        return $this->field('id,merchant_arange,cost_cat,cost,state,submitter,check_type')->where($condition)->page($page)->select();
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

    public function get_costs_by_id($id){
        if(!$id){
            return false;
        }
        return $this->where('id='.$id)->field('');
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