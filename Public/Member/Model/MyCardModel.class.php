<?php
/**
 * Created by PhpStorm.
 * User: 44364
 * Date: 2017/9/25
 * Time: 20:57
 */
namespace Member\Model;
use Think\Model;

class MyCardModel extends Model{
    protected $trueTableName = 'card';

    public function addCard($cardInfo){
        $this->add($cardInfo);
        echo $this->_sql();
    }
}