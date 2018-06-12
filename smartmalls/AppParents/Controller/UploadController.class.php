<?php
namespace AppParents\Controller;
class UploadController extends CommonController {
    public function worker_pic_api(){
//        before_api();
//        checkLogin();
//        checkAuth();
        $member_id = I("member_id").'';
        $root = APP_ROOT;
        $path = '/Public/Uploads/Images/Member/';
        $rootPath = $root.$path;
        // echo json_encode(array('code' => 2,'info' => $rootPath));die;
        $config = array(
            'maxSize' => 3145728 ,// 设置附件上传大小
            'exts' => array('jpg','gif','png','jpeg'),// 设置附件上传类型
            'rootPath' => $rootPath, // 设置附件上传根目录
            'autoSub' => true,
            'subName' => $member_id,
            'savePath' => '',
        );
        $rootPath = $rootPath.$member_id.'/';
        $path = $path.$member_id.'/';
        $upload = new \Think\Upload($config);// 实例化上传类
        $info   =   $upload->uploadOne($_FILES['uploadfile']);
        if(!$info) {// 上传错误提示错误信息
            echo json_encode(array('code' => 2, 'info' => $upload->getError()));
        }else{// 上传成功
            $image = new \Think\Image();
            $res = $image->open($rootPath.$info['savename'])
                ->thumb(164,164,\Think\Image::IMAGE_THUMB_CENTER)
                ->save($rootPath.'thumb_'.$info['savename']);
            if ($res) {
                unlink($rootPath.$info['savename']);
            }
            echo json_encode(array('code' => 1, 'url' => $path.'thumb_'.$info['savename']));
        }
    }

}