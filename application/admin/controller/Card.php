<?php

namespace app\admin\controller;

use app\common\controller\AdminBase;

class Card extends AdminBase
{
    protected function _initialize()
    {
        parent::_initialize();
    }

    public function index()
    {
        $param = $this->request->param();
        $where = [];
        if (isset($param['number'])) {
            $where['number'] = ['like', "%" . $param['number'] . "%"];
        }
        if (isset($param['usestatus'])) {
            $where['usestatus'] = $param['usestatus'];
        }
        if (isset($param['buystatus'])) {
            $where['buystatus'] = $param['buystatus'];
        }
        if (isset($param['status'])) {
            $where['status'] = $param['status'];
        }
        $list = model('card')->order('id desc')->where($where)->paginate(config('page_number'), false, ['query' => $param]);
        return $this->fetch('index', ['list' => $list]);
    }

    public function add()
    {
        if ($this->request->isPost()) {
            $param = $this->request->param();
            for($i=0;$i<$param['count'];$i++){
               $data[]=['number'=>rand_code($param['figures']),'money'=>$param['money'],'addtime'=>date('Y-m-d h:i:s', time())] ;
            }
            if(model('card')->saveAll($data)){
                $this->success('点卡生成成功');
            };
        }
        return $this->fetch('add');
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
                $result = $this->saveAll('card', $data, input('_verify', true));
            } else {
                $result = $this->update('card', $param, input('_verify', true));
            }
            if ($result === true) {
                $this->success('修改成功', url('admin/card/index'));
            } else {
                $this->error($this->errorMsg);
            }
        }
    }
    public function del()
    {
        if ($this->request->isPost()) {
            if ($this->delete('card', $this->request->param()) === true) {
                $this->success('删除成功');
            } else {
                $this->error($this->errorMsg);
            }
        }
    }
    function export(){
        if ($this->request->isPost()) {
            $param = $this->request->param();
            $map['id']  = array('between',[$param['start'],$param['end']]);
            $map['buystatus']=0;
            $map['usestatus']=0;
            $data = collection(model('card')->field('id,number,money,addtime')->where($map)->order('id asc')->select())->toArray();
            $card=$data;
            foreach ($data as $k=> $value) {
                $data[$k]['buystatus']=1;
            }
            $this->saveAll('card', $data, input('_verify', true));
            array_unshift($data, ['ID', '卡号', '面值', '点卡生成时间',]);
            export_excel($card, date('YmdHis'));
        }
        return $this->fetch('export');
    }
}
