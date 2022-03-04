<?php
declare( strict_types=1 );

/**
 * Class OnecomCheckPlugins
 */
class OnecomCheckPlugins extends OnecomHealthMonitor {
    use OnecomLite;
	public $pcache_plugin = 'onecom-vcache/vcaching.php';

	/**
	 * Check is Performance cache is installed and all its components are active
	 * @return array
	 */
	public function check_performance_cache(): array {
		$result = $this->format_result($this->flag_open);
		if ( ! $this->is_plugin_active( 'onecom-vcache/vcaching.php' ) ) {
			$result = $this->format_result($this->flag_open);
			$result['activate_plugin'] = true;
		    return $result;
		}
		$vc_state = get_option( 'varnish_caching_enable' );
		if ( $vc_state !== 'true' ) {
			$result = $this->format_result( $this->flag_open, '', );
		}

		if ( $vc_state === 'true' ) {
			$result = $this->format_result( $this->flag_resolved, '' );
		}

		return $result;
	}

	public function check_cdn(): array {
		$result = [
			$this->status_key => $this->flag_open
		];
		if ( ! $this->is_plugin_active( 'onecom-vcache/vcaching.php' ) ) {
			return [
				$this->status_key => $this->flag_open,
				'activate_plugin' => true
			];
		}
		$cdn_state = get_option( 'oc_cdn_enabled' );
		if ( $cdn_state !== 'true' ) {
			$result = $this->format_result( $this->flag_open, '' );
		}

		if ( $cdn_state === 'true' ) {
			$result = $this->format_result( $this->flag_resolved, '' );
		}

		return $result;
	}

	/**
	 * Get a list of all the plugins that are not tested with last 2 major version of WP
	 * @return array
	 */
	public function check_plugins_last_update(): array {
		$outdated    = [];
		$plugin_list = get_plugins();
		global $wp_version;
		foreach ( $plugin_list as $key => $p ) {
			$slug        = $this->plugin_slug( $key );
			$tested_upto = $this->get_tested_upto( $slug );
			$diff        = $this->version_compare( $tested_upto, $wp_version );
			if ( $diff > 11 ) {
				$outdated[] = $p['Name'];
			}
		}
		if ( empty( $outdated ) ) {
			$title  = __( 'All the plugins are tested with last 2 major releases of WordPress', $this->text_domain );
			$result = $this->format_result( $this->flag_resolved, $title );
		} else {
			$title = __( 'Some of the plugins are not tested with last 2 major releases of WordPress', $this->text_domain );
			$desc  = __( 'Following plugins are not tested with the last 2 major versions of WordPress. You should consider using their alternatives' );

			$result         = $this->format_result( $this->flag_open, $title, $desc );
			$result['list'] = $outdated;
		}

		return $result;
	}

	/**
	 * Try to get plugin slug from provided string
	 *
	 * @param string $plugin the name of plugin
	 *
	 * @return string
	 */
	public function plugin_slug( string $plugin ): string {
		if ( ! $plugin || $plugin === '' ) {
			return '';
		}
		if ( strpos( $plugin, '/' ) === false ) {
			return $plugin;
		}

		return explode( '/', $plugin )[0];
	}

	/**m
	 * Get plugin info from WordPress plugin API,
	 * and return the version of WP upto which plugin is tested. Returns empty string if
	 * no info is found.
	 *
	 * @param string $slug
	 *
	 * @return string
	 */
	private function get_tested_upto( string $slug ): string {
		$url      = "https://api.wordpress.org/plugins/info/1.0/{$slug}.json";
		$response = wp_remote_get( $url );
		$data     = json_decode( wp_remote_retrieve_body( $response ), true );
        if ( is_array($data) && array_key_exists('tested', $data) && !empty( $data['tested'] ) ){
			return $data['tested'];
		}

		return '';
	}

