<?php
namespace app\api\controller;
use app\common\extend\alipay\Alipay;
use app\common\extend\wechat\WechatPay;
use app\common\controller\IndexBase;
use app\common\extend\agora\RtcTokenBuilder;
use app\common\extend\agora\RtmTokenBuilder;
use app\common\extend\agora\AccessToken;
class Appcourse extends IndexBase
{
    /**
     * 课程搜索
     */
    function search(){
        $param = $this->request->param();
        $key='%'.$param['keywords'].'%';
        $videoCourseList=collection(model('videoCourse')->field(['id','cid','title','price','picture','type','teacher_id','youxiaoqi'])->order('sort_order asc,addtime desc')->where('title','like',$key)->where(['status'=>1,'is_top'=>1])->select())->toArray();
        $liveCourseList =collection(model('liveCourse')->field(['id','cid','title','price','picture','type','teacher_id','limit','youxiaoqi'])->order('sort_order asc,addtime desc')->where('title','like',$key)->where(['status'=>1,'is_top'=>1])->select())->toArray();
        foreach ($videoCourseList as $k=> $value) {
            $videoCourseList[$k]['stuNum']=getUserNum($videoCourseList[$k]['id'],1);
            $videoCourseList[$k]['picture']=formatUrl($videoCourseList[$k]['picture']);
            $videoCourseList[$k]['teacherName']=getTeacherName($videoCourseList[$k]['teacher_id']);
            $videoCourseList[$k]['teacherImg']=formatUrl(defaultAvatar(getAvatar(($videoCourseList[$k]['teacher_id']))));
            $videoCourseList[$k]['youxiaoqi']=youxiaoqi(($videoCourseList[$k]['youxiaoqi']));
            $videoCourseList[$k]['xueshi']=getCourseNum(($videoCourseList[$k]['id']),$videoCourseList[$k]['type']);
        }
        foreach ($liveCourseList as $i=> $value) {
            $liveCourseList[$i]['stuNum']=getUserNum($liveCourseList[$i]['id'],2);
            $liveCourseList[$i]['picture']=formatUrl($liveCourseList[$i]['picture']);
            $liveCourseList[$i]['teacherName']=getTeacherName(($liveCourseList[$i]['teacher_id']));
            $liveCourseList[$i]['teacherImg']=formatUrl(defaultAvatar(getAvatar(($liveCourseList[$i]['teacher_id']))));
            $liveCourseList[$i]['surplus']=$liveCourseList[$i]['limit']-getUserNum($liveCourseList[$i]['id'],$liveCourseList[$i]['type']);
            $liveCourseList[$i]['youxiaoqi']=youxiaoqi(($liveCourseList[$i]['youxiaoqi']));
            $liveCourseList[$i]['xueshi']=getCourseNum(($liveCourseList[$i]['id']),$liveCourseList[$i]['type']);
        }
        return  json_encode(array_merge($videoCourseList,$liveCourseList));
    }
    /**
     * 获取课程详细信息
     */
    function courseDetail(){
        $param=$this->request->param();
        switch ($param['type'])
        {
            case 1:
                model('videoCourse')->where(['id'=>$param['id']])->setInc('views');
                $courseinfo = model('videoCourse')->where(['id'=>$param['id']])->find();
                $courseinfo['isDaoQi']=$courseinfo['youxiaoqi']==0? false: get_course_last_time($courseinfo,4,$param['uid']);
                $courseinfo['keshi']=getCourseNum($courseinfo['id'],1);
                $courseinfo['userNum']=getUserNum($courseinfo['id'],1);
                $courseinfo['teacher']=getTeacherName($courseinfo['teacher_id']);
                $courseinfo['teacherAvatar']=formatUrl(defaultAvatar(getAvatar($courseinfo['teacher_id'])));
                $courseinfo['picture']= formatUrl($courseinfo['picture']);
                $courseinfo['progress']= round(100*getStuduedNum($param['id'],$param['type'],$param['uid'])/getCourseNum($param['id'],$param['type']));
                if(strstr($courseinfo['brief'],'oss-')){
                    $courseinfo['brief']=str_replace('src="//','src="https://',$courseinfo['brief']);
                }else{
                    $courseinfo['brief']= preg_replace_callback('/<[img|IMG].*?src=[\'| \"](?![http|https])(.*?(?:[\.png|\.gif|\.jpg]))[\'|\"].*?[\/]?>/',
                        function ($r) {
                            $str = 'https://'.$_SERVER['HTTP_HOST'].$r[1];
                            return str_replace($r[1], $str, $r[0]);
                        }, $courseinfo['brief']
                    );
                }
                $courseinfo['h5Url']=hashids_encode($param['id']).'/'.hashids_encode('1');
                $courseinfo['lasttime']=get_course_last_time($courseinfo,3,$param['uid']);
                $courseinfo['youxiaoqi']=youxiaoqi($courseinfo['youxiaoqi']);
                $courseinfo['cuxiao']=flashsale($courseinfo['id'],1,$courseinfo['price'],5);
                return json_encode($courseinfo) ;
            case 2:
                model('liveCourse')->where(['id'=>$param['id']])->setInc('views');
                $courseinfo = model('liveCourse')->where(['id'=>$param['id']])->find();
                $courseinfo['isDaoQi']=$courseinfo['youxiaoqi']==0? false: get_course_last_time($courseinfo,4,$param['uid']);
                $courseinfo['keshi']=getCourseNum($courseinfo['id'],2);
                $courseinfo['userNum']=getUserNum($courseinfo['id'],2);
                $courseinfo['teacher']=getTeacherName($courseinfo['teacher_id']);
                $courseinfo['teacherAvatar']=formatUrl(defaultAvatar(getAvatar($courseinfo['teacher_id'])));
                $courseinfo['progress']= round(100*getStuduedNum($param['id'],$param['type'],$param['uid'])/getCourseNum($param['id'],$param['type']));
                if(strstr($courseinfo['brief'],'oss-')){
                    $courseinfo['brief']=str_replace('src="//','src="https://',$courseinfo['brief']);
                }else{
                    $courseinfo['brief']= preg_replace_callback('/<[img|IMG].*?src=[\'| \"](?![http|https])(.*?(?:[\.png|\.gif|\.jpg]))[\'|\"].*?[\/]?>/',
                        function ($r) {
                            $str = 'https://'.$_SERVER['HTTP_HOST'].$r[1];
                            return str_replace($r[1], $str, $r[0]);
                        }, $courseinfo['brief']
                    );
                }
                $courseinfo['picture']= formatUrl($courseinfo['picture']);
                $courseinfo['h5Url']=hashids_encode($param['id']).'/'.hashids_encode('2');
                $courseinfo['lasttime']=get_course_last_time($courseinfo,3,$param['uid']);
                $courseinfo['youxiaoqi']=youxiaoqi($courseinfo['youxiaoqi']);
                $courseinfo['cuxiao']=flashsale($courseinfo['id'],2,$courseinfo['price'],5);
                return (json_encode($courseinfo)) ;
        }

    }
    /**
     * 获取一级分类
     */
    function getTopCategory(){
        $topCategory=collection(model('courseCategory')->field('id,appname')->where(['pid'=>0])->order('sort_order asc')->select())->toArray();
        foreach ($topCategory as $k=> $value) {
            $subCategory=collection(model('courseCategory')->field('id,appname')->where(['pid'=>$topCategory[$k]['id']])->order('sort_order asc')->select())->toArray();
            foreach ($subCategory as $i=> $value) {
                $subSubCategory=collection(model('courseCategory')->field('id,appname')->where(['pid'=>$subCategory[$i]['id']])->order('sort_order asc')->select())->toArray();
                if(!empty($subSubCategory)){
                    $topCategory[$k]['subCategory']=$subCategory;
                }
            }
        }
        return  json_encode($topCategory);
    }
    /**
     * 获取二级分类
     */
    function getSubCategory(){
        $param = $this->request->param();
        $where=[];
        if($param['pid']>0){
            $where['pid']=$param['pid'];
        }
        $category=collection(model('courseCategory')->where($where)->field('id,appname as name')->order('sort_order asc')->select())->toArray();
        array_unshift($category,['id'=>0,'name'=>'全部']);
        return  json_encode($category);
    }
    /**
     * 获取全部分类
     */
    function getAllCategory(){
        $allCategory=collection(model('courseCategory')->field('id,appname as name')->order('sort_order asc')->select())->toArray();
        return  json_encode($allCategory);
    }
    /**
     * 获取课程列表
     */
    function getCourse(){
        $param = $this->request->param();
        $category=model('courseCategory')->order('sort_order asc')->select();
        $coursemodel=new \app\index\controller\Course;
        $where=$coursemodel->getCateId($category,$param);
        $videoCourseList=collection(model('videoCourse')->field(['id','cid','title','price','picture','type','teacher_id','youxiaoqi'])->order('sort_order asc,addtime desc')->where('cid','in',$where)->where(['status'=>1,'is_top'=>1])->select())->toArray();
        $liveCourseList =collection(model('liveCourse')->field(['id','cid','title','price','picture','type','teacher_id','limit','youxiaoqi'])->order('sort_order asc,addtime desc')->where('cid','in',$where)->where(['status'=>1,'is_top'=>1])->select())->toArray();
        foreach ($videoCourseList as $k=> $value) {
            $videoCourseList[$k]['stuNum']=getUserNum($videoCourseList[$k]['id'],1);
            $videoCourseList[$k]['picture']=formatUrl($videoCourseList[$k]['picture']);
            $videoCourseList[$k]['teacherName']=getTeacherName($videoCourseList[$k]['teacher_id']);
            $videoCourseList[$k]['teacherImg']=formatUrl(defaultAvatar(getAvatar(getUidFromTid(($videoCourseList[$k]['teacher_id'])))));
            $videoCourseList[$k]['youxiaoqi']=youxiaoqi(($videoCourseList[$k]['youxiaoqi']));
            $videoCourseList[$k]['xueshi']=getCourseNum(($videoCourseList[$k]['id']),$videoCourseList[$k]['type']);
        }
        foreach ($liveCourseList as $i=> $value) {
            $liveCourseList[$i]['stuNum']=getUserNum($liveCourseList[$i]['id'],2);
            $liveCourseList[$i]['picture']=formatUrl($liveCourseList[$i]['picture']);
            $liveCourseList[$i]['teacherName']=getTeacherName(($liveCourseList[$i]['teacher_id']));
            $liveCourseList[$i]['teacherImg']=formatUrl(defaultAvatar(getAvatar(getUidFromTid(($liveCourseList[$i]['teacher_id'])))));
            $liveCourseList[$i]['surplus']=$liveCourseList[$i]['limit']-getUserNum($liveCourseList[$i]['id'],$liveCourseList[$i]['type']);
            $liveCourseList[$i]['youxiaoqi']=youxiaoqi(($liveCourseList[$i]['youxiaoqi']));
            $liveCourseList[$i]['xueshi']=getCourseNum(($liveCourseList[$i]['id']),$liveCourseList[$i]['type']);
        }
        return  json_encode(array_merge($videoCourseList,$liveCourseList));
    }
    /**
     * 是否收藏课程
     */
    public function isfavourite(){
        $param = $this->request->param();
        $res=db('favourite')->where($param)->find();
        if(!empty($res)){
            echo (json_encode(['code'=>'0','msg'=>$param]));
        }else{
            echo (json_encode(['code'=>'1','msg'=>$param]));
        }
    }
    /**
     * 收藏课程
     */
    public function favourite(){
        $param = $this->request->param();
        if(db('favourite')->insert($param)){
            echo (json_encode(['code'=>'1','msg'=>'收藏成功']));
        }else{
            echo (json_encode(['code'=>'0','msg'=>$this->error($this->errorMsg)]));
        }
    }
    /**
     * 取消收藏课程
     */
    public function unfavourite()
    {
        $param = $this->request->param();
        if (db('favourite')->where($param)->delete()) {
            echo(json_encode(['code' => '1', 'msg' => '取消收藏成功']));
        } else {
            echo(json_encode(['code' => '0', 'msg' => $this->error($this->errorMsg)]));
        }
    }

