<?php

namespace app\index\controller;

use app\common\controller\IndexBase;

class Forum extends IndexBase
{
    protected function _initialize()
    {
        parent::_initialize();
        $plate=model('forumPlate')->order('sort_order desc')->where('isshow',1)->select();
        $this->assign('plate',$plate);
    }
    function index(){
        $param=$this->request->param();
        $where = [];
        $order=[];
        if (isset($param['pid'])) {
            $where['pid'] = $param['pid'];
            $platename=model('forumPlate')->where('id',$param['pid'])->value('name');
            $platename='<span class="Y-axis">></span>'.$platename;
        }
        $toptopic=model('forumTopic')->where('istop=1')->order('addtime desc')->select();
        $topic=model('forumTopic')->where($where)->order('addtime desc,isessence desc')->paginate(15);
        $top10=model('forumTopic')->order(['replays'=>'desc','hits'=>'desc'])->limit(10)->select();
        $slide = model('ad')->order('sort_order desc')->where(['category'=>'wenda','status'=>1])->select();
        $videoCourse=model('videoCourse')->orderRaw('rand()')->where('is_top',1)->limit(1)->select();
        $liveCourse=model('liveCourse')->orderRaw('rand()')->where('is_top',1)->limit(1)->select();
        $tuijian=array_merge($videoCourse,$liveCourse);
        foreach ($top10 as $key => $value) {
            $top10[$key]['replaycount']=model('forumReply')->where(['tid'=>$top10[$key]['id']])->count();
        }
        return $this->fetch('index',['title'=>'问答','toptopic'=>$toptopic,'topic'=>$topic,'top10'=>$top10,'slide'=>$slide,'platename'=>$platename,'tuijian'=>$tuijian]);
    }
    function getslide(){
        $info=$this->get_site_info();
        $url = $info['server'] . "/api/ad/getForumAd";
        return json_to_array(https_request($url));
    }
    function detail(){
        $detail=model('forumTopic')->where(['id'=>input('id')])->find();
        model('forumTopic')->where(['id'=>input('id')])->setInc('hits');
        $replay=model('forumReply')->order('accept desc,addtime desc')->where(['tid'=>input('id')])->select();
        $detail['replaycount']=model('forumReply')->where(['tid'=>input('id')])->count();
        $top10=model('forumTopic')->order(['replays'=>'desc','hits'=>'desc'])->limit(10)->select();
        foreach ($top10 as $key => $value) {
            $top10[$key]['replaycount']=model('forumReply')->where(['tid'=>$top10[$key]['id']])->count();
        }
        if($detail['uid']==is_user_login() || getAdminAuthId(is_admin_login())==1){
            $isAuthor=1;
        }
        if(getAdminAuthId(is_admin_login())==1){
            $forumadmin=1;
        }else{
            $forumadmin=0;
        }
        return $this->fetch('detail',['title'=>$detail['name'],'detail'=>$detail,'replay'=>$replay,'top10'=>$top10,'isAuthor'=>$isAuthor,'forumadmin'=>$forumadmin]);
    }

    function addtopic(){
        !$this->checkLogin() && $this->redirect(url('index/user/login'));
        if ($this->request->isPost()) {
            $param=$this->request->param();
            $param['uid']=is_user_login();
            $param['addtime']=date("Y-m-d H:i:s",time());
            if ($this->insert('forumTopic',$param) === true) {
                $this->addjifen('tiwen',$param['uid']);
                $this->success('添加成功', url('index/forum/index'));
            } else {
                $this->error($this->errorMsg);
            }
        }
        $plate=model('forumPlate')->order('sort_order asc')->select();
        return $this->fetch('addtopic',['title'=>'发表主题','plate'=>$plate]);
    }
    public function edittopic(){
        if ($this->request->isPost()) {
            $param = $this->request->param();
            $param['id']=hashids_decode($param['id']);
            if ($this->update('forumTopic', $param, input('_verify', true)) === true) {
                $this->success('编辑成功', url('index/forum/detail',['id' => $param['id']]));
            } else {
                $this->error($this->errorMsg);
            }
        }
        return $this->fetch('addtopic', ['title'=>'编辑主题','data' => model('forumTopic')->get(hashids_decode(input('id')))]);
    }
    public function deltopic()
    {
        if ($this->request->isPost()) {
            $param=$this->request->param();
            $param['id']=hashids_decode($param['id']);
            if ($this->delete('forumTopic', $param) === true) {
                $res=['status'=>0,'msg'=>'删除成功','url'=>url('index/forum/index')];
                return $res;
            } else {
                $res=['status'=>1,'msg'=>'删除失败'];
                return $res;
            }
        }
    }
    function reply(){
        if ($this->request->isPost()) {
            $param=$this->request->param();
            $param['uid']=is_user_login();
            $param['addtime']=date("Y-m-d H:i:s",time());
            if ($this->insert('forumReply',$param) === true) {
                model('forumTopic')->where(['id'=>$param['tid']])->setInc('replays');
                $this->addjifen('huida',$param['uid']);
                $sentMessage= getUserName($param['uid']).'回复了我的问题';
                $this->sentMessage(0,getUserIdByTid($param['tid']),$param['uid'],$sentMessage,4,$param['tid']);
                $this->success('添加成功', url('index/forum/detail',['id'=>$param['tid']]));
            } else {
                $this->error($this->errorMsg);
            }
        }
    }

