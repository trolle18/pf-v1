(function ($) {
    var currentTab;
    let SP = {
        handleTabClick: function (el) {
            let target = $(el).attr('data-tab');
            $('.onecom_tabs_panel').fadeOut('fast');
            $(el).parent().find('.active').removeClass('active');
            $(el).addClass('active');
            $('#' + target).fadeIn('fast');

        },
        formSubmission: function (formname){
        $(formname).on('click','.oc-save',function(e) {

            e.preventDefault();
            let formID = $(formname).attr('id');

            $(this).siblings('#oc_sp_spinner').removeClass('success').addClass('is_active');
            if(formID == 'sp-settings') {
                $(this).val('Saving');
                SP.protectionForm(formname, $(this));
            }else if(formID == 'sp-advanced-settings'){
                $(this).val('Saving');
                SP.advanceSettings(formname, $(this));
            }else if(formID == 'sp-diagnostics'){

                SP.spamDiagnostics(formname, $(this));
            }else if(formID == 'sp-clear-logs'){
                $(this).val('Clearing')
                SP.spamClearlogs(formname, $(this));
            }

        })
    },

    protectionForm: function(e,button) {


        let elements = document.forms["sp-protect-options"].elements,

            data = {
                action: 'oc_save_settings',
                checks:
                    {
                        oc_sp_accept: (elements["oc_sp_accept"]).checked,
                        oc_sp_referrer: (elements["oc_sp_referrer"]).checked,
                        oc_sp_long: (elements["oc_sp_long"]).checked,
                        oc_sp_short: (elements["oc_sp_short"]).checked,
                        oc_sp_bbcode: (elements["oc_sp_bbcode"]).checked,
                        oc_sp_exploit: (elements["oc_sp_exploit"]).checked,
                        oc_sp_quickres: (elements["oc_sp_quickres"]).checked,
                        oc_max_login_val: (elements["oc_max_login_val"]).value,
                        oc_block_time: (elements["oc_block_time"]).value,
                        one_sp_nonce: elements["one_sp_nonce"].value,
                    },
            }

        $.post(ajaxurl, data, function (response) {
            $(button).siblings('#oc_sp_spinner').removeClass('is_active');

            if (response.success) {

                $(button).siblings('#oc_sp_spinner').addClass('success');
                $(e).parents().find('.notice-success').hide();
                $('#oc-sp-success').fadeIn('slow');
                $(e).find('input:submit').attr('disabled', true);
                $(button).val('Save');
                SP.disableSubmit();
                setTimeout(function() {
                    $(button).siblings('#oc_sp_spinner').removeClass('success');
                }, 6000);


            }

        })


    },

        advanceSettings: function(e,button) {
            let elements = document.forms['sp-advanced-settings'].elements,
                ocSpWhitelist = elements['oc_sp_whitelistuser'].checked,
                ocSpBaduseragent = elements['oc_spbadusragent'].checked,
                ocSpUrlshort = elements['oc_sp_urlshort'].checked,
                ocSpProburl = elements['oc_sp_proburl'].checked,

                advancedData = {
                action : 'oc_save_advanced_settings',
                    oc_sp_whitelistuser :ocSpWhitelist,
                    oc_spbadusragent : ocSpBaduseragent,
                    oc_sp_urlshort : ocSpUrlshort,
                    oc_sp_proburl : ocSpProburl,
                    one_sp_nonce: elements["one_sp_nonce"].value,


                    whitelist_usernames : (elements['oc_whitelist_usernames'].value!== '') ? elements['oc_whitelist_usernames'].value.split("\n") : [],
                    whitelist_agents :elements['oc_whitelist_useragent'].value!=='' ? elements['oc_whitelist_useragent'].value.split("\n") : [],
                    url_shorteners :elements['oc_url_shorters'].value!=='' ? elements['oc_url_shorters'].value.split("\n") : [],
                    exploit_urls : elements['oc_exploit_urls'].value !== '' ? elements['oc_exploit_urls'].value.split("\n") : [],
                    };


        if(!ocSpWhitelist) {
            delete (advancedData.whitelist_usernames);
        }
        if(!ocSpBaduseragent) {
            delete (advancedData.whitelist_agents);
        }
        if(!ocSpUrlshort) {
            delete (advancedData.url_shorteners);
        }
        if(!ocSpProburl) {
            delete (advancedData.exploit_urls);
        }




        SP.executeAjaxRequest(e,advancedData,button);

        },

    executeAjaxRequest: function(e,data,button){

            $.post(ajaxurl, data, function (response) {
            $(button).siblings('#oc_sp_spinner').removeClass('is_active');

            if (response.success) {

                $(button).siblings('#oc_sp_spinner').addClass('success');
                $(e).parents().find('.notice-success').hide();
                $(e).parents().find('.advanced-settings').fadeIn('slow');
                $(e).find('input:submit').attr('disabled', true);
                $(button).val('Save');
                SP.disableSubmit();
                setTimeout(function() {
                    $(button).siblings('#oc_sp_spinner').removeClass('success');
                }, 6000);


            }

        })

    },

        spamDiagnostics: function (e,button){

            $(button).val('Checking');

            let elements = document.forms['sp-diagnostics'].elements,
                $this = $(e),
                validation_err = $this.parent().find('.oc-dg-err');

            let emptyFields = $this.find(":input").filter(function () {
                return $.trim(this.value) === "";
            });


            if (
                (emptyFields.length) === 5 &&
                (validation_err.length) == 0) {

                $this.parent().prepend('<div class="notice notice-error oc-dg-err"><p class="error">' + onespnotice.oc_notice + '</p></div>');
                $(button).siblings('#oc_sp_spinner').removeClass('is_active');
                $(button).val('Check for spam');


            } else if (
                (emptyFields.length) === 5 &&
                (validation_err.length) > 0
            ) {
                $(button).siblings('#oc_sp_spinner').removeClass('is_active');
                $(button).val('Check for spam');
                return false;

            } else {
                $(e).find('input:submit').attr('disabled', true);
                $(e).parents().find('.oc-dg-err').remove();

                    let data = {
                        action : 'oc_check_spam_diagnostics',
                        oc_validate_ip : elements['oc_validate_ip'].value,
                        oc_validate_user: elements['oc_validate_user'].value,
                        oc_validate_email: elements['oc_validate_email'].value,
                        oc_validate_user_agent: elements['oc_validate_user_agent'].value,
                        oc_validate_content: elements['oc_validate_content'].value,
                        one_sp_nonce: elements["one_sp_nonce"].value,
                }

                $.post(ajaxurl, data, function (response) {


                    $(button).siblings('#oc_sp_spinner').removeClass('is_active');

                    if (response.success) {

                        $(button).siblings('#oc_sp_spinner').addClass('success');
                        $(e).parents().find('.ocdg-results').html(response.data);
                        $(e).find('input:submit').attr('disabled', false);
                        $(e)[0].reset();
                        $(button).val('Check for spam');
                        setTimeout(function() {
                            $(button).siblings('#oc_sp_spinner').removeClass('success');
                        }, 6000);


                    }

                })
            }


        },

        spamClearlogs: function (e,button){
            $(e).find('input:submit').attr('disabled', true);

            let data = {
                action : 'oc_clear_spam_logs',
                one_sp_nonce: $(e).find(".one_sp_nonce").val(),
            }

            $.post(ajaxurl, data, function (response) {

                $(button).siblings('#oc_sp_spinner').removeClass('is_active');

                if (response.success) {

                    $(e).parents().find('.notice-success').hide();
                    $(button).siblings('#oc_sp_spinner').addClass('success');
                    $(e).parents().find('.one-sp-logs').html(response.data);
                    $(e).find('input:submit').attr('disabled', false);

                }

            })


        },
        disableSubmit: function(){
            let arrForm = [
                $('form.sp-protect-options'),
                $('form.sp-blocked-lists')
            ];
            let formObj = $.map(arrForm, function(el){return el.get()});
            $(formObj).each(function () {
                $(this).data('serialized', $(this).serialize())
            })
                .on('change input', function () {
                    $(this)
                        .find('input:submit')
                        .attr('disabled', $(this).serialize() == $(this).data('serialized'));

                })
                .find('input:submit, button:submit')
                .attr('disabled', true);
        }



    }

$(document).ready(function ( ) {



    $('.onecom_tab').click(function (e) {
        SP.handleTabClick($(this));
        currentTab = $('.onecom_tab.active').data('tab');
    });

    $('.oc-duration-filter').change(function (e) {
        var $this = $(this);
        if ($this.hasClass('disabled-section')) {
            return false;
        }
        $('.filter-summary ul li').removeClass('active');
        if (!$this.parent().hasClass('active')) {
            $this.parent().addClass('active');
        }
        // console.log($(this).data('duration'))
        var data = {
            action: 'oc_get_summary',
            duration: $('option:selected', this).data('duration')
        };
        $('span#oc_switch_spinner').css('visibility', 'visible');
        var total_count = $('.oc-summary-body').find('.oc_total_count'),
            comment_count = $('.oc-summary-body').find('.oc_comment_count'),
            registration_count = $('.oc-summary-body').find('.oc_registration_count'),
            failed_login_count = $('.oc-summary-body').find('.oc_failed_login_count'),
            other_count = $('.oc-summary-body').find('.oc_other_count');

        $.post(ajaxurl, data, function (response) {
            total_count.html(response.total_count);
            comment_count.html(response.comments_count);
            registration_count.html(response.registration_count);
            failed_login_count.html(response.failed_login);
            other_count.html(response.other_count);
            $('#oc_switch_spinner').css('visibility', 'hidden');

        });
    });


    var blocked_lists = $('.sp-blocked-lists'),
        whitelist = blocked_lists.find('#spbadusragent'),
        urlshortener = blocked_lists.find('#spurlshort'),
        proburl = blocked_lists.find('#spprobchk'),
        whitelist_users = blocked_lists.find('#spwhitelistusername'),
        username_textarea = blocked_lists.find('.oc_whitelist_usernames'),
        useragent_textarea = blocked_lists.find('.oc-whitelist-useragent'),
        urlshorteners_textarea = blocked_lists.find('.oc-url-shorters'),
        exploit_url_textarea = blocked_lists.find('.oc-exploit-urls'),
        limitlogin = $('#spquickres'),
        max_login_val = $('.oc_max_login_val'),
        block_time = $('.oc_block_time');


    // events trigger on page load
    if (whitelist_users && whitelist_users.prop('checked') === true) {
        username_textarea.prop('disabled', false).css('background', '#ffffff');
    } else if (whitelist_users && whitelist_users.prop('checked') !== true) {
        username_textarea.prop('disabled', true).css('background', '#f0f0f1');
    }

    if (whitelist && whitelist.prop('checked') === true) {
        useragent_textarea.prop('disabled', false).css('background', '#ffffff');
    } else if (whitelist && whitelist.prop('checked') !== true) {
        useragent_textarea.prop('disabled', true).css('background', '#f0f0f1');
    }

    if (urlshortener && urlshortener.prop('checked') === true) {
        urlshorteners_textarea.prop('disabled', false).css('background', '#ffffff');
    } else if (urlshortener && urlshortener.prop('checked') !== true) {
        urlshorteners_textarea.prop('disabled', true).css('background', '#f0f0f1');
    }

    if (proburl && proburl.prop('checked') === true) {
        exploit_url_textarea.prop('disabled', false).css('background', '#ffffff');
    } else if (proburl && proburl.prop('checked') !== true) {
        exploit_url_textarea.prop('disabled', true).css('background', '#f0f0f1');
    }

    if (limitlogin && limitlogin.prop('checked') === true) {
        max_login_val.prop('disabled', false).css('background', '#ffffff');
        block_time.prop('disabled', false).css('background', '#ffffff');
    } else if (limitlogin && limitlogin.prop('checked') !== true) {
        max_login_val.prop('disabled', true).css('background', '#f0f0f1');
        block_time.prop('disabled', true).css('background', '#f0f0f1');
    }

    // page load events end //

// events which triggers on change of the toggle switches

    whitelist_users.on('change', function () {
        var checked = $(this).prop('checked');
        username_textarea.prop('disabled', !checked);
        if (!checked) {
            username_textarea.css('background', '#f0f0f1');
        } else {
            username_textarea.css('background', '#ffffff');
        }
    });


    whitelist.on('change', function () {
        var checked = $(this).prop('checked');
        useragent_textarea.prop('disabled', !checked);
        if (!checked) {
            useragent_textarea.css('background', '#f0f0f1');
        } else {
            useragent_textarea.css('background', '#ffffff');
        }
    });
    urlshortener.on('change', function () {
        var checked = $(this).prop('checked');
        urlshorteners_textarea.prop('disabled', !checked);
        if (!checked) {
            urlshorteners_textarea.css('background', '#f0f0f1');
        } else {
            urlshorteners_textarea.css('background', '#ffffff');
        }
    });
    proburl.on('change', function () {
        var checked = $(this).prop('checked');
        exploit_url_textarea.prop('disabled', !checked).css('background', '#f0f0f1');
        if (!checked) {
            exploit_url_textarea.css('background', '#f0f0f1');
        } else {
            exploit_url_textarea.css('background', '#ffffff');
        }
    });

    $('#spquickres').on('change', function () {
        var checked = $(this).prop('checked');
        max_login_val.prop('disabled', !checked).css('background', '#f0f0f1');
        block_time.prop('disabled', !checked).css('background', '#f0f0f1');
        if (!checked) {
            max_login_val.css('background', '#f0f0f1');
            block_time.css('background', '#f0f0f1');
        } else {
            max_login_val.css('background', '#ffffff');
            block_time.css('background', '#ffffff');
        }
    });

    // on change events end


    $('.oc-show-modal').on('click', function (e) {

        e.preventDefault();

        const bodylist = [
            "Protects the website from spam comments, spam registration, spambots, and other spammers. ",
            "Customizable protection settings, spam logs, and other options.",
        ];
        const popupContent = {
            "title": "Get Spam Protection plugin with \n Managed WordPress",
            "top-desc": "Spend less time worrying about your site’s safety and more time growing your business with one.com Managed WordPress.",
            "footer-desc":"Managed WordPress includes other great features such as Vulnerability Monitor and Upgrade Manager",
            "bodylist":bodylist
        };

        jQuery.ajax({
            url:ajaxurl,//NOTE: Change ajaxurl as per your variable
            type: "POST",
            data: {
                action: 'show_plugin_dependent_popup',//NOTE:Same value required
                popupContent: popupContent//NOTE:Same value required
            },
            success: function(response){
                var result = response;
                if (typeof result.success != 'undefined' && result.success === true) {
                    //success message
                    //NOTE: Change premium condition as per your variable
                    // if(!parseInt(ocvmObj.isPremium)){
                        $('#oc_um_overlay').html(result.data);
                        var referrer = location.search;
                        $('#oc_um_overlay').show();
                        ocSetModalData({
                            isPremium: true,//NOTE: Change premium condition as per your variable
                            feature: 'spam_protection',//NOTE: Change feature value as per your plugin dependent call
                            featureAction: 'getStarted',//NOTE: Change featureAction value as per your plugin dependent call
                            referrer: referrer
                        });
                    }
                // } else {
                //     //failed message
                // }
            },
            error: function (xhr, textStatus, errorThrown) {
                //error log
            }
        });


    })


    const checkDisabled = document.getElementsByClassName('disabled-section');

    if (checkDisabled.length === 0) {

// Show floating save button if regular button is not in viewport (via JS Observer API)
        let observer = new IntersectionObserver(function (entries) {
            // isIntersecting is true when element and viewport are overlapping else false
            if (entries[0].isIntersecting === true) {
                $('.oc-sp-float-btn').hide();
                $('.float-spinner').removeClass('success').hide();
            } else {
                $('.oc-sp-float-btn').show();
                $('.float-spinner').show();
            }
        }, {threshold: [0]});

        observer.observe(document.querySelector("#onecom-sp-ui #settings .oc-save"));
        observer.observe(document.querySelector("#onecom-sp-ui #advanced_settings .oc-save"));

    }
    // disable submit button if no change in settings form
    let settingsForm = $('#onecom-sp-ui').find('form.sp-protect-options'),
        advanceSettingsForm = $('#onecom-sp-ui').find('form.sp-blocked-lists'),
        spamDiagnosticsForm = $('#onecom-sp-ui').find('form.sp-diagnostics'),
        spamClearLogs = $('#onecom-sp-ui').find('form#sp-clear-logs');

    SP.formSubmission(settingsForm);
    SP.formSubmission(advanceSettingsForm);
    SP.formSubmission(spamDiagnosticsForm);
    SP.formSubmission(spamClearLogs);
    SP.disableSubmit();







})

})(jQuery)



