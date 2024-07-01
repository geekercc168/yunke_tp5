<?php

namespace app\admin\controller;

use app\common\controller\AdminBase;

class Appadmin extends AdminBase
{
    protected function _initialize()
    {
        parent::_initialize();
    }

    public function index()
    {
        $info=$this->get_site_info();
        return $this->fetch('index',['list'=>json_to_array(post_curl("https://www.yunknet.cn/api/Openupdate/appinfo",$param=[]))]);
    }

    public  function down(){
        $param = $this->request->param();
        $param['type']='open';
        $res=json_to_array(post_curl("https://www.yunknet.cn/api/Openupdate/getappurl",$param));
        if($res['code']==1){
            $this->success('获取成功，正在下载',url('admin/appadmin/downfile',['url'=>$res['downurl']]));
        }else{
            $this->error($res['msg']);
        }
    }
    public function downfile(){
        $param=$this->request->param();
        $url=urldecode($param['url']);
        $info = get_headers($url, true);
        $size = $info['Content-Length'];
        header("Content-type:application/octet-stream");
        $origin_name = basename($url);
        header("Content-Disposition:attachment;filename = " . $origin_name);
        header("Accept-ranges:bytes");
        header("Accept-length:" . $size);
        readfile($url);
    }
    public  function upload(){
        $list = model('appadmin')->order('id desc')->select();
        return $this->fetch('upload',['list' => $list]);
    }
    public function uploadapp(){
        if ($this->request->isPost()) {
            $param=$this->request->param();
            $param['downtime']=0;
            $param['addtime']=date('Y-m-d h:i:s', time());
            if ($this->insert('appadmin',$param ) === true) {
                $this->success('添加成功', url('admin/appadmin/upload'));
            } else {
                $this->error($this->errorMsg);
            }
        }
        return $this->fetch('uploadapp');
    }
    public function appedit(){
        if ($this->request->isPost()) {
            $param = $this->request->param();
            if ($this->update('appadmin', $param, input('_verify', true)) === true) {
                $this->success('修改成功',  url('admin/appadmin/upload'));
            } else {
                $this->error($this->errorMsg);
            }
        }
        return $this->fetch('uploadapp',['data' => model('appadmin')->where('id', input('id'))->find()]);
    }

    public function appdel(){
        if ($this->request->isPost()) {
            if ($this->delete('appadmin', $this->request->param()) === true) {
                $this->success('删除成功');
            } else {
                $this->error($this->errorMsg);
            }
        }
    }
    public function agreement(){
        $list = model('agreement')->order('id asc')->select();
        return $this->fetch('agreement',['list' => $list]);
    }
    public function addAgreement(){
        if ($this->request->isPost()) {
            $param=$this->request->param();
            $param['addtime']=date('Y-m-d h:i:s', time());
            if ($this->insert('agreement',$param ) === true) {
                $this->success('添加成功', url('admin/appadmin/agreement'));
            } else {
                $this->error($this->errorMsg);
            }
        }
        return $this->fetch('addAgreement',['data' => model('agreement')->where('id', input('id'))->find()]);
    }
    public function editagreement(){
        if ($this->request->isPost()) {
            $param = $this->request->param();
            if ($this->update('agreement', $param, input('_verify', true)) === true) {
                $this->success('修改成功',  url('admin/appadmin/agreement'));
            } else {
                $this->error($this->errorMsg);
            }
        }
        return $this->fetch('addAgreement',['data' => model('agreement')->where('id', input('id'))->find()]);
    }
    public function delagreement(){
        if ($this->request->isPost()) {
            if ($this->delete('agreement', $this->request->param()) === true) {
                $this->success('删除成功');
            } else {
                $this->error($this->errorMsg);
            }
        }
    }
    public function appshare(){
        if ($this->request->isPost()) {
            $data['detailes']=serialize($this->request->param());
            $res= db('appshare')->update(['detailes' => $data['detailes'],'id'=>1]);
            if($res){
                $this->success('修改成功');
            }else{
                $this->error('操作失败');
            }
        }
        $data = db('appshare')->where('id', 1)->find();
        $comment=db('appcomment')->select();
        return $this->fetch('appshare', ['data' => unserialize($data['detailes']),'comment'=>$comment]);
    }
    function addcomment(){
        if ($this->request->isPost()) {
            $param=$this->request->param();
            $param['addtime']=date('Y-m-d h:i:s', time());
            if(db('appcomment')->insert($param)){
                $this->success('修改成功');
            }else{
                $this->error('操作失败');
            }
        }
        return $this->fetch('addcomment');
    }
    function delcomment(){
        if ($this->request->isPost()) {
            if(db('appcomment')->delete(input('id'))){
                $this->success('修改成功');
            }else{
                $this->error('操作失败');
            }
        }
    }
}
