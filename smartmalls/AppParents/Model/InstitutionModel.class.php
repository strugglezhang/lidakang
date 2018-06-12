<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/6/19
 * Time: 15:45
 */
namespace AppParents\Model;
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


    public function get_app_list($category_id,$page='1,10',$fields){
        if($category_id !== 0){
            $condition['category_id'] = array('LIKE', '%' . $category_id . '%');
        }
            return $this->field('id,name,phone,logo,address,category_id')->where($condition)->page($page)->select();
    }
    public function get_app_count($category_id){

        if($category_id !== 0){
            $condition['category_id'] = array('LIKE', '%' . $category_id . '%');
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


    public function get_institution_info($id){
        return $this->field('name,logo')->where('id='.$id)->select();
    }
    public function  get_info($institution_id){
        if(!$institution_id){
            return false;
        }
        return $this->field('name')->where('id='.$institution_id)->select();
    }
//    public function get_isnt_info($id){
//        if(!$id){
//            return false;
//        }
//        return $this->field('name')->where('id='.$id)->select();
//    }
    public function get_app_info($institution_id){
        if(!$institution_id){
            return false;
        }
        return $this->field('name,imgs')->where('id='.$institution_id)->select();
    }


    public function get_institution(){
        return $this->field('id,name')->select();
    }
    public function get_institution_by_id($host_id){
        return $this->field('id,name')->where('id='.$host_id)->select();
    }
    public function get_isnt_info($id){
        if(!$id){
            return false;
        }
        return $this->field('id,name')->where('id='.$id)->select();
    }





    public function get_pic($id){
        if(!$id){
            return false;
        }
        return $this->field('id,logo,name')->where('id='.$id)->select();
    }
    public function get_content($id){
        if(!$id){
            return false;
        }
        return $this->field('id,about,address,name,web_site,phone,incharge_person')->where('id='.$id)->select();
    }
    public function get_teacher($id){
        if(!$id){
            return false;
        }
        return $this->field('id,institution_id,name')->where('id='.$id)->select();
    }
    public function get_honor($id){
        if(!$id){
            return false;
        }
        return $this->field('id,imgs,name')->where('id='.$id)->select();
    }

    public function get_certificate($id){
        if(!$id){
            return false;
        }
        return $this->field('id,certificate_img,name')->where('id='.$id)->select();
    }


}