<?php

namespace app\admin\controller;
use app\common\controller\AdminBase;
vendor ('category.Category');
class Exam extends AdminBase
{
    protected function _initialize()
    {
        parent::_initialize();
        if ($this->request->isGet()) {
            $this->assign('courseCategory', list_to_level(model('courseCategory')->order('sort_order asc')->select()));
        }
    }

    /**
     * 试题列表
     */
    public function questionsList()
    {
        $param = $this->request->param();
        $where = [];
        if (isset($param['question'])) {
            $where['question'] = ['like', "%" . $param['question'] . "%"];
        }
        if (isset($param['questionchapterid'])) {
            $where['questionchapterid'] = $param['questionchapterid'];
        }
        if (isset($param['questiontype'])) {
            $where['questiontype'] = $param['questiontype'];
        }
        if (isset($param['questionknowsid'])) {
            $where['questionknowsid'] = $param['questionknowsid'];
        }
        if(getAdminAuthId(is_admin_login())>1){
            $where['questionuserid'] = is_admin_login();
        }
        $list = model('questions')->order('id desc')->where($where)->where('questionparent',0)
            ->paginate(config('page_number'), false, ['query' => $param]);
        $type=model('questionType')->order('sort_order asc')->select();
        return $this->fetch('questionsList',['list' => $list,'type'=>$type]);
    }

    /**
     * 单个添加试题
     */
    public function questionsSingleAdd()
    {
        if ($this->request->isPost()) {
            $param = $this->request->param();
            $param['questionuserid']=is_admin_login();
            $typearr=explode("－",$param['questiontype']);
            $param['questiontype']=$typearr[0];
            if(get_question_mark($typearr[0])=='TiMao'){
                $param['questionsequence']=1;
                if ( $id=db('questions')->insertGetId($param)) {
                    $this->success('添加成功', url('admin/exam/addSubQuestions',['questionparent'=>$id,'questionchapterid'=>$param['questionchapterid']]));
                } else {
                    $this->error($this->errorMsg);
                }
            }else{
                $param['questionsequence']=0;
                $param['questionanswer']=$param['questionanswer'.get_question_mark($typearr[0])];
                if(is_array($param['questionanswer'])) {$param['questionanswer'] = implode('',$param['questionanswer']);}
                $param['question']=replaceimg($param['question']);
                $param['questionselect']=replaceimg($param['questionselect']);
                $param['questiondescribe']=replaceimg($param['questiondescribe']);
                if ($this->insert('questions', $param) === true) {
                    $this->success('添加成功', url('admin/exam/questionsList'));
                } else {
                    $this->error($this->errorMsg);
                }
            }
        }
        $list=model('courseCategory')->order('sort_order asc')->select();
        $cat = new \org\Category(array('id', 'pid', 'category_name','cname'));
        $data = $cat->getTree($list, intval($id=0));
        $type=model('questionType')->order('sort_order asc')->select();
        return $this->fetch('questionsSingleAdd',['category' => $data,'type'=>$type]);
    }
    /**
     * 批量添加试题
     */
    public function questionsMoreAdd()
    {
        if ($this->request->isPost()) {
            $param = $this->request->param();
            $typearr=explode("－",$param['questiontype']);
            $param['questiontype']=$typearr[0];
            $type=$typearr[1];
            $param['morequestion']=replaceimg($param['morequestion']);
            $temp=explode("【结束】",$param['morequestion']);
            foreach ($temp as $k => $value) {
                if($type=='SingleSelect' || $type=='MultiSelect' ){
                    $questions[$k]['tigan']=strip_tags($this->cut("【题干】","【选项】",$value),'<p>,<img>');
                    $questions[$k]['xuanxiang']=strip_tags($this->cut("【选项】","【答案】",$value),'<p>,<img>');
                    $questions[$k]['daan']=strip_tags($this->cut("【答案】","【解析】",$value));
                }else{
                    $questions[$k]['tigan']=strip_tags($this->cut("【题干】","【答案】",$value),'<p>,<img>');
                    $questions[$k]['daan']=strip_tags($this->cut("【答案】","【解析】",$value),'<p>,<img>');
                }
                $questions[$k]['jiexi']=strip_tags($this->cutall($value,"【解析】"),'<p>，<img>');
            }
            foreach ($questions as  $k=> $value) {
                $questionsArray[$k]['questionchapterid']=$param['questionchapterid'];
                $questionsArray[$k]['questionknowsid']=$param['questionknowsid'];
                $questionsArray[$k]['questiontype']=$param['questiontype'];
                $questionsArray[$k]['question']=$questions[$k]['tigan'];
                $questionsArray[$k]['questionselect']=$questions[$k]['xuanxiang'];
                if($type=='FillInBlanks' || $type=='ShortAnswer' || $type=='TrueOrfalse'){
                    $questionsArray[$k]['questionanswerFillInBlanks']=$questions[$k]['daan'];
                    $questionsArray[$k]['questionanswerShortAnswer']=$questions[$k]['daan'];
                }else{
                    $questionsArray[$k]['questionselectnumber']=$param['questionselectnumber'];
                }
                $questionsArray[$k]['questiondescribe']=$questions[$k]['jiexi'];
                $questionsArray[$k]['questionlevel']=$param['questionlevel'];
                $questionsArray[$k]['questioncreatetime']=$param['questioncreatetime'];
                $questionsArray[$k]['questionuserid']=is_admin_login();
                $questionsArray[$k]['questionsequence']=0;
                $questionsArray[$k]['questionanswer']=$questions[$k]['daan'];
            }

            if ($this->saveAll('questions', $questionsArray) === true) {
                $this->success('添加成功', url('admin/exam/questionsList'));
            } else {
                $this->error($this->errorMsg);
            }
        }
        $list=model('courseCategory')->order('sort_order asc')->select();
        $cat = new \org\Category(array('id', 'pid', 'category_name','cname'));
        $data = $cat->getTree($list, intval($id=0));
        $type=model('questionType')->order('sort_order asc')->select();
        return $this->fetch('questionsMoreAdd',['category' => $data,'type'=>$type]);
    }
    public function cut($begin,$end,$str){
        $b = mb_strpos($str,$begin) + mb_strlen($begin);
        $e = mb_strpos($str,$end) - $b;
        return mb_substr($str,$b,$e);
    }
    public function cutall($allString,$searchString){
        $newString = strstr($allString, $searchString);
        $length = strlen($searchString);
        return substr($newString, $length);
    }
    /**
     * 题帽题添加子题
     */

