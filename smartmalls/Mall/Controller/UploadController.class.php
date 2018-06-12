<?php
namespace Mall\Controller;

class UploadController extends CommonController {
    public function mallEplyPic(){
        before_api();

        checkLogin();

        checkAuth();

        $mall_id = session('mall.mall_id').'';
        $root = APP_ROOT;
        $path = '/Public/Uploads/Images/Mall/';
        $rootPath = $root.$path;
        // echo json_encode(array('code' => 2,'info' => $rootPath));die;
        $config = array(
            'maxSize' => 3145728 ,// 设置附件上传大小
            'exts' => array('jpg','gif','png','jpeg'),// 设置附件上传类型
            'rootPath' => $rootPath, // 设置附件上传根目录
            'autoSub' => true,
            'subName' => $mall_id,
            'saveName' => array('uniqid',''),
            'savePath' => '',
        );
        $rootPath = $rootPath.$mall_id.'/';
        $path = $path.$mall_id.'/'.'/';
        $upload = new \Think\Upload($config);// 实例化上传类
        $info   =   $upload->upload();
        $fileNum = count($info);
        if(!$info) {// 上传错误提示错误信息
            echo json_encode(array('code' => 2, 'info' => $upload->getError()));
        }else{// 上传成功
            if($fileNum == 1){
                $info = $info['uploadfile'];
                $image = new \Think\Image();
                $res = $image->open($rootPath.$info['savename'])
                    ->thumb(164,164,\Think\Image::IMAGE_THUMB_CENTER)
                    ->save($rootPath.'thumb_'.$info['savename']);
                if ($res) {
                    unlink($rootPath.$info['savename']);
                }
                echo json_encode(array('code' => 1, 'url' => $path.'thumb_'.$info['savename']));
            } else {
                $url = array();
                foreach($info as $key => $val){
                    $image = new \Think\Image();
                    $res = $image->open($rootPath.$val['savename'])
                        ->thumb(164,164, \Think\Image::IMAGE_THUMB_CENTER)
                        ->save($rootPath.'thumb_'.$val['savename']);
                    if ($res) {
                        unlink($rootPath.$info['savename']);
                    }
                    $url[$key] = $path.'thumb_'.$val['savename'];
                }
                echo json_encode(array('code' => 1, 'url' => $url));
            }
        }
    }
	
	public function worker_pic_api(){
        before_api();

        checkLogin();

        checkAuth();
        $mall_id = session('mall.mall_id').'';
        $root = APP_ROOT;
        $path = '/Public/Uploads/Images/Mall/';
        $rootPath = $root.$path;
        // echo json_encode(array('code' => 2,'info' => $rootPath));die;
        $config = array(
            'maxSize' => 3145728 ,// 设置附件上传大小
            'exts' => array('jpg','gif','png','jpeg'),// 设置附件上传类型
            'rootPath' => $rootPath, // 设置附件上传根目录
            'autoSub' => true,
            'subName' => $mall_id,
            'saveName' => array('uniqid',''),
            'savePath' => '',
        );
        $rootPath = $rootPath.$mall_id.'/';
        $path = $path.$mall_id.'/';
        $upload = new \Think\Upload($config);// 实例化上传类
        $info   =   $upload->upload();
        $fileNum = count($info);
        if(!$info) {// 上传错误提示错误信息
            echo json_encode(array('code' => 2, 'info' => $upload->getError()));
        }else{// 上传成功
            if($fileNum == 1){
                $info = $info['uploadfile'];
                $image = new \Think\Image();
                $res = $image->open($rootPath.$info['savename'])
                    ->thumb(164,164,\Think\Image::IMAGE_THUMB_CENTER)
                    ->save($rootPath.'thumb_'.$info['savename']);
                if ($res) {
                    unlink($rootPath.$info['savename']);
                }
                echo json_encode(array('code' => 1, 'url' => $path.'thumb_'.$info['savename']));
            } else {
                $url = array();
                foreach($info as $key => $val){
                    $image = new \Think\Image();
                    $res = $image->open($rootPath.$val['savename'])
                        ->thumb(164,164, \Think\Image::IMAGE_THUMB_CENTER)
                        ->save($rootPath.'thumb_'.$val['savename']);
                    if ($res) {
                        unlink($rootPath.$info['savename']);
                    }
                    $url[$key] = $path.'thumb_'.$val['savename'];
                }
                echo json_encode(array('code' => 1, 'url' => $url));
            }
        }
    }

