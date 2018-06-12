<?php

class Member extends Model {

    protected $dbName = 'SmartMalls';
    protected $tablePrefix = '';
    protected $autoCheckFields = FALSE;


//    wen.chang
    public function  __construct($table = '',$connection='') {
        parent::__construct($table,$connection);
    }

}



?>