    public function addSubQuestions()
    {
        if ($this->request->isPost()) {
            $param = $this->request->param();
            $param['questionuserid']=is_admin_login();
            $typearr=explode("-",$param['questiontype']);
            $param['questionanswer']=$param['questionanswer'.get_question_mark($typearr[0])];
            if(is_array($param['questionanswer'])) {$param['questionanswer'] = implode('',$param['questionanswer']);}
            if ($this->insert('questions', $param, $rule = false) === true) {
                $this->success('添加成功', url('admin/exam/addSubQuestions',['questionparent'=>$param['questionparent'],'questionchapterid'=>$param['questionchapterid']]));
            } else {
                $this->error($this->errorMsg);
            }
        }
        $type=model('questionType')->order('sort_order asc')->where('mark','not like','TiMao')->select();
        $param = $this->request->param();
        return $this->fetch('addSubQuestions',['type'=>$type,'questionparent'=>$param['questionparent']]);
    }

    /**
     * excel文件批量导入试题
     */
    public function questionsCSVleAdd()
    {
        $list=model('courseCategory')->order('sort_order asc')->select();
        $cat = new \org\Category(array('id', 'pid', 'category_name','cname'));
        $data = $cat->getTree($list, intval($id=0));
        $type=model('questionType')->order('sort_order asc')->select();
        return $this->fetch('questionsCSVleAdd',['category' => $data,'type'=>$type]);
    }
    function importExcel(){
        try {
            $param = $this->request->param();
            $successNum=0;$errorNum=0;
            if(empty($param['questionchapterid'])){
                return ['code' => 0, 'msg' => '请选择所属科目'];
            }
            if(empty($param['questionknowsid'])){
                return ['code' => 0, 'msg' => '请选择所属知识点'];
            }
            $file = request()->file('file');
            $info = $file->validate(['size' => 3145728, 'ext' => 'xlsx,xls,csv'])->move(ROOT_PATH . 'public' . DS . 'upload' . DS . 'excels');
            if ($info) {
                vendor('PHPExcel.PHPExcel.Reader.Excel5');
                $fileName = $info->getSaveName();
                $filePath = ROOT_PATH . 'public' . DS . 'upload' . DS . 'excels' . DS . str_replace('\\', '/', $fileName);
                $PHPReader = new \PHPExcel_Reader_Excel5();
                $objPHPExcel = $PHPReader->load($filePath);
                $sheet = $objPHPExcel->getSheet(0);
                $allRow = $sheet->getHighestRow();
                for ($j = 2; $j <= $allRow; $j++) {
                    $param['questionuserid'] = is_admin_login();
                    $param['questiontype'] = $objPHPExcel->getActiveSheet()->getCell("A" . $j)->getValue();
                    $param['question'] = $objPHPExcel->getActiveSheet()->getCell("B" . $j)->getValue();
                    $param['questionselect'] = $objPHPExcel->getActiveSheet()->getCell("C" . $j)->getValue();
                    $param['questionselectnumber'] = $objPHPExcel->getActiveSheet()->getCell("D" . $j)->getValue();
                    $param['questionanswer'] = $objPHPExcel->getActiveSheet()->getCell("E" . $j)->getValue();
                    $param['questiondescribe'] = $objPHPExcel->getActiveSheet()->getCell("F" . $j)->getValue();
                    $param['questioncreatetime'] = date('Y-m-d h:i:s', time());
                    $param['questionstatus'] = 1;
                    $param['questionlevel'] = $objPHPExcel->getActiveSheet()->getCell("G" . $j)->getValue();
                    if(!empty($param['questiontype'])){
                        if(model('questions')->isUpdate(false)->data($param,true)->save()){
                            $successNum=$successNum+1;
                        }else{
                            $errorNum=$errorNum+1;
                        }
                    }
                }
                return ['code' => 1, 'msg' => '导入成功'.$successNum.'个，失败'.$errorNum.'个', 'url' => '/public/upload/file/' . str_replace('\\', '/', $info->getSaveName())];
            } else {
                $excel_path = null;
            }
        } catch (\Exception $e) {
            return ['code' => 0, 'msg' => $e->getMessage()];
        }
    }
    /**
     * 编辑试题
     */
    public function questionsEdit()
    {
        if ($this->request->isPost()) {
            $param = $this->request->param();
            $param['questionuserid']=is_admin_login();
            $typearr=explode("－",$param['questiontype']);
            $param['questiontype']=$typearr[0];
            $param['questionanswer']=$param['questionanswer'.get_question_mark($typearr[0])];
            if(is_array($param['questionanswer'])) {$param['questionanswer'] = implode('',$param['questionanswer']);}
            if ($this->update('questions', $param) === true) {
                if(get_question_mark($typearr[0])=='TiMao'){
                    $this->success('编辑成功', url('admin/exam/listSubQuestions',['questionparent'=>$param['id']]));
                }else{
                    $this->success('编辑成功', url('admin/exam/questionsList'));
                }
            } else {
                $this->error($this->errorMsg);
            }

        }
        $list=model('courseCategory')->order('sort_order asc')->select();
        $cat = new \org\Category(array('id', 'pid', 'category_name','cname'));
        $data = $cat->getTree($list, intval($id=0));
        $type=model('questionType')->order('sort_order asc')->select();
        $questions=model('questions')->where('id',input('id'))->find();
        /*if(!strstr($questions['question'],'<p>')){
            $questions['question']='<p>'.$questions['question'].'</p>';
        }
        if(!strstr($questions['questionselect'],'<p>')){
            $questions['questionselect']='<p>'.$questions['questionselect'].'</p>';
        }
        if(!strstr($questions['questionanswer'],'<p>') and(get_question_mark($questions['questiontype'])!='SingleSelect') and (get_question_mark($questions['questiontype'])!='MultiSelect')){
            $questions['questionanswer']='<p>'.$questions['questionanswer'].'</p>';
        }
        if(!strstr($questions['questiondescribe'],'<p>')){
            $questions['questiondescribe']='<p>'.$questions['questiondescribe'].'</p>';
        }*/
        $Knowledge=model('knowledge')->where('sectionid', $questions['questionchapterid'])->order('sort_order asc')->select();
        return $this->fetch('questionsSingleAdd',['data'=>$questions,'category' => $data,'type'=>$type,'Knowledge'=>$Knowledge]);
    }
    /**
     * 题帽题子题列表
     */
    public function listSubQuestions(){
        $param = $this->request->param();
        $where['questionparent']=$param['questionparent'];
        $list = model('questions')->order('id desc')->where($where)->paginate(config('page_number'), false, ['query' => $param]);
        return $this->fetch('listSubQuestions',['list' => $list]);
    }
    /**
     * 试题浏览
     */
    public function questionsPreview()
    {
        $data= model('questions')->where('id',input('id'))->find();
        if($data['questionsequence']==1){
            $data['subQuestions']= model('questions')->where('questionparent',$data['id'])->select();
        }
        return $this->fetch('questionsPreview',['data'=>$data,'NoBtn'=>input('NoBtn')]);
    }

