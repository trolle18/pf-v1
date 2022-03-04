<?php
declare( strict_types=1 );

trait OnecomHMTexts {
	public $action_title = 'action_title';
	public $overview = 'overview';
	public $fix_button_text = 'fix_button_text';
	public $ignore_link_text = 'ignore_link_text';
	public $unignore_link_text = 'unignore_link_text';
	public $how_to_fix = 'how_to_fix';
	public $how_to_fix_lite = 'how_to_fix_lite';
	public $fix_confirmation = 'fix_confirmation';
	public $upsell_text = 'upsell_text';
	public $text = [];
	public $revert_text;
	public $ignore_text;
	public $unignore_text;
	public $text_domain = 'onecom-wp';
	public $fix_text;
	public $ignore_critical_text;
	public $status_text;
	public $status_desc = 'status_desc';
	public $status_resolved = 0;
	public $status_open = 1;
	public $hm_description;
	public $hm_description_premium;
	public $ignored_lite_text;
	public $get_started;
	public $upgrade_modal_text = [];
	public $open_modal_link = '';
	public $change_key;
	public $save_key;
	public $quick_fix_messages = [];

	public function init_trait() {
		$this->change_key           = __( 'Change', $this->text_domain );
		$this->save_key             = __( 'Save', $this->text_domain );
		$this->revert_text          = __( 'Revert', $this->text_domain );
		$this->ignore_text          = __( 'Ignore in future scans', $this->text_domain );
		$this->unignore_text        = __( 'Unignore', $this->text_domain );
		$this->fix_text             = __( 'How to fix', $this->text_domain );
		$this->ignore_critical_text = __( 'Ignore for 24 hours', $this->text_domain );
		$this->status_text          = __( 'Status', $this->text_domain );
		$this->hm_description       = __( 'Health Monitor lets you monitor the essential security and performance checkpoints and fix them if needed.', $this->text_domain );
		$this->hm_description_premium       = __( 'Monitor essential security and performance checkpoints, and fix them if needed. With the Pro version, you get the quick fix, ignore, and more functionalities.', $this->text_domain );
		$this->ignored_lite_text    = __( 'Get access to ignore functionality and more for free.', $this->text_domain );
		$this->get_started          = __( 'Get started', $this->text_domain );
		$this->open_modal_link      = '<a class="onecom__open-modal"> ' . __( 'Free upgrade', $this->text_domain ) . '</a>';
		$this->init_upgrade_text();
		$this->init_texts();
		$this->init_fix_messages();
	}

