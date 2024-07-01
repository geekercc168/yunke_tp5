var title=$('#isbulletscreen').val()== 1? $('#BulletScreen').val():null;
var buyurl=$('#buyurl').val();
var PreviewVodTime=parseInt($('#previewtimes').val());
var playauth=null;
var token=getplaytoken();
var duration=60;
var seeked=0;
var learnseek=$('#seek').val();
var learnover=$('#learnover').val();
var progress=learnover==1?'progress':'';
var player = new Aliplayer({
    id: 'J_prismPlayer',
    width: '100%',
    height:'210px',
    autoplay: false,
    preload: false,
    language: "zh-cn",
    isLive: false,
    useH5Prism:true,
    x5_type:'h5',
    x5_fullscreen:true,
    x5_orientation:'landscape',
    playsinline:true,
    //disableSeek:true,
    //format:'mp4',
    encryptType: 1,
    vid : $('#vid').val(),
    playauth : token,
    skinLayout: [
        {name: "bigPlayButton", align: "blabs", x: 30, y: 80},
        {name: "H5Loading", align: "cc"},
        {name: "errorDisplay", align: "tlabs", x: 0, y: 0},
        {name: "infoDisplay"},
        {name:"tooltip", align:"blabs",x: 0, y: 56},
        {name: "thumbnail"},
        {
            name: "controlBar", align: "blabs", x: 0, y: 0,
            children: [
                {name: progress, align: "blabs", x: 0, y: 44},
                {name: "playButton", align: "tl", x: 15, y: 12},
                {name: "timeDisplay", align: "tl", x: 10, y: 7},
                {name: "fullScreenButton", align: "tr", x: 10, y: 12},
                {name:"subtitle", align:"tr",x:15, y:12},
                {name:"setting", align:"tr",x:15, y:12},
                {name: "volume", align: "tr", x: 5, y: 10}
            ]
        }
    ],
    components: [
        {
            name: 'RateComponent',//播放速度
            type: AliPlayerComponent.RateComponent
        },{
            name: 'QualityComponent',//清晰度
            type: AliPlayerComponent.QualityComponent
        }, {
            name: 'PreviewVodComponent',//视频试看
            type: AliPlayerComponent.PreviewVodComponent,
            args: [PreviewVodTime, '#endPreviewTemplate', "<a href="+buyurl+" class='vip-join' style='color:#00c1de'>购买课程</a>观看完整视频"]
        },{
            name: 'BulletScreenComponent',
            type: AliPlayerComponent.BulletScreenComponent,
            args: [title, {fontSize: '16px', color: '#00c1de'}, 'random']
        }
    ]
},function(player){
    var cid= $("#cid").val();
    var sid= $("#sid").val();
    player.on('ready',function() {
        $.ajax({
            url:"/index/Course/nowStudy",
            type:"post", data:{'sid':sid,'cid':cid,'type':1},
            dataType:'json',
            success:function(data){
            }
        });
    });
    player.on('play',function  (e) {
        if(learnseek>0)
        {
            player.seek(learnseek);
        }
    });
    player.on('ended',function() {
        if(PreviewVodTime==0){
            $.ajax({
                url:"/index/Course/studied",
                type:"post", data:{'sid':sid,'cid':cid,'type':1},
                dataType:'json',
                success:function(data){
                    if(data.status==1){
                        layer.msg('恭喜你，本节课已经学完！')
                    }
                }
            })
        }
    });
    player.on('timeupdate',function(){
        var  seek= parseInt(player.getCurrentTime());
        if(seek>seeked  && seek % duration==0) {
            seeked=seek;
            learnseek=seek;
            $.ajax({
                url: "/index/Course/addduration",
                type: "post", data: {'sid': sid, 'cid': cid, 'type': 1, 'duration': duration,'seek':seek},
                dataType: 'json',
                success: function (data) {
                }
            });
        }
    });
});
function getplaytoken() {
    $.ajax({
        url:"/index/Course/getplaytoken",
        type:"post",
        data:{'vid':$('#vid').val()},
        dataType:'json',
        async:false,
        success:function(data){
            if(data.status==0){
                playauth= data.PlayAuth;
            }else{
                layer.msg(data.msg);
                return;
            }
        }
    });
    return playauth;
};
hui.tab('.hui-tab');
hui.accordion(true, true);