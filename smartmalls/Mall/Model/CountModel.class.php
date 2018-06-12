<?php
namespace Mall\Model;
use Think\Model;
class CountModel extends Model{
    protected $tableName='mall_staff';

    public function get_count_total($keyword,$dept_id){
        if (!empty($keyword)) {
            if(is_numeric($keyword)){
                $where['number'] = $keyword;
            }else{
                $where['name'] = array('LIKE','%'.$keyword.'%');
            }
        }
        if ($dept_id !== 0) {
            $where['dept_id'] = $dept_id;
        }
        return $this->where($where)->count();

    }
    public function get_count_info($keyword,$dept_id,$page){
        if (!empty($keyword)) {
            if(is_numeric($keyword)){
                $where['number'] = $keyword;
            }else{
                $where['name'] = array('LIKE','%'.$keyword.'%');
            }
        }
        if ($dept_id !== 0) {
            $where['dept_id'] = $dept_id;
        }
        return $this->where($where)->page($page)->select();
    }
    public function get_count_info_by_keyword($mall_id,$keyword){
        if(!$mall_id){
            return false;
        }
        $map=array('dept_id'=>$keyword);
        return $this->field('id,number,name,password,phone,sex,pic,position_id,dept_id,position_id')->where($map)->select();
    }

    public function update($data){
        $number=$data['number'];
        $password=$data['password'];
        if($password==null){
            Ret(array('code'=>2,'info'=>'密码不能为空'));
        }
        if(strlen($password)<6){
            Ret(array('code'=>2,'info'=>'密码长度不能小于6位'));
        }
        $map=array("number"=>$number);
        return $this->where($map)->save($data);
    }

}