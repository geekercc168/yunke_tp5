<?php
namespace app\api\controller;
use app\common\controller\IndexBase;
vendor ('aliyuncs.oss-sdk-php.autoload');
use OSS\OssClient;
use OSS\Core\OssException;
class Appexam extends IndexBase
{
    /**
     * 获取分类
     */
    function getCategory(){
        $param = $this->request->param();
        $where=[];
        if($param['pid']>0){
            $where['pid']=$param['pid'];
        }
        $category=collection(model('courseCategory')->where($where)->field('id,appname as name')->order('sort_order asc')->select())->toArray();
        array_unshift($category,['id'=>0,'name'=>'全部']);
        return  json_encode($category);
    }
    function getCategoryNoall(){
        $param = $this->request->param();
        $where=[];
        if($param['pid']>0){
            $where['pid']=$param['pid'];
        }
        $category=collection(model('courseCategory')->where($where)->field('id,appname as name')->order('sort_order asc')->select())->toArray();
        return  json_encode($category);
    }
    /**
     * 获取知识点做题
     */
    function getPointlist(){
        $param = $this->request->param();
        $category=model('courseCategory')->order('sort_order asc')->select();
        $where=controller('index/course')->getCateId($category,$param);
        $knowledge=model('knowledge')->where('sectionid','in',$where)->orderRaw('rand()')->order('sort_order asc')->limit(6)->select();
        foreach ($knowledge as $i=> $value) {
            $knowledge[$i]['questionNum']=get_question_num($knowledge[$i]['id']);
        }
        return  json_encode($knowledge);
    }
    /**
     * 获取知识点信息
     */
    function getKnowerInfo(){
        $param = $this->request->param();
        $knowledge=model('knowledge')->where('id',$param['id'])->order('sort_order asc')->find();
        $myquestions=db('myquestions')->where(['uid'=>$param['uid'],'pointid'=> $param['id']])->find();
        $knowledge['doneNum']= count(json_to_array($myquestions['myquestions']));
        $knowledge['allNum']=get_question_num($knowledge['id']);
        $knowledge['errorNum']= count(json_to_array($myquestions['myerrors']));
        $knowledge['noDone']=  $knowledge['allNum']-$knowledge['doneNum'];
        if($knowledge['allNum']==0){
            $knowledge['lv']=0;
        }else{
            if($knowledge['doneNum']==0){
                $knowledge['lv']=0;
            }else{
                $knowledge['lv']=round(number_format(1-$knowledge['errorNum']/$knowledge['doneNum'],2)*100);
            }

        }
        return  json_encode($knowledge);
    }
    /**
     * 获取所有知识点做题
     */
    function getAllPointlist(){
        $param = $this->request->param();
        $category=model('courseCategory')->order('sort_order asc')->select();
        $coursemodel=new \app\index\controller\Course;
        $where=$coursemodel->getCateId($category,$param);
        $knowledge=model('knowledge')->where('sectionid','in',$where)->order('sort_order asc')->select();
        foreach ($knowledge as $i=> $value) {
            $knowledge[$i]['questionNum']=get_question_num($knowledge[$i]['id']);
        }
        return  json_encode($knowledge);
    }
    /**
     * 获取知识点下的题目
     */
    function getPoints(){
        $param = $this->request->param();
        $myquestion=db('myquestions')->where(['uid'=>$param['uid'],'pointid'=>$param['id']])->select();
        $question=model('questions')->orderRaw('rand()')->where(['questionknowsid'=>$param['id']])->whereNotIn('id',json_to_array($myquestion[0]['myquestions']))->limit('5')->select();
        $question=list_sort_by($question,'questiontype','asc');
        foreach ($question as $i=> $value) {
            $question[$i]['mark']=get_question_mark($question[$i]['questiontype']);
            $question[$i]['questiontype']=get_question_type($question[$i]['questiontype']);
            $question[$i]['question']= preg_replace_callback('/<[img|IMG].*?src=[\'| \"](?![http|https])(.*?(?:[\.gif|\.jpg]))[\'|\"].*?[\/]?>/',
                function ($r) {
                    if(strstr($r[1], 'oss-')){
                        $str = 'http://'.$r[1];
                    }else{
                        $str = 'http://'.$_SERVER['HTTP_HOST'].$r[1];
                    }
                    return str_replace($r[1], $str, $r[0]);
                },$question[$i]['question']
            );
            $question[$i]['questionselect']= preg_replace_callback('/<[img|IMG].*?src=[\'| \"](?![http|https])(.*?(?:[\.gif|\.jpg]))[\'|\"].*?[\/]?>/',
                function ($r) {
                    if(strstr($r[1], 'oss-')){
                        $str = 'http://'.$r[1];
                    }else{
                        $str = 'https://'.$_SERVER['HTTP_HOST'].$r[1];
                    }
                    return str_replace($r[1], $str, $r[0]);
                },$question[$i]['questionselect']
            );
            $question[$i]['questiondescribe']= preg_replace_callback('/<[img|IMG].*?src=[\'| \"](?![http|https])(.*?(?:[\.gif|\.jpg]))[\'|\"].*?[\/]?>/',
                function ($r) {
                    if(strstr($r[1], 'oss-')){
                        $str = 'http://'.$r[1];
                    }else{
                        $str = 'https://'.$_SERVER['HTTP_HOST'].$r[1];
                    }
                    return str_replace($r[1], $str, $r[0]);
                },$question[$i]['questiondescribe']
            );
            $question[$i]['questionanswer']= preg_replace_callback('/<[img|IMG].*?src=[\'| \"](?![http|https])(.*?(?:[\.gif|\.jpg]))[\'|\"].*?[\/]?>/',
                function ($r) {
                    if(strstr($r[1], 'oss-')){
                        $str = 'http://'.$r[1];
                    }else{
                        $str = 'https://'.$_SERVER['HTTP_HOST'].$r[1];
                    }
                    return str_replace($r[1], $str, $r[0]);
                },$question[$i]['questionanswer']
            );
        }
        return  json_encode($question);
    }
    /**
     * 获取我的错题
     */
    function getMyErrors(){
        $param = $this->request->param();
        $myquestion=db('myquestions')->where(['uid'=>$param['uid'],'pointid'=>$param['id']])->find();
        $question=model('questions')->orderRaw('rand()')->whereIn('id',json_to_array($myquestion['myerrors']))->limit('5')->select();
        $question=list_sort_by($question,'questiontype','asc');
        foreach ($question as $i=> $value) {
            $question[$i]['mark']=get_question_mark($question[$i]['questiontype']);
            $question[$i]['questiontype']=get_question_type($question[$i]['questiontype']);
            $question[$i]['question']= preg_replace_callback('/<[img|IMG].*?src=[\'| \"](?![http|https])(.*?(?:[\.gif|\.jpg]))[\'|\"].*?[\/]?>/',
                function ($r) {
                    if(strstr($r[1], 'oss-')){
                        $str = 'http://'.$r[1];
                    }else{
                        $str = 'https://'.$_SERVER['HTTP_HOST'].$r[1];
                    }
                    return str_replace($r[1], $str, $r[0]);
                },$question[$i]['question']
            );
            $question[$i]['questionselect']= preg_replace_callback('/<[img|IMG].*?src=[\'| \"](?![http|https])(.*?(?:[\.gif|\.jpg]))[\'|\"].*?[\/]?>/',
                function ($r) {
                    if(strstr($r[1], 'oss-')){
                        $str = 'http://'.$r[1];
                    }else{
                        $str = 'https://'.$_SERVER['HTTP_HOST'].$r[1];
                    }
                    return str_replace($r[1], $str, $r[0]);
                },$question[$i]['questionselect']
            );
            $question[$i]['questiondescribe']= preg_replace_callback('/<[img|IMG].*?src=[\'| \"](?![http|https])(.*?(?:[\.gif|\.jpg]))[\'|\"].*?[\/]?>/',
                function ($r) {
                    if(strstr($r[1], 'oss-')){
                        $str = 'http://'.$r[1];
                    }else{
                        $str = 'https://'.$_SERVER['HTTP_HOST'].$r[1];
                    }
                    return str_replace($r[1], $str, $r[0]);
                },$question[$i]['questiondescribe']
            );
            $question[$i]['questionanswer']= preg_replace_callback('/<[img|IMG].*?src=[\'| \"](?![http|https])(.*?(?:[\.gif|\.jpg]))[\'|\"].*?[\/]?>/',
                function ($r) {
                    if(strstr($r[1], 'oss-')){
                        $str = 'http://'.$r[1];
                    }else{
                        $str = 'http://'.$_SERVER['HTTP_HOST'].$r[1];
                    }
                    return str_replace($r[1], $str, $r[0]);
                },$question[$i]['questionanswer']
            );
        }
        return  json_encode($question);
    }
    /**
     * 删除知识点做题记录
     */
    function clearpoint(){
        $param = $this->request->param();
        if(db('myquestions')->where(['uid'=>$param['uid'],'pointid'=>$param['id']])->delete()){
            $res['code']=0;
        }else{
            $res['code']=1;
        }
        return  json_encode($res);
    }
    /**
     * 获取模拟考试试卷列表
     */
    function getMoniExamlist(){
        $param = $this->request->param();
        $category=model('courseCategory')->order('sort_order asc')->select();
        $coursemodel=new \app\index\controller\Course;
        $where=$coursemodel->getCateId($category,$param);
        $exams=model('exams')->where('examsubject','in',$where)->where(['isopen'=>1,'examtype'=>$param['type']])->order('id',"desc")->limit(6)->select();
        foreach ($exams as $i=> $value) {
            $exams[$i]['examsetting']=json_to_array($exams[$i]['examsetting']);
        }
        return  json_encode($exams);
    }

