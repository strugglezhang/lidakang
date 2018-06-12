<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/7/29
 * Time: 22:26
 */
namespace Mall\Model;
use Think\Model;
class NaviThirdModel extends Model
{
    protected $tableName = 'navi_third';

    public function add_navi($data){
        return $this->add($data);

    }
    public function update_role($data){
        $id=$data['id'];
        return $this->where('id='.$id)->save($data);
    }
    public function delete_role($id){
        return $this->where('id='.$id)->delete();
    }

    public function get_third($first_level,$second_level){
        $condition['first_level']=$first_level;
        $condition['second_level']=$second_level;
        return $this->field('therd_level')->where($condition)->select();
    }
}