    public function memberPic(){
        before_api();
        checkLogin();

        checkAuth();

        $mall_id = session('mall.mall_id').'';
        $root = APP_ROOT;
        $path = '/Public/Uploads/Images/Member/';
        $rootPath = $root.$path;
        // echo json_encode(array('code' => 2,'info' => $rootPath));die;
        $config = array(
            'maxSize' => 3145728 ,// 设置附件上传大小
            'exts' => array('jpg','gif','png','jpeg'),// 设置附件上传类型
            'rootPath' => $rootPath, // 设置附件上传根目录
            'autoSub' => true,
            'subName' => $mall_id,
            'saveName' => array('uniqid',''),
            'savePath' => '',
        );
        $rootPath = $rootPath.$mall_id.'/';
        $path = $path.$mall_id.'/';
        $upload = new \Think\Upload($config);// 实例化上传类
        $info   =   $upload->upload();
        $fileNum = count($info);
        if(!$info) {// 上传错误提示错误信息
            echo json_encode(array('code' => 2, 'info' => $upload->getError()));
        }else{// 上传成功
            if($fileNum == 1){
                $info = $info['uploadfile'];
                $image = new \Think\Image();
                $res = $image->open($rootPath.$info['savename'])
                    ->thumb(164,164,\Think\Image::IMAGE_THUMB_CENTER)
                    ->save($rootPath.'thumb_'.$info['savename']);
                if ($res) {
                    unlink($rootPath.$info['savename']);
                }
                echo json_encode(array('code' => 1, 'url' => $path.'thumb_'.$info['savename']));
            } else {
                $url = array();
                foreach($info as $key => $val){
                    $image = new \Think\Image();
                    $res = $image->open($rootPath.$val['savename'])
                        ->thumb(164,164, \Think\Image::IMAGE_THUMB_CENTER)
                        ->save($rootPath.'thumb_'.$val['savename']);
                    if ($res) {
                        unlink($rootPath.$info['savename']);
                    }
                    $url[$key] = $path.'thumb_'.$val['savename'];
                }
                echo json_encode(array('code' => 1, 'url' => $url));
            }
        }
    }
    public function memberPPic(){
        before_api();
        checkLogin();

        checkAuth();

        $mall_id = session('mall.mall_id').'';
        $root = APP_ROOT;
        $path = '/Public/Uploads/Images/Member/';
        $rootPath = $root.$path;
        // echo json_encode(array('code' => 2,'info' => $rootPath));die;
        $config = array(
            'maxSize' => 3145728 ,// 设置附件上传大小
            'exts' => array('jpg','gif','png','jpeg'),// 设置附件上传类型
            'rootPath' => $rootPath, // 设置附件上传根目录
            'autoSub' => true,
            'subName' => $mall_id,
            'saveName' => array('uniqid',''),
            'savePath' => '',
        );
        $rootPath = $rootPath.$mall_id.'/';
        $path = $path.$mall_id.'/';
        $upload = new \Think\Upload($config);// 实例化上传类
        $info   =   $upload->upload();
        $fileNum = count($info);
        if(!$info) {// 上传错误提示错误信息
            echo json_encode(array('code' => 2, 'info' => $upload->getError()));
        }else{// 上传成功
            if($fileNum == 1){
                $info = $info['uploadfile'];
                $image = new \Think\Image();
                $res = $image->open($rootPath.$info['savename'])
                    ->thumb(164,164,\Think\Image::IMAGE_THUMB_CENTER)
                    ->save($rootPath.'thumb_'.$info['savename']);
                if ($res) {
                    unlink($rootPath.$info['savename']);
                }
                echo json_encode(array('code' => 1, 'url' => $path.'thumb_'.$info['savename']));
            } else {
                $url = array();
                foreach($info as $key => $val){
                    $image = new \Think\Image();
                    $res = $image->open($rootPath.$val['savename'])
                        ->thumb(164,164, \Think\Image::IMAGE_THUMB_CENTER)
                        ->save($rootPath.'thumb_'.$val['savename']);
                    if ($res) {
                        unlink($rootPath.$info['savename']);
                    }
                    $url[$key] = $path.'thumb_'.$val['savename'];
                }
                echo json_encode(array('code' => 1, 'url' => $url));
            }
        }
    }
//    public function instEplyPic(){
//        before_api();
//        checkLogin();
//
//        checkAuth();
//
//        $mall_id = session('inst.institution_id').'';
//        $rootPath = APP_ROOT.'/Public/Uploads/Images/Inst/'.$mall_id.'/';
//        $info = uploadFiles('Inst');
//        $fileNum = count($info);
//        if(!$info) {// 上传错误提示错误信息
//            echo json_encode(array('code' => 2, 'info' => $info->getError()));
//        }else{// 上传成功
//            if($fileNum == 1){
//                $info = $info['uploadfile'];
//                $image = new \Think\Image();
//                $res = $image->open($rootPath.$info['savename'])
//                    ->thumb(164,164,\Think\Image::IMAGE_THUMB_CENTER)
//                    ->save($rootPath.'thumb_'.$info['savename']);
//                if ($res) {
//                    unlink($rootPath.$info['savename']);
//                }
//                echo json_encode(array('code' => 1, 'url' => $rootPath.'thumb_'.$info['savename']));
//            } else {
//                $url = array();
//                foreach($info as $key => $val){
//                    $image = new \Think\Image();
//                    $res = $image->open($rootPath.$val['savename'])
//                        ->thumb(164,164, \Think\Image::IMAGE_THUMB_CENTER)
//                        ->save($rootPath.'thumb_'.$val['savename']);
//                    if ($res) {
//                        unlink($rootPath.$info['savename']);
//                    }
//                    $url[$key] = $rootPath.'thumb_'.$val['savename'];
//                }
//                echo json_encode(array('code' => 1, 'url' => $url));
//            }
//        }
//    }

