layui.config({
    base: '../../static/libs/layui/lay/forum/'
}).extend({
    fly: 'index'
}).use(['fly','jie'], function(){
    var fly = layui.fly;
    $('.detail-body').each(function(){
        var othis = $(this), html = othis.html();
        othis.html(fly.content(html));
    });
});