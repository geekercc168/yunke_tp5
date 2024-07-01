var nowVesion=$("#nowVesion").val();
var lastVersion=$("#lastVersion").val();
var $updateBtn = $(".upbutton");
var urls=new Array();
urls['checkUrl']=$updateBtn.attr('checkUrl');
urls['downLoadFile']=$updateBtn.attr('downLoadFile');
urls['delFile']=$updateBtn.attr('delFile');
urls['clearCache']=$updateBtn.attr('clearCache');
urls['finish']=$updateBtn.attr('finish');
var steps = getQueue(urls);
if(nowVesion<lastVersion){
    layer.alert('手机模板有新版本发布，请及时更新');
}
$.each(steps, function(i, step) {
    $(document).queue('update_step_queue', function() {
        exec(step.title, step.url, step.progressRange[0], step.progressRange[1]);
    });
});
function getQueue(urls) {
    var steps = [{
        title: '检查系统环境',
        url: urls.checkUrl,
        progressRange: [5, 20]
    }, {
        title: '下载手机模板代码包',
        url: urls.downLoadFile,
        progressRange: [20, 50]
    },{
        title: '删除临时文件',
        url: urls.delFile,
        progressRange: [50, 80]
    },{
        title: '清空缓存',
        url: urls.clearCache,
        progressRange: [80, 95]
    },{
        title: '升级完成',
        url: urls.finish,
        progressRange: [100, 100]
    }];
    return steps;
}
function exec(title, url, startProgress, endProgress){
    progress(startProgress,endProgress,'正在'+ title);
    $.ajax({
        url : url,
        type : "post",
        dataType : "json",
        async:false,
        success : function(data) {
            if(data.code==1){
                setTimeout( function(){
                    progress(startProgress,endProgress,title + '完成');
                    $(document).dequeue('update_step_queue');
                }, 1000 )
            }else if(data.code==2){
                finish();
                progress(startProgress,endProgress,'恭喜您'+ title);
            }else{
                $(document).clearQueue('update_step_queue');
                layer.alert(data.msg);
            }
        }
    });
}

$('.upversion').on('click', function () {
    var upurl = $(this).attr('upurl');
    var nowVesion=$("#nowVesion").val();
    var lastVersion=$("#lastVersion").val();
    if(nowVesion==lastVersion){
        layer.msg('您的版本是最新版本，无需升级!');
    }else{
        var index = layer.open({
            skin: 'layui-layer-molv',
            title: '手机模板下载',
            type: 2,
            area: ['500px', '350px'],
            content: upurl,
            shade:0.4
        })
    }
})

$('.upbutton').on('click', function () {
    initialize();
    $(document).dequeue('update_step_queue');
})
$('.finish').on('click', function () {
    layer.closeAll();
    parent.location.reload();
})
function initialize() {
    $('.update-progress').removeClass('layui-hide');
    $('.update-message').removeClass('layui-hide');
    $('.upbutton').hide();
    $('.doing').removeClass('layui-hide');
}
function progress(start,end,text){
    element.progress('progress', start+'%');
    $('.update-message').html(text);
}
function finish() {
    $('.update-progress').removeClass('layui-hide');
    $('.update-message').removeClass('layui-hide');
    $('.upbutton').hide();
    $('.doing').hide();
    $('.finish').removeClass('layui-hide');
}
function update(url,type,param) {
    var res;
    $.ajax({
        url : url,
        type : "post",
        dataType : "json",
        data:{'type':type,'param':param},
        async:false,
        success : function(data) {
            if(data.code==1){
                res = data;
            }else{
                layer.msg(data.msg);
            }
        }
    });
    return res;
}