    public function instCertPic(){
        before_api();
        checkLogin();

        checkAuth();
//        $mall_id =session('inst.institution_id');
//        $mall_id = session('mall.mall_id').'';

        $mall_id = session('mall.mall_id').'';
        $root = APP_ROOT;
        $path = '/Public/Uploads/Images/Mall/';
        $rootPath = $root.$path;
        // echo json_encode(array('code' => 2,'info' => $rootPath));die;
        $config = array(
            'maxSize' => 3145728 ,// 设置附件上传大小
            'exts' => array('jpg','gif','png','jpeg'),// 设置附件上传类型
            'rootPath' => $rootPath, // 设置附件上传根目录
            'autoSub' => true,
            'subName' => $mall_id,
            'saveName' => array('uniqid',''),
            'savePath' => '',
        );
        $rootPath = $rootPath.$mall_id.'/';
        $path = $path.$mall_id.'/'.'/';
        $upload = new \Think\Upload($config);// 实例化上传类
        $info   =   $upload->upload();
        $fileNum = count($info);
        if(!$info) {// 上传错误提示错误信息
            echo json_encode(array('code' => 2, 'info' => $upload->getError()));
        }else{// 上传成功
            if($fileNum == 1){
                $info = $info['uploadfile'];
                $image = new \Think\Image();
                $res = $image->open($rootPath.$info['savename'])
                    ->thumb(164,164,\Think\Image::IMAGE_THUMB_CENTER)
                    ->save($rootPath.'thumb_'.$info['savename']);
                if ($res) {
                    unlink($rootPath.$info['savename']);
                }
                echo json_encode(array('code' => 1, 'url' => $path.'thumb_'.$info['savename']));
            } else {
                $url = array();
                foreach($info as $key => $val){
                    $image = new \Think\Image();
                    $res = $image->open($rootPath.$val['savename'])
                        ->thumb(164,164, \Think\Image::IMAGE_THUMB_CENTER)
                        ->save($rootPath.'thumb_'.$val['savename']);
                    if ($res) {
                        unlink($rootPath.$info['savename']);
                    }
                    $url[$key] = $path.'thumb_'.$val['savename'];
                }
                echo json_encode(array('code' => 1, 'url' => $url));
            }
        }
    }

    public function instIdPic(){
        before_api();
        checkLogin();
        checkAuth();
        //$mall_id = session('inst.institution_id').'';
//        $mall_id = date('Y-m-d',time());
        $mall_id =session('mall.mall_id').'';
        $mall_id = session('mall.mall_id').'';
        $root = APP_ROOT;
        $path = '/Public/Uploads/Images/Mall/';
        $rootPath = $root.$path;
        // echo json_encode(array('code' => 2,'info' => $rootPath));die;
        $config = array(
            'maxSize' => 3145728 ,// 设置附件上传大小
            'exts' => array('jpg','gif','png','jpeg'),// 设置附件上传类型
            'rootPath' => $rootPath, // 设置附件上传根目录
            'autoSub' => true,
            'subName' => $mall_id,
            'saveName' => array('uniqid',''),
            'savePath' => '',
        );
        $rootPath = $rootPath.$mall_id.'/';
        $path = $path.$mall_id.'/'.'/';
        $upload = new \Think\Upload($config);// 实例化上传类
        $info   =   $upload->upload();
        $fileNum = count($info);
        if(!$info) {// 上传错误提示错误信息
            echo json_encode(array('code' => 2, 'info' => $upload->getError()));
        }else{// 上传成功
            if($fileNum == 1){
                $info = $info['uploadfile'];
                $image = new \Think\Image();
                $res = $image->open($rootPath.$info['savename'])
                    ->thumb(164,164,\Think\Image::IMAGE_THUMB_CENTER)
                    ->save($rootPath.'thumb_'.$info['savename']);
                if ($res) {
                    unlink($rootPath.$info['savename']);
                }
                echo json_encode(array('code' => 1, 'url' => $path.'thumb_'.$info['savename']));
            } else {
                $url = array();
                foreach($info as $key => $val){
                    $image = new \Think\Image();
                    $res = $image->open($rootPath.$val['savename'])
                        ->thumb(164,164, \Think\Image::IMAGE_THUMB_CENTER)
                        ->save($rootPath.'thumb_'.$val['savename']);
                    if ($res) {
                        unlink($rootPath.$info['savename']);
                    }
                    $url[$key] = $path.'thumb_'.$val['savename'];
                }
                echo json_encode(array('code' => 1, 'url' => $url));
            }
        }
    }

