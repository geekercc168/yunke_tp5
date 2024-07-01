<?php

namespace app\admin\controller;

use app\common\controller\AdminBase;

class Vip extends AdminBase
{
    function setup(){
        if ($this->request->isPost()) {
            $data = [];
            foreach ($this->request->param() as $k => $v) {
                $data[] = ['name' => $k, 'value' => $v];
            }
            if ($this->saveAll('system', $data) === true) {
                clear_cache();
                insert_admin_log('VIP会员设置');
                $this->success('保存成功');
            } else {
                $this->error($this->errorMsg);
            }
        }
        $data['isvipopen']=config('isvipopen');
        $data['vipyueprice'] = config('vipyueprice');
        $data['vipjiprice'] = config('vipjiprice');
        $data['vipnianprice'] = config('vipnianprice');
        $list = db('vipset')->select();
        foreach ($list as $k => $v) {
            $pids[$v['type']]=$v['iospproductid'];
        }
        return $this->fetch('setup',['data' => $data,'list' => $pids]);
    }
    function setiospproductid(){
        if ($this->request->isPost()) {
            foreach ($this->request->param() as $k => $v) {
                $data[] = ['type' => $k, 'iospproductid' => $v];
            }
            if ($this->saveAll('vipset', $data) === true) {
                $this->success('保存成功');
            } else {
                $this->error($this->errorMsg);
            }
        }
    }
}