    /**
     * 获取课程课时列表
     */
    function getSection(){
        $param=$this->request->param();
        switch ($param['type'])
        {
            case 1:
                $section=model('videoCourse')->getSection($param['id']);
                break;
            case 2:
                $section=model('liveCourse')->getSection($param['id']);
                break;
        }
        foreach ($section as $k=> $value) {
            $sectionList[$k]['id']=$value['id'];
            $sectionList[$k]['isfree']=$value['isfree'];
            $sectionList[$k]['head']=$value['title'];
            $sectionList[$k]['stype']=$value['sectype'];
            $sectionList[$k]['playtimes']=$value['playtimes'];
            $sectionList[$k]['paperid']=$value['paperid'];
            $sectionList[$k]['isstudy']=isStudyBySidApp($param['id'],$value['id'],$param['type'],$param['uid']);
            if($param['type']==2){
                $sectionList[$k]['starttime']=$section[$k]['starttime'];
                $sectionList[$k]['liveStatus']=getLiveStatus($section[$k]['starttime'],$section[$k]['duration'],'app');
                $sectionList[$k]['isLiveStart']=isLiveStart($section[$k]['starttime']);
                $sectionList[$k]['isLiveOver']=isLiveOver($section[$k]['starttime'],$section[$k]['duration']);
            }
            if($section[$k]['sectype']==4){
                $sectionList[$k]['ispass']=isExamPassApp($value['id'],$param['id'],$param['type'],$value['paperid'],$param['uid']);
            }
            if(!empty($section[$k]['section'])){
                foreach ($section[$k]['section'] as $i=> $value2) {
                    $sectionList[$k]['section'][$i]['id']=$section[$k]['section'][$i]['id'];
                    $sectionList[$k]['section'][$i]['isfree']=$section[$k]['section'][$i]['isfree'];
                    $sectionList[$k]['section'][$i]['head']=$section[$k]['section'][$i]['title'];
                    $sectionList[$k]['section'][$i]['stype']=$section[$k]['section'][$i]['sectype'];
                    $sectionList[$k]['section'][$i]['playtimes']=$section[$k]['section'][$i]['playtimes'];
                    $sectionList[$k]['section'][$i]['paperid']=$section[$k]['section'][$i]['paperid'];
                    $sectionList[$k]['section'][$i]['isstudy']=isStudyBySidApp($param['id'],$section[$k]['section'][$i]['id'],$param['type'],$param['uid']);
                    if($section[$k]['section'][$i]['sectype']==4){
                        $sectionList[$k]['section'][$i]['ispass']=isExamPassApp($value2['id'],$param['id'],$param['type'],$value2['paperid'],$param['uid']);
                    }
                    if($param['type']==2){
                        $sectionList[$k]['section'][$i]['starttime']=$section[$k]['section'][$i]['starttime'];
                        $sectionList[$k]['section'][$i]['liveStatus']=getLiveStatus($section[$k]['section'][$i]['starttime'],$section[$k]['section'][$i]['duration'],'app');
                        $sectionList[$k]['section'][$i]['isLiveStart']=isLiveStart($section[$k]['section'][$i]['starttime']);
                        $sectionList[$k]['section'][$i]['isLiveOver']=isLiveOver($section[$k]['section'][$i]['starttime'],$section[$k]['section'][$i]['duration']);
                    }
                }
            }else{
                $sectionList[$k]['section']='';
            }
        }
        return (json_encode($sectionList)) ;
    }
    /**
     * 获取课时评论
     */
    function getComments(){
        $param=$this->request->param();
        $where['cid']=$param['cid'];
        $where['sid']=$param['sid'];
        $comment=model('comment')->where($where)->limit(30)->order('addtime desc')->select();
        foreach ($comment as $k=> $value) {
            $comment[$k]['userName']=getUserName($comment[$k]['uid']);
            if(!empty($comment[$k]['ruid'])){
                $comment[$k]['ruid']=getUserName( $comment[$k]['ruid']);
            }
        }
        return (json_encode($comment)) ;
    }
    /**
     * 添加评论
     */
    public function addComment(){
        $param = $this->request->param();
        $param['addtime']=date("Y-m-d H:i:s",time());
        $isBuy=$this->isBuy($param['uid'],$param['cid'],$param['type']);
        if($isBuy){
            if(!model('comment')->where(['sid'=>$param['sid'],'uid'=>$param['uid']])->find()){
                if ($id=model('comment')->insertGetId($param)){
                    echo (json_encode(['code'=>'1','id'=>$id]));
                }else{
                    echo (json_encode(['code'=>'0','msg'=>$param]));
                }
            }else{
                echo (json_encode(['code'=>'0','msg'=>'请不要重复评论！']));
            }
        }else{
            echo (json_encode(['code'=>'0','msg'=>'抱歉，只能评论已经购买的课程']));
        }
    }
    /**
     * 获取课时详细信息
     */
    function sectionDetail(){
        $param=$this->request->param();
        if($param['type']==1){
            $sectionInfo=model('videoSection')->where(['id'=>$param['sid']])->find();
            $sectionInfo['islocked']=false;
            $islock=model('videoCourse')->where(['id'=>$param['cid']])->value('islock');
            if($islock){
                $previousId=model('videoSection')->where('id','lt',$param['sid'])->where(['csid'=>$param['cid'],'ischapter'=>0])->order('id','desc')->value('id');
                if($previousId){
                    if(db('learned')->where(['sid'=>$previousId,'type'=>1,'uid'=>$param['uid'],'status'=>1])->find()){
                        $sectionInfo['islocked']=false;
                    }else{
                        $sectionInfo['islocked']=true;
                    }
                }
            }
            if($sectionInfo['sectype']=='1'){
                if($sectionInfo['platform']=='aliyun'){
                    $videoUrls = (action('index/course/getPlayInfo',['videoid'=>$sectionInfo['videoid']]));
                    foreach ($videoUrls as $k=> $value) {
                        $sectionInfo['videoUrl'.$k]=$videoUrls[$k]['PlayURL'];
                    }
                }
                if($sectionInfo['platform']=='local'){
                    $sectionInfo['videoUrl0']=is_https().get_domain().'/upload/video/'.$sectionInfo['localvideo'];
                }
            }
            if($sectionInfo['sectype']=='3'){
                if(!strpos($sectionInfo['document'], '.oss-cn-')){
                    $sectionInfo['document']= preg_replace_callback('/<[img|IMG].*?src=[\'| \"](?![http|https])(.*?(?:[\.gif|\.jpg]))[\'|\"].*?[\/]?>/', function ($r) {
                        $str = is_https().$_SERVER['HTTP_HOST'].$r[1];
                        return str_replace($r[1], $str, $r[0]);
                    },$sectionInfo['document']);
                }else{
                    $sectionInfo['document']=str_replace("//","https://",$sectionInfo['document']);
                }
            }
        }
        if($param['type']==2){
            $sectionInfo=model('liveSection')->where(['id'=>$param['sid']])->find();
            $sectionInfo['islocked']=false;
            $islock=model('liveCourse')->where(['id'=>$param['cid']])->value('islock');
            if($islock){
                $previousId=model('liveSection')->where('id','lt',$param['sid'])->where(['csid'=>$param['cid'],'ischapter'=>0])->order('id','desc')->value('id');
                if($previousId){
                    if(db('learned')->where(['sid'=>$previousId,'type'=>2,'uid'=>$param['uid'],'status'=>1])->find()){
                        $sectionInfo['islocked']=false;
                    }else{
                        $sectionInfo['islocked']=true;
                    }
                }
            }else {
                if ($sectionInfo['replaplatform'] == 'aliyun') {
                    $videoUrls = (action('index/course/getPlayInfo', ['videoid' => $sectionInfo['videoid']]));
                    foreach ($videoUrls as $k => $value) {
                        $sectionInfo['videoUrl' . $k] = $videoUrls[$k]['PlayURL'];
                    }
                }
                if ($sectionInfo['replaplatform'] == 'local') {
                    $sectionInfo['videoUrl0'] = is_https() . get_domain() . '/upload/video/' . $sectionInfo['localvideo'];
                }
                if ($sectionInfo['replaplatform'] == 'yunluzhi') {
                    $sectionInfo['videoUrl0'] =$sectionInfo['replayurl'];
                }
                if ($sectionInfo['replaplatform'] == 'share') {
                    $sectionInfo['videoUrl0'] = $sectionInfo['shareurl'];
                }
            }
        }
        return json_encode($sectionInfo) ;
    }
    /**
     * 获取声网token和参数
     */
    function getRtmParam(){
        $post=input('post.');
        $info=$this->get_site_info(4);
        $sectionInfo=model('liveSection')->where(['id'=>$post['sid']])->find();
        $uuid=$sectionInfo['room_id'].$post['uid'];
        $token=$this->RtmTokenBuilder($info['agoraAppid'],$info['agoraRestfulKey'],$uuid);
        if($token){
            $restemp['role']=2;
            $restemp['token']=$token;
            $restemp['channelId']=$sectionInfo['room_id'];
            $restemp['agoraAppid']=$info['agoraAppid'];
            $restemp['title']=$sectionInfo['title'];
            $restemp['duration']=$sectionInfo['duration'];
            $restemp['live_type']=$sectionInfo['live_type'];
            $restemp['uiMode']=$sectionInfo['type']==1?'dark':'light';
            $restemp['userName']= getUserName($post['uid']);
            $restemp['userUuid']=$uuid;
            $restemp['code']=0;
        }else{
            $restemp['code']=1;
            $restemp['msg']='获取token失败';
        }
        echo json_encode($restemp);
    }
    function RtmTokenBuilder($appID,$appCertificate,$uid){
        $role = RtmTokenBuilder::RoleRtmUser;
        $expireTimeInSeconds = 3600;
        $currentTimestamp = (new \DateTime("now", new \DateTimeZone('UTC')))->getTimestamp();
        $privilegeExpiredTs = $currentTimestamp + $expireTimeInSeconds;
        $token = RtmTokenBuilder::buildToken($appID, $appCertificate, $uid, $role, $privilegeExpiredTs);
        return $token;
    }
    /**
     * 获取拍乐云参数
     */
    function getPanoParam(){
        $post=input('post.');
        $sectionInfo=model('liveSection')->where(['id'=>$post['sid']])->find();
        $info=$this->get_site_info(5);
        $userId='202106181217'.$post['uid'];
        $limit = model('liveCourse')->where(['id'=>$post['cid']])->value('limit');
        $restemp['token']=$this->getPanoToken($info['panoAppid'],$info['panoSecret'],$sectionInfo['room_id'],$userId,$limit,time());
        $restemp['appId']=$info['panoAppid'];
        $restemp['channelId']=$sectionInfo['room_id'];
        $restemp['userId']=$userId;
        $restemp['userName']=getUserName($post['uid']);
        $restemp['title']=$sectionInfo['title'];
        echo json_encode($restemp);
    }
    /**
     * 获取拍乐云token
     */
    function getPanoToken($appId,$appSecret,$channelId,$userId,$size,$timestamp){
        $version = "02";
        $joinParams= ["channelId" => $channelId, "userId" => $userId, "size" => $size, "delayClose" => 0];
        $params = base64_encode(json_encode($joinParams));
        $signData = $version .$appId  . $timestamp . $params;
        $signature = base64_encode(hash_hmac("sha256", $signData, $appSecret, true));
        $token= $version . "." . $appId . "." . $timestamp . "." . $params . "." . $signature;
        return $token;
    }
    /**
     * 拍乐云获取用户名
     */
    function getUserName(){
        $post=input('post.');
        if(strstr($post['uid'], '198107251215')){
            $res['role']="教师";
        }else{
            $res['role']="";
        }
        $uid=substr($post['uid'],12);
        $res['username']= getUserName($uid);
        $res['code']=0;
        echo json_encode($res);
    }
    /**
     * 获取教师信息
     */
    function getTeacherInfo(){
        $param=$this->request->param();
        $info= model('admin')->field('id,uid,sign,username,showname')->where('id',$param['id'])->find();
        if(empty($info['showname'])){
            $info['showname']=$info['username'];
        }
        $info['picture']=formatUrl(defaultAvatar(getAvatar($info['uid'])));
        return (json_encode($info)) ;
    }
    /**
     * 懂了吗
     */
    function donglema(){
        $param = $this->request->param();
        if(db('understand')->insert(['roomid'=>$param['channel'],'understand'=>$param['understand'],'uid'=>$param['uid'],'cishu'=>$param['cishu'],'addtime'=>date('y-m-d H:i:s',time())])){
            $res['code']=0;
        }else{
            $res['code']=1;
        };
        echo json_encode($res);
    }
    /**
     * 点名答到
     */
    function dianming(){
        $param = $this->request->param();
        $sectionInfo=model('liveSection')->where(['room_id'=>$param['channel']])->find();
        if(db('dianming')->insert(['cid'=>$sectionInfo['csid'],'sid'=>$sectionInfo['id'],'uid'=>$param['uid'],'times'=>$param['cishu'],'addtimes'=>date('y-m-d H:i:s',time())])){
            $res['code']=0;
        }else{
            $res['code']=1;
        };
        $res['code']=0;
        echo json_encode($res);
    }
    /**
     * 写入课程学习记录
     */
    public function studied(){
        $param = $this->request->param();
        $res=db('learned')->where(['cid'=>$param['cid'],'sid'=>$param['sid'],'type'=>$param['type'],'uid'=>$param['uid']])->find();
        if(!empty($res)){
            if(db('learned')->where('id', $res['id'])->update(['status'=>1])){
                $res['status']=1;
                echo json_encode($res);
            }else{
                $res['status']=3;
                echo json_encode($res);
            }
        }
        if(empty($res)){
            if(db('learned')->insert(['cid'=>$param['cid'],'sid'=>$param['sid'],'type'=>$param['type'],'uid'=>$param['uid'],'status'=>1,'addtime'=>date('y-m-d H:i:s',time())])){
                $res['status']=1;
                echo json_encode($res);
            }
        }
    }
    /**
     * 写入正在学习章节
     */
    public function nowStudy(){
        $param = $this->request->param();
        $res=db('learned')->where(['cid'=>$param['cid'],'sid'=>$param['sid'],'type'=>$param['type'],'uid'=>$param['uid']])->find();
        if(empty($res)){
            db('learned')->insert(['cid'=>$param['cid'],'sid'=>$param['sid'],'type'=>$param['type'],'uid'=>$param['uid'],'laststudy'=>$param['sid'],'status'=>0,'duration'=>0,'addtime'=>date('y-m-d H:i:s',time())]);
        }
        if(!empty($res)){
            db('learned')->where('id', $res['id'])->update(['laststudy'=>$param['sid'],'addtime'=>date('y-m-d H:i:s',time())]);
        }
    }
    /**
     * 写入正在学习时长
     */
    public function addduration(){
        $param = $this->request->param();
        $res=db('learned')->where(['cid'=>$param['cid'],'sid'=>$param['sid'],'type'=>$param['type'],'uid'=>$param['uid']])->find();
        if($res){
            db('learned')->where('id', $res['id'])->update(['laststudy'=>$param['sid'],'duration'=>$res['duration']+$param['duration'],'seek'=>$param['seek'],'addtime'=>date('y-m-d H:i:s',time())]);
        }
    }
    /**
     * 检测是否购买课程
     */
     function isAppBuy(){
         $param=$this->request->param();
         if(empty($param['uid'])){
             return (json_encode($res['code']=1)) ;
         }else{
             $state=model('userCourse')->where(['cid'=>$param['cid'],'type'=>$param['type'],'uid'=>$param['uid'],'state'=>1])->find();
             $isInClassroom=$this->appisInClassroom($param['cid'],$param['type'],$param['uid']);
             if($isInClassroom  || $state || isVip($param['uid'])){
                 $res['code']=0;
             }else{
                 $res['code']=1;
             }
             return json_encode($res) ;
         }
     }
    /**
     * 检查课程是否在购买的班级中
     */
    function appisInClassroom($cid,$type,$uid){
        $id=$type.'-'.$cid;
        $myclassRoom=model('userCourse')->order('addtime desc')->where(['uid'=>$uid,'type'=>3])->select();
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
     * 创建订单
     */
    public function createOrder(){
        $param=$this->request->param();
        $courseinfo=$this->getCouseInfo($param['id'],$param['type']);
        $order['title']=$courseinfo['title'];
        $order['ctype']=$courseinfo['type'];
        $order['state']=0;
        $order['addtime']=date('Y-m-d H:i:s', time());
        $order['uid']=$param['uid'];
        if($courseinfo['type']==3){
            $order['tid']=$courseinfo['headteacher'];
        }else{
            $order['tid']=$courseinfo['teacher_id'];
        }
        $order['paytype']=$param['payment'];
        $order['cid']=$param['id'];
        $order['orderid']= date('Ymdhms').rand(1000, 9999);
        $order['total']= $param['price'];
        $courseinfo['price']=$param['price'];
        if($info=model('order')->where(['ctype'=>$courseinfo['type'],'cid'=>$param['id'],'uid'=>$param['uid']])->find()){
            $order['id']=$info['id'];
            $res=$this->update('order',$order);
        }else{
            $res=$this->insert('order',$order);
        }
        if($res === true){
            if($order['total']==0){
                $this->update('order', ['state'=>1,'orderid'=>$order['orderid']], $rule = true, $field = true, $key = 'orderid');
                if($info=model('userCourse')->where(['uid'=>$order['uid'],'cid'=>$order['cid'],'type'=>$order['ctype']])->find()){
                    $res2= model('userCourse')->where('id',$info['id'])->setField('addtime', date('Y-m-d H:i:s', time()));
                }else{
                    $res2=$this->insert('userCourse',['uid'=>$order['uid'],'cid'=>$order['cid'],'type'=>$order['ctype'],'state'=>1,'addtime'=>date('Y-m-d H:i:s', time())]);
                }
                if($res2 === true){
                    $this->addjifenByGouke($order['uid'],$order['total']);
                    if($order['ctype']==4){
                        $sentMessage= '成功购买VIP会员';
                    }else{
                        $sentMessage= '成功购买课程'.getCourseName($order['cid'],$order['ctype']);
                    }
                    $this->sentMessage(0,$order['uid'],0,$sentMessage,$order['ctype'],$order['cid']);
                    return (json_encode(['code'=>0,'msg'=>'添加成功'])) ;
                }else{
                    return (json_encode(['code'=>1,'msg'=>'添加失败'])) ;
                }
            }else{
                return $this->payment($order);
            }
        }
    }
    /**
     * 检测支付结果
     */
    public function checkPay(){
        $param = $this->request->param();
        $orderInfo = model('order')->where(['orderid'=>$param['orderid']])->value('state');
        if($orderInfo==1){
            $res['code']=1;
        }else{
            $res['code']=0;
        }
        return json_encode($res) ;
    }
    /**
     * 获取订单信息
     */
    function getOrderInfo(){
        $param=$this->request->param();
        $order=model('order')->where('id',$param['id'])->find();
        $order['courseinfo']=$this->getCouseInfo($order['cid'],$order['ctype']);
        return json_encode($order);
    }
    /**
     * 免费课程添加
     */
    public function buyFree(){
        $param=$this->request->param();
        if($this->insert('userCourse',['uid'=>$param['uid'],'cid'=>$param['cid'],'type'=>$param['type'],'state'=>1,'addtime'=>date('Y-m-d h:i:s', time())],$rule = false)===true){
            return json_encode(['status'=>1,'msg'=>'添加成功']);
        }else{
            return json_encode(['status'=>0,'msg'=>$this->errorMsg]);
        }
    }

    /**
     * 订单支付
     */
    function payment($order){
        if($order['paytype']=='alipay'){
            return  $this->alipay($order);
        }
        if($order['paytype']=='wxpay'){
            return  $this->wxpay($order);
        }
        if($order['paytype']=='yuepay'){
            return  $this->yuepay($order);
        }
    }
    function repay(){
        $param=$this->request->param();
        $order=model('order')->where('id',$param['id'])->find();
        $this->update('order', ['id'=>$param['id'],'orderid'=>date('Ymdhms')], $rule = true, $field = true, $key = 'id');
        $order['title']=$param['title'];
        if($param['paytype']=='alipay'){
            $order['paytype']='alipay';
            return  $this->alipay($order);
        }
        if($param['paytype']=='wxpay'){
            $order['paytype']='wxpay';
            return  $this->wxpay($order);
        }
        if($param['paytype']=='yuepay'){
            $order['paytype']='yuepay';
            return  $this->yuepay($order);
        }
    }
    /**
     * 微信支付
     */
    function wxpay($order){
        $data=$this->wechatAppConfig();
        $name=$order['title'];
        $payData = [
            'body'=>$order['title'],
            'out_trade_no'=>$order['orderid'],
            'total_fee'=> $order['total']*100,
            'trade_type'=> 'APP',
            'wap_url'=>is_https().get_domain(),
            'wap_name'=>$name,
            'notify_url'=>get_domain(). '/index/course/notifywechat/',
        ];
        $wechatPay = new WechatPay($data);
        $info = $wechatPay->unifiedOrder($payData);
        $res=$wechatPay->getAppParam($info['prepay_id']);
        $res['orderid']=$order['orderid'];
        return (json_encode($res)) ;
    }
    /**
     * 支付宝支付
     */
    function alipay($order){
        $payData = [
            'order_no' =>$order['orderid'],
            'order_price' => $order['total'],
            'subject' => $order['title'],
            'notify_url' =>is_https().get_domain(). '/index/course/notifyalipay/',
            'return_url'=>is_https().get_domain(). '/index/course/return_url/',
        ];
        $alipay = new Alipay($this->alipayConfig());
        $info = $alipay->placeApp($payData);
        $info['orderid']=$order['orderid'];
        return (json_encode($info)) ;
    }
    /**
     * 账户余额支付
     */
    function yuepay($orderInfo){
        $userInfo=model('user')->where(['id'=>$orderInfo['uid']])->find();
        if($userInfo['yue']<$orderInfo['total']){
            return (json_encode(['code'=>1,'msg'=>'余额不足'])) ;
        }
        $this->update('order', ['profit'=>$orderInfo['total']*config('bili'),'state'=>1,'payorder'=>'','orderid'=>$orderInfo['orderid']], $rule = true, $field = true, $key = 'orderid');
        //添加到我的课程中，若存在则更新时间
        if($info=model('userCourse')->where(['uid'=>$orderInfo['uid'],'cid'=>$orderInfo['cid'],'type'=>$orderInfo['ctype']])->find()){
            $res=model('userCourse')->where('id',$info['id'])->setField('addtime', date('Y-m-d H:i:s', time()));
        }else{
            $res=$this->insert('userCourse',['uid'=>$orderInfo['uid'],'cid'=>$orderInfo['cid'],'type'=>$orderInfo['ctype'],'state'=>1,'addtime'=>date('Y-m-d H:i:s', time())]);
        }
        //教师提成分配，3为班级的教师盈利分配
        if($orderInfo['type']==3){
            $this->batchAddProfit($orderInfo['cid'],$orderInfo['total']);
        }else{
            if($profit=db('profit')->where(['tid'=>$orderInfo['tid']])->find()){
                db('profit')->where(['tid'=>$orderInfo['tid']])->update(['profit'=>$orderInfo['total']*config('bili')+$profit['profit']]);
            }else{
                db('profit')->insert(['tid'=>$orderInfo['tid'],'profit'=>$orderInfo['total']*config('bili')]);
            }
        }
        //分销提成
        model('distribution')->distribution($orderInfo['uid'],$orderInfo['total'],$orderInfo['cid'],$orderInfo['type']);
        if($res){
            db('user')->where(['id'=>$orderInfo['uid']])->update(['yue'=>$userInfo['yue']-$orderInfo['total']]);
            $this->addjifenByGouke($orderInfo['uid'],$orderInfo['total']);
            $this->coupon('buy',$orderInfo['uid']);
            return (json_encode(['code'=>0,'msg'=>'支付成功','orderid'=>$orderInfo['orderid']])) ;
        }
    }
    /**
     * 获取班级列表
     */
    function getClassList(){
        $classroom = model('classroom')->where('is_top',1)->order('sort_order asc,id desc')->limit(5)->select();
        foreach ($classroom as $k=> $value) {
            $classroom[$k]['picture']=formatUrl($classroom[$k]['picture']);
            $classroom[$k]['stuNum']=getUserNum($classroom[$k]['id'],3);
            $classroom[$k]['CourseNum']=getClassroomCourseNum($classroom[$k]['id']);
        }
        return json_encode($classroom);
    }
    /**
     * 获取班级所有列表
     */
    function getAllClassList(){
        $classroom = model('classroom')->where('is_top',1)->order('sort_order asc,id desc')->select();
        foreach ($classroom as $k=> $value) {
            $classroom[$k]['picture']=formatUrl($classroom[$k]['picture']);
            $classroom[$k]['stuNum']=getUserNum($classroom[$k]['id'],3);
            $classroom[$k]['CourseNum']=getClassroomCourseNum($classroom[$k]['id']);
        }
        return json_encode($classroom);
    }
    /**
     * 获取班级详情
     */
    function classDetail(){
        $param = $this->request->param();
        $classroominfo = model('classroom')->where(['id'=>$param['id']])->find();
        $classroominfo['picture']= formatUrl($classroominfo['picture']);
        $classroominfo['cuxiao']=flashsale($classroominfo['id'],3,$classroominfo['price'],5);
        $classroominfo['lasttime']=get_class_last_time($classroominfo,3,$param['uid']);
        $classroominfo['isDaoQi']=$classroominfo['youxiaoqi']==0? false: get_class_last_time($classroominfo,2,$param['uid']);
        $classroominfo['youxiaoqi']=youxiaoqi($classroominfo['youxiaoqi']);
        $cids=json_to_array($classroominfo['cids']);
        model('classroom')->where(['id'=>$param['id']])->setInc('views');
        foreach($cids as $k=>$v) {
            $ids=explode('-',$cids[$k],2);
            $ids[0]==1?$videoIds[]=$ids[1]:$liveIds[]=$ids[1];
            $ids[0]==1?$videoStudied[$ids[1]]=getStuduedNum($ids[1],1,$param['uid']):$liveStudied[$ids[1]]=getStuduedNum($ids[1],2,$param['uid']);
            $ids[0]==1?$videoCourseNum[$ids[1]]=getCourseNum($ids[1],1):$liveCourseNum[$ids[1]]=getCourseNum($ids[1],2);
        }
        if(strstr($classroominfo['brief'],'oss-')){
            $classroominfo['brief']=str_replace('src="//','src="https://',$classroominfo['brief']);
        }else{
            $classroominfo['brief']= preg_replace_callback('/<[img|IMG].*?src=[\'| \"](?![http|https])(.*?(?:[\.png|\.gif|\.jpg]))[\'|\"].*?[\/]?>/',
                function ($r) {
                    $str = 'https://'.$_SERVER['HTTP_HOST'].$r[1];
                    return str_replace($r[1], $str, $r[0]);
                }, $classroominfo['brief']
            );
        }
        $classroominfo['userNum']= getUserNum($classroominfo['id'],3);
        $classroominfo['h5Url']=hashids_encode($param['id']);
        $classroominfo['courseNum']=getClassroomCourseNum($classroominfo['id']);
        $classroominfo['videoCourse']=model('videoCourse')->order('sort_order asc,addtime desc')->whereIn('id',$videoIds)->select();
        $classroominfo['liveCourse']=model('liveCourse')->order('sort_order asc,addtime desc')->whereIn('id',$liveIds)->select();
        $classroominfo['AllProgress']=round(100*(array_sum($videoStudied)+array_sum($liveStudied))/(array_sum($videoCourseNum)+array_sum($liveCourseNum)),2);
        foreach( $classroominfo['videoCourse'] as $k=>$v) {
            $classroominfo['videoCourse'][$k]['picture']=formatUrl($classroominfo['videoCourse'][$k]['picture']);
            $classroominfo['videoCourse'][$k]['tname']=getTeacherName($classroominfo['videoCourse'][$k]['teacher_id']);
            $classroominfo['videoCourse'][$k]['xueshi']=getCourseNum($classroominfo['videoCourse'][$k]['id'],1);
            $classroominfo['videoCourse'][$k]['stuNum']=getUserNum($classroominfo['videoCourse'][$k]['id'],1);
            $classroominfo['videoCourse'][$k]['progress']=round(100*getStuduedNum($classroominfo['videoCourse'][$k]['id'],1,$param['uid'])/getCourseNum($classroominfo['videoCourse'][$k]['id'],1));
            $classroominfo['videoCourse'][$k]['avatar']=formatUrl(defaultAvatar(getAvatar(getUidFromTid($classroominfo['videoCourse'][$k]['teacher_id']))));
        }
        foreach( $classroominfo['liveCourse'] as $k=>$v) {
            $classroominfo['liveCourse'][$k]['picture']=formatUrl($classroominfo['liveCourse'][$k]['picture']);
            $classroominfo['liveCourse'][$k]['tname']=getTeacherName($classroominfo['liveCourse'][$k]['teacher_id']);
            $classroominfo['liveCourse'][$k]['xueshi']=getCourseNum($classroominfo['liveCourse'][$k]['id'],2);
            $classroominfo['liveCourse'][$k]['stuNum']=getUserNum($classroominfo['liveCourse'][$k]['id'],3);
            $classroominfo['liveCourse'][$k]['progress']=round(100*getStuduedNum($classroominfo['liveCourse'][$k]['id'],2,$param['uid'])/getCourseNum($classroominfo['liveCourse'][$k]['id'],2));
            $classroominfo['liveCourse'][$k]['avatar']=formatUrl(defaultAvatar(getAvatar(getUidFromTid($classroominfo['liveCourse'][$k]['teacher_id']))));

        }
        return json_encode($classroominfo);
    }


}