jQuery(document).ready(function () {

    // Disable premium fields for non-premium (or downgraded package)
    jQuery(".oc-non-premium #dev_mode_duration").prop('disabled', true);
    jQuery(".oc-non-premium #oc_dev_duration_save").prop('disabled', true);
    jQuery(".oc-non-premium #exclude_cdn_data").prop('disabled', true);
    jQuery(".oc-non-premium #oc_exclude_cdn_data_save").prop('disabled', true);
    
    jQuery('#pc_enable').change(function () {
        ocSetVCState();
    });
    jQuery('#oc_ttl_save').click(function(){
        if (oc_validate_ttl()){
            oc_update_ttl();    
        }
    });
    jQuery('#oc_dev_duration_save').click(function(){
        if (oc_validate_dev_duration()){
            oc_update_dev_duration();  
        }
    });
    jQuery('#oc_exclude_cdn_data_save').click(function(){
        if (oc_validate_exclude_cdn_data()){
            oc_update_exclude_cdn_data();  
        }
    });
    
    jQuery('#cdn_enable').change(function (){
        ocSetCdnState();
    });
    jQuery('#dev_mode_enable').change(function (){
        jQuery('#dev_mode_duration').removeClass('oc_error');
        ocSetDevMode();
    });
    jQuery('#exclude_cdn_enable').change(function (){
        jQuery('#exclude_cdn_data').removeClass('oc_error');
        ocExcludeCDNState();
    });
});

function oc_toggle_state(element) {
    var current_icon = element.attr('src');
    var new_icon = element.attr('data-alt-image');
    element.attr({
        'src': new_icon,
        'data-alt-image': current_icon
    });
}

function ocSetVCState() {
    jQuery('#oc_pc_switch_spinner').removeClass('success').addClass('is_active');
    var vc_state = jQuery('#pc_enable').prop('checked') ? '1' : '0';
    vc_ttl = jQuery('#oc_vcache_ttl').val() || '2592000';
    vc_ttl_unit = jQuery('#oc_vcache_ttl_unit').val() || 'days';
    jQuery.post(ajaxurl, {
        action: 'oc_set_vc_state',
        vc_state: vc_state,
        vc_ttl: vc_ttl,
        vc_ttl_unit: vc_ttl_unit
    }, function (response) {
        jQuery('#oc_pc_switch_spinner').removeClass('is_active').addClass('success');
        if (response.status === 'success') {
            oc_toggle_pSection();
            oc_trigger_log({
                actionType: 'wppremium_click_feature',
                isPremium: 'true',
                feature: 'PERFORMANCE_CACHE',
                featureAction: (vc_state == '1') ? 'enable_vcache' : 'disable_vcache'
            });
        }else{
            jQuery('#oc_um_overlay').show();
            ocSetModalData({
                isPremium: 'true',
                feature: 'PERFORMANCE_CACHE',
                featureAction: (vc_state == '1') ? 'enable_vcache' : 'disable_vcache'
            });
            jQuery('#pc_enable').prop('checked', false);
        }
    })
}

function oc_toggle_pSection() {
    if (jQuery('#pc_enable').prop('checked')) {
        //jQuery('#oc-performance-icon').hide();
        //jQuery('#oc-performance-icon-active').show();
        if (!jQuery('#oc_vcache_ttl').val()){
            jQuery('#oc_vcache_ttl').val('2592000');
        }
        jQuery('#pc_enable_settings').show();
    } else {
        //jQuery('#oc-performance-icon').show();
        //jQuery('#oc-performance-icon-active').hide();
        jQuery('#pc_enable_settings').hide();
    }
}

function oc_toggle_devModeSection() {
    if (jQuery('#dev_mode_enable').prop('checked')) {
        if (!jQuery('#dev_mode_duration').val()){
            jQuery('#dev_mode_duration').val('48');
        }
        jQuery('#dev_mode_enable_settings').show();
    } else {
        jQuery('#dev_mode_enable_settings').hide();
    }
}

