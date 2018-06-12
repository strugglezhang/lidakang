<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/7/16
 * Time: 16:44
 */
namespace Inst\Model;
use Think\Model;
class InstCheckLogModel extends Model{
    protected $tableName='inst_check_log';
    public function add_log($log){
        return $this->add($log);
    }
    public function get_count($keyword=null)
    {
//        if ($category_id !== 0) {
//            $condition['category_id'] = array('LIKE', '%' . $category_id . '%');
//        }
//        if ($state !== 0) {
//            $condition['state'] = $state;
//        }
        if (!empty($keyword)) {
            if (preg_match("/^\d*$/", $keyword)) {
                $condition['id'] = $keyword;
            } else {
                $condition['institution'] = array('LIKE', '%' . $keyword . '%');
            }
        }
        return $this->where($condition)->count();
    }

    public function get_list($keyword = null, $page = '1,10',$fields = null){
//        if ($category_id !== 0) {
//            $condition['category_id'] = array('LIKE','%'.$category_id.'%');
//        }
//        if ($state !== 0) {
//            $condition['state'] = $state;
//        }
        if (!empty($keyword)) {
            if (preg_match("/^\d*$/", $keyword)) {
                $condition['id'] = $keyword;
            } else {
                $condition['institution'] = array('LIKE', '%' . $keyword . '%');
            }
        }
        if ($fields === null) {
            return $this->field('password',true)->where($condition)->page($page)->select();
        }else{
            return $this->field($fields)->where($condition)->page($page)->select();
        }
    }

    public function insertLog($data){
        return $this->add($data);
    }
}