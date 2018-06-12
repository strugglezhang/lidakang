<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/7/22
 * Time: 22:38
 */
namespace Mall\Model;
use Think\Model;
class ShopCheckLogModel extends Model{
    protected $tableName = 'shop_check_log';
    public function get_list($page,$keyword){
        return $this->page($page)->select();
    }
    public function get_count(){
        return $this->count();
    }
    public function add_log($data){
        return $this->add($data);
    }

    public function insertLog($data){
        return $this->add($data);
    }

    public function getCount($keyword = null){
        if (!empty($keyword)) {
            if(preg_match("/^\d*$/",$keyword)){
                $condition['id|shop_position'] = $keyword;
            }
        }
        return $this->where($condition)->count();
    }

    public function getList($keyword = null, $page = '1,10',$fields = null)
    {
        if (!empty($keyword)) {
            if(preg_match("/^\d*$/",$keyword)){
                $condition['id|shop_position'] = $keyword;
            }
        }
        return $this->field('id,check_time,shop_position,check_type,submitter,checker,check_state,shop_id')->where($condition)->page($page)->select();
    }
}
