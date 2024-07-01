<?php
namespace app\api\controller;
use app\common\extend\alipay\Alipay;
use app\common\extend\wechat\WechatPay;
use app\common\controller\IndexBase;
class Appuser extends IndexBase
{
    /**
     * 用户登录
     */
    function login(){
        $param = $this->request->param();
        if($param['loginType']=='pass'){
            if(check_mobile($param['username'])){
                $where['mobile']=$param['username'];
                $where['password']=md5($param['password']);
            }else{
                $where['username']=$param['username'];
                $where['password']=md5($param['password']);
            }
            $user = model('user')->where($where)->find();
            if ($user) {
                if( $user['status'] != 1){
                    $res['code']=1;
                    $res['msg']='账号已禁用';
                    return (json_encode($res)) ;
                }else{
                    model('user')->save([
                        'last_login_time' => time(),
                        'last_login_ip'   => $this->request->ip(),
                        'login_count'     => $user['login_count'] + 1,
                    ], ['id' => $user['id']]);
                    $res['code']=0;
                    $res['msg']='登录成功';
                    $this->addjifen('login',$user['id']);
                    $user['avatar']=formatUrl(defaultAvatar(getAvatar($user['id'])));
                    if($user['schoolId']){
                        $user['schoolname']=getUserSchool($user['schoolId']);
                    }
                    if($user['greadId']){
                        $user['greadname']=getUserGrade($user['greadId']);
                    }

                    if(empty($user['nickname'])){
                        $user['nickname']=$user['username'];
                    }
                    $res['userInfo']=$user;
                    return (json_encode($res)) ;
                }
            } else {
                $res['code']=1;
                $res['msg']='账号或密码错误';
                return (json_encode($res)) ;
            }
        }
        if($param['loginType']=='captcha'){
            if(session('telphoneCode')!=$param['codeverify']){
                $res['code']=1;
                $res['msg']='手机验证码错误';
                return (json_encode($res)) ;
            }
            if(session('telphone')!=$param['mobile']){
                $res['code']=1;
                $res['msg']='手机号码输入有误';
                return (json_encode($res)) ;
            }
            if($user=model('user')->where(['mobile' => $param['mobile']])->find()){
                $res['code']=0;
                $res['msg']='登录成功';
                $user['avatar']=formatUrl(defaultAvatar(getAvatar($user['id'])));
                $this->addjifen('login',$user['id']);
                if($user['schoolId']){
                    $user['schoolname']=getUserSchool($user['schoolId']);
                }
                if($user['greadId']){
                    $user['greadname']=getUserGrade($user['greadId']);
                }

                if(empty($user['nickname'])){
                    $user['nickname']=$user['username'];
                }
                $res['userInfo']=$user;
                return (json_encode($res)) ;
            }else{
                $res['code']=1;
                $res['msg']='手机号尚未注册';
                return (json_encode($res)) ;
            }
        }
    }
    /**
     * 用户注册手机验证码
     */
    function regCode(){
        $param = $this->request->param();
        $param['mobile'];
        $code = rand_number(5);
        $templateParam = array("code"=>$code);
        $templateCode=unserialize(config('SmsTemplates_MC'));
        $config['KeyID']=config('KeyID');
        $config['KeySecret']=config('KeySecret');
        $config['SmsSign']=config('SmsSign');
        $tempres=sendSMS($param['mobile'],$templateParam,$templateCode['TemplatesId'],$config);
        if($tempres['Code']=='OK'){
            $res['code']=0;
            $res['msg']='发送成功';
            $res['telphone']=$param['mobile'];
            $res['regCode']=$code;
            session('telphoneCode',$code);
            session('telphone',$param['mobile']);
        }else{
            $res['code']=0;
            $res['msg']=$tempres['Message'];
        }
        return (json_encode($res));
    }
    /**
     * 用户注册
     */
    public function register(){
        $param = $this->request->param();
        if(model('user')->where(['mobile' => $param['mobile']])->find()){
            $res['code']=1;
            $res['msg']='手机号已被注册';
            return (json_encode($res));

        }else{
            if($this->insert('user',$param,$rule = false) === true){
                $user= model('user')->where(['mobile' => $param['mobile']])->find();
                $res['code']=0;
                $res['userInfo']=$user;
                $res['userInfo']['avatar']=formatUrl(defaultAvatar(getAvatar($res['userInfo']['id'])));
                $res['msg']='注册成功';
                return (json_encode($res));
            }else{
                $res['code']=1;
                $res['msg']=$this->errorMsg;
                return (json_encode($res));
            }
        }
    }

