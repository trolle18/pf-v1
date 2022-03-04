<?php

/**
 * adds one.com walkthrough tour to wordpress admin
 */


if ( ! class_exists( 'Onecom_Walkthrough' ) ) {

    class Onecom_Walkthrough {


        const BUTTON_2 = 'button2';
        const CONTENT = 'content';
        const PARENT = 'parent';
        const VCACHE_PLUGIN = 'onecom-vcache/vcaching.php';
        const WEBSHOP_PLUGIN = 'onecom-webshop/webshop.php';
        const WEBSHOP_SETTINGS = 'one-webshop-settings';
        const PHP_SCANNER = 'onecom-php-scanner/onecom-compatibility-scanner.php';
        const OC_FUNCTION = 'function';
        const DISMISSED_POINTERS = 'dismissed_wp_pointers';


        // Initiate construct
        function __construct() {
            add_action( 'admin_enqueue_scripts', array(
                $this,
                'oc_enqueue_scripts'
            ) );  // Hook to admin_enqueue_scripts
            add_action( 'wp_ajax_ocwkt_reset_tour', array( $this, 'oc_walkthrough_reset_tour' ) );
            add_action( 'admin_head-index.php', array( $this, 'onecom_restart_tour' ) );

        }


        function oc_enqueue_scripts() {

            // Check to see if user has already dismissed the pointer tour
            $dismissed = explode( ',', get_user_meta( wp_get_current_user()->ID, self::DISMISSED_POINTERS, true ) );
            $do_tour   = ! in_array( 'oc_walthrough_pointer', $dismissed );

            // If not, we are good to continue
            if ( $do_tour ) {

                // Enqueue WP pointer scripts and styles
                wp_enqueue_style( 'wp-pointer' );
                wp_enqueue_script( 'wp-pointer' );

                // Finish hooking to WP admin areas
                add_action( 'admin_print_footer_scripts', array(
                    $this,
                    'walkthrough_footer_scripts'
                ) );  // Hook to admin footer scripts
            }
            add_action( 'admin_head', array( $this, 'css_admin_head' ) );  // Hook to admin head
        }

        // Used to add css of walkthrough
        function css_admin_head() { ?>
            <style>#pointer-primary, #oc-pointer {
                    margin: 0 0 0 5px;
                }

                .ocwt_pointer .button-primary, .ocwt_pointer .button-secondary-final {
                    border-radius: 100px;
                    width: 89px;
                    text-align: center;
                    height: 30px;
                    float: right;
                    font-weight: 500;
                    font-size: 12px;
                    background: #0078C8;
                    line-height: 27px;

                }

                .ocwt_pointer .button-secondary-final {
                    color: #FFFFFF;
                    line-height: 30px;
                }

                .ocwt_pointer .button-secondary {
                    background-color: transparent !important;
                    border: none;
                    font-weight: 500;
                    font-size: 12px;
                    line-height: 30px;
                    color: #0078C8;
                    margin-right: 10px;
                }

                .ocwt_pointer .wp-pointer-content h4 {
                    margin: 0;
                    font-weight: 600;
                    font-size: 18px;
                    line-height: 25px;
                    letter-spacing: 0.2px;
                }

                .ocwt_pointer .wp-pointer-content p {
                    padding: 10px 0;
                    margin: 0;
                    font-size: 14px;
                    line-height: 19px;
                    letter-spacing: 0.2px;
                }

                .ocwt_pointer .wp-pointer-buttons {
                    padding: 0px;
                    margin-top: 15px;
                }

                .ocwt_pointer {
                    z-index: 99999 !important;
                }

                .oc-reset-wlk-tour span.oc_reset {
                    text-decoration: none !important;
                    color: #d5d5d5;
                    float: left;
                    margin-right: 10px;
                }

                .ocwt_pointer .wp-pointer-content {
                    padding: 24px 30px 30px 30px;
                }

                .oc-walk-img {
                    padding-left: 4px;
                }

                .oc-pointer-wrap {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgb(60, 60, 60, 0.5);
                    z-index: 99999;
                }

                .img-holder {
                    min-height: 72px;
                }

                #oc_hmwidget_spinner {
                    background: url(data:image/gif;base64,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) no-repeat;
                    background-size: 20px 20px;
                    display: inline-block;
                    visibility: hidden;
                    float: none;
                    vertical-align: middle;
                    opacity: .7;
                    width: 20px;
                    height: 20px;
                    margin: 0 10px 0;
                }

                @media screen and (max-width: 782px) {
                    .ocwt_pointer, .oc-pointer-wrap,.oc-reset-wlk-tour a {
                        display: none;
                    }
                }
            </style>
        <?php }

        // Define footer scripts
        function walkthrough_footer_scripts() {


            $tour = $this->oc_generate_array();


            // Determine the current page in query parameter
            $page = isset( $_GET['page'] ) ? $_GET['page'] . '-tour' : '';
            $tab  = isset( $_GET['tab'] ) ? $_GET['tab'] : '';

//          // Define other variables
            $function     = '';
            $button2      = '';
            $id           = '';
            $options      = array();
            $show_pointer = false;


            if ( $page != '' && in_array( $page, array_keys( $tour ) ) && $tab == '' ) {
                $show_pointer = true;
                $parent       = true;
                $screen       = $page;
                if ( isset ( $tour[ $page ]['id'] ) ) {
                    $id = $tour[ $page ]['id'];
                }

                $options = array(
                    self::CONTENT  => $tour[ $page ][ self::CONTENT ],
                    'pointerWidth' => '342',
                    'position'     => array( 'edge' => 'left', 'align' => 'left' )
                );

                $button2  = false;
                $function = '';

                if ( isset ( $tour[ $page ][ self::BUTTON_2 ] ) ) {
                    $button2 = $tour[ $page ][ self::BUTTON_2 ];
                }
                if ( isset ( $tour[ $page ][ self::OC_FUNCTION ] ) ) {
                    $function = $tour[ $page ][ self::OC_FUNCTION ];
                }

                $parent = $tour[ $page ][ self::PARENT ];

            }

            if ( $show_pointer && $id == '#onecom_plugins' ) {
                $this->make_pointer_script( $id, $options, __( 'Got it!', OC_PLUGIN_DOMAIN ), $button2, $function, $parent, $screen );
            } elseif ( $show_pointer ) {
                $this->make_pointer_script( $id, $options, __( 'Close', OC_PLUGIN_DOMAIN ), $button2, $function, $parent, $screen );

            }
        }


        /**
         * returns array which will be used for generating pointers(apart from default)
         */

        public function oc_generate_array( $arr = array() ) {

            $themes_page            = $this->onecom_theme_page_tour();
            $plugins_page           = $this->onecom_plugins_tour();
            $staging_page           = $this->onecom_staging_tour();
            $health_monitor_page    = $this->onecom_health_monitor_tour();
            $cookie_banner_page     = $this->onecom_utility_tour();
            $error_page             = $this->onecom_utility_tour();
            $webshop                = $this->onecom_utility_tour();
            $one_photo              = $this->onecom_utility_tour();
            $maintenance_mode       = $this->onecom_utility_tour();
            $php_scanner            = $this->onecom_utility_tour();
            $performance_cache_page = $this->onecom_performance_cache_tour();

            // adding -tour to the page slug is required for generating the walkthrough
            $tours = array(
                'onecom-wp-themes-tour'                 => $themes_page,
                'onecom-wp-plugins-tour'                => $plugins_page,
                'onecom-wp-staging-tour'                => $staging_page,
                'onecom-wp-health-monitor-tour'         => $health_monitor_page,
                'onecom-wp-spam-protection-tour'        => $health_monitor_page,
                'onecom-wp-cookie-banner-tour'          => $cookie_banner_page,
                'onecom-wp-error-page-tour'             => $error_page,
                'onecom-php-compatibility-scanner-tour' => $php_scanner,
                'onecom-wp-under-construction-tour'     => $maintenance_mode,
                'onecom-vcache-plugin-tour'             => $performance_cache_page,
                'one-webshop-settings-tour'             => $webshop,
                'oc_onephoto-tour'                      => $one_photo,

            );


            if ( isset( $arr ) && ! empty( $arr ) ) {

                return array_merge( $tours, $arr );
            }

            return $tours;


        }

        /**
         * generates parameters array for themes
         */
        public function onecom_theme_page_tour() {
            return array(
                'id'              => '#onecom_themes',
                self::CONTENT     => '<div class="img-holder"><img class="oc-walk-img" src=' . ONECOM_WP_URL . 'assets/images/theme_walk.svg /></div>'
                                     . '<h4>Themes</h4>'
                                     . '<p>' . __( 'Choose the perfect theme to suit your brand and give your site a professional appearance.', OC_PLUGIN_DOMAIN ) . '</p>',
                self::BUTTON_2    => __( 'Next', OC_PLUGIN_DOMAIN ),
                self::OC_FUNCTION => 'window.location="' . menu_page_url( 'onecom-wp-plugins', false ) . '"',
                self::PARENT      => true
            );

        }

        /**
         * generates parameters array for plugins
         */

        public function onecom_plugins_tour() {
            return array(
                'id'          => '#onecom_plugins',
                self::CONTENT => '<div class="img-holder"><img class="oc-walk-img" src=' . ONECOM_WP_URL . 'assets/images/plugin_walk.svg /></div>'
                                 . '<h4>Plugins</h4>'
                                 . '<p>' . __( 'Get a contact form, social media buttons, galleries and much more by adding plugins to your website.', OC_PLUGIN_DOMAIN ) . '</p>',
                self::PARENT  => true
            );

        }

        /**
         * generates parameters array for staging
         */

        public function onecom_staging_tour() {
            $staging = array(
                'id'          => '#onecom_staging',
                self::CONTENT => '<div class="img-holder"><img class="oc-walk-img" src=' . ONECOM_WP_URL . 'assets/images/staging_walk.svg /></div>'
                                 . '<h4>Staging</h4>'
                                 . '<p>' . __( 'Create a copy fo your page to try out new plugins and themes without disturbing or breaking your existing website.', OC_PLUGIN_DOMAIN ) . '</p>',
                self::PARENT  => true
            );

            if ( is_plugin_active( self::WEBSHOP_PLUGIN ) ) {

                $button2 = array(
                    self::BUTTON_2    => __( 'Next', OC_PLUGIN_DOMAIN ),
                    self::OC_FUNCTION => 'window.location="' . menu_page_url( 'one-webshop-settings', false ) . '"'
                );
                $staging = array_merge( $staging, $button2 );

            } else {
                $button2 = array(
                    self::BUTTON_2    => __( 'Next', OC_PLUGIN_DOMAIN ),
                    self::OC_FUNCTION => 'window.location="' . menu_page_url( 'onecom-wp-error-page', false ) . '"'
                );
                $staging = array_merge( $staging, $button2 );

            }

            return $staging;
        }

        /**
         * generates parameters array for health & security category
         */

        public function onecom_health_monitor_tour() {
            $health_mn = array(
                'id'              => '#onecom_health_security',
                self::CONTENT     => '<div class="img-holder"><img class="oc-walk-img" src=' . ONECOM_WP_URL . 'assets/images/hs_walk.svg /></div>'
                                     . '<h4>Health and Security</h4>'
                                     . '<p>' . __( 'Health Monitor and Spam Protection keeps an eye on any issues, spammers, vulnerabilities and lets you fix most of them with just 1-click.', OC_PLUGIN_DOMAIN ) . '</p>',

                self::PARENT => true
            );

            if ( is_plugin_active( self::VCACHE_PLUGIN ) ) {

                $button2 = array(
                    self::BUTTON_2    => __( 'Next', OC_PLUGIN_DOMAIN ),
                    self::OC_FUNCTION => 'window.location="' . menu_page_url( 'onecom-vcache-plugin', false ) . '"'
                    // We are relocating to "Settings" page with the 'site_title' query var
                );

                $health_mn = array_merge( $health_mn, $button2 );

            }else{
                $button2 = array(
                self::BUTTON_2    => __( 'Next', OC_PLUGIN_DOMAIN ),
                self::OC_FUNCTION => 'window.location="' . menu_page_url( 'onecom-wp-staging', false ) . '"');
                $health_mn = array_merge( $health_mn, $button2 );
            }

            return $health_mn;


        }


        /**
         * generates parameters array for performance tools
         */

        public function onecom_performance_cache_tour() {

            if ( ! is_plugin_active( self::VCACHE_PLUGIN ) ) {

                return false;
            }
            $performance_cache = array(
                'id'          => '#onecom-performance-tools',
                self::CONTENT => '<div class="img-holder"><img class="oc-walk-img" src=' . ONECOM_WP_URL . 'assets/images/vcache_walk.svg /></div>'
                                 . '<h4>Performance Tools</h4>'
                                 . '<p>' . __( 'Performance Cache, CDN and WP Rocket will enhance the speed of your website and improve its overall performance. ', OC_PLUGIN_DOMAIN ) . '</p>',
                self::PARENT  => true
            );


            $button2 = array(
                self::BUTTON_2    => __( 'Next', OC_PLUGIN_DOMAIN ),
                self::OC_FUNCTION => 'window.location="' . menu_page_url( 'onecom-wp-staging', false ) . '"'
                // We are relocating to "Settings" page with the 'site_title' query var
            );

            $performance_cache = array_merge( $performance_cache, $button2 );


            return $performance_cache;


        }

        /**
         * generates parameters array for utility
         */

        public function onecom_utility_tour() {


            $utility = array(
                'id'          => '#onecom-utility',
                self::CONTENT => '<div class="img-holder"><img class="oc-walk-img" src=' . ONECOM_WP_URL . 'assets/images/utility_walk.svg /></div>'
                                 . '<h4>Utility Tools</h4>'
                                 . '<p>' . __( 'Add features such as an Error Page, Cookie Banner or a Maintenance Mode to increase the usability of your site.', OC_PLUGIN_DOMAIN ) . '</p>',
                self::PARENT  => true
            );

            $button2 = array(
                self::BUTTON_2    => __( 'Next', OC_PLUGIN_DOMAIN ),
                self::OC_FUNCTION => 'window.location="' . menu_page_url( 'onecom-wp-themes', false ) . '"'
            );

            $utility = array_merge( $utility, $button2 );


            return $utility;


        }




        /**
         * generates Jquery script for the pointers
         */

        // Print footer scripts
        function make_pointer_script( $id, $options, $button1, $button2 = false, $function = '', $parent = false, $screen = '' ) { ?>
            <script type="text/javascript">

                (function ($) {

                    $(document).ready(function () {

                        // Define pointer options
                        let wp_pointers_tour_opts =<?php echo json_encode( $options ); ?>, setup,

                            pointerElement = document.getElementsByClassName('ocwt_pointer'),
                            id = '<?php echo $id; ?>',
                            tabScreenSize = false,
                            button;
                        const screen = '<?php echo $screen;?>';

                        // For tablet screen sizes
                        if (window.screen.width < 960) {
                            id = '#toplevel_page_onecom-wp';
                            tabScreenSize = true;
                        }


                        wp_pointers_tour_opts = $.extend(wp_pointers_tour_opts, {

                            pointerClass: 'ocwt_pointer',

                            // Add 'Close' button & Got it for the final screen i.e plugins
                            buttons: function (event, t) {

                                if (screen == "onecom-wp-plugins-tour") {

                                    button = jQuery('<a id="ocwk-pointer-close" href="javascript:;"  class="button-secondary-final">' + '<?php echo $button1; ?>' + '</a>');

                                } else {
                                    button = jQuery('<a id="ocwk-pointer-close" href="javascript:;"  class="button-secondary">' + '<?php echo $button1; ?>' + '</a>');

                                }

                                button.bind('click.pointer', function () {
                                    t.element.pointer('close');

                                });
                                return button;
                            },

                            close: function () {

                                // Post to admin ajax to disable pointers when user clicks "Close"
                                $.post(ajaxurl, {
                                    pointer: 'oc_walthrough_pointer',
                                    action: 'dismiss-wp-pointer'
                                });

                                $(document).find('.oc-pointer-wrap').remove();

                                var args = {
                                    'event_action': 'close',
                                    'item_category': 'blog',
                                    'item_name': 'onecom_tour',
                                    'referrer': screen,
                                }

                                oc_push_stats_by_js(args);
                            }


                        });

                        // This is used for our "button2" value above (advances the pointers)
                        setup = function () {

                            <?php  if($parent) { ?>
                            $('<?php echo $id; ?>').parent().pointer(wp_pointers_tour_opts).pointer('open');

                            <?php      }else{ ?>
                            $('<?php echo $id; ?>').pointer(wp_pointers_tour_opts).pointer('open');

                            <?php }
                            if ($button2) { ?>

                            let onePhoto = $(document).find('.ocop_pointer');

                            // To avoid conflict with onephoto
                            if (onePhoto.length > 0) {
                                // setTimeout(function(){
                                $(".ocop_pointer #pointer-primary").removeAttr('id');
                                // }, 3000);

                            }
                            jQuery('#ocwk-pointer-close').before('<a id="pointer-primary" class="button-primary">' + '<?php echo $button2; ?>' + '</a>');
                            jQuery('#pointer-primary').click(function () {
                                <?php echo $function; ?>  // Execute button2 function
                            });



                            <?php } ?>

                            if (tabScreenSize) {
                                $(pointerElement).css('left', function () {

                                    return $('#adminmenuback').width() + 'px';

                                })
                            }
                        };


                        if (wp_pointers_tour_opts.position && wp_pointers_tour_opts.position.defer_loading) {

                            $(window).bind('load.wp-pointers', setup);
                        } else {
                            setup();
                        }


                        let adminBar = document.getElementById('wpadminbar'),
                            posPointer = pointerElement[0].getBoundingClientRect(),
                            posAdminBar = adminBar.getBoundingClientRect();

// This is to adjust the top margin of pointer in case of overlap with admin bar
                        if (
                            posAdminBar.bottom > posPointer.top ||
                            posAdminBar.top < posPointer.bottom &&
                            id == '#onecom-performance-tools'
                        ) {
                            $(pointerElement).css('top', function () {
                                return ($('#wpadminbar').height() * 2) + 'px';
                            })

                            let targetElem = $(document).find(id)[0];

                            $(pointerElement).find('.wp-pointer-arrow').css('top', ($(targetElem).offset().top - $(pointerElement).offset().top + 15 + 'px'));

                        }

                        $(pointerElement).wrap('<div class="oc-pointer-wrap"></div>');

                        // This is to adjust the popup and arrow position for the top submenu //
                        if (id == '#onecom_health_security') {
                            $(pointerElement).css('top', function () {
                                return ($('#wpadminbar').height() * 2) + 'px';
                            })
                            $(pointerElement).find('wp-pointer-arrow').css('top', $(pointerElement).offset().top);
                        }
                    });
                })(jQuery);
            </script>
            <?php
        }


        function oc_walkthrough_reset_tour() {
            $pointers    = get_user_meta( get_current_user_id(), self::DISMISSED_POINTERS, true );
            $pointersArr = explode( ',', $pointers );
            $pointer_key = array_search( 'oc_walthrough_pointer', $pointersArr );
            if ( $pointer_key !== false ) {
                unset( $pointersArr[ $pointer_key ] );
            }

            $newpointers = join( ",", $pointersArr );
            if ( $newpointers === $pointers ) {
                die( json_encode( array( "status" => false ) ) );
            } elseif ( update_user_meta( get_current_user_id(), self::DISMISSED_POINTERS, $newpointers ) ) {
                wp_send_json_success( admin_url( 'admin.php?page=onecom-wp-health-monitor' ) );
            }
        }

        function onecom_restart_tour() {
            ?>
            <script type="text/javascript">
                (function ($) {
                    jQuery(document).ready(function () {


                        jQuery(".oc-reset-wlk-tour a").on('click', function (e) {
                            e.preventDefault();
                            $(document).find('#oc_hmwidget_spinner').css('visibility', 'visible');
                            jQuery.post(ajaxurl,
                                {
                                    'action': 'ocwkt_reset_tour',
                                    'nonce': 'asdsadsad'
                                },
                                function (response) {
                                    $(document).find('#oc_hmwidget_spinner').css('visibility', 'hidden');
                                    if ("object" === typeof response && response.success && response.data != undefined) {

                                        window.location.href = response.data;


                                    } else {
                                        console.log('Could not restart tour. Retrying..');
                                    }
                                },
                                'json',
                                false,
                                0
                            );
                        });
                    })
                })(jQuery);
            </script>

            <?php
        }

    }
}