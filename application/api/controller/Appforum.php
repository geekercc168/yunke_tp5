<?php

namespace app\api\controller;

use app\common\controller\IndexBase;

class Appforum extends IndexBase
{

    function getTopicList(){
        $param=$this->request->param();
        $where = [];
        if ($param['plateid']>0) {
            $where['pid'] = $param['plateid'];
        }
        if($param['type']==1){
            $topic=model('forumTopic')->where($where)->order('addtime desc')->select();
        }
        if($param['type']==2){
            $topic=model('forumTopic')->where($where)->order('replays desc,hits desc')->select();
        }
        if($param['type']==3){
            $topic=model('forumTopic')->where($where)->where(['knot'=>0])->order('addtime desc')->select();
        }
        if($param['type']==4){
            $topic=model('forumTopic')->where($where)->where(['knot'=>1])->order('addtime desc')->select();
        }
        foreach ($topic as $key => $value) {
            $topic[$key]['replaycount']=model('forumReply')->where(['tid'=>$value['id'],'touid'=>0])->count();
            $topic[$key]['username']=getUserName($value['uid']);
            $topic[$key]['plateName']=getPlateName($value['pid']);
            $topic[$key]['addtime']=get_last_time($value['addtime']);
        }
        return  json_encode($topic);
    }
    function getTopicDetails(){
        $detail=model('forumTopic')->where(['id'=>input('id')])->find();
        model('forumTopic')->where(['id'=>input('id')])->setInc('hits');
        $detail['replay']=model('forumReply')->order('accept desc,addtime desc')->where(['tid'=>input('id')])->select();
        $detail['replaycount']=model('forumReply')->where(['tid'=>input('id')])->count();
        $detail['username']=getUserName($detail['uid']);
        $detail['avatar']=formatUrl(defaultAvatar(getAvatar($detail['uid'])));
        $detail['addtime']=get_last_time($detail['addtime']);
        $detail['plateName']=getPlateName($detail['pid']);
        if($detail['uid']==input('uid')){
            $detail['isAuthor']=1;
        }else{
            $detail['isAuthor']=0;
        }
        return  json_encode($detail);
    }
    function getplatelist(){
        $plate=model('forumPlate')->order('sort_order desc')->where('isshow',1)->select();
        return  json_encode($plate);
    }
    function addtopic(){
        $param=$this->request->param();
        $param['addtime']=date("Y-m-d H:i:s",time());
        if (db('forumTopic')->insert($param)) {
            $this->addjifen('tiwen',$param['uid']);
            $res['code']=0;
            $res['msg']='发布成功';
        } else {
            $res['code']=1;
            $res['msg']=$this->errorMsg;
        }
        return  json_encode($res);
    }
    function getCommentList(){
        $param=$this->request->param();
        $replay=model('forumReply')->order('accept desc,addtime desc')->where(['tid'=>$param['tid'],'touid'=>0])->select();
        foreach ($replay as $key => $value) {
            $replay[$key]['username']=getUserName($value['uid']);
            $replay[$key]['avatar']=formatUrl(defaultAvatar(getAvatar($value['uid'])));
            $replay[$key]['addtime']=get_last_time($value['addtime']);
            $replay[$key]['child']=model('forumReply')->order('accept desc,addtime desc')->where(['tid'=>$param['tid'],'touid'=>$value['uid'],'toid'=>$value['id']])->select();
            foreach ($replay[$key]['child'] as $key2 => $value2) {
                $replay[$key]['child'][$key2]['username']=getUserName($value2['uid']);
                $replay[$key]['child'][$key2]['tousername']=getUserName($value2['touid']);
                $replay[$key]['child'][$key2]['avatar']=formatUrl(defaultAvatar(getAvatar($value2['uid'])));
                $replay[$key]['child'][$key2]['addtime']=get_last_time($value2['addtime']);
            }
        }
        return  json_encode($replay);
    }
    function addComment(){
        $param=$this->request->param();
        $param['addtime']=date("Y-m-d H:i:s",time());
        if (db('forumReply')->insert($param)) {
            model('forumTopic')->where(['id'=>$param['tid']])->setInc('replays');
            $this->addjifen('huida',$param['uid']);
            $sentMessage= getUserName($param['uid']).'回复了我的问题';
            $this->sentMessage(0,getUserIdByTid($param['tid']),$param['uid'],$sentMessage,4,$param['tid']);
            $res['code']=0;
            $res['msg']='发布成功';
        } else {
            $res['code']=1;
            $res['msg']=$this->errorMsg;
        }
        return  json_encode($res);
    }
    public function deltopic()
    {
        $param=$this->request->param();
        if ($this->delete('forumTopic', $param) === true) {
            db('forumReply')->where('tid',$param['id'])->delete();
            $res=['code'=>0,'msg'=>'删除成功'];
        } else {
            $res=['code'=>1,'msg'=>$this->errorMsg];
            return $res;
        }
        return json_encode($res);
    }
    public function accept(){
        $param=$this->request->param();
        $param['accept']=1;
        $data=model('forumReply')->where(['id'=>$param['id']])->find();
        if($data['uid']!=$param['uid']){
            if (model('forumReply')->where(['id'=>$param['id']])->update(['accept'=>1])) {
                $reply=model('forumReply')->where(['id'=>$param['id']])->find();
                $topic= model('forumTopic')->where(['id'=>$reply['tid']])->find();
                model('forumTopic')->where(['id'=>$reply['tid']])->update(['knot'=>1]);
                $res=['status'=>0,'msg'=>'采纳成功'];
                $this->addjifen('caina',$reply['uid']);
                $sentMessage= getUserName($topic['uid']).'采纳了我的回答';
                $this->sentMessage(0,$reply['uid'],$topic['uid'],$sentMessage,4,$topic['id']);
            } else {
                $res=['code'=>1,'msg'=>'采纳失败'];
            }
        }else{
            $res=['code'=>1,'msg'=>'不能采纳自己的回答'];
        }
        echo json_encode($res);
    }
    public function jifen(){

        $data = model('system')->where('name', 'jifen')->find();
        $jifenArry=unserialize($data['value']);
        dump($jifenArry);
    }
}