<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/7/11
 * Time: 21:01
 */
namespace Inst\Model;
use Think\Model;
class MemberModel extends Model{
    protected $tableName='Member';
    public function getMemberInfo($member){
        if(!$member){
            return false;
        }
        $condition['id'] = $member;
        return  $this->field('id,name,phone,parent_phone,pic,card_number')->where($condition)->select();
    }

    public function get_info($member_id){
        if($member_id){
            $condition['member_id']=$member_id;
        }
        return $this->field('id,name,card_number,phone,parent_phone,number,pic')
            ->where('id='.$member_id)->select();

    }
}