    public function instIndPic(){
        before_api();
        checkLogin();

        checkAuth();
        $mall_id = session('inst.institution_id').'';
        $mall_id = session('mall.mall_id').'';
        $root = APP_ROOT;
        $path = '/Public/Uploads/Images/InstInd/';
        $rootPath = $root.$path;
        // echo json_encode(array('code' => 2,'info' => $rootPath));die;
        $config = array(
            'maxSize' => 3145728 ,// 设置附件上传大小
            'exts' => array('jpg','gif','png','jpeg'),// 设置附件上传类型
            'rootPath' => $rootPath, // 设置附件上传根目录
            'autoSub' => true,
            'subName' => $mall_id,
            'saveName' => array('uniqid',''),
            'savePath' => '',
        );
        $rootPath = $rootPath.$mall_id.'/';
        $path = $path.$mall_id.'/'.'/';
        $upload = new \Think\Upload($config);// 实例化上传类
        $info   =   $upload->upload();
        $fileNum = count($info);
        if(!$info) {// 上传错误提示错误信息
            echo json_encode(array('code' => 2, 'info' => $upload->getError()));
        }else{// 上传成功
            if($fileNum == 1){
                $info = $info[0];
                $image = new \Think\Image();
                $res = $image->open($rootPath.$info['savename'])
                    ->thumb(164,164,\Think\Image::IMAGE_THUMB_CENTER)
                    ->save($rootPath.'thumb_'.$info['savename']);
                if ($res) {
                    unlink($rootPath.$info['savename']);
                }
                echo json_encode(array('code' => 1, 'url' => $path.'thumb_'.$info['savename']));
            } else {
                $url = array();
                foreach($info as $key => $val){
                    $image = new \Think\Image();
                    $res = $image->open($rootPath.$val['savename'])
                        ->thumb(164,164, \Think\Image::IMAGE_THUMB_CENTER)
                        ->save($rootPath.'thumb_'.$val['savename']);
                    if ($res) {
                        unlink($rootPath.$info['savename']);
                    }
                    $url[$key] = $path.'thumb_'.$val['savename'];
                }
                echo json_encode(array('code' => 1, 'url' => $url));
            }
        }
    }

    public function instPic(){
        before_api();
        checkLogin();
        checkAuth();
        $mall_id = session('inst.institution_id').'';
        $mall_id = session('mall.mall_id').'';
        $root = APP_ROOT;
        $path = '/Public/Uploads/Images/InstInd/';
        $rootPath = $root.$path;
        // echo json_encode(array('code' => 2,'info' => $rootPath));die;
        $config = array(
            'maxSize' => 3145728 ,// 设置附件上传大小
            'exts' => array('jpg','gif','png','jpeg'),// 设置附件上传类型
            'rootPath' => $rootPath, // 设置附件上传根目录
            'autoSub' => true,
            'subName' => $mall_id,
            'saveName' => array('uniqid',''),
            'savePath' => '',
        );
        $rootPath = $rootPath.$mall_id.'/';
        $path = $path.$mall_id.'/'.'/';
        $upload = new \Think\Upload($config);// 实例化上传类
        $info   =   $upload->upload();
        $fileNum = count($info);
        if(!$info) {// 上传错误提示错误信息
            echo json_encode(array('code' => 2, 'info' => $upload->getError()));
        }else{// 上传成功
            if($fileNum == 1){
                $info = $info[0];
                $image = new \Think\Image();
                $res = $image->open($rootPath.$info['savename'])
                    ->thumb(164,164,\Think\Image::IMAGE_THUMB_CENTER)
                    ->save($rootPath.'thumb_'.$info['savename']);
                if ($res) {
                    unlink($rootPath.$info['savename']);
                }
                echo json_encode(array('code' => 1, 'url' => $path.'thumb_'.$info['savename']));
            } else {
                $url = array();
                foreach($info as $key => $val){
                    $image = new \Think\Image();
                    $res = $image->open($rootPath.$val['savename'])
                        ->thumb(164,164, \Think\Image::IMAGE_THUMB_CENTER)
                        ->save($rootPath.'thumb_'.$val['savename']);
                    if ($res) {
                        unlink($rootPath.$info['savename']);
                    }
                    $url[$key] = $path.'thumb_'.$val['savename'];
                }
                echo json_encode(array('code' => 1, 'url' => $url));
            }
        }
    }

    public function instCosPic(){
        before_api();
        checkLogin();
        checkAuth();
        $mall_id = session('mall.mall_id').'';
        $root = APP_ROOT;
        $path = '/Public/Uploads/Images/Mall/';
        $rootPath = $root.$path;
        // echo json_encode(array('code' => 2,'info' => $rootPath));die;
        $config = array(
            'maxSize' => 3145728 ,// 设置附件上传大小
            'exts' => array('jpg','gif','png','jpeg'),// 设置附件上传类型
            'rootPath' => $rootPath, // 设置附件上传根目录
            'autoSub' => true,
            'subName' => $mall_id,
            'saveName' => array('uniqid',''),
            'savePath' => '',
        );
        $rootPath = $rootPath.$mall_id.'/';
        $path = $path.$mall_id.'/'.'/';
        $upload = new \Think\Upload($config);// 实例化上传类
        $info   =   $upload->upload();
        $fileNum = count($info);
        if(!$info) {// 上传错误提示错误信息
            echo json_encode(array('code' => 2, 'info' => $upload->getError()));
        }else{// 上传成功
            if($fileNum == 1){
                $info = $info['uploadfile'];
                $image = new \Think\Image();
                $res = $image->open($rootPath.$info['savename'])
                    ->thumb(164,164,\Think\Image::IMAGE_THUMB_CENTER)
                    ->save($rootPath.'thumb_'.$info['savename']);
                if ($res) {
                    unlink($rootPath.$info['savename']);
                }
                echo json_encode(array('code' => 1, 'url' => $path.'thumb_'.$info['savename']));
            } else {
                $url = array();
                foreach($info as $key => $val){
                    $image = new \Think\Image();
                    $res = $image->open($rootPath.$val['savename'])
                        ->thumb(164,164, \Think\Image::IMAGE_THUMB_CENTER)
                        ->save($rootPath.'thumb_'.$val['savename']);
                    if ($res) {
                        unlink($rootPath.$info['savename']);
                    }
                    $url[$key] = $path.'thumb_'.$val['savename'];
                }
                echo json_encode(array('code' => 1, 'url' => $url));
            }
        }
    }