	/**
	 * Check the difference between provided WP versions.
	 *
	 * @param string $version1 the version of WordPress, plugin is tested upto
	 * @param string $version2 the version of WordPress that is installed
	 *
	 * @return int
	 */
	private function version_compare( string $version1, string $version2 ): int {
		if ( $version1 === '' ) {
			return - 1;
		}
		$ver1_array   = explode( '.', $version1 );
		$ver2_array   = explode( '.', $version2 );
		$numeric_diff = intval( $ver2_array[0] ) - intval( $ver1_array[0] );

//Pad the strings before converting them to version integer. This is done to handle cases like 5.2 and 5.02
		$ver1_array[1] = intval( str_pad( $ver1_array[1], 2, '0' ) );
		$ver2_array[1] = intval( str_pad( $ver2_array[1], 2, '0' ) );

		$fractional_diff = $ver2_array[1] - $ver1_array[1];

// compound the numeric and fractional diff. This is done to address the cases like
// 5.1 - 4.9 = 2
		return $numeric_diff + $fractional_diff;
	}

	/**
	 * Fix the performance cache
	 * @return array
	 */
	public function fix_performance_cache(): array {
		if ( ! $this->is_plugin_active( $this->pcache_plugin ) ) {
			$activation_result = activate_plugin( $this->pcache_plugin );
		}
		$vc_state = update_option( 'varnish_caching_enable', 'true' );
		if ( $vc_state || ( ! $activation_result ) ) {
			return $this->format_result(
				$this->flag_resolved,
				$this->text['performance_cache'][ $this->fix_confirmation ],
				$this->text['performance_cache'][ $this->status_desc ][ $this->status_resolved ] );
		}

		return $this->format_result( $this->flag_open );
	}

	/**
	 * Fix the performance cache
	 * @return array
	 */
	public function fix_performance_cdn(): array {

		$activation_result = '';
		if ( ! $this->is_plugin_active( $this->pcache_plugin ) ) {
			$activation_result = activate_plugin( $this->pcache_plugin );
		}
		$cdn_state = update_option( 'oc_cdn_enabled', 'true' );
		if ( $cdn_state || ( ! $activation_result ) ) {
			return $this->format_result(
				$this->flag_resolved,
				$this->text['enable_cdn'][ $this->fix_confirmation ],
				$this->text['enable_cdn'][ $this->status_desc ][ $this->status_resolved ]
			);
		}

		return $this->format_result( $this->flag_open );
	}

	public function undo_check_performance_cache(): array {
		if ( update_option( 'varnish_caching_enable', "false" ) ) {
			$check = 'performance_cache';

            $ignoreText = $this->ignore_text;
            if(!$this->onecom_is_premium()){
                $ignoreText = '';
            }

			return [
				$this->status_key      => $this->flag_resolved,
				$this->fix_button_text => $this->text[ $check ][ $this->fix_button_text ],
				$this->desc_key        => $this->text[ $check ][ $this->status_desc ][ $this->status_open ],
				$this->how_to_fix      => $this->text[ $check ][ $this->how_to_fix ],
				'ignore_text'          => $ignoreText
			];

		}
	}

	public function check_discouraged_plugins(): array {
		$this->log_entry( 'Scanning for discouraged plugins' );
		$plugins = onecom_fetch_plugins( false, true );
		if ( ! is_wp_error( $plugins ) && ! empty( $plugins ) ) {
			$plugins = $this->discouraged_plugins( $plugins );
		}
		if ( ! empty( $plugins['active'] ) ) {
			$result         = $this->format_result( $this->flag_open );
			$result['list'] = $plugins['active'];
			$result['fix']  = true;
		} else {
			$result = $this->format_result( $this->flag_resolved );
			if ( ! empty( $plugins['inactive'] ) ) {
				$result['list'] = $plugins['inactive'];
			};
		}
		$this->log_entry( 'Finished scanning for discouraged plugins' );

		// @todo oc_sh_save_result( 'discouraged_plugins', $result[ $oc_hm_status ], 1 );


		return $result;
	}

	public function discouraged_plugins( array $plugins = array() ) {

		$discouraged_slugs = [];
		// filter out those plugins that are not installed
		foreach ( $plugins as $key => $plugin ) {
			$discouraged_slugs[] = $plugin->slug;
		}
		$list = [];
		//get slug of all the installed plugins
		$plugin_infos = get_plugins();
		if ( ! empty( $plugin_infos ) ) {
			foreach ( $plugin_infos as $file => $info ):
				$slug = explode( '/', $file )[0];
				if ( ! in_array( $slug, $discouraged_slugs ) ) {
					continue;
				}
				if ( is_plugin_inactive( $file ) ) {
					$list['inactive'][ $file ] = $info['Name'];
				} else {
					$list['active'][ $file ] = $info['Name'];
				}
			endforeach;
		}

		return $list;
	}

