var height=$(document.body).height()-60+'px';
var tabHieght=$(document.body).height()-125+'px';
$('.layui-tab-content ').height(tabHieght);
var replayurl,playcounts=0;
var seeked=0;
var buyurl=$('#buyurl').val();
var sectionInfo=[];
var learnInfo=[];
var BulletScreen;
var progress;
$(function (){
    var sid = $("#sid").val();
    getSection(sid);
    $.when(ajaxGetSection).done(function(){
        if(sectionInfo.replayurl){
            play(sectionInfo.replayurl)
        }else{
            layer.msg('回放地址不存在或已经删除');
        }
    })
})
function getSection(sid){
    ajaxGetSection = $.ajax({
        url: '/index/course/getSectionInfo',
        type: 'post',
        data: {sid:sid},
        dataType: 'json',
        success: function (result) {
            if (result.code === 0) {
                sectionInfo=result.sectionInfo;
                learnInfo=result.learnInfo
                progress=(learnInfo.learnover==1 || sectionInfo.isforward==1)?'progress':'';
                BulletScreen=(sectionInfo.isbulletscreen==1)?result.BulletScreen:'';
            }else{
                layer.msg(result.msg);
            }
        },
        error: function (xhr, state, errorThrown) {
            layer.msg(state + '：' + errorThrown);
        }
    });
}
function play(url){
    var player = new Aliplayer({
        id: 'Player',
        width: '99%',
        height: '92%',
        autoplay: true,
        source : url,
        skinLayout: [
            {"name": "H5Loading", "align": "cc"},
            {"name": "thumbnail"},
            {"name": "controlBar", "align": "blabs", "x": 0, "y": 0,
                "children": [
                    {"name": "playButton", "align": "tl", "x": 15, "y": 12},
                    {"name": "timeDisplay", "align": "tl", "x": 10, "y": 7},
                    {"name": "fullScreenButton", "align": "tr", "x": 10, "y": 12},
                    {"name": "volume", "align": "tr", "x": 5, "y": 10},
                    {"name": progress, "align": "tlabs", "x": 0, "y": 0}
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
                args: [sectionInfo.previewtimes, '#endPreviewTemplate', "<a href="+buyurl+" class='vip-join' style='color:#00c1de'>购买课程</a>观看完整视频"]
            },{
                name: 'BulletScreenComponent',
                type: AliPlayerComponent.BulletScreenComponent,
                args: [BulletScreen, {fontSize: '16px', color: '#00c1de'}, 'random']
            }
        ]
    },function(player){
        var cid= sectionInfo.csid;
        var sid= sectionInfo.id;
        player.on('ready',function() {
            $.ajax({
                url:"/index/Course/nowStudy",
                type:"post",
                data:{'sid':sectionInfo.id,'cid':sectionInfo.csid,'type':2},
                dataType:'json',
                success:function(data){
                }
            });
        });
        player.on('play',function  (e) {
            var learnseek=learnInfo.seek
            if(learnseek>0)
            {
                player.seek(learnseek);
            }
        });
        player.on('ended',function() {
            $.ajax({
                url:"/index/Course/studied",
                type:"post",
                data:{'sid':sectionInfo.id,'cid':sectionInfo.csid,'type':2},
                dataType:'json',
                success:function(data){
                    if(data.status==1){
                        notify.success('恭喜你，本节课已经学完！',"vcenter")
                    }
                }
            })
        });
        player.on('timeupdate',function(){
            var  seek= parseInt(player.getCurrentTime());
            var  duration=sectionInfo.timer>0?sectionInfo.timer:300;
            if(seek>seeked  && seek % duration==0) {
                seeked=seek;
                learnseek=seek;
                $.ajax({
                    url: "/index/Course/addduration",
                    type: "post",
                    data: {'sid': sectionInfo.id, 'cid': sectionInfo.csid, 'type': 2, 'duration': duration,'seek':seek},
                    dataType: 'json',
                    success: function (data) {
                        if(data.code==0){
                            notify.success('已经记录学习时间')
                        }
                    }
                });
            }
        });
    });
}

