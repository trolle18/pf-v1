(function ($) {
    // Upon cancel/close popup, update popup status via ajax
    $("#oc_login_masking_overlay_wrap .oc_cancel_btn, #oc_login_masking_overlay_wrap .oc_login_masking_close").click(function () {
        $("#oc_login_masking_overlay").hide();
        $(".loading-overlay.fullscreen-loader").removeClass('show');
        let args = {
            'event_action': 'close_modal',
            'item_category': 'blog',
            'item_name': 'auto_login_protection_modal'
        };

        oc_push_stats_by_js(args);
        ajax_popup_update();
    });

    $("#oc_login_masking_overlay_wrap .oc_up_btn").click(function (){


        let args = {
            'event_action': 'click_control_panel_button',
            'item_category': 'blog',
            'item_name': 'auto_login_protection_modal'
        };

        oc_push_stats_by_js(args);


    })

    // Save session token along with never_show state
    function ajax_popup_update() {
        let data = {
            action: 'update_popup_info',
            cancel_action_time: Date.now()/1000
        };
        $.post(ajaxurl, data, function (res) {
            if (res.status === 'success') {
                console.log("ALP popup info saved.");
            } else {
                console.log("Failed to save ALP popup info.");
            }
        });
    }
})(jQuery);