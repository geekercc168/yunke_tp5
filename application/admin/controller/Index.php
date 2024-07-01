<?php

namespace app\admin\controller;

use app\common\controller\AdminBase;

class Index extends AdminBase
{
    protected $noLogin = ['login', 'captcha'];
    protected $noAuth = ['index', 'icon', 'logout'];
    protected function _initialize()
    {
        parent::_initialize();
    }
 
    public function index()
    {
        // 快捷导航
        $where = ['index' => 1, 'status' => 1];
        if (session('admin_auth.username') != config('administrator')) {
            $access      = model('authGroupAccess')->with('authGroup')
                ->where('uid', session('admin_auth.admin_id'))->find();
            $where['id'] = ['in', $access['rules']];
        }
        $index = model('authRule')->where($where)->order('pid asc,sort_order asc')->select();
        // 服务器信息
        $server = [
            'os'                  => PHP_OS, // 服务器操作系统
            'sapi'                => PHP_SAPI, // 服务器软件
            'version'             => PHP_VERSION, // PHP版本
            'mysql'               => db()->query('select VERSION() as version'), // mysql 版本
            'root'                => $_SERVER['DOCUMENT_ROOT'], // 当前运行脚本所在的文档根目录
            'max_execution_time'  => ini_get('max_execution_time') . 's', // 最大执行时间
            'upload_max_filesize' => ini_get('upload_max_filesize'), // 文件上传限制
            'memory_limit'        => ini_get('memory_limit'), // 允许内存大小
            'date'                => date('Y-m-d H:i:s', time()), // 服务器时间
        ];
        $teacherInfo=model('admin')->where('id',is_admin_login())->find();
        $info['domain']=get_domain();
        $info['authorcode']=config('author_code');
        $info['server']=config('author_web');
        $url=$info['server']."/educloud/educloud/getAuthoInfo";
        $authoInfo=json_decode(post_curl($url,$info),true);
        $authoInfo['yu']=get_autho_last_time(strtotime($authoInfo['endtime']));
        $authoInfo['server']=$info['server'];
        $lastVersion=json_decode(post_curl($info['server'].'/api/update/getlastversion',$info),true);
        $tourl=$info['server'].'autologin/domain/'.passport_encrypt(get_domain(),'newlogo').'/code/'.passport_encrypt(config('author_code'),'newlogo');
        return $this->fetch('index', ['index' => $index, 'server' => $server,
            'teacherInfo'=>$teacherInfo,
            'url'=>$tourl,
            'authoInfo'=>$authoInfo,
            'version'=>config('version'),
            'lastVersion'=>$lastVersion,
            'tixianList'=>db('tixian')->order('id desc')->where(['tid'=>is_admin_login()])->paginate(9),
            'profit'=>db('profit')->where(['tid'=>is_admin_login()])->value('profit')]);
    }

    public function login()
    {
        is_admin_login() && $this->redirect('admin/index/index'); // 登录直接跳转
        if ($this->request->isPost()) {
            $param = $this->request->param();
            $result = $this->validate($param, 'login');
            if ($result !== true) {
                $this->error($result);
            }
            $admin = model('admin')->where([
                'username' => $param['username'],
                'password' => md5($param['password'])
            ])->find();
            if ($admin) {
                $admin['status'] != 1 && $this->error('账号已禁用');
                // 保存状态
                $auth = [
                    'admin_id' => $admin['id'],
                    'username' => $admin['username'],
                ];
                session('admin_auth', $auth);
                session('admin_auth_sign', data_auth_sign($auth));
                // 更新信息
                model('admin')->save([
                    'last_login_time' => time(),
                    'last_login_ip'   => $this->request->ip(),
                    'login_count'     => $admin['login_count'] + 1,
                ], ['id' => $admin['id']]);
                $teacherInfo=model('user')->where('admin_id',$admin['id'])->find();
                if(!empty($teacherInfo)){
                    $auth = [
                        'user_id' => $teacherInfo['id'],
                        'username' => $teacherInfo['username'],
                    ];
                    session('user_auth', $auth);
                    session('user_auth_sign', data_auth_sign($auth));
                }
                insert_admin_log('登录了后台系统');
                $this->success('登录成功', url('admin/index/index'));
            } else {
                $this->error('账号或密码错误');
            }
        }
        return $this->fetch('login');
    }

