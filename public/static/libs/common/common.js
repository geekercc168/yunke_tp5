var layer = layui.layer,
    form = layui.form,
    element = layui.element,
    laydate = layui.laydate,
    upload = layui.upload,
    laypage = layui.laypage;
var countdown=60;
// 通用提交
form.on('submit(*)', function (data) {
    var index = layer.msg('提交中，请稍候', {
        icon: 16,
        time: false,
        shade: 0.3
    });
    $(data.elem).attr('disabled', true);
    $.ajax({
        url: data.form.action,
        type: data.form.method,
        dataType: 'json',
        data: $(data.form).serialize(),
        success: function (result) {
            if (result.code === 1 && result.url != '') {
                setTimeout(function () {
                    location.href = result.url;
                }, 1000);
            } else {
                $(data.elem).attr('disabled', false);
            }
            layer.close(index);
            layer.msg(result.msg);
        },
        error: function (xhr, state, errorThrown) {
            layer.close(index);
            layer.msg(state + '：' + errorThrown);
        }
    });
    return false;
});
// 用户注册验证码出错刷新
form.on('submit(login)', function(data) {
    var index = layer.msg('登录中，请稍候', {
        icon: 16,
        time: false,
        shade: 0.3
    });
    $.ajax({
        url: data.form.action,
        type: data.form.method,
        dataType: 'json',
        data: $(data.form).serialize(),
        success: function(result) {
            if (result.code === 1) {
                location.href = result.url;
            } else {
                $('.captcha img').attr('src', '/admin/index/captcha.html');
                $(".captchaInput").val("");
                layer.close(index);
                layer.msg(result.msg);
            }
        },
        error: function (xhr, state, errorThrown) {
            layer.close(index);
            layer.msg(state + '：' + errorThrown);
        }
    });
    return false;
});
// 父窗口通用提交
form.on('submit(i)', function (data) {
    var index = layer.msg('提交中，请稍候', {
        icon: 16,
        time: false,
        shade: 0.3
    });
    $.ajax({
        url: data.form.action,
        type: data.form.method,
        dataType: 'json',
        data: $(data.form).serialize(),
        success: function (result) {
            if (result.code === 1) {
                setTimeout(function () {
                   parent.location.reload();
                }, 1000);
            }
            layer.close(index);
            layer.msg(result.msg);
        },
        error: function (xhr, state, errorThrown) {
            layer.close(index);
            layer.msg(state + '：' + errorThrown);
        }
    });
    return false;
});
// 带确认信息的提交
form.on('submit(c)', function (data) {
    var trip='KeyID和KeySecret若修改的话，请选中重建视频目录，其他修改不必选中！';
    layer.confirm( trip, {
        icon: 3,
        title: '提示'
    }, function (index) {
        var index = layer.msg('提交中，请稍候', {
            icon: 16,
            time: false,
            shade: 0.3
        });
        $.ajax({
            url: data.form.action,
            type: data.form.method,
            dataType: 'json',
            data: $(data.form).serialize(),
            success: function (result) {
                if (result.code === 1 && result.url != '') {
                    setTimeout(function () {
                        location.href = result.url;
                    }, 1000);
                } else {
                    $(data.elem).attr('disabled', false);
                }
                layer.close(index);
                layer.msg(result.msg);
            },
            error: function (xhr, state, errorThrown) {
                layer.close(index);
                layer.msg(state + '：' + errorThrown);
            }
        });
    });
    return false;
});
// 通用开关
form.on('switch(*)', function (data) {
    var index = layer.msg('修改中，请稍候', {
        icon: 16,
        time: false,
        shade: 0.3
    });
    // 参数
    var obj = {};
    obj[$(this).attr('name')] = this.checked == true ? 1 : 0;
    obj['_verify'] = 0;
    $.ajax({
        url: $(this).data('url'),
        type: 'post',
        dataType: 'json',
        data: obj,
        success: function (result) {
            layer.close(index);
            layer.msg(result.msg);
        },
        error: function (xhr, state, errorThrown) {
            layer.close(index);
            layer.msg(state + '：' + errorThrown);
        }
    });
});
// 通用全选
form.on('checkbox(*)', function (data) {
    $('.layui-table tbody input[lay-skin="primary"]').each(function (index, item) {
        item.checked = data.elem.checked;
    });
    form.render('checkbox');
});

