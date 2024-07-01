<?php
namespace app\admin\controller;
use app\common\controller\Base;
vendor('Aliyun.aliyun-php-sdk-core.Config');
use DefaultProfile;
use DefaultAcsClient;
use vod\Request\V20170321 as vod;
use app\common\extend\agora\RtmTokenBuilder;
/**
 * Class Account 教育云控制器类
 * www.yunknet.cn
 */
class Educloud extends Base
{
    protected function _initialize()
    {
        parent::_initialize();
        if ($this->request->isGet()) {
            $this->assign('navbar', list_to_tree($this->getNavbar()));
            $this->assign('breadcrumb', array_reverse(explode(',', $this->getBreadcrumb())));
            $this->assign('empty', '<tr><td colspan="20">~~暂无数据</td></tr>');
            $this->assign('addtime',date('Y-m-d h:i:s', time()) );
        }
    }
    /**
     * 绑定教育云直播账户
     */
    public function liveBind(){
        if ($this->request->isPost()) {
            $param=$this->request->param();
            $data = [];
            foreach ($param as $k => $v) {
                $data[] = ['name' => $k, 'value' => $v];
            }
            if($param['liveplatform']=='agora' || $param['liveplatform']=='pano'){
                db('auth_rule')->where('id',97)->setField(['status'=>0]);
                db('auth_rule')->where('id',99)->setField(['status'=>0]);
                db('auth_rule')->where('id',229)->setField(['status'=>0]);
            }
            if ($this->saveAll('system', $data) === true) {
                clear_cache();
                insert_admin_log('绑定教育云直播账户');
                $this->success('保存成功',url('admin/educloud/liveBind'));
            } else {
                $this->error($this->errorMsg);
            }
        }
        $data = [];
        foreach (model('system')->select() as $v) {
            $data[$v['name']] = $v['value'];
        }
        return $this->fetch('liveBind', ['data' => $data]);
    }

    /**
     * 绑定教育云点播账户
     */
    public function videoBind()
    {
        if ($this->request->isPost()) {
            $data = [];
            foreach ($param=$this->request->param() as $k => $v) {
                $data[] = ['name' => $k, 'value' => $v];
            }
            if ($this->saveAll('system', $data) === true) {
                if($param['rebuild']==1){
                    $admindata=model('admin')->field('id')->select();
                    foreach($admindata as $k => $v){
                        db('admin')->where(['id'=>$admindata[$k]['id']])->update(['category_id'=>'']);
                    }
                }
                clear_cache();
                insert_admin_log('绑定教育云点播播账户');
                $this->success('保存成功',url('admin/educloud/videoList'));
            } else {
                $this->error($this->errorMsg);
            }
        }
        $data = [];
        foreach (model('system')->select() as $v) {
            $data[$v['name']] = $v['value'];
        }
        return $this->fetch('videoBind', ['data' => $data]);
    }

