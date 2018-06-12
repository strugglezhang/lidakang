<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/6/16
 * Time: 14:37
 */
namespace AppParents\Model;
use Think\Model;
class CourseModel extends Model{
    protected $tableName='course';
    public function get_count($course_catid = 0,$fields = null){

        if($course_catid !== 0){
            $condition['course_catid'] = array('LIKE', '%' . $course_catid . '%');
        }
        //$condition['state']=1;
        return $this->where($condition)->count();

    }
    public function get_list($course_catid=0 ,$page = '1,10',$fields = null){

        if($course_catid !== 0){
            $condition['course_catid'] = array('LIKE', '%' . $course_catid . '%');
        }
        //$condition['state']=1;
        return $this->field('id,name,course_catid,institution_id,course_time,pic')->where($condition)->page($page)->select();
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
        return $this->where('id='.$id)->field('id,name,pic')->select();
    }

    public function get_course_number(){
        return $this->field('id,name')->select();

    }
    public function get_infos($id){
        if(!$id){
            return false;
        }
        return $this->field('id,name,institution_id,single_time_price,single_month_price,single_month_times,single_quarter_price,single_quarter_times')->where('id='.$id)->select();
    }
    public function get_by_list( $institution_id,$page = '1,10',$fields = null){
        if ($institution_id) {
            $condition['institution_id'] = $institution_id;
        }
        return $this->field('id,name,institution_id,course_time,pic')->where('institution_id='.$institution_id)->page($page)->select();
    }
    public function get_app_count($institution_id){
        if ($institution_id) {
            $condition['institution_id'] = $institution_id;
        }
        return $this->where($condition)->count();

    }

    public function updateState($data)
    {
        return $this->where('id='.$data['id'])->setField('state',$data['state']);
    }
    public function getAllField($id)
    {
        return $this->where('id='.$id)->field('*')->select();
    }

    public function get_info($course_id){
        if(!$course_id){
            return false;
        }
        return $this->field('name,pic')->where('id='.$course_id)->select();
    }


    public function get_pic($id){
        if(!$id){
            return false;
        }
        return $this->field('id,pic,name')->where('id='.$id)->select();
    }
    public function get_content($id){
        if(!$id){
            return false;
        }
        return $this->field('id,name,content,institution_id')->where('id='.$id)->select();
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
        return $this->field('id,credit_img,name')->where('id='.$id)->select();
    }

    public function get_time($course_id){
        if($course_id){
           return $this->field('start_time,end_time')->where('id='.$course_id)->select();
        }
    }
    public function get_card($id){
        if(!$id){
            return false;
        }
        return $this->field('id,name')->where('id='.$id)->select();
    }

}