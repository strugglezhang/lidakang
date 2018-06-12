<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/6
 * Time: 9:30
 */
namespace Member\Model;
use Think\Model;
class GoodsModel extends Model
{
    protected $tableName = 'goods';
    public function makeStoreModify($code,$count){
        if($code && $count){
            $count_old = $this->where('code='.$code)->find();
            $data['count']=$count_old['count']-$count;
            return $this->where('code='.$code)->save($data);
        }

    }
}