    /**
     * 密码修改
     */
    function repass(){
        $param = $this->request->param();
        if(!model('user')->where(['mobile' => $param['mobile']])->find()){
            $res['code']=1;
            $res['msg']='该手机号还未注册';
        }
        if (model('user')->where(['mobile'=>$param['mobile']])->update(['password'=>md5($param['password'])])) {
            $res['code']=0;
            $res['msg']='密码重置成功';
        }
        return (json_encode($res));

    }
    /**
     * 用户信息更新
     */
    function getUserInfo(){
        $param = $this->request->param();
        $res['userInfo']=model('user')->where(['id' => $param['uid']])->find();
        $res['regfield']= model('regfield')->select();
        return (json_encode($res));
    }
    /**
     * 用户信息更新
     */
    function updataUserInfo(){
        $param = $this->request->param();
        if($userInfo=model('user')->where(['id' => $param['uid']])->find()){
            $userInfo['avatar']=formatUrl(defaultAvatar(getAvatar($userInfo['id'])));
            $userInfo['coupon']= model('coupon')->order('id desc')->where(['userId'=>$param['uid'],'status'=>0,'usestatus'=>0])->where('userendtime','> time',date("Y-m-d H:i:s",time()))->count();
            if(empty($userInfo['nickname'])){
                $userInfo['nickname']=$userInfo['username'];
            }
            $res['code']=0;
            $res['msg']='信息更新成功';
            $res['userInfo']=$userInfo;
            return (json_encode($res)) ;
        }else{
            $res['code']=1;
            $res['msg']='信息更新失败';
            return (json_encode($res)) ;
        }
    }

