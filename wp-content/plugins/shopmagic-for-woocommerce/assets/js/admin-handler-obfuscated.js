jQuery(function(a) {
    if (a("#_shopmagic_edit_page").length) {
        a("#shopmagic_placeholders_metabox").length && a("#shopmagic_placeholders_metabox").hide(), 
        a.fn.tinymce_textareas = function() {};
        var e = [],
            n = [],
            t = [],
            i = !1,
            o = function() {
                a("#_event").val().length && a.ajax({
                    url: ShopMagic.ajaxurl,
                    method: "POST",
                    data: {
                        action: "shopmagic_load_event_params",
                        event_slug: a("#_event").val(),
                        post: a("#post_ID").val(),
                        paramProcessNonce: ShopMagic.paramProcessNonce
                    },
                    beforeSend: function(e) {
                        a("#shopmagic_event_metabox .spinner").addClass("is-active"), a("#shopmagic_event_metabox .error-icon").removeClass("error-icon-visible")
                    }
                }).done(function(n, t, i) {
                    n.length ? (params = JSON.parse(n), a("#event-config-area").html(params.event_box), 
                    	a("#event-desc-area .content").html(params.description_box),
                    	a("#_shopmagic_placeholders_area").html(params.placeholders_box), 
                    	a("#shopmagic_placeholders_metabox").show(), 
                    	e = params.data_domains, _()) : a("#shopmagic_placeholders_metabox").hide()
                }).fail(function(e, n, t) {
                    a("#shopmagic_event_metabox .error-icon").addClass("error-icon-visible")
                }).always(function(e, n, t) {
                    a("#shopmagic_event_metabox .spinner").removeClass("is-active")
                })
            };
        a("#_event").change(o);

        var e = [],
            n = [],
            t = [],
            i = !1,
        filterChange = function() {
                a("#_filter").val().length && a.ajax({
                    url: ShopMagic.ajaxurl,
                    method: "POST",
                    data: {
                        action: "shopmagic_load_filter_params",
                        event_slug: a("#_filter").val(),
                        post: a("#post_ID").val(),
                        filterParamProcessNonce: ShopMagic.filterParamProcessNonce
                    },
                    beforeSend: function(e) {
                        a("#shopmagic_filter_metabox .spinner").addClass("is-active"), a("#shopmagic_filter_metabox .error-icon").removeClass("error-icon-visible")
                    }
                }).done(function(n, t, i) {
                    n.length ? (params = JSON.parse(n), 
                        //a("#filter-config-area").html(params.filter), 
                        a("#filter-desc-area .content").html(params.description_box)
                        //a("#filter-filter-area .filter-content").html(params.filter_box), 
                }).fail(function(e, n, t) {
                    a("#shopmagic_filter_metabox .error-icon").addClass("error-icon-visible")
                }).always(function(e, n, t) {
                    a("#shopmagic_filter_metabox .spinner").removeClass("is-active")
                })
            };
        a("#_filter").change(filterChange);

        var c = function(e, n) {
                a("#action-area-" + e + " .spinner").addClass("is-active"), a("#action-area-" + e + " .error-icon").removeClass("error-icon-visible"), t.push({
                    id: e,
                    obj: n
                }), r()
            },
            r = function() {
                if (i === !1 && t.length > 0) {
                    var a = t.shift();
                    s(a.id, a.obj)
                }
            },
            s = function(e, t) {
                var o = t;
                a(o).val().length && a.ajax({
                    url: ShopMagic.ajaxurl,
                    method: "POST",
                    data: {
                        action: "shopmagic_load_action_params",
                        action_slug: a(o).val(),
                        action_id: e,
                        post: a("#post_ID").val(),
                        editor_initialized: window.SM_EditorInitialized === !0,
                        paramProcessNonce: ShopMagic.paramProcessNonce
                    },
                    beforeSend: function(a) {
                        i = !0
                    }
                }).done(function(t, i, o) {
                    t.length && (params = JSON.parse(t), n[e] = params.data_domains, a("#action-config-area-" + e).html(params.action_box).tinymce_textareas(), _())
                }).fail(function(n, t, i) {
                    a("#action-area-" + e + " .error-icon").addClass("error-icon-visible")
                }).always(function(n, t, o) {
                    a("#action-area-" + e + " .spinner").removeClass("is-active"), i = !1, r()
                })
            },
            l = function(e) {
                e.stopPropagation();
                var n = this,
                    t = a(n).parent().find(".action_number").text() - 1;
                c(t, n)
            },
            _ = function() {
                for (var t = 0; t < n.length; t++)
                    if (n[t].length > 0) {
                        for (var i = !0, o = 0; o < n[t].length; o++)
                            if (e.indexOf(n[t][o]) == -1) {
                                i = !1;
                                break
                            }
                        i ? (a("#action-area-" + t).removeClass("wrong_action"), a("#action-area-" + t + " .wrong_action_alert").remove()) : a("#action-area-" + t + " .wrong_action_alert").length || (a("#action-area-" + t).addClass("wrong_action"), a("<div class='wrong_action_alert'><span class='dashicons dashicons-dismiss'></span><span class='alert_info'>Some data required by this action is unavailable in the event and the action may not work correctly</span></div>").insertBefore("#action-area-" + t + " > h2"))
                    }
            };
        window.addNewAction = function() {
            var e = a("#action-area-stub").clone().insertAfter(".action-form-table:last");
            return e.attr("id", "action-area-" + nextActionIndex), e.find("#action-config-area-stub").attr("id", "action-config-area-" + nextActionIndex), e.find("#_action_stub").attr("id", "_actions_" + nextActionIndex + "_action").attr("name", "actions[" + nextActionIndex + "][_action]"), e.find(".action_main_select").change(l), e.find(".action_main_select").click(actionSelClick), e.find(".action_number").html(nextActionIndex + 1), e.find("#_action_title_stub").attr("id", "_action_title_" + nextActionIndex), e.find("#action_title_label_stub").attr("id", "action_title_label" + nextActionIndex + "_action").attr("for", "action_title_input_" + nextActionIndex), e.find("#action_title_stub").attr("id", "action_title_input_" + nextActionIndex).attr("name", "actions[" + nextActionIndex + "][_action_title]").on("input", titleChange), elem_suffix = "occ", elem_attribs = ["id", "class", "name", "for"], change_suffix_for(e, elem_attribs, elem_suffix, nextActionIndex), nextActionIndex++, !1
        }, window.change_suffix_for = function(a, e, n, t) {
            jQuery.each(e, function(e, i) {
                a.find("[" + i + "*='" + n + "']").each(function(a) {
                    old_id_name = jQuery(this).attr(i), new_id_name = old_id_name.replace(n, t), jQuery(this).attr(i, new_id_name)
                })
            })
        }, window.actionSelClick = function(a) {
            a.stopPropagation()
        }, window.removeAction = function(e) {
            return a(e).parent().parent().parent().parent().parent().parent().remove(), !1
        }, window.titleChange = function(e) {
            var n = a(this).attr("id").split("_")[3];
            a("#_action_title_" + n).text(a(this).val())
        }, a(".action_main_select").change(l), a(".action_main_select").click(actionSelClick), a(".action_title_input").on("input", titleChange), a(function() {
            o();
            var e = a('*[class^="action_main_select"]');
            e.trigger("change")
        }), window.loadEmailTemplate = function(e) {
            var n = a("#predefined_block_" + e).val();
            return a.ajax({
                url: ShopMagic.ajaxurl,
                method: "POST",
                data: {
                    action: "sm_sea_load_email_template",
                    template_slug: n,
                    paramProcessNonce: ShopMagic.paramProcessNonce
                },
                beforeSend: function(n) {
                    a(".email_templates_" + e + " .spinner").addClass("is-active"), a(".email_templates_" + e + " .error-icon").removeClass("error-icon-visible")
                }
            }).done(function(a, n, t) {
                tinymce.execCommand("mceFocus", !1, e), tinymce.activeEditor.execCommand("mceInsertContent", !1, a)
            }).fail(function(n, t, i) {
                a(".email_templates_" + e + " .error-icon").addClass("error-icon-visible")
            }).always(function(n, t, i) {
                a(".email_templates_" + e + " .spinner").removeClass("is-active")
            }), !1
        }
    }
});