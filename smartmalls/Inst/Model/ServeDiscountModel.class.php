<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/7/4
 * Time: 15:51
 */
namespace Inst\Model;
use Think\Model;
class ServeDiscountModel extends Model{
    protected $tableName='service_charge';
    public function get_count( $mall_id=0,$keyword=null)
    {
        if ($mall_id !== 0) {
            $condition['mall_id'] = $mall_id;
        }
        if (!empty($keyword)) {
            if (preg_match("/^\d*$/", $keyword)) {
                $condition['id'] = $keyword;
            } else {
                $condition['service_name'] = array('like', "%{$keyword}%", 'or');
            }
        }
        return $this->where($condition)->count();

    }
    public function get_list($mall_id=0,$keyword=null,$page = '1,10', $fields = null)
    {
        if ($mall_id !== 0) {
            $condition['mall_id'] = $mall_id;
        }
        if (!empty($keyword)) {
            if (preg_match("/^\d*$/", $keyword)) {
                $condition['id'] = $keyword;
            } else {
                $condition['service_name'] = array('LIKE', '%' . $keyword . '%');
            }
        }
      return $this->field('id,service_catid,service_sub_catid,start_time,end_time,rate,service_id,state,submitter,mall_id,check_type')->where($condition)->page($page)->select();

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
        $condition=array('id'=>$data['id'],'state=2');
        return $this->where($condition)->save($data);
    }
    public function del_serve($id){
            if(!$id){
                return false;
            }
        return $this->where('id='.$id)->delete($id);
    }
    public function get_serve_by_id($id){
        return $this->field('name')->where('id='.$id)->select();
    }
    public function get_serve_info($id){
        if(!$id){
            return false;
        }
        return $this->field('id,service_catid,service_sub_catid,start_time,end_time,rate,merchant_id,service_id,submitter')->where('id='.$id)->select();
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
