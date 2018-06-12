<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/6/16
 * Time: 14:37
 */
namespace Inst\Model;
use Think\Model;
class CourseModel extends Model{
    protected $tableName='course';
    public function get_count( $course_catid = 0, $keyword = null,$fields = null){

        if($course_catid !== 0){
            $condition['course_catid'] = array('LIKE', '%' . $course_catid . '%');
        }
        if (!empty($keyword)) {
            if(preg_match("/^\d*$/",$keyword)){
                $condition['id'] = $keyword;
            }else{
                $condition['name'] = array('LIKE','%'.$keyword.'%');

            }
        }
       // $condition['state']=1;
        /*if(!empty($instId))
        {
            $condition['institution_id']=$instId;
        }*/
        return $this->where($condition)->count();
       //return $this->count();
    }
    public function get_count_by_inst( $course_catid = 0, $keyword = null,$fields = null,$instId)
    {
        if($course_catid !== 0){
            $condition['course_catid'] = array('LIKE', '%' . $course_catid . '%');
        }
        if (!empty($keyword)) {
            if(preg_match("/^\d*$/",$keyword)){
                $condition['id'] = $keyword;
            }else{
                $condition['name'] = array('LIKE','%'.$keyword.'%');

            }
        }
        if(!empty($instId))
        {
            $condition['institution_id']=$instId;
        }
        //$condition['state']=1;
        return $this->where($condition)->count();
    }
    public function get_list( $course_catid , $keyword , $page = '1,10',$fields = null){

        if($course_catid !== 0){
            $condition['course_catid'] = array('LIKE', '%' . $course_catid . '%');
        }
        if (!empty($keyword)) {
            if(preg_match("/^\d*$/",$keyword)){
                $condition['id'] = $keyword;
            }else{
                $condition['name'] = array('LIKE','%'.$keyword.'%');
            }
        }
       /* if(!empty($instId))
        {
            $condition['institution_id']=$instId;
        }*/
        //$condition['state']=1;
        return $this->field('id,name,institution_id,course_catid,course_sub_catid,course_time,pic')->where($condition)->page($page)->select();
    }
    public function get_list_by_inst($course_catid , $keyword , $page = '1,10',$fields = null,$instId)
    {
        if($course_catid !== 0){
            $condition['course_catid'] = array('LIKE', '%' . $course_catid . '%');
        }
        if (!empty($keyword)) {
            if(preg_match("/^\d*$/",$keyword)){
                $condition['id'] = $keyword;
            }else{
                $condition['name'] = array('LIKE','%'.$keyword.'%');
            }
        }
         if(!empty($instId))
         {
             $condition['institution_id']=$instId;
         }
        //$condition['state']=1;
        return $this->field('id,name,institution_id,course_catid,course_sub_catid,course_time,pic')->where($condition)->page($page)->select();
    }

    public function get_course_infos($id){
        if(!$id){
            return false;
        }
        return $this->field('id,pic,name,institution_id,course_catid,course_sub_catid,course_time,content,credit_img')->where('id='.$id)->select();
    }

   public function add_course($data){
       if(!$data){
           return false;
       }
       return $this->add($data);
   }
    public function update_course($data){
        if(!$data){
            return false;
        }
        $condition['id']=$data['id'];
        return $this->where($condition)->save($data);
    }
    public function del_course($id){
        if(!$id){
            return false;
        }
        return $this->where('id='.$id)->delete($id);
    }

    public function get_course_by_id($id){
        if(!$id){
            return false;
        }
        return $this->where('id='.$id)->field('id,name')->select();
    }

    public function get_course_number($id){
        return $this->field('id,name')->where("institution_id = $id")->select();

    }
    public function get_infos($id){
        if(!$id){
            return false;
        }
        return $this->field('id,name,institution_id,single_time_price,single_month_price,single_month_times,single_quarter_price,single_quarter_times')->where('id='.$id)->select();
    }
    public function get_by_list( $state = 0, $course_catid ,$page = '1,10',$fields = null){
        if ($state !== 0) {
            $condition['state'] = $state;
        }
        if($course_catid !== 0){
            $key_where['course_catid'] = $course_catid;
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
       // $condition['state']=1;
        return $this->field('id,name,institution_id,course_catid,course_sub_catid,course_time,single_time_price,pic')->where($condition)->page($page)->select();
    }

    public function updateState($data)
    {
        return $this->where('id='.$data['id'])->setField('state',$data['state']);
    }
    public function getAllField($id)
    {
        return $this->where('id='.$id)->field('*')->select();
    }

    public function get_course($institution_id){
        if(!$institution_id){
            return false;
        }
        $conditin['institution_id'] = $institution_id;
        //$conditin['state']=1;
        return $this->field('id,name')->where($conditin)->select();
    }

    public function getLastInserId(){
        return $this->getLastInsID();
    }
    public function get_course_list($course_id){
        if(!$course_id){
            return false;
        }
        return $this->field('id,name,imgs,remarks')->where('course_id='.$course_id)->select();
    }
    public function get_course_count($course_id){
        $where['course_id']=$course_id;
        return $this->where($where)->count();
    }
}