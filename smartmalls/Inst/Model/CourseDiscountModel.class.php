<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/7/3
 * Time: 21:06
 */
namespace Inst\Model;
use Think\Model;
class CourseDiscountModel extends Model{
    protected $tableName='course_discount';

    public function get_count( $institution_id = 0,$category_id,$keyword)
    {

        if ($institution_id !== 0) {
            $condition['mall_id'] = $institution_id;
        }
            if (!empty($category_id)) {
            $condition['category_id'] = $category_id;
        }
        if (!empty($keyword)) {

            if (preg_match("/^\d*$/", $keyword)) {
                $condition['id'] = $keyword;
            } else {
                $key_where['name'] = $keyword;
                $key_where['name'] = array('LIKE', '%' . $keyword . '%');
                $key_where['_logic'] = 'OR';
                $condition['_complex'] = $key_where;
            }
        }
        return $this->where($condition)->count();

    }
    public function get_list($institution_id = 0,$category_id=0,$keyword,$page = '1,10', $fields = null)
    {

        if ($institution_id !== 0) {
            $condition['mall_id'] = $institution_id;
        }
        if (!empty($category_id)) {
            $condition['category_id'] = $category_id;
        }
        if (!empty($keyword)) {

            if (preg_match("/^\d*$/", $keyword)) {
                $condition['id'] = $keyword;
            } else {
                $key_where['name'] = $keyword;
                $key_where['name'] = array('LIKE', '%' . $keyword . '%');
                $key_where['_logic'] = 'OR';
                $condition['_complex'] = $key_where;
            }
        }

        return $this->field('id,institution_id,category_id,number,price_type,start_time,end_time,discount,state,submitter,check_type')->where($condition)->page($page)->select();
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
        return $this->where('id='.$data['id'])->save($data);
    }
    public function del_course($id){
        if(!$id){
            return false;
        }
        return $this->where('id='.$id)->delete($id);
    }

        public function get_course_infos($id){
            if(!$id){
                return false;
            }
            return $this->field('id,type,category_id,number,price_type,start_time,end_time,discount,state')
                ->where('id='.$id)->select();
        }

    public function updateState($data)
    {
        return $this->where('id='.$data['id'])->setField('state',$data['state']);
    }
    public function getAllField($id)
    {
        return $this->where('id='.$id)->field('*')->select();
    }

}