function oc_toggle_excludeCDNSection() {
    if (jQuery('#exclude_cdn_enable').prop('checked')) {
        if (!jQuery('#exclude_cdn_data').val()){
            jQuery('#exclude_cdn_data').val('');
        }
        jQuery('#exclude_cdn_enable_settings').show();
    } else {
        jQuery('#exclude_cdn_enable_settings').hide();
    }
}

function oc_validate_ttl(){
    var element = jQuery('#oc_vcache_ttl');
    var ttl_value = element.val();
    var pattern = /^\d+$/;
    if (pattern.test(ttl_value)){
        element.removeClass('oc_error');
        return true;
    }else{
        element.addClass('oc_error');
        return false;
    }
}

function oc_validate_dev_duration(){
    var element = jQuery('#dev_mode_duration');
    var dev_mode_value = element.val();
    var pattern = /^\d+$/;
    if (pattern.test(dev_mode_value)){
        element.removeClass('oc_error');
        return true;
    }else{
        element.addClass('oc_error');
        return false;
    }
}

function oc_update_ttl(){
    jQuery('#oc_ttl_spinner').removeClass('success').addClass('is_active');
    var vc_ttl = jQuery('#oc_vcache_ttl').val() || '2592000';
    var vc_ttl_unit = jQuery('#oc_vcache_ttl_unit').val() || 'days';

    /* if (vc_ttl_unit == 'minutes') {
        vc_ttl = vc_ttl * 60;
    } else if (vc_ttl_unit == 'hours') {
        vc_ttl = vc_ttl * 3600;
    } else if (vc_ttl_unit == 'days') {
        vc_ttl = vc_ttl * 86400;
    } */

    jQuery.post(ajaxurl, {
        action: 'oc_set_vc_ttl',
        vc_ttl: vc_ttl,
        vc_ttl_unit: vc_ttl_unit
    }, function(response){
        jQuery('#oc_ttl_spinner').removeClass('is_active');
        if (response.status === 'success'){
            jQuery('#oc_ttl_spinner').addClass('success');
        }
        if ( ! jQuery('#oc_vcache_ttl').val().trim()){
            jQuery('#oc_vcache_ttl').val('2592000');
        }
    });
}

function oc_validate_exclude_cdn_data(){
    var element = jQuery('#exclude_cdn_data');
    if (element.val().trim() != ""){
        element.removeClass('oc_error');
        return true;
    }else{
        element.addClass('oc_error');
        return false;
    }
}

function oc_update_dev_duration(){
    jQuery('#oc_dev_duration_spinner').removeClass('success').addClass('is_active');
    jQuery.post(ajaxurl, {
        action: 'oc_set_dev_mode_time',
        dev_duration: jQuery('#dev_mode_duration').val() || '48'
    }, function(response){
        jQuery('#oc_dev_duration_spinner').removeClass('is_active');
        if (response.status === 'success'){
            jQuery('#oc_dev_duration_spinner').addClass('success');
        }
        if ( ! jQuery('#dev_mode_duration').val().trim()){
            jQuery('#dev_mode_duration').val('48');
        }
    });
}

function ocExcludeCDNState(){
    jQuery('#oc_exclude_cdn_switch_spinner').removeClass('success').addClass('is_active');
    var exclude_cdn_mode = jQuery('#exclude_cdn_enable').prop('checked') ? '1' : '0';
    jQuery.post(ajaxurl, {
        action: 'oc_exclude_cdn_mode',
        exclude_cdn_mode : exclude_cdn_mode,
    }, function(response){
        jQuery('#oc_exclude_cdn_switch_spinner').removeClass('is_active');
        if (response.status === 'success'){
            oc_toggle_excludeCDNSection();
            jQuery('#oc_exclude_cdn_switch_spinner').addClass('success');
            oc_trigger_log({
                actionType: 'wppremium_click_feature',
                isPremium: 'true',
                feature: 'CDN',
                /* featureAction: (dev_mode == '1') ? 'enable_cdn' : 'disable_cdn' */
            });
        }else{
            jQuery('#oc_um_overlay').show();
            jQuery('#exclude_cdn_enable').prop('checked', false);
        }        
    });    
}