    public function instCosWardPic(){
        before_api();
        checkLogin();

        checkAuth();

        $mall_id = session('inst.institution_id').'';
        $root = APP_ROOT;
        $path = '/Public/Uploads/Images/InstCos';
        $rootPath = $root.$path;
        // echo json_encode(array('code' => 2,'info' => $rootPath));die;
        $config = array(
            'maxSize' => 3145728 ,// 设置附件上传大小
            'exts' => array('jpg','gif','png','jpeg'),// 设置附件上传类型
            'rootPath' => $rootPath, // 设置附件上传根目录
            'autoSub' => true,
            'subName' => $mall_id,
            'saveName' => array('uniqid',''),
            'savePath' => '',
        );
        $rootPath = $rootPath.$mall_id.'/';
        $path = $path.$mall_id.'/';
        $upload = new \Think\Upload($config);// 实例化上传类
        $info   =   $upload->upload();
        $fileNum = count($info);
        if(!$info) {// 上传错误提示错误信息
            echo json_encode(array('code' => 2, 'info' => $upload->getError()));
        }else{// 上传成功
            if($fileNum == 1){
                $info = $info['uploadfile'];
                $image = new \Think\Image();
                $res = $image->open($rootPath.$info['savename'])
                    ->thumb(164,164,\Think\Image::IMAGE_THUMB_CENTER)
                    ->save($rootPath.'thumb_'.$info['savename']);
                if ($res) {
                    unlink($rootPath.$info['savename']);
                }
                echo json_encode(array('code' => 1, 'url' => $path.'thumb_'.$info['savename']));
            } else {
                $url = array();
                foreach($info as $key => $val){
                    $image = new \Think\Image();
                    $res = $image->open($rootPath.$val['savename'])
                        ->thumb(164,164, \Think\Image::IMAGE_THUMB_CENTER)
                        ->save($rootPath.'thumb_'.$val['savename']);
                    if ($res) {
                        unlink($rootPath.$info['savename']);
                    }
                    $url[$key] = $path.'thumb_'.$val['savename'];
                }
                echo json_encode(array('code' => 1, 'url' => $url));
            }
        }
    }

    public function merEplyPic(){
        before_api();
        checkLogin();

        checkAuth();

        $mall_id = session('mall.mall_id').'';
        $root = APP_ROOT;
        $path = '/Public/Uploads/Images/MerInd/';
        $rootPath = $root.$path;
        // echo json_encode(array('code' => 2,'info' => $rootPath));die;
        $config = array(
            'maxSize' => 3145728 ,// 设置附件上传大小
            'exts' => array('jpg','gif','png','jpeg'),// 设置附件上传类型
            'rootPath' => $rootPath, // 设置附件上传根目录
            'autoSub' => true,
            'subName' => $mall_id,
            'saveName' => array('uniqid',''),
            'savePath' => '',
        );
        $rootPath = $rootPath.$mall_id.'/';
        $path = $path.$mall_id.'/'.'/';
        $upload = new \Think\Upload($config);// 实例化上传类
        $info   =   $upload->upload();
        $fileNum = count($info);
        if(!$info) {// 上传错误提示错误信息
            echo json_encode(array('code' => 2, 'info' => $upload->getError()));
        }else{// 上传成功
            if($fileNum == 1){
                $info = $info['uploadfile'];
                $image = new \Think\Image();
                $res = $image->open($rootPath.$info['savename'])
                    ->thumb(164,164,\Think\Image::IMAGE_THUMB_CENTER)
                    ->save($rootPath.'thumb_'.$info['savename']);
                if ($res) {
                    unlink($rootPath.$info['savename']);
                }
                echo json_encode(array('code' => 1, 'url' => $path.'thumb_'.$info['savename']));
            } else {
                $url = array();
                foreach($info as $key => $val){
                    $image = new \Think\Image();
                    $res = $image->open($rootPath.$val['savename'])
                        ->thumb(164,164, \Think\Image::IMAGE_THUMB_CENTER)
                        ->save($rootPath.'thumb_'.$val['savename']);
                    if ($res) {
                        unlink($rootPath.$info['savename']);
                    }
                    $url[$key] = $path.'thumb_'.$val['savename'];
                }
                echo json_encode(array('code' => 1, 'url' => $url));
            }
        }
    }
    public function merCertPic(){
        before_api();
        checkLogin();

        checkAuth();

//        $mall_id = session('inst.institution_id').'';
        $mall_id = session('mall.mall_id').'';
        $root = APP_ROOT;
        $path = '/Public/Uploads/Images/MerInd/';
        $rootPath = $root.$path;
        // echo json_encode(array('code' => 2,'info' => $rootPath));die;
        $config = array(
            'maxSize' => 3145728 ,// 设置附件上传大小
            'exts' => array('jpg','gif','png','jpeg'),// 设置附件上传类型
            'rootPath' => $rootPath, // 设置附件上传根目录
            'autoSub' => true,
            'subName' => $mall_id,
            'saveName' => array('uniqid',''),
            'savePath' => '',
        );
        $rootPath = $rootPath.$mall_id.'/';
        $path = $path.$mall_id.'/'.'/';
        $upload = new \Think\Upload($config);// 实例化上传类
        $info   =   $upload->upload();
        $fileNum = count($info);
        if(!$info) {// 上传错误提示错误信息
            echo json_encode(array('code' => 2, 'info' => $upload->getError()));
        }else{// 上传成功
            if($fileNum == 1){
                $info = $info['uploadfile'];
                $image = new \Think\Image();
                $res = $image->open($rootPath.$info['savename'])
                    ->thumb(164,164,\Think\Image::IMAGE_THUMB_CENTER)
                    ->save($rootPath.'thumb_'.$info['savename']);
                if ($res) {
                    unlink($rootPath.$info['savename']);
                }
                echo json_encode(array('code' => 1, 'url' => $path.'thumb_'.$info['savename']));
            } else {
                $url = array();
                foreach($info as $key => $val){
                    $image = new \Think\Image();
                    $res = $image->open($rootPath.$val['savename'])
                        ->thumb(164,164, \Think\Image::IMAGE_THUMB_CENTER)
                        ->save($rootPath.'thumb_'.$val['savename']);
                    if ($res) {
                        unlink($rootPath.$info['savename']);
                    }
                    $url[$key] = $path.'thumb_'.$val['savename'];
                }
                echo json_encode(array('code' => 1, 'url' => $url));
            }
        }
    }

