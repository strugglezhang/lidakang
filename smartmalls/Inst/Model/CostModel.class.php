<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/6/20
 * Time: 16:32
 */
namespace Inst\Model;
use Think\Model;
class ServeModel extends Model
{
    protected $tableName = 'service_charge';
    public function get_count(){
        return $this->count();
    }

    public function get_list( $keyword = null, $page = '1,10', $fields = null)
    {
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
        return $this->field('id,name,mall_id,service_catid,service_sub_catid,merchant_id,price,state')->where($condition)->page($page)->select();
    }

}