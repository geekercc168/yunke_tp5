<?php

namespace app\api\controller;
use app\common\controller\Base;
use think\Controller;
vendor ('aliyuncs.oss-sdk-php.autoload');
use OSS\OssClient;
use OSS\Core\OssException;
class Uploader extends Base
{
    /**
     * 上传图片到本地
     */
    public function uploadImage()
    {
        try {
            $file = $this->request->file('file');
            $info = $file->move(ROOT_PATH . 'public' . DS . 'upload' . DS . 'image');
            if ($info) {
                $upload_image = unserialize(config('upload_image'));
                if ($upload_image['is_thumb'] == 1 || $upload_image['is_water'] == 1 || $upload_image['is_text'] == 1) {
                    $object_image = \think\Image::open($info->getPathName());
                    // 图片压缩
                    if ($upload_image['is_thumb'] == 1) {
                        $object_image->thumb($upload_image['max_width'], $upload_image['max_height']);
                    }
                    // 图片水印
                    if ($upload_image['is_water'] == 1) {
                        $object_image->water(ROOT_PATH . trim($upload_image['water_source'], '/'), $upload_image['water_locate'], $upload_image['water_alpha']);
                    }
                    // 文本水印
                    if ($upload_image['is_text'] == 1) {
                        $font = !empty($upload_image['text_font']) ? trim($upload_image['text_font'], '/') : 'vendor/topthink/think-captcha/assets/zhttfs/1.ttf';
                        $object_image->text($upload_image['text'], ROOT_PATH . $font, $upload_image['text_size'], $upload_image['text_color'], $upload_image['text_locate'], $upload_image['text_offset'], $upload_image['text_angle']);
                    }
                    $object_image->save($info->getPathName());
                }
                return ['code' => 1, 'url' => '/upload/image/' . str_replace('\\', '/', $info->getSaveName())];
            } else {
                return ['code' => 0, 'msg' => $file->getError()];
            }
        } catch (\Exception $e) {
            return ['code' => 0, 'msg' => $e->getMessage()];
        }
    }
    /**
     * 上传文件到本地
     */
    public function uploadFile()
    {
        try {
            $file = $this->request->file('file');
            $info = $file->move(ROOT_PATH . 'public' . DS . 'upload' . DS . 'file');
            if ($info) {
                return ['code' => 1, 'url' => '/upload/file/' . str_replace('\\', '/', $info->getSaveName())];
            } else {
                return ['code' => 0, 'msg' => $file->getError()];
            }
        } catch (\Exception $e) {
            return ['code' => 0, 'msg' => $e->getMessage()];
        }
    }
    /**
     * 上传视频到本地
     */
    public function uploadVideo()
    {
        try {
            $file = $this->request->file('file');
            $info = $file->move(ROOT_PATH . 'public' . DS . 'upload' . DS . 'video');
            if ($info) {
                return ['code' => 1, 'url' => '/upload/video/' . str_replace('\\', '/', $info->getSaveName())];
            } else {
                return ['code' => 0, 'msg' => $file->getError()];
            }
        } catch (\Exception $e) {
            return ['code' => 0, 'msg' => $e->getMessage()];
        }
    }
    /**
     * 阿里云Oss上传类
     */
    function new_oss(){
        if(empty(config('KeyID')) || empty(config('KeySecret'))){
            return ['code' => 0, 'msg' => '请先配置阿里云oss'];
        }else{
            $oss=new OssClient(config('KeyID'),config('KeySecret'),config('EndPoint'));
            return $oss;
        }
    }

