<?php

/**
 * Defines admin settings functions (sections and fields)
 *
 * @since      0.1.0
 * @package    Under_Construction
 * @subpackage OCUC_Admin_Settings
 */

// Exit if file accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class OCUC_Admin_Settings
{
	private $settings_api;

	function init_admin_settings()
	{
		$this->settings_api = new OCUC_Admin_Settings_API;
		add_action('admin_init', array($this, 'uc_settings_init_fn'));
		add_action('admin_menu', array($this, 'uc_add_page_fn'));
		add_action('admin_init', array($this->settings_api, 'settings_init'));
		add_action('admin_head', array($this, 'uc_menu_icon_css_fn'));
	}

	// Add sections/groups for different fields
	function get_settings_sections()
	{
		$sections = array(
			array(
				'id'    => 'onecom_under_construction_settings',
				'title' => __('General settings', ONECOM_UC_TEXT_DOMAIN),
				'desc'	=> '',
				'callback' => 'callback_section'
			),
			array(
				'id'    => 'onecom_under_construction_content',
				'title' => __('Content', ONECOM_UC_TEXT_DOMAIN),
				'callback' => 'callback_section'
			),
			array(
				'id'    => 'onecom_under_construction_customization',
				'title' => __('Customization', ONECOM_UC_TEXT_DOMAIN),
				'callback' => 'callback_section'
			)
		);
		return $sections;
	}

	/**
	 * Returns all the settings fields for above sections
	 *
	 * @return array settings fields
	 */
	function get_settings_fields()
	{
		// prepare users array to whitelist via multicheck option
		$role_info = wp_roles();
		$users_list = $role_info->role_names;

		$settings_fields = array(
			'onecom_under_construction_settings' => array(

				array(
					'name'    => 'uc_status',
					'label'   => __('Status', ONECOM_UC_TEXT_DOMAIN),
					'type'    => 'checkbox',
					'desc'	  => __('Enable Maintenance Mode on your website', ONECOM_UC_TEXT_DOMAIN)
				),

				array(
					'name'    => 'uc_theme',
					'label'   => __('Select design', ONECOM_UC_TEXT_DOMAIN),
					'desc'    => __('Choose a design for the Maintenance Mode page', ONECOM_UC_TEXT_DOMAIN),
					'type'    => 'radio_image',
					'options' => array(
						'theme-1' => 'design-1.png',
						'theme-2' => 'design-2.png',
						'theme-3' => 'design-3.png',
						'theme-4' => 'design-4.png',
						'theme-5' => 'design-5.png',
						'theme-6' => 'design-6.png',
					)
				),

				array(
					'name'    => 'uc_http_mode',
					'label'   => __('Mode', ONECOM_UC_TEXT_DOMAIN),
					'desc'    => '',
					'type'    => 'radio',
					'options' => array(
						'200' => __('Coming soon', ONECOM_UC_TEXT_DOMAIN) .
							' <p class="description" style="margin-bottom:6px;padding-left:31px;">' .
							__('Returns standard 200 HTTP OK response code to indexing robots', ONECOM_UC_TEXT_DOMAIN) .
							'</p>',
						'503' => __('Maintenance mode', ONECOM_UC_TEXT_DOMAIN) .
							' <p class="description" style="margin-bottom:6px;padding-left:31px;">' .
							__('Returns 503 HTTP Service unavailable code to indexing robots', ONECOM_UC_TEXT_DOMAIN) .
							'</p>',
					)
				),

				array(
					'name'    => 'uc_timer_switch',
					'label'   => __('Countdown timer', ONECOM_UC_TEXT_DOMAIN),
					'desc'    => __('Would you like to show countdown timer?', ONECOM_UC_TEXT_DOMAIN),
					'type'    => 'checkbox',
				),

				array(
					'name'              => 'uc_timer',
					'label'             => '',
					'type'              => 'datetime',
					'placeholder'           => __('Select date', ONECOM_UC_TEXT_DOMAIN),
					'desc'				=> __('Set countdown timer. Current Wordpress time: ', ONECOM_UC_TEXT_DOMAIN) .
						current_time('Y-m-d H:i') . '. <a href="' . admin_url('options-general.php') . '" target="_blank">' .
						__('Change timezone', ONECOM_UC_TEXT_DOMAIN) .
						'</a>',
					'sanitize_callback' => 'sanitize_text_field'
				),

				array(
					'name'    => 'uc_timer_action',
					'label'   => __('Countdown action', ONECOM_UC_TEXT_DOMAIN),
					'type'    => 'select',
					'options' => array(
						'no-action' => __('No action', ONECOM_UC_TEXT_DOMAIN),
						'hide' => __('Hide countdown timer', ONECOM_UC_TEXT_DOMAIN),
						'disable' => __('Disable the Maintenance Mode and show your website', ONECOM_UC_TEXT_DOMAIN),
					),
					'desc'    => __('Select action after countdown ends', ONECOM_UC_TEXT_DOMAIN),
				),

				array(
					'name'    => 'uc_subscribe_form',
					'label'   => __('Subscribe form', ONECOM_UC_TEXT_DOMAIN),
					'desc'    => __('Would you like to show subscription form?', ONECOM_UC_TEXT_DOMAIN),
					'type'    => 'checkbox',
				),

				array(
					'name'    => 'uc_whitelisted_roles',
					'label'   => __('Whitelisted user roles', ONECOM_UC_TEXT_DOMAIN),
					'desc'    => __('Selected user roles will see the normal site, instead of the Maintenance Mode.', ONECOM_UC_TEXT_DOMAIN),
					'type'    => 'multicheck',
					'options' => $users_list
				),

				array(
					'name'    => 'uc_exclude_pages',
					'label'   => __('Exclude pages', ONECOM_UC_TEXT_DOMAIN),
					'type'    => 'exclude_multiselect',
					'options' => array(),
					'desc'    => __('Select the page(s) to be excluded by maintenance mode such as "WooCommerce Lost password" or any custom login page. To avoid performance issues on sites, we show only the first 250 entries for each post type (post, page, product etc).', ONECOM_UC_TEXT_DOMAIN),
				),

				array(
					'name'        => 'uc_submit',
					'label'       => '',
					'type'        => 'submit',
					'id'        => 'oc_submit'
				),
			),

			/* Design Settings */
			'onecom_under_construction_content' => array(
				array(
					'name'    => 'uc_logo',
					'label'   => __('Logo', ONECOM_UC_TEXT_DOMAIN),
					'type'    => 'file',
					'default' => '',
					'options' => array(
						'button_label' => __('Select Image', ONECOM_UC_TEXT_DOMAIN)
					),
					'desc'             => __('Site title will be displayed if no image uploaded.', ONECOM_UC_TEXT_DOMAIN) . ' ' . __('Site title', ONECOM_UC_TEXT_DOMAIN) . ': ' . get_bloginfo('blogname'),
					'sanitize_callback' => 'sanitize_text_field'

				),

				array(
					'name'    => 'uc_favicon',
					'label'   => __('Site icon', ONECOM_UC_TEXT_DOMAIN),
					'type'    => 'file',
					'default' => '',
					'options' => array(
						'button_label' => __('Select Image', ONECOM_UC_TEXT_DOMAIN)
					),
					'desc'    => __('Site Icons are what you see in browser tabs, bookmark bars, and within the WordPress mobile apps.', ONECOM_UC_TEXT_DOMAIN) . ' ' . __('Site Icons should be square and at least 512 × 512 pixels.', ONECOM_UC_TEXT_DOMAIN),
					'sanitize_callback' => 'sanitize_text_field'
				),

				array(
					'name'              => 'uc_headline',
					'label'             => __('Headline', ONECOM_UC_TEXT_DOMAIN),
					'type'              => 'text',
					'default'           => '',
					'sanitize_callback' => 'sanitize_text_field'
				),

				array(
					'name'    => 'uc_description',
					'label'   => __('Description', ONECOM_UC_TEXT_DOMAIN),
					'desc'    => '',
					'type'    => 'wysiwyg',
					'default' => ''
				),

				array(
					'name'              => 'uc_copyright',
					'label'             => __('Copyright Text', ONECOM_UC_TEXT_DOMAIN),
					'type'              => 'text',
					'default'           => '',
					'sanitize_callback' => 'sanitize_text_field'
				),

				array(
					'name'              => 'uc_facebook_url',
					'label'             => __('Facebook', ONECOM_UC_TEXT_DOMAIN),
					'type'              => 'url',
					'sanitize_callback' => 'sanitize_text_field',
					'placeholder'		=> 'https://facebook.com/profile'
				),
				array(
					'name'              => 'uc_twitter_url',
					'label'             => __('Twitter', ONECOM_UC_TEXT_DOMAIN),
					'type'              => 'url',
					'sanitize_callback' => 'sanitize_text_field',
					'placeholder'		=> 'https://twitter.com/profile'
				),

				array(
					'name'              => 'uc_instagram_url',
					'label'             => __('Instagram', ONECOM_UC_TEXT_DOMAIN),
					'type'              => 'url',
					'sanitize_callback' => 'sanitize_text_field',
					'placeholder'		=> 'https://instagram.com/profile'
				),

				array(
					'name'              => 'uc_linkedin_url',
					'label'             => __('LinkedIn', ONECOM_UC_TEXT_DOMAIN),
					'type'              => 'url',
					'sanitize_callback' => 'sanitize_text_field',
					'placeholder'		=> 'https://linkedin.com/profile'
				),

				array(
					'name'              => 'uc_youtube_url',
					'label'             => __('YouTube', ONECOM_UC_TEXT_DOMAIN),
					'type'              => 'url',
					'sanitize_callback' => 'sanitize_text_field',
					'placeholder'		=> 'https://youtube.com/profile'
				),

				array(
					'name'              => 'uc_seo_title',
					'label'             => __('SEO title', ONECOM_UC_TEXT_DOMAIN),
					'type'              => 'text',
					'default'           => '',
					'sanitize_callback' => 'sanitize_text_field',
					'desc'    			=> __('Search engines displays the 50 to 65 characters of a title tag on search engine results pages.', ONECOM_UC_TEXT_DOMAIN)
				),

				array(
					'name'              => 'uc_seo_description',
					'label'             => __('SEO description', ONECOM_UC_TEXT_DOMAIN),
					'type'              => 'textarea',
					'desc'    			=> __('SEO meta description length is recommended between 120 to 160 characters.', ONECOM_UC_TEXT_DOMAIN),
				),

				array(
					'name'        => 'uc_submit',
					'label'       => '',
					'type'        => 'submit'
				),

			),

			'onecom_under_construction_customization' => array(
				array(
					'name'    => 'uc_page_bg_color',
					'label'   => __('Background Color', ONECOM_UC_TEXT_DOMAIN),
					'desc'    => '',
					'type'    => 'color',
					'default' => '',
				),

				array(
					'name'    => 'uc_primary_color',
					'label'   => __('Primary color', ONECOM_UC_TEXT_DOMAIN),
					'desc'    => '',
					'type'    => 'color',
					'default' => '',
					'desc'    => __('Set color for site title and button', ONECOM_UC_TEXT_DOMAIN),
				),

				array(
					'name'    => 'uc_page_bg_image',
					'label'   => __('Background image', ONECOM_UC_TEXT_DOMAIN),
					'desc'    => __('Choose between having a solid color background or uploading an image. By default images will cover the entire background.', ONECOM_UC_TEXT_DOMAIN),
					'type'    => 'file',
					'default' => '',
					'options' => array(
						'button_label' => __('Select Image', ONECOM_UC_TEXT_DOMAIN)
					),
					'sanitize_callback' => 'sanitize_text_field'
				),

				array(
					'name'        => 'uc_custom_css',
					'label'       => __('Custom CSS', ONECOM_UC_TEXT_DOMAIN),
					'placeholder' => '.selector { property-name: property-value; }',
					'desc'        => __('Add custom CSS code', ONECOM_UC_TEXT_DOMAIN),
					'type'        => 'textarea'
				),

				array(
					'name'        => 'uc_scripts',
					'label'       => __('Header scripts', ONECOM_UC_TEXT_DOMAIN),
					'placeholder' => '&lt;script&gt;
  &lt;!-- Analytics code --&gt;
&lt;/script&gt;',
					'desc'        => __('Paste in your universal or classic google analytics code in header', ONECOM_UC_TEXT_DOMAIN),
					'type'        => 'textarea',
				),

				array(
					'name'        => 'uc_footer_scripts',
					'label'       => __('Footer scripts', ONECOM_UC_TEXT_DOMAIN),
					'placeholder' => '&lt;script&gt;
  &lt;!-- Analytics code --&gt;
&lt;/script&gt;',
					'desc'        => __('Paste in your analytics or custom scripts in footer', ONECOM_UC_TEXT_DOMAIN),
					'type'        => 'textarea',
				),

				array(
					'name'        => 'uc_submit',
					'label'       => '',
					'type'        => 'submit'
				),
			)

		);

		return $settings_fields;
	}

	/**
	 * Initialize and registers the settings sections and fileds to WordPress
	 *
	 * Usually this should be called at `admin_init` hook.
	 *
	 * This function gets the initiated settings sections and fields. Then
	 * registers them to WordPress and ready for use.
	 */

	public function uc_settings_init_fn()
	{
		//set the settings
		$this->settings_api->set_sections($this->get_settings_sections());
		$this->settings_api->set_fields($this->get_settings_fields());
	}

	// Add sub page to the Settings Menu
	public function uc_add_page_fn()
	{
		// @later-todo - move out as public var if getting used at multiple places
		$menu_title = __("Maintenance Mode", ONECOM_UC_TEXT_DOMAIN);
		add_menu_page($menu_title, $menu_title, 'manage_options', 'onecom-wp-under-construction', array($this, 'uc_page_fx'), 'dashicons-admin-generic');

    }

	// add uc settings menu icon
	function uc_menu_icon_css_fn()
	{
		define('OCUC_MENU_ICON_GREY', ONECOM_UC_DIR_URL . 'assets/images/uc-menu-icon-grey.svg');
		define('OCUC_MENU_ICON_BLUE', ONECOM_UC_DIR_URL . 'assets/images/uc-menu-icon-blue.svg');

		echo "<style>.toplevel_page_onecom-wp-under-construction > .wp-menu-image{display:flex !important;align-items: center;justify-content: center;}.toplevel_page_onecom-wp-under-construction > .wp-menu-image:before{content:'';background-image:url('" . OCUC_MENU_ICON_GREY . "');font-family: sans-serif !important;background-repeat: no-repeat;background-position: center center;background-size: 18px 18px;background-color:#fff;border-radius: 100px;padding:0 !important;width:18px;height: 18px;}.toplevel_page_onecom-wp-under-construction.current > .wp-menu-image:before{background-size: 16px 16px; background-image:url('" . OCUC_MENU_ICON_BLUE . "');}.ab-top-menu #wp-admin-bar-purge-all-varnish-cache .ab-icon:before,#wpadminbar>#wp-toolbar>#wp-admin-bar-root-default>#wp-admin-bar-onecom-wp .ab-item:before, .ab-top-menu #wp-admin-bar-onecom-staging .ab-item .ab-icon:before{top: 2px;}a.current.menu-top.toplevel_page_onecom-wp-under-construction.menu-top-last{word-spacing: 10px;}@media only screen and (max-width: 960px){.auto-fold #adminmenu a.menu-top.toplevel_page_onecom-wp-under-construction{height: 55px;}}</style>";
		return true;
	}

	// Display the admin options page
	function uc_page_fx()
	{
		$premium_class = $this->settings_api->oc_premium() ? 'oc-premium' : 'oc-non-premium';
?>
		<div class="wrap one_uc_wrap" id="onecom-ui">
			<?php $this->uc_admin_head();
                // Show message after settings save
                settings_errors();
            ?>
			<div class="ocuc-setting-wrap <?php echo $premium_class; ?>" id="responsiveTabsDemo" style="visibility: hidden;">
                <div class="wrap-head-desc">
                    <div class="oc-flex-center oc-icon-box">
                        <img width="48" height="48" src="<?php echo ONECOM_UC_DIR_URL . '/assets/images/mm-icon.svg' ?>" alt="one.com">
                        <h2 class="main-heading"> <?php echo __("Maintenance Mode", ONECOM_UC_TEXT_DOMAIN);?> </h2>
                    </div>
                    <p>
                        <?php
                        if ($this->settings_api->oc_premium() === true){
                            echo __('Make your website private when editing it. Maintenance Mode tells the visitors that your website is under construction.', ONECOM_UC_TEXT_DOMAIN)." ". __('With the Pro version, you get more customization options.', ONECOM_UC_TEXT_DOMAIN);
                        }
                        else{
                            echo __('Make your website private when editing it. Maintenance Mode tells the visitors that your website is under construction.', ONECOM_UC_TEXT_DOMAIN);
                        }
                        ?>
                    </p>
                </div>
				<?php
				    $this->uc_show_navigation();
				?>
				<form method="post" action="options.php" id="uc-form">
					<?php settings_fields(ONECOM_UC_OPTION_FIELD); ?>
                    <div class="onecom_tabs_panels">
					<?php
                    $loop = 0;
                    $class = '';
                    foreach ($this->get_settings_sections() as $form) {
                        $class = 'onecom_tabs_panel '.$form['id'];
                        if($loop > 0){
                            $class = 'onecom_tabs_panel oc_hidden '.$form['id'];
                        }
                    $loop++;
                    ?>
                        <div class="<?php echo $class;?>" id="<?php echo $form['id']; ?>">

							<?php
							do_action('wsa_form_top_' . $form['id'], $form);

							do_settings_sections($form['id']);
							do_action('wsa_form_bottom_' . $form['id'], $form);
							?>
						</div>
					<?php } ?>
                    </div>
				</form>
			</div>
		<?php
		$this->settings_api->script();
	}

	function uc_admin_head()
	{ ?>
			<?php
            if ($this->settings_api->oc_premium() != true && function_exists('onecom_premium_theme_admin_notice')){
                onecom_premium_theme_admin_notice();
                //(function_exists( 'onecom_generic_log')? onecom_generic_log( "wp_premium_click", "pcache"):'');
            }
        	?>

			<h1 class="one-title"> <?php echo __("Utility Tools", ONECOM_UC_TEXT_DOMAIN);
			if ($this->settings_api->oc_premium() === true){ 
				echo "<span>Pro</span>";
			} ?>
			</h1>
			<div class="page-subtitle">
                <?php
                echo __('Helpful tools for building and maintaining your site.',ONECOM_UC_TEXT_DOMAIN);
                ?>
			</div>
			<!-- <h2 class="one-logo">
				<div class="textleft"><span><?php echo __("Maintenance Mode", ONECOM_UC_TEXT_DOMAIN); ?> <span class="uc_spinner"></span></span></div>
				<div class="textright">
					<img src="<?php echo ONECOM_UC_DIR_URL . '/assets/images/one.com-logo@2x.svg' ?>" alt="one.com" srcset="<?php echo ONECOM_UC_DIR_URL . '/assets/images/one.com-logo@2x.svg 2x' ?>" />
				</div>wrap
			</h2> -->
		<?php }

    /**
     * All three navigation
     * @return void
     */
	function uc_show_navigation2()
	{
		$count = count($this->get_settings_sections());

		// don't show the navigation if only one section exists
		if ($count === 1) {
			return;
		}

		$html = '<ul>';
		foreach ($this->get_settings_sections() as $tab) {
			$html .= sprintf('<li><a href="#%1$s" id="%1$s-tab">%2$s</a></li>', $tab['id'], $tab['title']);
		}
		$html .= '</ul>';
		echo $html;
		?>
		<?php }
    /**
     * All three navigation
     * @return void
     */
    function uc_show_navigation()
    {
        $count = count($this->get_settings_sections());

        // don't show the navigation if only one section exists
        if ($count === 1) {
            return;
        }

        $settings = (array)get_site_option("onecom_under_construction_info");
        $disableClass = '';
        if (
            empty($settings) ||
            "" == $settings ||
            !array_key_exists("uc_status", $settings) ||
            "on" !== $settings["uc_status"]
        ) {
            $disableClass = ' disabled-tab';
        }
        $loop = 0;
        $class = 'onecom_tab active';
        $html = '<div class="h-parent-wrap"><div class="h-parent"><div class="h-child"><div class="onecom_tabs_container">';
        foreach ($this->get_settings_sections() as $tab) {
            if($loop > 0){
                $class = 'onecom_tab'.$disableClass;
            }
            $loop++;
            $html .= sprintf('<div class="%3$s" data-tab="%1$s">%2$s</div>', $tab['id'], $tab['title'],$class);
        }
        $html .= '</div></div></div></div>';
        echo $html;
        ?>
    <?php }
	function uc_forms()
	{ ?>


	<?php }
}
