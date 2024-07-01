layui.define('fly', function(exports){
  var $ = layui.jquery;
  var layer = layui.layer;
  var util = layui.util;
  var laytpl = layui.laytpl;
  var form = layui.form;
  var fly = layui.fly;
  var gather = {}, dom = {
    jieda: $('#jieda')
    ,content: $('#L_content')
    ,jiedaCount: $('#jiedaCount')
  };
  //求解管理
  gather.jieAdmin = {
    //删求解
    del: function(div){
      layer.confirm('确认删除该主题么？', function(index){
        layer.close(index);
        fly.json('/index/forum/deltopic/', {
          id: div.data('id')
        }, function(res){
            layer.msg(res.msg);
            setTimeout(function(){
              location.href = res.url
            },1000)

        });
      });
    }
    //设置置顶、状态
    ,set: function(div){
      var othis = $(this);
      if(othis.attr('rank')==1){
          var rank=0
      }else{
          var rank=1
      }
      fly.json('/index/forum/set/', {
        id: div.data('id')
        ,rank: rank
        ,field: othis.attr('field')
      }, function(res){
        if(res.status === 0){
            layer.msg(res.msg);
            setTimeout(function(){
                location.reload();
            },1000)

        }
      });
    }

    //收藏
    ,collect: function(div){
      var othis = $(this), type = othis.data('type');
      fly.json('/collection/'+ type +'/', {
        cid: div.data('id')
      }, function(res){
        if(type === 'add'){
          othis.data('type', 'remove').html('取消收藏').addClass('layui-btn-danger');
        } else if(type === 'remove'){
          othis.data('type', 'add').html('收藏').removeClass('layui-btn-danger');
        }
      });
    }
  };

  $('body').on('click', '.jie-admin', function(){
    var othis = $(this), type = othis.attr('type');
    gather.jieAdmin[type] && gather.jieAdmin[type].call(this, othis.parent());
  });

  //解答操作
  gather.jiedaActive = {
    zan: function(li){ //赞
      var othis = $(this), ok = othis.hasClass('zanok');
      fly.json('/index/forum/zan/', {
        ok: ok
        ,id: li.data('id')
      }, function(res){
        if(res.status === 0){
          var zans = othis.find('em').html()|0;
          /*othis[ok ? 'removeClass' : 'addClass']('zanok');*/
          othis.find('em').html(ok ? (++zans) : (++zans));
          layer.msg(res.msg);
        } else {
          layer.msg(res.msg);
        }
      });
    }
    ,reply: function(li){ //回复
      var val = dom.content.val();
      var aite = '@'+ li.find('.fly-detail-user cite').text().replace(/\s/g, '');
      dom.content.focus()
      if(val.indexOf(aite) !== -1) return;
      dom.content.val(aite +' ' + val);
    }
    ,accept: function(li){ //采纳
      var othis = $(this);
      layer.confirm('是否采纳该回答为最佳答案？', function(index){
        layer.close(index);
        fly.json('/index/forum/accept/', {
          id: li.data('id')
        }, function(res){
          if(res.status === 0){
            $('.jieda-accept').remove();
            li.addClass('jieda-daan');
            li.find('.detail-about').append('<i class="iconfont icon-caina" title="最佳答案"></i>');
            layer.msg(res.msg);
          } else {
            layer.msg(res.msg);
          }
        });
      });
    }
    ,edit: function(li){ //编辑
      fly.json('/index/forum/getreply/', {
        id: li.data('id')
      }, function(res){
        layer.prompt({
          formType: 2
          ,value: res.content
          ,maxlength: 100000
          ,title: '编辑回帖'
          ,area: ['728px', '300px']
          ,success: function(layero){
            fly.layEditor({
              elem: layero.find('textarea')
            });
          }
        }, function(value, index){
          fly.json('/index/forum/editreply/', {
            id: li.data('id')
            ,content: value
          }, function(res){
            if(res.status==0){
                layer.msg(res.msg);
                layer.close(index);
                li.find('.detail-body').html(fly.content(value));
            }else{
                layer.msg(res.msg);
            }
          });
        });
      });
    }
    ,del: function(li){ //删除
      layer.confirm('确认删除该回答么？', function(index){
        layer.close(index);
        fly.json('/index/forum/delreply/', {
          id: li.data('id')
        }, function(res){
          if(res.status === 0){
            //如果删除了最佳答案
            if(li.hasClass('jieda-daan')){
              $('.jie-status').removeClass('jie-status-ok').text('求解中');
            }
              layer.msg(res.msg);
              setTimeout(function(){
                  location.reload();
              },1000)
          } else {
            layer.msg(res.msg);
          }
        });
      });    
    }
  };

  $('.jieda-reply span').on('click', function(){
    var othis = $(this), type = othis.attr('type');
    gather.jiedaActive[type].call(this, othis.parents('li'));
  });


  //定位分页
  if(/\/page\//.test(location.href) && !location.hash){
    var replyTop = $('#flyReply').offset().top - 80;
    $('html,body').scrollTop(replyTop);
  }

  exports('jie', null);
});