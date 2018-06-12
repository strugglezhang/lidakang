<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-07-12
 * Time: 5:57
 */
namespace Inst\Model;
use Think\Model;

class ActivityModel extends Model{
    protected $tableName='activity';
    public function get_app_count($state,$member_id){
        if ($state !== 0) {
            $condition['state'] = $state;
        }
        return $this->where($condition)->count();

    }
    public function get_app_list( $state ,$member_id, $keyword , $page = '1,10',$fields = null){
        //var_dump($course_catid);die;
        if ($state !== 0) {
            $condition['state'] = $state;
        }

        if (!empty($keyword)) {
            if(preg_match("/^\d*$/",$keyword)){
                $key_where['id'] = $keyword;
            }else{
                $key_where['id'] = $keyword;
                $key_where['name'] = array('LIKE','%'.$keyword.'%');
                $key_where['_logic'] = 'OR';
                $condition['_complex'] = $key_where;
            }
        }

        return $this->field('id,name,img,start_time,end_time')->where($condition)->page($page)->select();
    }
}