var layer = layui.layer,
    element = layui.element;
var uploader = new AliyunUpload.Vod({
    userId:document.getElementById("userId").value,
    region:"cn-shanghai",
    partSize: 1048576,
    parallel: 5,
    retryCount: 3,
    retryDuration: 2,
    'onUploadstarted': function (uploadInfo) {
        var ajaxtoken=ajaxgettoken(uploadInfo.file.name);
        uploader.setUploadAuthAndAddress(uploadInfo, ajaxtoken.uploadAuth, ajaxtoken.uploadAddress,ajaxtoken.VideoId);
    },
    'onUploadSucceed': function (uploadInfo) {
        $(".up-percent").html('上传完成')
    },
    'onUploadFailed': function (uploadInfo, code, message) {
    },
    'onUploadProgress': function (uploadInfo, totalSize, loadedPercent) {
        addinfo(uploadInfo.file.name,totalSize,loadedPercent);
    },
    'onUploadTokenExpired': function (uploadInfo) {
        uploader.resumeUploadWithAuth(uploadAuth);
    },
    'onUploadEnd':function(uploadInfo){
        setTimeout(function(){
            layer.msg('上传成功');
            location.reload();
        }, 1000);
    }
});
document.getElementById("files")
    .addEventListener('change', function (event) {
        for(var i=0; i<event.target.files.length; i++) {
            $(".radio").prepend(
                "<li class='layui-col-xs3 layui-col-sm3 layui-col-md3 pd10'>" +
                "    <div class='video-pic'>" +
                "        <i class='fa fa-cloud-upload'></i>" +
                "        <input type='hidden' id='videoid"+hex_md5(event.target.files[i].name)+"' value=''>" +
                "        <div class='video-title'>"+event.target.files[i].name+"</div>"+
                "        <div class='shadow selected'></div>" +
                "        <div class='up-percent'>0%</div>"+
                "        <div class='layui-progress' lay-showpercent='true' lay-filter='progress'>" +
                "            <div class='layui-progress-bar layui-bg-red' lay-percent='0%'></div>" +
                "         </div>"+
                "     </div>" +
                "</li>")
            uploader.addFile(event.target.files[i], null, null, null);
            $("#button-start").click();
        }
    });
function ajaxgettoken(filename){
    var result;
    var category_id=$("#categoryid").val();
    $.ajax({
        url:"/admin/educloud/getaliuptoken",
        type:"post",
        data:{'title':filename,'categoryId':category_id},
        dataType:'json',
        async:false,
        success:function(data){
            result=data;
        },
        error:function () {
            layer.msg('获取上传视频token失败');
        }
    });
    return result;
}
function ToSelectFile() {
    var category_id=$("#categoryid").val();
    if(category_id==''||category_id==null){
        layer.msg("请先选择视频分类");
    }else{
        $("#files").click();
    }
}
function addinfo(name,totalSize,loadedPercent){
    element.progress('progress', loadedPercent*100+'%');
    $(".up-percent").html(Math.ceil(loadedPercent*100)+'%');
}

function start() {
    uploader.startUpload();
}
function shut(){
    var index = parent.layer.getFrameIndex(window.name);
    parent.layer.close(index);
}
// 视频课程选择视频的点击事件
$(".radio li").click(function() {
    $(".shadow").removeClass("selected");
    $(this).find('.shadow').addClass("selected");
    $('input[name=videoid]', window.parent.document).val($(this).find('input').val());
    var index = parent.layer.getFrameIndex(window.name);
    parent.layer.close(index);
})
// 视频分类点击事件
$(document).ready(function (){
    $("a").each(function(index){
        $(this).click(function(){
            $("a").removeClass("active");
            $("a").eq(index).addClass("active");
        });
    });
});