// 通用提交
$('.ajax-submit').on('click', function () {
    var than = $(this);
    var form = $(this).parents('form');
    var index = layer.msg('提交中，请稍候', {
        icon: 16,
        time: false,
        shade: 0.3
    });
    than.attr('disabled', true);
    $.ajax({
        url: form.attr('action'),
        type: form.attr('method'),
        dataType: 'json',
        data: $(data.form).serialize(),
        success: function (result) {
            if (result.code === 1 && result.url != '') {
                setTimeout(function () {
                    location.href = result.url;
                }, 1000);
            } else {
                than.attr('disabled', false);
            }
            layer.close(index);
            layer.msg(result.msg);
        },
        error: function (xhr, state, errorThrown) {
            layer.close(index);
            layer.msg(state + '：' + errorThrown);
        }
    });
    return false;
});
// 通用异步
$('.ajax-action').on('click', function () {
    var url = $(this).attr('href');
    var index = layer.msg('请求中，请稍候', {
        icon: 16,
        time: false,
        shade: 0.3
    });
    $.ajax({
        url: url,
        type: 'post',
        dataType: 'json',
        success: function (result) {
            if (result.code === 1 && result.url != '') {
                setTimeout(function () {
                    location.href = result.url;
                }, 1000);
            }
            layer.close(index);
            layer.msg(result.msg);
        },
        error: function (xhr, state, errorThrown) {
            layer.close(index);
            layer.msg(state + '：' + errorThrown);
        }
    });
    return false;
});
// 向父窗口插入数据并提交到数据库
$('.ajax-insert').on('click', function () {
    var url = $(this).attr('href');
    var index = layer.msg('请求中，请稍候', {
        icon: 16,
        time: false,
        shade: 0.3
    });
    $.ajax({
        url: url,
        type: 'post',
        dataType: 'json',
        success: function (result) {
            if (result.code === 1 && result.url != '') {
                setTimeout(function () {
                    parent.location.reload();
                }, 1000);
            }
            layer.msg(result.msg);
            layer.close(index);
        },
        error: function (xhr, state, errorThrown) {
            layer.close(index);
            layer.msg(state + '：' + errorThrown);
        }
    });
    return false;
});
// 通用更新
$('.ajax-update').on('blur', function () {
    var obj = {};
    var index = layer.msg('请求中，请稍候', {
        icon: 16,
        time: false,
        shade: 0.3
    });
    obj[$(this).attr('name')] = $(this).val();
    obj['_verify'] = 0;
    $.ajax({
        url: $(this).data('url'),
        type: 'post',
        dataType: 'json',
        data: obj,
        success: function (result) {
            if (result.code === 1) {
                layer.msg(result.msg);
                setTimeout(function () {
                    location.reload();
                }, 1000);
            }
        },
        error: function (xhr, state, errorThrown) {
            layer.close(index);
            layer.msg(state + '：' + errorThrown);
        }
    });
    return false;
});
// 通用删除
$('.ajax-delete').on('click', function () {
    var url = $(this).attr('href');
    layer.confirm('确定删除？', {
        icon: 3,
        title: '提示'
    }, function (index) {
        var index = layer.msg('删除中，请稍候', {
            icon: 16,
            time: false,
            shade: 0.3
        });
        $.ajax({
            url: url,
            type: 'post',
            dataType: 'json',
            success: function (result) {
                if (result.code === 1 && result.url != '') {
                    setTimeout(function () {
                        location.href = result.url;
                    }, 1000);
                }
                layer.close(index);
                layer.msg(result.msg);
            },
            error: function (xhr, state, errorThrown) {
                layer.close(index);
                layer.msg(state + '：' + errorThrown);
            }
        });
    });
    return false;
});
// 通用详情
$('.ajax-detail').on('click', function () {
    var title = $(this).html();
    var url = $(this).attr('href');
    var index = layer.open({
        title: title,
        type: 2,
        content: url,
        success: function (layero, index) {
            setTimeout(function () {
                layer.tips('点击此处返回', '.layui-layer-setwin .layui-layer-close', {
                    tips: 3
                });
            }, 500)
        }
    })
    layer.full(index);
    return false;
});
// 通用窗口
$('.ajax-iframe').on('click', function() {
    var title = $(this).html();
    var url = $(this).attr('href');
    var width = $(this).data('width');
    var height = $(this).data('height');
    var index = layer.open({
        title: title,
        type: 2,
        area: [width, height],
        content: url,
        shade:0.4,
        maxmin: true
    })
    return false;
});