    /**
     * 商户身份证
     */
    public function merIdPic(){
        before_api();
        checkLogin();

        checkAuth();

//        $mall_id = session('inst.institution_id').'';
        $mall_id = session('mall.mall_id').'';
        $root = APP_ROOT;
        $path = '/Public/Uploads/Images/MerInd/';
        $rootPath = $root.$path;
        // echo json_encode(array('code' => 2,'info' => $rootPath));die;
        $config = array(
            'maxSize' => 3145728 ,// 设置附件上传大小
            'exts' => array('jpg','gif','png','jpeg'),// 设置附件上传类型
            'rootPath' => $rootPath, // 设置附件上传根目录
            'autoSub' => true,
            'subName' => $mall_id,
            'saveName' => array('uniqid',''),
            'savePath' => '',
        );
        $rootPath = $rootPath.$mall_id.'/';
        $path = $path.$mall_id.'/'.'/';
        $upload = new \Think\Upload($config);// 实例化上传类
        $info   =   $upload->upload();
        $fileNum = count($info);
        if(!$info) {// 上传错误提示错误信息
            echo json_encode(array('code' => 2, 'info' => $upload->getError()));
        }else{// 上传成功
            if($fileNum == 1){
                $info = $info['uploadfile'];
                $image = new \Think\Image();
                $res = $image->open($rootPath.$info['savename'])
                    ->thumb(164,164,\Think\Image::IMAGE_THUMB_CENTER)
                    ->save($rootPath.'thumb_'.$info['savename']);
                if ($res) {
                    unlink($rootPath.$info['savename']);
                }
                echo json_encode(array('code' => 1, 'url' => $path.'thumb_'.$info['savename']));
            } else {
                $url = array();
                foreach($info as $key => $val){
                    $image = new \Think\Image();
                    $res = $image->open($rootPath.$val['savename'])
                        ->thumb(164,164, \Think\Image::IMAGE_THUMB_CENTER)
                        ->save($rootPath.'thumb_'.$val['savename']);
                    if ($res) {
                        unlink($rootPath.$info['savename']);
                    }
                    $url[$key] = $path.'thumb_'.$val['savename'];
                }
                echo json_encode(array('code' => 1, 'url' => $url));
            }
        }
    }

    public function merIndPic(){
        before_api();
        checkLogin();

        checkAuth();

        $mall_id = session('inst.institution_id').'';
        $mall_id = session('mall.mall_id').'';
        $root = APP_ROOT;
        $path = '/Public/Uploads/Images/Merchant/';
        $rootPath = $root.$path;
        // echo json_encode(array('code' => 2,'info' => $rootPath));die;
        $config = array(
            'maxSize' => 3145728 ,// 设置附件上传大小
            'exts' => array('jpg','gif','png','jpeg'),// 设置附件上传类型
            'rootPath' => $rootPath, // 设置附件上传根目录
            'autoSub' => true,
            'subName' => $mall_id,
            'saveName' => array('uniqid',''),
            'savePath' => '',
        );
        $rootPath = $rootPath.$mall_id.'/';
        $path = $path.$mall_id.'/'.'/';
        $upload = new \Think\Upload($config);// 实例化上传类
        $info   =   $upload->upload();
        $fileNum = count($info);
        if(!$info) {// 上传错误提示错误信息
            echo json_encode(array('code' => 2, 'info' => $upload->getError()));
        }else{// 上传成功
            if($fileNum == 1){
                $info = $info[0];
                $image = new \Think\Image();
                $res = $image->open($rootPath.$info['savename'])
                    ->thumb(164,164,\Think\Image::IMAGE_THUMB_CENTER)
                    ->save($rootPath.'thumb_'.$info['savename']);
                if ($res) {
                    unlink($rootPath.$info['savename']);
                }
                echo json_encode(array('code' => 1, 'url' => $path.'thumb_'.$info['savename']));
            } else {
                $url = array();
                foreach($info as $key => $val){
                    $image = new \Think\Image();
                    $res = $image->open($rootPath.$val['savename'])
                        ->thumb(164,164, \Think\Image::IMAGE_THUMB_CENTER)
                        ->save($rootPath.'thumb_'.$val['savename']);
                    if ($res) {
                        unlink($rootPath.$info['savename']);
                    }
                    $url[$key] = $path.'thumb_'.$val['savename'];
                }
                echo json_encode(array('code' => 1, 'url' => $url));
            }
        }
    }


