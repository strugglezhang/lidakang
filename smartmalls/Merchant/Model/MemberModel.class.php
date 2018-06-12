<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/9/2
 * Time: 12:11
 */
namespace Merchant\Model;
use Think\Model;
class MemberModel extends Model{
    protected $tableName='member';
    public function get_info($member_id){
        if($member_id){
            $condition['member_id']=$member_id;
        }
        return $this->field('id,name,card_number,phone,parent_phone,number,pic')
            ->where('id='.$member_id)->select();

    }
}