// 通用窗口无叉号
$('.ajax-iframe_noshut').on('click', function() {
    var title = $(this).html();
    var url = $(this).attr('href');
    var width = $(this).data('width');
    var height = $(this).data('height');
    var index = layer.open({
        title: title,
        type: 2,
        area: [width, height],
        content: url,
        closeBtn: 0,
        shade:0.4,
        btn: ['确定'],
        yes: function(index, layero){
            layer.close(index);
        }
    })
    return false;
});
// 通用窗口无叉号无确定
$('.ajax-iframe_noshut_nobutton').on('click', function() {
    var title = $(this).html();
    var url = $(this).attr('href');
    var width = $(this).data('width');
    var height = $(this).data('height');
    var index = layer.open({
        title: title,
        type: 2,
        area: [width, height],
        content: url,
        closeBtn: 0,
        shade:0.4
    })
    return false;
});
// 通用搜索
$('.ajax-search').on('click', function () {
    var form = $(this).parents('form');
    var url = form.attr('action');
    var query = form.serialize();
    query = query.replace(/(&|^)(\w*?\d*?\-*?_*?)*?=?((?=&)|(?=$))/g, '');
    query = query.replace(/^&/g, '');
    if (url.indexOf('?') > 0) {
        url += '&' + query;
    } else {
        url += '?' + query;
    }
    location.href = url;
    return false;
});
// 通用批量
$('.ajax-batch').on('click', function () {
    var url = $(this).attr('href');
    var val = [];
    $('.layui-table tbody input[lay-skin="primary"]:checked').each(function (i) {
        val[i] = $(this).val();
    });
    if (val === undefined || val.length == 0) {
        layer.msg('请选择数据');
        return false;
    }
    var index = layer.msg('请求中，请稍候', {
        icon: 16,
        time: false,
        shade: 0.3
    });
    // 参数
    var obj = {};
    obj[$('.layui-table tbody input[lay-skin="primary"]:checked').attr('name')] = val;
    obj['_verify'] = 0;
    $.ajax({
        url: url,
        type: 'post',
        dataType: 'json',
        data: obj,
        success: function (result) {
            if (result.code === 1 && result.url != '') {
                setTimeout(function () {
                    location.reload();
                }, 1000);
            }
            layer.close(index);
            layer.msg(result.msg);
        },
        error: function (xhr, state, errorThrown) {
            layer.close(index);
            layer.msg(state + '：' + errorThrown);
        }
    });
    return false;
});
// 添加图标
$('.ajax-icon').on('click', function () {
    var url = $(this).attr('href');
    var index = layer.open({
        title: '选择图标',
        type: 2,
        area: ['100%', '100%'],
        content: url
    });
    return false;
});
// 导出数据
$('.ajax-export').on('click', function () {
    var loading = layer.load(1, {
        shade: [0.3,'#000'] //0.1透明度的白色背景
    });
    var url = $(this).attr('url');
    $.ajax({
        url: url,
        type: 'post',
        dataType: 'json',
        data: [],
        success: function (result) {
            if(result.code==0){
                setTimeout(function () {
                    layer.close(loading)
                    window.location.href = '/'+result.url
                }, 1000);
            }
        },
        error: function (xhr, state, errorThrown) {
            layer.close(loading)
            layer.msg(state + '：' + errorThrown);
        }
    });
});
// 通用上传
upload.render({
    elem: '.ajax-images',
    url: '/api/uploader/uploadImage',
    done: function (result) {
        // 上传完毕回调
        if (result.code === 1) {
            this.item.prev('input').val(result.url);
        } else {
            layer.msg(result.msg);
        }
    }
});
upload.render({
    elem: '.ajax-file',
    url: '/api/uploader/uploadFile',
    accept: 'file', // 普通文件
    done: function (result) {
        // 上传完毕回调
        if (result.code === 1) {
            this.item.prev('input').val(result.url);
        } else {
            layer.msg(result.msg);
        }
    }
});
upload.render({
    elem: '.ajax-video',
    url: '/api/uploader/uploadVideo',
    accept: 'video', // 视频文件
    done: function (result) {
        // 上传完毕回调
        if (result.code === 1) {
            this.item.prev('input').val(result.url);
        } else {
            layer.msg(result.msg);
        }
    }
});
// 导入学员到班级
upload.render({
    elem: '.ajax-import',
    url: '/admin/Classroom/importExcel',
    accept: 'file', // 普通文件
    data: {
        classroomId:function(){
            return $('#classroomId').val();
        }},
    before: function(obj){
        layer.load();
    },
    done: function (result , index, upload) {
        // 上传完毕回调
        if (result.code === 1) {
            layer.closeAll('loading');
            setTimeout(function () {
                window.parent.location.reload();
                layer.close(layer.index);
            }, 1000);
            layer.msg(result.msg);
        } else {
            layer.msg(result.msg);
        }
    }
});
// 批量导入学员
upload.render({
    elem: '.ajax-import-user',
    url: '/admin/user/importExcel',
    accept: 'file', // 普通文件
    before: function(obj){
        layer.load();
    },
    done: function (result , index, upload) {
        // 上传完毕回调
        if (result.code === 1) {
            layer.closeAll('loading');
            setTimeout(function () {
                window.parent.location.reload();
                layer.close(layer.index);
            }, 1000);
            layer.msg(result.msg);
        } else {
            layer.msg(result.msg);
        }
    }
});
// 批量导入教师
upload.render({
    elem: '.ajax-import-teacher',
    url: '/admin/user/teacherimportExcel',
    accept: 'file', // 普通文件
    before: function(obj){
        layer.load();
    },
    done: function (result , index, upload) {
        // 上传完毕回调
        if (result.code === 1) {
            layer.closeAll('loading');
            setTimeout(function () {
                window.parent.location.reload();
                layer.close(layer.index);
            }, 1000);
            layer.msg(result.msg);
        } else {
            layer.msg(result.msg);
        }
    }
});
// 批量导入学员到课程
upload.render({
    elem: '.ajax-import-studends',
    url: '/admin/course/importStudentsExcel',
    accept: 'file', // 普通文件
    before: function(obj){
        layer.load();
    },
    done: function (result , index, upload) {
        // 上传完毕回调
        if (result.code === 1) {
            layer.closeAll('loading');
            setTimeout(function () {
                window.parent.location.reload();
                layer.close(layer.index);
            }, 1000);
            layer.msg(result.msg);
        } else {
            layer.msg(result.msg);
        }
    }
});
// 批量导入试题
upload.render({
    elem: '.ajax-import-questions',
    url: '/admin/exam/importExcel',
    accept: 'file', // 普通文件
    before: function(obj){
        layer.load();
        this.data={'questionchapterid':$('#chapterid').val(),'questionknowsid':$('#knowledge').val()};
    },
    done: function (result , index, upload) {
        // 上传完毕回调
        if (result.code === 1) {
            layer.closeAll('loading');
            setTimeout(function () {
                window.parent.location.reload();
                layer.close(layer.index);
            }, 1000);
            layer.msg(result.msg);
        } else {
            setTimeout(function () {
                window.location.reload();
                layer.close(layer.index);
            }, 1000);
            layer.msg(result.msg);
        }
    }
});
// 上传头像
upload.render({
    elem: '.ajax-avatar',
    url: '/index/user/upavatar',
    done: function (result) {
        // 上传完毕回调
        if (result.code === 1) {
            this.item.prev('input').val(result.url);
        } else {
            layer.msg(result.msg);
        }
    }
});
// 阿里云oss上传文件,完成后刷新本页
upload.render({
    elem: '.ajax-oss',
    url: '/api/uploader/ossupload',
    accept: 'file',
    before: function(obj){
        this.data={'type':'file'};
    },
    done: function (result) {
        if (result.code === 1) {
            layer.msg("上传成功");
            location.reload();
        } else {
            layer.msg(result.msg);
        }
    }
});
// 阿里云oss上传图片
upload.render({
    elem: '.ajax-oss-image',
    url: '/api/uploader/ossupload',
    accept: 'file',
    before: function(obj){
        this.data={'type':'image'};
    },
    done: function (result) {
        if (result.code === 1) {
            this.item.prev('input').val(result.url);
        } else {
            layer.msg(result.msg);
        }
    }
});

