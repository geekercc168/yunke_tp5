<?php

namespace app\index\controller;

use app\common\controller\IndexBase;

class Index extends IndexBase
{
    public function index()
    {
        $slide = model('ad')->order('sort_order asc')->where(['category'=>'index','status'=>1])->select();
        $videoCourse=model('videoCourse')->order('sort_order asc,id desc')->where('is_top',1)->limit(16)->select();
        $liveCourse=model('liveCourse')->order('sort_order asc,id desc')->where('is_top',1)->limit(16)->select();
        $offlineCourse=model('offlineCourse')->order('sort_order asc,id desc')->where('is_top',1)->limit(16)->select();
        $videoCourse2=model('videoCourse')->order('sort_order asc,id desc')->where('is_top',1)->limit(8)->select();
        $liveCourse2=model('liveCourse')->order('sort_order asc,id desc')->where('is_top',1)->limit(8)->select();
        $offlineCourse2=model('offlineCourse')->order('sort_order asc,id desc')->where('is_top',1)->limit(8)->select();
        $allCourse=array_merge($videoCourse2,$liveCourse2,$offlineCourse2);
        $article=model('article')->order('sort_order asc,update_time desc')->limit(6)->select();
        $teacher=model('admin')->where('status',1)->limit(6)->select();
        $classroom = model('classroom')->where('is_top',1)->order('sort_order asc,addtime desc')->limit(8)->select();
        $parent=model('courseCategory')->where(['pid'=>0])->order('sort_order asc')->select();
        foreach ($parent as $k=> $value) {
            $where=[];
            $where[]=(int)$value['id'];
            $parent[$k]['sub']=model('courseCategory')->where(['pid'=>$value['id']])->order('sort_order asc')->select();
            foreach ( $parent[$k]['sub'] as $v=> $val) {
                $where[]=(int)$val['id'];
                $parent[$k]['sub'][$v]['url']='/video?parent='.$value['id'].'&second='.$val['id'];
            }
            $videocourse=collection(model('videoCourse')->orderRaw('rand()')->where('is_hot',1)->where('cid','in',$where)->limit(3)->select())->toArray();
            $livecourse=collection(model('liveCourse')->orderRaw('rand()')->where('is_hot',1)->where('cid','in',$where)->limit(3)->select())->toArray();
            $offlinecourse=collection(model('offlineCourse')->orderRaw('rand()')->where('is_hot',1)->where('cid','in',$where)->limit(3)->select())->toArray();
            $courseArry=array_merge($videocourse, $livecourse,$offlinecourse);
            shuffle($courseArry);
            $parent[$k]['course']=array_slice($courseArry ,0,3);
        }
        $this->checkBangTel();
        return $this->fetch('index', ['slide' => $slide,'allCourse'=>$allCourse,'videoCourse'=>$videoCourse,'liveCourse'=>$liveCourse,'offlineCourse'=>$offlineCourse,'article'=>$article,'classroom'=>$classroom,'teacher'=>$teacher,'topCategory'=>$parent]);
    }
    function search(){
        return $this->fetch('search');
    }
    function LiveClient(){
        $android = model('appadmin')->order('id desc')->field( 'downurl,version')->where(['platform'=>'android'])->limit(1)->select();
        $ios = model('appadmin')->order('id desc')->field( 'downurl,version')->where(['platform'=>'ios'])->limit(1)->select();
        if(!$android){
            $androidUrl="敬请期待";
        }else{
            $androidUrl=UrlEncode(formatUrl($android[0]['downurl']));
        }
        if(!$ios){
            $iosUrl="敬请期待";
        }else{
            $iosUrl=UrlEncode(formatUrl($android[0]['downurl']));
        }
        return $this->fetch('liveClient',['androidUrl'=>$androidUrl,'iosUrl'=>$iosUrl]);
    }
    function qrcode(){
        vendor('phpqrcode.phpqrcode');
        $param = $this->request->param(false);
        $url=urldecode($param['url']);
        $level = $param['level']="H";
        $size = $param['size']="5";
        $margin=2;
        \QRcode::png($url, false, $level, $size,$margin);
    }
    function policy(){
        $data = model('agreement')->where('id', input('id'))->find();
        return $this->fetch('policy',['data'=>$data]);
    }
    /**
     * APP分享页面
     */
    function appshere(){
        $android = model('appadmin')->order('id desc')->field( 'downurl,version')->where(['platform'=>'android'])->limit(1)->select();
        $androidUrl=(formatUrl($android[0]['downurl']));
        $share=db('appshare')->where('id', 1)->find();
        $comment=db('appcomment')->order('id','desc')->select();
        return $this->fetch('share',['time'=>date("Y-m-d",time()),'androidUrl'=>$androidUrl,'share'=>unserialize($share['detailes']),'comment'=>$comment]);
    }
}