    /**
     * 删除试题
     */
    public function questionsDel()
    {
        if ($this->request->isPost()) {
            if ($this->delete('questions', $this->request->param()) === true) {
                insert_admin_log('删除了试题');
                $this->success('删除成功');
            } else {
                $this->error($this->errorMsg);
            }
        }
    }
    /**
     * 试卷开放设置
     */
    function  examIsOpen(){
        if ($this->request->isPost()) {
            $result = $this->update('exams', $this->request->param(), input('_verify', true));
            if ($result === true) {
                $this->success('修改成功');
            } else {
                $this->error($this->errorMsg);
            }
        }
    }
    /**
     * 试卷列表
     */
    public function examList()
    {
        $param = $this->request->param();
        $where = [];
        if (isset($param['exam'])) {
            $where['exam'] = ['like', "%" . $param['exam'] . "%"];
        }
        if (isset($param['cid'])) {
            $where['examsubject'] = $param['cid'];
        }
        if(getAdminAuthId(is_admin_login())>1){
            $where['examauthorid'] = is_admin_login();
        }
        return $this->fetch('examList',['list' => model('exams')->order('id desc')->where($where)->paginate(config('page_number'))]);
    }

    /**
     * 试卷预览
     */
    public function examPreview()
    {
        $exam=model('exams')->where('id',input('id'))->find();
        $exam['examsetting']=json_to_array($exam['examsetting']);
        $exam['examquestions']=json_to_array($exam['examquestions']);
        $subQuestions=[];
        foreach ($exam['examquestions'] as $k => $value) {
            if(get_question_mark($k)=='TiMao'){
                foreach ($exam['examquestions'][$k] as $i => $value) {
                    $subQuestions[$value]=$this->getSubQuestions($value);
                }
            }
        }
        $exam['subQuestions']=$subQuestions;
        return $this->fetch('examPreview',['exam'=>$exam]);
    }
    /**
     * 根据题帽ID获取题帽题子题
     */
    function getSubQuestions($id){
        $getSubQuestions= model('questions')->field('id')->where('questionparent',$id)->column('id');
        return $getSubQuestions;
    }
    /**
     * 删除试卷
     */
    public function examDel()
    {
        if ($this->request->isPost()) {
            if ($this->delete('exams', $this->request->param()) === true) {
                insert_admin_log('删除了试卷');
                $this->success('删除成功');
            } else {
                $this->error($this->errorMsg);
            }
        }
    }