    public function delreply()
    {
        if ($this->request->isPost()) {
            $param=$this->request->param();
            $reply=model('forumReply')->where('id',$param['id'])->find();
            if ($this->delete('forumReply', $param) === true) {
                if($reply['accept']==1){
                    model('forumTopic')->where('id',$reply['tid'])->update(['knot'=>0]);
                }
                $res=['status'=>0,'msg'=>'删除成功'];
                return $res;
            } else {
                $res=['status'=>1,'msg'=>'删除失败'];
                return $res;
            }
        }
    }
    /**
     * 获取回帖内容
     */
    public function getreply(){
        if ($this->request->isPost()) {
            $param=$this->request->param();
            $param['id']=hashids_decode($param['id']);
            $res=['status'=>0,'content'=>model('forumReply')->where('id',$param['id'])->value('content')];
            echo json_encode($res);
        }
    }
    /**
     * 编辑回帖
     */
    public function editreply(){
        if ($this->request->isPost()) {
            $param=$this->request->param();
            $param['id']=hashids_decode($param['id']);
            if($this->update('forumReply',$param) === true){
                $res=['status'=>0,'msg'=>'编辑成功'];
            }else{
                $res=['status'=>1,'msg'=>'编辑失败'];
            }
            echo json_encode($res);
        }
    }
    /**
     * 解答采纳
     */
    public function accept(){
        if ($this->request->isPost()) {
            $param=$this->request->param();
            $param['accept']=1;
            if (model('forumReply')->where(['id'=>$param['id']])->update(['accept'=>1])) {
                $reply=model('forumReply')->where(['id'=>$param['id']])->find();
                $topic= model('forumTopic')->where(['id'=>$reply['tid']])->find();
                model('forumTopic')->where(['id'=>$reply['tid']])->update(['knot'=>1]);
                $res=['status'=>0,'msg'=>'采纳成功'];
                $this->addjifen('caina',$reply['uid']);
                $sentMessage= getUserName($topic['uid']).'采纳了我的回答';
                $this->sentMessage(0,$reply['uid'],$topic['uid'],$sentMessage,4,$topic['id']);
            } else {
                $res=['status'=>1,'msg'=>'采纳失败'];
            }
            echo json_encode($res);
        }
    }
    /**
     * 点赞
     */
    public function zan(){
        if ($this->request->isPost()) {
            $param=$this->request->param();
            $param['id']=hashids_decode($param['id']);
            if(model('forumReply')->where(['id'=>$param['id']])->setInc('zan')){
                $res=['status'=>0,'msg'=>'点赞成功'];
            }else{
                $res=['status'=>1,'msg'=>'点赞失败'];
            }
            echo json_encode($res);
        }
    }
    public function set(){
        if ($this->request->isPost()) {
            $param = $this->request->param();
            $param['id']=hashids_decode($param['id']);
            $field=$param['field'];
            $data[$field]=$param['rank'];
            if ($result = model('forumTopic')->where(['id'=>$param['id']])->update($data)) {
                $res=['status'=>0,'msg'=>'设置成功'];
                return $res;
            } else {
                $this->error($this->errorMsg);
            }
        }
    }

    public function upload(){
        $this->uploadImage();
    }
}