    /**
     * 我的身份
     */
    function getUserIdentity (){
        $param = $this->request->param();
        $is_teacher=  model('user')->where(['id'=>$param['id']])->value('is_teacher');
        if($is_teacher==1){
            $res['msg']='教师账户';
        }elseif ($vip=model('vip')->where(['uid'=>$param['id']])->where('endtime','>',date("Y-m-d H:i:s"))->find()){
            $res['msg']='VIP会员 剩余时间：'.getLastTime(strtotime($vip['endtime']));
        }else {
            $res['msg']='学生账户';
        }
        return (json_encode($res)) ;
    }
    /**
     * 获取我的课程
     */
    function getMycourse(){
        $param = $this->request->param();
        $myCourse=model('userCourse')->order('addtime desc')->where(['uid'=>$param['uid']])->where(['type'=>array('in','1,2')])->paginate($param['pagesize']);
        foreach ($myCourse as $key => $value) {
            $myCourse[$key]['courseInfo']=$this->appGetCouseInfo($myCourse[$key]['cid'],$myCourse[$key]['type']);
            $myCourse[$key]['xueshi']=getCourseNum($myCourse[$key]['cid'],$myCourse[$key]['type']);
            $myCourse[$key]['teacherName']=getTeacherName($myCourse[$key]['courseInfo']['teacher_id']);
            $myCourse[$key]['teacherImg']=formatUrl(defaultAvatar(getAvatar(getUidFromTid($myCourse[$key]['courseInfo']['teacher_id']))));
            $myCourse[$key]['progress']=round(100*getStuduedNum($myCourse[$key]['cid'],$myCourse[$key]['type'],$param['uid'])/getCourseNum($myCourse[$key]['cid'],$myCourse[$key]['type']));
            $myCourse[$key]['remain'] =$myCourse[$key]['courseInfo']['youxiaoqi']==0?'永久有效': get_course_last_time($myCourse[$key]['courseInfo'],3,$param['uid']);
        }
        return (json_encode($myCourse)) ;
    }
    /**
     * 获取我的收藏
     */
    function getMyfavourite(){
        $param = $this->request->param();
        $myCourse=db('favourite')->order('id desc')->where(['uid'=>$param['uid']])->select();
        foreach ($myCourse as $key => $value) {
            $myCourse[$key]['courseInfo']=$this->appGetCouseInfo($myCourse[$key]['cid'],$myCourse[$key]['type']);
            $myCourse[$key]['xueshi']=getCourseNum($myCourse[$key]['cid'],$myCourse[$key]['type']);
            $myCourse[$key]['teacherName']=getTeacherName($myCourse[$key]['courseInfo']['teacher_id']);
            $myCourse[$key]['teacherImg']=formatUrl(defaultAvatar(getAvatar($myCourse[$key]['courseInfo']['teacher_id'])));
            $myCourse[$key]['stuNum']=getUserNum($myCourse[$key]['cid'],$myCourse[$key]['type']);
            if($myCourse[$key]['type']==2){
                $myCourse[$key]['surplus']=$myCourse[$key]['courseInfo']['limit']-getUserNum($myCourse[$key]['cid'],2);
            }
        }
        return json_encode($myCourse);
    }
    /**
     * 获取我的证书
     */
    function mycertificate(){
        $param = $this->request->param();
        $mycertificate=db('certificate')->where(['uid'=>$param['uid']])->select();
        foreach ($mycertificate as $key => $value) {
            $ctype=db('myexam')->where(['uid'=>$param['uid'],'eid'=>$value['eid'],'cid'=>$value['cid']])->value('ctype');
            $mycertificate[$key]['courseName']=getCourseName($value['cid'],$ctype);
            $mycertificate[$key]['imgurl']=is_https().get_domain().'/static/default/certificate/'.$value['imgurl'];
        }
        return json_encode($mycertificate);
    }
    /**
     * 获取我的订单
     */
    function getMyorder(){
        $param = $this->request->param();
        $myOrder=model('order')->order('addtime desc')->where('ctype','neq',4)->where(['uid'=>$param['uid']])->paginate($param['pagesize']);
        foreach ($myOrder as $key => $value) {
            $myOrder[$key]['CourseName']=getCourseName($value['cid'],$value['ctype']);
            $myOrder[$key]['CoursePrice']=getCoursePrice($value['cid'],$value['ctype'])?:0;
            $myOrder[$key]['total']=$myOrder[$key]['total']?:0;
        }
        $res['code']=0;
        $res['order']=$myOrder;
        return (json_encode($res)) ;
    }
    /**
     * 删除订单
     */
    function delMyOrder(){
        if ($this->delete('order', $this->request->param()) === true) {
            $res['code']=0;
            $res['msg']='删除成功';
        } else {
            $res['code']=1;
            $res['msg']='删除失败';
        }
        return (json_encode($res)) ;
    }
    /**
     * 点卡充值
     */
    function myCard(){
        $param = $this->request->param();
        $cardInfo= model('card')->where(['number'=>$param['number']])->find();
        $userInfo= model('user')->where(['id'=>$param['uid']])->find();
        if(!$cardInfo){
            $res['code']=1;
            $res['msg']='此卡号错误，请核实重新输入';
        }elseif($cardInfo['usestatus']==1){
            $res['code']=1;
            $res['msg']='此卡号已被使用，请核实重新输入';
        }elseif ($cardInfo['status']==1){
            $res['code']=1;
            $res['msg']='此卡号已被禁用，请核实重新输入';
        }else{
            $yue=$cardInfo['money']+$userInfo['yue'];
            if ($this->update('user', ['id'=>$param['uid'],'yue'=>$yue], input('_verify', false)) === true) {
                $this->update('card', ['number'=>$param['number'],'usestatus'=>1,'buystatus'=>1,'uid'=>$param['uid']], input('_verify', false),$field = true,$key='number');
                $res['code']=0;
                $res['msg']='充值成功';
            } else {
                $res['code']=1;
                $res['msg']=$this->errorMsg;
            }
        }
        return json_encode($res) ;
    }
    /**
     * 我的测评
     */
    function getMyCeping(){
        $param = $this->request->param();
        $myexam=model('myexam')->where(['uid'=>$param['uid'],'eid' => array('neq','')])->select();
        foreach ($myexam as $key => $value) {
            $examinfo=json_to_array(model('exams')->where('id',$value['eid'])->value('examsetting'));
            $myexam[$key]['title']=get_exam_title($value['eid']);
            $myexam[$key]['type']=model('exams')->where('id',$value['eid'])->value('examtype');
            $myexam[$key]['examscore']=$examinfo['examscore'];
            $myexam[$key]['passscore']=$examinfo['passscore'];
            if($value['cid']){
                $certificateInfo=db('certificate')->where(['cid'=>$value['cid'],'uid'=>$param['uid'],'eid'=>$value['eid']])->value('imgurl');
                $myexam[$key]['certificate']=formatUrl('/static/default/certificate/'.$certificateInfo);
            }else{
                $myexam[$key]['certificate']='';
            }
        }
        return json_encode($myexam) ;
    }