    /**
     * 手动组卷
     */
    public function selfpage()
    {
        if ($this->request->isPost()) {
            $param = $this->request->param();
            foreach ($param['examquestions'] as $k => $value) {
                $param['examquestions'][$k]=explode(',',(rtrim(ltrim($value['questions'],','),',')));
            }
            foreach ($param['questype'] as $k => $value) {
                if(!empty($param['questype'][$k]['number'])){
                    $temp[$k]=$param['questype'][$k];
                }
                $exam['examscore']=$exam['examscore']+$param['questype'][$k]['number']*$param['questype'][$k]['score'];
            }
            $exam['examauthorid']=is_admin_login();
            $exam['examsetting']= json_encode(['examtime'=>$param['examtime'],'examscore'=>$exam['examscore'],'passscore'=>$param['passscore']*$exam['examscore'],'questype'=>$temp,'examtype'=>$param['examtype'],'starttime'=>$param['starttime'],'directions'=>$param['directions']]);
            $exam['examquestions']= json_encode($param['examquestions']);
            $exam['examtype']=$param['examtype'];
            $exam['examsubject']=$param['examsubject'];
            $exam['exam']=$param['exam'];
            $exam['examtime']=$param['examtime'];
            $exam['examstatus']=1;
            $exam['addtime']=$param['addtime'];
            $exam['isopen']=$param['isopen'];
            if ($this->insert('exams', $exam)===true) {
                insert_admin_log('添加了试卷');
                session('subjectid',null);
                $this->success('添加成功', url('admin/exam/examList'));
            } else {
                $this->error($this->errorMsg);
            }
        }
        $list=model('courseCategory')->order('sort_order asc')->select();
        $cat = new \org\Category(array('id', 'pid', 'category_name','cname'));
        $data = $cat->getTree($list, intval($id=0));
        return $this->fetch('selfpage',['questiontype'=>model('questionType')->order('sort_order asc')->select(),'courseCategory'=>$data]);
    }
    /**
     * 智能组卷
     */
     public function  intellectpage(){
         if ($this->request->isPost()) {
             $param = $this->request->param();
             foreach ($param['questype'] as $k => $value) {
                 $ids[$k]=$this->getQuestionIds($param['examsubject'],$k,$param['questype'][$k]);
                 $exam['examscore']=$exam['examscore']+$param['questype'][$k]['number']*$param['questype'][$k]['score'];
                 if(!empty($param['questype'][$k]['number'])){
                     $temp[$k]=$param['questype'][$k];
                 }
             }
             $exam['examauthorid']=is_admin_login();
             $exam['examsetting']= json_encode(['examtime'=>$param['examtime'],'examscore'=>$exam['examscore'],'passscore'=>$param['passscore']*$exam['examscore'],'questype'=>$temp,'examtype'=>$param['examtype'],'starttime'=>$param['starttime'],'directions'=>$param['directions']]);
             $exam['examquestions']= json_encode($ids);
             $exam['examsubject']=$param['examsubject'];
             $exam['examtype']=$param['examtype'];
             $exam['exam']=$param['exam'];
             $exam['examtime']=$param['examtime'];
             $exam['examstatus']=1;
             $exam['addtime']=$param['addtime'];
             $exam['isopen']=$param['isopen'];
             if ($this->insert('exams', $exam)===true) {
                 insert_admin_log('添加了试卷');
                 session('subjectid',null);
                 $this->success('添加成功', url('admin/exam/examList'));
             } else {
                 $this->error($this->errorMsg);
             }
         }
         $list=model('courseCategory')->order('sort_order asc')->select();
         $cat = new \org\Category(array('id', 'pid', 'category_name','cname'));
         $data = $cat->getTree($list, intval($id=0));
         return $this->fetch('intellectpage',['questiontype'=>model('questionType')->order('sort_order asc')->select(),'courseCategory'=>$data]);
     }
    /**
     * 智能组卷获取试题ID
     */
    function getQuestionIds($examsubject,$type,$info){
        if(!empty($info['number'])){
            if(getAdminAuthId(is_admin_login())>1){
                $where['questionuserid'] = is_admin_login();
            }
            $simple=model('questions')->orderRaw('rand()')->where($where)->where(['questionchapterid'=>$examsubject,'questionparent'=>0,'questionlevel'=>1,'questiontype'=>$type])->field('id')->limit((($info['simple']/($info['simple']+$info['middle']+$info['hard'])))*$info['number'])->select();
            $middle=model('questions')->orderRaw('rand()')->where($where)->where(['questionchapterid'=>$examsubject,'questionparent'=>0,'questionlevel'=>2,'questiontype'=>$type])->field('id')->limit((($info['middle']/($info['simple']+$info['middle']+$info['hard'])))*$info['number'])->select();
            $hard  =model('questions')->orderRaw('rand()')->where($where)->where(['questionchapterid'=>$examsubject,'questionparent'=>0,'questionlevel'=>3,'questiontype'=>$type])->field('id')->limit((($info['hard']/($info['simple']+$info['middle']+$info['hard'])))*$info['number'])->select();
            foreach ($idObj=array_merge($simple,$middle,$hard) as $k => $value) {
                $ids[] =$idObj[$k]['id'];
            }
            return ($ids);
        }
    }
    /**
     * 试卷编辑
     */
    function examEdit(){
        if ($this->request->isPost()) {
            $param = $this->request->param();
            foreach ($param['examquestions'] as $k => $value) {
                $param['examquestions'][$k]=explode(',',(rtrim(ltrim($value['questions'],','),',')));
            }
            $exam = [];
            foreach ($param['questype'] as $k => $value) {
                if(!empty($param['questype'][$k]['number'])){
                    $temp[$k]=$param['questype'][$k];
                }
                $exam['examscore']=$exam['examscore']+$param['questype'][$k]['number']*$param['questype'][$k]['score'];
            }
            $exam['examauthorid']=is_admin_login();
            $exam['examsetting']= json_encode(['examtime'=>$param['examtime'],'examscore'=>$exam['examscore'],'passscore'=>$param['passscore']*$exam['examscore'],'questype'=>$temp,'examtype'=>$param['examtype'],'starttime'=>$param['starttime'],'directions'=>$param['directions']]);
            $exam['examquestions']= json_encode($param['examquestions']);
            $exam['examtype']=$param['examtype'];
            $exam['examsubject']=$param['examsubject'];
            $exam['exam']=$param['exam'];
            $exam['examtime']=$param['examtime'];
            $exam['examstatus']=1;
            $exam['addtime']=$param['addtime'];
            $exam['isopen']=$param['isopen'];
            $exam['id']=$param['id'];
            $result = $this->update('exams', $exam, input('_verify', true));
            if ($result === true) {
                $this->success('编辑成功', url('admin/exam/examlist'));
            } else {
                $this->error($this->errorMsg);
            }
        }
        $list=model('courseCategory')->order('sort_order asc')->select();
        $cat = new \org\Category(array('id', 'pid', 'category_name','cname'));
        $data = $cat->getTree($list, intval($id=0));
        $paper=model('exams')->where('id',input('id'))->find();
        $paper['examsetting']=json_to_array($paper['examsetting']);
        $paper['examquestions']=array_filter(json_to_array($paper['examquestions']));
        foreach ($paper['examquestions'] as $k => $value) {
            $examquestionsNoNull[$k]=array_filter($value);
            if(!empty($value[0])){
                $examquestionsTemp[$k]=','.implode(',', $value).',';
            }
        }
        return $this->fetch('examEdit',['questiontype'=>model('questionType')->order('sort_order asc')->select(),'courseCategory'=>$data,'paper'=>$paper,'examquestionsTemp'=>$examquestionsTemp,'examquestionsNoNull'=>$examquestionsNoNull]);
    }
    /**
     * 手动组卷选择试题
     */
    public function questionsSelect()
    {
        $where['questiontype']=input('questiontype');
        if ((session('subjectid'))>0) {
            $where['questionchapterid'] = session('subjectid');
        }
        if ((session('knowledgeid'))>0) {
            $where['questionknowsid'] = session('knowledgeid');
        }
        if(getAdminAuthId(is_admin_login())>1){
            $where['questionuserid'] = is_admin_login();
        }
        if($list=model('questions')->where($where)->where('questionparent',0)->order('id asc')->paginate(30)){
            session('subjectid', null);
            session('knowledgeid', null);
        }
        return $this->fetch('questionsSelect',['list'=>$list,'questiontype'=>input('questiontype')]);

    }
    /**
     * 试题类型列表
     */
    public function typeList(){

        return $this->fetch('typeList', ['list' => model('questionType')->order('sort_order asc')->select()]);
    }

