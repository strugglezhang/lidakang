<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/7/7
 * Time: 10:28
 */
namespace Merchant\Model;
use Think\Model;
class ShopModel extends Model
{
    protected $tableName = 'shop';

    public function get_shop()
    {
        return $this->field('id,eqip')->select();
    }
    public function get_shop_cost($merchant_id)
    {
        if(!$merchant_id){
            return false;
        }
        return $this->field('id,property_price,rent_rate,position')->where('id='.$merchant_id)->select();
    }

    public function get_shop_by_id($id)
    {
        return $this->field('id,position')->where('id='.$id)->select();
    }
    //商铺绑定商户
    public function merchantBondShopIsOk($shopId,$ownerId,$ownerName)
    {
            $data['id']=$shopId;
            $data['owner_id']=$ownerId;
            $data['owner_name']=$ownerName;
            $data['ownertype_id']=2;
            $data['bond_state']=1;
            $data['owner_type']='商户';
            $this->save($data);
    }
    //商铺绑定商户
    public function merchantUnBondShopIsOk($shopId)
    {
        $data['id']=$shopId;
        $data['owner_id']='';
        $data['owner_name']='';
        $data['ownertype_id']='';
        $data['bond_state']=0;
        $data['owner_type']='';
        $this->save($data);
    }
}