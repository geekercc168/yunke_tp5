var options = {};
var recordOptions = {
    minFPS:1,
    maxFPS:1,
    resolution:0.8,
    autoResolution: false,
    autoFPS: false,
    maxResolutionLevel: 1
};
var virtualBackgroundImages=['/static/default/backgroundImg/default1.jpeg',
    '/static/default/backgroundImg/default2.jpeg',
    '/static/default/backgroundImg/default3.jpeg',
    '/static/default/backgroundImg/default4.jpeg',
    '/static/default/backgroundImg/default5.jpeg',
    '/static/default/backgroundImg/default6.jpeg',
    '/static/default/backgroundImg/default7.jpeg'
]

$(function () {
    options.channelid = $("#channel").val();
    options.uid = $("#uid").val();
    options.record=options.uid.indexOf("810725") !=-1?false:true
    options.Uuid =options.channelid + options.uid;
    if(IsPC()){
        options.platform='PC'
    }else{
        options.platform='H5'
    }
    getParam(options.Uuid);
    $.when(ajaxGetParam).done(function(){
        AgoraEduSDK.config({
            appId: options.appid,
            region: 'CN'
        });
        AgoraEduSDK.launch(document.querySelector('#root'), {
            userUuid: options.Uuid,
            userName: options.userName,
            roomUuid: options.channelid,
            roleType: options.role,
            roomType: options.live_type,
            roomSubType: 0,
            roomName: options.title,
            pretest: options.record,
            rtmToken: options.token,
            language: 'zh',
            duration: 60 * options.duration,
            recordUrl: options.url,
            appId:options.appid,
            recordOptions:recordOptions,
            uiMode: options.uiMode,
            platform:options.platform,
            virtualBackgroundImages:virtualBackgroundImages,
            listener: (evt, args) => {
                if (evt === 1) {
                    nowStudy()
                    if(options.role==1){
                        startClass()
                    }
                }
                if(evt === 2){
                    if(options.role==1){
                        getRecordUrl()
                        endRecord()
                        endClass()
                        studied()
                        window.history.go(-1);
                    }else{
                        studied();
                    }
                }
            },
        });
    })
    async function  getParam(uid){
        ajaxGetParam = $.ajax({
            url: '/index/course/getRtmParam',
            type: 'post',
            data: {uid:uid,channelid:options.channelid},
            dataType: 'json',
            success: function (result) {
                if (result.code === 0) {
                    options.appid=result.agoraAppid
                    options.token=result.token
                    options.duration=result.duration
                    options.title=result.title
                    options.userName=result.userName
                    options.role=result.role
                    options.live_type=result.live_type
                    options.uiMode=result.uiMode
                    options.sid=result.sid
                    options.cid=result.cid
                    options.url = window.location.href+'?token='+result.urltoken;
                }else{
                    layer.msg(result.msg);
                    return false;
                }
            },
            error: function (xhr, state, errorThrown) {
                layer.msg(state + '：' + errorThrown);
            }
        });
    }
    function startClass(){
        $.ajax({
            url: '/index/course/startClass',
            type: 'post',
            data: {roomUuid:options.channelid,uid:options.Uuid,token:options.token},
            dataType: 'json',
            success: function (result) {
                startRecord()
            }
        });
    }
    function startRecord(){
        var recordurl=options.url+'?userUuid='+options.Uuid+'?roomUuid='+options.channelid+'?roomType='+options.live_type+'?roleType=0'+'?pretest=false'+'?rtmToken='+options.token+'?language=zh'+'?appId='+options.appid
        $.ajax({
            url: '/index/course/startRecord',
            type: 'post',
            data: {url:recordurl,roomUuid:options.channelid,uid:options.Uuid,token:options.token,states:1},
            dataType: 'json',
            success: function (result) {
                updataRecord()
            }
        });
    }
    function readyRecord(){
        $.ajax({
            url: '/index/course/readyRecord',
            type: 'post',
            data: {roomUuid:options.channelid,uid:options.Uuid,token:options.token,states:1},
            dataType: 'json',
            success: function (result) {
            }
        });
    }
    function updataRecord(){
        setTimeout(function () {
            $.ajax({
                url: '/index/course/updataRecord',
                type: 'post',
                data: {roomUuid:options.channelid,uid:options.Uuid,token:options.token},
                dataType: 'json',
                success: function (result) {
                    readyRecord()
                }
            });
        }, 1000*20);
    }
    function endRecord(){
        var recordurl=options.url+'?userUuid='+options.Uuid+'?roomUuid='+options.channelid+'?roomType='+options.live_type+'?roleType=0'+'?pretest=false'+'?rtmToken='+options.token+'?language=zh'+'?appId='+options.appid
        $.ajax({
            url: '/index/course/startRecord',
            type: 'post',
            data: {url:recordurl,roomUuid:options.channelid,uid:options.Uuid,token:options.token,states:0},
            dataType: 'json',
            success: function (result) {
            }
        });
    }
    function getRecordUrl(){
        $.ajax({
            url: '/index/course/getRecordUrl',
            type: 'post',
            data: {roomUuid:options.channelid,uid:options.Uuid,token:options.token},
            dataType: 'json',
            success: function (result) {

            }
        });
    }
    function endClass(){
        $.ajax({
            url: '/index/course/endClass',
            type: 'post',
            data: {roomUuid:options.channelid,uid:options.Uuid,token:options.token},
            dataType: 'json',
            success: function (result) {
            }
        });
    }
    function IsPC() {
        var userAgentInfo = navigator.userAgent;
        var Agents = ["Android", "iPhone",
            "SymbianOS", "Windows Phone",
            "iPad", "iPod"];
        var flag = true;
        for (var v = 0; v < Agents.length; v++) {
            if (userAgentInfo.indexOf(Agents[v]) > 0) {
                flag = false;
                break;
            }
        }
        return flag;
    }

})