    /**
     * 添加试题类型
     */
    public function typeAdd(){

        if ($this->request->isPost()) {
            if ($this->insert('questionType', $this->request->param()) === true) {
                insert_admin_log('添加了题型');
                $this->success('添加成功', url('admin/exam/typeList'));
            } else {
                $this->error($this->errorMsg);
            }
        }
        return $this->fetch('typeSave');
    }

    /**
     * 编辑试题类型
     */
    public function typeEdit()
    {
        if ($this->request->isPost()) {
            if ($this->update('questionType', $this->request->param(), input('_verify', true)) === true) {
                insert_admin_log('修改了题型');
                $this->success('修改成功', url('admin/exam/typeList'));
            } else {
                $this->error($this->errorMsg);
            }
        }
        return $this->fetch('typeSave', ['data' => model('questionType')->where('id', input('id'))->find()]);
    }

    /**
     * 删除试题类型
     */
    public function typeDel()
    {
        if ($this->request->isPost()) {
            if ($this->delete('questionType', $this->request->param()) === true) {
                insert_admin_log('删除试题类型');
                $this->success('删除成功');
            } else {
                $this->error($this->errorMsg);
            }
        }
    }
    /**
     * 批阅试卷
     */
    public function mark(){
        if ($this->request->isPost()) {
            $param = $this->request->param();
            $myscore=json_to_array(db('myexam')->where(['uid'=>$param['uid'],'eid'=>$param['eid']])->value('myscore'));
            $kgscores=json_to_array(db('myexam')->where(['uid'=>$param['uid'],'eid'=>$param['eid']])->value('kgscores'));
            $myexam['myscore']=json_encode($myscore+$param['score']);
            foreach ($param['score'] as $k=> $value) {
                $myexam['zgscores']=$myexam['zgscores']+$param['score'][$k];
            }
            $myexam['totalscores']=$myexam['zgscores']+$kgscores;
            $myexam['status']=1;
            $myexam['id']=$param['id'];
            if($this->update('myexam', $myexam ,input('_verify', true)) === true){
                $this->success('批阅完成');
            }else{
                $this->error($this->errorMsg);
            }
        }
        $param = $this->request->param();
        $exam=model('exams')->where('id',$param['eid'])->find();
        $exam['examsetting']=json_to_array($exam['examsetting']);
        $exam['examquestions']=json_to_array($exam['examquestions']);
        $subQuestions=[];
        foreach ($exam['examquestions'] as $k => $value) {
            if(get_question_mark($k)=='TiMao'){
                foreach ($exam['examquestions'][$k] as $i => $value) {
                    $subQuestions[$value]=$this->getSubQuestions($value);
                }
            }
        }
        $exam['subQuestions']=$subQuestions;
        $myexam=model('myexam')->where(['uid'=>$param['uid'],'id'=>$param['id'],'eid'=>$param['eid']])->find();
        $myexam['myanswer']=json_to_array((htmlspecialchars_decode($myexam['myanswer'])));
        $myexam['myscore']=json_to_array($myexam['myscore']);
        return $this->fetch('mark', ['exam'=>$exam,'myexam'=>$myexam]);
    }
    /**
     * 试卷列表
     */
    public function paperList(){
        $param = $this->request->param();
        $where = ['status'=>1];
        if (isset($param['cid'])) {
            $ids= explode("-", $param['cid']);
            $map['ctype'] = $ids[0];
            $map['cid'] = $ids[1];
        }
        if (isset($param['status'])) {
            if($param['status']==2){
                $map['status'] = 0;
            }else{
                $map['status'] = $param['status'];
            }
        }
        if (isset($param['mobile'])) {
            $map['uid'] = getUserIdBuyTel($param['mobile']);
        }
        /*if (isset($param['name'])) {
            $map['uid'] = getUserIdBuyName($param['name']);
        }*/
        if(getAdminAuthId(is_admin_login())!=1){
            $where['teacher_id']=is_admin_login();
            $map['tid'] = is_admin_login();
        }
        $list=model('myexam')->where($map)->whereNotNull('eid')->order('id desc')->paginate(config('page_number'),false,['query'=>request()->param()]);
        $videoCourse=model('videoCourse')->order('sort_order asc,addtime desc')->where($where)->select();
        $liveCourse=model('liveCourse')->order('sort_order asc,addtime desc')->where($where)->select();
        $regfield= model('regfield')->select();
        return $this->fetch('paperList', ['regfield'=>$regfield,'list' => $list,'course'=>array_merge($videoCourse,$liveCourse)]);
    }
    /**
     * 删除我的试卷列表
     */
    function delmypaper(){
        if ($this->request->isPost()) {
            if ($this->delete('myexam', $this->request->param()) === true) {
                insert_admin_log('删除试题类型');
                $this->success('删除成功');
            } else {
                $this->error($this->errorMsg);
            }
        }
    }
    /**
     * 试卷成绩分析
     */
    public function analysis(){
        $param = $this->request->param();
        $passNum=0;$noPassNum=0;
        $paperinfo=model('exams')->where('id',$param['id'])->find();
        $paperinfo['examsetting']=json_to_array($paperinfo['examsetting']);
        $myexam=model('myexam')->where(['eid'=>$param['id'],'status'=>1,'cid'=>$param['cid']])->select();
        $count=model('myexam')->where(['eid'=>$param['id'],'status'=>1,'cid'=>$param['cid']])->count();
        $maxScore=model('myexam')->where(['eid'=>$param['id'],'status'=>1,'cid'=>$param['cid']])->max('totalscores');
        $minScore=model('myexam')->where(['eid'=>$param['id'],'status'=>1,'cid'=>$param['cid']])->min('totalscores');
        $avgScore=model('myexam')->where(['eid'=>$param['id'],'status'=>1,'cid'=>$param['cid']])->avg('totalscores');
        foreach ($myexam as $k => $value) {
            $myexam[$k]['totalscores']>$paperinfo['examsetting']['passscore']?$passNum++:$noPassNum++;
        }
        $pass=round(100*$passNum/$count,2);
        $regfield= model('regfield')->select();
        return $this->fetch('analysis', ['regfield'=>$regfield,'cid'=>$param['cid'],'count'=>$count,'maxScore'=>$maxScore,'minScore'=>$minScore,'avgScore'=>$avgScore,'paperinfo' => $paperinfo,'passNum'=>$passNum,'noPassNum'=>$noPassNum,'pass'=>$pass]);
    }
    /**
     * 学员成绩分析
     */
    public function useranalysis(){
        $param = $this->request->param();
        $paperinfo=model('exams')->where('id',$param['id'])->find();
        $paperinfo['examsetting']=json_to_array($paperinfo['examsetting']);
        $myexam=model('myexam')->where(['eid'=>$param['id'],'status'=>1])->order('totalscores','desc')->select();
        $alysis['code']=0;
        $alysis['count']=$count=model('myexam')->where(['eid'=>$param['id'],'status'=>1])->count();
        foreach ($myexam as $k => $value) {
            $alysis['data'][]=['username'=>getUserName($myexam[$k]['uid']),
                'tel'=>getUserTel($myexam[$k]['uid']),
                'totalscore'=>$paperinfo['examsetting']['examscore'],
                'passscore'=>$paperinfo['examsetting']['passscore'],
                'score'=>$myexam[$k]['totalscores'],
                'ispass'=>$myexam[$k]['totalscores']>=$paperinfo['examsetting']['passscore']?'通过':'<font color="red">未通过</font>',
                'usedtime'=>$myexam[$k]['uid'],
                'addtime'=>$myexam[$k]['addtime'],
                'school'=>getUserSchoolBuyUid($myexam[$k]['uid']),
                'grade'=>getUserGradeBuyUid($myexam[$k]['uid']),
                'sort'=>$k+1
            ];
        }
        echo json_encode($alysis);
    }
    /**
     * 导出学员成绩
     */
    public function export(){
        $param = $this->request->param();
        $map['eid']=$param['eid'];
        $map['cid']=$param['cid'];
        $myexam=model('myexam')->field('id,eid,uid,cid,ctype,tid,totalscores,addtime')->order('id asc')->where($map)->select();
        $data = collection($myexam)->toArray();
        $examTitle=getCourseName($param['cid'],$data[0]['ctype']);
        $regfield= model('regfield')->select();
        foreach ($myexam as $k => $value) {
            $data[$k]['eid']=get_exam_title($myexam[$k]['eid']);
            $data[$k]['uid']=getUserName($myexam[$k]['uid']);
            if($regfield[1]['status']==1){
                $data[$k]['schoolid']=getUserSchoolBuyUid($myexam[$k]['uid']);
            }
            if($regfield[0]['status']==1){
                $data[$k]['gradeid']=getUserGradeBuyUid($myexam[$k]['uid']);
            }
            $data[$k]['cid']=getCourseName($myexam[$k]['cid'],$myexam[$k]['ctype']);
            $data[$k]['ctype']=$myexam['$k']['ctype']==1?'点播课程':'直播课程';
            $data[$k]['tid']=getUserTel($myexam[$k]['uid']);
        }
        if($regfield[0]['status']==1 and $regfield[1]['status']==1){
            array_unshift($data, ['ID', '试卷标题', '用户名','科目','课程类型','用户手机号','得分','考试时间',$regfield[1]['name'],$regfield[0]['name']]);
        }elseif($regfield[0]['status']==1){
            array_unshift($data, ['ID', '试卷标题', '用户名','科目','课程类型','用户手机号','得分','考试时间',$regfield[0]['name']]);
        }elseif($regfield[1]['status']==1){
            array_unshift($data, ['ID', '试卷标题', '用户名','科目','课程类型','用户手机号','得分','考试时间',$regfield[1]['name']]);
        }else{
            array_unshift($data, ['ID', '试卷标题', '用户名','科目','课程类型','用户手机号','得分','考试时间']);
        }
        $res=export_excel($data, $examTitle.'考试成绩统计');
        echo json_encode($res);
    }
   
    /**
     * 开放试卷列表
     */
    public function examsList(){
        $param = $this->request->param();
        if (isset($param['status'])) {
            if($param['status']==2){
                $map['status'] = 0;
            }else{
                $map['status'] = $param['status'];
            }
        }
        if (isset($param['mobile'])) {
            $map['uid'] = getUserIdBuyTel($param['mobile']);
        }
        if(getAdminAuthId(is_admin_login())!=1){
            $map['authorid'] = is_admin_login();
        }
        $map['cid']=null;
        $list=model('myexam')->where($map)->whereNotNull('eid')->order('id desc')->paginate(config('page_number'),false,['query'=>request()->param()]);
        $regfield= model('regfield')->select();
        return $this->fetch('packageList', ['regfield'=>$regfield,'list' => $list]);
    }
}