// 通用相册
upload.render({
    elem: '.ajax-photos',
    url: '/api/uploader/uploadImage',
    multiple: true,
    done: function (result) {
        // 上传完毕回调
        if (result.code === 1) {
            var html = '<div class="layui-form-item"><label class="layui-form-label"></label><div class="layui-input-block"><input type="text" name="photo[]" value="' + result.url + '" autocomplete="off" readonly class="layui-input"><button type="button" class="layui-btn layui-btn-primary layui-btn-position delete-photo"><i class="fa fa-times-circle"></i></button></div></div>';
            this.item.parents('.layui-form-item').after(html);
        } else {
            layer.msg(result.msg);
        }
    }
});
// 删除相册
$('.layui-form').delegate('.delete-photo', 'click', function () {
    $(this).parents('.layui-form-item').remove();
});
// 选择图标
$('.icon-library .fa').on('click', function () {
    $('input[name=icon]', window.parent.document).val($(this).attr('class'));
    parent.layer.closeAll();
});
form.verify({
    username: function (value, item) {
        if (!new RegExp("^[a-zA-Z0-9_\u4e00-\u9fa5\\s·]+$").test(value)) {
            return '用户名不能有特殊字符';
        }
        if (/(^\_)|(\__)|(\_+$)/.test(value)) {
            return '用户名首尾不能出现下划线\'_\'';
        }
        if (/^\d+\d+\d$/.test(value)) {
            return '用户名不能全为数字';
        }
    },
    Ndouble:[
        /^[1-9]\d*$/
        ,'只能输入整数哦'
    ],
    password: [
        /^[\S]{6,12}$/
        , '密码必须6到12位，且不能出现空格'
    ],
    payment : function(value) {
        if (value.length < 1) {
            return '请选择支付方式';
        }
    },
    http: function(value){
        if (value.indexOf("http") != -1 ) {
            return '私有域名中不能含有https://';
        }
    },
    content: function(value) {
        return layedit.sync(index);
    },
    img: function(value){
        var fileArr = value.split("\\");
        var fileTArr = fileArr[fileArr.length - 1].toLowerCase().split(".");
        var filetype = fileTArr[fileTArr.length - 1];
        if (filetype == "png" || filetype == "ico" || filetype == "jpg" || filetype === "bmp" || filetype == "jpg" || filetype == "tiff" || filetype == "gif"||filetype =="svg" ||filetype =="psd")
        {

        } else {
            return '必须是图片格式';
        }
    },
    bigMaxLength: function (value) {
        if(value.length > 5000){
            return '超出最大文本大小限制';
        }
    },
    smallMaxLength: function (value) {
        if(value.length > 1000){
            return '超出最大文本大小限制';
        }
    }
});
// 向试卷中添加试题
form.on('checkbox(exam)', function (data) {
    var id=data.value;
    var typeId= data.elem.getAttribute("typeid");
    var selected=$('#iselectquestions_'+typeId, window.parent.document);
    var selectnum=$('#ialreadyselectnumber_'+typeId,window.parent.document);
    var selectnumInput=$('#iselectallnumber_'+typeId,window.parent.document);
    if(selected.val() == '') {selected.val(',')}
    if(data.elem.checked){
        if(selected.val().indexOf(','+id+',') < 0){
            selected.val(selected.val()+id+',');
            var num= parseInt(selectnum.html())+parseInt(1);
            selectnum.html(num);
            selectnumInput.val(num);
        }
    }else{
        var t = eval('/,'+id+',/');
        if(selected.val().indexOf(','+id+',') >= 0){
            selected.val(selected.val().replace(t,','));
            var num= parseInt(selectnum.html())-parseInt(1);
            selectnum.html(num);
            selectnumInput.val(num);
        }
    }
});
//选择支付方式
$('.payment #alipay').on('click', function () {
    $(this).addClass("active");
    $('#payment').val('alipay');
    $('.payment #wxpay').removeClass("active");
    $('.payment #yuepay').removeClass("active");
});
$('.payment #wxpay').on('click', function () {
    $(this).addClass("active");
    $('#payment').val('wxpay');
    $('.payment #alipay').removeClass("active");
    $('.payment #yuepay').removeClass("active");
});
$('.payment #yuepay').on('click', function () {
    $(this).addClass("active");
    $('#payment').val('yuepay');
    $('.payment #wxpay').removeClass("active");
    $('.payment #alipay').removeClass("active");
});
// 支付提交
form.on('submit(f)', function (data) {
    var index = layer.msg('提交中，请稍候', {
        icon: 16,
        time: false,
        shade: 0.3
    });
    return true;
});
// 视频播放页面右侧栏
$("#aside-slide").on("click", function() {
    var width="0px";
    var paddingRight="16px";
    if($(this).hasClass("l")){
        $(this).removeClass("l").addClass("s");
        $("#aside-slide i").html("〈");
    }else if($(this).hasClass("s")){
        $(this).removeClass("s").addClass("l");
        width="338px";
        paddingRight="338px";
        $("#aside-slide i").html("〉");
    }
    $(".learn-right-box").animate({
        width: width
    }, {
        duration: 200
    });
    $(".learn-content").animate({
        paddingRight: paddingRight
    }, {
        duration: 200
    });
});
//检测是否购买课程
$('.check-learn a').on('click', function () {
    var sid =  $(this).attr('sid');
    var type = $(this).attr('type');
    var url = $(this).attr('url');
    var isstudy = $(this).attr('isstudy');
    var sectype = $(this).attr('sectype');
    if(sectype==4 && isstudy==1){
        return false;
    }else{
        $.ajax({
            url: url,
            type: 'post',
            dataType: 'json',
            data: {sid:sid,type:type},
            success: function (result) {
                if(result.code ==0){
                    layer.msg(result.msg);
                }
                if (result.code ==1) {
                    location.href = result.href;
                }
                if (result.code ==2) {
                    layer.msg(result.msg);
                }
                if (result.code ==3) {
                    location.href = result.href;
                }
            },
            error: function (xhr, state, errorThrown) {
                layer.msg(state + '：' + errorThrown);
            }
        });
    }
    return false;
});
//手动点击已经学完按钮
$('.studied').on('click' , function (){
    var sid=$(this).attr('sid');
    var cid=$(this).attr('cid');
    var type=$(this).attr('type');
    var url=$(this).attr('url');
    $.ajax({
        url:url,
        type:"post", data:{'sid':sid,'cid':cid,'type':type},
        dataType:'json',
        success:function(data){
            if(data.status==1){
                layer.msg('恭喜你，本节课已经学完！');
            }else if(data.status==2){
                layer.msg('请先登录系统！');
            }else{
                layer.msg('您好像点过了亲！');
            }
        }
    })

})
//获取笔记
function getNotes(cid,sid,type,geturl,id) {
    $.ajax({
        url : geturl,
        type : "post",
        dataType : "json",
        data : {"cid" : cid, "sid" : sid, "type": type,'id':id},
        async:false,
        success : function(result) {
            var htm='<li id="courseNote'+result.id+'">'+
                '	    <div class="notes-list-box">'+
                '		    <section class="mt10  mr10">'+
                '			    <p class="c-999  font-12">'+result.contents+'</p>'+
                '           </section>'+
                '           <section class="mt10 mr10 hui-fr">'+
                '               <span class="c-999  l-h-30 font-12 ">' +result.addtime+'</span>'+
                '               <span class="c-666">'+
                '                   <a class="layui-btn  btn-del layui-btn-sm fr delNotes" url="/index/course/delnotes.html" id="'+result.id+'"><i class="layui-icon">&#xe640;</i></a>'+
                '               </span>'+
                '           </section>'+
                '       </div>'+
                '     </li>';
            $("#noteslist").prepend(htm);
        }
    });
}
//添加笔记
$('.addNotes').on('click' , function (){
    var quesContent=$('#quesContent').val();
    var cid=$(this).attr('cid');
    var sid=$(this).attr('sid');
    var type=$(this).attr('type');
    var url=$(this).attr('url');
    var geturl=$(this).attr('geturl');
    if(quesContent==''){
        layer.msg("笔记内容不能为空！");
    }else{
        $.ajax({
            url:url,
            type:"post", data:{'sid':sid,'cid':cid,'cstype':type,'contents':quesContent},
            dataType:'json',
            success:function(data){
                if(data.code==1){
                    layer.msg("笔记添加成功");
                    $('#quesContent').val('');
                    var id=data.id;
                    getNotes(cid,sid,type,geturl,id);
                }else{
                    layer.msg(data.msg);
                }
            }
        })
    }
})
//删除笔记
$('.delNotes').on('click' , function (){
    var id=$(this).attr('id');
    var url=$(this).attr('url');
    $.ajax({
        url : url,
        type : "post",
        dataType : "json",
        data : {'id' : id},
        async:false,
        success : function(data) {
            if(data.code==1){
                layer.msg(data.msg);
                $("#courseNote"+id).remove();
            }else{
                layer.msg(data.msg);
            }
        }
    });
})
//获取课程评论
function getComment(cid,sid,type,geturl,id) {
    $.ajax({
        url : geturl,
        type : "post",
        dataType : "json",
        data : {"cid" : cid, "sid" : sid, "type": type,'id':id},
        async:false,
        success : function(result) {
            var htm='<li id="courseNote'+result.id+'">'+
                '	    <div class="notes-list-box">'+
                '		    <section class="mt10  mr10">'+
                '			    <p class="c-999  font-12">'+result.contents+'</p>'+
                '           </section>'+
                '           <section class="mt10 mr10 hui-fr">'+
                '               <span class="c-999  l-h-30 font-12 ">' +result.addtime+'</span>'+
                '               <span class="c-666">'+
                '                   <a class="layui-btn  btn-del layui-btn-sm fr delNotes" url="/index/course/delnotes.html" id="'+result.id+'"><i class="layui-icon">&#xe640;</i></a>'+
                '               </span>'+
                '           </section>'+
                '       </div>'+
                '     </li>';
            $("#commentlist").prepend(htm);
        }
    });
}
//添加课程评论
$('.addComment').on('click' , function (){
    var commentContent=$('#commentContent').val();
    var cid=$(this).attr('cid');
    var sid=$(this).attr('sid');
    var type=$(this).attr('type');
    var url=$(this).attr('url');
    var geturl=$(this).attr('geturl');
    if(commentContent==''){
        layer.msg("评论内容不能为空！");
    }else{
        $.ajax({
            url:'/index/course/addComment',
            type:"post", data:{'sid':sid,'cid':cid,'cstype':type,'contents':commentContent},
            dataType:'json',
            success:function(data){
                if(data.code==1){
                    layer.msg("评论添加成功");
                    $('#commentContent').val('');
                    var id=data.id;
                    getComment(cid,sid,type,geturl,id);
                }else{
                    layer.msg(data.msg);
                }
            }
        })
    }
})
//删除课程评论
$('.delComment').on('click' , function (){
    var id=$(this).attr('id');
    var url=$(this).attr('url');
    $.ajax({
        url : url,
        type : "post",
        dataType : "json",
        data : {'id' : id},
        async:false,
        success : function(data) {
            if(data.code==1){
                layer.msg(data.msg);
                $("#courseComment"+id).remove();
            }else{
                layer.msg(data.msg);
            }
        }
    });
})

