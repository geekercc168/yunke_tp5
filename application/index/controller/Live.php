<?php

namespace app\index\controller;

use app\common\controller\IndexBase;

class live extends IndexBase
{
    protected function _initialize()
    {
        parent::_initialize();
        $this->checkBangTel();
    }
    function enterRoom(){
        header("Content-Type: application/json");
        $param = $this->request->param();
        $channel_id=$param['room'];
        $user=$param['user'];
        $app_id='rs4wjhy7';
        $app_key='c2615a42b0c94b80cdf679215aaad9fd';
        $user_id = $this->CreateUserID($channel_id, $user);
        $nonce = 'AK-' . uniqid();
        $timestamp = strtotime(date('Y-m-d H:i:s', strtotime('+2day')));
        $token = $this->CreateToken($app_id, $app_key, $channel_id, $user_id, $nonce, $timestamp);
        $username = $user_id . '?appid=' . $app_id . '&channel=' . $channel_id . '&nonce=' . $nonce . '&timestamp=' . $timestamp;
        $data=['code' => 0,'data' =>['appid'=>$app_id,'userid'=>$user_id,'gslb'=>['https://rgslb.rtc.aliyuncs.com'],'token'=>$token,'nonce'=>$nonce,'timestamp'=>$timestamp,'turn'=>['username'=>$username,'password'=>$token]]];
        echo json_encode($data);
    }
    function CreateUserID($channel_id, $user)
    {
        $s = $channel_id . '/' . $user;
        $uid = hash('sha256', $s);
        return substr($uid, 0, 16);
    }
    function CreateToken($app_id, $app_key, $channel_id, $user_id, $nonce, $timestamp)
    {
        $s = $app_id . $app_key . $channel_id . $user_id . $nonce . $timestamp;
        $token = hash('sha256', $s);
        return $token;
    }
}
