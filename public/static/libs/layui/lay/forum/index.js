
layui.define(['layer', 'laytpl', 'form', 'element', 'upload', 'util'], function(exports){
    var $ = layui.jquery
        ,layer = layui.layer
        ,laytpl = layui.laytpl
        ,form = layui.form
        ,element = layui.element
        ,upload = layui.upload
        ,util = layui.util
        ,device = layui.device()
        ,DISABLED = 'layui-btn-disabled';
    //阻止IE7以下访问
    if(device.ie && device.ie < 8){
        layer.alert('如果您非得使用 IE 浏览器访问Fly社区，那么请使用 IE8+');
    }
    layui.focusInsert = function(obj, str){
        var result, val = obj.value;
        obj.focus();
        if(document.selection){ //ie
            result = document.selection.createRange();
            document.selection.empty();
            result.text = str;
        } else {
            result = [val.substring(0, obj.selectionStart), str, val.substr(obj.selectionEnd)];
            obj.focus();
            obj.value = result.join('');
        }
    };
    //数字前置补零
    layui.laytpl.digit = function(num, length, end){
        var str = '';
        num = String(num);
        length = length || 2;
        for(var i = num.length; i < length; i++){
            str += '0';
        }
        return num < Math.pow(10, length) ? str + (num|0) : num;
    };

    var fly = {
        //Ajax
        json: function(url, data, success, options){
            var that = this, type = typeof data === 'function';

            if(type){
                options = success
                success = data;
                data = {};
            }

            options = options || {};

            return $.ajax({
                type: options.type || 'post',
                dataType: options.dataType || 'json',
                data: data,
                url: url,
                success: function(res){
                    if(res.status === 0) {
                        success && success(res);
                    } else {
                        layer.msg(res.msg || res.code, {shift: 6});
                        options.error && options.error();
                    }
                }, error: function(e){
                    layer.msg('请求异常，请重试', {shift: 6});
                    options.error && options.error(e);
                }
            });
        }

        //计算字符长度
        ,charLen: function(val){
            var arr = val.split(''), len = 0;
            for(var i = 0; i <  val.length ; i++){
                arr[i].charCodeAt(0) < 299 ? len++ : len += 2;
            }
            return len;
        }

        ,form: {}

        //简易编辑器
        ,layEditor: function(options){
            var html = ['<div class="layui-unselect fly-edit">'
                /*,'<span type="face" title="插入表情"><i class="iconfont icon-yxj-expression" style="top: 1px;"></i></span>'*/
                ,'<span type="picture" title="插入图片：img[src]"><i class="iconfont icon-tupian"></i></span>'
                ,'<span type="href" title="超链接格式：a(href)[text]"><i class="iconfont icon-lianjie"></i></span>'
                ,'<span type="code" title="插入代码或引用"><i class="iconfont icon-emwdaima" style="top: 1px;"></i></span>'
                ,'<span type="hr" title="插入水平线">hr</span>'
                ,'<span type="yulan" title="预览"><i class="iconfont icon-yulan1"></i></span>'
                ,'</div>'].join('');

            var log = {}, mod = {
                face: function(editor, self){ //插入表情
                    var str = '', ul, face = fly.faces;
                    for(var key in face){
                        str += '<li title="'+ key +'"><img src="'+ face[key] +'"></li>';
                    }
                    str = '<ul id="LAY-editface" class="layui-clear">'+ str +'</ul>';
                    layer.tips(str, self, {
                        tips: 3
                        ,time: 0
                        ,skin: 'layui-edit-face'
                    });
                    $(document).on('click', function(){
                        layer.closeAll('tips');
                    });
                    $('#LAY-editface li').on('click', function(){
                        var title = $(this).attr('title') + ' ';
                        layui.focusInsert(editor[0], 'face' + title);
                    });
                }
                ,picture: function(editor){ //插入图片
                    layer.open({
                        type: 1
                        ,id: 'fly-jie-upload'
                        ,title: '插入图片'
                        ,area: 'auto'
                        ,shade: false
                        ,area: '465px'
                        ,fixed: false
                        ,offset: [
                            editor.offset().top - $(window).scrollTop() + 'px'
                            ,editor.offset().left + 'px'
                        ]
                        ,skin: 'layui-layer-border'
                        ,content: ['<ul class="layui-form layui-form-pane" style="margin: 20px;">'
                            ,'<li class="layui-form-item">'
                            ,'<label class="layui-form-label">URL</label>'
                            ,'<div class="layui-input-inline">'
                            ,'<input required name="image" placeholder="支持直接粘贴远程图片地址" value="" class="layui-input">'
                            ,'</div>'
                            ,'<button type="button" class="layui-btn layui-btn-primary" id="uploadImg"><i class="layui-icon">&#xe67c;</i>上传图片</button>'
                            ,'</li>'
                            ,'<li class="layui-form-item" style="text-align: center;">'
                            ,'<button type="button" lay-submit lay-filter="uploadImages" class="layui-btn">确认</button>'
                            ,'</li>'
                            ,'</ul>'].join('')
                        ,success: function(layero, index){
                            var image =  layero.find('input[name="image"]');
                            //执行上传实例
                            upload.render({
                                elem: '#uploadImg'
                                ,url: '/index/forum/uploadImage'
                                ,size: 2000
                                ,done: function(res){
                                    if(res.code == 1){
                                        image.val(res.url);
                                    } else {
                                        layer.msg(res.msg, {icon: 5});
                                    }
                                }
                            });

                            form.on('submit(uploadImages)', function(data){
                                var field = data.field;
                                if(!field.image) return image.focus();
                                layui.focusInsert(editor[0], 'img['+ field.image + '] ');
                                layer.close(index);
                            });
                        }
                    });
                }
                ,href: function(editor){ //超链接
                    layer.prompt({
                        title: '请输入合法链接'
                        ,shade: false
                        ,fixed: false
                        ,id: 'LAY_flyedit_href'
                        ,offset: [
                            editor.offset().top - $(window).scrollTop() + 'px'
                            ,editor.offset().left + 'px'
                        ]
                    }, function(val, index, elem){
                        if(!/^http(s*):\/\/[\S]/.test(val)){
                            layer.tips('这根本不是个链接，不要骗我。', elem, {tips:1})
                            return;
                        }
                        layui.focusInsert(editor[0], ' a('+ val +')['+ val + '] ');
                        layer.close(index);
                    });
                }
                ,code: function(editor){ //插入代码
                    layer.prompt({
                        title: '请贴入代码或任意文本'
                        ,formType: 2
                        ,maxlength: 10000
                        ,shade: false
                        ,id: 'LAY_flyedit_code'
                        ,area: ['800px', '360px']
                    }, function(val, index, elem){
                        layui.focusInsert(editor[0], '[pre]\n'+ val + '\n[/pre]');
                        layer.close(index);
                    });
                }
                ,hr: function(editor){ //插入水平分割线
                    layui.focusInsert(editor[0], '[hr]');
                }
                ,yulan: function(editor){ //预览
                    var content = editor.val();

                    content = /^\{html\}/.test(content)
                        ? content.replace(/^\{html\}/, '')
                        : fly.content(content);

                    layer.open({
                        type: 1
                        ,title: '预览'
                        ,shade: false
                        ,area: ['100%', '100%']
                        ,scrollbar: false
                        ,content: '<div class="detail-body" style="margin:20px;">'+ content +'</div>'
                    });
                }
            };

            layui.use('face', function(face){
                options = options || {};
                fly.faces = face;
                $(options.elem).each(function(index){
                    var that = this, othis = $(that), parent = othis.parent();
                    parent.prepend(html);
                    parent.find('.fly-edit span').on('click', function(event){
                        var type = $(this).attr('type');
                        mod[type].call(that, othis, this);
                        if(type === 'face'){
                            event.stopPropagation()
                        }
                    });
                });
            });

        }

        ,escape: function(html){
            return String(html||'').replace(/&(?!#?[a-zA-Z0-9]+;)/g, '&amp;')
                .replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/'/g, '&#39;').replace(/"/g, '&quot;');
        }

        //内容转义
        ,content: function(content){
            var util = fly,item = fly.faces
            //支持的html标签
            var html = function(end){
                return new RegExp('\\n*\\|\\-'+ (end||'') +'(div|span|p|button|table|thead|th|tbody|tr|td|ul|li|ol|li|dl|dt|dd|h2|h3|h4|h5)([\\s\\S]*?)\\-\\|\\n*', 'g');
            };
            //XSS
            content = util.escape(content||'')
            //转义图片
                .replace(/img\[([^\s]+?)\]/g, function(img){
                    return '<img src="' + img.replace(/(^img\[)|(\]$)/g, '') + '">';
                })
                //转义@
                .replace(/@(\S+)(\s+?|$)/g, '@<a href="javascript:;" class="fly-aite">$1</a>$2')
                //转义表情
                .replace(/face\[([^\s\[\]]+?)\]/g, function(face){
                    var alt = face.replace(/^face/g, '');
                    return '<img alt="'+ alt +'" title="'+ alt +'" src="' + item[alt] + '">';
                })
                //转义脚本
                .replace(/a(\(javascript:)(.+)(;*\))/g, 'a(javascript:layer.msg(\'非法脚本\');)')
                //转义链接
                .replace(/a\([\s\S]+?\)\[[\s\S]*?\]/g, function(str){
                    var href = (str.match(/a\(([\s\S]+?)\)\[/)||[])[1];
                    var text = (str.match(/\)\[([\s\S]*?)\]/)||[])[1];
                    if(!href) return str;
                    var rel =  /^(http(s)*:\/\/)\b(?!(\w+\.)*(sentsin.com|layui.com))\b/.test(href.replace(/\s/g, ''));
                    return '<a href="'+ href +'" target="_blank"'+ (rel ? ' rel="nofollow"' : '') +'>'+ (text||href) +'</a>';
                })
                //转义横线
                .replace(/\[hr\]\n*/g, '<hr>')
                //转义表格
                .replace(/\[table\]([\s\S]*)\[\/table\]\n*/g, function(str){
                    return str.replace(/\[(thead|th|tbody|tr|td)\]\n*/g, '<$1>')
                        .replace(/\n*\[\/(thead|th|tbody|tr|td)\]\n*/g, '</$1>')

                        .replace(/\[table\]\n*/g, '<table class="layui-table">')
                        .replace(/\n*\[\/table\]\n*/g, '</table>');
                })
                //转义 div/span
                .replace(/\n*\[(div|span)([\s\S]*?)\]([\s\S]*?)\[\/(div|span)\]\n*/g, function(str){
                    return str.replace(/\[(div|span)([\s\S]*?)\]\n*/g, '<$1 $2>')
                        .replace(/\n*\[\/(div|span)\]\n*/g, '</$1>');
                })
                //转义列表
                .replace(/\[ul\]([\s\S]*)\[\/ul\]\n*/g, function(str){
                    return str.replace(/\[li\]\n*/g, '<li>')
                        .replace(/\n*\[\/li\]\n*/g, '</li>')

                        .replace(/\[ul\]\n*/g, '<ul>')
                        .replace(/\n*\[\/ul\]\n*/g, '</ul>');
                })
                //转义代码
                .replace(/\[pre\]([\s\S]*)\[\/pre\]\n*/g, function(str){
                    return str.replace(/\[pre\]\n*/g, '<pre>')
                        .replace(/\n*\[\/pre\]\n*/g, '</pre>');
                })
                //转义引用
                .replace(/\[quote\]([\s\S]*)\[\/quote\]\n*/g, function(str){
                    return str.replace(/\[quote\]\n*/g, '<div class="layui-elem-quote">')
                        .replace(/\n*\[\/quote\]\n*/g, '</div>');
                })
                //转义换行
                .replace(/\n/g, '<br>')
            return content;
        }
    };
    //搜索
    $('.fly-search').on('click', function(){
        layer.open({
            type: 1
            ,title: false
            ,closeBtn: false
            //,shade: [0.1, '#fff']
            ,shadeClose: true
            ,maxWidth: 10000
            ,skin: 'fly-layer-search'
            ,content: ['<form action="http://cn.bing.com/search">'
                ,'<input autocomplete="off" placeholder="搜索内容，回车跳转" type="text" name="q">'
                ,'</form>'].join('')
            ,success: function(layero){
                var input = layero.find('input');
                input.focus();

                layero.find('form').submit(function(){
                    var val = input.val();
                    if(val.replace(/\s/g, '') === ''){
                        return false;
                    }
                    input.val('site:layui.com '+ input.val());
                });
            }
        })
    });
    //点击@
    $('body').on('click', '.fly-aite', function(){
        var othis = $(this), text = othis.text();
        if(othis.attr('href') !== 'javascript:;'){
            return;
        }
        text = text.replace(/^@|（[\s\S]+?）/g, '');
        othis.attr({
            href: '/jump?username='+ text
            ,target: '_blank'
        });
    });

    //表单提交
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
    //加载特定模块
    if(layui.cache.page && layui.cache.page !== 'index'){
        var extend = {};
        extend[layui.cache.page] = layui.cache.page;
        layui.extend(extend);
        layui.use(layui.cache.page);
    }

    //加载编辑器
    fly.layEditor({
        elem: '.fly-editor'
    });

    //手机设备的简单适配
    var treeMobile = $('.site-tree-mobile')
        ,shadeMobile = $('.site-mobile-shade')

    treeMobile.on('click', function(){
        $('body').addClass('site-mobile');
    });

    shadeMobile.on('click', function(){
        $('body').removeClass('site-mobile');
    });

    //获取统计数据
    $('.fly-handles').each(function(){
        var othis = $(this);
        $.get('/api/handle?alias='+ othis.data('alias'), function(res){
            othis.html('（下载量：'+ res.number +'）');
        })
    });
    exports('fly', fly);

});

