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
        result(uploadInfo.file.name);
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
        layer.msg('上传成功');

    }
});
document.getElementById("files")
    .addEventListener('change', function (event) {
        for(var i=0; i<event.target.files.length; i++) {
            $("#video-list").prepend("<tr><td class='layui-elip'>"
                + event.target.files[i].name
                + "</td> "
                + "<td class='upload-video-size' id='size"
                + hex_md5(event.target.files[i].name)
                + "'>"
                + "计算中"
                + "</td>"
                + "<td class='upload-video-state'>"
                + "<span>"
                + "<u id='bfb"
                + hex_md5(event.target.files[i].name)
                + "'></u>"
                + " </span>"
                + " </td>"
                + "<td class='upload-video-operation' id='state"
                + hex_md5(event.target.files[i].name)
                + "'>"
                + "等待中"
                + "</td>"
                + "</div>");
            $("#button-start").removeClass('layui-hide');
            $("#button-shut").removeClass('layui-hide')
            uploader.addFile(event.target.files[i], null, null, null);
        }
    });
function ajaxgettoken(filename){
    var result;
    var category_id=$("#categoryId").val();
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
    var category_id=$("#categoryId").val();
    if(category_id==''||category_id==null){
        layer.msg("请先选择视频分类");
    }else{
        $("#files").click();
    }
}
function addinfo(name,totalSize,loadedPercent){
    var filename=hex_md5(name);
    var bfb='bfb'+filename;
    var state='state'+filename;
    var size='size'+filename;
    var percent=Math.ceil(loadedPercent * 100) + "%";
    var sizem = (totalSize/1048576).toFixed(2);
    $("#"+state).html('上传中');
    $("#"+size).html(sizem+'M');
    $("#"+bfb).width(loadedPercent * 100+'%');
}

function result(name){
    var filename=hex_md5(name);
    var state='state'+filename;
    $("#"+state).html('<i class="layui-icon layui-icon-ok-circle" style="font-size: 20px; color: green;"></i>');
}
function start() {
    uploader.startUpload();
}
function shut(){
    var index = parent.layer.getFrameIndex(window.name);
    window.parent.location.reload()
    parent.layer.close(index);
}