	public function init_texts() {
		$this->text['uploads_index']            = [
			$this->action_title     => __( 'Reduce upload directory size', $this->text_domain ),
			$this->overview         => sprintf(__( 'The total file count of uploads directory exceeds the acceptable limits. Your website will open very slow and eventually it may get slower by the time. In extreme cases, %sone.com%s may suspend your domain temporarily.', $this->text_domain ),'<a target="_blank" href="https://www.one.com">','</a>' ) ,
			$this->fix_button_text  => '',
			$this->how_to_fix       => sprintf(__( 'Reduce the size of the uploads folder by cleaning up the Media Library in your WordPress dashboard. Check out %sour guide%s to learn more about the WordPress Media Library and how to clean it up.', $this->text_domain) ,'<a target="_blank" href="https://help.one.com/hc/en-us/articles/4402376353425-Clean-up-the-WordPress-media-library">','</a>' ),
			$this->how_to_fix_lite  => '',
			$this->fix_confirmation => '',
			$this->status_desc      => [
				$this->status_resolved => __( 'Uploads directory size is optimized', $this->text_domain ),
				$this->status_open     => __( 'The index of uploads directory is huge. Clean up these directories:', $this->text_domain )
			]
		];
		$this->text['options_table_count']      = [
			$this->action_title     => __( 'Optimize options table', $this->text_domain ),
			$this->overview         => __( 'The total row count of options table exceeds the acceptable limits. Your website will open very slow and eventually it may get slower by the time. In extreme cases, one.com may suspend your domain temporarily.<br/>Automatic backups will also fail with time.', $this->text_domain ),
			$this->fix_button_text  => '',
			$this->how_to_fix       => __( 'Use PHPMyAdmin to delete the unwanted rows in the "$prefix_"options table.', $this->text_domain ) ,
			$this->how_to_fix_lite  => '',
			$this->fix_confirmation => '',

			$this->status_desc => [
				$this->status_resolved => __( 'Options table is optimized', $this->text_domain ),
				$this->status_open     => __( 'The size of options table is huge.', $this->text_domain )
			]
		];
		$this->text['staging_time']             = [
			$this->action_title     => __( 'Delete old  staging website.', $this->text_domain ),
			$this->overview         => __( 'A staging site which is not touched for long become vulnerable to hacking attacks. We therefore recommend deleting staging sites that you do not need.', $this->text_domain ),
			$this->how_to_fix       => __( 'Staging sites are managed from the one.com staging plugin. This is also where you can delete it.', $this->text_domain ),
			$this->how_to_fix_lite  => '',
			$this->fix_confirmation => '',
			$this->fix_button_text  => __( 'Go to Staging section', $this->text_domain ),

			$this->status_desc => [
				$this->status_resolved => __( 'All your old staging websites are deleted', $this->text_domain ),
				$this->status_open     => __( 'You have a staging site which is not touched for more than 6 months', $this->text_domain )
			]
		];
		$this->text['backup_zip']               = [
			$this->action_title     => __( 'Clean backups', $this->text_domain ),
			$this->overview         => __( 'Customers often create and forget about the backup zip files. These backup zip files can be downloaded by hackers and this downloaded backup can be analysed for vulnerabilities.', $this->text_domain ),
			$this->how_to_fix       => __( 'Delete the backup zips one-by-one in the shown list.', $this->text_domain ) ,
			$this->how_to_fix_lite  => '',
			$this->fix_confirmation => __( 'File %s deleted', $this->text_domain ),
			$this->fix_button_text  => __( 'Fix by deleting', $this->text_domain ),

			$this->status_desc => [
				$this->status_resolved => __( 'No old or obsolete backups', $this->text_domain ),
				$this->status_open     => __( 'Some old or obsolete backup files are present on your webspace', $this->text_domain )
			]
		];
		$this->text['performance_cache']        = [
			$this->action_title     => __( 'Enable Performance Cache.', $this->text_domain ),
			$this->overview         => __( 'With <a target="_blank" href="https://www.one.com">one.com</a> Performance Cache enabled your website loads a lot faster. We save a cached copy of your website on a Varnish server, that will then be served to your next visitors. This is especially useful if you have a lot of visitors. It also helps to improve your SEO ranking.', $this->text_domain ),
			$this->fix_button_text  => __( 'Enable Performance Cache', $this->text_domain ),
			$this->how_to_fix       => __( 'Click the button below.', $this->text_domain ),
			$this->how_to_fix_lite  => '',//__( 'Go to the <a target="_blank" href="' . admin_url( 'admin.php?page=onecom-wp-plugins' ) . '">Plugins section</a> of the one.com plugin and make sure one.com Performance Cache plugin is installed and Cache activated.', $this->text_domain ),
			$this->fix_confirmation => __( 'Performance cache enabled.', $this->text_domain ),
			$this->status_desc      => [
				$this->status_resolved => __( 'Performance cache is enabled', $this->text_domain ),
				$this->status_open     => __( 'Performance cache is not enabled', $this->text_domain )
			]
		];
		$this->text['updated_long_ago']         = [
			$this->action_title    => __( 'Use compatible plugins', $this->text_domain ),
			$this->overview        => __( 'Plugins (that are not maintained anymore) pose security as well as stability threats.<br/>If a plugin is not tested with last 2 major version of WordPress, it is advisable to use alternatives.', $this->text_domain ),
			$this->fix_button_text => '',
			$this->how_to_fix      => __( "Search for alternatives to reported plugins and replace these.", $this->text_domain ) ,
			$this->upsell_text     => __( 'Need help? Upgrade to one.com Managed WordPress for free and get specialised WordPress Support.', $this->text_domain ) . '<a class="onecom__open-modal"> ' . __( 'Free upgrade', $this->text_domain ) . '</a>',
			$this->how_to_fix_lite => __( 'Search for alternatives to reported plugins and replace these.', $this->text_domain ),
			$this->status_desc     => [
				$this->status_resolved => __( 'All installed plugins are compatible with the last two major releases of WordPress', $this->text_domain ),
				$this->status_open     => __( 'One or more installed plugins are not compatible with the last two major WordPress versions.', $this->text_domain )
			]
		];
		$this->text['pingbacks']                = [
			$this->action_title     => __( 'Disable trackbacks &  pingbacks', $this->text_domain ),
			$this->overview         => __( 'Pingbacks notify a website when it has been mentioned by another website, like a form of courtesy communication. However, these notifications can be sent to any website willing to receive them, opening you up to DDoS attacks, which can take your website down in seconds and fill your posts with spam comments', $this->text_domain ),
			$this->fix_button_text  => __( 'Disable pingback', $this->text_domain ),
			$this->how_to_fix       => __( 'Click below.', $this->text_domain ),
			$this->how_to_fix_lite  => sprintf( __( 'Go to %sWordPress admin > Settings > Discussion%s and uncheck the boxes that say

%s %sAttempt to notify any blogs linked to from the post%s
%sAllow link notifications from other blogs (pingbacks and trackbacks) on new posts%s %s', $this->text_domain ), '<strong>', '</strong>', '<ol>', '<li>', '</li>', '<li>', '</li>', '</ol>' ),
			$this->fix_confirmation => __( 'You have successfuly disabled pingbacks and trackbacks.', $this->text_domain ),
			$this->upsell_text      => __( 'one.com Managed WordPress comes with a quick fix so you can spend more time on your website, less on security', $this->text_domain ) . $this->open_modal_link,
			$this->fix_confirmation => __( 'Pingbacks are disabled.', $this->text_domain ),
			$this->status_desc      => [
				$this->status_resolved => __( 'Pingbacks are disabled.', $this->text_domain ),
				$this->status_open     => __( 'You have pingbacks enabled on your site.', $this->text_domain )
			]
		];
		$this->text['logout_duration']          = [
			$this->action_title     => __( 'Logout duration', $this->text_domain ),
			$this->overview         => __( 'By default, WordPress allows users to be logged in for 14 days. This can create security issues if a User logs in on a public computer and forgets to logout. To prevent this, you can reduce the duration for which a user session is remembered.', $this->text_domain ),
			$this->fix_button_text  => sprintf( __( 'Change logout time to %s hours', $this->text_domain ), '4' ),
			$this->fix_confirmation => sprintf( __( 'Logout time changed to %s hours', $this->text_domain ), '4' ),
			$this->how_to_fix       => __( 'Click on fix now below', $this->text_domain ),
			$this->status_desc      => [
				$this->status_resolved => __( 'You are using optimal logout duration.', $this->text_domain ),
				$this->status_open     => __( 'You are using the default login expiration.', $this->text_domain )
			]
		];
		$this->text['xmlrpc']                   = [
			$this->action_title     => __( 'Disable XML-RPC', $this->text_domain ),

			$this->overview         => __( 'XML-RPC is a legacy technology that is being used by Jetpack-plugin and the WordPress mobile application. In case you are not using neither of these, it is safe and recommended to disable it to further protect your website.', $this->text_domain ),
			$this->fix_button_text  => __( 'Disable XML RPC', $this->text_domain ),
			$this->how_to_fix       => __( 'Click the button below.', $this->text_domain ),
			$this->how_to_fix_lite  => __( 'You need to paste following code snippet in your .htaccess file', $this->text_domain ) . '<code>
<p>#one.com block xmlrpc</p>
<p>&lt;Files xmlrpc.php&gt;</p>
<p>order deny,allow</p>
<p>deny from all</p>
<p>&lt;/Files&gt;</p>
<p>#one.com block xmlrpc END</p></code>',
			$this->fix_confirmation => __( 'XML RPC disabled.', $this->text_domain ),
			$this->status_desc      => [
				$this->status_resolved => __( 'You have disabled XML RPC in your site.', $this->text_domain ),
				$this->status_open     => __( 'XML-RPC is currently enabled.', $this->text_domain )
			],
			$this->upsell_text      => __( 'one.com Managed WordPress comes with a quick fix so you can spend more time on your website, less on security ', $this->text_domain ) . $this->open_modal_link,
		];
		$this->text['spam_protection']          = [
			$this->action_title     => __( 'Install a spam protection plugin', $this->text_domain ),
			$this->overview         => __( 'Unprotected forms on your site are biggest source of spam registrations and spam comments.<br/>We recommend enabling a spam protection plugin.', $this->text_domain ),
			$this->fix_button_text  => __( 'Install one.com plugin', $this->text_domain ),
			$this->how_to_fix       => __( 'Install and activate one.com spam protection plugin.', $this->text_domain ),
			$this->how_to_fix_lite  => __( 'Install and activate a spam protection plugin - go to <a target="_blank" href="' . admin_url( 'admin.php?page=onecom-wp-recommended-plugins' ) . '">recommended plugins</a> section and find your preferred option', $this->text_domain ),
			$this->fix_confirmation => __( 'one.com spam plugin is now installed and activated.', $this->text_domain ),
			$this->upsell_text      => __( 'one.com Managed WordPress comes with spam protection plugin and more included.', $this->text_domain ) . $this->open_modal_link,
			$this->status_desc      => [
				$this->status_resolved => __( 'You have spam protection enabled.', $this->text_domain ),
				$this->status_open     => __( "You don't have any spam protection enabled.", $this->text_domain )
			]
		];
		$this->text['login_attempts']           = [
			$this->action_title     => __( 'Limit failed logins', $this->text_domain ),
			$this->overview         => __( 'By default, WordPress allows users to enter passwords as many times as they want. Hackers may try to exploit this by using scripts that enter different combinations until your website cracks.<br/>To prevent this, you can limit the number of failed login attempts per user.', $this->text_domain ),
			$this->fix_button_text  => __( 'Limit failed logins', $this->text_domain ),
			$this->upsell_text      => __( 'one.com Managed WordPress comes with this feature included and more.' ) . $this->open_modal_link,
			$this->how_to_fix       => __( 'Failed login attempts can be easily limited by activating Spam protection plugin just a click.', $this->text_domain ),
			$this->how_to_fix_lite  => sprintf(__( 'Please install your preferred plugin such as <a target="_blank" href="https://wordpress.org/plugins/login-lockdown/">Login Lockdown</a> and limit the failed login attempts.', $this->text_domain ), '<a target="_blank" href="https://wordpress.org/plugins/login-lockdown/">', '</a>'),
			$this->fix_confirmation => __( 'Failed login attempts limited', $this->text_domain ),

			$this->status_desc => [
				$this->status_resolved => __( 'Failed login attempts are limited.', $this->text_domain ),
				$this->status_open     => __( 'No limit for failed logins.', $this->text_domain )
			]
		];
		$this->text['login_recaptcha']          = [
			$this->action_title     => __( 'Protect your login-form', $this->text_domain ),
			$this->overview         => __( 'By default, WordPress does not have any feature to protect the login form against brute force attacks.<br/>To address this, you can use Google reCaptcha in login form.', $this->text_domain ),
			$this->fix_button_text  => __( 'Enable reCaptcha', $this->text_domain ),
			$this->how_to_fix       => __( "The login form can be protected by entering Site key and Site secret obtained from <a target='_blank' href='https://www.google.com/recaptcha/admin/create'>Google's dashboard</a>.<br/>Go to Google ReCaptcha Dasboard and follow these steps:", $this->text_domain ) . '<ol><li>' . __( "Get the Site key and Site secret from <a target='_blank' href='https://www.google.com/recaptcha/admin/create'>Google's ReCaptcha Dashboard</a>.", $this->text_domain ) . '</li><li>' . __( 'Click Enable reCaptcha below.', $this->text_domain ) . '</li><li>' . __( 'Enter the Site key and Site secret values and click enter', $this->text_domain ) . '</li></ol>' ,
			$this->how_to_fix_lite  => __( 'You can install a suitable plugin from WordPress plugin repo to fix this', $this->text_domain ),
			$this->fix_confirmation => __( 'Login form protected with reCaptcha', $this->text_domain ),
			$this->upsell_text      => __( 'one.com Managed WordPress comes with login protection included and more.', $this->text_domain ) . $this->open_modal_link,
			$this->status_desc      => [
				$this->status_resolved => __( 'Your login form is protected.', $this->text_domain ),
				$this->status_open     => __( 'Your login form is unprotected', $this->text_domain )
			]
		];
		$this->text['asset_minification']       = [
			$this->action_title    => __( 'Asset minification Title', $this->text_domain ),
			$this->overview        => '',
			$this->fix_button_text => '',

			$this->status_desc => [
				$this->status_resolved => '',
				$this->status_open     => '',
			]
		];
		$this->text['php_updates']              = [
			$this->action_title => __( 'Update to latest PHP version', $this->text_domain ),
			$this->overview     => __( 'PHP is the software that powers WordPress. It interprets the WordPress code and generates web pages people view. Naturally, PHP comes in different versions and is regularly updated. As newer versions are released, WordPress drops support for older PHP versions in favour of newer, faster versions with fewer bugs.', $this->text_domain ),
			$this->how_to_fix   => sprintf(__( 'You can update PHP from the one.com control panel, under PHP & Database - MariaDB. Check our guide for more information: <a target="_blank" href="https://help.one.com/hc/en/articles/360000449117-How-do-I-update-PHP-for-my-WordPress-site-">How do I update PHP for my WordPress site?</a>', $this->text_domain ), '<a href="'.OC_CP_LOGIN_URL.'" target="_blank">' ,'</a>', '<a target="_blank" href="https://help.one.com/hc/en/articles/360000449117-How-do-I-update-PHP-for-my-WordPress-site-">' ,'</a>'),

			$this->status_desc => [
				$this->status_resolved => __( 'You are using the recommended PHP version. Boom!', $this->text_domain ),
				$this->status_open     => __( 'You are not using the latest stable PHP version.', $this->text_domain )
			]
		];
		$this->text['plugin_updates']           = [
			$this->action_title    => __( 'Update plugin(s)', $this->text_domain ),
			$this->overview        => __( 'Plugins that are not updated to latest version make your site vulnerable to security attacks. You should also delete plugins that are not in use.', $this->text_domain ),
			$this->fix_button_text => '',
			$this->how_to_fix      => sprintf(__( 'Plugins are managed from the Plugins section in WP Admin. %sGo to Plugins%s and update plugins', $this->text_domain ),'<a target="_blank" href="'. admin_url('plugins.php') .'">','</a>'),
			$this->status_desc     => [
				$this->status_resolved => __( 'Great, all your plugins are updated.', $this->text_domain ),
				$this->status_open     => __( 'These plugins are not updated', $this->text_domain )
			]
		];
		$this->text['theme_updates']            = [
			$this->action_title    => __( 'Update theme(s)', $this->text_domain ),
			$this->overview        => __( 'Using outdated themes can break your site and generate potential security risks. You should also delete themes you do not use.', $this->text_domain ),
			$this->fix_button_text => '',
			$this->how_to_fix      => sprintf(__( 'Update your themes to the latest version. We recommend that you remove any themes that you don’t plan on using. %sGo to Themes%s and update them.', $this->text_domain ),'<a target="_blank" href="'. admin_url('themes.php') .'">','</a>'),

			$this->status_desc => [
				$this->status_resolved => __( 'All your themes are up to date. Good stuff! ', $this->text_domain ),
				$this->status_open     => __( 'These themes are not up to date', $this->text_domain )
			]
		];
		$this->text['wp_updates']               = [
			$this->action_title    => __( 'Update WordPress to latest version', $this->text_domain ),
			$this->overview        => str_replace('\n', '', __( 'WordPress is an extremely popular platform, and with that popularity comes hackers that increasingly want to exploit WordPress based websites. Leaving your WordPress installation out of date is an almost guaranteed way to get hacked as you’re missing out on the latest security patches.', $this->text_domain )),
			$this->fix_button_text => '',
			$this->how_to_fix      => str_replace('\n',  '', sprintf(__( 'Update WordPress to the latest version, especially minor updates are important because they usually include security fixes. Check this guide for more instructions:  %sHow do I update a CMS like WordPress?%s', $this->text_domain ),'<a target="_blank" href="https://help.one.com/hc/en/articles/360001621938-How-do-I-update-a-CMS-like-WordPress-and-Joomla-">','</a>')) ,
			$this->how_to_fix_lite => str_replace('\n', '', sprintf(__( 'Update WordPress to the latest version, especially minor updates are important because they usually include security fixes. Check this guide for more instructions:  %sHow do I update a CMS like WordPress?%s', $this->text_domain ),'<a target="_blank" href="https://help.one.com/hc/en/articles/360001621938-How-do-I-update-a-CMS-like-WordPress-and-Joomla-">','</a>')),
			$this->status_desc     => [
				$this->status_resolved => __( 'You are using the latest WordPress version', $this->text_domain ),
				$this->status_open     => __( "You aren't using the newest version of WordPress", $this->text_domain )
			]
		];
		$this->text['wp_connection']            = [
			$this->action_title    => __( 'Cannot connect to wordpress.org', $this->text_domain ),
			$this->overview        => __( 'WordPress websites fetch critical information related to updates etc. from wordpress.org if a site is unable to connect to wordpress.org, it poses security risk since the latest update information is not available.', $this->text_domain ),
			$this->fix_button_text => '',
			$this->how_to_fix      => sprintf(__( "Try to disable all plugins and themes and do a new scan to check if the connection to wordpress.org is restored. If this worked, enable your plugins one-by-one, to find the culprit. If this didn't work, %splease contact our chat support%s.", $this->text_domain ),"<a target='_blank'  href='https://help.one.com/hc/en-us'>","</a>"),
			$this->status_desc     => [
				$this->status_resolved => __( 'The connection to wordpress.org succeeded', $this->text_domain ),
				$this->status_open     => __( 'The connection to wordpress.org failed', $this->text_domain )
			]
		];
		$this->text['core_updates']             = [
			$this->action_title    => __( 'Enable automatic minor core updates', $this->text_domain ),
			$this->overview        => __( 'Enable automatic minor core updates again. Leaving your WordPress installation out of date is an almost guaranteed way to get hacked as you’re missing out on the latest security patches.', $this->text_domain ),
			$this->fix_button_text => '',
			$this->how_to_fix      => __( "Enable automatic minor WordPress core updates again, either by changing a setting in the plugin you use to manage updates or by changing a setting in wp - config . if you would like to know why updates are so important, check this guide:Why you should always update WordPress .if you are using any plugin such as Easy Updates Manager, please deactivate those . if you are not using any such plugin, please open wp-config. php file and look for define( 'WP_AUTO_UPDATE_CORE', false ); and comment out this line.", $this->text_domain ) ,
			$this->how_to_fix_lite => __( "Enable automatic minor WordPress core updates again, either by changing a setting in the plugin you use to manage updates or by changing a setting in wp - config . if you would like to know why updates are so important, check this guide:Why you should always update WordPress .
if you are using any plugin such as Easy Updates Manager, please deactivate those .
if you are not using any such plugin, please open wp-config. php file and look for define( 'WP_AUTO_UPDATE_CORE', false ); and comment out this line.", $this->text_domain ),
			$this->status_desc     => [
				$this->status_resolved => __( 'The automatic minor core updates are enabled in your site.', $this->text_domain ),
				$this->status_open     => __( 'Automatic minor core updates are disabled', $this->text_domain )
			]
		];
		$this->text['ssl']                      = [
			$this->action_title    => __( 'Use a valid SSL certificate', $this->text_domain ),
			$this->overview        => sprintf(__( "SSL certification enabled HTTPS prevent intruders from tampering with the communications between your websites and your users' browsers. All domains hosted with %sone.com%s automatically get an SSL certificate assigned, so this state means that something is wrong with the configuration.", $this->text_domain ),"<a target='_blank' href='https://www.one.com'>","</a>"),
			$this->fix_button_text => '',
			$this->how_to_fix      => __( "Let customer support check and fix this.", $this->text_domain ) ,
			$this->how_to_fix_lite => sprintf(__( 'Please contact our chat support, so we can check what is wrong and fix it.', $this->text_domain ), '<a href="https://help.one.com/hc/en-us" target="_blank">' ,'</a>'),
			$this->status_desc     => [
				$this->status_resolved => __( 'Your site has a valid SSL certificate', $this->text_domain ),
				$this->status_open     => __( "Your site doesn't have a working SSL certificate", $this->text_domain )
			]
		];
		$this->text['file_execution']           = [
			$this->action_title     => __( 'Prevent file execution in uploads folder', $this->text_domain ),
			$this->overview         => __( "By default, a plugin/theme vulnerability could allow a PHP file or other files to get uploaded into your site's directories and in turn execute harmful scripts that can wreak havoc on your website. Prevent this altogether by disabling direct execution in your uploads folder.", $this->text_domain ),
			$this->fix_button_text  => __( 'Protect uploads folder', $this->text_domain ),
			$this->fix_confirmation => __( 'Uploads folder is protected', $this->text_domain ),
			$this->how_to_fix       => __( "Click the button to prevent file execution.", $this->text_domain ),
			$this->how_to_fix_lite => __('Follow the steps here: <a target="_blank" href="https://help.one.com/hc/en/articles/360002102258-Disable-file-execution-in-the-WordPress-uploads-folder">Disable file execution in the WordPress uploads folder</a>', $this->text_domain),
			$this->upsell_text      => __( 'one.com Managed WordPress comes with an easy fix and more.', $this->text_domain ) . '<a  class="onecom__open-modal"> ' . __( 'Free upgrade', $this->text_domain ) . '</a>',
			$this->status_desc      => [
				$this->status_resolved => __( 'Your uploads folder is protected against malicious file execution', $this->text_domain ),
				$this->upsell_text => __( "one.com Managed WordPress comes with an easy fix and more.<br/><a>Free Upgrade</a>", $this->text_domain ),
				$this->status_open     => __( 'File execution in your uploads folder is enabled', $this->text_domain )
			]
		];
		$this->text['file_permissions']         = [
			$this->action_title    => __( 'Reduce File Permissions as recommended by wordpress.org', $this->text_domain ),
			$this->overview        => __( 'It is crucial to set correct file permissions to each file and directory in your WordPress setup. Incorrect file permission can introduce security vulnerabilities and make your site and easy target for hackers.', $this->text_domain ),
			$this->fix_button_text => '',
			$this->how_to_fix_lite => sprintf(__( 'To fix this, you need to use an FTP client to change the permissions of your files to 644, and of your folders to 755. Check our guide for step-by-step instructions: Change the file permissions via an FTP client', $this->text_domain ), '<a href="https://help.one.com/hc/en-us/articles/360002087097-Change-the-file-permissions-via-an-FTP-client" target="_blank">', '</a>'),
			$this->how_to_fix      => sprintf(__( 'To fix this, you need to use an FTP client to change the permissions of your files to 644, and of your folders to 755. Check our guide for step-by-step instructions: Change the file permissions via an FTP client', $this->text_domain ), '<a href="https://help.one.com/hc/en-us/articles/360002087097-Change-the-file-permissions-via-an-FTP-client" target="_blank">', '</a>') ,

			$this->status_desc => [
				$this->status_resolved => __( 'The file permissions are set correctly', $this->text_domain ),
				$this->status_open     => __( 'Your site has incorrect file and folder permissions', $this->text_domain )
			]
		];
		$this->text['DB']                       = [
			$this->action_title    => __( 'Some title', $this->text_domain ),
			$this->overview        => __( 'Some overview', $this->text_domain ),
			$this->fix_button_text => __( 'Fix', $this->text_domain ),

			$this->status_desc => [
				$this->status_resolved => __( 'Resolved', $this->text_domain ),
				$this->status_open     => __( 'Open', $this->text_domain )
			]
		];
		$this->text['file_edit']                = [
			$this->action_title    => __( 'Disable file editing', $this->text_domain ),
			$this->overview        => __( "When file editing is enabled, Administrator users can edit the code of themes and plugins directly from the WordPress dashboard. This is a potential security risk because not everyone has the skills to write code, and if a hacker breaks in, they would have access to all your data. That's why we recommend disabling it.", $this->text_domain ),
			$this->fix_button_text => '',
			$this->how_to_fix_lite => sprintf(__( "To fix this you need to add a line to your wp-config.php file which disables file editing options from your dashboard. We have created a guide with step-by-step instructions: %sDisable file editing in WordPress admin.%s", $this->text_domain ),'<a target="_blank" href="https://help.one.com/hc/articles/360002104398">','</a>'),
			$this->how_to_fix      => sprintf(__( "To fix this you need to add a line to your wp-config.php file which disables file editing options from your dashboard. We have created a guide with step-by-step instructions: %sDisable file editing in WordPress admin.%s", $this->text_domain ),'<a target="_blank" href="https://help.one.com/hc/articles/360002104398">','</a>') ,
			$this->status_desc     => [
				$this->status_resolved => __( 'File editing from WordPress admin is disabled', $this->text_domain ),
				$this->status_open     => __( 'File editing from WordPress admin is allowed', $this->text_domain )
			]
		];
		$this->text['usernames']                = [
			$this->action_title     => __( 'Use custom usernames', $this->text_domain ),
			$this->overview         => __( 'Hackers often try to gain access to your WordPress administration with a Brute Force Attack, where robots try millions of different password and username combinations to try to log in. To make it more difficult to guess your login details, we recommend creating a unique username', $this->text_domain ),
			$this->how_to_fix       => __( 'Change the common username to a personal one, based on your name or nickname.', $this->text_domain ),
			$this->fix_button_text  => __( 'Change user name', $this->text_domain ),
			$this->fix_confirmation => __( 'User name is changed', $this->text_domain ),
			$this->status_desc      => [
				$this->status_resolved => __( 'You are using custom usernames for your login', $this->text_domain ),
				$this->status_open     => __( 'You are using a generic username that is easy to guess', $this->text_domain )
			]
		];
		$this->text['dis_plugin']               = [
			$this->action_title     => __( "You're using a plugin which we advice against", $this->text_domain ),
			$this->overview         => sprintf(__( 'Some plugins does the opposite of what they promise. Others make your site slow or are easy to hack. We therefore keep a list of discouraged plugins. See it here:  %sDiscouraged WordPress plugins%s.', $this->text_domain ),'<a target="_blank" href="https://help.one.com/hc/en/articles/115005586029-Discouraged-WordPress-plugins">','</a>'),
			$this->fix_button_text  => __( 'Deactivate plugin(s)', $this->text_domain ),
			$this->fix_confirmation => __( 'These plugins are now deactivated:', $this->text_domain ),
			$this->how_to_fix       => __( 'Deactivate the discouraged plugins. ', $this->text_domain ),
			$this->status_desc      => [
				$this->status_resolved => __( 'These plugins are now deactivated:', $this->text_domain ),
				$this->status_open     => __( 'You are using one or more of the plugins we advice against:', $this->text_domain )
			]
		];
		$this->text['woocommerce_sessions']     = [
			$this->action_title     => __( 'Expired woocommerce session data', $this->text_domain ),
			$this->overview         => __( 'You have some expired session data present in your database. <br/>Old sessions and customer carts will be stored in your database until they expire, so if you have modified the WooCommerce session expiration time in Clear Cart for WooCommerce, we recommend that you clear all existing WooCommerce sessions.', $this->text_domain ),
			$this->fix_button_text  => __( 'Fix now', $this->text_domain ),
			$this->how_to_fix       => __( 'Click Fix now to  automatically clean up the session garbage.', $this->text_domain ),
			$this->fix_confirmation => __( 'The expired woocommerce session data is deleted.', $this->text_domain ),
			$this->status_desc      => [
				$this->status_resolved => __( 'The expired woocommerce session data is deleted.', $this->text_domain ),
				$this->status_open     => __( 'You have some expired session data present in your database.', $this->text_domain )
			]
		];
		$this->text['error_reporting']          = [
			$this->action_title     => __( 'Hide error reporting', $this->text_domain ),
			$this->overview         => __( "Developers often use the built-in PHP and scripts error debugging feature, which displays code errors on the frontend of your website. It's useful for active development, but on live sites provides hackers yet another way to find loopholes in your site's security.", $this->text_domain ),
			$this->fix_button_text  => __( 'Fix now', $this->text_domain ),
			$this->how_to_fix       => sprintf(__( 'You can disable PHP error reporting in the one.com control panel and WordPress debugging in the wp.config.php file.Check these two guides for more details on how to manage these settings: <a target="_blank" href="https://help.one.com/hc/en-us/articles/115005593705-How-do-I-enable-error-messages-for-PHP-">How do I enable error messages for PHP?</a> and <a target="_blank" href="https://help.one.com/hc/en-us/articles/115005594045-How-do-I-enable-debugging-in-WordPress-">How do I enable debugging in WordPress?</a>', $this->text_domain ),'<a target="_blank" href="https://help.one.com/hc/en-us/articles/115005593705-How-do-I-enable-error-messages-for-PHP-">', '</a>', '<a target="_blank" href="https://help.one.com/hc/en-us/articles/115005594045-How-do-I-enable-debugging-in-WordPress-">', '</a>'),
			$this->fix_confirmation => '',
			$this->status_desc      => [
				$this->status_resolved => __( 'Error reporting and debugging mode are disabled', $this->text_domain ),
				$this->status_open     => __( 'Your site is configured to display errors to visitors', $this->text_domain )
			]
		];
		$this->text['user_enumeration']         = [
			$this->action_title     => __( 'Disable user enumeration', $this->text_domain ),
			$this->overview         => __( "One of the more common methods for bots and hackers to gain access to your website is to find out login usernames and brute force the login area with tons of dummy passwords. The hope is that one the username and password combos will match, and voilà - they have access (you'd be surprised how common weak passwords are!).", $this->text_domain ).
                __("There are two sides to this hacking method - the username and the password. The passwords are random guesses, but (unfortunately) the username is easy to get. Simply typing the query string ?author=1, ?author=2 and so on, will redirect the page to /author/username/ - bam, the bot now has your usernames to begin brute force attacks with.", $this->text_domain ).
__("This security recommendation locks down your website by preventing the redirect, making it much harder for bots to get your usernames. We highly advise actioning this recommendation.", $this->text_domain ),
			$this->fix_button_text  => __( 'Disable user enumeration', $this->text_domain ),
			$this->upsell_text      => __( 'one.com Managed WordPress comes with this feature included and more.', $this->text_domain ) . '<a class="onecom__open-modal"> ' . __( 'Free upgrade', $this->text_domain ) . '</a>',
			$this->how_to_fix       => __( 'Click the button below.', $this->text_domain ),
			$this->how_to_fix_lite  => sprintf(__( 'Install a plugin, for example, <a target="_blank" href="https://wordpress.org/plugins/stop-user-enumeration/">Stop User Enumeration</a>, and use that to disable User Enumeration.', $this->text_domain ), '<a target="_blank" href="https://wordpress.org/plugins/stop-user-enumeration/">', '</a>'),
			$this->fix_confirmation => __( 'User enumeration is disabled.', $this->text_domain ),
			$this->status_desc      => [
				$this->status_resolved => __( 'User enumeration is disabled.', $this->text_domain ),
				$this->status_open     => __( 'User enumeration is enabled on your site.', $this->text_domain )
			]
		];
		$this->text['optimize_uploaded_images'] = [
			$this->action_title     => __( 'Optimize uploaded images', $this->text_domain ),
			$this->overview         => __( "By default, WordPress does not optimize images very well. We recommend using the Imagify plugin to increase performance and visitor experience on your website with faster image loading speed.", $this->text_domain ),
			$this->fix_button_text  => __( 'Go to Imagify', $this->text_domain ),
			$this->upsell_text      => '',
            $this->how_to_fix       => (!is_plugin_active('imagify/imagify.php')) ? __('Install & activate the Imagify plugin, go to Imagify settings, and set up the plugin following the instructions on the page.', $this->text_domain) : sprintf(__('Go to %sImagify settings%s and set up the plugin following the instructions on the page.', $this->text_domain), '<a target="_blank" href="' . admin_url('options-general.php?page=imagify') . '">', '</a>'),
            $this->how_to_fix_lite => (!is_plugin_active('imagify/imagify.php')) ? __('Install & activate the Imagify plugin, go to Imagify settings, and set up the plugin following the instructions on the page.', $this->text_domain) : sprintf(__('Go to %sImagify settings%s and set up the plugin following the instructions on the page.', $this->text_domain), '<a target="_blank" href="' . admin_url('options-general.php?page=imagify') . '">', '</a>'),
			$this->fix_confirmation => '',
			$this->status_desc      => [
				$this->status_resolved => (is_plugin_active('imagify/imagify.php'))?__( 'Imagify is now set up. The images you upload will be optimized.', $this->text_domain ):__( 'The images you upload will be optimized.', $this->text_domain ),
				$this->status_open     => __( 'Imagify is not set up', $this->text_domain )
			]
		];
		$this->text['enable_cdn']               = [
			$this->action_title     => __( 'Enable Performance CDN.', $this->text_domain ),
			$this->overview         => __( "A content delivery network (CDN) is a system of distributed servers that deliver pages and other web content to a user, based on the geographic locations of the user, the origin of the webpage and the content delivery server. This is especially useful if you have a lot of visitors spread across the globe.", $this->text_domain ),
			$this->fix_button_text  => __( 'Enable CDN', $this->text_domain ),
			$this->upsell_text      => '',
			$this->how_to_fix       => __( 'Click the button below.', $this->text_domain ),
			$this->how_to_fix_lite  => '',//str_replace('<a target="_blank" href="' . admin_url( 'admin.php?page=onecom-wp-plugins' ) . '">one.com</a>', 'one.com', __( 'Go to the <a target="_blank" href="' . admin_url( 'admin.php?page=onecom-wp-plugins' ) . '">Plugins section</a> of the <a target="_blank" href="' . admin_url( 'admin.php?page=onecom-wp-plugins' ) . '">one.com</a> plugin and make sure one.com Performance Cache plugin is installed and CDN activated.', $this->text_domain )),
			$this->fix_confirmation => __( 'CDN is enabled.', $this->text_domain ),
			$this->status_desc      => [
				$this->status_resolved => __( 'CDN is enabled', $this->text_domain ),
				$this->status_open     => __( 'CDN is not enabled', $this->text_domain )
			]
		];
        $this->text['login_protection']               = [
            $this->action_title     => __( 'Enable one.com Advanced Login Protection', $this->text_domain ),
            $this->overview         => __( 'We recommend that you enable the Advanced login Protection in the one.com control panel. This means you won’t need to remember passwords for your WordPress sites and your login will be more protected.', $this->text_domain ),
            $this->fix_button_text  => __( 'Go to one.com control panel', $this->text_domain ),
            $this->upsell_text      => '',
            $this->how_to_fix       => __( 'Click the button below.', $this->text_domain ),
            $this->how_to_fix_lite  => __( 'Click the button below.', $this->text_domain ),
            $this->fix_confirmation => __( 'Advanced login protection is enabled.', $this->text_domain ),
            $this->status_desc      => [
                $this->status_resolved => __( 'Advanced login protection is enabled.', $this->text_domain ),
                $this->status_open     => __( 'Advanced login protection is disabled.', $this->text_domain )
            ]
        ];
	}

	public function get_text( $check ): array {
		$refined_check = str_replace( 'check_', '', $check );

		return $this->text[ $refined_check ];
	}

	public function init_upgrade_text(): array {
		$this->upgrade_modal_text['title'] = __( 'Make your WordPress more powerful', $this->text_domain );

		$body                             = __( 'Spend less time worrying about your site and more time growing your business with one.com Managed WordPress.', $this->text_domain );
		$body                             .= '<ul>';
		$body                             .= '<li>' . __( 'Quick fix or ignore recommendations', $this->text_domain ) . '</li>';
		$body                             .= '<li>' . __( 'Get better performance with Performance Cache and CDN', $this->text_domain ) . '</li>';
		$body                             .= '<li>' . __( 'Get notified about security with Vulnerability Monitoring', $this->text_domain ) . '</li>';
		$body                             .= '<li>' . __( 'Get helpful tips with Advanced Error Page', $this->text_domain ) . '</li>';
		$body                             .= '<li>' . __( 'Get access to our Premium themes', $this->text_domain ) . '</li>';
		$body                             .= '<li>' . __( 'Increase your authentication security', $this->text_domain ) . '</li>';
		$body                             .= '<li>' . __( 'Host on our WordPress servers built for speed', $this->text_domain ) . '</li>';
		$body                             .= '</ul>';
		$this->upgrade_modal_text['body'] = $body;

		return $this->upgrade_modal_text;
	}

	public function init_fix_messages() {
		$this->quick_fix_messages = [
			'error'   => [
				'username_invalid'     => __( 'Please enter a valid username', $this->text_domain ),
				'username_not_changed' => __( 'User name could not be changed', $this->text_domain )
			],
			'success' => [
				'username_changed' => __( 'User name is changed', $this->text_domain )
			]
		];
	}
}