    /**
     * 我的优惠卷
     */
    function getMyCoupon(){
        $param = $this->request->param();
        $res['code']=0;
        $res['noused']=$Coupon=model('coupon')->order('id desc')->where(['userId'=>$param['uid'],'status'=>0,'usestatus'=>0])->where('userendtime','> time',date("Y-m-d H:i:s",time()))->select();
        $res['used']=$Coupon=model('coupon')->order('id desc')->where(['userId'=>$param['uid'],'status'=>0,'usestatus'=>1])->select();
        $res['guoqi']=$Coupon=model('coupon')->order('id desc')->where(['userId'=>$param['uid'],'status'=>0,'usestatus'=>0])->where('userendtime','<= time',date("Y-m-d H:i:s",time()))->select();
        return json_encode($res) ;
    }
    /**
     * 上传头像
     */
    function upImage(){
        $data = model('system')->where('name', 'upload_image')->find();
        $upload=unserialize($data['value']);
        $param = $this->request->param();
        $exename  = $this->getExeName($_FILES['file']['name']);
        $tmp_name=$_FILES['file']['tmp_name'];
        if($upload['location']==1){
            if(!empty($_FILES['file'])){
                if($exename != 'png' && $exename != 'jpg' && $exename != 'gif'){
                    $res['code']=1;
                    $res['msg']='不允许的扩展名';
                    return json_encode($res) ;
                }
                $temp= uniqid().'.'.$exename;
                $imageSavePath ='./upload/avatar/'. $temp;
                if(move_uploaded_file($tmp_name, $imageSavePath)){
                    $res['code']=0;
                    $res['imageSavePath']='/upload/avatar/'. $temp;
                    return json_encode($res) ;
                }
            }
        }else{
            try {
                $file = $this->request->file('file');
                $temp= uniqid().'.'.$exename;
                $ossClient=controller('api/uploader')->new_oss();
                $ossClient->uploadFile(config('Bucket'), 'files'.$param['uid'].'/avatar/'.$temp,$file->getInfo()['tmp_name']);
                $url='//'.config('Bucket').'.'.config('EndPoint').'/'.'files'.$param['uid'].'/avatar/'.$temp;
                $res['code']=0;
                $res['imageSavePath']=$url;
                return json_encode($res) ;

            } catch (\Exception $e) {
                return json_encode(['code' => 1, 'msg' => $e->getMessage()]);
            }
        }

    }
    public function getExeName($fileName){
        $pathinfo      = pathinfo($fileName);
        return strtolower($pathinfo['extension']);
    }
    /**
     * 更新头像
     */
    public function upavatar(){
        $param = $this->request->param();
        if($this->update('user', $param, input('_verify', false)) === true){
            $res['code']=0;
            $res['msg']='更新成功';
            $res['info']= model('user')->where(['id' => $param['id']])->find();
            $res['info']['schoolname']=getUserSchool($res['info']['schoolId']);
            $res['info']['greadname']=getUserGrade($res['info']['greadId']);
        }
        $res['info']['avatar']=formatUrl($res['info']['avatar']);
        return json_encode($res) ;
    }
    /**
     * 获取VIP信息
     */
    public function getVipInfo(){
        $res['yueprice']=config('vipyueprice');
        $res['jiprice']=config('vipjiprice');
        $res['nianprice']=config('vipnianprice');
        return json_encode($res) ;
    }
    public function createOrder(){
        $param=$this->request->param();
        if($param['type']=='yue'){
            $duration='1';
            $order['title']="月度VIP会员";
            $price=config('vipyueprice');
        }
        if($param['type']=='ji'){
            $duration='3';
            $order['title']="季度VIP会员";
            $price=config('vipjiprice');
        }
        if($param['type']=='nian'){
            $duration='12';
            $order['title']="年度VIP会员";
            $price=config('vipnianprice');
        }
        if(($coupon=model('coupon')->where(['code'=>$param['usedCoupon']])->find()) && !empty($param['usedCoupon'])){
            $order['total']= round($price*$coupon['rate']/10,1);
        }else{
            $order['total']= $price;
        }
        $order['ctype']=4;
        $order['duration']=$duration;
        $order['state']=0;
        $order['addtime']=date('Y-m-d H:i:s', time());
        $order['uid']=$param['uid'];
        $order['paytype']=$param['payment'];
        $order['orderid']= date('Ymdhms');
        $res=$this->insert('order',$order);
        if($res === true){
            return $this->payment($order);
        }
        else {
            $this->error($this->errorMsg);
        }
    }
    /**
     * 改变优惠券的使用状态
     */
    function updatacoupon(){
        $param=$this->request->param();
        db('coupon')->where(['code'=>$param['usedCoupon']])->update(['usestatus'=>1]);
    }
    /**
     * 订单支付
     */
    function payment($order){
        if($order['paytype']=='alipay'){
            return  $this->alipay($order);
        }
        if($order['paytype']=='wxpay'){
            return  $this->wxpay($order);
        }
        if($order['paytype']=='yuepay'){
            return  $this->yuepay($order);
        }
    }
    /**
     * 微信支付
     */
    function wxpay($order){
        $data=$this->wechatAppConfig();
        $name=$order['title'];
        $payData = [
            'body'=>$order['title'],
            'out_trade_no'=>$order['orderid'],
            'total_fee'=> $order['total']*100,
            'trade_type'=> 'APP',
            'wap_url'=>is_https().get_domain(),
            'wap_name'=>$name,
            'notify_url'=>get_domain(). '/index/course/notifywechat/',
        ];
        $wechatPay = new WechatPay($data);
        $info = $wechatPay->unifiedOrder($payData);
        $res=$wechatPay->getAppParam($info['prepay_id']);
        return (json_encode($res)) ;
    }
    /**
     * 支付宝支付
     */
    function alipay($order){
        $payData = [
            'order_no' =>$order['orderid'],
            'order_price' => $order['total'],
            'subject' => $order['title'],
            'notify_url' =>is_https().get_domain(). '/index/user/notifyalipay/',
            'return_url'=>is_https().get_domain(). '/index/user/return_url/',
        ];
        $alipay = new Alipay($this->alipayConfig());
        $info = $alipay->placeApp($payData);
        return (json_encode($info)) ;
    }