    public function merPic(){
        before_api();
        checkLogin();

        checkAuth();

        $mall_id = session('inst.institution_id').'';
        $mall_id = session('mall.mall_id').'';
        $root = APP_ROOT;
        $path = '/Public/Uploads/Images/Merchant/';
        $rootPath = $root.$path;
        // echo json_encode(array('code' => 2,'info' => $rootPath));die;
        $config = array(
            'maxSize' => 3145728 ,// 设置附件上传大小
            'exts' => array('jpg','gif','png','jpeg'),// 设置附件上传类型
            'rootPath' => $rootPath, // 设置附件上传根目录
            'autoSub' => true,
            'subName' => $mall_id,
            'saveName' => array('uniqid',''),
            'savePath' => '',
        );
        $rootPath = $rootPath.$mall_id.'/';
        $path = $path.$mall_id.'/'.'/';
        $upload = new \Think\Upload($config);// 实例化上传类
        $info   =   $upload->upload();
        $fileNum = count($info);
        if(!$info) {// 上传错误提示错误信息
            echo json_encode(array('code' => 2, 'info' => $upload->getError()));
        }else{// 上传成功
            if($fileNum == 1){
                $info = $info[0];
                $image = new \Think\Image();
                $res = $image->open($rootPath.$info['savename'])
                    ->thumb(164,164,\Think\Image::IMAGE_THUMB_CENTER)
                    ->save($rootPath.'thumb_'.$info['savename']);
                if ($res) {
                    unlink($rootPath.$info['savename']);
                }
                echo json_encode(array('code' => 1, 'url' => $path.'thumb_'.$info['savename']));
            } else {
                $url = array();
                foreach($info as $key => $val){
                    $image = new \Think\Image();
                    $res = $image->open($rootPath.$val['savename'])
                        ->thumb(164,164, \Think\Image::IMAGE_THUMB_CENTER)
                        ->save($rootPath.'thumb_'.$val['savename']);
                    if ($res) {
                        unlink($rootPath.$info['savename']);
                    }
                    $url[$key] = $path.'thumb_'.$val['savename'];
                }
                echo json_encode(array('code' => 1, 'url' => $url));
            }
        }
    }



    public function merGoodsPic(){
        before_api();
        checkLogin();

        checkAuth();

        $mall_id = session('mall.mall_id').'';
        $root = APP_ROOT;
        $path = '/Public/Uploads/Images/Mall/';
        $rootPath = $root.$path;
        // echo json_encode(array('code' => 2,'info' => $rootPath));die;
        $config = array(
            'maxSize' => 3145728 ,// 设置附件上传大小
            'exts' => array('jpg','gif','png','jpeg'),// 设置附件上传类型
            'rootPath' => $rootPath, // 设置附件上传根目录
            'autoSub' => true,
            'subName' => $mall_id,
            'saveName' => array('uniqid',''),
            'savePath' => '',
        );
        $rootPath = $rootPath.$mall_id.'/';
        $path = $path.$mall_id.'/'.'/';
        $upload = new \Think\Upload($config);// 实例化上传类
        $info   =   $upload->upload();
        $fileNum = count($info);
        if(!$info) {// 上传错误提示错误信息
            echo json_encode(array('code' => 2, 'info' => $upload->getError()));
        }else{// 上传成功
            if($fileNum == 1){
                $info = $info['uploadfile'];
                $image = new \Think\Image();
                $res = $image->open($rootPath.$info['savename'])
                    ->thumb(164,164,\Think\Image::IMAGE_THUMB_CENTER)
                    ->save($rootPath.'thumb_'.$info['savename']);
                if ($res) {
                    unlink($rootPath.$info['savename']);
                }
                echo json_encode(array('code' => 1, 'url' => $path.'thumb_'.$info['savename']));
            } else {
                $url = array();
                foreach($info as $key => $val){
                    $image = new \Think\Image();
                    $res = $image->open($rootPath.$val['savename'])
                        ->thumb(164,164, \Think\Image::IMAGE_THUMB_CENTER)
                        ->save($rootPath.'thumb_'.$val['savename']);
                    if ($res) {
                        unlink($rootPath.$info['savename']);
                    }
                    $url[$key] = $path.'thumb_'.$val['savename'];
                }
                echo json_encode(array('code' => 1, 'url' => $url));
            }
        }
    }



