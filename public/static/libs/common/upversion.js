var nowVesion=$("#nowVesion").val();
var lastVersion=$("#lastVersion").val();
var $updateBtn = $(".upbutton");
var urls=new Array();
urls['checkUrl']=$updateBtn.attr('checkUrl');
urls['downLoadFile']=$updateBtn.attr('downLoadFile');
urls['execScript']=$updateBtn.attr('execScript');
urls['delFile']=$updateBtn.attr('delFile');
urls['clearCache']=$updateBtn.attr('clearCache');
urls['finish']=$updateBtn.attr('finish');
var steps = getQueue(urls);
if(nowVesion<lastVersion){
    layer.alert('有新版本发布，请及时更新');
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
        progressRange: [15, 20]
    }, {
        title: '下载升级包',
        url: urls.downLoadFile,
        progressRange: [30, 50]
    },{
        title: '执行升级程序',
        url: urls.execScript,
        progressRange: [50, 70]
    }, {
        title: '删除临时文件',
        url: urls.delFile,
        progressRange: [70, 90]
    },{
        title: '清空缓存',
        url: urls.clearCache,
        progressRange: [90, 95]
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
    var url = $(this).attr('url');
    var upurl = $(this).attr('upurl');
    var nowVersionInfo  = update(url,1,0);
    var lastVersionInfo = update(url,3,0);
    if(nowVersionInfo.version==lastVersionInfo.version){
        layer.msg('您的版本是最新版本，无需升级!');
    }else{
        var index = layer.open({
            skin: 'layui-layer-molv',
            title: '版本升级',
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
