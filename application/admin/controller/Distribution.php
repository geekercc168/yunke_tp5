<?php

namespace app\admin\controller;

use app\common\controller\AdminBase;

class Distribution extends AdminBase
{
    function setup(){
        if ($this->request->isPost()) {
            $data = [];
            foreach ($this->request->param() as $k => $v) {
                $data[] = ['name' => $k, 'value' => $v];
            }
            if ($this->saveAll('system', $data) === true) {
                clear_cache();
                insert_admin_log('三级分销设置分成比例');
                $this->success('保存成功');
            } else {
                $this->error($this->errorMsg);
            }
        }
        $data['isdistributionopen']=config('isdistributionopen');
        $data['distributioone'] = config('distributioone');
        $data['distributiontwo'] = config('distributiontwo');
        $data['distributionthree'] = config('distributionthree');
        return $this->fetch('setup',['data' => $data]);
    }
}
