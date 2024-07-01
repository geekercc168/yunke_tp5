<?php
namespace app\admin\controller;
use app\common\controller\AdminBase;
use think\Db;
class Mobileterminal extends AdminBase
{
    function index(){
        $Android=json_to_array($this->getAppStatus('Android'));
        $Wxxcx=json_to_array($this->getAppStatus('Weixinxcx'));

        return $this->fetch('index',['Android'=>$Android,'Weixinxcx'=>$Wxxcx]);
    }
    function app(){
        if ($this->request->isPost()) {
            $info=$this->get_site_info();
            $param=$this->request->param();
            $param['appicon']=is_https().get_domain().$param['appicon'];
            $param['type']='Android';
            $param['domain']=$info['domain'];
            $param['authorcode']=$info['authorcode'];
            $url = $info['server']. "/educloud/Mobileterminal/app";
            $restemp = json_to_array(post_curl($url, $param));
            if($restemp['code']==1){
                $this->success('申请成功');
            }else{
                $this->error($restemp['msg']);
            }
        }
        return $this->fetch('app');
    }
    function weixinxcx(){
        if ($this->request->isPost()) {
            $param=$this->request->param();
            $param['appicon']=is_https().get_domain().$param['appicon'];
            $param['type']='Weixinxcx';
            $url = config('author_web') . "/educloud/Mobileterminal/app";
            $restemp = json_to_array(post_curl($url, $param));
            if($restemp['code']=1){
                $this->success('申请成功');
            }else{
                $this->error($restemp['msg']);
            }
        }
        return $this->fetch('weixinxcx');
    }
    
    function mobile(){
        $info=$this->get_site_info();
        $url = $info['server'] . "/educloud/Mobileterminal/getMobileVersion";
        $postdata = ['domain'=>$info['domain']];
        $Mobile=json_to_array((post_curl($url, $postdata)));
        return $this->fetch('mobile',['H5'=>$Mobile,'h5version'=>config('h5version')]);
    }
    function getAppStatus($type){
        $info=$this->get_site_info();
        $url = $info['server'] . "/educloud/Mobileterminal/getAppStatus";
        $postdata = ['domain'=>$info['domain'],'type'=>$type];
        return (post_curl($url, $postdata));
    }
    function downh5(){
        $param=$this->request->param();
        return $this->fetch('downh5',['downurl'=>$param['url'],'newversion'=>$param['newversion'],'notes'=>$param['notes']]);
    }
    function downMobile(){
        $param=$this->request->param();
        return $this->fetch('downMobile',['newversion'=>$param['newversion'],'notes'=>$param['notes']]);
    }
    function checkEnvironment()
    {
        $res['code']=1;
        $downloadDirectory = 'update/download';
        if (file_exists($downloadDirectory)) {
            if (!is_writeable($downloadDirectory)) {
                $res['code']=0;
                $res['msg']="下载目录({$downloadDirectory})无写权限";
                echo json_encode($res);exit();
            }
        } else {
            try {
                mkdir($downloadDirectory, 0777, true);
            } catch (\Exception $e) {
                $res['code']=0;
                $res['msg']="下载目录({$downloadDirectory})创建失败";
                echo json_encode($res);exit();
            }
        }
        echo json_encode($res);
    }
    public function downLoadFile(){
        $info= $this->get_site_info();
        $url = $info['server'] . "/educloud/Mobileterminal/getMobileDownUrl";
        $res=json_decode(post_curl($url,$info),true);
        if($res['code']==1){
            $downurl= $info['server'] .str_replace('/public','',$res['downurl']);
            $downurl=str_replace('/upload','upload',$downurl);
        }else{
            echo json_encode($res);exit();
        }
        $filePath = $this->downfile(UrlDecode($downurl));
        if($filePath){
            $this->unzipFile($filePath);
            $this->replaceFile();
            $res['code']=1;
            echo json_encode($res);
        }else{
            $res['code']=0;
            $res['msg']='下载文件失败！';
            echo json_encode($res);
        }
    }
    private function downfile($url){
        if(strstr($url,'http')){
            $save_dir = "update/download";
            $filename = basename($url);
            if ($this->exists($save_dir)) {
                $this->remove($save_dir);
                mkdir($save_dir, 0777, true);
            }
            $filePath = $this->getFile($url, $save_dir, $filename, 1);
            return   $filePath;
        }else{
            return   $url;
        }
    }
    public function unzipFile($filePath)
    {
        $unzipDir ='update/unzip';
        if (file_exists($unzipDir)) {
            if (!is_writeable($unzipDir)) {
                $res['code']=0;
                $res['msg']="解压目录({$unzipDir})无写权限";
                echo json_encode($res);exit();
            }
        } else {
            try {
                mkdir($unzipDir, 0777, true);
            } catch (\Exception $e) {
                $res['code']=0;
                $res['msg']="解压目录({$unzipDir})创建失败";
                echo json_encode($res);exit();
            }
        }
        $zip = new \ZipArchive;
        if ($zip->open($filePath) === true) {
            $zip->extractTo($unzipDir);
            $zip->close();
        } else {
            $res['code']=0;
            $res['msg']='打开文件失败';
            echo json_encode($res);exit();
        }
    }
    private function getFile($url, $save_dir = '', $filename = '', $type) {
        if (trim($url) == '') {
            $res['code']=0;
            $res['msg']='升级包文件下载路径不存在！';
            echo json_encode($res);exit();
        }
        if (trim($save_dir) == '') {
            $save_dir = './';
        }
        if (0 !== strrpos($save_dir, '/')) {
            $save_dir.= '/';
        }
        if (!file_exists($save_dir) && !mkdir($save_dir, 0777, true)) {
            $res['code']=0;
            $res['msg']='升级包文件不存在！';
            echo json_encode($res);exit();
        }
        if ($type) {
            $ch = curl_init();
            $timeout = 50;
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            $content = curl_exec($ch);
            curl_close($ch);
        } else {
            ob_start();
            readfile($url);
            $content = ob_get_contents();
            ob_end_clean();
        }
        $fp2 = @fopen($save_dir . $filename, 'a');
        fwrite($fp2, $content);
        fclose($fp2);
        unset($content, $url);
        $filePath=$save_dir . $filename;
        return $filePath;
    }
    public function replaceFile($packageDir='update')
    {
        $this->mirror("{$packageDir}/unzip/file/", ROOT_PATH, null, array(
            'override'        => true,
            'copy_on_windows' => true
        ));
    }