    /**
     * 获取套题
     */
    function dopackage(){
        $param = $this->request->param();
        $exam=model('exams')->where('id',$param['id'])->find();
        $exam['examsetting']=json_to_array($exam['examsetting']);
        $exam['examquestions']=json_to_array($exam['examquestions']);
        foreach ($exam['examsetting']['questype'] as $i=> $value) {
            $questionslist[$i]['typeid']=$i;
            $questionslist[$i]['type']=get_question_type($i);
            $questionslist[$i]['mark']=get_question_mark($i);
            $questionslist[$i]['number']=$exam['examsetting']['questype'][$i]['number'];
            $questionslist[$i]['score']=$exam['examsetting']['questype'][$i]['score'];
            $questionslist[$i]['describe']=$exam['examsetting']['questype'][$i]['describe'];
            foreach ($exam['examquestions'][$i] as $k=> $value2) {
                $questionslist[$i]['list'][] =preg_replace_callback('/<[img|IMG].*?src=[\'| \"](?![http|https])(.*?(?:[\.gif|\.jpg]))[\'|\"].*?[\/]?>/',
                    function ($r) {
                        if(strstr($r[1], 'oss-')){
                            $str = 'http://'.$r[1];
                        }else{
                            $str = 'https://'.$_SERVER['HTTP_HOST'].$r[1];
                        }
                        return str_replace($r[1], $str, $r[0]);
                    },app_get_question_info($value2)
                );
            }
        }
        $myexam=model('myexam')->where(['uid'=>$param['uid'],'eid'=>$param['id']])->find();
        $myexam['myanswer']=json_to_array($myexam['myanswer']);
        $myexam['myscore']=json_to_array($myexam['myscore']);
        $myexam['myresult']=json_to_array($myexam['myresult']);
        $res['questionslist']=$questionslist;
        $res['myexam']=$myexam;
        $res['title']=$exam['exam'];
        $res['eid']=$exam['id'];
        $res['examtype']=$exam['examtype'];
        $res['examtime']=$exam['examtime'];
        $res['starttime']=$exam['examsetting']['starttime'];
        $res['endtime']=$res['starttime']? strtotime("+".$exam['examsetting']['examtime']."minutes",strtotime($exam['examsetting']['starttime'])):'';
        $res['directions']=$exam['examsetting']['directions'];
        return json_encode($res) ;
    }
    /**
     * 获取试题类型
     */
    function getQuestype(){
        $param = $this->request->param();
        $res['type'] =get_question_type($param['id']);
        return json_encode($res) ;
    }
    /**
     * 获取试卷详情
     */
    function getPaperInfo(){
        $param = $this->request->param();
        $exams=model('exams')->where(['id'=>$param['id']])->find();
        $exams['examsetting']=json_to_array($exams['examsetting']);
        $exams['type']=$exams['examtype']==1?'套卷练习':'模拟定时考试';
        $exams['starttime']=$exams['examsetting']['starttime']?:'';
        $exams['starttimestamp']=$exams['examsetting']['starttime']?strtotime($exams['examsetting']['starttime']):'';
        $exams['endtime']=$exams['starttime']? strtotime("+".$exams['examsetting']['examtime']."minutes",strtotime($exams['examsetting']['starttime'])):'';
        $exams['passscore']=intval($exams['examsetting']['passscore']);
        $exams['isend']= time()>$exams['endtime'];
        return  json_encode($exams);
    }

