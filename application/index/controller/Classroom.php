<?php
namespace app\index\controller;
use app\common\controller\IndexBase;
use app\common\extend\alipay\Alipay;
use app\common\extend\wechat\WechatPay;
class Classroom extends IndexBase
{
    /**
     * 班级首页
     */
    function index(){
        $parent=model('courseCategory')->where(['pid'=>0])->order('sort_order asc')->select();
        $param = $this->request->param();
        if(!empty($param['parent'])){
            $secondclass=model('courseCategory')->where(['pid'=>$param['parent']])->order('sort_order asc')->select();
        }
        if(!empty($param['second'])){
            $thirdclass=model('courseCategory')->where(['pid'=>$param['second']])->order('sort_order asc')->select();
        }
        if(request()->isMobile()){
            $classroom = model('classroom')->where('is_top',1)->order('sort_order asc,id desc')->select();
        }else{
            $classroom = model('classroom')->where('is_top',1)->order('sort_order asc,id desc')->paginate(config('page_number'), false, ['query' => $param]);
        }
        $this->checkBangTel();
        return $this->fetch('index',['title'=>'班级中心','parent'=>$parent,'second'=>$secondclass,'third'=>$thirdclass,'list'=>$classroom]);
    }
    /**
     * 班级详情
     */
    function info(){
        session('returnUrl',$this->request->url());
        $param = $this->request->param();
        $param['id']=hashids_decode($param['id']);
        $classroominfo = model('classroom')->where(['id'=>$param['id']])->find();
        $cids=json_to_array($classroominfo['cids']);
        $isBuy=$this->checkBuied(is_user_login(),$classroominfo['id'],$classroominfo['type']);
        if(db('favourite')->where(['uid'=>is_user_login(),'cid'=>$param['id'],'type'=>$param['type']])->find()){
            $isfavourite='layui-hide';
            $nofavourite='';
        }else{
            $nofavourite='layui-hide';
            $isfavourite='';
        }
        model('classroom')->where(['id'=>$param['id']])->setInc('views');
        foreach($cids as $k=>$v) {
            $ids=explode('-',$cids[$k],2);
            $ids[0]==1?$videoIds[]=$ids[1]:$liveIds[]=$ids[1];
            $ids[0]==1?$videoStudied[$ids[1]]=getStuduedNum($ids[1],1):$liveStudied[$ids[1]]=getStuduedNum($ids[1],2);
            $ids[0]==1?$videoCourseNum[$ids[1]]=getCourseNum($ids[1],1):$liveCourseNum[$ids[1]]=getCourseNum($ids[1],2);
            $ids[0]==1?$videoProgress[$ids[1]]=round(100*getStuduedNum($ids[1],1)/getCourseNum($ids[1],1)):$liveProgress[$ids[1]]=round(100*getStuduedNum($ids[1],2)/getCourseNum($ids[1],2));
            if($ids[0]==1){
                $vdata=model('videoCourse')->where(['id'=>$ids[1]])->field('teacher_id')->find();
                $teacherId[$ids[1]]=$vdata['teacher_id'];
            }else{
                $ldata=model('liveCourse')->where(['id'=>$ids[1]])->field('teacher_id')->find();
                $teacherId[$ids[1]]=$ldata['teacher_id'];
            }
        }
        $comment=model('comment')->whereIn('cid',$videoIds)->where('cstype',1)->order('addtime desc')->limit(10)->paginate(10);
        $teacherIds= (array_flip(array_flip($teacherId)));
        $teachersInfo=model('admin')->whereIn('id',$teacherIds)->select();
        $AllProgress=round(100*(array_sum($videoStudied)+array_sum($liveStudied))/(array_sum($videoCourseNum)+array_sum($liveCourseNum)),2);
        $videoCourse=model('videoCourse')->order('sort_order asc,addtime desc')->whereIn('id',$videoIds)->select();
        $liveCourse=model('liveCourse')->order('sort_order asc,addtime desc')->whereIn('id',$liveIds)->select();
        $classroomUser=model('userCourse')->where(['cid'=>$param['id'],'type'=>3])->limit(36)->select();
        return $this->fetch('info',['title'=>$classroominfo['title'],'comment'=>$comment,'teachersInfo'=>$teachersInfo,'teacherIds'=>$teacherIds,'videoProgress'=>$videoProgress,'liveProgress'=>$liveProgress,
            'info'=>$classroominfo,'videoCourse'=>$videoCourse,'liveCourse'=>$liveCourse,'isBuy'=>$isBuy,'classroomUser'=>$classroomUser,'progress'=>$AllProgress,'isfavourite'=>$isfavourite,'nofavourite'=>$nofavourite]);
    }
    /**
     * 检测是否购买了课程
     */
    function checkBuied($uid,$cid,$type){
        if(db('userCourse')->where(['uid'=>$uid,'cid'=>$cid,'type'=>$type])->find()){
            return true;
        }else{
            return false;
        }
    }
}