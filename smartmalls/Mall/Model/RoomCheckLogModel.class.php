<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/8/14
 * Time: 12:09
 */
namespace Mall\Model;

 use Think\Model;

 class RoomCheckLogModel extends Model{
     protected $tableName ='room_check_log';
     public function get_list($room_id,$page,$keyword=null,$fields=null){
         if($keyword!=0){
             $condition['keyword']=$keyword;
         }
         if($room_id!=0){
             $condition['keyword']=$room_id;
         }
         if (!empty($keyword)) {
             $key_where['id'] = $keyword;
             $key_where['_logic'] = 'OR';
             $condition['_complex'] = $key_where;
         }
          return $this->field($fields)->where($condition)->page($page)->select();
     }
     public function get_count($room_id,$keyword){
         if($keyword!=0){
             $condition['keyword']=$keyword;
         }
         if($room_id!=0){
             $condition['keyword']=$room_id;
         }
         return $this->where($condition)->count();

     }

     public function insertLog($data){
         return $this->add($data);
     }
     public function add_log($log){
         return $this->add($log);
     }
 }