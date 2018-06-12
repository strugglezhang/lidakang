<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/6/27
 * Time: 9:28
 */
namespace Mall\Model;
use Think\Model;
class ShopModel extends Model{
    protected $tableName='shop';
    public function add_shop($data,$condition){
        return $this->where($condition)->add($data);
    }
    public function update_shop($data){
        return $this->save($data);
    }
    public function view_shop($id){
        if(!$id){
            return false;
        }
        return $this->where('id='.$id)->select();
    }
    public function delete_shop($id){
         return $this->delete($id);
    }

    public function get_count($keyword = null){
        if (!empty($keyword)) {
            if(preg_match("/^\d*$/",$keyword)){
                $condition['id|position'] = $keyword;
            }
        }
        return $this->where($condition)->count();
    }

    public function get_list($keyword = null, $page = '1,10',$fields = null)
    {
        if (!empty($keyword)) {
            if(preg_match("/^\d*$/",$keyword)){
                $condition['id|position'] = $keyword;
            }
        }
         return $this->field('id,mall_id,state,number,position,type,eqmac,area,rent_rate,property_price,eqip,submitter,check_type')->where($condition)->page($page)->select();
    }
    public function add_log($data){
        return $this->add($data);
    }
    public function check($data){
        return $this->save($data);
    }
    public function updateState($data)
    {
        return $this->where('id='.$data['id'])->setField('state',$data['state']);
    }
    public function getAllField($id)
    {
        return $this->where('id='.$id)->field('*')->select();
    }
    public function getShopInfoByEqip($eqip)
    {
        //$m = new Model();
        //$sql="select * from shop where"
        return $this->where('eqip='.$eqip)->select();
    }
    public  function getShopInfoByPos($pos)
    {
        return $this->where('position='.$pos)->select();
    }
    public function updateShopEqInfo($eqId,$eqIp,$shopId)
    {
        $data['eqip']=$eqId;
        $data['equipment_ip']=$eqIp;
        $data['id']=$shopId;
        return $this->update_shop($data);
    }
    public function bondShopInfo($data)
    {
        //var_dump($data);die;
        return $this->update_shop($data);
    }
    //商铺绑定教室
    public function classBondShopIsOk($shopIdList,$ownerId,$ownerName)
    {
        $ids = explode("-",$shopIdList);
        foreach ($ids as $key=>$value)
        {
            $data['id']=$value;
            $data['owner_id']=$ownerId;
            $data['owner_name']=$ownerName;
            $data['ownertype_id']=3;
            $data['bond_state']=1;
            $data['owner_type']='教室';
            //var_dump($data);die;
            $this->save($data);
        }
    }
    //商铺绑定教室
    public function classUnBondShopIsOk($shopIdList)
    {
        $ids = explode("-",$shopIdList);
        foreach ($ids as $key=>$value)
        {
            $data['id']=$value;
            $data['owner_id']='';
            $data['owner_name']='';
            $data['ownertype_id']='';
            $data['bond_state']=0;
            $data['owner_type']='';
            $this->save($data);
        }
    }
}