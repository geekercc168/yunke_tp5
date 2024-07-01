<?php

namespace app\admin\controller;

use app\common\controller\AdminBase;

class Article extends AdminBase
{
    protected function _initialize()
    {
        parent::_initialize();
        if ($this->request->isGet()) {
            $this->assign('category', list_to_level(model('category')->order('sort_order asc')->select()));
        }
    }

    public function index()
    {
        $param = $this->request->param();
        $where = [];
        if (isset($param['title'])) {
            $where['title'] = ['like', "%" . $param['title'] . "%"];
        }
        if (isset($param['cid'])) {
            $where['cid'] = $param['cid'];
        }
        if (isset($param['is_top'])) {
            $where['is_top'] = $param['is_top'];
        }
        if (isset($param['is_hot'])) {
            $where['is_hot'] = $param['is_hot'];
        }
        if (isset($param['status'])) {
            $where['status'] = $param['status'];
        }
        if(getAdminAuthId(is_admin_login())!=1){
            $map['uid'] =(is_admin_login());
        }
        $list = model('article')->with('category')->order('id desc')->where($where)->where($map)
            ->paginate(config('page_number'), false, ['query' => $param]);
        return $this->fetch('index', ['list' => $list]);
    }

    public function add()
    {
        if ($this->request->isPost()) {
            $param=$this->request->param();
            $param['uid']=is_admin_login();
            if ($this->insert('article',$param ) === true) {
                insert_admin_log('添加了文章');
                $this->success('添加成功', url('admin/article/index'));
            } else {
                $this->error($this->errorMsg);
            }
        }
        return $this->fetch('save');
    }

    public function edit()
    {
        if ($this->request->isPost()) {
            $param = $this->request->param();
            if (is_array($param['id'])) {
                $data = [];
                foreach ($param['id'] as $v) {
                    $data[] = ['id' => $v, $param['name'] => $param['value']];
                }
                $result = $this->saveAll('article', $data, input('_verify', true));
            } else {
                $result = $this->update('article', $param, input('_verify', true));
            }
            if ($result === true) {
                insert_admin_log('修改了文章');
                $this->success('修改成功', url('admin/article/index'));
            } else {
                $this->error($this->errorMsg);
            }
        }
        return $this->fetch('save', ['data' => model('article')->get(input('id'))]);
    }

    public function del()
    {
        if ($this->request->isPost()) {
            if ($this->delete('article', $this->request->param()) === true) {
                insert_admin_log('删除了文章');
                $this->success('删除成功');
            } else {
                $this->error($this->errorMsg);
            }
        }
    }
    public function page(){
        $param = $this->request->param();
        $where = [];
        if (isset($param['title'])) {
            $where['title'] = ['like', "%" . $param['title'] . "%"];
        }
        $list = db('page')->order('id desc') ->where($where)->paginate(config('page_number'), false, ['query' => $param]);
        return $this->fetch('page', ['list' => $list]);
    }
    public function pageadd(){
        if ($this->request->isPost()) {
            $param=$this->request->param();
            $param['addtime'] = date('Y-m-d h:i:s', time());
            if (db('page')->insert($param ,true)) {
                $this->success('添加成功', url('admin/article/page'));
            } else {
                $this->error($this->errorMsg);
            }
        }
        return $this->fetch('pageadd', ['data' => db('page')->where('id',input('id'))->find()]);
    }
    public function pagedel()
    {
        if ($this->request->isPost()) {
            if (db('page')->delete(input('id'))) {
                $this->success('删除成功');
            } else {
                $this->error($this->errorMsg);
            }
        }
    }
}