    function delFile(){
        $packageDir='update';
        if ($this->exists($packageDir.'/download')) {
            $this->remove($packageDir.'/download');
            mkdir($packageDir.'/download', 0777, true);
        }
        if ($this->exists($packageDir.'/unzip')) {
            $this->remove($packageDir.'/unzip');
            mkdir($packageDir.'/unzip', 0777, true);
        }
        $res['code']=1;
        echo json_encode($res);
    }
    function  clearCache(){
        clear_cache();
        $res['code']=1;
        echo json_encode($res);
    }
    function finish(){
        $data = [];
        foreach ($this->request->param() as $k => $v) {
            $data[] = ['name' => $k, 'value' => $v];
        }
        $this->saveAll('system', $data);
        clear_cache();
        $res['code']=2;
        echo json_encode($res);
    }
    public function mirror($originDir, $targetDir, \Traversable $iterator = null, $options = array())
    {
        $targetDir = rtrim($targetDir, '/\\');
        $originDir = rtrim($originDir, '/\\');
        if ($this->exists($targetDir) && isset($options['delete']) && $options['delete']) {
            $deleteIterator = $iterator;
            if (null === $deleteIterator) {
                $flags = \FilesystemIterator::SKIP_DOTS;
                $deleteIterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($targetDir, $flags), \RecursiveIteratorIterator::CHILD_FIRST);
            }
            foreach ($deleteIterator as $file) {
                $origin = str_replace($targetDir, $originDir, $file->getPathname());
                if (!$this->exists($origin)) {
                    $this->remove($file);
                }
            }
        }
        $copyOnWindows = false;
        if (isset($options['copy_on_windows']) && !function_exists('symlink')) {
            $copyOnWindows = $options['copy_on_windows'];
        }
        if (null === $iterator) {
            $flags = $copyOnWindows ? \FilesystemIterator::SKIP_DOTS | \FilesystemIterator::FOLLOW_SYMLINKS : \FilesystemIterator::SKIP_DOTS;
            $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($originDir, $flags), \RecursiveIteratorIterator::SELF_FIRST);
        }
        if ($this->exists($originDir)) {
            $this->mkdir($targetDir);
        }
        foreach ($iterator as $file) {
            $target = str_replace($originDir, $targetDir, $file->getPathname());
            if ($copyOnWindows) {
                if (is_link($file) || is_file($file)) {
                    $this->copy($file, $target, isset($options['override']) ? $options['override'] : false);
                } elseif (is_dir($file)) {
                    $this->mkdir($target);
                } else {
                    throw new \RuntimeException(sprintf('Unable to guess "%s" file type.', $file));
                }
            } else {
                if (is_link($file)) {
                    $this->symlink($file->getRealPath(), $target);
                } elseif (is_dir($file)) {
                    $this->mkdir($target);
                } elseif (is_file($file)) {
                    $this->copy($file, $target, isset($options['override']) ? $options['override'] : false);
                } else {
                    throw new \RuntimeException(sprintf('Unable to guess "%s" file type.', $file));
                }
            }
        }
    }
    public function copy($originFile, $targetFile, $override = false)
    {
        if (stream_is_local($originFile) && !is_file($originFile)) {
            throw new \RuntimeException(sprintf('Failed to copy %s because file not exists', $originFile));
        }
        $this->mkdir(dirname($targetFile));
        $doCopy = true;
        if (!$override && null === parse_url($originFile, PHP_URL_HOST) && is_file($targetFile)) {
            $doCopy = filemtime($originFile) > filemtime($targetFile);
        }
        if ($doCopy) {
            $source = fopen($originFile, 'r');
            $target = fopen($targetFile, 'w', null, stream_context_create(array('ftp' => array('overwrite' => true))));
            stream_copy_to_stream($source, $target);
            fclose($source);
            fclose($target);
            unset($source, $target);
            if (!is_file($targetFile)) {
                throw new \RuntimeException(sprintf('Failed to copy %s to %s', $originFile, $targetFile));
            }
        }
    }
    public function mkdir($dirs, $mode = 0777)
    {
        foreach ($this->toIterator($dirs) as $dir) {
            if (is_dir($dir)) {
                continue;
            }
            if (true !== @mkdir($dir, $mode, true)) {
                $error = error_get_last();
                if (!is_dir($dir)) {
                    if ($error) {
                        throw new \RuntimeException(sprintf('Failed to create "%s": %s.', $dir, $error['message']));
                    }
                    throw new \RuntimeException(sprintf('Failed to create "%s"', $dir));
                }
            }
        }
    }
    private function toIterator($files)
    {
        if (!$files instanceof \Traversable) {
            $files = new \ArrayObject(is_array($files) ? $files : array($files));
        }
        return $files;
    }
    public function exists($files)
    {
        foreach ($this->toIterator($files) as $file) {
            if (!file_exists($file)) {
                return false;
            }
        }
        return true;
    }
    public function remove($files)
    {
        $files = iterator_to_array($this->toIterator($files));
        $files = array_reverse($files);
        foreach ($files as $file) {
            if (!file_exists($file) && !is_link($file)) {
                continue;
            }
            if (is_dir($file) && !is_link($file)) {
                $this->remove(new \FilesystemIterator($file));
                if (true !== @rmdir($file)) {
                    throw new \RuntimeException(sprintf('Failed to remove directory %s', $file));
                }
            } else {
                if ('\\' === DIRECTORY_SEPARATOR && is_dir($file)) {
                    if (true !== @rmdir($file)) {
                        throw new \RuntimeException(sprintf('Failed to remove file %s', $file));
                    }
                } else {
                    if (true !== @unlink($file)) {
                        throw new \RuntimeException(sprintf('Failed to remove file %s', $file));
                    }
                }
            }
        }
    }

}