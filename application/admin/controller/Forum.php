<?php

namespace app\admin\controller;

use app\common\controller\AdminBase;

class Forum extends AdminBase
{
    /**
     * 问答板块管理
     */
    public function plate()
    {
        return $this->fetch('plate', ['list' => model('forumPlate')->order('sort_order asc')->select()]);
    }

    /**
     * 问答主题管理
     */
    public function topic()
    {

        return $this->fetch('topic');
    }

    /**
     * 添加问答板块
     */
    public function addplate()
    {
        if ($this->request->isPost()) {
            if ($this->insert('forumPlate', $this->request->param()) === true) {
                insert_admin_log('添加了分类');
                $this->success('添加成功', url('admin/forum/plate'));
            } else {
                $this->error($this->errorMsg);
            }
        }
        return $this->fetch('addplate');
    }

    /**
     * 编辑问答板块
     */
    public function editplate()
    {
        if ($this->request->isPost()) {
            if ($this->update('forumPlate', $this->request->param(), input('_verify', true)) === true) {
                insert_admin_log('修改了论坛板块');
                $this->success('修改成功', url('admin/forum/plate'));
            } else {
                $this->error($this->errorMsg);
            }
        }
        return $this->fetch('addplate', ['data'=> model('forumPlate')->where('id', input('id'))->find()]);
    }

    /**
     * 删除问答板块
     */
    public function delplate()
    {
        if ($this->request->isPost()) {
            if(model('forumTopic')->where(['pid'=>input('id')])->find()){
                $this->error('板块下有问答，请先删除问答再删除板块');
            }else{
                if ($this->delete('forumPlate', $this->request->param()) === true) {
                    insert_admin_log('删除了论坛板块');
                    $this->success('删除成功');
                } else {
                    $this->error($this->errorMsg);
                }
            }
        }
    }
    /**
     * 管理问答主题
     */
    public function adminplate(){
        $param = $this->request->param();
        $where = [];
        if (isset($param['name'])) {
            $where['name'] = ['like', "%" . $param['name'] . "%"];
        }
        if (isset($param['pid'])) {
            $where['pid'] = $param['pid'];
        }
        if (isset($param['istop'])) {
            $where['istop'] = $param['istop'];
        }
        $data= model('forumTopic')->where($where)->paginate(config('page_number'), false, ['query' => $param]);
        $plate=model('forumPlate')->order('sort_order asc')->select();
        return $this->fetch('adminplate', ['data'=>$data,'plate'=>$plate]);
    }

    /**
     * 删除问答主题
     */
    public function deltopic()
    {
        if ($this->request->isPost()) {
            $param=$this->request->param();
            if(is_array($param['id'])){
                foreach ($param['id'] as $key => $value) {
                    if (model('forumTopic')->where(['id'=>$value])->delete()) {
                        $this->success('删除成功');
                    } else {
                        $this->error($this->errorMsg);
                    }
                }
            }else{
                if ($this->delete('forumTopic', $param) === true) {
                    if(model('forumReply')->where(['tid'=>input('id')])->find()){
                        model('forumReply')->where(['tid'=>input('id')])->delete();
                    }
                    $this->success('删除成功');
                } else {
                    $this->error($this->errorMsg);
                }
            }
        }
    }
    /**
     * 问答主题回复管理
     */
    function reply(){
        $replay=model('forumReply')->order('addtime desc')->where(['tid'=>input('id')])->paginate(config('page_number'));
        return $this->fetch('reply', ['data'=>$replay]);
    }
    /**
     * 删除问答主题回复
     */
    public function delreply()
    {
        if ($this->request->isPost()) {
            $param=$this->request->param();
            if ($this->delete('forumReply', $param) === true) {
                $this->success('删除成功');
            } else {
                $this->error($this->errorMsg);
            }
        }
    }
}