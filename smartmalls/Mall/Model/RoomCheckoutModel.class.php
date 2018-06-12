<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-07-02
 * Time: 4:38
 */
namespace Mall\Model;
use Think\Model;

class RoomCheckoutModel extends Model{
    protected $tableName ='room_checkout_charge';
    public function get_list($mall_id,$keyword=null,$page,$fields=null){
        if(!$mall_id){
            return false;
        }
        if($keyword!=0){
            $condition['keyword']=$keyword;
        }
        $condition['mall_id']=$mall_id;
        return $this->field($fields)->where($condition)->page($page)->select();
    }
    public function get_count($mall_id){
        if(!$mall_id){
            return false;
        }
        $condition['mall_id']=$mall_id;
        return $this->where($condition)->count();
    }
    public function get_info($id){
        if(!$id){
            return false;
        }
        return $this->where('id='.$id)->field('id,category_id,room_number,state,sevenday_rate,sixday_rate,fiveday_rate,fourday_rate,threeday_rate,twoday_rate,oneday_rate,submit_time,submitter')->select();
    }
    public function add_room($data){
        //print_r($data);
         return $this->add($data);
    }
    public function update_room($data){
        return $this->save($data);
    }
    public function delete_room($id){
        return $this->delete($id);
    }

    public function updateState($data)
    {
        return $this->where('id='.$data['id'])->setField('state',$data['state']);
    }
    public function getAllField($id)
    {
        return $this->where('id='.$id)->field('*')->select();
    }
    public function del_room($id){
        if(!$id){
            return false;
        }
        return $this->where('id='.$id)->delete($id);

    }
    public function getInfoByRoomId($roomId)
    {
        return $this->where('room_id='.$roomId)->find();
    }
}