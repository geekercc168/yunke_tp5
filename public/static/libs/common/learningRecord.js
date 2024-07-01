var cid= $("#cid").val(),sid= $("#sid").val(),type= $("#type").val();
function nowStudy(){
    $.ajax({
        url:"/index/Course/nowStudy",
        type:"post", data:{'sid':sid,'cid':cid,'type':type},
        dataType:'json',
        success:function(data){
        }
    });
}
function studied(){
    $.ajax({
        url:"/index/Course/studied",
        type:"post", data:{'sid':sid,'cid':cid,'type':type},
        dataType:'json',
        success:function(data){
            if(data.status==1){
                layer.msg('恭喜你，本节课已经学完！')
            }
        }
    })
}
function addduration(){
    $.ajax({
        url: "/index/Course/addduration",
        type: "post", data: {'sid': sid, 'cid': cid, 'type': type, 'duration': 20,'seek':20},
        dataType: 'json',
        success: function (data) {
        }
    });
}