    public function instCosOnrPic(){
        before_api();
        checkLogin();
        checkAuth();
//        $mall_id = session('inst.institution_id').'';
        $mall_id = session('mall.mallid');
        $rootPath = APP_ROOT.'/Public/Uploads/Images/Inst/'.$mall_id.'/';
        $upload = uploadFiles('Inst');
        $info = $upload->upload();
        $fileNum = count($info);
        if(!$info) {// 上传错误提示错误信息
            echo json_encode(array('code' => 2, 'info' => $upload->getError()));
        }else{// 上传成功
            if($fileNum == 1){
                $info = $info[0];
                $image = new \Think\Image();
                $res = $image->open($rootPath.$info['savename'])
                    ->thumb(164,164,\Think\Image::IMAGE_THUMB_CENTER)
                    ->save($rootPath.'thumb_'.$info['savename']);
                if ($res) {
                    unlink($rootPath.$info['savename']);
                }
                echo json_encode(array('code' => 1, 'url' => $rootPath.'thumb_'.$info['savename']));
            } else {
                $url = array();
                foreach($info as $key => $val){
                    $image = new \Think\Image();
                    $res = $image->open($rootPath.$val['savename'])
                        ->thumb(164,164, \Think\Image::IMAGE_THUMB_CENTER)
                        ->save($rootPath.'thumb_'.$val['savename']);
                    if ($res) {
                        unlink($rootPath.$info['savename']);
                    }
                    $url[$key] = $rootPath.'thumb_'.$val['savename'];
                }
                echo json_encode(array('code' => 1, 'url' => $url));
            }
        }
    }

    public function instEplyPic(){
        before_api();

        checkLogin();

        checkAuth();
        $mall_id = session('mall.mall_id').'';
        $root = APP_ROOT;
        $path = '/Public/Uploads/Images/Mall/';
        $rootPath = $root.$path;
        // echo json_encode(array('code' => 2,'info' => $rootPath));die;
        $config = array(
            'maxSize' => 3145728 ,// 设置附件上传大小
            'exts' => array('jpg','gif','png','jpeg'),// 设置附件上传类型
            'rootPath' => $rootPath, // 设置附件上传根目录
            'autoSub' => true,
            'subName' => $mall_id,
            'saveName' => array('uniqid',''),
            'savePath' => '',
        );
        $rootPath = $rootPath.$mall_id.'/';
        $path = $path.$mall_id.'/'.'/';
        $upload = new \Think\Upload($config);// 实例化上传类
        $info   =   $upload->upload();
        $fileNum = count($info);
        if(!$info) {// 上传错误提示错误信息
            echo json_encode(array('code' => 2, 'info' => $upload->getError()));
        }else{// 上传成功
            if($fileNum == 1){
                $info = $info['uploadfile'];
                $image = new \Think\Image();
                $res = $image->open($rootPath.$info['savename'])
                    ->thumb(164,164,\Think\Image::IMAGE_THUMB_CENTER)
                    ->save($rootPath.'thumb_'.$info['savename']);
                if ($res) {
                    unlink($rootPath.$info['savename']);
                }
                echo json_encode(array('code' => 1, 'url' => $path.'thumb_'.$info['savename']));
            } else {
                $url = array();
                foreach($info as $key => $val){
                    $image = new \Think\Image();
                    $res = $image->open($rootPath.$val['savename'])
                        ->thumb(164,164, \Think\Image::IMAGE_THUMB_CENTER)
                        ->save($rootPath.'thumb_'.$val['savename']);
                    if ($res) {
                        unlink($rootPath.$info['savename']);
                    }
                    $url[$key] = $path.'thumb_'.$val['savename'];
                }
                echo json_encode(array('code' => 1, 'url' => $url));
            }
        }
    }

    /**
     * 课程荣誉图片
     */
    public function coursePic(){
        before_api();
        checkLogin();

        checkAuth();

        $mall_id = session('inst.institution_id').'';
        $mall_id = session('mall.mall_id').'';
        $root = APP_ROOT;
        $path = '/Public/Uploads/Images/Merchant/';
        $rootPath = $root.$path;
        // echo json_encode(array('code' => 2,'info' => $rootPath));die;
        $config = array(
            'maxSize' => 3145728 ,// 设置附件上传大小
            'exts' => array('jpg','gif','png','jpeg'),// 设置附件上传类型
            'rootPath' => $rootPath, // 设置附件上传根目录
            'autoSub' => true,
            'subName' => $mall_id,
            'saveName' => array('uniqid',''),
            'savePath' => '',
        );
        $rootPath = $rootPath.$mall_id.'/';
        $path = $path.$mall_id.'/'.'/';
        $upload = new \Think\Upload($config);// 实例化上传类
        $info   =   $upload->upload();
        $fileNum = count($info);
        if(!$info) {// 上传错误提示错误信息
            echo json_encode(array('code' => 2, 'info' => $upload->getError()));
        }else{// 上传成功
            if($fileNum == 1){
                $info = $info[0];
                $image = new \Think\Image();
                $res = $image->open($rootPath.$info['savename'])
                    ->thumb(164,164,\Think\Image::IMAGE_THUMB_CENTER)
                    ->save($rootPath.'thumb_'.$info['savename']);
                if ($res) {
                    unlink($rootPath.$info['savename']);
                }
                echo json_encode(array('code' => 1, 'url' => $path.'thumb_'.$info['savename']));
            } else {
                $url = array();
                foreach($info as $key => $val){
                    $image = new \Think\Image();
                    $res = $image->open($rootPath.$val['savename'])
                        ->thumb(164,164, \Think\Image::IMAGE_THUMB_CENTER)
                        ->save($rootPath.'thumb_'.$val['savename']);
                    if ($res) {
                        unlink($rootPath.$info['savename']);
                    }
                    $url[$key] = $path.'thumb_'.$val['savename'];
                }
                echo json_encode(array('code' => 1, 'url' => $url));
            }
        }
    }
}