    /**
     * 教育云点播视频列表
     */
    public function videoList()
    {
        $info = $this->get_site_info(1);
        $info['authorcode']=config('author_code');
        if(empty($info['KeyID'])||empty($info['KeySecret'])){
            $this->redirect('admin/educloud/videoBind');
        }
        $param = $this->request->param();
        $teacherinfo=model('admin')->where(['id'=>is_admin_login()])->find();
        $category_id=$teacherinfo['category_id'];
        if(empty($category_id)){
            $res=$this->addCategoryPhpSDK($teacherinfo['username'],config('root_category_id'));
            if(!empty($res['Category']['CateId'])){
                db('admin')->where(['id'=>is_admin_login()])->update(['category_id' => $res['Category']['CateId']]);
            }else{
                $this->error('创建视频目录失败，检查是否进行了域名授权!');
            }
        }
        if(!empty($param['CateId'])){
            $category_id=$param['CateId'];
        }
        $url = $info['server'] . "/educloud/alivideo/getvideolist";
        $postdata = ['private_domain'=>$info['private_domain'],'domain'=>$info['domain'],'authorcode'=>$info['authorcode'],'KeyID' => $info['KeyID'], 'keySecret' => $info['KeySecret'], 'PageSize' => config('PageSize'),'PageNo'=>$param['page'],'aliyuncategory'=>$category_id];
        $restemp = json_to_array(post_curl($url, $postdata));
        $videocategory=$this->getVideoCategory(1,100);
        return $this->fetch('videoList', ['list' => $restemp['VideoList']['Video'],'cateId'=>$param['CateId'],'curr'=>$param['page'],'count'=>$restemp['Total'],'PageSize'=>config('PageSize'),'videocategory'=>$videocategory['SubCategories']['Category']]);
    }
    /**
     * 阿里云视频列表
     */
    public function videoList2($param){
        $info = $this->get_site_info(1);
        $info['authorcode']=config('author_code');
        if(empty($param['CateId'])){
            $category_id=model('admin')->where(['id'=>is_admin_login()])->value('category_id');
        }else{
            $category_id=$param['CateId'];
        }
        $url = $info['server'] . "/educloud/alivideo/getvideolist";
        $postdata = ['private_domain'=>$info['private_domain'],'domain'=>$info['domain'],'authorcode'=>$info['authorcode'],'KeyID' => $info['KeyID'], 'keySecret' => $info['KeySecret'], 'PageSize' => config('PageSize'),'PageNo'=>$param['page'],'aliyuncategory'=>$category_id];
        $restemp =post_curl($url, $postdata);
        return  $restemp;
    }
    /**
     * 删除教育云视频
     */
    public function videodel()
    {
        $info = $this->get_site_info(1);
        $info['authorcode']=config('author_code');
        $url=$info['server']."/educloud/alivideo/delvideo";
        $flg=1;
        if ($this->request->isPost()) {
            $data = $this->request->param();
        }
        foreach($data['id'] as $key=>$value){
            $postdata=['private_domain'=>$info['private_domain'],'domain'=>$info['domain'],'authorcode'=>$info['authorcode'],'KeyID' => $info['KeyID'], 'keySecret' => $info['KeySecret'],'id'=>$value];
            $res=json_to_array(post_curl($url,$postdata));
            if(empty($res['Code'])){
                $flg=1;
            }else{
                $flg=0;
            }
        }
        if($flg==1){
            $this->success('删除成功',url('admin/educloud/videoList'));
        }else{
            $this->error($res['Code'],url('admin/educloud/videoList'));
        }
    }
    /**
     * 上传教育云视频
     */
    public function videoup()
    {
        $videocategory=$this->getVideoCategory(1,100);
        return $this->fetch('videoup',['videocategory'=>$videocategory['SubCategories']['Category'],'aliUid'=>config('AliUserId')]);
    }
    /**
     * 教育云视频分类列表
     */
    public function videocategory(){
        $param = $this->request->param();
        if(empty($param['page'])){
            $param['page']=1;
        }
        $PageSize=5;
        $videocategory=$this->getVideoCategory($param['page'],$PageSize);
        return $this->fetch('videocategory',['list'=>$videocategory['SubCategories']['Category'],'SubTotal'=>$videocategory['SubTotal'],'curr'=>$param['page'],'PageSize'=>$PageSize]);
    }
    public function getvideoCategory($curr,$PageSize){
        $url = config('author_web') . "/educloud/alivideo/getCategory";
        $category_id = model('admin')->where(['id' => is_admin_login()])->value('category_id');
        $postdata = ['KeyID' => config('KeyID'), 'keySecret' => config('KeySecret'),'CateId'=>$category_id,'PageNo'=>$curr,'PageSize'=>$PageSize];
        $restemp = json_to_array(post_curl($url, $postdata));
        return $restemp;
    }
    /**
     * 删除教育云视频分类
     */
    public function delvideocategory(){
        $param=$this->request->param();
        $url = config('author_web') . "/educloud/alivideo/delCategory";
        $postdata = ['KeyID' => config('KeyID'), 'keySecret' => config('KeySecret'),'CateId'=>$param['id']];
        json_to_array(post_curl($url, $postdata));
        $this->success('删除成功');
    }