	/**
	 * is_plugin_active()
	 * Check if a plugin is active
	 *
	 * @param string $plugin plugin slug
	 *
	 * @return bool
	 */
	public function is_plugin_active( string $plugin ): bool {
		if ( empty ( $plugin ) ) {
			return false;
		}
		$plugin_list = get_plugins();

		if ( ! array_key_exists( $plugin, $plugin_list ) ) {
			return false;
		}
		$active_plugin_list = $this->active_plugins;

		return in_array( $plugin, $active_plugin_list );
	}

	/**
	 * Function is_imagify_setup()
	 * Check if imagify is installed and active.
	 * @return array
	 */
	public function is_imagify_setup(): array {

        /**
         * @todo: throw error if any of following plugins is NOT activated
         * wp-smushit
         * ewww-image-optimizer
         * optimole-wp
         * shortpixel-image-optimiser
         *
         * if activated then in done section chnage  the text
         *
         */


         /* @todo: If imagify is active, additional checks
         * 1. Plugin has api key
         * 2. API key is valid
         */
		$optimisation_plugins = array(
		    'wp-smushit/wp-smush.php',
            'ewww-image-optimizer/ewww-image-optimizer.php',
            'optimole-wp/optimole-wp.php',
            'shortpixel-image-optimiser/wp-shortpixel.php'

        );
        foreach ($optimisation_plugins as $optimisation_plugin) {
            if (in_array($optimisation_plugin, $this->active_plugins)) {
                return [
                    $this->status_key => $this->flag_resolved
                ];
            }

        }

        if (!in_array('imagify/imagify.php', $this->active_plugins)) {
            return [
                $this->status_key => $this->flag_open
            ];
        } else if (
            in_array('imagify/imagify.php', $this->active_plugins)
            && class_exists('Imagify_Requirements')
            && method_exists('Imagify_Requirements', 'is_api_key_valid')
            && Imagify_Requirements::is_api_key_valid()) {
            return [
                $this->status_key => $this->flag_resolved
            ];
        }else {
            return [
                $this->status_key => $this->flag_open
            ];
        }


	}

	/**
	 * Deactivate discouraged plugins
	 * @return array
	 * @todo use deactivate_plugins()
	 */
	public function fix_dis_plugin(): array {
		$plugins = onecom_fetch_plugins( false, true );
		if ( ! is_wp_error( $plugins ) && ! empty( $plugins ) ) {
			$dis_plugins = $this->discouraged_plugins( $plugins )['active'];
		}
		$plugins_to_deactivate = [];
		$html                  = '<ul>';

		foreach ( $dis_plugins as $key => $plugin ) {
			$plugins_to_deactivate[] = $key;
			$html                    .= '<li>' . $plugin . '</li>';
		}
		$html           .= '</ul>';
		$active_plugins = get_option( 'active_plugins' );
		if ( empty( $active_plugins ) ) {
			$active_plugins = [];
		}
		$refined_plugins = [];
		foreach ( $active_plugins as $active_plugin ) {
			if ( in_array( $active_plugin, $plugins_to_deactivate ) ) {
				continue;
			}
			$refined_plugins[] = $active_plugin;
		}
		if ( update_option( 'active_plugins', $refined_plugins ) ) {

			return $this->format_result(
				$this->flag_resolved,
				$this->text['dis_plugin'][ $this->fix_confirmation ] . $html,
				$this->text['dis_plugin'][ $this->status_desc ][ $this->status_resolved ] . $html
			);
		}

		return $this->format_result( $this->flag_open );
	}


	public function undo_check_performance_cdn() {
		if ( update_option( 'oc_cdn_enabled', "false" ) ) {
			$check = 'enable_cdn';

            $ignoreText = $this->ignore_text;
            if(!$this->onecom_is_premium()){
                $ignoreText = '';
            }

			return [
				$this->status_key      => $this->flag_resolved,
				$this->fix_button_text => $this->text[ $check ][ $this->fix_button_text ],
				$this->desc_key        => $this->text[ $check ][ $this->status_desc ][ $this->status_open ],
				$this->how_to_fix      => $this->text[ $check ][ $this->how_to_fix ],
				'ignore_text'          => $ignoreText
			];

		} else {
			return $this->format_result( $this->status_open );
		}
	}
}