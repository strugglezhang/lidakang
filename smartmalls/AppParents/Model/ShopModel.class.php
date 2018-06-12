<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/7/1
 * Time: 16:48
 */
namespace Inst\Model;
use Think\Model;
class ShopModel extends Model{
    protected $tableName='shop';
    public function get_shop_by_id($id){

         return $this->where('id='.$id)->field('id,position')->select();
//        echo $this->_sql();
    }
    public function get_shop(){
        return $this->field('id,eqip')->select();
    }
    public function get_shop_cost($merchant_id)
    {
        if(!$merchant_id){
            return false;
        }
        return $this->where('id='.$merchant_id)->select();
    }
    public function get_check_list($keyword = null, $page = '1,10'){
        $where['state'] = 1;
        if (!empty($keyword)) {
            if(is_numeric($keyword)){
                $where['id'] = $keyword;
            }else{
                $where['postion'] = array('LIKE','%'.$keyword.'%');
            }
        }

        return $this->where($where)->page($page)->select();
    }
    public function get_check_list_num($keyword = null)
    {
        $where['state'] = 1;
        if (!empty($keyword)) {
            if(is_numeric($keyword)){
                $where['id'] = $keyword;
            }else{
                $where['postion'] = array('LIKE','%'.$keyword.'%');
            }
        }

        return $this->where($where)->count();
    }
    public function get_list($keyword = null, $page = '1,10')
    {
        if (!empty($keyword)) {
            if(is_numeric($keyword)){
                $where['id'] = $keyword;
            }else{
                $where['postion'] = array('LIKE','%'.$keyword.'%');
            }
            return $this->where($where)->page($page)->select();
        }
        else
        {
            return $this->page($page)->select();
        }

    }
    public function get_list_num($keyword = null)
    {
        if (!empty($keyword)) {
            if(is_numeric($keyword)){
                $where['id'] = $keyword;
            }else{
                $where['postion'] = array('LIKE','%'.$keyword.'%');
            }
            return $this->where($where)->count();
        }
        else
        {
            return $this->count();
        }

    }
    //机构绑定商铺
    public function instBondShopIsOk($shopIdList,$ownerId,$ownerName)
    {
        $ids = explode("-",$shopIdList);
        foreach ($ids as $key=>$value)
        {
            $data['id']=$value;
            $data['owner_id']=$ownerId;
            $data['owner_name']=$ownerName;
            $data['ownertype_id']=1;
            $data['bond_state']=1;
            $data['owner_type']='机构';
            $this->save($data);
        }
    }

    //机构解绑商铺
    public function instUnBondShopIsOk($shopIdList)
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