//下载远程文件
$('.downfile a').on('click', function () {
    var cid=$(this).attr('cid');
    var type=$(this).attr('type');
    var mid=$(this).attr('mid');
    var url=$(this).attr('url');
    $.ajax({
        url: url,
        type: 'post',
        dataType: 'json',
        data: {cid:cid,type:type,mid:mid},
        success: function (result) {
            if (result.code ==1) {
                var $form = $('<form method="GET"></form>');
                $form.attr('action', result.downHref);
                $form.appendTo($('body'));
                $form.submit();
            }
            if(result.code ==0){
                layer.msg(result.msg);
            }
        },
        error: function (xhr, state, errorThrown) {
            layer.msg(state + '：' + errorThrown);
        }
    });
});
//试题解析按钮
$('.js-analysis a').on('click', function () {
    var id=$(this).attr('aid');
    if($(this).hasClass('zhankai')){
        $(".zhankai"+id).addClass("layui-hide");
        $(".shouqi"+id).removeClass("layui-hide");
        $(".analysis"+id).removeClass("layui-hide");
    }
    if($(this).hasClass('shouqi')){
        $(".zhankai"+id).removeClass("layui-hide");
        $(".shouqi"+id).addClass("layui-hide");
        $(".analysis"+id).addClass("layui-hide");
    }
})
//试卷批阅检测给的分数是否合理
$('.check-score').on('blur', function () {
    var score=$(this).val();
    var tscore=$(this).attr('score');
    if(parseInt(score)>parseInt(tscore)){
        layer.msg('给的分值大于本题最高分值')
    }
    return false;
});
// 添加试卷点击事件
$(".exam-list a").click(function() {
    $('input[name=paperid]', window.parent.document).val($(this).attr('eid'));
    $('input[name=title]', window.parent.document).val($(this).attr('ename'));
    var index = parent.layer.getFrameIndex(window.name);
    parent.layer.close(index);
})
// 视频课程选择视频的点击事件
$(".radio li").click(function() {
    $(".shadow").removeClass("selected");
    $(this).find('.shadow').addClass("selected");
    if($(this).find('input[name=localvideo]').val()){
        $('input[name=localvideo]', window.parent.document).val($(this).find('input[name=dir]').val() +'/'+ $(this).find('input[name=localvideo]').val());
        $('input[name=playtimes]', window.parent.document).val($(this).find('input[name=duration]').val());
        $('input[name=title]', window.parent.document).val($(this).find('input[name=name]').val());
    }else{
        $('input[name=videoid]', window.parent.document).val($(this).find('input[name=videoid]').val());
        $('input[name=playtimes]', window.parent.document).val($(this).find('input[name=duration]').val());
    }
    var index = parent.layer.getFrameIndex(window.name);
    parent.layer.close(index);
})
// 课程收藏
$('.favourite').on('click', function () {
    var cid=$(this).attr('cid');
    var url=$(this).attr('url');
    var type=$(this).attr('type');
    var status=$(this).attr('status');
    $.ajax({
        url : url,
        type : "post",
        dataType : "json",
        data : {'cid':cid,'type':type},
        async:false,
        success : function(data) {
            if(data.code==1){
                layer.msg(data.msg);
                $(".f").addClass("layui-hide");
                $(".unf").removeClass("layui-hide");
            }
            if(data.code==2){
                layer.msg(data.msg);
                $(".f").removeClass("layui-hide");
                $(".unf").addClass("layui-hide");
            }
            if(data.code==0){
                layer.msg(data.msg);
            }
        }
    });
})
// 发送注册手机验证码
$('.getPhoneCode').on('click', function () {
    var url=$(this).attr('url');
    var mobile=$("#mobile").val();
    if(mobile=='' || mobile==null){
        layer.msg('请输入手机号码');
        return false;
    }
    $.ajax({
        type: "post",
        url: url,
        data: {mobile:mobile},
        dataType: "json",
        async:false,
        success:function(data){
            if(data.Code=='OK'){
                layer.msg('发送成功');
                settime();
            }else{
                layer.msg(data.Message);
            }
        },
        error:function(err){
            console.log(err);
        }
    });
});
function settime() {
    var _generate_code = $(".generate_code");
    _generate_code.addClass('layui-btn-disabled');
    if (countdown == 0) {
        _generate_code.removeClass('layui-btn-disabled');
        _generate_code.val("获取验证码");
        countdown = 60;
        return false;
    } else {
        _generate_code.addClass('layui-btn-disabled');
        _generate_code.val("重新发送(" + countdown + ")");
        countdown--;
    }
    setTimeout(function() {
        settime();
    },1000);
}
function IsPC() {
    var userAgentInfo = navigator.userAgent;
    var Agents = ["Android", "iPhone",
        "SymbianOS", "Windows Phone",
        "iPad", "iPod"];
    var flag = true;
    for (var v = 0; v < Agents.length; v++) {
        if (userAgentInfo.indexOf(Agents[v]) > 0) {
            flag = false;
            break;
        }
    }
    return flag;
}
// 优惠券点击使用
$('.mycoupon').on('click', function () {
    var coupon=$('#coupon').val();
    var url=$(this).attr('url');
    var cid=$(this).attr('cid');
    var price=$(this).attr('price');
    var type=$(this).attr('type');
    var usetype=$(this).attr('usetype');
    if(coupon=='none'){
        layer.msg('请选择优惠券');
    }else{
        $.ajax({
            url : url,
            type : "post",
            dataType : "json",
            data : {'coupon':coupon,'cid':cid,'price':price,'type':type,'usetype':usetype},
            async:false,
            success : function(data) {
                if(data.code==1){
                    layer.msg(data.msg);
                }
                if(data.code==0){
                    $("#usedCoupon").val(coupon);
                    $("#coupon-type").text(data.rate+'折优惠券');
                    $("#coupon-price").text(parseFloat(parseFloat(data.rate/10)*parseFloat($("#yuan-price").text())).toFixed(1));
                }
            }
        });
    }
})
//阿里云视频、分类分页
var count=$("#count").val();
var PageSize=$("#PageSize").val();
var curr=$("#curr").val();
var url=$("#url").val();
laypage.render({
    elem: 'page'
    ,count: count
    ,curr:curr
    ,limit:PageSize
    ,layout: [ 'prev', 'page', 'next']
    ,jump: function(obj,first){
        if (!first) {
            var cateId=$("#cateId").val();
            if(cateId){
                location.href = url+obj.curr+'/CateId/'+cateId+'.html';
            }else{
                location.href = url+obj.curr+'.html';
            }
        }
    }
});
//监听secect
form.on('select(select)', function(data){
    var url=$(data.elem).attr("data-url");
    var obj = {};
    obj[$(data.elem).attr('name')] = data.value;
    obj['_verify'] = 0;
    $.ajax({
        url: url,
        type: 'post',
        dataType: 'json',
        data: obj,
        success: function (result) {
            if (result.code === 1) {
                layer.msg(result.msg);
                setTimeout(function () {
                    location.reload();
                }, 1000);
            }
        },
        error: function (xhr, state, errorThrown) {
            layer.close(index);
            layer.msg(state + '：' + errorThrown);
        }
    });
    form.render('select');
});
//鼠标滑过显示tips
$(".tips").mouseover(function() {
    var tips=$(this).attr('tips');
    layer.tips(tips, this, {
        tips: [1, "#FF5722"]
    });
});
//倒计时
layui.use('util', function(){
    var util = layui.util;
    var getendtime=$('#endTime').val()?$('#endTime').val():'';
    getendtime=getendtime.replace(/\-/g, '/');
    var getnowtime=$('#nowTime').val()?$('#nowTime').val():'';
    getnowtime=getnowtime.replace(/\-/g, '/');
    var endTime = new Date(getendtime).getTime()
        ,serverTime = new Date(getnowtime).getTime()
        ,type=$('#useType').val()
        ,examtype=$('#examtype').val();
    util.countdown(endTime, serverTime, function(date, serverTime, timer){
        if(date[0]==0){
            var str= '';
            str=str+'<span class="layui-badge layui-bg-blue countdown" >'+date[1]+'小时</span>';
            str=str+'<span class="layui-badge layui-bg-blue countdown" >'+date[2]+'分</span>';
            str=str+'<span class="layui-badge layui-bg-blue countdown" >'+date[3]+'秒</span>';
        }else{
            var str= '<span class="layui-badge layui-bg-blue countdown">'+date[0]+'天</span>';
            str=str+'<span class="layui-badge layui-bg-blue countdown" >'+date[1]+'小时</span>';
            str=str+'<span class="layui-badge layui-bg-blue countdown" >'+date[2]+'分</span>';
            str=str+'<span class="layui-badge layui-bg-blue countdown" >'+date[3]+'秒</span>';
        }

        lay('#remain').html(str);
        if(type==2 && date[0]==0 && date[1]==0 && date[2]==0 && date[3]==0){
            var ctype=$('#type').val();
            var id=$('#id').val();
            var isopen=$('#isopen').val();
            if(isopen==1){
                var href="/index/exam/dopackage/id/"+id+"/exam/formal";
            }else{
                var href="/learn/"+id+"/"+ctype+"/exam/formal";
            }
            $('#startexam').attr('href',href);
            $('#startexam').removeClass("layui-btn-primary");
        }
        if(examtype==2 && type==3 && date[0]==0 && date[1]==0 && date[2]==15 && date[3]==0){
            layer.alert('离考试结束还有15分钟，请注意安排时间！', {
                closeBtn: 0
            });
        }
        if(examtype==2 && type==3 && date[0]==0 && date[1]==0 && date[2]==0 && date[3]==0){
            $('#jiaojuan').click();
        }
    });
});
$('#top-back').hide()
$(window).scroll(function() {
    if ($(this).scrollTop() > 350) {
        $("#top-back").fadeIn();
    } else {
        $("#top-back").fadeOut();
    }
})

