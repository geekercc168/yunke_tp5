<?php

namespace app\admin\controller;

use app\common\controller\AdminBase;

class Coupon extends AdminBase
{
    protected function _initialize()
    {
        parent::_initialize();
    }

    public function index()
    {
        $param = $this->request->param();
        $where = [];
        if (isset($param['code'])) {
            $where['code'] = ['like', "%" . $param['code'] . "%"];
        }
        if (isset($param['buystatus'])) {
            $where['buystatus'] = $param['buystatus'];
        }
        if (isset($param['usestatus'])) {
            $where['usestatus'] = $param['usestatus'];
        }
        if (isset($param['status'])) {
            $where['status'] = $param['status'];
        }
        $list = model('coupon')->order('id desc')->where($where)->paginate(config('page_number'), false, ['query' => $param]);
        return $this->fetch('index', ['list' => $list]);
    }

    public function add()
    {
        if ($this->request->isPost()) {
            $param = $this->request->param();
            for($i=0;$i<$param['count'];$i++){
                $data[]=['code'=>rand_code($param['figures']),'rate'=>$param['rate'],'youxiaoqi'=>$param['youxiaoqi'],'status'=>0,'userId'=>0,'addtime'=>date('Y-m-d h:i:s', time())] ;
            }
            if(model('coupon')->saveAll($data)){
                $this->success('打折卡生成成功');
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
                $result = $this->saveAll('coupon', $data, input('_verify', true));
            } else {
                $result = $this->update('coupon', $param, input('_verify', true));
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
            if ($this->delete('coupon', $this->request->param()) === true) {
                $this->success('删除成功');
            } else {
                $this->error($this->errorMsg);
            }
        }
    }
    function fafang(){
        $data=model('marketing')->where(['type'=>'coupon'])->select();
        foreach ($data as $key => $value) {
            $data[$key]['details']=json_decode($data[$key]['details'],true);
        }
        $couponList = model('coupon')->field('rate')->select(); 
        foreach ($couponList as $key => $value) {
            $rate[$key]=$couponList[$key]['rate'];
        }
        $rate = array_unique($rate);
        if ($this->request->isPost()) {
            $param = $this->request->param();
            $data=model('marketing')->where(['id'=>$param['id']])->find();
            $data['details']=json_decode($data['details'],true);
            $param['details']=json_encode(['numbs'=>$param['numbs']?:$data['details']['numbs'],'rate'=>$param['rate']?:$data['details']['rate'],'status'=>$param['status']===null? $data['details']['status']:$param['status']]);
            if ($this->update('marketing', $param, input('_verify', true)) === true) {
                $this->success('修改成功', url('admin/coupon/index'));
            } else {
                $this->error($this->errorMsg);
            }
        }
        return $this->fetch('fafang',['data' => $data,'rate'=>$rate]);
    }
}