function oc_update_exclude_cdn_data(){
    jQuery('#oc_exclude_cdn_data_spinner').removeClass('success').addClass('is_active');
    jQuery.post(ajaxurl, {
        action: 'oc_exclude_cdn_data',
        exclude_cdn_data: jQuery('#exclude_cdn_data').val() || ''
    }, function(response){
        jQuery('#oc_exclude_cdn_data_spinner').removeClass('is_active');
        if (response.status === 'success'){
            jQuery('#oc_exclude_cdn_data_spinner').addClass('success');
        }
        /* if ( ! jQuery('#exclude_cdn_data').val().trim()){
            jQuery('#exclude_cdn_data').val('48');
        } */
    });
}

// Set dev mode when switched
function ocSetDevMode(){
    jQuery('#oc_dev_mode_switch_spinner').removeClass('success').addClass('is_active');
    var dev_mode = jQuery('#dev_mode_enable').prop('checked') ? '1' : '0';
    var dev_duration = jQuery('#dev_mode_duration').val() || '48';
    jQuery.post(ajaxurl, {
        action: 'oc_set_dev_mode',
        dev_mode : dev_mode,
        dev_duration: dev_duration
    }, function(response){
        jQuery('#oc_dev_mode_switch_spinner').removeClass('is_active');
        if (response.status === 'success'){
            oc_toggle_devModeSection();
            jQuery('#oc_dev_mode_switch_spinner').addClass('success');
            oc_trigger_log({
                actionType: 'wppremium_click_feature',
                isPremium: 'true',
                feature: 'CDN',
                featureAction: (dev_mode == '1') ? 'enable_cdn' : 'disable_cdn'
            });
        }else{
            jQuery('#oc_um_overlay').show();
            jQuery('#dev_mode_enable').prop('checked', false);
        }        
    });    
}

function ocSetCdnState(){
    jQuery('#oc_cdn_switch_spinner').removeClass('success').addClass('is_active');
    var cdn_state = jQuery('#cdn_enable').prop('checked') ? '1' : '0';
    jQuery.post(ajaxurl, {
        action: 'oc_set_cdn_state',
        cdn_state : cdn_state,
    }, function(response){
        jQuery('#oc_cdn_switch_spinner').removeClass('is_active');
        if (response.status === 'success'){
            jQuery('#oc_cdn_switch_spinner').addClass('success');
            oc_change_cdn_icon();
            oc_trigger_log({
                actionType: 'wppremium_click_feature',
                isPremium: 'true',
                feature: 'CDN',
                featureAction: (cdn_state == '1') ? 'enable_cdn' : 'disable_cdn'
            });
        }else{
            jQuery('#oc_um_overlay').show();
            ocSetModalData({
                isPremium: 'true',
                feature: 'CDN',
                featureAction: (cdn_state == '1') ? 'enable_cdn' : 'disable_cdn'
            });
            jQuery('#cdn_enable').prop('checked', false);
        }        
    });    
}

function oc_change_cdn_icon(){
    if (jQuery('#cdn_enable').prop('checked')) {
        jQuery('#oc-cdn-icon-active').show();
        jQuery('#oc-cdn-icon').hide();
        jQuery('.oc-cdn-feature-box').show();
        // Remove sub features success classes else spinner animate on each switch
        jQuery('.oc-cdn-feature-box .oc_cb_spinner').removeClass('success');
    } else {
        jQuery('#oc-cdn-icon').show();
        jQuery('#oc-cdn-icon-active').hide();
        jQuery('.oc-cdn-feature-box').hide();
        // Remove sub features success classes else spinner animate on each switch
        jQuery('.oc-cdn-feature-box .oc_cb_spinner').removeClass('success');
    }
}