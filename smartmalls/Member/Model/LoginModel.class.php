<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/6/7
 * Time: 16:05
 */
namespace Member\Model;
use Think\Model;
class LoginModel extends Model{

    protected $tableName='member';

    public function login($username,$password){
        $password = createPassword($password);
        return $this->where(array('number' => $username, 'password' => $password))->find();
    }
    public function get_info($id){
        if(!$id){
            return false;
        }
        return $this ->field('id,number,name,phone,parent_phone,pic,sex,id_number,birth_province_id,birth_city_id,birth_district_id,birth_address,address,marriage,health,education,major,school')->where('id='.$id)->select();

    }
    public function get_count($id,$keyword){
        $where['id'] = $id;

        if (!empty($keyword)) {
            $key_where['number'] = $keyword;
            $key_where['name'] = array('LIKE','%'.$keyword.'%');
            $key_where['_logic'] = 'OR';
            $where['_complex'] = $key_where;
        }
        return $this->where($where)->count();
    }
    public function get_list($id,$keyword,$page='1,10',$fields){
        if(!$id){
            return false;
        }
        if (!empty($keyword)) {
            $key_where['number'] = $keyword;
            $key_where['name'] = array('LIKE','%'.$keyword.'%');
            $key_where['_logic'] = 'OR';
            $where['_complex'] = $key_where;
        }
        return $this->field($fields)->where('id='.$id)->page($page)->select();
    }
    public function add_member($data){
        return $this->add($data);
    }
    public function update_member($data){
        return $this->save($data);
    }
    public function delete_member($id){
        return $this->where('id='.$id)->delete();
    }

    public function get_app_info($id){
        if(!$id){
            return false;
        }
        return $this->field('id,name,pic')->where('id='.$id)->select();
    }

}