    public function captcha()
    {
        $config = [
            // 验证码字符集合
            'codeSet'  => '2345678abcdefhijkmnpqrstuvwxyzABCDEFGHJKLMNPQRTUVWXY',
            // 验证码字体大小(px)
            'fontSize' => 16,
            // 是否画混淆曲线
            'useCurve' => false,
            // 验证码图片高度
            'imageH'   => 42,
            // 验证码图片宽度
            'imageW'   => 135,
            // 验证码位数
            'length'   => 4,
            // 验证成功后是否重置
            'reset'    => true,
        ];
        return captcha('', $config);
    }
    public function importExcel()
    {
        try {
            $file = request()->file('excel');
            $info = $file->validate(['size'=>3145728,'ext'=>'xlsx,xls,csv'])->move(ROOT_PATH . 'public' . DS . 'upload' . DS . 'excels');
            if($info){
                $excel_path = ROOT_PATH.'public'.DS.'upload'.DS."excels".DS.$info->getSaveName();
            }else{
                $excel_path = null;
            }
            ini_set('memory_limit', '1024M');//设置php允许的文件大小最大值
            header("Content-type:text/html;charset=utf-8");
            //接收文件
            $file = $_FILES['excel'];
            $extension = strtolower( pathinfo($file['name'], PATHINFO_EXTENSION) );
            //实例化主文件
            $phpExcel =new PHPExcel();
            //创建读入器
            if($extension=='xlsx'){
                $objRender = $phpExcel->createReader('excel2007');
            }else{
                $objRender = $phpExcel->createReader('Excel5');
            }
            $ExcelObj = $objRender->load($file['tmp_name']);
            $sheetContent = $ExcelObj->getSheet(0)->toArray();
            unset($sheetContent[0]);
            foreach ($sheetContent as $k => $v){
                $arr['uname'] = $v[0];
                $arr['sex'] = $v[1];
                $arr['age'] = $v[2];
                $arr['class_name'] = $v[3];
                $res[] = $arr;
            }
            if(Db::name('excel_upload')->insertAll($res)){
                $this->success("导入成功");
            }else{
                $this->error("导入失败");
            }
        } catch (\Exception $e) {
            return ['code' => 0, 'msg' => $e->getMessage()];
        }
    }
    public function icon()
    {
        return $this->fetch();
    }

    // 修改密码
    public function editPassword()
    {
        if ($this->request->isPost()) {
            $param = $this->request->param();
            // 验证条件
            empty($param['password']) && $this->error('请输入旧密码');
            empty($param['new_password']) && $this->error('请输入新密码');
            empty($param['rep_password']) && $this->error('请输入确认密码');
            !check_password($param['new_password'], 6, 16) && $this->error('请输入6-16位的密码');
            $param['new_password'] != $param['rep_password'] && $this->error('两次密码不一致');
            $admin = model('admin')->where('id', session('admin_auth.admin_id'))->find();
            $admin['password'] != md5($param['password']) && $this->error('旧密码错误');
            $data = ['id' => session('admin_auth.admin_id'), 'password' => $param['new_password']];
            if ($this->update('admin', $data, false) === true) {
                insert_admin_log('修改了登录密码');
                $this->success('更新成功', url('admin/index/index'));
            } else {
                $this->error($this->errorMsg);
            }
        }
        return $this->fetch('editPassword');
    }

    // 退出登录
    public function logout()
    {
        insert_admin_log('退出了后台系统');
        session('admin_auth', null);
        session('admin_auth_sign', null);
        $this->redirect('admin/index/login');
    }

    // 清除缓存
    public function clear()
    {
        clear_cache();
        insert_admin_log('清除了系统缓存');
        $this->success('清除成功');
    }
    /**
     * 教师资料修改
     */
    function teacherInfoSave(){
        if ($this->request->isPost()) {
            $param = $this->request->param();
            if ($this->update('admin', $param, input('_verify', false)) === true) {
                $this->success('修改成功', url('admin/index/index'));
            } else {
                $this->error($this->errorMsg);
            }
        }
    }
}
