$('#upload-container').click(function(event) {
    $("#picker").find('input').click();
});
var uploader = WebUploader.create({
    auto: true,
    swf: '/static/libs/webuploader/Uploader.swf',
    server: '/api/uploader/localupload',
    dnd: '#upload-container',
    pick: '#picker',
    multiple: true,
    chunked: true,
    threads: 5,
    method: 'POST',
    fileSizeLimit: 1024*1024*100*100*10, //验证文件总大小是否超出限制, 超出则不允许加入队列。
    fileSingleSizeLimit: 1024*1024*100*10, //验证单个文件大小是否超出限制, 超出则不允许加入队列。
    fileVal:'file', // [默认值：'file'] 设置文件上传域的name。
});
uploader.on('fileQueued', function(file) {
    $(".radio").prepend(
        "<li class='layui-col-xs3 layui-col-sm3 layui-col-md3 pd10'>" +
        "    <div class='video-pic'>" +
        "        <i class='fa fa-cloud-upload'></i>" +
        "        <input type='hidden' value=''>" +
        "        <div class='video-title-up'>"+file.name+"</div>"+
        "        <div class='shadow selected'></div>" +
        "        <div class='up-percent' id="+ file.id +">0%</div>"+
        "        <div class='layui-progress' lay-showpercent='true' lay-filter="+'progress'+file.id+">" +
        "            <div class='layui-progress-bar layui-bg-red' lay-percent='0%'></div>" +
        "         </div>"+
        "     </div>" +
        "</li>")
});

uploader.on('uploadProgress', function(file, percentage) {
    var percent = parseInt(percentage * 100) + '%' ;
    $( '#'+file.id ).html(percent);
    element.progress('progress'+file.id, percent);
});

uploader.on('uploadSuccess', function(file, response) {
    layer.msg('上传成功');
});

uploader.on('uploadError', function(file) {
    layer.msg('上传失败');
});

$('#upload-list').on('click', '.upload-item .btn-delete', function() {
    file_id = $(this).data('file_id');
    uploader.removeFile(file_id, true);
    console.log(uploader.getFiles());
});

$('#upload-list').on('click', '.btn-retry', function() {
    uploader.retry($(this).data('file_id'));
});

uploader.on('uploadComplete', function(file) {
    //console.log(uploader.getFiles());
});