function topBack() {
    $('body,html').animate({
        scrollTop: 0
    }, 300);
}
$(function () {
    $('.subMenu').stickyNavbar({
        activeClass: "active",
        sectionSelector: "scrollto",
        animDuration: 250,
        startAt: 0,
        easing: "linear",
        animateCSS: true,
        animateCSSRepeat: false,
        cssAnimation: "fadeInDown",
        jqueryEffects: false,
        jqueryAnim: "slideDown",
        selector: "a",
        mobile: false,
        mobileWidth: 480,
        zindex: 9999,
        stickyModeClass: "sticky",
        unstickyModeClass: "unsticky"
    });
});
$('.nums a').on('click', function () {
    var id=$(this).attr('signid');
    //$('html, body').animate({scrollTop: $('#question_'+id).offset().top}, 300);
})
form.on('radio(answer)', function(data){
    var id=$(this).attr('radioid');
    $("#signeditor"+id).addClass("done");
});
form.on('checkbox(answer)', function(data){
    var id=$(this).attr('checkbox');
    $("#signeditor"+id).addClass("done");
});
//点播视频平台选择
form.on("radio(selectVideoplatform)", function (data) {
    var val = data.value;
    if(val=='yunluzhi'){
        $('#yunluzhi').removeClass('layui-hide').addClass('layui-show');
        $('#aliyun').removeClass('layui-show').addClass('layui-hide');
        $('#local').removeClass('layui-show').addClass('layui-hide');
        $('#share').removeClass('layui-show').addClass('layui-hide');
    }
    if(val=='aliyun'){
        $('#aliyun').removeClass('layui-hide').addClass('layui-show');
        $('#yunluzhi').removeClass('layui-show').addClass('layui-hide');
        $('#local').removeClass('layui-show').addClass('layui-hide');
        $('#share').removeClass('layui-show').addClass('layui-hide');
    }
    if(val=='local'){
        $('#local').removeClass('layui-hide').addClass('layui-show');
        $('#yunluzhi').removeClass('layui-show').addClass('layui-hide');
        $('#aliyun').removeClass('layui-show').addClass('layui-hide');
        $('#share').removeClass('layui-show').addClass('layui-hide');
    }
    if(val=='share'){
        $('#share').removeClass('layui-hide').addClass('layui-show');
        $('#yunluzhi').removeClass('layui-show').addClass('layui-hide');
        $('#aliyun').removeClass('layui-show').addClass('layui-hide');
        $('#local').removeClass('layui-show').addClass('layui-hide');
    }
});
//直播平台选择
form.on("radio(selectliveplatform)", function (data) {
    var val = data.value;

    if(val=='agora'){
        $('#agora').removeClass('layui-hide').addClass('layui-show');
        $('#talkcloud').removeClass('layui-show').addClass('layui-hide');
        $('#baijiayun').removeClass('layui-show').addClass('layui-hide');
        $('#pano').removeClass('layui-show').addClass('layui-hide');
    }
    if(val=='pano'){
        $('#pano').removeClass('layui-hide').addClass('layui-show');
        $('#agora').removeClass('layui-hide').addClass('layui-hide');
        $('#talkcloud').removeClass('layui-show').addClass('layui-hide');
        $('#baijiayun').removeClass('layui-show').addClass('layui-hide');
    }
});
//直播类型选择监听
form.on('select(roomtype)', function(data){
    var val = data.value;
    if(val==0){
        $('#layout').val('51');
        $('#roomLayout_type').removeClass('layui-hide').addClass('layui-show');
        $('#rowroomLayout').removeClass('layui-show').addClass('layui-hide');
    }
    if(val==3){
        $('#layout').val('1');
        $('#rowroomLayout').removeClass('layui-hide').addClass('layui-show');
        $('#roomLayout_type').removeClass('layui-show').addClass('layui-hide');
    }
})

