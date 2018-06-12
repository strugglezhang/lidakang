<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/6/15
 * Time: 16:09
 */
namespace Member\Model;
use Think\Model;
class ConsumeModel extends Model{
    protected $tableName='member_bills';
    public function get_consume_list($start_time,$end_time,$type,$category,$member_id,$page){
        if($start_time || $end_time){
            $condition['time']=array('between',array($start_time,$end_time));
        }
        if($category){
            $condition['content']=$category;
        }
        if($member_id){
            $condition['member_id']=$member_id;
        }
        if($type){
            $condition['type']=$type;
        }
        return $this->field('id,type,member_id,institution_id,consume_type,content,time,price,count')->where($condition)->page($page)->select();
    }

    public function get_consume_count($start_time,$end_time,$type,$category,$member_id){
        if($start_time || $end_time){
            $condition['time']=array('between',array($start_time,$end_time));
        }
        if($category){
            $condition['content']=$category;
        }
        if($member_id){
            $condition['member_id']=$member_id;
        }
        if($type){
            $condition['type']=$type;
        }
        return $this->where($condition)->count();
    }

    public function get_recharge_by_keyword($mall_id,$keyword){
        if(!$mall_id){
            return false;
        }
        //if($keyword['begintime']>$keyword['endtime']){echo '开始时间大于结束时间';}else{echo 'aa';};die;
        $condition['mall_id']=$mall_id;
        $condition['member_id']=$keyword['member_id'];
        $condition['institution_id']=$keyword['institution_id'];
        $condition['consume_type']=$keyword['consume_type'];
        $condition['time']= array('egt',$keyword['begintime']);
        $condition['time']= array('elt',$keyword['endtime'],'AND');
        return $this->field('id,member_id,time,recharge_monney')->where($condition)->select();

    }

    public function make_reback($data){
        if($data){
            return $this->save($data);
        }
    }

    public function check_list($page){
        $condition['reback_money']=array('gt',0);
        $condition['reback_state']=0;
        return $this->where($condition)->page($page)->select();
    }
    public function check_count(){
        $condition['reback_money']=array('gt',0);
        $condition['reback_state']=0;
        return $this->where($condition)->count();
    }

    public function get_consume_reback_cout($member_id){
        if($member_id){
            $condition['member_id']=$member_id;
            return $this->where($condition)->count();
        }
    }
    public function get_consume_reback_list($member_id,$page){
        if($member_id){
            $condition['member_id']=$member_id;
            return $this->where($condition)->page($page)->select();
        }
    }

    public function make_consume($data){
        if($data){
            return $this->add($data);
        }
    }
    public function get_app_consume_list($start_time,$end_time,$member_id,$page='1,10'){
        if($start_time || $end_time){
            $condition['time']=array('between',array($start_time,$end_time));
        }
        if($member_id){
            $condition['member_id']=$member_id;
        }
        return $this->field('id,member_id,time,price,course_id,institution_id')->where($condition)->page($page)->select();
    }

    public function get_app_consume_count($end_time,$start_time,$member_id){
        if($start_time || $end_time){
            $condition['time']=array('between',array($start_time,$end_time));
        }
        if($member_id){
            $condition['member_id']=$member_id;
        }
        return $this->where($condition)->count();
    }


}