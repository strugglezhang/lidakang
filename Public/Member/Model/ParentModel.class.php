<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/7/5
 * Time: 10:41
 */
namespace Member\Model;
use Think\Model;
class ParentModel extends Model
{
    protected $tableName = 'parent';

    public function get_info($id)
    {
        if (!$id) {
            return false;
        }
        $condition['member_id'] = $id;
        return $this->where($condition)->select();
    }

    public function get_app_parent($member_id)
    {
        if (!$member_id) {
            return false;
        }
        return $this->field('id,member_id,name,relation,phone,pic')->where('member_id='.$member_id)->select();
    }

    public function update_paren($data){
        if($data){
            $condition['member_id']=$data['member_id'];
            return $this->where($condition)->save($data);
        }
    }
}