//vip会员选择
$('.vip-item-wrap').on('click', function () {
    $(".vip-item-wrap").removeClass("active");
    $(this).addClass("active");
    var type=$(this).attr('type');
    $('.role-join-button').attr('href','/vip/'+type+'.html');
});
//上传课件弹窗
$('#updoc').on('click', function() {
    var url = $(this).attr('href');
    var index = layer.open({
        title: '上传文件',
        type: 2,
        area: ['500px', '260px'],
        content: url,
        closeBtn: 1,
        shade:0.4,
        yes: function(index, layero){
            layer.close(index);
        }
    })
    return false;
});
//双击文件夹
$(".local-videolist").dblclick(function(){
    var tid = $(this).attr("tid");
    window.location.href = "/admin/course/localvideoadmin/tid/"+tid;
})
$('.redoexam').on('click', function() {
    var cid = $(this).attr('cid');
    var sid = $(this).attr('sid');
    var type = $(this).attr('type');
    var eid = $(this).attr('eid');
    var uid = $(this).attr('uid');
    var url = $(this).attr('href');
    var delurl ='/index/exam/delexam' ;
    var trip='重考会清除之前得考试记录，是否继续？';
    layer.confirm( trip, {
        icon: 3,
        title: '提示'
    }, function (index) {
        var index = layer.msg('正在处理，请稍候', {
            icon: 16,
            time: false,
            shade: 0.3
        });
        $.ajax({
            url: delurl,
            type: 'post',
            dataType: 'json',
            data: {eid:eid,uid:uid,cid:cid,sid:sid,type:type},
            success: function (data) {
                setTimeout(function () {
                    location.href = url;
                    layer.close(index);
                }, 1000);
            },
            error: function (xhr, state, errorThrown) {
                layer.close(index);
                layer.msg(state + '：' + errorThrown);
            }
        });
    });
    return false;
});
$('.examRes').on('click', function() {
    var url = $(this).attr('href');
    location.href = url;
    return false;
});
//首页banner导航
$('.carousel-left').mousemove(function(event){
    $(".big_block").css({"width":"530px"})
});
$('.carousel-left').mouseout(function(event){
    $(".big_block").css({"width":"0px"})
});
$('#carousel-nav li').mousemove(function(event){
    var id = $(this).attr('categoryid');
    $("#list_item_"+id).removeClass('layui-hide').siblings().addClass('layui-hide')
    $("#courses_item_"+id).removeClass('layui-hide').siblings().addClass('layui-hide')
});
// app上传
upload.render({
    elem: '.appimages',
    url: '/api/uploader/uploadImage',
    done: function (result) {
        // 上传完毕回调
        if (result.code === 1) {
            console.log(result.url)
            this.item.children('img').attr('src',result.url);
            this.item.children('input').val(result.url);
        } else {
            layer.msg(result.msg);
        }
    }
});
