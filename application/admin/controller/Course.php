<?php
namespace app\admin\controller;
use app\common\controller\AdminBase;
vendor ('category.Category');
class Course extends AdminBase
{
    protected function _initialize()
    {
        parent::_initialize();
        if ($this->request->isGet()) {
            $this->assign('courseCategory', list_to_level(model('courseCategory')->order('sort_order asc')->select()));
        }
    }
    /**
     * 点播课程
     */
    public function videoIndex()
    {
        $param = $this->request->param();
        $where = [];
        if (isset($param['title'])) {
            $where['title'] = ['like', "%" . $param['title'] . "%"];
        }
        if (isset($param['cid'])) {
            $where['cid'] = $param['cid'];
        }
        if (isset($param['is_top'])) {
            $where['is_top'] = $param['is_top'];
        }
        if (isset($param['is_hot'])) {
            $where['is_hot'] = $param['is_hot'];
        }
        if (isset($param['status'])) {
            $where['status'] = $param['status'];
        }
        if(getAdminAuthId(is_admin_login())!=1){
            $map['teacher_id'] =is_admin_login();
        }
        $list = model('videoCourse')->with('courseCategory')->order('addtime desc')->where($where)->where($map)
            ->paginate(config('page_number'), false, ['query' => $param]);
        return $this->fetch('videoindex', ['list' => $list]);
    }
    /**
     * 添加点播课程
     */
    public function videoAdd(){
        if ($this->request->isPost()) {
            $param=$this->request->param();
            $param['addtime']=date('Y-m-d h:i:s', time());
            $param['teacher_id']=is_admin_login();
            if ($this->insert('videoCourse', $param) === true) {
                clear_cache();
                insert_admin_log('添加了点播课程');
                $this->success('添加成功', url('admin/course/videoindex'));
            } else {
                $this->error($this->errorMsg);
            }
        }
        return $this->fetch('videosave');
    }
    /**
     * 编辑点播课程
     */
    public function videoEdit()
    {
        if ($this->request->isPost()) {
            $param = $this->request->param();
            if (is_array($param['id'])) {
                $data = [];
                foreach ($param['id'] as $v) {
                    $data[] = ['id' => $v, $param['name'] => $param['value']];
                    $this->checkCurseAuth($v,1);
                }
                $result = $this->saveAll('videoCourse', $data, input('_verify', true));
            } else {
                $this->checkCurseAuth($param['id'],1);
                $result = $this->update('videoCourse', $param, input('_verify', true));
            }
            if ($result === true) {
                insert_admin_log('修改了点播课程');
                clear_cache();
                $this->success('修改成功', url('admin/course/videoindex'));
            } else {
                $this->error($this->errorMsg);
            }
        }
        return $this->fetch('videosave', ['data' => model('videoCourse')->get(input('id'))]);
    }
    /**
     * 删除点播课程
     */
    public function videoDel()
    {
        if ($this->request->isPost()) {
            $param = $this->request->param();
            $this->checkCurseAuth($param['id'],1);
            if ($this->delete('videoCourse', $param) === true) {
                db('user_course')->where(['cid'=>$param['id'],'type'=>1])->delete();
                db('favourite')->where(['cid'=>$param['id'],'type'=>1])->delete();
                db('order')->where(['cid'=>$param['id'],'ctype'=>1])->delete();
                insert_admin_log('删除了点播课程');
                clear_cache();
                $this->success('删除成功');
            } else {
                $this->error($this->errorMsg);
            }
        }
    }
    /**
     * 点播课程管理
     */
    public function videoAdmin(){
        $data=model('videoCourse')->where('id', input('id'))->find();
        $this->checkCurseAuth(input('id'),1);
        $sectionlist=model('videoSection')->order('sort_order asc')->where(['csid'=>input('id'),'ischapter'=>1])->select();
        if(!empty($sectionlist)){
            foreach ($sectionlist as $key => $value) {
                $sectionlist[$key]['seclist']=model('videoSection')->order('sort_order asc')->where(['chapterid'=>$sectionlist[$key]['id']])->select();
            }
        }else{
            $sectionlist=model('videoSection')->order('sort_order asc')->where(['csid'=>input('id')])->select();
        }
        return $this->fetch('videoadmin',['data' =>$data,'sectionlist'=>$sectionlist ]);
    }
    /**
     * 点播课程添加章
     */
    public function videoAddZhang(){
        if ($this->request->isPost()) {
            if ($this->insert('videoSection', $this->request->param()) === true) {
                insert_admin_log('添加了点播课程章节');
                $this->success('添加成功', url('admin/course/videoadmin',['id'=>input('cid')]));
            } else {
                $this->error($this->errorMsg);
            }
        }
        return $this->fetch('videoAddZhang',['cid'=>input('cid')]);
    }
    /**
     * 点播课程编辑章
     */
    public function videoEditZhang()
    {
        if ($this->request->isPost()) {
            $param = $this->request->param();
            $result = $this->update('videoSection', $param, input('_verify', true));
            if ($result === true) {
                insert_admin_log('修改了点播课程章节');
                $this->success('修改成功', url('admin/course/videoadmin'));
            } else {
                $this->error($this->errorMsg);
            }
        }
        $data=model('videoSection')->get(input('id'));
        return $this->fetch('videoAddZhang', ['cid'=>$data['csid'],'data' =>$data]);
    }
    /**
     * 点播课程添加课时
     */
    public function videoAddSection(){
        if ($this->request->isPost()) {
            if ($this->insert('videoSection', $this->request->param()) === true) {
                insert_admin_log('添加了点播课程章节');
                $this->success('添加成功', url('admin/course/videoadmin',['id'=>input('cid')]));
            } else {
                $this->error($this->errorMsg);
            }
        }
        $zhang=model('videoSection')->order('sort_order asc')->where(['csid'=>input('cid'),'ischapter'=>1])->select();
        return $this->fetch('videoAddSection',['cid'=>input('cid'),'zhang'=>$zhang]);
    }
    /**
     * 点播课程编辑课时
     */
    public function videoEditSection()
    {
        if ($this->request->isPost()) {
            $param = $this->request->param();
            $result = $this->update('videoSection', $param, input('_verify', true));
            if ($result === true) {
                insert_admin_log('修改了点播课程章节');
                $this->success('修改成功', url('admin/course/videoadmin'));
            } else {
                $this->error($this->errorMsg);
            }
        }
        $data=model('videoSection')->get(input('id'));
        $zhang=model('videoSection')->order('sort_order asc')->where(['csid'=>$data['csid'],'ischapter'=>1])->select();
        return $this->fetch('videoAddSection', ['cid'=>$data['csid'],'data' =>$data,'zhang'=>$zhang]);
    }
    /**
     * 点播课程删除课时
     */
    public function videoDelSection()
    {
        if ($this->request->isPost()) {
            $param = $this->request->param();
            if ($this->delete('videoSection', $param) === true) {
                db('learned')->where(['sid'=>$param['id'],'type'=>1])->delete();
                insert_admin_log('删除了点播课程');
                $this->success('删除成功');
            } else {
                $this->error($this->errorMsg);
            }
        }
    }
    /**
     * 本地视频列表
     */
    public function localvideolist(){
        $dir = 'upload/video/'.is_admin_login();
        $videolist=(list_sort_by($this->printdir($dir),'time','desc'));
        return $this->fetch('localvideolist',['list'=>$videolist,'dir'=>is_admin_login(),'flag'=>2]);
    }
    function printdir($dir){
        $files = array();
        $k=0;
        if($handle = @opendir($dir)){
            while(($file = readdir($handle)) !== false){
                if( $file != ".." && $file != "."){
                    if(is_dir($dir . "/" . $file)) {
                        $files[get_admin_name($file)] = $this->printdir($dir . "/" . $file);
                    } else {
                        $filetime = date('Y-m-d H:i:s', filemtime($dir . "/" . $file));
                        $files[$k]['time'] = $filetime;
                        $files[$k]['name'] = substr($file,0,strpos($file,"."));
                        $files[$k]['id']=$file;
                        $k++;
                    }
                }
            }
            @closedir($handle);
            return $files;
        }
    }
    /**
     * 本地视频管理
     */
    function localvideoadmin(){
        $param=$this->request->param();
        if (!empty($param['tid'])) {
            if(getAdminAuthId(is_admin_login())!=1){
                $this->error('不要乱来呦！');
            }else{
                $dir='upload/video/'.$param['tid'];
                $videolist=(list_sort_by($this->printdir($dir),'time','desc'));
                return $this->fetch('localvideoadmin',['list'=>$videolist,'role'=>'file','tid'=>$param['tid']]);
            }
        }else{
            if(getAdminAuthId(is_admin_login())==1){
                $dir='upload/video';
                $files = array();$k=0;
                if($handle = @opendir($dir)){
                    while(($file = readdir($handle)) !== false){
                        if( $file != ".." && $file != "."){
                            if(is_dir($dir . "/" . $file)) {
                                $files[$k]['teachername'] = get_admin_name($file);
                                $files[$k]['teacherid'] = $file;
                                $k++;
                            }
                        }
                    }
                    @closedir($handle);
                }
                return $this->fetch('localvideoadmin',['list'=>$files,'role'=>'dir','tid'=>is_admin_login()]);
            }else{
                $dir='upload/video/'.is_admin_login();
                $videolist=(list_sort_by($this->printdir($dir),'time','desc'));
                return $this->fetch('localvideoadmin',['list'=>$videolist,'role'=>'file','tid'=>is_admin_login()]);
            }
        }
    }
    /**
     * 上传本地视频
     */
    public function upvideolist(){
        return $this->fetch('localvideolist',['flag'=>1]);
    }
    /**
     * 删除本地视频
     */
    function localvideodell(){
        $param=$this->request->param();
        $dir='upload/video/';
        if(is_array($param['id'])){
            foreach($param['id'] as $value){
                $file=ROOT_PATH . 'public' . DS .$dir.$param['tid']. DS .$value;
                unlink($file);
            }
            $this->success('删除成功');
        }else{
            $file=ROOT_PATH . 'public' . DS .$dir.$param['tid']. DS .$param['id'];
            if(unlink($file)){
                $this->success('删除成功');
            }else{
                $this->success('删除失败');
            }
        }
    }
    /**
     * 阿里云视频列表
     */
    public function videoList(){
        $param = $this->request->param();
        $res= controller('admin/educloud')->videoList2($param);
        $restemp = json_to_array($res);
        $categoryList=controller('admin/educloud')->getvideoCategory(1,100);
        return $this->fetch('videoList', ['categoryList'=>$categoryList['SubCategories']['Category'],'list' => $restemp['VideoList']['Video'],'curr'=>$param['page'],'count'=>$restemp['Total'],'PageSize'=>config('PageSize'),'userId'=>config('AliUserId'),'categoryid'=>$param['CateId']]);
    }

