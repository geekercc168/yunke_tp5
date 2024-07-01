function SelectTag(t) {
    this.child = t.child || ".default",
        this.over = t.over || "on",
        this.all = t.all || ".all",
        this.init()
}
$.extend(SelectTag.prototype, {
    init: function() {
        var t = this;
        t._bindEvent(),
            t._select()
    },
    _bindEvent: function() {
        var t = this;
        $(t.child).click(function(e) {
            e.preventDefault();
            var i = t._removeURLParameter(window.location.href,'page')
                , n = $(this).attr("rel")
                , r = $(this).attr("name");

            $(this).hasClass(t.over) || (window.location.href = t._changeURLPar(i, r, n))
        }),
            $(t.all).click(function(e) {
                e.preventDefault();
                var i = i = t._removeURLParameter(window.location.href,'page')
                    , n = $(this).attr("name");
                $("[name=" + n + "]").removeClass(t.over),
                    $(this).addClass(t.over),
                    window.location.href = t._delUrlPar(i, n)
            })
    },
    _delUrlPar: function(t, e) {
        var n = "";
        if (t.indexOf("?") == -1)
            return t;
        n = t.substr(t.indexOf("?") + 1);
        var r = ""
            , a = "";
        if (n.indexOf("&") != -1) {
            r = n.split("&");
            for (i in r){
                if(e=='third')
                    r[i].split("=")[0] != e && (a = a + r[i].split("=")[0] + "=" + r[i].split("=")[1] + "&");
                if(e=='second'){
                    e='third|second';
                    e.indexOf(r[i].split("=")[0]) == -1 && (a = a + r[i].split("=")[0] + "=" + r[i].split("=")[1] + "&");
                }
                if(e=='parent'){
                    return t.substr(0, t.indexOf("?"));
                }
            }
            return t.substr(0, t.indexOf("?")) + "?" + a.substr(0, a.length - 1);
        }
        return r = n.split("="),
            r[0] == e ? t.substr(0, t.indexOf("?")) : t
    },
    _changeURLPar: function(t, e, i) {
        var n = this
            , r = e + "=([^&]*)"
            , a = e + "=" + i
            , s = encodeURI(n._getQueryString(e))
            , f= t.match(r) ? t = t.replace(s, i) : t.match("[?]") ? t + "&" + a : t + "?" + a;

        if (t.indexOf("?") == -1)
            return f;
        var c = f.substr(t.indexOf("?") + 1)
            , h = c.split("&")
            , b='';
        for (i in h){
            if(e=='third'){
                return f;
            }
            if(e=='second'){
                h[i].split("=")[0] != 'third' && (b = b + h[i].split("=")[0] + "=" + h[i].split("=")[1] + "&");
            }
            if(e=='parent'){
                n='third|second';
                n.indexOf(h[i].split("=")[0]) == -1 && (b = b + h[i].split("=")[0] + "=" + h[i].split("=")[1] + "&");
            }
        }
        return f.substr(0, f.indexOf("?")) + "?" + b.substr(0, b.length - 1);
    },
    _getQueryString: function(t) {
        var e = new RegExp("(^|&)" + t + "=([^&]*)(&|$)","i")
            , i = window.location.search.substr(1).match(e);
        return null != i ? decodeURI(i[2]) : null
    },
    _select: function() {
        var t = this
            , e = window.location.href;
        $(t.child).each(function() {
            var i = $(this).attr("name") + "=" + $(this).attr("rel")
                , n = new RegExp(encodeURI(i),"g");
            if (n.test(e)) {
                $(this).addClass(t.over);
                var r = $(this).attr("name");
                $("[name=" + r + "]").eq(0).removeClass(t.over)
            } else
                $(this).removeClass(t.over)
        })
    },
    _removeURLParameter:function(url, parameter) {
        var urlparts = url.split('?');
        if (urlparts.length >= 2) {
            var prefix = encodeURIComponent(parameter) + '=';
            var pars = urlparts[1].split(/[&;]/g);
            for (var i = pars.length; i-- > 0;) {
                if (pars[i].lastIndexOf(prefix, 0) !== -1) {
                    pars.splice(i, 1);
                }
            }
            return urlparts[0] + (pars.length > 0 ? '?' + pars.join('&') : '');
        }
        return url;
    }
});