    /**
     * 阿里云Oss上传实例（通用）
     */
    public function ossupload()
    {
        try {
            $file = $this->request->file('file');
            $param = $this->request->param();
            if(empty($param)) {
                $param['type']='file';
            };
            $ossClient =$this->new_oss();
            $exename  = $this->getExeName($file->getInfo('name'));
            $imageSavePath = uniqid().'.'.$exename;
            $ossClient->uploadFile(config('Bucket'), 'files'.is_admin_login().'/'.$param['type'].'/'.$imageSavePath,$file->getInfo()['tmp_name']);
            $url='//'.config('Bucket').'.'.config('EndPoint').'/'.'files'.is_admin_login().'/'.$param['type'].'/'.$imageSavePath;
            if($this->insert('Material', ['type'=>$param['type'],'uid'=>is_admin_login(),'original_name'=>$file->getInfo('name'),'oss_name'=>'files'.is_admin_login().'/'.$param['type'].'/'.$file->getInfo('name'),'oss_url'=>$url,'addtime'=>date('Y-m-d H:i:s', time())])===true){
                return ['code' => 1, 'url' => $url];
            }else{
                return ['code' => 0, 'msg' => $this->errorMsg];
            }
        } catch (\Exception $e) {
            return ['code' => 0, 'msg' => $e->getMessage()];
        }
    }
    /**
     * 删除OSS上的文件
     */
    public function ossdel()
    {
        if ($this->request->isPost()) {
            $data=model('Material')->where(['id'=>input('id')])->field('oss_name')->find();
            $ossClient =$this->new_oss();
            $ossClient->deleteObject(config('Bucket'), $data['oss_name']);
            if ($this->delete('Material', $this->request->param()) === true) {
                insert_admin_log('删除资料');
                $this->success('删除成功');
            } else {
                $this->error($this->errorMsg);
            }
        }
    }
    /**
     *编辑器上传图片
     */
    function wangEditorUp(){
        $file = $this->request->file('image');
        $data = model('system')->where('name', 'upload_image')->find();
        $upload=unserialize($data['value']);
        if($upload['location']==1){
            return $this->wangEditorUpLocal($file);
        }else{
            return $this->wangEditorUpOss($file);
        }
    }
    /**
     *编辑器上传图片到oss
     */
    function wangEditorUpOss($file){
        try {
            $param['type']='image';
            $ossClient =$this->new_oss();
            $exename  = $this->getExeName($file->getInfo('name'));
            $imageSavePath = uniqid().'.'.$exename;
            $ossClient->uploadFile(config('Bucket'), 'files'.is_admin_login().'/'.$param['type'].'/'.$imageSavePath,$file->getInfo()['tmp_name']);
            $url='//'.config('Bucket').'.'.config('EndPoint').'/'.'files'.is_admin_login().'/'.$param['type'].'/'.$imageSavePath;
            return json_encode(['errno' => 0, 'data' =>[$url]]);
        } catch (\Exception $e) {
            return json_encode(['errno' => 1, 'data' => $e->getMessage()]);
        }
    }
    /**
     *编辑器上传图片到本地
     */
    function wangEditorUpLocal($file){
        try {
            $info = $file->move(ROOT_PATH . 'public' . DS . 'upload' . DS . 'image');
            if ($info) {
                $upload_image = unserialize(config('upload_image'));
                if ($upload_image['is_thumb'] == 1 || $upload_image['is_water'] == 1 || $upload_image['is_text'] == 1) {
                    $object_image = \think\Image::open($info->getPathName());
                    // 图片压缩
                    if ($upload_image['is_thumb'] == 1) {
                        $object_image->thumb($upload_image['max_width'], $upload_image['max_height']);
                    }
                    // 图片水印
                    if ($upload_image['is_water'] == 1) {
                        $object_image->water(ROOT_PATH . trim($upload_image['water_source'], '/'), $upload_image['water_locate'], $upload_image['water_alpha']);
                    }
                    // 文本水印
                    if ($upload_image['is_text'] == 1) {
                        $font = !empty($upload_image['text_font']) ? trim($upload_image['text_font'], '/') : 'vendor/topthink/think-captcha/assets/zhttfs/1.ttf';
                        $object_image->text($upload_image['text'], ROOT_PATH . $font, $upload_image['text_size'], $upload_image['text_color'], $upload_image['text_locate'], $upload_image['text_offset'], $upload_image['text_angle']);
                    }
                    $object_image->save($info->getPathName());
                }
                return json_encode(['errno' => 0, 'data' =>['/upload/image/' . str_replace('\\', '/', $info->getSaveName())]]);
            } else {
                return json_encode(['errno' => 1, 'data' => $file->getError()]);
            }
        } catch (\Exception $e) {
            return json_encode(['errno' => 1, 'data' => $e->getMessage()]);
        }
    }
    /**
     * 上传文件函数，如过上传不成功打印$_FILES数组，查看error报错信息
     * 值：0; 没有错误发生，文件上传成功。
     * 值：1; 上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值。
     * 值：2; 上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值。
     * 值：3; 文件只有部分被上传。
     * 值：4; 没有文件被上传。
     */
    public function localupload(){
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Content-type: text/html; charset=gbk32");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        $folder = input('folder');
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            exit;
        }
        if ( !empty($_REQUEST[ 'debug' ]) ) {
            $random = rand(0, intval($_REQUEST[ 'debug' ]) );
            if ( $random === 0 ) {
                header("HTTP/1.0 500 Internal Server Error");
                exit;
            }
        }
        set_time_limit(5 * 60);
        usleep(5000);
        $targetDir = ROOT_PATH . 'public' . DS . 'upload' . DS . 'video';
        if($folder){
            $uploadDir = $targetDir.DS.'file_material'.DIRECTORY_SEPARATOR.$folder.DIRECTORY_SEPARATOR.date('Ymd');
        }else{
            $uploadDir = $targetDir.DS.is_admin_login();
        }
        $cleanupTargetDir = true;
        $maxFileAge = 5 * 3600;
        if (!file_exists($targetDir)) {
            mkdir($targetDir,0777,true);
        }
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir,0777,true);
        }
        if (isset($_REQUEST["name"])) {
            $fileName = $_REQUEST["name"];
        } elseif (!empty($_FILES)) {
            $fileName = $_FILES["file"]["name"];
        } else {
            $fileName = uniqid("file_");
        }
        $oldName = $fileName;
        $fileName = iconv('UTF-8','gb2312',$fileName);
        $filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;
        $chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
        $chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 1;
        if ($cleanupTargetDir) {
            if (!is_dir($targetDir) || !$dir = opendir($targetDir)) {
                die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory111."}, "id" : "id"}');
            }
            while (($file = readdir($dir)) !== false) {
                $tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;
                if ($tmpfilePath == "{$filePath}_{$chunk}.part" || $tmpfilePath == "{$filePath}_{$chunk}.parttmp") {
                    continue;
                }
                if (preg_match('/\.(part|parttmp)$/', $file) && (filemtime($tmpfilePath) < time() - $maxFileAge)) {
                    unlink($tmpfilePath);
                }
            }
            closedir($dir);
        }
        if (!$out = fopen("{$filePath}_{$chunk}.parttmp", "wb")) {
            die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream222."}, "id" : "id"}');
        }
        if (!empty($_FILES)) {
            if ($_FILES["file"]["error"] || !is_uploaded_file($_FILES["file"]["tmp_name"])) {
                die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file333."}, "id" : "id"}');
            }
            if (!$in = fopen($_FILES["file"]["tmp_name"], "rb")) {
                die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream444."}, "id" : "id"}');
            }
        } else {
            if (!$in = fopen("php://input", "rb")) {
                die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream555."}, "id" : "id"}');
            }
        }
        while ($buff = fread($in, 4096)) {
            fwrite($out, $buff);
        }
        fclose($out);
        fclose($in);
        rename("{$filePath}_{$chunk}.parttmp", "{$filePath}_{$chunk}.part");
        $index = 0;
        $done = true;
        for( $index = 0; $index < $chunks; $index++ ) {
            if ( !file_exists("{$filePath}_{$index}.part") ) {
                $done = false;
                break;
            }
        }
        if ($done) {
            $pathInfo = pathinfo($fileName);
            $hashStr = substr(md5($pathInfo['basename']),8,16);
            $hashName = $oldName;
            $uploadPath = $uploadDir . DIRECTORY_SEPARATOR .$hashName;
            if (!$out = fopen($uploadPath, "wb")) {
                die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream666."}, "id" : "id"}');
            }
            if ( flock($out, LOCK_EX) ) {
                for( $index = 0; $index < $chunks; $index++ ) {
                    if (!$in = fopen("{$filePath}_{$index}.part", "rb")) {
                        break;
                    }
                    while ($buff = fread($in, 4096)) {
                        fwrite($out, $buff);
                    }
                    fclose($in);
                    unlink("{$filePath}_{$index}.part");
                }
                flock($out, LOCK_UN);
            }
            fclose($out);
            $response = [
                'success'=>true,
                'oldName'=>$oldName,
                'filePath'=>$uploadPath,
                'fileSuffixes'=>$pathInfo['extension'],
            ];
            return json($response);
        }
    }
    /**
     * 获取文件扩展名
     */
    public function getExeName($fileName){
        $pathinfo      = pathinfo($fileName);
        return strtolower($pathinfo['extension']);
    }
}