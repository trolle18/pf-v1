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
		<?php _e( 'Presenting some of our favorite WordPress plugins, curated for quality.', OC_PLUGIN_DOMAIN ); ?>
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
					<a href="<?php echo admin_url( 'admin.php?page=onecom-wp-plugins' ); ?>" class="onecom_tab">
						<?php _e( 'One.com plugins', OC_PLUGIN_DOMAIN); ?><span><?php echo $plugin_count['onecom_excluding_generic'] ?></span>
					</a>
					<a href="<?php echo admin_url( 'admin.php?page=onecom-wp-recommended-plugins' ); ?>" class="onecom_tab active">
						<?php _e( 'Recommended plugins', OC_PLUGIN_DOMAIN); ?><span><?php echo $plugin_count['recommended'] ?></span>
					</a>
					<a href="<?php echo admin_url( 'admin.php?page=onecom-wp-discouraged-plugins' ); ?>" class="onecom_tab">
						<?php _e( 'Discouraged plugins', OC_PLUGIN_DOMAIN); ?><span><?php echo $plugin_count['discouraged'] ?></span>
					</a>
				</div>
			</div>
		</div>
		</div>
		<div id="recommended" class="tab active-tab">
			<div class="plugin-browser widefat">
				<?php $recommended_plugins = onecom_fetch_plugins( $recommended = true ); ?>
				<?php if( ! is_wp_error( $recommended_plugins ) ) : ?>
					<?php foreach( $recommended_plugins as $recommended_plugin ) : ?>
						<?php
							$plugin = $recommended_plugin;

							if( $plugin->slug == '' ) {
								continue;
							}
							$plugin_installed = $plugin_activated = false;
							if ( is_dir( WP_PLUGIN_DIR . '/' . $plugin->slug ) ) {
								$plugin_installed = true;

								$plugin_infos = get_plugins( '/'.$plugin->slug );
								if( ! empty( $plugin_infos ) ) {
									foreach ($plugin_infos as $file => $info) :
										$is_activate = is_plugin_active_for_network( $plugin->slug.'/'.$file );
										if ( $is_activate  ) {
											$plugin_activated = true;
										} else {
											$activateUrl = add_query_arg( array(
												'_wpnonce' => wp_create_nonce( 'activate-plugin_' . $plugin->slug.'/'.$file ),
												'action'   => 'activate',
												'plugin'   => $plugin->slug.'/'.$file,
											), network_admin_url( 'plugins.php' ) );
										}
									endforeach;
								}
							}
						?>
						<div class="one-plugin-card <?php echo ( count( $recommended_plugins )  == 1 ) ? 'single-plugin' : ''; ?> <?php echo ( $plugin_installed ) ? 'installed' : ''; ?>">
							<div class="plugin-card-top">
								<div class="name column-name">
									<h3>
                                        <span><?php echo $plugin->name; ?></span>
										<?php 
											$icon_url = $plugin->thumbnail;
											if( $icon_url != '' ) :
												?>
													<span class="plugin-icon-wrapper icon-available">
														<span class="plugin-category"><?php echo $recommended_plugin->category; ?></span>
														<img src="<?php echo oc_get_plugin_thumbnail($plugin->slug); ?>" alt="<?php echo $plugin->name; ?> " onerror="this.style.display='none'" />
													</span>
												<?php
											else :
												$acronym = onecom_string_acronym( $plugin->name );
												$style = 'background-color:'.onecom_random_color( $key );
												?>
													<span class="plugin-icon-wrapper" style="<?php echo $style; ?>">
														<span class="onecom-acronym"><?php echo $acronym; ?></span>
													</span>
												<?php
											endif;
										?>
									</h3>
								</div>
								<div class="action-links">
									<ul class="plugin-action-buttons">
										<li>
											<?php if( $plugin_installed && $plugin_activated ) : ?>
												<a class="installed-plugin btn button_1" href="javascript:void(0)" data-slug="<?php echo $plugin->slug; ?>" data-name="<?php echo $plugin->name ?>" disabled="true" ><?php _e( 'Active', OC_PLUGIN_DOMAIN ); ?></a>
											<?php elseif ( $plugin_installed && ( ! $plugin_activated ) ) : ?>
												<a class="activate-plugin btn button_1" href="<?php echo $activateUrl ?>"><?php _e( 'Activate', OC_PLUGIN_DOMAIN ); ?></a>
											<?php else : ?>
												<a class="install-now btn button_1" href="javascript:void(0)" data-slug="<?php echo $plugin->slug; ?>" data-name="<?php echo $plugin->name ?>" aria-label="Install <?php echo $plugin->name ?> now" data-download_url="<?php echo $plugin->download_link; ?>" data-action="onecom_install_plugin" data-plugin_type="recommended"><?php _e( 'Install now', OC_PLUGIN_DOMAIN ); ?></a>
											<?php endif; ?>
										</li>
										<li>
											<?php
												$info_url = network_admin_url( 'plugin-install.php?tab=plugin-information&plugin='.$plugin->slug.'&TB_iframe=true&width=772&height=521' );												
											?>
											<a href="<?php echo $info_url; ?>" class="thickbox open-plugin-details-modal" title="<?php _e( 'More details', OC_PLUGIN_DOMAIN ); ?>"><?php _e( 'More details', OC_PLUGIN_DOMAIN ); ?></a>
										</li> 
									</ul>
								</div>
								<div class="desc column-description">
									<p><?php echo $plugin->short_description; ?>
									<?php
										$info_url = ( is_multisite() ) ? network_admin_url( 'plugin-install.php?tab=plugin-information&plugin='.$plugin->slug.'&TB_iframe=true&width=772&height=521' ) : admin_url( 'plugin-install.php?tab=plugin-information&plugin='.$plugin->slug.'&TB_iframe=true&width=772&height=521' );
									?>
									<a href="<?php echo $info_url; ?>" class="thickbox open-plugin-details-modal" title="<?php _e( 'More details', OC_PLUGIN_DOMAIN); ?>"><?php _e( 'More details', OC_PLUGIN_DOMAIN); ?></a> 
									<p class="authors">
										<cite><?php _e( 'By', OC_PLUGIN_DOMAIN ) ?> <?php echo $plugin->author; ?></cite>
									</p>
								</div>
							</div>
						</div> <!-- one-plugin-card -->
					<?php endforeach; ?>
				<?php else : ?>
						<p><?php echo $recommended_plugins->get_error_message(); ?></p>
				<?php endif; ?>

			</div> <!-- plugin-browser -->
		</div> <!-- tab -->

	</div> <!-- wrap_inner -->
</div> <!-- wrap -->
<?php add_thickbox(); ?> 

<span class="dashicons dashicons-arrow-up-alt onecom-move-up"></span>