<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/6/15
 * Time: 13:59
 */
namespace AppParents\Model;
use Think\Model;
class MemberModel extends Model{
    protected $tableName='member';
    public function getPwd($phone)
    {
        return $this->field('*')->where('phone='.$phone)->select();

    }
    public function updatePwdByPhone($phone,$newpwd)
    {
        $data = [
            'password' => $newpwd
        ];
        return $this->where('phone='.$phone)->data($data)->save();

    }



    public function get_member_info_by_id($member_id){
        if($member_id){
            return $this->field('name')->where('id='.$member_id)->select();
        }else{
            return false;
        }
    }
    public function get_member_infos($id,$fields){
        if($id){
            return $this->field($fields)->where('id='.$id)->select();
        }else{
            return false;
        }
    }

    public function get_count($mall_id, $state = 0, $keyword = null){
        $where['mall_id'] = $mall_id;
        if ($state !== 0) {
            $where['state'] = $state;
        }
        if (!empty($keyword)) {
            $key_where['id'] = $keyword;
            $key_where['name'] = array('LIKE','%'.$keyword.'%');
            $key_where['_logic'] = 'OR';
            $where['_complex'] = $key_where;
        }
        return $this->where($where)->count();
    }

    public function get_list($mall_id, $state = 0,$keyword = null, $page = '1,10',$fields = null){
        $where['mall_id'] = $mall_id;
        if ($state !== 0) {
            $where['state'] = $state;
        }
//        if (!empty($keyword)) {
//            if(isMobile($keyword)){
//                $where['phone'] = $keyword;
//            }
//            if(isCreditNo($keyword)){
//                $where['id_number'] = $keyword;
//            }
//            if(!is_numeric($keyword)){
//                $where['name'] = array('LIKE','%'.$keyword.'%');
//            }else{
//                $where['id'] = $keyword;
//            }
//        }
        if (!empty($keyword)) {
            $key_where['id'] = $keyword;
            $key_where['name'] = array('LIKE','%'.$keyword.'%');
            $key_where['_logic'] = 'OR';
            $where['_complex'] = $key_where;
        }
        if ($fields === null) {
            return $this->field('password',true)->where($where)->page($page)->select();
        }else{
            return $this->field($fields)->where($where)->page($page)->select();
        }
    }
    public function get_app_material($member_id){
        if($member_id==null){
            return false;
        }
        return $this->field('id,pic,name,sex,birthdate,health,address,weight,address,phone,school')->where('id='.$member_id)->select();
    }

    public function add_member($data){
        return $this->add($data);
    }
    public function update_member($data){
        return $this->save($data);
    }
    public function delete_member($id){
        return $this->delete($id);
    }



    public function get_app_parent($member_id)
    {
        if (!$member_id) {
            return false;
        }
        return $this->field('id,relations')->where('id='.$member_id)->select();
    }

//    public function get_count_card_lost($keyword){
//        if($keyword){
//            if(is_numeric($keyword)){
//                $key['id'] = $keyword;
//                $key['phone'] = $keyword;
//                $key['_logic'] = 'OR';
//                $condition['_complex'] = $key;
//            }elseif(!is_numeric($keyword)){
//                $condition['name'] = array('LIKE','%'.$keyword.'%');
//            }
//            return $this->where($condition)->count();
//        }
//    }

//    public function check_member_card($pid){
//        if($pid){
//            $condition['id']=$pid;
//            $condition['cardState']=array('in','0,1');
//            return $this->where($condition)->select();
//        }
//    }

//    public function get_list_card_lost($keyword,$page,$fields= null){
//        if($keyword){
//            if(is_numeric($keyword)){
//                $key['id'] = $keyword;
//                $key['phone'] = $keyword;
//                $key['_logic'] = 'OR';
//                $condition['_complex'] = $key;
//            }elseif(!is_numeric($keyword)){
//                $condition['name'] = array('LIKE','%'.$keyword.'%');
//            }
//            return $this->where($condition)->page($page)->field($fields)->select();
//        }
//    }
//    public function app_update($member_id){
//        if($member_id){
//            $condition['member_id']= $member_id;
//        }
//        return $this->save($member_id);
//
//    }

    public function get_balance($member_id){
        if($member_id){
            return $this->field('id,balance')->where('id='.$member_id)->select();
        }
    }
}