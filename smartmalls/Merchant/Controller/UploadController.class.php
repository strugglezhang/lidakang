<?php
namespace Merchant\Controller;

class UploadController extends CommonController {
    public function activity_pic_api(){
        checkLogin();

        checkAuth();

        $merchant_id = session('member_id').'';
        $root = APP_ROOT;
        $path = '/Public/Uploads/Images/Merchant/';
        $rootPath = $root.$path;
        // echo json_encode(array('code' => 2,'info' => $rootPath));die;
        $config = array(
            'maxSize' => 3145728 ,// 设置附件上传大小
            'exts' => array('jpg','gif','png','jpeg'),// 设置附件上传类型
            'rootPath' => $rootPath, // 设置附件上传根目录
            'autoSub' => true,
            'subName' => $merchant_id,
            'savePath' => '',
        );
        $rootPath = $rootPath.$merchant_id.'/';
        $path = $path.$merchant_id.'/';
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

    //新增商户，商户图片
    public function index_merchant_pic_api(){
        checkLogin();

        checkAuth();

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
            'savePath' => '',
        );
        $rootPath = $rootPath.$mall_id.'/';
        $path = $path.$mall_id.'/';
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

    //新增商户，营业执照图片
    public function index_passpord_pic_api(){
        checkLogin();

        checkAuth();

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
            'savePath' => '',
        );
        $rootPath = $rootPath.$mall_id.'/';
        $path = $path.$mall_id.'/';
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

    //新增商户，身份证图片图片
    public function index_id_pic_api(){
        checkLogin();

        checkAuth();

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
            'savePath' => '',
        );
        $rootPath = $rootPath.$mall_id.'/';
        $path = $path.$mall_id.'/';
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

    //新增商户，行业资质证书图片(多张)
    public function index_industry_pic_api(){
        checkLogin();

        checkAuth();

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
            'savePath' => '',
        );
        $rootPath = $rootPath.$mall_id.'/';
        $path = $path.$mall_id.'/';
        $upload = new \Think\Upload($config);// 实例化上传类

        $info   =   $upload->upload($_FILES['uploadfile']);
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

    //新增商户，商户相册(多张)
    public function index_merchants_pic_api(){
        checkLogin();

        checkAuth();

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
            'savePath' => '',
        );
        $rootPath = $rootPath.$mall_id.'/';
        $path = $path.$mall_id.'/';
        $upload = new \Think\Upload($config);// 实例化上传类

        $info   =   $upload->upload($_FILES['uploadfile']);
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