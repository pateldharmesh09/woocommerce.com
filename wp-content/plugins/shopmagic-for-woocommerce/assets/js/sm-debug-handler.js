jQuery(function(a) {
    function i() {
        e(n + 1)
    }

    function o() {
        e(n - 1)
    }

    function e(i) {
        r = i, a.ajax({
            url: ShopMagic.ajaxurl,
            method: "POST",
            data: {
                action: "shopmagic_load_detail_log_data",
                id: i,
                paramProcessNonce: ShopMagic.paramProcessNonce
            },
            beforeSend: function(i) {
                var o = a("#modal-record-log-detail");
                o.find(".spinner").addClass("is-active"), o.find(".error-icon").removeClass("error-icon-visible")
            }
        }).done(function(i, o, e) {
            if (i.length && (params = JSON.parse(i), null != params)) {
                n = r;
                var d = a("#modal-record-log-detail"),
                    l = (d.dialog({
                        title: params.title
                    }), d.find("#record-log-detail-content"));
                l.find(".record-id").find(".value").html(params.id), l.find(".record-time").find(".value").html(params.time), l.find(".record-severity").find(".value").html(params.severity), l.find(".record-source").find(".value").html(params.source), l.find(".record-description").find(".value").html(params.description)
            }
        }).fail(function(i, o, e) {
            a("#modal-record-log-detail").find(".error-icon").addClass("error-icon-visible")
        }).always(function(i, o, e) {
            a("#modal-record-log-detail").find(".spinner").removeClass("is-active")
        })
    }
    var r = -1,
        n = -1;
    window.showDetailInfo = function(r) {
        var n = a("#modal-record-log-detail");
        n.dialog({
            dialogClass: "wp-dialog",
            modal: !0,
            autoOpen: !1,
            closeOnEscape: !0,
            title: "Message is loading ... ",
            buttons: {
                Prev: function() {
                    o()
                },
                Next: function() {
                    i()
                },
                Close: function() {
                    a(this).dialog("close")
                }
            }
        }), n.dialog("open"), e(r)
    }
});