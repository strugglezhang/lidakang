<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/7/21
 * Time: 10:42
 */
namespace Inst\Model;
use Think\Model;
class InstPositionModel extends Model {
    protected $trueTableName = 'inst_position';


    public function get_position_by_id($position_id){
        return $this->where(array('id' => $position_id))->getField('name');
    }


}