    /**
     * 判断是否是VIP会员
     */
    function isvip(){
        $param=$this->request->param();
        $vip=model('vip')->where(['uid'=>$param['uid']])->where('endtime','>',date("Y-m-d H:i:s"))->find();
        if($vip){
            $res['isvip']=true;
            $res['lasttime']=getLastTime(strtotime($vip['endtime']));
        }else{
            $res['isvip']=false;
        }
       
        return (json_encode($res)) ;
    }
    /**
     * 获取APP用户协议列表
     */
    function getagreement(){
        $list = model('agreement')->field('id,type')->order('id desc')->select();
        return (json_encode($list)) ;
    }
    /**
     * 获取APP用户协议内容
     */
    function getagreementContents(){
        $data = model('agreement')->where('id', input('id'))->find();
        return (json_encode($data)) ;
    }
    /**
     * 获取我的直播课程表
     */
    function getMylive(){
        $param=$this->request->param();
        $myCourse=model('userCourse')->where(['uid'=>$param['uid'],'type'=>2])->select();
        foreach ($myCourse as $key => $value) {
            $cids[]=$myCourse[$key]['cid'];
        }
        $list=model('liveSection')->where(['csid'=>array('in',$cids)])->select();
        if($list){
            foreach ($list as $key => $value) {
                $data[$key]['date']=date("Y-m-d",strtotime($list[$key]['starttime']));
                $data[$key]['starttime']=$list[$key]['starttime'];
                $data[$key]['liveStatus']=getLiveStatus($list[$key]['starttime'],$list[$key]['duration'],'app');
                $data[$key]['title']='有课';
                $data[$key]['coursetitle']=getCourseName($list[$key]['csid'],2);
                $data[$key]['sectitle']=$list[$key]['title'];
                $data[$key]['cid']=$list[$key]['csid'];
            }
        }else{
            $data[0]['date']='';
            $data[0]['title']='';
        }
        return (json_encode($data)) ;
    }
    /**
     * 获取我的班级
     */
    function getMyClsss(){
        $param=$this->request->param();
        $myClass=model('userCourse')->order('addtime desc')->where(['uid'=>$param['uid'],'type'=>3])->paginate($param['pagesize']);
        foreach ($myClass as $key => $value) {
            $myClass[$key]['classInfo']=model('classroom')->where(['id'=>$myClass[$key]['cid']])->find();
            $myClass[$key]['remain'] =$myClass[$key]['classInfo']['youxiaoqi']==0?'永久有效': getLastTime(strtotime("+".$myClass[$key]['classInfo']['youxiaoqi']."days",strtotime( $myClass[$key]['addtime'])));
            $myClass[$key]['progress'] = getAllProgress($myClass[$key]['cid'], $param['uid']);
            $myClass[$key]['isDaoQi']=(strtotime("+".$myClass[$key]['classInfo']['youxiaoqi']."days",strtotime($myClass[$key]['addtime']))<= time() && $myClass[$key]['classInfo']['youxiaoqi']>0)?true:false;
        }
        return (json_encode($myClass)) ;
    }
    /**
     * 获取注册绑定信息
     */
   function getregfield(){
       $res['regfield']= model('regfield')->order('id','desc')->select();
       return (json_encode($res)) ;
   }
   function getschool(){
       $res['school']=model('school')->order('sort_order asc')->select();
       return (json_encode($res)) ;
   }
   function getgrade(){
       $res['grade']=model('grade')->order('sort_order asc')->select();
       return (json_encode($res)) ;
   }
    /**
     * 获取是的问答
     */
    function getMyWenDa(){
        $param=$this->request->param();
        $res['mytopic']=model('forumTopic')->order('addtime desc')->where('uid',$param['uid'])->paginate(10);
        foreach ( $res['mytopic'] as $key => $value) {
            $res['mytopic'][$key]['replaycount']=model('forumReply')->where(['tid'=>$value['id'],'touid'=>0])->count();
            $res['mytopic'][$key]['plateName']=getPlateName($value['pid']);
            $res['mytopic'][$key]['addtime']=get_last_time($value['addtime']);
        }
        $res['myreplay']=model('forumReply')->order('accept desc,addtime asc')->where('uid',$param['uid'])->paginate(10);
        return json_encode($res);
    }
    /**
     * 获取积分明细
     */
   function getJifenList(){
       $param=$this->request->param();
       $data = model('system')->where('name', 'jifen')->find();
       $res['list']=db('jifen')->where(['uid'=>$param['uid'],'addtime'=>date("Y-m-d",time())])->select();
       $res['mingxi']=unserialize($data['value']);
       return json_encode($res);
   }
    /**
     * 获取消息是否未读
     */
   function getMessageFlag(){
       $param=$this->request->param();
       if(db('message')->where(['uid'=>$param['uid'],'status'=>0])->find()){
           $res['flag']=0;
       }else{
           $res['flag']=1;
       }
       return json_encode($res);
   }
    /**
     * 获取消息
     */
   function getMyMessage (){
       $param=$this->request->param();
       $res=db('message')->where(['uid'=>$param['uid']])->order('id','desc')->select();
       foreach ( $res as $key => $value) {
           if($value['fromuid']!=0){
               $res[$key]['fromUserName']=getUserName($value['fromuid']);
           }
       }
       return json_encode($res);
   }
    /**
     * 读消息
     */
   function readMyMessage(){
       $param=$this->request->param();
       db('message')->where(['id'=>$param['id']])->setField('status', 1);
   }
    /**
     * 查询邮寄地址
     */
    function myaddress(){
        $param=$this->request->param();
        $res=db('address')->where(['uid'=>$param['uid']])->find();
        return json_encode($res);
    }
    /**
     * 添加邮寄地址
     */
    function addaddress(){
        $param=$this->request->param();
        $address=db('address')->where(['uid'=>$param['uid']])->find();
        if(!$address){
           if(db('address')->insert($param)){
               $res['code']=0;
               $res['msg']="修改成功";
           }else{
               $res['code']=1;
               $res['msg']="修改失败";
           }
        }else{
            if(db('address')->where('id', $address['id'])->update($param)){
                $res['code']=0;
                $res['msg']="修改成功";
            }else{
                $res['code']=1;
                $res['msg']="修改失败";
            }
        }
        return json_encode($res);
    }
    /**
     * 添加邮寄地址
     */
    function logout(){
        $param=$this->request->param();
        if(db('user')->where(['id'=>$param['uid']])->delete()){
            db('userCourse')->where(['uid'=>$param['uid']])->delete();
            db('userClassroom')->where(['uid'=>$param['uid']])->delete();
            db('comment')->where(['uid'=>$param['uid']])->delete();
            db('certificate')->where(['uid'=>$param['uid']])->delete();
            db('favourite')->where(['uid'=>$param['uid']])->delete();
            db('learned')->where(['uid'=>$param['uid']])->delete();
            db('message')->where(['uid'=>$param['uid']])->delete();
            db('vip')->where(['uid'=>$param['uid']])->delete();
            db('myexam')->where(['uid'=>$param['uid']])->delete();
            db('myquestions')->where(['uid'=>$param['uid']])->delete();
            db('coupon')->where(['userId'=>$param['uid']])->delete();
            $res['code']=0;
            $res['msg']="注销成功";
        }else{
            $res['code']=1;
            $res['msg']="注销失败";
        }
        return json_encode($res);
    }
    function updatamobile(){
        $param=$this->request->param();
        if(session('telphoneCode')!=$param['codeverify']){
            $res['code']=1;
            $res['msg']='验证码错误';
            return (json_encode($res)) ;
        }
        if(session('telphone')!=$param['mobile']){
            $res['code']=1;
            $res['msg']='手机号码有误';
            return (json_encode($res)) ;
        }
        if(db('user')->where('id', $param['uid'])->update(['mobile'=>$param['mobile']])){
            $res['info']= model('user')->where(['id' => $param['id']])->find();
            $res['code']=0;
            $res['msg']="修改成功";
            return (json_encode($res)) ;
        }else{
            $res['code']=1;
            $res['msg']="修改失败";
            return (json_encode($res)) ;
        }
    }
}