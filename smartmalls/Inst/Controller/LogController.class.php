<?php
/**
 * Created by PhpStorm.
 * User: zhoujie
 * Date: 2017/7/22
 * Time: 21:50
 */
namespace Inst\Controller;
class LogController extends CommonController {
    public function index(){

    }
    /*
     * 机构操作日志
     */
    public function index_api(){
        before_api();
//        $state = I('state',0,'intval');
//        $is_manager = true;
//        if ($state !== 2 && !$is_manager) {
//            $state = 2;
//        }
        $keyword = I('keyword');
//        $category_id=I('category_id',0,'intval');
//        $condition['name'] = array('LIKE','%'.$keyword.'%');
//
//        $condition['name'] = array('LIKE','%'.$keyword.'%');
//
//        if (preg_match("/^\d*$/", $keyword)) {
//            $condition['id'] = $keyword;
//        } else {
//            $condition['name'] = array('LIKE', '%' . $keyword . '%');
//        }

        $page = I('page',1,'intval');
        $pagesize = I('pagesize',10,'intval');
        $pagesize = $pagesize < 1 ? 1 : $pagesize;
        $pagesize = $pagesize > 50 ? 50 : $pagesize;
        $page = $page.','.$pagesize;
        $instCheckLogModel=D('InstCheckLog');
        $count = $instCheckLogModel->get_count($keyword);
        $fields =array('id,check_time,institution,check_type,submitter,checker,check_state,pic');
        $data =$instCheckLogModel->get_list($keyword,$page,$fields);
        if($data){
            Ret(array('code'=>1,'data'=>$data,'total' => $count, 'page_count' => ceil($count/$pagesize)));
        }else{
            Ret(array('code'=>2,'data'=>'数据获取失败'));
        }

    }
    private function get_cat_by_id($id){
        if($id){
            $model=D('InstCat');
            return $model->get_cat($id);
        }
    }
}