<?php
namespace app\index\controller;
use app\common\controller\IndexBase;
class Exam extends IndexBase
{
    protected function _initialize()
    {
        parent::_initialize();

        $param = $this->request->param();
        $parent=model('courseCategory')->where(['pid'=>0])->order('sort_order asc')->select();
        if(!empty($param['parent'])){
            $secondclass=model('courseCategory')->where(['pid'=>$param['parent']])->order('sort_order asc')->select();
        }
        if(!empty($param['second'])){
            $thirdclass=model('courseCategory')->where(['pid'=>$param['second']])->order('sort_order asc')->select();
        }
        $this->checkBangTel();
        $this->assign(['parent'=>$parent,'second'=>$secondclass,'third'=>$thirdclass]);
    }

    function index(){
        return $this->fetch('index');
    }
    function point(){
        $param = $this->request->param();
        $category=model('courseCategory')->order('sort_order asc')->select();
        $where=controller('course')->getCateId($category,$param);
        $knowledge=model('knowledge')->where('sectionid','in',$where)->order('sort_order asc')->paginate(12,false,[ 'query' => request()->param()]);
        return $this->fetch('point',['knowledge'=>$knowledge,]);
    }
    function dopoint(){
        !$this->checkLogin() && $this->redirect(url('index/user/login'));
        $param = $this->request->param();
        $param['kid']=hashids_decode($param['kid']);
        if($param['id']){
            $myanswer=model('myexam')->where(['uid'=>is_user_login(),'pointid'=>$param['id']])->find();
            $myanswer['myanswer']=json_to_array($myanswer['myanswer']);
            $myanswer['myresult']=json_to_array($myanswer['myresult']);
            $myexercise=model('exercise')->where('id',$param['id'])->find();
            $myexercise['examquestions']=json_to_array($myexercise['examquestions']);
        }else{
            $myquestion=db('myquestions')->where(['uid'=>is_user_login(),'pointid'=>$param['kid']])->select();
            $question=model('questions')->orderRaw('rand()')->where(['questionknowsid'=>$param['kid']])->whereNotIn('id',json_to_array($myquestion[0]['myquestions']))->limit('10')->select();

            foreach ($question as $k => $value) {
                $questionArry[$question[$k]['questiontype']][]=$question[$k]['id'];
            }
            $exercise['uid']=is_user_login();
            $exercise['examquestions']=json_encode ($questionArry);
            $exercise['addtime'] = date('Y-m-d h:i:s', time());
            $this->insert('exercise', $exercise);
            $myexercise=model('exercise')->where('id',$this->insertId)->find();
            $myexercise['examquestions']=json_to_array($myexercise['examquestions']);
        }
        return $this->fetch('dopoint',['exam'=>$myexercise,'myexam'=>$myanswer,'kid'=>$param['kid']]);
    }
    function dopointPost(){
        $param = $this->request->param(false);
        $myquestions=[];
        $myerrors=[];
        foreach ($param['answer'] as $k=> $value) {
            if(is_array($value)){
                $value=implode($value);
            }
            $myexam['myanswer'][$k]=$value;
            if(check_answer($k,$value)){
                $myexam['myresult'][$k]=1;
                $this->addjifen('dian',is_user_login());
            }else{
                $myexam['myresult'][$k]=2;
                array_push($myerrors,$k);
            }
            array_push($myquestions,$k);
        }
        $myexam['uid']=is_user_login();
        $myexam['pointid']=$param['eid'];
        $myexam['ispost']=1;
        $myexam['myanswer']=json_encode($myexam['myanswer']);
        $myexam['myresult']=json_encode($myexam['myresult']);
        $myexam['addtime']=date('Y-m-d H:i:s', time());
        $mydone['myquestions']=json_encode($myquestions);
        $mydone['myerrors']=json_encode($myerrors);
        $mydone['uid']=is_user_login();
        $mydone['pointid']=$param['kid'];
        if($mydonequestions=db('myquestions')->where(['uid'=>is_user_login(),'pointid'=>$param['kid']])->find()){
            $mynewdone=array_unique(array_merge(json_to_array($mydonequestions['myquestions']),$myquestions));
            if($mydonequestions['myerrors']==null || empty($mydonequestions['myerrors'])){
                $mydone['myerrors']=json_encode($myerrors);
            }else{
                $mynewerrors=array_unique(array_merge(json_to_array($mydonequestions['myerrors']),$myerrors));
                $mydone['myerrors']=json_encode(array_values($mynewerrors));
            }
            $mydone['myquestions']=json_encode($mynewdone);
            db('myquestions')->where(['uid'=>is_user_login(),'pointid'=>$param['kid']])->update($mydone);
        }else{
            db('myquestions')->insert($mydone);
        }
        if($this->insert('myexam', $myexam) === true){
            $this->success('提交成功',url('index/exam/dopoint',['id'=>$param['eid']]));
        }else {
            $this->error($this->errorMsg);
        }
    }
    function package(){
        $param = $this->request->param();
        $type=hashids_decode($param['type']);
        $category=model('courseCategory')->order('sort_order asc')->select();
        $where=controller('course')->getCateId($category,$param);
        $exams=model('exams')->where('examsubject','in',$where)->order('id desc')->where(['examtype'=>$type,'isopen'=>1])->paginate(12,false,[ 'query' => request()->param()]);
        foreach ($exams as $k=> $value) {
            $exams[$k]['examsetting']=json_to_array($exams[$k]['examsetting']);
        }
        return $this->fetch('package',['exams'=>$exams]);
    }
    function dopackage(){
        !$this->checkLogin() && $this->redirect(url('index/user/login'));
        $param = $this->request->param();
        $param['id']=hashids_decode($param['id']);
        $exam=model('exams')->where('id',$param['id'])->find();
        $exam['examsetting']=json_to_array($exam['examsetting']);
        $exam['examquestions']=json_to_array($exam['examquestions']);
        $subQuestions=[];
        foreach ($exam['examquestions'] as $k => $value) {
            if(get_question_mark($k)=='TiMao'){
                foreach ($exam['examquestions'][$k] as $i => $value) {
                    $subQuestions[$value]=controller('course')->getSubQuestions($value);
                }
            }
        }
        $exam['subQuestions']=$subQuestions;
        $myexam=model('myexam')->where(['uid'=>is_user_login(),'eid'=>$param['id']])->find();
        $myexam['myanswer']=json_to_array($myexam['myanswer']);
        $myexam['myscore']=json_to_array($myexam['myscore']);
        $myexam['myresult']=json_to_array($myexam['myresult']);
        if($exam['examsetting']['examtype']==1 || $myexam['ispost']==1){
            return $this->fetch('dopackage',['useType'=>4,'title'=>$exam['exam'],'exam'=>$exam,'myexam'=>$myexam]);
        }
        if($exam['examsetting']['examtype']==2 and empty(input('exam'))){
            $examend=date("Y-m-d H:i:s",strtotime("+".$exam['examsetting']['examtime']."minutes",strtotime($exam['examsetting']['starttime'])));
            strtotime($examend) < time()?$useType=5:$useType=2;
            return $this->fetch('preview',['useType'=>$useType,'title'=>$exam['exam'],'exam'=>$exam]);
        }else{
            $now=$exam['examsetting']['starttime']?$exam['examsetting']['starttime']:date('Y-m-d H:i:s', time());
            if(empty(session(is_user_login().$exam['id'].'endtime'))){
                $end=date("Y-m-d H:i:s",strtotime("+".$exam['examsetting']['examtime']."minutes",strtotime($now)));
                session(is_user_login().$exam['id'].'endtime',$end);
            }else{
                $end=session(is_user_login().$exam['id'].'endtime');
            }
            return $this->fetch('dopackage',['uid'=>'19810725'.is_user_login(),'useType'=>3,'now'=>date('Y-m-d H:i:s', time()),'end'=>$end,'title'=>$exam['exam'],'exam'=>$exam,'myexam'=>$myexam]);
        }
    }

