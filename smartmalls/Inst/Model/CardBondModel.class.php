<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/7/11
 * Time: 21:45
 */
namespace Inst\Model;
use Think\Model;
class CardBondModel extends Model{
    protected $tableName='cardbond';
    public function getCardByMemberID($id){
        if(!id){
            return false;
        }
        return $this->field('card_number')->where('pid='.$id)->select();
    }
}