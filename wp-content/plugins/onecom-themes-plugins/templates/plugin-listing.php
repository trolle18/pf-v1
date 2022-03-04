<div class="wrap" id="onecom-ui">
	<div class="loading-overlay fullscreen-loader">
		<div class="loading-overlay-content">
			<div class="loader"></div>
		</div>
	</div><!-- loader -->
	<div class="onecom-notifier"></div>

	<?php
    if (!ismWP() && function_exists('onecom_premium_theme_admin_notice')){
        onecom_premium_theme_admin_notice();
    }
    ?>

	<h1 class="one-title"> <?php _e( 'Plugins', OC_PLUGIN_DOMAIN ); ?> </h1>

	<div class="page-subtitle">
		<?php _e( 'Improve the experience of your website with one.com plugins.', OC_PLUGIN_DOMAIN); ?>
	</div>

	<?php 
		// Fetch plugins count
		$plugin_count = onecom_plugins_count();
	?>
	<div class="wrap_inner inner one_wrap">
		<div class="h-parent-wrap">
		<div class="h-parent">
			<div class="h-child">
				<div class="onecom_tabs_container">
					<a href="<?php echo admin_url( 'admin.php?page=onecom-wp-plugins' ); ?>" class="onecom_tab active">
						<?php _e( 'One.com plugins', OC_PLUGIN_DOMAIN); ?><span><?php echo $plugin_count['onecom_excluding_generic'] ?></span>
					</a>
					<a href="<?php echo admin_url( 'admin.php?page=onecom-wp-recommended-plugins' ); ?>" class="onecom_tab">
						<?php _e( 'Recommended plugins', OC_PLUGIN_DOMAIN); ?><span><?php echo $plugin_count['recommended'] ?></span>
					</a>
					<a href="<?php echo admin_url( 'admin.php?page=onecom-wp-discouraged-plugins' ); ?>" class="onecom_tab">
						<?php _e( 'Discouraged plugins', OC_PLUGIN_DOMAIN); ?><span><?php echo $plugin_count['discouraged'] ?></span>
					</a>
				</div>
			</div>
		</div>
		</div>
		<div id="free" class="tab active-tab">

			<div class="plugin-browser widefat">

				<?php $plugins = onecom_fetch_plugins(); ?>

				<?php if( is_wp_error( $plugins ) ) : ?>
					<?php echo $plugins->get_error_message(); ?>
				<?php else : ?>
					<?php foreach( $plugins as $key => $plugin ) : ?>
						<?php
                        if($plugin->slug ==='onecom-themes-plugins'){
                            unset( $plugins[ $key ] );
                            continue;
                        }
							$plugin_installed = $plugin_activated = false;
							if ( is_dir( WP_PLUGIN_DIR . '/' . $plugin->slug ) ) {
								$plugin_installed = true;

								$plugin_infos = get_plugins( '/'.$plugin->slug );
								if( ! empty( $plugin_infos ) ) {
									foreach ($plugin_infos as $file => $info) :
										$is_inactivate = is_plugin_inactive( $plugin->slug.'/'.$file );
										if ( !$is_inactivate ) {
											$plugin_activated = true;
										} else {
											$activateUrl = add_query_arg( array(
												'_wpnonce' => wp_create_nonce( 'activate-plugin_' . $plugin->slug.'/'.$file ),
												'action'   => 'activate',
												'plugin'   => $plugin->slug.'/'.$file,
											), admin_url( 'plugins.php' ) );
										}
									endforeach;
								}
							}
						?>
						<div class="one-plugin-card 
							<?php echo ( count( $plugins )  == 1 ) ? 'single-plugin' : ''; ?> <?php echo ( $plugin_installed ) ? 'installed' : ''; ?>" >
							<div class="plugin-card-top <?php echo $plugin->slug; ?>">
								<div class="name column-name">
									<h3>
										<span><?php echo $plugin->name; ?></span>
										<?php 
											$thumbnail_url = $plugin->thumbnail;
										?>
											<span class="plugin-icon-wrapper icon-available">
												<span class="plugin-icon-wrapper-inner"><img src="<?php echo $thumbnail_url; ?>" alt="<?php echo $plugin->name; ?>" /></span>
											</span>
									</h3>
								</div>
								<div class="action-links">
									<ul class="plugin-action-buttons">
										<?php
											// handling marketgoo for subdomains
											$mg_info = new Onecom_Marketgoo();
											$is_root = $mg_info->is_root_domain();
										?>
										<li>
											<?php if ( $plugin->slug === 'one-marketgoo' && !$is_root ) : ?>
												<a class="installed-plugin btn button_1" href="javascript:void(0)" data-slug="<?php echo $plugin->slug; ?>" data-name="<?php echo $plugin->name ?>" disabled="true" ><?php _e( 'Unavailable', OC_PLUGIN_DOMAIN); ?></a>
											<?php elseif( $plugin_installed && $plugin_activated ) : ?>
												<a class="installed-plugin btn button_1" href="javascript:void(0)" data-slug="<?php echo $plugin->slug; ?>" data-name="<?php echo $plugin->name ?>" disabled="true" ><?php _e( 'Active', OC_PLUGIN_DOMAIN); ?></a>
											<?php elseif ( $plugin_installed && ( ! $plugin_activated ) ) : ?>
												<?php if( ( ! isset( $plugin->redirect ) ) || $plugin->redirect != '' ) : ?>
													<a class="activate-plugin activate-plugin-ajax btn button_1" href="javascript:void(0)" data-action="onecom_activate_plugin" data-redirect="<?php echo $plugin->redirect; ?>" data-slug="<?php echo $plugin->slug.'/'.$file; ?>" data-name="<?php echo $plugin->name ?>"><?php _e( 'Activate', OC_PLUGIN_DOMAIN); ?></a>
												<?php else : ?>
													<a class="activate-plugin btn button_1" href="<?php echo $activateUrl ?>"><?php _e( 'Activate', OC_PLUGIN_DOMAIN); ?></a>
												<?php endif; ?>
											<?php else : ?>
												<a class="install-now btn button_1" href="javascript:void(0)" data-slug="<?php echo $plugin->slug; ?>" data-name="<?php echo $plugin->name ?>" aria-label="Install <?php echo $plugin->name ?> now" data-action="onecom_install_plugin" data-redirect="<?php echo $plugin->redirect; ?>" data-plugin_type="<?php echo $plugin->type; ?>"><?php _e( 'Install now', OC_PLUGIN_DOMAIN); ?></a>
											<?php endif; ?>
										</li>

									</ul>
								</div>
								<div class="desc column-description">
									<p><?php _e($plugin->description, OC_PLUGIN_DOMAIN); ?>
									<?php
										$info_url = ( is_multisite() ) ? network_admin_url( 'plugin-install.php?tab=plugin-information&plugin='.$plugin->slug.'&TB_iframe=true&width=772&height=521' ) : admin_url( 'plugin-install.php?tab=plugin-information&plugin='.$plugin->slug.'&TB_iframe=true&width=772&height=521' );
									?>
								</p>
								<?php if ( $plugin->slug === 'one-marketgoo' && !$is_root ) { ?>
									<p><strong>
									<?php _e( 'This plugin is not available on subdomain installations', OC_PLUGIN_DOMAIN);  ?>
									</p></strong>
									<?php } ?>
								</div>
                                <?php if(true === onecom_checkdate_timestamp($plugin->new)){ ?>
                                    <div class="oc-new-ribbon">
                                        <span class="oc-new-ribbon-text"><?php echo __('New', OC_PLUGIN_DOMAIN); ?></span>
                                    </div>
                                <?php } ?>
							</div>
						</div> <!-- one-plugin-card -->
					<?php endforeach; ?>
				<?php endif; ?>

			</div> <!-- plugin-browser -->
		</div> <!-- tab -->

	</div> <!-- wrap_inner -->
</div> <!-- wrap -->
<?php add_thickbox(); ?> 

<span class="dashicons dashicons-arrow-up-alt onecom-move-up"></span>