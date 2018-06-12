<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/6/2
 * Time: 14:16
 */
namespace Mall\Controller;


class CardController extends CommonController
{
    //会员卡激活页面
    public function card_active(){
        $this->show('card active');
    }
    //会员卡激活api
    public function card_active_api(){

    }

    //会员补卡页面
    public function card_replace(){
        $this->show('card replace');
    }

    //会员卡充值页面
    public function card_recharge(){
        $this->show('card recharge');
    }

    //会员卡充值api
    public function card_recharge_api(){
        //
    }

}