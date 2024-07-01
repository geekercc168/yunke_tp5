<?php

namespace app\admin\controller;

use app\common\controller\AdminBase;

class Link extends AdminBase
{
    /**
     * 合作伙伴首页
     */
    public function index()
    {
        $param = $this->request->param();
        $list = model('link')->order('id desc')->paginate(config('page_number'), false, ['query' => $param]);
        return $this->fetch('index', ['list' => $list]);
    }

    /**
     * 添加合作伙伴
     */
    public function add()
    {
        if ($this->request->isPost()) {
            if ($this->insert('link', $this->request->param()) === true) {
                $this->success('添加成功', url('admin/link/index'));
            } else {
                $this->error($this->errorMsg);
            }
        }
        return $this->fetch('save');
    }

    /**
     * 编辑合作伙伴
     */
    public function edit()
    {
        if ($this->request->isPost()) {
            if ($this->update('link', $this->request->param(), input('_verify', true)) === true) {
                $this->success('修改成功', url('admin/link/index'));
            } else {
                $this->error($this->errorMsg);
            }
        }
        return $this->fetch('save', ['data'=> model('link')->where('id', input('id'))->find()]);
    }

    /**
     * 删除合作伙伴
     */
    public function del()
    {
        if ($this->request->isPost()) {
            if ($this->delete('link', $this->request->param()) === true) {
                $this->success('删除成功');
            } else {
                $this->error($this->errorMsg);
            }
        }
    }
}