    function exampPost(){
        $param = $this->request->param(false);
        $zhuGuanNum=0;
        $myexam['kgscores']=0;
        if($param['answer']){
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
                            $myexam['myscore'][$k]=2;
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
        }else{
            $myexam['kgscores']=0;
            $myexam['myscore']=0;
        }
        if($zhuGuanNum==0){
            $myexam['totalscores']=$myexam['kgscores'];
            $myexam['status']=1;
        }
        $myexam['uid']=is_user_login();
        $myexam['authorid']=getExamAuthorid($param['eid']);
        $myexam['eid']=$param['eid'];
        $myexam['myscore']=json_encode($myexam['myscore']);
        $myexam['myanswer']=json_encode($myexam['myanswer']);
        $myexam['myresult']=json_encode($myexam['myresult']);
        $myexam['ispost']=1;
        $myexam['addtime']=date('Y-m-d H:i:s', time());
        if($this->insert('myexam', $myexam) === true){
            $this->success('提交成功');
            session(is_user_login().$param['eid'].'endtime','');
        }else {
            $this->error($this->errorMsg);
        }
    }
    function error(){
        $param = $this->request->param();
        $category=model('courseCategory')->order('sort_order asc')->select();
        $where=controller('course')->getCateId($category,$param);
        $knowledge=model('knowledge')->where('sectionid','in',$where)->order('sort_order asc')->paginate(12,false,[ 'query' => request()->param()]);
        return $this->fetch('error',['knowledge'=>$knowledge,]);
    }
    function doerror(){
        !$this->checkLogin() && $this->redirect(url('index/user/login'));
        $param = $this->request->param();
        $param['kid']=hashids_decode($param['kid']);
        $myquestions=db('myquestions')->where(['uid'=>is_user_login(),'pointid'=> $param['kid']])->select();
        $myerrors=[];
        foreach ($myquestions as $k=> $value) {
            $myerrors=array_unique(array_merge($myerrors,json_to_array($myquestions[$k]['myerrors'])));
        }
        if($param['id']){
            $myanswer=model('myexam')->where(['uid'=>is_user_login(),'pointid'=>$param['id']])->find();
            $myanswer['myanswer']=json_to_array($myanswer['myanswer']);
            $myanswer['myresult']=json_to_array($myanswer['myresult']);
            $myerror=model('exercise')->where('id',$param['id'])->find();
            $myerrors=json_to_array($myerror['examquestions']);
        }else{
            $exercise['uid']=is_user_login();
            $exercise['examquestions']=json_encode(array_values($myerrors));
            $exercise['addtime'] = date('Y-m-d h:i:s', time());
            $this->insert('exercise', $exercise);
        }
        return $this->fetch('doerror',['eid'=>$this->insertId,'myexam'=>$myanswer,'kid'=> $param['kid'],'myerrors'=>$myerrors]);
    }
    function doerrorPost(){
        $param = $this->request->param(false);
        $myquestions=[];
        $myerrors=[];
        foreach ($param['answer'] as $k=> $value) {
            if(is_array($value)){
                $value=implode($value);
            }
            $myexam['myanswer'][$k]=$value;
            if(check_answer($k,$value)){
                $myexam['myresult'][$k]=1;
            }else{
                $myexam['myresult'][$k]=2;
                array_push($myerrors,$k);
            }
            array_push($myquestions,$k);
        }
        $myexam['uid']=is_user_login();
        $myexam['pointid']=$param['eid'];
        $myexam['ispost']=1;
        $myexam['myanswer']=json_encode($myexam['myanswer']);
        $myexam['myresult']=json_encode($myexam['myresult']);
        $myexam['addtime']=date('Y-m-d H:i:s', time());
        $mydone['myerrors']=json_encode($myerrors);
        $mydone['uid']=is_user_login();
        $mydone['pointid']=$param['kid'];
        db('myquestions')->where(['uid'=>is_user_login(),'pointid'=>$param['kid']])->update($mydone);
        if($this->insert('myexam', $myexam) === true){
            $this->success('提交成功',url('index/exam/doerror',['id'=>$param['eid']]));
        }else {
            $this->error($this->errorMsg);
        }
    }
    function writeBoard(){
        return $this->fetch('writeBoard');
    }
    function getExamImg(){
        $src = $this->base64ContentToImage(input('src_data','',null));
        $data['code']=0;
        $data['src']=$src;
        return json_encode($data);
    }
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
    public function base64ContentToImage($base64_image_content){
        $path= 'upload/examanswer';
        $dir = $path;
        if(!file_exists($dir)){
            mkdir(iconv("GBK", "UTF-8", $dir),0777,true);
        }
        if(preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result)){
            $type = $result[2];
            $new_file = $path."/".date('Ymd',time())."/";
            if(!file_exists($new_file)){
                mkdir($new_file, 0700);
            }
            $new_file = $new_file.time().".{$type}";
            if (file_put_contents($new_file, base64_decode(str_replace($result[1], '', $base64_image_content)))){
                return '/'.$new_file;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
}
