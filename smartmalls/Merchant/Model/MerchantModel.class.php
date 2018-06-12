<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/6/22
 * Time: 14:26
 */

namespace Merchant\Model;
use Think\Model;
class MerchantModel extends Model
{
    protected $tableName='merchant';

    public function get_count($mall_id,$merchant_catid = 0,$keyword=null){
        if ($mall_id !== null) {
            $condition['mall_id'] = $mall_id;
        }
        if ($merchant_catid !== 0) {
            $condition['merchant_category_id'] = $merchant_catid;
        }
        if (!empty($keyword)) {
            if (preg_match("/^\d*$/", $keyword)) {
                $condition['id'] = $keyword;
            } else {
                $condition['name'] = array('LIKE', '%' . $keyword . '%');
            }
        }
        $condition['state']=1;
        //var_dump($where);die;
        return $this->where($condition)->count();
    }

    public function get_list( $mall_id,$merchant_catid,$keyword = null, $page = '1,10',$fields = null){
        $condition['mall_id'] = $mall_id;
        if ($merchant_catid !== 0) {
            $condition['merchant_category_id'] = $merchant_catid;
        }
        if (!empty($keyword)) {
            if (preg_match("/^\d*$/", $keyword)) {
                $condition['id'] = $keyword;
            } else {
                $condition['name'] = array('LIKE', '%' . $keyword . '%');
            }
        }
        $condition['state']=1;
        if ($fields === null) {
            return $this->field('password',true)->where($condition)->page($page)->select();
        }else{
            return $this->field($fields)->where($condition)->page($page)->select();
        }
    }
    public function get_cat_by_id($merchant_id){
        if($merchant_id){
            return $this->where('id='.$merchant_id)->field('id,name')->find();
        }
    }

    public function get_merchant($merchant_id){
        if(!$merchant_id){
            return false;
        }
        return $this->field('id,name')->where('id='.$merchant_id)->select();
    }

    public function get_merchant_by_id($id){
        if($id){
            return $this->field('id,name')->where('id='.$id)->select();
        }
    }

    public function add_merchant($data){
        if(!$data){
            return false;
        }
        return $this->add($data);
    }

    public function update_merchant($data){
            return $this->save($data);
    }

    public function delete_merchant($id){
    if($id){
        return $this->where('id='.$id)->delete();
    }

}

    public function view_merchant($inst_id,$mall_id,$fields){
        if(!$inst_id){
            return false;
        }
        if(!$mall_id){
            return false;
        }
        $condition['id']=$inst_id;
        $condition['mall_id']=$mall_id;
        return $this->where($condition)->field($fields)->select();
    }

    public function get_check_count($mall_id){
        if ($mall_id !== null) {
            $where['mall_id'] = $mall_id;
        }
        //$where['state']=array(array('eq',0),array('eq',4),'or');
        return $this->where($where)->count();
    }

    public function get_check_list( $mall_id, $page = '1,10',$fields = null){
        $where['mall_id'] = $mall_id;
       //$where['state']=array(array('eq',0),array('eq',4),'or');
       $fields=array('id,incharge_person,pic,state,phone,merchant_category,submitter,name,check_type');
       return $this->field($fields)->where($where)->page($page)->select();

    }
    public function make_check($data){
        if(!$data){
            return false;
        }
        return $this->save($data);
    }

    public function updateState($data)
    {
        return $this->where('id='.$data['id'])->setField('state',$data['state']);
    }
    public function getAllField($id)
    {
        return $this->where('id='.$id)->field('*')->select();
    }

    public function getMerchant(){
        return $this->field('id,name')->select();
    }
}
