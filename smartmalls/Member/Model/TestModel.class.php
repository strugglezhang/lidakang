<?php
/**
 * Created by PhpStorm.
 * User: 44364
 * Date: 2017/9/25
 * Time: 21:52
 */

namespace Member\Model;


use Think\Model;

class TestModel extends Model
{
    protected $tableName ='card';

    public function addCard($cardInfo){
        return $this->add($cardInfo);

    }

}