    /**
     * 阿里云Oss上传类
     */
    function new_oss(){
        if(empty(config('KeyID') || empty(config('KeySecret')))){
            return ['code' => 0, 'msg' => '请先配置阿里云oss'];
        }else{
            $oss=new OssClient(config('KeyID'),config('KeySecret'),config('EndPoint'));
            return $oss;
        }
    }
    /**
     * 上传试题拍照
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
                $imageSavePath ='./upload/appexam/'. uniqid().'.'.$exename;
                if(move_uploaded_file($tmp_name, $imageSavePath)){
                    return json_encode(formatUrl($imageSavePath)) ;
                }
            }
        }else{
            try {
                $file = $this->request->file('file');
                $ossClient =$this->new_oss();
                $ossClient->uploadFile(config('Bucket'), 'files'.$param['uid'].'/appexam/'.$file->getInfo('name'),$file->getInfo()['tmp_name']);
                $url='//'.config('Bucket').'.'.config('EndPoint').'/'.'files'.$param['uid'].'/appexam/'.$file->getInfo('name');
                $res['code']=0;
                $res['imageSavePath']=$url;
                return json_encode($url) ;

            } catch (\Exception $e) {
                return json_encode(['code' => 1, 'msg' => $e->getMessage()]);
            }
        }

    }
    /**
     * 获取文件扩展名
     */
    public function getExeName($fileName){
        $pathinfo      = pathinfo($fileName);
        return strtolower($pathinfo['extension']);
    }
    /**
     * 做知识点提交
     */
    function dopointPost(){
        $param = $this->request->param(false);
        $param['answer']=json_to_array($param['answer']);
        $myquestions=[];
        $myerrors=[];
        foreach ($param['answer'] as $k=> $value) {
            if(is_array($value)){
                $value=implode($value);
            }
            if(!check_answer($k,$value)){
                array_push($myerrors,$k);
            }else{
                $this->addjifen('dian',$param['uid']);
            }
            array_push($myquestions,$k);
            $myanswer[$k]=$value;
        }
        $mydone['myquestions']=json_encode($myquestions);
        $mydone['myerrors']=json_encode($myerrors);
        $mydone['uid']=$param['uid'];
        $mydone['pointid']=$param['kid'];
        if($mydonequestions=db('myquestions')->where(['uid'=>$param['uid'],'pointid'=>$param['kid']])->find()){
            $mynewdone=array_unique(array_merge(json_to_array($mydonequestions['myquestions']),$myquestions));
            if($mydonequestions['myerrors']==null || empty($mydonequestions['myerrors'])){
                $mydone['myerrors']=json_encode($myerrors);
            }else{
                $mynewerrors=array_unique(array_merge(json_to_array($mydonequestions['myerrors']),$myerrors));
                $mydone['myerrors']=json_encode(array_values($mynewerrors));
            }
            $mydone['myquestions']=json_encode($mynewdone);
            db('myquestions')->where(['uid'=>$param['uid'],'pointid'=>$param['kid']])->update($mydone);
            return json_encode(['code' => 0, 'msg' => '提交成功', 'myanswer'=>$myanswer]);
        }else{
            db('myquestions')->insert($mydone);
            return json_encode(['code' => 0, 'msg' => '提交成功', 'myanswer'=>$myanswer]);
        }
    }
    /**
     * 提交错题重做
     */
    function doErrorPost(){
        $param = $this->request->param(false);
        $param['answer']=json_to_array($param['answer']);
        $mydonequestions=db('myquestions')->where(['uid'=>$param['uid'],'pointid'=>$param['kid']])->find();
        $myOldError=json_to_array($mydonequestions['myerrors']);
        $myright=[];
        foreach ($param['answer'] as $k=> $value) {
            if(is_array($value)){
                $value=implode($value);
            }
            if(check_answer($k,$value)){
                $myright[]=$k;
                $this->addjifen('dian',$param['uid']);
            }
            $myanswer[$k]=$value;
        }
        if(empty($myright)){
            return json_encode(['code' => 0, 'msg' => '提交成功', 'myanswer'=>$myanswer]);
        }else{
            $myNewError=array_values(array_diff($myOldError, $myright));
            $newdata['myerrors']=json_encode($myNewError);
            if(db('myquestions')->where(['uid'=>$param['uid'],'pointid'=>$param['kid']])->update($newdata)){
                return json_encode(['code' => 0, 'msg' => '提交成功', 'myanswer'=>$myanswer]);
            }else{
                return json_encode(['code' => 1, 'msg' => '提交失败']);
            }
        }
    }
    /**
     * 提交试卷
     */
    function doExamPost(){
        $param = $this->request->param();
        $examinfo=model('exams')->where('id',$param['eid'])->find();
        $param['questionscore']=json_to_array($param['questionscore']);
        $param['answer']=json_to_array($param['answer']);
        $zhuGuanNum=0;
        $myexam['kgscores']=0;
        foreach ($param['answer'] as $k=> $value) {
            if(is_array($value)){
                $value=implode($value);
            }
            $myexam['myanswer'][$k]=$value;
            if(in_array( get_question_mark(get_question_type_id($k)),['SingleSelect','MultiSelect','TrueOrfalse'])){
                if(get_question_mark(get_question_type_id($k))=='MultiSelect'){
                    if(check_m_answer($k,$value)==1){
                        $myexam['myresult'][$k]=1;
                        $myexam['myscore'][$k]=$param['questionscore'][$k];
                        $myexam['kgscores']=($myexam['kgscores']+$param['questionscore'][$k]);
                    }
                    if(check_m_answer($k,$value)==2){
                        $myexam['myresult'][$k]=2;
                        $myexam['myscore'][$k]=$param['questionscore'][$k]-3;
                        $myexam['kgscores']=($myexam['kgscores']+$myexam['myscore'][$k]);
                    }
                    if(check_m_answer($k,$value)==3){
                        $myexam['myresult'][$k]=2;
                        $myexam['myscore'][$k]=0;
                    }
                }else{
                    if(check_answer($k,$value)){
                        $myexam['myresult'][$k]=1;
                        $myexam['myscore'][$k]=$param['questionscore'][$k];
                        $myexam['kgscores']=($myexam['kgscores']+$param['questionscore'][$k]);
                    }else{
                        $myexam['myresult'][$k]=2;
                        $myexam['myscore'][$k]=0;
                    }
                }
            }
            if(in_array( get_question_mark(get_question_type_id($k)),['FillInBlanks','ShortAnswer','TiMao'])){
                $zhuGuanNum++;
            }
        }
        if($zhuGuanNum==0){
            $myexam['totalscores']=$myexam['kgscores'];
            $myexam['status']=1;
            $obj = controller("index/course");
            if(($param['cid']>0)){
                $obj->certificate($myexam['totalscores'],$param['eid'],$param['sid'],$param['cid'],$param['uid'],$param['ctype']);
            }
        }
        $myexam['uid']=$param['uid'];
        $myexam['sid']=$param['sid'];
        $myexam['eid']=$param['eid'];
        if($param['cid']){
            $myexam['cid']=$param['cid'];
            $myexam['ctype']=$param['ctype'];
        }else{
            $myexam['pointid']=$examinfo['examsubject'];
        }
        $myexam['tid']=$examinfo['examauthorid'];
        $myexam['myscore']=json_encode($myexam['myscore']);
        $myexam['myanswer']=json_encode($myexam['myanswer']);
        $myexam['myresult']=json_encode($myexam['myresult']);
        $myexam['ispost']=1;
        $myexam['addtime']=date('Y-m-d H:i:s', time());
        if($this->insert('myexam', $myexam) === true){
            if(($myexam['cid']>0)){
                 $obj = controller("index/course");
                 $obj->examstudied($myexam['uid'],$myexam['cid'],$param['sid'],$myexam['ctype']);
             }
             return json_encode(['code' => 0, 'msg' => '交卷成功']);
         }else {
             return json_encode(['code' => 1, 'msg' => $this->error($this->errorMsg)]);
         }
    }
    /**
     * 重考删除考试记录
     */
    function delexam(){
        $param=$this->request->param();
        $res1=db('myexam')->where(['eid'=>$param['eid'],'uid'=>$param['uid']])->delete();
        $res2=db('learned')->where(['cid'=>$param['cid'],'sid'=>$param['sid'],'type'=>$param['type'],'uid'=>$param['uid']])->delete();
        if ($res1 && $res2) {
            $data['code']=1;
        } else {
            $data['code']=0;
        }
        return json_encode($data);
    }
}