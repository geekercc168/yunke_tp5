var uploader = new BJY.VideoUploader({
    mainElement: $('input[type="file"]'),
    ignoreError: true,
    name: '',
    multiple: true,
    getUploadUrl: function (data) {
        return $.ajax({
            url: '/admin/educloud/getuptoken',
            type: 'post',
            dataType:'json',
            data: {
                file_name: data.videoName
            }
        })
            .then(function (response) {
                var result = response.data;
                var category_id=$("#categoryid").val();
                $.ajax({
                    url: '/admin/educloud/setVideoCategory',
                    type: 'post',
                    dataType:'json',
                    data: {
                        video_id: result.video_id,
                        category_id: category_id
                    }
                })
                return {
                    id: result.video_id,
                    url: result.upload_url
                };
            });
    },
    getResumeUploadUrl: function (data) {
        return $.ajax({
            url:  '/admin/educloud/getResumeUploadUrl',
            type: 'post',
            dataType:'json',
            data: {
                video_id: data.uploadId
            }
        })
            .then(function (response) {
                var result = response.data;
                return {
                    fid: result.video_id,
                    id: result.video_id,
                    url: result.upload_url,
                    uploaded: result.upload_size
                };
            });
    },
    onFileChange: function (files) {
        var currentFiles = uploader.currentFiles;
        if (!currentFiles.length) {
            return;
        }
        if (!uploader.validateFiles(currentFiles)) {
            uploader.reset();
            return;
        }
        $.each(
            currentFiles,
            function (index, file) {
                $(".radio").prepend(
                    "<li class='layui-col-xs3 layui-col-sm3 layui-col-md3 pd10'>" +
                    "    <div class='video-pic'>" +
                    "        <i class='fa fa-cloud-upload'></i>" +
                    "        <input type='hidden' id='videoid"+hex_md5(file.videoName)+"' value=''>" +
                    "        <div class='video-title'>"+file.videoName+"</div>"+
                    "        <div class='shadow selected'></div>" +
                    "        <div class='up-percent'>0%</div>"+
                    "        <div class='layui-progress' lay-showpercent='true' lay-filter='progress'>" +
                    "            <div class='layui-progress-bar layui-bg-red' lay-percent='0%'></div>" +
                    "         </div>"+
                    "     </div>" +
                    "</li>")
                uploader.autoUpload(file);
            }
        );

    },
    onUploadStart: function (data) {
    },
    onUploadProgress: function (data) {
        element.progress('progress', data.percent);
        $(".up-percent").html(data.percent);
    },
    onUploadSuccess: function (data) {
        setTimeout(function(){
            layer.msg('上传成功');
            location.reload();
        }, 1000);
    },
    onChunkUploadError: function (data) {
        layer.msg('分片上传错误，请重新上传');
    },
    onUploadError: function (data) {
        layer.msg('上传错误，请重新上传');
        $('.upload-data-video-content').eq(data.fileItem.index).find(".upload-video-operation").html('上传失败');
    },
    onUploadComplete: function (data) {
    },
    onError: function (data) {
        alert(data.content);
    }
});
$('#button-start').click(function () {
    uploader.stopFile(0);
});
$('#button-insert').click(function () {
    var category_id=$("#categoryid").val();
    if(category_id==''||category_id==null){
        layer.msg("请先选择视频分类");
    }else{
        $("#files").click();
    }
});
$('#button-shut').click(function (){
    var index = parent.layer.getFrameIndex(window.name);
    window.parent.location.reload()
    parent.layer.close(index);
});
