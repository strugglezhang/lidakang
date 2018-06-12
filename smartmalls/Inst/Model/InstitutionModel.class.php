<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/6/19
 * Time: 15:45
 */
namespace Inst\Model;
use Think\Model;
class InstitutionModel extends Model{
    protected $tableName='institution';

    public function get_inst_by_id($inst_id){
        if($inst_id){
            return $this->where('id='.$inst_id)->field('id,name')->select();
        }
    }
    public function getAllField($id)
    {
        return $this->where('id='.$id)->field('*')->select();
    }
    
    public function get_count($keyword=null,$category_id=0,$state=0)
    {
        if ($category_id !== 0) {        
            $condition['category_id'] = array('LIKE', '%' . $category_id . '%');
        }
        /*if ($state !== 0) {
            $condition['state'] = $state;
        }*/
        if (!empty($keyword)) {
            if (preg_match("/^\d*$/", $keyword)) {
                $condition['id'] = $keyword;
            } else {
                $condition['name'] = array('LIKE', '%' . $keyword . '%');
            }
        }
        $condition['state']=1;
        return $this->where($condition)->count();
    }
    public function get_checked_count($keyword=null,$category_id=0,$state=1)
    {
        if ($category_id !== 0) {
            $condition['category_id'] = array('LIKE', '%' . $category_id . '%');
        }
        /*if ($state !== 0) {
            $condition['state'] = $state;
        }*/
        if (!empty($keyword)) {
            if (preg_match("/^\d*$/", $keyword)) {
                $condition['id'] = $keyword;
            } else {
                $condition['name'] = array('LIKE', '%' . $keyword . '%');
            }
        }
        $condition['state'] = 1;
        return $this->where($condition)->count();
    }
    public function get_list($keyword = null, $page = '1,10',$fields = null,$category_id = 0,$state=0){
		if ($category_id !== 0) {
            $condition['category_id'] = array('LIKE','%'.$category_id.'%');
        }
        if ($state !== 0) {
            $condition['state'] = $state;
        }
        if (!empty($keyword)) {
            if (preg_match("/^\d*$/", $keyword)) {
                $condition['id'] = $keyword;
            } else {
                $condition['name'] = array('LIKE', '%' . $keyword . '%');
            }
        }
        $condition['state']=1;
        if ($fields === null) {
            return $this->field('password',true)->where($condition)->page($page)->select();
        }else{
            return $this->field($fields)->where($condition)->page($page)->select();
        }
    }
    public function get_checked_list($keyword = null, $page = '1,10',$fields = null,$category_id = 0,$state=0)
    {
        if ($category_id !== 0) {
            $condition['category_id'] = array('LIKE','%'.$category_id.'%');
        }
        //if ($state !== 0) {
        $condition['state'] = 1;
        //}
        if (!empty($keyword)) {
            if (preg_match("/^\d*$/", $keyword)) {
                $condition['id'] = $keyword;
            } else {
                $condition['name'] = array('LIKE', '%' . $keyword . '%');
            }
        }
        $condition['state']=1;
        if ($fields === null) {
            return $this->field('password',true)->where($condition)->page($page)->select();
        }else{
            return $this->field($fields)->where($condition)->page($page)->select();
        }
    }
    public function add_inst($data){
        if(!$data){
            return false;
        }
        return $this->add($data);
    }
    public function update_inst($data){
        if(!$data){
            return false;
        }
        $condition['id']=$data['id'];
        return $this->where($condition)->save($data);
    }
    public function delete_inst($id){
        if(!$id){
            return false;
        }
        return $this->where('id='.$id)->delete($id);
    }

    public function get_inst_info($id){
        if(!$id){
            return false;
        }
        return $this->where('id='.$id)->select();
    }

    public function app_inst_info($id){
        if(!$id){
            return false;
        }
        return $this->field('id,logo,name,shop_id,web_site,about,certificate_img,number,imgs ')->where('id='.$id)->select();
    }


    public function get_app_list($state,$keyword,$page='1,10',$fields){
        if ($state !== 0) {
            $condition['state'] = $state;
        }
        if (!empty($keyword)) {
            $key_where['number'] = $keyword;
            $key_where['name'] = array('LIKE','%'.$keyword.'%');
            $key_where['_logic'] = 'OR';
            $condition['_complex'] = $key_where;
        }
            return $this->field('id,name,phone,logo')->where($condition)->page($page)->select();
    }
    public function get_app_count($state,$keyword=null){

        if ($state !== 0) {
            $condition['state'] = $state;
        }
        if (!empty($keyword)) {
            if(preg_match("/^\d*$/",$keyword)){
                $key_where['id'] = $keyword;
            }else{
                $key_where['number'] = $keyword;
                $key_where['name'] = array('LIKE','%'.$keyword.'%');
                $key_where['_logic'] = 'OR';
                $condition['_complex'] = $key_where;
            }
        }
        return $this->where($condition)->count();

    }

    public function get_check_count(){
        /*$where['state']=array(array('eq',0),array('eq',4),'or');
        return $this->where($where)->count();*/
        return $this->count();

    }
    public function get_check_list($page = '1,10',$fields = null){
        /*$where['state']=array(array('eq',0),array('eq',4),'or');
        if ($fields === null) {
            return $this->field('password',true)->where($where)->page($page)->select();
        }else{
            return $this->field($fields)->where($where)->page($page)->select();
        }*/
        if ($fields === null) {
            return $this->field('password',true)->page($page)->select();
        }else{
            return $this->field($fields)->page($page)->select();
        }
    }
    public function updateFiled($data){
//        $condition['id']=$data['id'];
//        $condition['status']=$data['state'];
       return $this->save($data);

    }

    public function updateState($data)
    {
        return $this->where('id='.$data['id'])->setField('state',$data['state']);
    }

    public function get_inst_id(){
        $condition['state']=1;
            return $this->field('id,name')->where($condition)->select();

    }

    public function get_info($id){
        if(!$id){
            return false;
        }
        return $this->where('id='.$id)->select();
    }



}