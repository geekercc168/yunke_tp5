<?php

namespace app\admin\controller;

use app\common\controller\AdminBase;

class Flashsale extends AdminBase
{
    protected function _initialize()
    {
        parent::_initialize();
    }

    public function index()
    {
        $data=model('marketing')->where(['type'=>'flashsale'])->select();
        foreach ($data as $key => $value) {
            $data[$key]['details']=json_decode($data[$key]['details'],true);
        }
        return $this->fetch('index',['list'=>$data]);
    }
    public function create()
    {
        $param = $this->request->param();
        $dataJson=json_encode($this->getCourse($param['type']));
        return $this->fetch('create',['course'=>$dataJson,'c_type'=>$param['type']]);
    }
    public function createPost()
    {
        $param = $this->request->param();
        $data['name']='flashsale';
        $data['type']='flashsale';
        $data['c_type']=$param['c_type'];
        $data['title']=$param['title'];
        $data['details']=json_encode(['rate'=>$param['rate'],'starttime'=>$param['starttime'],'endtime'=>$param['endtime'],'course'=>$param['course']]);
        if(empty($param['rate']) ||empty($param['starttime'])||empty($param['endtime'])||empty($param['course'])) {
            $res=['code'=>1,'msg'=>'请把信息填写完整'];
            echo json_encode($res);
        }else{
            if ($this->insert('marketing', $data) === true) {
                $res=['code'=>0,'msg'=>'添加成功'];
            } else {
                $res=['code'=>1,'msg'=>$this->errorMsg];
            }
            echo json_encode($res);
        }
    }
    function edit(){
        $param = $this->request->param();
        $data=model('marketing')->where(['id'=>$param['id']])->find();
        $data['details']=$details=json_decode($data['details'],true);
        foreach ($details['course'] as $key => $value){
           $selectCourse[]=$details['course'][$key]['value'];
        }
        $dataJson=json_encode($this->getCourse($param['ctype']));
        return $this->fetch('edit',['selectCourse'=>json_encode($selectCourse),'data'=>$data,'course'=>$dataJson]);
    }
    public function editPost()
    {
        $param = $this->request->param();
        $data['name']='flashsale';
        $data['id']=$param['id'];
        $data['type']='flashsale';
        $data['title']=$param['title'];
        $data['details']=json_encode(['rate'=>$param['rate'],'starttime'=>$param['starttime'],'endtime'=>$param['endtime'],'course'=>$param['course']]);
        if ($this->update('marketing', $data) === true) {
            $res=['code'=>0,'msg'=>'编辑成功'];
        } else {
            $res=['code'=>1,'msg'=>$this->errorMsg];
        }
        echo json_encode($res);

    }
    function del(){
        if ($this->request->isPost()) {
            if ($this->delete('marketing', $this->request->param()) === true) {
                $this->success('删除成功');
            } else {
                $this->error($this->errorMsg);
            }
        }
    }
    function getCourse($type){ 
        if($type==1){
            $videoCourse=model('videoCourse')->order('sort_order asc,addtime desc')->field('id,title,type')->select();
            $liveCourse=model('liveCourse')->order('sort_order asc,addtime desc')->field('id,title,type')->select();
            $allCourse=array_merge($videoCourse,$liveCourse);
        }
        if($type==2){
            $allCourse=model('classroom')->order('sort_order asc,addtime desc')->field('id,title,type')->select();
        }
        foreach ($allCourse as $key => $value) {
            $data[]=['value'=>$allCourse[$key]['type'].'-'.$allCourse[$key]['id'],'title'=>$allCourse[$key]['title'],'disabled'=>'','checked'=>''];
        }
        return $data;
    }
    function comments(){
        $comment=db('virtualcomment')->order('id desc')->paginate(12);
        return $this->fetch('comments',['comment'=>$comment]);
    }
    function addComments(){
        if ($this->request->isPost()) {
            $param = $this->request->param();
            $user['username']=$param['username'];
            $user['password']=md5('12345678');
            $param['uid']=db('user')->insertGetId($user);
            if(db('virtualcomment')->insert($param)){
                $this->success('添加成功');
            }else{
                $this->error('操作失败');
            }
        }
        return $this->fetch('addComments');
    }
    function delcomment(){
        if ($this->request->isPost()) {
            if(db('virtualcomment')->delete(input('id'))){
                $this->success('删除成功');
            }else{
                $this->error('操作失败');
            }
        }
    }


}
