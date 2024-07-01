var E = window.wangEditor;
var editor1 = new E('#tigan');
editor1.config.onchange = function (html) {
    $('#question').val(html)
};
editor1.config.zIndex = 1;
editor1.config.uploadFileName = 'image';
editor1.config.uploadImgServer = '/api/Uploader/wangEditorUp';
editor1.config.pasteIgnoreImg = false
editor1.create();

var editor2 = new E('#describeeditor');
editor2.config.onchange = function (html) {
    $('#questiondescribe').val(html)
};
editor2.config.zIndex = 1;
editor2.config.uploadFileName = 'image';
editor2.config.uploadImgServer = '/api/Uploader/wangEditorUp';
editor2.create();
$('#questiondescribe').val(editor2.txt.html());
$(function(){
    var questiontype=$('#questiontype option:selected').val();
    if(questiontype){
        if(questiontype.length >0){
            changeType(questiontype);
        }
    }
})

function changeType(typemark){
    if (typemark.indexOf("SingleSelect")>0) {
        $('#questionselectbox').removeClass('layui-hide');
        $('#questionselectnumberbox').removeClass('layui-hide');
        $('#answerbox').removeClass('layui-hide');
        $('#answerbox_1').removeClass('layui-hide');
        $('#other-btn').removeClass('layui-hide');
        $('#timao-btn').addClass('layui-hide');
        $('#answerbox_2').addClass('layui-hide');
        $('#answerbox_3').addClass('layui-hide');
        $('#answerbox_4').addClass('layui-hide');
        $('#answerbox_5').addClass('layui-hide');
        var editor3 = new E('#questionselecteditor');
        editor3.config.onchange = function (html) {
            $('#questionselect').val(html)
        };
        editor3.config.zIndex = 1;
        editor3.config.uploadFileName = 'image';
        editor3.config.uploadImgServer = '/api/Uploader/wangEditorUp';
        editor3.create();
        $('#questionselect').val(editor3.txt.html());
    }
    if (typemark.indexOf("MultiSelect")>0) {
        $('#questionselectbox').removeClass('layui-hide');
        $('#questionselectnumberbox').removeClass('layui-hide');
        $('#answerbox').removeClass('layui-hide');
        $('#other-btn').removeClass('layui-hide');
        $('#timao-btn').addClass('layui-hide');
        $('#answerbox_1').addClass('layui-hide');
        $('#answerbox_2').removeClass('layui-hide');
        $('#answerbox_3').addClass('layui-hide');
        $('#answerbox_4').addClass('layui-hide');
        $('#answerbox_5').addClass('layui-hide');
        var editor4 = new E('#questionselecteditor');
        editor4.config.onchange = function (html) {
            $('#questionselect').val(html)
        };
        editor4.config.zIndex = 1;
        editor4.config.uploadFileName = 'image';
        editor4.config.uploadImgServer = '/api/Uploader/wangEditorUp';
        editor4.create();
        $('#questionselect').val(editor4.txt.html());
    }
    if (typemark.indexOf("FillInBlanks")>0) {
        $('#questionselectbox').addClass('layui-hide');
        $('#questionselectnumberbox').addClass('layui-hide');
        $('#answerbox').removeClass('layui-hide');
        $('#other-btn').removeClass('layui-hide');
        $('#timao-btn').addClass('layui-hide');
        $('#answerbox_1').addClass('layui-hide');
        $('#answerbox_2').addClass('layui-hide');
        $('#answerbox_3').addClass('layui-hide');
        $('#answerbox_4').removeClass('layui-hide');
        $('#answerbox_5').addClass('layui-hide');
        var editor5 = new E('#FillInBlankseditor');
        editor5.config.onchange = function (html) {
            $('#questionanswer4').val(html)
        };
        editor5.config.zIndex = 1;
        editor5.config.uploadFileName = 'image';
        editor5.config.uploadImgServer = '/api/Uploader/wangEditorUp';
        editor5.create();
        $('#questionanswer4').val(editor5.txt.html());
    }
    if (typemark.indexOf("TrueOrfalse")>0) {
        $('#questionselectbox').addClass('layui-hide');
        $('#questionselectnumberbox').addClass('layui-hide');
        $('#answerbox').removeClass('layui-hide');
        $('#other-btn').removeClass('layui-hide');
        $('#timao-btn').addClass('layui-hide');
        $('#answerbox_1').addClass('layui-hide');
        $('#answerbox_2').addClass('layui-hide');
        $('#answerbox_3').removeClass('layui-hide');
        $('#answerbox_4').addClass('layui-hide');
        $('#answerbox_5').addClass('layui-hide');
    }
    if (typemark.indexOf("ShortAnswer")>0) {
        $('#questionselectbox').addClass('layui-hide');
        $('#questionselectnumberbox').addClass('layui-hide');
        $('#answerbox').removeClass('layui-hide');
        $('#other-btn').removeClass('layui-hide');
        $('#timao-btn').addClass('layui-hide');
        $('#answerbox_1').addClass('layui-hide');
        $('#answerbox_2').addClass('layui-hide');
        $('#answerbox_3').addClass('layui-hide');
        $('#answerbox_4').addClass('layui-hide');
        $('#answerbox_5').removeClass('layui-hide');
        var editor6 = new E('#ShortAnswereditor');
        editor6.config.onchange = function (html) {
            $('#questionanswer5').val(html)
        };
        editor6.config.zIndex = 1;
        editor6.config.uploadFileName = 'image';
        editor6.config.uploadImgServer = '/api/Uploader/wangEditorUp';
        editor6.create();
        $('#questionanswer5').val(editor6.txt.html());
    }
    if (typemark.indexOf("TiMao")>0) {
        $('#questionselectbox').addClass('layui-hide');
        $('#other-btn').addClass('layui-hide')
        $('#timao-btn').removeClass('layui-hide');
        $('#questionselectnumberbox').addClass('layui-hide');
        $('#questiondescribe_box').addClass('layui-hide');
        $('#answerbox').addClass('layui-hide');
        $('#answerbox_1').addClass('layui-hide');
        $('#answerbox_2').addClass('layui-hide');
        $('#answerbox_3').removeClass('layui-hide');
        $('#answerbox_4').addClass('layui-hide');
        $('#answerbox_5').addClass('layui-hide');
    }
}
function replaceimg(content) {
   ajaxreplaceimg = $.ajax({
        url: '/admin/exam/replaceimg',
        type: 'post',
        data: {content:content},
        dataType: 'json',
        success: function (result) {
            return result.content;
        },
        error: function (xhr, state, errorThrown) {
            layer.msg(state + '：' + errorThrown);
        }
    });
}
form.on('radio(answer)', function(data){
    var id=$(this).attr('radioid');
    $("#signeditor"+id).addClass("done");
});
form.on('checkbox(answer)', function(data){
    var id=$(this).attr('checkbox');
    $("#signeditor"+id).addClass("done");
});
layui.use('form', function() {
    form.on('select(questiontype)', function (data) {
        var typemark=data.value;
        changeType(typemark);
    });
    form.on('select(questionknowsid)', function(data){
        var parentId=$("#chapterid").val();
        $('#knowledgebox').addClass('layui-hide');
        if(null!= parentId && ""!=parentId){
            $.getJSON("/admin/course/ajaxGetKnowledge",{id:parentId},function(myJSON){
                    var options="";
                    if(myJSON.length>0){
                        $('#knowledgebox').removeClass('layui-hide');
                        options+="<option value=''>==知识点==</option>";
                        for(var i=0;i<myJSON.length;i++){
                            options+="<option value="+myJSON[i].id+">"+myJSON[i].title+"</option>";
                        }
                        $("#knowledge").html(options);
                        form.render('select');
                    }else{
                        layer.msg('该分类下没有知识点，请先添加知识点');
                        setTimeout(function () {
                            parent.location.href="/admin/course/coursecategory"
                        }, 1000);
                    }
                }
            )
        }
    })
})
