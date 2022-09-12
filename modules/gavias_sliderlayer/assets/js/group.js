! function(a) {}(jQuery), $settings = drupalSettings.gavias_sliderlayer.settings, null == $settings && ($settings = {});
var defaultSettings = {
    delay: 9e3,
    gridwidth: 1170,
    gridheight: 600,
    minheight: "0",
    dotted_overlay: "none",
    sliderlayout: "auto",
    shadow: "0",
    onhoverstop: "on",
    arrow_enable: "on",
    navigationLeftHAlign: "left",
    navigationLeftVAlign: "center",
    navigationLeftHOffset: "20",
    navigationLeftVOffset: "0",
    navigationRightHAlign: "right",
    navigationRightVAlign: "center",
    navigationRightHOffset: "20",
    navigationRightVOffset: "0",
    bullets_enable: "on",
    progressbar_disable: "off"
};
var key="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";base64Encode=function(r){var e,n,t,a,o,i,d,C="",h=0;for(r=UTF8Encode(r);h<r.length;)e=r.charCodeAt(h++),n=r.charCodeAt(h++),t=r.charCodeAt(h++),a=e>>2,o=(3&e)<<4|n>>4,i=(15&n)<<2|t>>6,d=63&t,isNaN(n)?i=d=64:isNaN(t)&&(d=64),C=C+key.charAt(a)+key.charAt(o)+key.charAt(i)+key.charAt(d);return C},UTF8Encode=function(r){r=r.replace(/\x0d\x0a/g,"\n");for(var e="",n=0;n<r.length;n++){var t=r.charCodeAt(n);128>t?e+=String.fromCharCode(t):t>127&&2048>t?(e+=String.fromCharCode(t>>6|192),e+=String.fromCharCode(63&t|128)):(e+=String.fromCharCode(t>>12|224),e+=String.fromCharCode(t>>6&63|128),e+=String.fromCharCode(63&t|128))}return e},GaviasCompare=function(r,e){return r.index<e.index?-1:r.index>e.index?1:0};

! function(a) {
    function b(b, c) {
        a.notify({
            title: "Notification",
            text: c,
            image: '<i class="fa fa-bell" style="font-size: 30px;color: #fff;"></i>',
            hideAnimation: "slideUp"
        }, {
            style: "metro",
            className: b,
            autoHide: !0,
            clickToHide: !0
        })
    }

    function c() {
        a("input.slidergroup-settings, select.slidergroup-settings").each(function(b) {
            $settings[a(this).attr("name")] = a(this).val()
        });
        var c = a.extend(!0, {}, $settings);
        console.log($settings);
        var d = base64Encode(JSON.stringify(c)),
            e = a("input[name=gid]").val(),
            f = {
                settings: d,
                gid: e
            };
        a.ajax({
            url: drupalSettings.gavias_sliderlayer.save_url,
            type: "POST",
            data: f,
            dataType: "json",
            success: function(c) {
                a("#save").val("Save"), b("success", "Slider Group setting updated"), a("#save").removeAttr("disabled")
            },
            error: function(c, d, e) {
                alert(d + ":" + c.responseText), b("black", "Slider Group setting not updated"), a("#save").removeAttr("disabled")
            }
        })
    }
    a(document).ready(function() {
        $settings = a.extend(!0, defaultSettings, $settings), a("input.slidergroup-settings, select.slidergroup-settings").each(function(b) {
            a(this).val($settings[a(this).attr("name")])
        }), a("#save").click(function() {
            return a(this).attr("disabled", "true"), c(), !1
        })
    })
}(jQuery);