    /**
     * 点播课程添加文本课时
     */
    public function videoAddDoc(){
        if ($this->request->isPost()) {
            if ($this->insert('videoSection', $this->request->param()) === true) {
                insert_admin_log('添加了点播课程章节');
                $this->success('添加成功', url('admin/course/videoadmin',['id'=>input('cid')]));
            } else {
                $this->error($this->errorMsg);
            }
        }
        $zhang=model('videoSection')->order('sort_order asc')->where(['csid'=>input('cid'),'ischapter'=>1])->select();
        return $this->fetch('videoAddDoc',['cid'=>input('cid'),'zhang'=>$zhang]);
    }
    /**
     * 点播课程编辑文本课时
     */
    public function videoEditDoc()
    {
        if ($this->request->isPost()) {
            $param = $this->request->param();
            $result = $this->update('videoSection', $param, input('_verify', true));
            if ($result === true) {
                insert_admin_log('修改了点播课程章节');
                $this->success('修改成功', url('admin/course/videoadmin'));
            } else {
                $this->error($this->errorMsg);
            }
        }
        $data=model('videoSection')->get(input('id'));
        $zhang=model('videoSection')->order('sort_order asc')->where(['csid'=>$data['csid'],'ischapter'=>1])->select();
        return $this->fetch('videoAddDoc', ['cid'=>$data['csid'],'data' =>$data,'zhang'=>$zhang]);
    }
    /**
     * 点播课程添加考试课程
     */
    public function videoaddExam()
    {
        if ($this->request->isPost()) {
            $param = $this->request->param();
            $param['addtime'] = date('Y-m-d h:i:s', time());
            $param['ischapter'] = 0;
            $param['certificate']=json_encode(['iscertificate'=>$param['iscertificate'],'iscertificatetitle'=>$param['iscertificatetitle'],'organ'=>$param['organ']]);
            if ($this->insert('videoSection', $param) === true) {
                insert_admin_log('添加了点播课程考试章节');
                $this->success('添加成功', url('admin/course/videoadmin', ['id' => input('csid')]));
            } else {
                $this->error($this->errorMsg);
            }
        }
        $zhang = model('videoSection')->order('sort_order asc')->where(['csid' => input('cid'), 'ischapter' => 1])->select();
        return $this->fetch('videoaddExam', ['cid' => input('cid'), 'zhang' => $zhang]);
    }
    /**
     * 点播课程编辑考试课程
     */
    public function videoEditExam()
    {
        if ($this->request->isPost()) {
            $param = $this->request->param();
            $param['addtime'] = date('Y-m-d h:i:s', time());
            $param['ischapter'] = 0;
            $param['certificate']=json_encode(['iscertificate'=>$param['iscertificate'],'iscertificatetitle'=>$param['iscertificatetitle'],'organ'=>$param['organ']]);
            $result = $this->update('videoSection', $param, input('_verify', true));
            if ($result === true) {
                insert_admin_log('修改了点播课程考试章节');
                $this->success('修改成功', url('admin/course/videoadmin'));
            } else {
                $this->error($this->errorMsg);
            }
        }
        $data = model('videoSection')->get(input('id'));
        $data['certificate']=json_to_array($data['certificate']);
        $zhang = model('videoSection')->order('sort_order asc')->where(['csid' => $data['csid'], 'ischapter' => 1])->select();
        return $this->fetch('videoaddExam', ['cid' => $data['csid'], 'data' => $data, 'zhang' => $zhang]);
    }
    /**
     * 试卷列表
     */
    public function paperlist(){
        if(getAdminAuthId(is_admin_login())!=1){
            $map['examauthorid'] =is_admin_login();
        }
        $list=model('Exams')->order('id desc')->where($map)->paginate(15);
        return $this->fetch('paperList',['list'=>$list]);
    }
    /**
     * 课程学员列表
     */
    public function xueyuanList(){
        $param = $this->request->param(); $where = [];
        if (isset($param['key'])) {
            if(check_mobile($param['key'])){
                $fiterId=collection(model('user')->field('id')->where(['mobile'=>$param['key']])->select())->toArray();
                foreach ($fiterId as   $key => $value) {
                    $fiterIds[]=$fiterId[$key]['id'];
                }
                $where['uid'] =array('in',$fiterIds);
            }else{
                $fiterId=collection(model('user')->field('id')->where(['nickname'=>$param['key']])->select())->toArray();
                foreach ($fiterId as   $key => $value) {
                    $fiterIds[]=$fiterId[$key]['id'];
                }
                $where['uid'] = array('in',$fiterIds);
            }
        }
        $where['cid']=$param['cid'];
        $where['type']=$param['type'];
        $list=model('userCourse')->where($where)->order('id desc')->paginate(config('page_number'),false,['query'=>request()->param()]);
        foreach ($list as $key => $value) {
            $list[$key]['progress']=round(getStuduedNum($param['cid'],$param['type'],$list[$key]['uid'])/getCourseNum($param['cid'],$param['type'])*100);
        }
        return $this->fetch('xueyuanList', ['list'=>$list,'cid'=>$param['cid'],'type'=>$param['type']]);
    }
    /**
     * 向课程中添加学员
     */
    function addstudents(){
        if ($this->request->isPost()) {
            $param = $this->request->param();
            if (is_array($param['id'])) {
                $data = [];
                foreach ($param['id'] as $v) {
                    $data[] = ['uid' => $v, 'cid'=>$param['cid'],'type'=>$param['ctype'],'state'=>1,'addtime'=>date('Y-m-d h:i:s', time())];
                }
                $result = $this->saveAll('userCourse', $data, input('_verify', true));
            }
            if ($result === true) {
                insert_admin_log('向课程中添加了学员');
                clear_cache();
                $param['ctype']==1?$url='admin/course/videoindex':$url='admin/course/liveindex';
                $this->success('添加成功', url($url));
            } else {
                $this->error($this->errorMsg);
            }
        }
        $param = $this->request->param();
        $added=collection(model('userCourse')->field('uid')->where(['cid'=>$param['cid'],'type'=>$param['ctype']])->select())->toArray();
        foreach ($added as   $key => $value) {
            $addedIds[]=$added[$key]['uid'];
        }
        $where['id'] = array('not in',$addedIds);
        if (isset($param['key'])) {
            if(check_mobile($param['key'])){
                $where['mobile'] = ['like', "%" . $param['key'] . "%"];
            }else{
                $where['username'] = ['like', "%" . $param['key'] . "%"];
            }
        }
        if (isset($param['school'])) {
            $where['schoolId'] = $param['school'];
        }
        if (isset($param['grade'])) {
            $where['greadId'] = $param['grade'];
        }
        $regfield= model('regfield')->select();
        $grade=model('grade')->order('sort_order asc')->select();
        $school=model('school')->order('sort_order asc')->select();
        $list=model('user')->order('id desc')->where($where)->paginate(config('page_number'),false,['query'=>request()->param()]);
        return $this->fetch('addstudents', ['type'=>$param['ctype'],'cid'=>$param['cid'],'regfield'=>$regfield,'grade'=>$grade,'school'=>$school,'list' =>$list]);
    }
    /**
     * 向课程中批量导入学员
     */
    public function importStudents(){
        $param = $this->request->param();
        session('cid',$param['cid']);
        session('type',$param['ctype']);
        return $this->fetch('importStudents');
    }
    /**
     * 向课程中批量导入学员入库
     */
    public function importStudentsExcel(){
        try {
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
                $errorNum=0;
                $successNum=0;
                for ($j = 2; $j < $allRow; $j++) {
                    $data['username'] = $objPHPExcel->getActiveSheet()->getCell("A" . $j)->getValue();
                    $data['nickname'] = $objPHPExcel->getActiveSheet()->getCell("B" . $j)->getValue();
                    $data['mobile'] = $objPHPExcel->getActiveSheet()->getCell("C" . $j)->getValue();
                    $data['password'] = md5($objPHPExcel->getActiveSheet()->getCell("D" . $j)->getValue());
                    $data['yue'] = $objPHPExcel->getActiveSheet()->getCell("E" . $j)->getValue();
                    $data['create_time']=time();
                    if (!$user = model('user')->where(['mobile' => $data['mobile']])->find()) {
                        $uid=model('user')->insertGetId($data);
                        $successNum=$successNum+1;
                    } else {
                        model('user')->where('id',$user['id'])->setField('create_time', time());
                        $uid=$user['id'];
                        $errorNum=$errorNum+1;
                    }
                    if(!$user = model('userCourse')->where(['uid' => $uid,'cid'=>session('cid'),'type'=>session('type')])->find()){
                        $insertArray[]=['uid'=>$uid,'cid'=>session('cid'),'type'=>session('type'),'state'=>1,'addtime'=>date('Y-m-d H:i:s', time())];
                    }
                }
                model('userCourse')->insertAll($insertArray);
                if($errorNum>0){
                    $msg='导入成功了'.$successNum.'个学员,重复了'.$errorNum.'个手机号未导入！';
                }else{
                    $msg='导入成功了'.$successNum.'个学员;';
                }
                session('cid', null);
                session('type', null);
                return ['code' => 1, 'msg' => $msg];
            } else {
                $excel_path = null;
            }
        } catch (\Exception $e) {
            return ['code' => 0, 'msg' => $e->getMessage()];
        }
    }
    /**
     * 删除课程中指定学员
     */
    function delStudent(){
        if ($this->request->isPost()){
            $param = $this->request->param();
            if(model('userCourse')->where(['cid'=>$param['cid'],'type'=>$param['type'],'uid'=>$param['uid']])->delete()){
                if(db('learned')->where(['cid'=>$param['cid'],'type'=>$param['type'],'uid'=>$param['uid']])->find()){
                    db('learned')->where(['cid'=>$param['cid'],'type'=>$param['type'],'uid'=>$param['uid']])->delete();
                }
                $this->success('删除成功');
            }else {
                $this->error($this->errorMsg);
            }
        }
    }
    /**
     * 课程资料列表
     */
    public function materialList(){
        $param = $this->request->param();
        if($param['type']==1){
            $courseInfo=model('videoCourse')->where(['id'=>$param['cid']])->field('material_id')->find();
        }
        if($param['type']==2){
            $courseInfo=model('liveCourse')->where(['id'=>$param['cid']])->field('material_id')->find();
        }
        $materialIds=json_to_array($courseInfo['material_id']);
        $where['id']=['in',$materialIds];
        return $this->fetch('materialList', ['cid'=>input('cid'),'list' => model('Material')->where($where)->where('type','file')->order('id desc')->paginate(7)]);
    }
    /**
     * 课程添加资料
     */
    public function MaterialAdd(){
        return $this->fetch('materialAdd', ['list' => model('Material')->where('type','file')->order('id desc')->paginate(7),'cid'=>input('cid'),'cstype'=>input('cstype')]);
    }
    /**
     * 课程删除资料，只是删除关联关系，并未真实删除
     */
    public function videoMaterialDel()
    {
        if ($this->request->isPost()){
            $model=model('videoCourse');
            $courseInfo=$model->where(['id'=>input('cid')])->field('material_id')->find();
            $materialIds=json_to_array($courseInfo['material_id']);
            foreach( $materialIds as $k=>$v) {
                if(input('mid') == $v) unset($materialIds[$k]);
            }
            $data['material_id']=json_encode($materialIds);
            if($model->allowField(true)->save($data, ['id'=>input('cid')])){
                $this->success('删除成功');
            }else {
                $this->error($this->errorMsg);
            }
        }
    }
    /**
     * 向点播课程中添加资料
     */
    public function MaterialInsert(){
        if ($this->request->isPost()) {
            if(input('cstype')==1){
                $model= model('videoCourse');
            }
            if(input('cstype')==2){
                $model= model('liveCourse');
            }
            $courseInfo=$model->where(['id'=>input('cid')])->field('material_id')->find();
            $materialIds=json_to_array($courseInfo['material_id']);
            if(empty($materialIds)){
                $materialIdsArr[]=input('id');
                $data['material_id']=json_encode($materialIdsArr);
            }else{
                if(in_array(input('id'),$materialIds)){
                    $this->error("课程中已存在，不要重复添加");
                }else{
                    array_push($materialIds,input('id'));
                    $data['material_id']=json_encode($materialIds);
                }
            }
            if($model->allowField(true)->save($data, ['id'=>input('cid')])){
                insert_admin_log('向点播课程中添加了资料');
                $this->success('添加成功');
            }else {
                $this->error($this->errorMsg);
            }
        }
    }
    /**
     * 直播课程
     */
    public function liveIndex(){
        $param = $this->request->param();
        $where = [];
        if (isset($param['title'])) {
            $where['title'] = ['like', "%" . $param['title'] . "%"];
        }
        if (isset($param['cid'])) {
            $where['cid'] = $param['cid'];
        }
        if (isset($param['is_top'])) {
            $where['is_top'] = $param['is_top'];
        }
        if (isset($param['is_hot'])) {
            $where['is_hot'] = $param['is_hot'];
        }
        if (isset($param['status'])) {
            $where['status'] = $param['status'];
        }
        if(getAdminAuthId(is_admin_login())!=1){
            $map['teacher_id'] =is_admin_login();
        }
        $list = model('liveCourse')->with('courseCategory')->order('sort_order asc')->where($where)->where($map)
            ->paginate(config('page_number'), false, ['query' => $param]);
        return $this->fetch('liveindex', ['list' => $list]);
    }
    /**
     * 课程直播课程
     */
    public function liveAdd(){
        if ($this->request->isPost()) {
            $param=$this->request->param();
            $param['addtime']=date('Y-m-d h:i:s', time());
            $param['teacher_id']=is_admin_login();
            if ($this->insert('liveCourse',$param) === true) {
                clear_cache();
                insert_admin_log('添加了点播课程');
                $this->success('添加成功', url('admin/course/liveIndex'));
            } else {
                $this->error($this->errorMsg);
            }
        }
        return $this->fetch('livesave');
    }
    /**
     * 编辑直播课程
     */
    public function liveEdit()
    {
        if ($this->request->isPost()) {
            $param = $this->request->param();
            if (is_array($param['id'])) {
                $data = [];
                foreach ($param['id'] as $v) {
                    $data[] = ['id' => $v, $param['name'] => $param['value']];
                    $this->checkCurseAuth($v,2);
                }
                $result = $this->saveAll('liveCourse', $data, input('_verify', true));
            } else {
                $this->checkCurseAuth($param['id'],2);
                $result = $this->update('liveCourse', $param, input('_verify', true));
            }
            if ($result === true) {
                insert_admin_log('修改了点播课程');
                clear_cache();
                $this->success('修改成功', url('admin/course/liveIndex'));
            } else {
                $this->error($this->errorMsg);
            }
        }
        return $this->fetch('livesave', ['data' => model('liveCourse')->get(input('id'))]);
    }
    /**
     * 删除直播课程
     */
    public function liveDel()
    {
        if ($this->request->isPost()) {
            $param=$this->request->param();
            $this->checkCurseAuth($param['id'],2);
            if ($this->delete('liveCourse', $param) === true) {
                db('user_course')->where(['cid'=>$param['id'],'type'=>2])->delete();
                db('favourite')->where(['cid'=>$param['id'],'type'=>2])->delete();
                db('order')->where(['cid'=>$param['id'],'ctype'=>2])->delete();
                insert_admin_log('删除了点播课程');
                clear_cache();
                $this->success('删除成功');
            } else {
                $this->error($this->errorMsg);
            }
        }
    }
    /**
     * 直播课程管理
     */
    public function liveAdmin(){
        $data=model('liveCourse')->where('id', input('id'))->find();
        $this->checkCurseAuth(input('id'),2);
        $sectionlist=model('liveSection')->order('sort_order asc')->where(['csid'=>input('id'),'ischapter'=>1])->select();
        if(!empty($sectionlist)){
            foreach ($sectionlist as $key => $value) {
                $sectionlist[$key]['seclist']=model('liveSection')->order('sort_order asc')->where(['chapterid'=>$sectionlist[$key]['id']])->select();
            }
        }else{
            $sectionlist=model('liveSection')->order('sort_order asc')->where(['csid'=>input('id')])->select();
        }
        return $this->fetch('liveadmin',['data' =>$data,'sectionlist'=>$sectionlist]);
    }
    /**
     * 直播课程添加章
     */
    public function liveAddZhang(){
        if ($this->request->isPost()) {
            if ($this->insert('liveSection', $this->request->param()) === true) {
                insert_admin_log('添加了点播课程章节');
                $this->success('添加成功', url('admin/course/liveadmin',['id'=>input('cid')]));
            } else {
                $this->error($this->errorMsg);
            }
        }
        return $this->fetch('liveAddZhang',['cid'=>input('cid')]);
    }
    /**
     * 直播课程编辑章
     */
    public function liveEditZhang()
    {
        if ($this->request->isPost()) {
            $param = $this->request->param();
            $result = $this->update('liveSection', $param, input('_verify', true));
            if ($result === true) {
                insert_admin_log('修改了点播课程章节');
                $this->success('修改成功', url('admin/course/liveadmin'));
            } else {
                $this->error($this->errorMsg);
            }
        }
        $data=model('liveSection')->get(input('id'));
        return $this->fetch('liveAddZhang', ['cid'=>$data['csid'],'data' =>$data]);
    }
    /**
     * 直播课程添加课时创建直播间
     */
    public function liveAddSection(){
        if ($this->request->isPost()) {
            $param=$this->request->param();
            $info=$this->get_site_info(4);
            if(empty($info['agoraAppid'])){
                $this->error("请先配置声网参数");exit();
            }
            $res = controller('admin/educloud')->createRoomId();
            if ($res['code'] == 0) {
                $param['room_id'] = $res['room_id'];
                if ($this->insert('liveSection', $param) === true) {
                    insert_admin_log('添加了直播课程章节');
                    $this->success('添加成功', url('admin/course/liveadmin', ['id' => input('cid')]));
                } else {
                    $this->error($this->errorMsg);
                }
            } else {
                $this->error($res['msg']);
            }
        }
        $zhang=model('liveSection')->order('sort_order asc')->where(['csid'=>input('cid'),'ischapter'=>1])->select();
        return $this->fetch('liveAddSection',['cid'=>input('cid'),'zhang'=>$zhang]);
    }
    /**
     * 编辑直播间信息
     */
    public function liveEditSection()
    {
        if ($this->request->isPost()) {
            $param = $this->request->param();
            $result = $this->update('liveSection', $param, input('_verify', true));
            if ($result === true) {
                insert_admin_log('修改了点播课程章节');
                $this->success('修改成功', url('admin/course/liveadmin'));
            } else {
                $this->error($this->errorMsg);
            }
        }
        $data=model('liveSection')->get(input('id'));
        $zhang=model('liveSection')->order('sort_order asc')->where(['csid'=>$data['csid'],'ischapter'=>1])->select();
        return $this->fetch('liveAddSection', ['cid'=>$data['csid'],'data' =>$data,'zhang'=>$zhang]);
    }
    /**
     * 删除直播课时
     */
    public function liveDelSection(){
        {
            if ($this->request->isPost()) {
                $param = $this->request->param();
                if( model('liveSection')->where('chapterid', input('id'))->find()){
                    $this->error('请先删除此章下的节');
                }else{
                    if ($this->delete('liveSection', $param) === true) {
                        db('learned')->where(['sid'=>$param['id'],'type'=>2])->delete();
                        insert_admin_log('删除了点播课程章节');
                        $this->success('删除成功');
                    } else {
                        $this->error($this->errorMsg);
                    }
                }
            }
        }
    }
    /**
     * 直播课程添加文回放地址
     */
    function  liveaddrecord(){
        if ($this->request->isPost()) {
            $param = $this->request->param();
            if ($this->update('liveSection', $param, input('_verify', false))===true) {
                insert_admin_log('添加了直播回放');
                $this->success('添加成功', url('admin/course/liveadmin',['id'=>input('cid')]));
            } else {
                $this->error($this->errorMsg);
            }
        }
        $data=model('liveSection')->get(input('id'));
        return $this->fetch('liveaddrecord',['data'=>$data]);
    }
    /**
     * 直播课程添加文本课时
     */
    public function liveAddDoc(){
        if ($this->request->isPost()) {
            if ($this->insert('liveSection', $this->request->param()) === true) {
                insert_admin_log('添加了点播课程章节');
                $this->success('添加成功', url('admin/course/liveadmin',['id'=>input('cid')]));
            } else {
                $this->error($this->errorMsg);
            }
        }
        $zhang=model('liveSection')->order('sort_order asc')->where(['csid'=>input('cid'),'ischapter'=>1])->select();
        return $this->fetch('liveAddDoc',['cid'=>input('cid'),'zhang'=>$zhang]);
    }
    /**
     * 直播课程编辑文本课时
     */
    public function liveEditDoc()
    {
        if ($this->request->isPost()) {
            $param = $this->request->param();
            $result = $this->update('liveSection', $param, input('_verify', true));
            if ($result === true) {
                insert_admin_log('修改了点播课程章节');
                $this->success('修改成功', url('admin/course/liveadmin'));
            } else {
                $this->error($this->errorMsg);
            }
        }
        $data=model('liveSection')->get(input('id'));
        $zhang=model('liveSection')->order('sort_order asc')->where(['csid'=>$data['csid'],'ischapter'=>1])->select();
        return $this->fetch('liveAddDoc', ['cid'=>$data['csid'],'data' =>$data,'zhang'=>$zhang]);
    }
    /**
     * 直播课程添加考试课程
     */
    public function liveaddExam()
    {
        if ($this->request->isPost()) {
            $param = $this->request->param();
            $param['addtime'] = date('Y-m-d h:i:s', time());
            $param['ischapter'] = 0;
            $param['certificate']=json_encode(['iscertificate'=>$param['iscertificate'],'iscertificatetitle'=>$param['iscertificatetitle'],'organ'=>$param['organ']]);
            if ($this->insert('liveSection', $param) === true) {
                insert_admin_log('添加了直播课程考试章节');
                $this->success('添加成功', url('admin/course/liveadmin', ['id' => input('csid')]));
            } else {
                $this->error($this->errorMsg);
            }
        }
        $zhang = model('liveSection')->order('sort_order asc')->where(['csid' => input('cid'), 'ischapter' => 1])->select();
        return $this->fetch('liveaddExam', ['cid' => input('cid'), 'zhang' => $zhang]);
    }
    /**
     * 直播课程添加考试课程
     */
    public function liveEditExam()
    {
        if ($this->request->isPost()) {
            $param = $this->request->param();
            $param['addtime'] = date('Y-m-d h:i:s', time());
            $param['ischapter'] = 0;
            $param['certificate']=json_encode(['iscertificate'=>$param['iscertificate'],'iscertificatetitle'=>$param['iscertificatetitle'],'organ'=>$param['organ']]);
            $result = $this->update('liveSection', $param, input('_verify', true));
            if ($result === true) {
                insert_admin_log('修改了点播课程考试章节');
                $this->success('修改成功', url('admin/course/liveadmin'));
            } else {
                $this->error($this->errorMsg);
            }
        }
        $data = model('liveSection')->get(input('id'));
        $data['certificate']=json_to_array($data['certificate']);
        $zhang = model('liveSection')->order('sort_order asc')->where(['csid' => $data['csid'], 'ischapter' => 1])->select();
        return $this->fetch('liveaddExam', ['cid' => $data['csid'], 'data' => $data, 'zhang' => $zhang]);
    }
    /**
     * 线下课程
     */
    function OfflineCourse(){
        $param = $this->request->param();
        $where = [];
        if (isset($param['title'])) {
            $where['title'] = ['like', "%" . $param['title'] . "%"];
        }
        if (isset($param['cid'])) {
            $where['cid'] = $param['cid'];
        }
        if (isset($param['is_top'])) {
            $where['is_top'] = $param['is_top'];
        }
        if (isset($param['is_hot'])) {
            $where['is_hot'] = $param['is_hot'];
        }
        if (isset($param['status'])) {
            $where['status'] = $param['status'];
        }
        if(getAdminAuthId(is_admin_login())!=1){
            $map['teacher_id'] =is_admin_login();
        }
        $list = model('offlineCourse')->with('courseCategory')->order('sort_order asc')->where($where)->where($map)
            ->paginate(config('page_number'), false, ['query' => $param]);
        return $this->fetch('OfflineCourse', ['list' => $list]);
    }
    /**
     * 添加线下课程
     */
    function addOfflineCourse(){
        if ($this->request->isPost()) {
            $param=$this->request->param();
            $param['addtime']=date('Y-m-d h:i:s', time());
            if ($this->insert('offlineCourse',$param) === true) {
                clear_cache();
                insert_admin_log('添加了线下课程');
                $this->success('添加成功', url('admin/course/OfflineCourse'));
            } else {
                $this->error($this->errorMsg);
            }
        }
        return $this->fetch('addofflinecourse');
    }
    /**
     * 编辑线下课程
     */
    function editOfflineCourse(){
        if ($this->request->isPost()) {
            $param = $this->request->param();
            if (is_array($param['id'])) {
                $data = [];
                foreach ($param['id'] as $v) {
                    $data[] = ['id' => $v, $param['name'] => $param['value']];
                }
                $result = $this->saveAll('offlineCourse', $data, input('_verify', true));
            } else {
                $result = $this->update('offlineCourse', $param, input('_verify', true));
            }
            if ($result === true) {
                insert_admin_log('修改了线下课程');
                clear_cache();
                $this->success('修改成功', url('admin/course/OfflineCourse'));
            } else {
                $this->error($this->errorMsg);
            }
        }
        return $this->fetch('addofflinecourse',['data' => model('offlineCourse')->get(input('id'))]);
    }
    /**
     * 删除线下课程
     */
    function delOfflineCourse(){
        if ($this->request->isPost()) {
            $param=$this->request->param();
            if ($this->delete('offlineCourse', $param) === true) {
                insert_admin_log('删除了线下课程');
                clear_cache();
                $this->success('删除成功');
            } else {
                $this->error($this->errorMsg);
            }
        }
    }
    /**
     * 线下课程学员列表
     */
    function signupList(){
        $param = $this->request->param();
        $where = [];
        if (isset($param['key'])) {
            if(check_mobile($param['key'])){
                $where['tel'] =$param['key'];
            }else{
                $where['username'] = ['like', "%" . $param['title'] . "%"];;
            }
        }
        $where['cid']=$param['cid'];
        $list=db('signup')->where($where)->order('id desc')->paginate(config('page_number'),false,['query'=>request()->param()]);
        return $this->fetch('signupList', ['list'=>$list,'cid'=>$param['cid']]);
    }
    /**
     * 导出线下课程学员信息
     */
    function exportOfflineStu(){
        $param = $this->request->param();
        $user=collection(db('signup')->field('username,tel,address,addtime')->where(['cid'=>$param['cid']])->order('uid asc')->select())->toArray();
        $title=getCourseName($param['cid'],5).'-学员信息';
        array_unshift($user,['学员姓名','联系电话','详细地址','报名时间']);
        $res=export_excel($user,$title);
        echo json_encode($res);
    }
    /**
     * 课程删除资料，只是删除关联关系，并未真实删除
     */
    public function liveMaterialDel()
    {
        if ($this->request->isPost()){
            $model=model('liveCourse');
            $courseInfo=$model->where(['id'=>input('cid')])->field('material_id')->find();
            $materialIds=json_to_array($courseInfo['material_id']);
            foreach( $materialIds as $k=>$v) {
                if(input('mid') == $v) unset($materialIds[$k]);
            }
            $data['material_id']=json_encode($materialIds);
            if($model->allowField(true)->save($data, ['id'=>input('cid')])){
                $this->success('删除成功');
            }else {
                $this->error($this->errorMsg);
            }
        }
    }

    /**
     * 课程分类
     */
    public function courseCategory(){
        $list=model('courseCategory')->order('sort_order asc')->select();
        foreach ($list as $key => $value) {
            $list[$key]['knowledgePoints']=count(model('knowledge')->where(['sectionid'=>$list[$key]['id']])->select());
        }
        $cat = new \org\Category(array('id', 'pid', 'category_name','cname'));
        $data = $cat->getTree($list, intval($id=0));
        return $this->fetch('courseCategory', ['list' => $data]);
    }
    /**
     * 添加课程分类
     */
    public function categoryAdd()
    {
        if ($this->request->isPost()) {
            if ($this->insert('courseCategory', $this->request->param()) === true) {
                insert_admin_log('添加了课程分类');
                $this->success('添加成功', url('admin/course/courseCategory'));
            } else {
                $this->error($this->errorMsg);
            }
        }
        $list=model('courseCategory')->order('sort_order asc')->select();
        $cat = new \org\Category(array('id', 'pid', 'category_name','cname'));
        $data = $cat->getTree($list, intval($id=0));
        return $this->fetch('categorySave', ['category' => $data]);
    }
    /**
     * 编辑课程分类
     */
    public function categoryEdit()
    {
        if ($this->request->isPost()) {
            if ($this->update('courseCategory', $this->request->param(), input('_verify', true)) === true) {
                insert_admin_log('修改了课程分类');
                $this->success('修改成功', url('admin/course/coursecategory'));
            } else {
                $this->error($this->errorMsg);
            }
        }
        $list=model('courseCategory')->order('sort_order asc')->select();
        $cat = new \org\Category(array('id', 'pid', 'category_name','cname'));
        $data = $cat->getTree($list, intval($id=0));
        return $this->fetch('categorySave', [
            'data'     => model('courseCategory')->where('id', input('id'))->find(),
            'category' => $data,
        ]);
    }
    /**
     * 删除课程分类
     */
    public function categoryDel()
    {
        if ($this->request->isPost()) {
            if( model('courseCategory')->where('pid', input('id'))->find() or model('knowledge')->where('sectionid', input('id'))->find()){
                $this->error('请先删除子分类或者知识点');
            }else{
                if ($this->delete('courseCategory', $this->request->param()) === true) {
                    insert_admin_log('删除了课程分类');
                    $this->success('删除成功');
                } else {
                    $this->error($this->errorMsg);
                }
            }
        }
    }
    /**
     * 分类知识点列表
     */
    public function knowledgeList()
    {
        $list=model('knowledge')->where('sectionid', input('pid'))->order('sort_order asc')->select();
        return $this->fetch('knowledgeList', ['list' => $list]);
    }
    /**
     * 给分类添加知识点
     */
    public function knowledgeAdd(){
        if ($this->request->isPost()) {
            $this->isAdd(input('sectionid'));
            if ($this->insert('knowledge', $this->request->param()) === true) {
                insert_admin_log('添加了知识点');
                $this->success('添加成功', url('admin/course/courseCategory'));
            } else {
                $this->error($this->errorMsg);
            }
        }
        return $this->fetch('knowledgeSave',['pid'=>input('pid')]);
    }
    /**
     * 给分类编辑知识点
     */
    public function knowledgeEdit(){
        if ($this->request->isPost()) {
            if ($this->update('knowledge', $this->request->param(),input('_verify', true)) === true) {
                insert_admin_log('编辑了知识点');
                $this->success('编辑成功');
            } else {
                $this->error($this->errorMsg);
            }
        }
        $data=model('knowledge')->get(input('id'));
        return $this->fetch('knowledgeEdit',['data' =>$data]);
    }
    /**
     * 删除知识点
     */
    public function knowledgeDel(){
        if ($this->request->isPost()) {
            if ($this->delete('knowledge', $this->request->param()) === true) {
                insert_admin_log('删除了知识点');
                $this->success('删除成功');
            } else {
                $this->error($this->errorMsg);
            }
        }
    }
    /**
     * 获取知识点
     */
    public function ajaxGetKnowledge(){
        $list=model('knowledge')->where('sectionid', input('id'))->order('sort_order asc')->select();
        echo json_encode($list);
    }
    /**
     * 判断是否可以添加知识点
     */
    public function isAdd($pid){
        if(model('courseCategory')->where('pid',$pid)->select()){
            $this->error('此处不能添加知识点!',url('admin/course/courseCategory'));
        }
    }
    /**
     * 课程订单
     */
    public function courseOrder(){
        $param = $this->request->param();
        $where = [];
        if (isset($param['orderid'])) {
            $where['orderid'] = ['like', "%" . $param['orderid'] . "%"];
        }
        if (isset($param['state'])) {
            $where['state'] = $param['state'];
        }
        if(getAdminAuthId(is_admin_login())!=1){
            $where['tid'] = is_admin_login();
        }
        if (isset($param['mobile'])) {
            $where['uid'] = getUserIdBuyTel($param['mobile']);
        }
        $list=model('order')->where($where)->order('id desc')->paginate(config('page_number'), false, ['query' => $param]);
        return $this->fetch('courseOrder',['list'=>$list]);
    }
    /**
     * 导出订单
     */
    function exportorder(){
        $data = collection(model('order')->field('id,uid,tid,cid,ctype,orderid,paytype,state,total,addtime')->order('id asc')->select())->toArray();
        foreach ($data as $k=> $value) {
            $data[$k]['tid']="\t".getUserTel($data[$k]['uid'])."\t";
            $data[$k]['uid']=getUserName($data[$k]['uid']);
            $data[$k]['cid']=getCourseName($data[$k]['cid'],$data[$k]['ctype']);
            $data[$k]['ctype']=course_type($data[$k]['ctype']);
            $data[$k]['paytype']=getPayMethod($data[$k]['paytype']);
            $data[$k]['orderid']="\t". $data[$k]['orderid']."\t";
            $data[$k]['state']=getOrderSatatus($data[$k]['state']);
        }
        array_unshift($data, ['ID', '用户名','用户电话','课程', '课程类型','支付订单','支付方式','支付状态','支付金额','支付时间']);
        $res=export_excel($data, '订单列表');
        echo json_encode($res);
    }
    /**
     * 删除课程订单
     */
    public function delCourseOrder(){
        if ($this->request->isPost()) {
            if ($this->delete('order', $this->request->param()) === true) {
                $this->success('删除成功');
            } else {
                $this->error($this->errorMsg);
            }
        }
    }
    /**
     * 后台展示课程评论
     */
    public function commentList()
    {
        $param = $this->request->param();
        $comment = model('comment')->where(['cid' => $param['cid'], 'cstype' => $param['type']])->order('addtime desc')->paginate(10);
        return $this->fetch('commentList', ['list' => $comment,'cid'=>$param['cid'],'cstype'=>$param['type']]);
    }
    /**
     * 为课程添加评论马甲
     */
    function addvirtualcomment(){
        if ($this->request->isPost()) {
            $param = $this->request->param();
            foreach ($param['ids'] as $k => $value) {
                $data=db('virtualcomment')->where(['id'=>$value])->find();
                $start = strtotime(date('Y-m-d H:i:s', strtotime('-7 days')));
                $end =  strtotime(date("Y-m-d H:i:s",time()));
                $timestamp = rand($start, $end);
                $time = date("Y-m-d H:i:s", $timestamp);
                $comment=['cid'=>$param['cid'],'sid'=>0,'uid'=>$data['uid'],'cstype'=>$param['cstype'],'contents'=>$data['comment'],'addtime'=>$time];
                db('comment')->insert($comment);
            }
            $this->success('添加成功');
        }
        $param = $this->request->param();
        $comment=db('virtualcomment')->order('id desc')->paginate(12);
        return $this->fetch('addvirtualcomment', ['list' => $comment,'cid'=>$param['cid'],'cstype'=>$param['cstype']]);
    }
    /**
     * 删除课程评论
     */
    public function commentsDel(){
        if ($this->request->isPost()) {
            if ($this->delete('comment', $this->request->param()) === true) {
                $this->success('删除成功');
            } else {
                $this->error($this->errorMsg);
            }
        }
    }
    /**
     * 回复课程评论
     */
    public function commentsRe()
    {
        if ($this->request->isPost()) {
            $param=$this->request->param();
            $param['ruid']=is_admin_login();
            if (model('comment')->where(['id'=>$param['id']])->update($param)) {
                $this->success('回复成功');
            } else {
                $this->error($this->errorMsg);
            }
        }
        $param = $this->request->param();
        $comment=model('comment')->where(['id'=>$param['id']])->find();
        return $this->fetch('commentsRe', ['comment' => $comment]);
    }
    /**
     * 检测权限
     */
    public function checkCurseAuth($cid,$type){
        if($type==1){
            $courseInfo=model('videoCourse')->where(['id'=>$cid])->find();
        }
        if($type==2){
            $courseInfo=model('liveCourse')->where(['id'=>$cid])->find();
        }
        !($courseInfo['teacher_id']==is_admin_login() || getAdminAuthId(is_admin_login())==1) && $this->error('您没有权限，请不要非法操作');
    }
    function getSign($params, $partner_key) {
        ksort($params);
        $str = '';
        foreach ($params as $k => $val) {
            $str.= "{$k}={$val}&";
        }
        $str.="partner_key=".$partner_key;
        $sign = md5($str);
        return $sign;
    }
    function progress(){
        $param=$this->request->param();
        $userList=model('userCourse')->where(['cid'=>$param['cid'],'type'=>$param['cstype'],'state'=>1])->select();
        foreach ($userList as $key => $value) {
            $data[]=['value'=>$value['uid'],'title'=>getUserName($value['uid']),'disabled'=>'','checked'=>''];
        }
        return $this->fetch('progress',['user'=>json_encode($data),'cid'=>$param['cid'],'type'=>$param['cstype']]);
    }
    function progressPost(){
        $param=$this->request->param();
        $userList=$param['user'];
        if(empty($param['starttime']) || empty($param['endtime'])){
            $res=['code'=>1,'msg'=>'请填写时间'];
            echo json_encode($res);
        }else{
            if($param['type']==1){
                $sectionlist=model('videoSection')->where(['csid'=>$param['cid'],'ischapter'=>0])->select();
            }
            if($param['type']==2){
                $sectionlist=model('liveSection')->where(['csid'=>$param['cid'],'ischapter'=>0])->select();
            }
            foreach ($sectionlist as $k => $v) {
                foreach ($userList as $kk => $vv) {
                    if(!db('learned')->where(['cid'=>$param['cid'],'sid'=>$v['id'],'type'=>$param['type'],'uid'=>$vv['value'],'status'=>1])->find()){
                        $parsed = date_parse($v['playtimes']);
                        $duration=$parsed['hour'] * 3600 +  $parsed['minute'] * 60 +  $parsed['second'];
                        $timestamp = rand(strtotime($param['starttime']), strtotime($param['endtime']));
                        $data['uid']=$vv['value'];
                        $data['cid']=$param['cid'];
                        $data['sid']=$v['id'];
                        $data['type']=$param['type'];
                        $data['duration']=$duration>0?$duration:rand($param['duration_min']*60, $param['duration_max']*60);
                        $data['seek']=0;
                        $data['laststudy']=0;
                        $data['status']=1;
                        $data['addtime']=date('Y-m-d H:i:s',$timestamp);
                        db('learned')->insert($data);
                    }
                }
            }
            $res=['code'=>0,'msg'=>'添加成功'];
            echo json_encode($res);
        }
    }
}