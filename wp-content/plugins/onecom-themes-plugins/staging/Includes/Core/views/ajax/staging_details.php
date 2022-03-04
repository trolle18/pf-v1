<!--Staging entry box-->
<div id="staging_entry">
    <div class="one-staging-details card-2">
        <div class="one-staging-site-info box one-card-staging-create-info">
            <div class="oc-column oc-left-column">
                <div class="oc-flex-center oc-icon-box">
                    <img src="<?php echo ONECOM_WP_URL.'assets/images/staging-icon.svg' ?>" alt="One Staging - Ready" class="one-card-staging-create-icon-old" />
                    <h2 class="main-heading"><?php _e( 'Staging', OC_PLUGIN_DOMAIN ); ?></h2>
                </div>
                <div class="stg-desc">
                    <?php if(isset($cloneExists) && $cloneExists): ?>
                        <p><?php _e( 'The staging website is a copy of your live website, where you can test new plugins and themes without affecting your live website.', OC_PLUGIN_DOMAIN ); ?> <br>
                            <?php _e( 'Only one staging version can be created for each website. When you rebuild a staging website, any existing staging site will be replaced with a new snapshot of your live website.', OC_PLUGIN_DOMAIN ); ?><br>
                            <?php _e( 'The login details for the staging backend are the same as for the live website.', OC_PLUGIN_DOMAIN ); ?><br><br>

                            <?php echo sprintf(__( '%sCaution:%s Rebuilding will overwrite all files and the database of your existing staging website.', OC_PLUGIN_DOMAIN ),'<strong>','</strong>'); ?>
                        </p>
                    <?php else: ?>
                        <div>
                            <p><strong><?php _e('Staging site broken',OC_PLUGIN_DOMAIN);?></strong></p>
                            <p><?php _e('We have detected that your staging site is broken due to missing database table(s) and/or directory(s).', OC_PLUGIN_DOMAIN ); ?><br>
                                <?php _e('Click on "Rebuild Staging" to regenerate your staging site.', OC_PLUGIN_DOMAIN ); ?></p>
                        </div>
                    <?php endif; ?>
                    <?php if(!isPremium()){ ?>
                        <div class="wrap-rgh-btn-desc preimum_badge"><?php echo apply_filters('oc_staging_button_delete', '', __("Premium feature", OC_PLUGIN_DOMAIN), 'stg');?></div>
                    <?php } ?>
                </div>
            </div>
            <div class="oc-column oc-right-column">
                <div class="one-card-action-old staging-details-created">
                    <?php
                    if(!empty($clones)):
                        foreach ($clones as $key=>$clone): ?>
                            <div class="one-staging-entry staging-entry" id="entry_<?php echo $key; ?>" data-staging-id="<?php echo $key; ?>"></div>
                            <?php if(empty($clones) || $cloneExists){?>
                            <div class="wrap-rgh-btn"><a href="javascript:void(0);" data-loginUrl="<?php echo trailingslashit($clone['url']); ?>wp-login.php" data-stgUrl="<?php echo trailingslashit($clone['url']); ?>" class="one-button btn button_1 loginStaging" style="min-width: 62px;text-align: center;"><?php _e( 'Login to your site', OC_PLUGIN_DOMAIN ); ?></a></div>
                            <div class="wrap-rgh-btn"><a href="<?php echo $clone['url']; ?>" target="_blank" class="viewStaging"><img src="<?php echo ONECOM_WP_URL.'assets/images/view-site.svg' ?>" alt="View your site" class="action-rht-img" /><span><?php _e( 'View your site', OC_PLUGIN_DOMAIN ); ?></span></a></div>
                            <?php } ?>
                        <?php endforeach;
                    endif;
                    if (empty($clones) || $cloneExists) {
                        echo $rebuild_btn = '<div class="wrap-rgh-btn"><a href="javascript:void(0);" class="one-button btn one-button-update-staging" data-staging-id="" data-dialog-id="staging-update-confirmation" data-title="' . __('Are you sure?', OC_PLUGIN_DOMAIN) . '" data-width="500" data-height="300"><img src="' . ONECOM_WP_URL . 'assets/images/rebuild-staging.svg" alt="Rebuild staging" class="action-rht-img" /><span>' . __('Rebuild staging', OC_PLUGIN_DOMAIN) . '</span></a></div>';
                    }else{
                        echo $rebuild_btn = '<div class="wrap-rgh-btn"><a href="javascript:void(0);" class="one-button btn one-button-update-staging rebuild-btn" data-staging-id="" data-dialog-id="staging-update-confirmation" data-title="' . __('Are you sure?', OC_PLUGIN_DOMAIN) . '" data-width="500" data-height="300"><span>' . __('Rebuild staging', OC_PLUGIN_DOMAIN) . '</span></a></div>';
                    }
                    $delete_btn = '<div class="wrap-rgh-btn"><a href="javascript:;" class="staging-trash one-button-delete-staging"  title="'. __("Delete Staging", OC_PLUGIN_DOMAIN).'" data-title="'.__( 'Are you sure?', OC_PLUGIN_DOMAIN ).'" data-dialog-id="staging-delete" data-width="500" data-height="275"><img src="'.ONECOM_WP_URL.'assets/images/delete-staging.svg" alt="Delete staging" class="action-rht-img" /><span>'.__("Delete staging", OC_PLUGIN_DOMAIN).'</span></a></div>';
                    echo $delete_btn;
                    ?>
                    <div class="wrap-rgh-btn"><a href="<?php echo onecom_generic_locale_link( $request = 'staging_guide', get_locale() ); ?>" target="_blank" class="help_link2"><img src="<?php echo ONECOM_WP_URL.'assets/images/need-help.svg' ?>" alt="Need help" class="action-rht-img" /><span><?php _e( 'Need help?', OC_PLUGIN_DOMAIN ); ?></span></a></div>
                </div>
            </div>
        </div>
    </div>
</div>