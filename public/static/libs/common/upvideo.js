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
            var category_id=$("#categoryId").val();
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
                itemHTML= "<tr><td class='layui-elip'>"
                    + file.videoName
                    + "</td> "
                    + "<td class='upload-video-size' id='size"
                    + "'>"
                    + "计算中"
                    + "</td>"
                    + "<td class='upload-video-state'>"
                    + "<span>"
                    + "<u class='bfb"
                    + "'></u>"
                    + " </span>"
                    + " </td>"
                    + "<td class='upload-video-operation' id='state"
                    + "'>"
                    + "等待中"
                    + "</td>"
                    + "</tr>";
                $("#video-list").prepend(itemHTML);
                uploader.autoUpload(file);
            }
        );

    },
    onUploadStart: function (data) {
    },
    onUploadProgress: function (data) {
        $('.upload-data-video-content').eq(data.fileItem.index).find(".bfb").width(data.percent);
        $('.upload-data-video-content').eq(data.fileItem.index).find(".upload-video-operation").html('上传中');
        $('.upload-data-video-content').eq(data.fileItem.index).find(".upload-video-size").html((data.total / 1048576).toFixed(2) + 'M');
    },
    onUploadSuccess: function (data) {
        $('.upload-data-video-content').eq(data.fileItem.index).find(".upload-video-operation").html('<i class="layui-icon layui-icon-ok-circle" style="font-size: 20px; color: green;"></i>');
        layer.msg('上传成功');
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
    var category_id=$("#categoryId").val();
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
