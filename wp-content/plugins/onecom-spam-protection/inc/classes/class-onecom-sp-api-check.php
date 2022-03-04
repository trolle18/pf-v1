<?php


class OnecomSpApiCheck extends OnecomSp
{

    public $is_spam = '';

    public function __construct()
    {

        parent::__construct();
        add_action('init', array($this, 'oc_form_submission_check'));
        add_action('pre_comment_approved', array($this, 'oc_comment_filter'), 1, 2);
        add_filter('pre_user_login', array($this, 'oc_check_user_registration'), 1, 1);


    }

    public function oc_check_user_registration($user_login)
    {



        if ($this->is_spam!='' && $this->is_spam !== false) {

            unset($this->is_spam);
            wp_die( "<p style='text-align: center'>".__('Spam Detected!',OC_SP_TEXTDOMAIN)."</p>", __("Access Denied",OC_SP_TEXTDOMAIN),
                array( 'response' => 403 ) );

        } else {
            return $user_login;
        }


    }

    public function oc_form_submission_check()
    {

        if (function_exists('is_user_logged_in') && is_user_logged_in()) {
            remove_filter('pre_user_login', 'oc_check_user_registration', 1);
            if (current_user_can('edit_posts')) {
                return false;
            }
        }

        $oc_post = oc_sp_post_values();

        if (!empty($oc_post['email']) || !empty($oc_post['author'])
            || !empty($oc_post['comment'])
        ) {


            $ip = $this->get_user_ip();

            $user_email = $oc_post['email'];
            $user_agent = $this->oc_get_user_agent();

			//username check disabled for all
            $user_name = '';

            $current_uri = $_SERVER['REQUEST_URI'];


            $oc_check = $this->sp_api_check($ip, $user_email, $user_agent, $user_name);

            if ($oc_check["is_spam"] !== false) {

                oc_log_spam($ip, $oc_post, $oc_check["reason"]);
                $this->is_spam = true;
                if ( strpos( $current_uri, 'wp-login.php' ) === false && strpos( $current_uri, 'wp-comments-post.php' ) === false) {

                wp_die( "<p style='text-align: center'>".__('Spam Detected!',OC_SP_TEXTDOMAIN)."</p>", __('Access Denied',OC_SP_TEXTDOMAIN),
                    array( 'response' => 403 ) );
                }


            } elseif ($oc_check["is_spam"] === false) {


                $this->is_spam = $this->oc_website_checks();
                if ($this->is_spam !== false &&  strpos( $current_uri, 'wp-login.php' ) === false && strpos( $current_uri, 'wp-comments-post.php' ) === false) {

                    wp_die("<p style='text-align: center'>".__('Spam Detected!',OC_SP_TEXTDOMAIN)."</p>", __('Access Denied',OC_SP_TEXTDOMAIN),
                        array( 'response' => 403 ) );
                }

            } else {

                return false;
            }

        }


    }

    public function oc_comment_filter($approved, $comment)
    {


        if ($this->is_spam != '' && $this->is_spam !== false) {

            unset($this->is_spam);


            return 'spam';

        }

        return $approved ;


    }

    public function oc_website_checks()
    {
        $user_ip=$this->get_user_ip();
        $logs=json_decode(get_option('onecom_sp_spam_logs'),true);
        $sp_options=json_decode(get_option('onecom_sp_protect_options'),true);
        $oc_post = oc_sp_post_values();
        $init_check= new OnecomSpWebsiteCheck();


        $response=$init_check->execute($user_ip,$logs,$sp_options,$oc_post);

        return $response;


    }

}