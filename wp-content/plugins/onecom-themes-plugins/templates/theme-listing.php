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

	<h1 class="one-title"> <?php _e( 'Themes', OC_PLUGIN_DOMAIN ); ?> </h1>

	<div class="page-subtitle">
		<?php _e( 'Get exclusive themes especially crafted for one.com customers.', OC_PLUGIN_DOMAIN ); ?>
	</div>
	
	<div class="wrap_inner inner one_wrap">
		
		<div id="free" class="active-tab">

			<div class="theme-filters">
				<?php $theme_count = onecom_themes_cat_count(); ?>
				<div class="h-parent-wrap">
				<div class="h-parent">
				<div class="h-child">
					<ul id="oc_theme_filter" class="oc_theme_filter">
						<li data-filter-key="all" class="oc-active-filter">
							<?php _e( 'All', ''); ?>
							<span><?php echo $theme_count['all']; ?></span>
						</li>
						<li data-filter-key="premium" class="oc-premium-filter">
							<?php _e( 'Premium', ''); ?>
							<span><?php echo $theme_count['premium']; ?></span>
						</li>
						<li data-filter-key="blogging">
							<?php _e( 'Blog', ''); ?>
							<span><?php echo $theme_count['blogging']; ?></span>
						</li>
						<li data-filter-key="business-services">
							<?php _e( 'Business & Services', ''); ?>
							<span><?php echo $theme_count['business-services']; ?></span>
						</li>
						<li data-filter-key="events">
							<?php _e( 'Events', ''); ?>
							<span><?php echo $theme_count['events']; ?></span>
						</li>
						<li data-filter-key="family-recreation">
							<?php _e( 'Family & Recreation', ''); ?>
							<span><?php echo $theme_count['family-recreation']; ?></span>
						</li>
						<li data-filter-key="food-hospitality">
							<?php _e( 'Food & Hospitality', ''); ?>
							<span><?php echo $theme_count['food-hospitality']; ?></span>
						</li>
						<li data-filter-key="music-art">
							<?php _e( 'Music & Art', ''); ?>
							<span><?php echo $theme_count['music-art']; ?></span>
						</li>
						<li data-filter-key="online-shop">
							<?php _e( 'Online Shop', ''); ?>
							<span><?php echo $theme_count['online-shop']; ?></span>
						</li>
						<li data-filter-key="portfolio-cv">
							<?php _e( 'Portfolio & CV', ''); ?>
							<span><?php echo $theme_count['portfolio-cv']; ?></span>
						</li>
					</ul>
				</div>
			</div>
			</div>
				
			</div>  <!-- theme-filters -->

			<?php 
				$requsted_paged = ( isset( $_GET[ 'paged' ] ) ) ? ( int ) $_GET[ 'paged' ] : 1;
				global $theme_data; 
				$theme_data = onecom_fetch_themes( $page = $requsted_paged, $exclude_ilotheme = true );
				$config = onecom_themes_listing_config();

			?>

			<div class="theme-browser" data-total_themes="<?php echo $config[ 'total' ]; ?>" data-item_count="<?php echo $config[ 'item_count' ]; ?>">
				<?php 
					load_template( dirname( __FILE__ ) . '/theme-listing-loop.php' ); 
				?>
			</div> <!-- theme-browser -->

			<div class="loading-overlay theme-loader">
				<div class="loading-overlay-content">
					<div class="loader"></div>
				</div>
			</div><!-- loader -->
			
			<?php onecom_themes_listing_pagination( $config, $requsted_paged ); ?>

		</div> <!-- tab -->

	</div> <!-- wrap_inner -->
</div> <!-- wrap -->

<?php add_thickbox(); ?> 

<div id="thickbox_preview" style="display:none">
   <div id="preview_box">
       <div class="one-theme-listing-bar">
           <span class="dashicons dashicons-wordpress-alt"></span>
       </div>
       <div class="header_btn_bar">
           <div class="left-header">
               <a href="javascript:void(0)" class="close_btn"><?php _e( 'Back to themes', OC_PLUGIN_DOMAIN); ?></a>
               <div class="btn btn_arrow previous" data-demo-id=""><span class="dashicons dashicons-arrow-left-alt2"></span></div>
               <span data-theme-count="" data-active-demo-id="" class="theme-info hide"></span>
			   <div class="btn btn_arrow next" data-demo-id=""><span class="dashicons dashicons-arrow-right-alt2"></span></div>
			   
			   <?php echo apply_filters('oc_preview_install', '<a href="javascript:void(0)" class="btn button_1 preview-install-button" data-active-demo-id="">'.__( 'Install', OC_PLUGIN_DOMAIN).'</a>', '', 'ptheme'); ?>
           </div>
           <div class="right-header">
               <div class="btn button_2 current" id="desktop"> <span class="dashicons dashicons-desktop"></span> <?php _e( 'Desktop', OC_PLUGIN_DOMAIN); ?></div>
               <div class="btn button_2" id="mobile"> <span class="dashicons dashicons-smartphone"></span> <?php _e( 'Mobile', OC_PLUGIN_DOMAIN); ?></div>
         </div>
       </div>
       
       <span class="divider_shadow" > </span>

       <div class="preview-container">
             <div class="desktop-content text-center preview">
                 <iframe src='#' title="Preview"></iframe>
             </div>
       </div>
   </div>
</div>   

<span class="dashicons dashicons-arrow-up-alt onecom-move-up"></span>