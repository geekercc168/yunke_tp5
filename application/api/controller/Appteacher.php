<?php
namespace app\api\controller;
use app\common\controller\IndexBase;
class Appteacher extends IndexBase
{
   function getTeacherList(){
       $param = $this->request->param();
       $teacher=model('admin')->field(['id','showname','sign','brief'])->where('status',1)->orderRaw('rand()')->paginate($param['pagesize']);
       foreach ($teacher as $k=> $value) {
           $teacher[$k]['picture']=formatUrl(defaultAvatar(getAvatar($teacher[$k]['id'])));
           $teacher[$k]['brief']=strip_tags(removeImgHtml($teacher[$k]['brief']));
       }
       return  json_encode($teacher);
   }
   function getTeacherInfo(){
       $param=$this->request->param();
       $teacherInfo=model('admin')->where('id',$param['id'])->field('id,uid,showname,brief')->find();
       $teacherInfo['brief']=cleanhtml($teacherInfo['brief']);
       $teacherInfo['brief']=preg_replace_callback('/<[img|IMG].*?src=[\'| \"](?![http|https])(.*?(?:[\.gif|\.jpg]))[\'|\"].*?[\/]?>/',
           function ($r) {
               if(strstr($r[1], 'oss-')){
                   $str = 'http://'.$r[1];
               }else{
                   $str = 'http://'.$_SERVER['HTTP_HOST'].$r[1];
               }
               return str_replace($r[1], $str, $r[0]);
           },$teacherInfo['brief']
       );
       $videoCourseList=model('videoCourse')->order('sort_order asc,addtime desc')->where(['status'=>1,'teacher_id'=>$param['id']])->select();
       $liveCourseList=model('liveCourse')->order('sort_order asc,addtime desc')->where(['status'=>1,'teacher_id'=>$param['id']])->select();
       foreach ($videoCourseList as $k=> $value) {
           $videoCourseList[$k]['stuNum']=getUserNum($videoCourseList[$k]['id'],1);
           $videoCourseList[$k]['picture']=formatUrl($videoCourseList[$k]['picture']);
           $videoCourseList[$k]['teacherName']=getTeacherName($videoCourseList[$k]['teacher_id']);
           $videoCourseList[$k]['teacherImg']=formatUrl(defaultAvatar(getAvatar(($videoCourseList[$k]['teacher_id']))));
           $videoCourseList[$k]['youxiaoqi']=youxiaoqi(($videoCourseList[$k]['youxiaoqi']));
           $videoCourseList[$k]['xueshi']=getCourseNum(($videoCourseList[$k]['id']),$videoCourseList[$k]['type']);
       }
       foreach ($liveCourseList as $i=> $value) {
           $liveCourseList[$i]['stuNum']=getUserNum($liveCourseList[$i]['id'],2);
           $liveCourseList[$i]['picture']=formatUrl($liveCourseList[$i]['picture']);
           $liveCourseList[$i]['teacherName']=getTeacherName(($liveCourseList[$i]['teacher_id']));
           $liveCourseList[$i]['teacherImg']=formatUrl(defaultAvatar(getAvatar(($liveCourseList[$i]['teacher_id']))));
           $liveCourseList[$i]['surplus']=$liveCourseList[$i]['limit']-getUserNum($liveCourseList[$i]['id'],$liveCourseList[$i]['type']);
           $liveCourseList[$i]['youxiaoqi']=youxiaoqi(($liveCourseList[$i]['youxiaoqi']));
           $liveCourseList[$i]['xueshi']=getCourseNum(($liveCourseList[$i]['id']),$liveCourseList[$i]['type']);
       }
       $allCourse=array_merge($videoCourseList,$liveCourseList);
       $data['info']=$teacherInfo;
       $data['course']=$allCourse;
       return  json_encode($data);
   }
}