<?php

namespace app\common\controller;

use think\Controller;

class Base extends Controller
{
    protected $model;
    protected $errorMsg = '未知错误';
    protected $insertId = 0;

    protected function _initialize()
    {
        parent::_initialize();
        if (!file_exists("data/install.lock"))
        {
            return $this->redirect(url('install/index/index'));
        }
        ini_set("session.cookie_httponly", 1);
        $system = cache('db_system_data');
        if (!$system) {
            $system = [];
            foreach (model('system')->select() as $v) {
                $system[$v['name']] = $v['value'];
            }
            cache('db_system_data', $system);
        }
        config($system);
        $config = cache('db_config_data');
        if (!$config) {
            $config = [];
            foreach (model('config')->select() as $v) {
                $config[$v['group']][$v['name']] = $v['value'];
            }
            cache('db_config_data', $config);
        }
        config($config);
        $userInfo=model('user')->where(['id'=>is_user_login()])->find();
        $this->assign('userInfo',$userInfo);
        $data = model('system')->where('name', 'upload_image')->find();
        $upload=unserialize($data['value']);
        if($upload['location']==1){
            $uploadlocation='ajax-images';
        }else{
            $uploadlocation='ajax-oss-image';
        }
        return $this->assign('uploadlocation', $uploadlocation);
    }

    /**
     * 插入记录
     * @param string $name 模型
     * @param array $data 数据
     * @param bool $rule 是否开启认证
     * @param string|array $field 允许写入的字段 如果为true只允许写入数据表字段
     * @param string $key 获取模型的主键，默认是id
     * @return bool|mixed
     */
    public function insert($name, $data, $rule = true, $field = true, $key = 'id')
    {
        try {
            $this->model = model($name);
            if ($this->model->allowField($field)->validate($rule)->save($data)) {
                $this->insertId = $this->model->$key;
                return true;
            } else {
                return $this->errorMsg = $this->model->getError();
            }
        } catch (\Exception $e) {
            return $this->errorMsg = config('app_debug') ? $e->getMessage() : '请求错误';
        }
    }

    /**
     * 更新记录
     * @param string $name 模型
     * @param array $data 数据
     * @param bool $rule 是否开启认证
     * @param string|array $field 允许写入的字段 如果为true只允许写入数据表字段
     * @param string $key 更新条件字段 多个用逗号隔开
     * @return bool|mixed
     */
    public function update($name, $data, $rule = true, $field = true, $key = 'id')
    {
        try {
            $where = [];
            foreach (explode(',', $key) as $v) {
                $where[$v] = $data[$key];
            }
            $this->model = model($name);
            if ($this->model->allowField($field)->validate($rule)->save($data, $where)) {
                return true;
            } else {
                return $this->errorMsg = $this->model->getError();
            }
        } catch (\Exception $e) {
            return $this->errorMsg = config('app_debug') ? $e->getMessage() : '请求错误';
        }
    }

    /**
     * 删除记录
     * @param string $name 模型
     * @param array $data 数据
     * @param string $key 主键
     * @return bool|mixed
     */
    public function delete($name, $data, $key = 'id')
    {
        try {
            $this->model = model($name);
            if ($this->model->where($key, 'in', $data[$key])->delete()) {
                return true;
            } else {
                return $this->errorMsg = '删除失败';
            }
        } catch (\Exception $e) {
            return $this->errorMsg = config('app_debug') ? $e->getMessage() : '请求错误';
        }
    }

    /**
     * 保存多个数据到当前数据对象
     * @param string $name 模型
     * @param array $data 数据
     * @param bool $rule 是否开启认证
     * @param string|array $field 允许写入的字段 如果为true只允许写入数据表字段
     * @return bool|mixed
     */
    protected function saveAll($name, $data, $rule = true, $field = true)
    {
        try {
            $this->model = model($name);
            if ($this->model->allowField($field)->validate($rule)->saveAll($data)) {
                return true;
            } else {
                return $this->errorMsg = $this->model->getError();
            }
        } catch (\Exception $e) {
            return $this->errorMsg = config('app_debug') ? $e->getMessage() : '请求错误';
        }
    }
    //API访问需要的信息
    function get_site_info($type=1){
        $info['domain']=get_domain();
		$info['authorcode']=config('author_code');
        $info['server']='https://www.yunknet.cn/';
        if($type==1){
            $info['KeyID']=config('KeyID');
            $info['KeySecret']=config('KeySecret');
            $info['EndPoint']=config('EndPoint');
            $info['AliUserId']=config('AliUserId');
            $info['bucket']=config('bucket');
            $info['region']=config('region');
        }
        if($type==2){
            $info['partner_id']=config('partner_id');
            $info['partner_key']=config('partner_key');
            $info['private_domain']=config('private_domain');
        }
        if($type==3){
            $info['talkauthkey']=config('talkauthkey');
            $info['talkdomain']=config('talkdomain');
        }
        if($type==4){
            $info['agoraAppid']=config('agoraAppid');
            $info['agoraRestfulId']=config('agoraRestfulId');
            $info['agoraRestfulKey']=config('agoraRestfulKey');
            $info['agoraCustomerId']=config('agoraCustomerId');
            $info['agoraCustomerSecret']=config('agoraCustomerSecret');
            $info['whiteAK']=config('whiteAK');
            $info['whiteSK']=config('whiteSK');
            $info['whiteAppIdentifier']=config('whiteAppIdentifier');
        }
        if($type==5){
            $info['panoAppid']=config('panoAppid');
            $info['panoSecret']=config('panoSecret');
        }
        return $info;
    }
    /**
     * 发送消息
     */
    function sentMessage($type,$uid,$fromuid,$message,$messagetype,$messageid){
       $res= db('message')->insert(['uid'=>$uid,'type'=>$type,'fromuid'=>$fromuid,'message'=>$message,'messagetype'=>$messagetype,'messageid'=>$messageid, 'addtime'=>date("Y-m-d H:i:s",time()),'status'=>0]);
       if($res){
           return true;
       }else{
           return false;
       }
    }
    /**
     * 阅读消息
     */
    function readMessage($id){
        db('message')->where(['id'=>$id])->update(['status'=>1]);
    }
}
