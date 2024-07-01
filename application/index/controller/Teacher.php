<?php

namespace app\index\controller;

use app\common\controller\IndexBase;

class Teacher extends IndexBase
{
    protected function _initialize()
    {
        parent::_initialize();
        $this->checkBangTel();
    }
    function index(){
        $teacher=model('admin')->where('status',1)->where('uid','gt',0)->select();
        return $this->fetch('index',['title'=>'教师中心','teacher'=>$teacher]);
    }
    function centert(){
        $param=$this->request->param();
        $param['id']=hashids_decode($param['id']);
        $teacherInfo=model('admin')->where('id',$param['id'])->find();
        $videoCourse=model('videoCourse')->order('sort_order asc,addtime desc')->where(['status'=>1,'teacher_id'=>$param['id']])->select();
        $liveCourse=model('liveCourse')->order('sort_order asc,addtime desc')->where(['status'=>1,'teacher_id'=>$param['id']])->select();
        $allCourse=array_merge($videoCourse,$liveCourse);
        return $this->fetch('centert',['title'=>$teacherInfo['showname'],'list'=>$allCourse,'teacherInfo'=>$teacherInfo]);
    }



}