    /**
     * 添加教育云视频分类
     */
    public function addvideocategory(){
        if ($this->request->isPost()) {
            $param = $this->request->param();
            $category_id = model('admin')->where(['id' => is_admin_login()])->value('category_id');
            $res = $this->addCategoryPhpSDK($param['name'], $category_id);
            if (!empty($res['Category']['CateId'])) {
                $this->success('创建成功');
            }else{
                $this->error($res['msg']);
            }
        }
        return $this->fetch('addvideocategory');
    }
    /**
     * 远程添加教育云视频子分类
     */
    public function addCategoryPhpSDK($name='111',$ParentId='1000182630'){
        $info = $this->get_site_info(1);
        $info['authorcode']=config('author_code');
        $url = config('author_web') . "/educloud/alivideo/AddCategoryPhpSDK";
        $postdata = ['domain'=>$info['domain'],'authorcode'=>$info['authorcode'],'KeyID' => $info['KeyID'], 'keySecret' => $info['KeySecret'],'CateName'=>$name,'ParentId'=>$ParentId];
        $restemp = json_to_array(post_curl($url, $postdata));
        return $restemp;
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
     * 获取上传教育云视频凭证
     */
    public function getaliuptoken()
    {
        $info = $this->get_site_info(1);
        $info['authorcode']=config('author_code');
        $param = $this->request->param();
        if(empty($param['categoryId'])){
            $category_id=model('admin')->where(['id'=>is_admin_login()])->value('category_id');
        }else{
            $category_id=$param['categoryId'];
        }
        $url=config('author_web')."/educloud/alivideo/getaliuptoken";
        $postdata = ['private_domain'=>$info['private_domain'],'domain'=>$info['domain'],'authorcode'=>$info['authorcode'],'KeyID' => $info['KeyID'], 'keySecret' => $info['KeySecret'], 'filename' =>input('post.title'),'CateId'=>$category_id];
        echo post_curl($url, $postdata);
    }
    /**
     * 获取点播播放凭证
     */
    public function getplaytoken($param=''){
        $info = $this->get_site_info(1);
        $info['authorcode']=config('author_code');
        $url = config('author_web') . "/educloud/alivideo/getplayertoken";
        $postdata = ['domain' => $info['domain'], 'authorcode' => $info['authorcode'],'KeyID' => $info['KeyID'], 'keySecret' => $info['KeySecret'], 'VideoId'=>$param['vid']];
        $res= json_to_array(post_curl($url, $postdata));
        if($res['code']==1){
            echo json_encode($res);
        }else{
            if($res['status']==1){
                $resarr['status']=1;
                $resarr['msg']=$res['msg'];
            }else{
                $resarr['status']=0;
                $resarr['PlayAuth']=$res['PlayAuth'];
            }
            echo json_encode($resarr);
        }
    }
    /**
     * APP获取点播播放信息
     */
    function getPlayInfo($videoid){
        $info = $this->get_site_info(1);
        $info['authorcode']=config('author_code');
        $url = config('author_web') . "/educloud/alivideo/getPlayInfo";
        $postdata = ['domain' => $info['domain'], 'authorcode' => $info['authorcode'],'KeyID' => $info['KeyID'], 'keySecret' => $info['KeySecret'], 'VideoId'=>$videoid];
        $res= post_curl($url, $postdata);
        return $res['PlayInfoList']['PlayInfo'] ;
    }
    /**
     * 获取声网token
     */
    function getAgoraToken($channel,$role,$uid){
        $info=$this->get_site_info(4);
        $info['authorcode']=config('author_code');
        $url = config('author_web') . "/educloud/agora/RtmTokenBuilder";
        $postdata = ['domain' => $info['domain'], 'authorcode' => $info['authorcode'], 'agoraAppid'=>$info['agoraAppid'],'agoraRestfulKey'=>$info['agoraRestfulKey'],'channel'=>$channel,'role'=>$role,'uid'=>$uid];
        return json_to_array(post_curl($url, $postdata));
    }
    /**
     * 生成直播间ID
     */
    function createRoomId(){
        $info = $this->get_site_info(1);
        $info['authorcode']=config('author_code');
        $url = config('author_web') . "/educloud/agora/roomCreate";
        $postdata = ['domain' => $info['domain'], 'authorcode' => $info['authorcode']];
        return json_to_array(post_curl($url, $postdata));
    }
    /**
     * 教育云短信
     */
    function cloudSMS(){

    }
    /**
     * 教育云短信签名
     */
    function signName(){
        if ($this->request->isPost()) {
            $param=$this->request->param();
            model('system')->save(['value' =>$param['SmsSign']], ['name' => 'SmsSign']);
            clear_cache();
            insert_admin_log('配置云短信签名');
            $this->success('保存成功');
        }
        $data = model('system')->where('name', 'SmsSign')->find();
        return $this->fetch('signName', ['data' => $data]);
    }
    /**
     * 教育云短信模板
     */
    function templates(){

        if ($this->request->isPost()) {
            $param=$this->request->param();
            $data=model('system')->where('name', $param['type'])->find();
            $val=unserialize($data['value']);
            if(empty($param['TemplatesId'])){
                $param['TemplatesId']=$val['TemplatesId'];
            }
            if($param['status']===null){
                $param['status']=$val['status'];
            }
            model('system')->save(['value' => serialize($param)], ['name' => $param['type']]);
            clear_cache();
            $this->success('设置成功');
        }
        $MC= model('system')->where('name', 'SmsTemplates_MC')->find();
        $SK= model('system')->where('name', 'SmsTemplates_SK')->find();
        return $this->fetch('templates', ['MC' => unserialize($MC['value']),'SK'=>unserialize($SK['value'])]);
    }

    // 获取导航栏
    public function getNavbar()
    {
        $where = ['type' => 'nav', 'status' => 1];
        if (session('admin_auth.username') != config('administrator')) {
            $access      = model('authGroupAccess')->with('authGroup')
                ->where('uid', session('admin_auth.admin_id'))->find();
            $where['id'] = ['in', $access['rules']];
        }
        return collection(model('authRule')->where($where)
            ->order('sort_order asc')->select())->toArray();
    }

    // 获取面包屑
    public function getBreadcrumb($id = null)
    {
        if ($authRule = model('authRule')->where(empty($id) ? ['url' => $this->request->module() . '/'
            . to_under_score($this->request->controller()) . '/'
            . $this->request->action()] : ['id' => $id])->order('pid desc')->find()) {
            $breadcrumb = $authRule['name'];
            if ($authRule['pid'] != 0) {
                $breadcrumb .= ',' . $this->getBreadcrumb($authRule['pid']);
            }
            return $breadcrumb;
        }
    }

}
