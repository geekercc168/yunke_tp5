<?php
namespace app\common\controller;
use wechat\Jssdk;
class IndexBase extends Base
{
    protected $noLogin = []; // 不用权限认证和登录的方法
    protected function _initialize()
    {
        parent::_initialize();
		!config('website_status') && die(config('colse_explain'));
        input('code') && session('code', input('code'));
        $nav=cache('nav');
        if(!$nav){
            $nav=model('nav')->order('sort_order asc')->where(['pid'=>0,'isshow'=>1])->select();
            foreach ($nav as $key => $value) {
                $nav[$key]['url']=(strstr($nav[$key]['url'],'http') || strstr($nav[$key]['url'],'javascript'))?$nav[$key]['url']:url($nav[$key]['url']);
                $childNav=model('nav')->order('sort_order asc')->where('pid',$nav[$key]['id'])->where('isshow',1)->select();
                foreach($childNav as $i => $value){
                    $childNav[$i]['url']=(strstr($childNav[$i]['url'],'http') || strstr($childNav[$i]['url'],'javascript'))?$childNav[$i]['url']:url($childNav[$i]['url']);
                }
                $nav[$key]['childNav']=$childNav;
            }
            cache('nav', $nav);
        }
        $link=cache('link');
        if(!$link){
            $link=model('link')->order('sort asc')->where(['status'=>1])->select();
            cache('link', $link);
        }
        foreach ($nav as $key => $value) {
            if(!empty($nav[$key]['childNav'])){
                foreach ($nav[$key]['childNav'] as $n => $value) {
                    $navUrlArray[]=$nav[$key]['childNav'][$n]['url'];
                }
            }
            $navUrlArray[]=$nav[$key]['url'];
        }
        //dump($navUrlArray);
        $code=hashids_encode(is_user_login(),'yunknet','8');
        $this->assign('empty', '<div class="point-content">
                                    <div class="nodata text-center"><img src="/static/default/img/nodata.png"><br>暂无内容，敬请期待</div>
                                </div>');
        $this->assign('islogin',is_user_login());
        $this->assign('addtime', date('Y-m-d h:i:s', time()));
        $this->assign('nav',$nav);
        $this->assign('navUrlArray',$navUrlArray);
        $this->assign('link',$link);
        $this->assign('code',$code);
        $this->assign('regfield',db('regfield')->select());
    }
    /**
     * 生成token
     */
    public function makeToken(){
        $name = "liverecord";
        $time = time();
        $end_time = time()+86400;
        $info = $name. '.' .$time.'.'.$end_time;
        $signature = hash_hmac('md5',$info,'www.yunknet.cn');
        $token = $info . '.' . $signature;
        return $token;
    }
    /**
     * 检查token
     */
    public function checkToken($token){
        $explode = explode('.',$token);
        $info = $explode[0].'.'.$explode[1].'.'.$explode[2];
        $true_signature = hash_hmac('md5',$info,'www.yunknet.cn');
        if ($true_signature != $explode[3]) {
            exit();
        }
    }
    /**
     * 检查是否登录
     */
    public function checkLogin()
    {
        if (!is_user_login() && !in_array($this->request->action(), $this->noLogin)) {
            return false;
        }
        return true;
    }
    /**
     * 检查是否绑定手机号
     */
    public function checkBangTel(){
        if(is_user_login()){
            $userInfo=model('user')->where(['id'=>is_user_login()])->find();
            if(empty($userInfo['mobile']) || empty($userInfo['nickname'])){
                return $this->redirect(url('index/user/binding'));
            }
            return true;
        }
        return true;
    }
    /**
     * 上传图片
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
                        $object_image->water(ROOT_PATH . str_replace('/', '\\', trim($upload_image['water_source'], '/')), $upload_image['water_locate'], $upload_image['water_alpha']);
                    }
                    // 文本水印
                    if ($upload_image['is_text'] == 1) {
                        $font = !empty($upload_image['text_font']) ? str_replace('/', '\\', trim($upload_image['text_font'], '/')) : 'vendor\topthink\think-captcha\assets\zhttfs\1.ttf';
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
     * 支付宝配置
     */
    public function alipayConfig(){
        $config = [
            'app_id' => config('alipay_app_id'),
            'rsa_private_key' =>config('alipay_rsa_private_key'),
            'alipay_public_key' => config('alipay_public_key')
        ];
        return $config;
    }
    /**
     * 微信配置
     */
    public function wechatConfig(){
        $config = [
            'appid' => config('weixin_appid'),
            'mch_id' =>config('weixin_mch_id'),
            'key' =>config('weixin_key'),
            'AppSecret' =>config('weixin_AppSecret'),
            'jsapi_appid'=>config('weixin_appid'),
            'jsapi_secret'=>config('weixin_AppSecret'),
            'sslcert_path' => '',
            'sslkey_path' => ''
        ];
        return $config;
     } 
    /**
     * 微信APP支付配置
     */
    public function wechatAppConfig(){
        $config = [
            'appid' => config('app_appid'),
            'mch_id' =>config('app_mch_id'),
            'key' =>config('app_key'),
            'AppSecret' =>config('app_AppSecret'),
        ];
        return $config;
    }
    /**
     * 微信内支付配置
     */
    public function wechatJsApiConfig(){
        $config = [
            'appid' => config('mp_appid'),
            'mch_id' =>config('mp_mch_id'),
            'key' =>config('mp_key'),
            'appsecret' =>config('mp_AppSecret'),
            'sslcert_path' => '',
            'sslkey_path' => ''
        ];
        return $config;
    }
    /**
     * 微信登录配置
     */
    function getWeixinLoginConfig(){
        $data = model('system')->where('name', 'Weixin_Login')->find();
        return unserialize($data['value']);
    }
    /**
     * QQ登录配置
     */
    function getQqLoginConfig(){
        $data = model('system')->where('name', 'QQ_Login')->find();
        return unserialize($data['value']);
    }
    /**
     * 根据课程ID，课程类型获取课程信息
     */
     public function getCouseInfo($cid,$type){
         switch ($type)
         {
             case 1:
                 $courseinfo = model('videoCourse')->where(['id'=>$cid])->find();
                 break;
             case 2:
                 $courseinfo = model('liveCourse')->where(['id'=>$cid])->find();
                 break;
             case 3:
                 $courseinfo = model('classroom')->where(['id'=>$cid])->find();
                 $courseinfo['cids']=json_to_array($courseinfo['cids']);
                 $courseinfo['teacher_id']=$courseinfo['headteacher'];
                 break;
         }
         return $courseinfo;
     }
    /**
     * 根据课程ID，课程类型获取课程信息
     */
    public function appGetCouseInfo($cid,$type){
        switch ($type)
        {
            case 1:
                $courseinfo = model('videoCourse')->where(['id'=>$cid])->field('brief',true)->find();
                $courseinfo['picture']=formatUrl($courseinfo['picture']);
                break;
            case 2:
                $courseinfo = model('liveCourse')->where(['id'=>$cid])->field('brief',true)->find();
                $courseinfo['picture']=formatUrl($courseinfo['picture']);
                break;
            case 3:
                $courseinfo = model('classroom')->where(['id'=>$cid])->find();
                $courseinfo['cids']=json_to_array($courseinfo['cids']);
                $courseinfo['teacher_id']=$courseinfo['headteacher'];
                break;
        }
        return $courseinfo;
    }
    /**
     * 获取课程优惠券信息
     */
    function coupon($name,$uId){
        $coupon=model('marketing')->where(['name'=>$name,'type'=>'coupon'])->find();
        $details=json_decode($coupon['details'],true);
        $count=model('userCourse')->where(['uid'=>is_user_login()])->count();
        if(($details['status']==1 && $name=='reg')|| ($details['status']==1 && $name=='buy' && $count<=1)){
            $where=['rate'=>$details['rate'],'status'=>0,'buystatus'=>0,'usestatus'=>0,'userId'=>0];
            $couponList=  model('coupon')->where($where)->limit($details['numbs'])->select();
            if($couponList){
                foreach ($couponList as $key => $value) {
                    $now=time();
                    $useaddtime=date("Y-m-d H:i:s",time());
                    $userendtime=date("Y-m-d H:i:s",strtotime('+'.$couponList[$key]['youxiaoqi'].'day',$now));
                    $data=['id'=>$couponList[$key]['id'],'buystatus'=>1,'userId'=>$uId,'useaddtime'=>$useaddtime,'userendtime'=>$userendtime];
                    model('coupon')->update($data);
                }
                $this->sentMessage(0,$uId,0,'恭喜您获取了课程购买优惠券',0,0);
            }
        }
    }
    public function share(){
        $appid=config('mp_appid');
        $appsercet=config('mp_AppSecret');
        $jssdkOBJ=new Jssdk($appid,$appsercet);
        $res=$jssdkOBJ->getSignPackage();
        return (['appId'=>$res['appId'],'timestamp'=>$res['timestamp'],'nonceStr'=>$res['nonceStr'],'signature'=>$res["signature"]]);
    }
    /**
     * 检测是否购买了课程
     */
    function isBuy($uid,$cid,$type,$usetype=0){
        switch ($type)
        {
            case 1:
                $tid = model('videoCourse')->where(['id'=>$cid])->value('teacher_id');
                break;
            case 2:
                $tid = model('liveCourse')->where(['id'=>$cid])->value('teacher_id');
                break;
        }
        $state=model('userCourse')->where(['cid'=>$cid,'type'=>$type,'uid'=>$uid])->find();
        $isInClassroom=$this->isInClassroom($cid,$type);
        if(isVip($uid) || $isInClassroom  || $state  || ($tid==getTeacherIdByUid(is_user_login())) || (getAdminAuthId(is_admin_login())==1)){
            return 1;
        }else{
            if($usetype==1){
                $this->redirect(url('index/course/info',array('id'=>hashids_encode($cid),'type'=>hashids_encode($type))));
                exit();
            }else{
                return false;
            }
        }
    }
    /**
     *下单时检测是否购买课程 课时ID
     */
    function checkBuied($uid,$cid,$type){
        $info=$this->getCouseInfo($cid,$type);
        if($buyInfo=db('userCourse')->where(['uid'=>$uid,'cid'=>$cid,'type'=>$type])->find()){
            if(strtotime("+".$info['youxiaoqi']."days",strtotime($buyInfo['addtime'])) >= time()){
                $this->error('您已经购买了此课程，请不要重复购买！');
            }
        }
        if(getTeacherIdByUid(is_user_login())==$info['teacher_id'] ){
            $this->error('您是本课程的教师或班级的创办者，无需购买，直接观看学习');
        }
        if(getAdminAuthId(is_admin_login())==1){
            $this->error('超级管理员，无需购买，直接观看学习');
        }
    }
    /**
     * php检查是否购买了课程  课时ID
     */
    public function checkBuyPhp($type,$sid){
        if($type==1){
            $sessionInfo=model('videoSection')->where(['id'=>$sid])->find();
            $tid = model('videoCourse')->where(['id'=>$sessionInfo['csid']])->value('teacher_id');
        }
        if($type==2){
            $sessionInfo=model('liveSection')->where(['id'=>$sid])->find();
            $tid = model('liveCourse')->where(['id'=>$sessionInfo['csid']])->value('teacher_id');
        }
        $state=model('userCourse')->where(['cid'=>$sessionInfo['csid'],'type'=>$type,'uid'=>is_user_login()])->find();
        $isInClassroom=$this->isInClassroom($sessionInfo['csid'],$type);
        if($isInClassroom || $sessionInfo['previewtimes']>0  || $state || ($sessionInfo['isfree']==1) || ($tid==getTeacherIdByUid(is_user_login())) || (getAdminAuthId(is_admin_login())==1 || isVip(is_user_login()))){
            return true;
        }else{
            $this->redirect(url('index/course/info',array('id'=>hashids_encode($sessionInfo['csid']),'type'=>hashids_encode($type))));
            exit();
        }
    }

    /**
     * 检查课程是否在购买的班级中
     */
    function isInClassroom($cid,$type){
        $id=$type.'-'.$cid;
        $myclassRoom=model('userCourse')->order('addtime desc')->where(['uid'=>is_user_login(),'type'=>3])->select();
        foreach ($myclassRoom as $k=> $value) {
            $cids= model('classroom')->where(['id'=>$myclassRoom[$k]['cid']])->value('cids');
            $cidsArr=(json_to_array($cids));
            if(in_array($id,$cidsArr)){
                return true;
                break;
            }
        }
    }
    /**
     * 增加积分
     */
    function addjifen($type,$uid){
        $data = model('system')->where('name', 'jifen')->find();
        $jifenArry=unserialize($data['value']);
        $res=db('jifen')->where(['uid'=>$uid,'type'=>$type,'addtime'=>date("Y-m-d",time())])->find();
        $userInfo=model('user')->where(['id'=>$uid])->find();
        if($res){
            if($res['times'] < $jifenArry[$type.'times']){
                db('jifen')->where(['id'=>$res['id']])->update(['jifen' =>$res['jifen']+$jifenArry[$type],'times'=>$res['times']+1]);
                $param['jifen']=$userInfo['jifen']+$jifenArry[$type];
                $param['id']=$uid;
                $this->update('user', $param, input('_verify', false));
            }
        }else{
            db('jifen')->insert(['uid'=>$uid,'type'=>$type,'jifen'=>$jifenArry[$type],'addtime'=>date("Y-m-d",time()),'times'=>1]);
            $param['jifen']=$userInfo['jifen']+$jifenArry[$type];
            $param['id']=$uid;
            $this->update('user', $param, input('_verify', false));
        }
    }
    /**
     * 购课增加积分
     */
    function addjifenByGouke($uid,$total){
        $data = model('system')->where('name', 'jifen')->find();
        $jifenArry=unserialize($data['value']);
        $userInfo=model('user')->where(['id'=>$uid])->find();
        db('jifen')->insert(['uid'=>$uid,'type'=>'gouke','jifen'=>round($jifenArry['gouke']*$total),'addtime'=>date("Y-m-d",time()),'times'=>1]);
        $param['jifen']=$userInfo['jifen']+round($jifenArry['gouke']*$total);
        $param['id']=$uid;
        $this->update('user', $param, input('_verify', false));
    }
}