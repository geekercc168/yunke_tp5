<?php

namespace app\index\controller;

use app\common\controller\IndexBase;

class Article extends IndexBase
{
    protected function _initialize()
    {
        parent::_initialize();
        $this->checkBangTel();
        $this->assign('category', (model('category')->order('sort_order asc')->select()));
    }

    function index(){
        $param = $this->request->param();
        $param['cid']=hashids_decode($param['cid']);
        $where = [];
        $where['status']=1;
        if (isset($param['cid'])) {
            $where['cid'] = $param['cid'];
        }else{
            $param['cid']=0;
        }
        $slide = model('ad')->order('sort_order desc')->where(['category'=>'article','status'=>1])->select();
        $list = model('article')->with('category')->order('id desc')->where($where)
            ->paginate(config('page_number'), false, ['query' => $param]);
        $tuijian=model('article')->where(['is_top'=>1,'is_hot'=>1,'status'=>1])->order('update_time desc')->select();
        $new=model('article')->where(['status'=>1])->order('create_time desc')->select();
        return $this->fetch('index',['title'=>'文章资讯','slide' => $slide,'list'=>$list,'tuijian'=>$tuijian,'new'=>$new,'cid'=>$param['cid']]);
    }

    function contents(){
        $contents=model('article')->with('category')->where(['id'=>hashids_decode(input('id'))])->find();
        $tuijian=model('article')->where(['is_top'=>1,'is_hot'=>1,'status'=>1])->order('update_time desc')->select();
        model('article')->where(['id'=>hashids_decode(input('id'))])->setInc('view');
        return $this->fetch('contents',['title'=>$contents['title'],'contents'=>$contents,'tuijian'=>$tuijian]);
    }
    function page(){
        $param = $this->request->param();
        $data=db('page')->where('id',hashids_decode($param['id']))->find();
        return $this->fetch('page', ['data' => $data]);
    }

}