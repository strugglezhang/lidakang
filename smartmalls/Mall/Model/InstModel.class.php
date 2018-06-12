<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/8/2
 * Time: 16:07
 */
namespace Mall\Model;
use Think\Model;
class InstModel extends Model{
    protected $tableName = "institution";
    public function getInstInfoById($id)
    {
        return $this->where('id='.$id)->find();
    }
}