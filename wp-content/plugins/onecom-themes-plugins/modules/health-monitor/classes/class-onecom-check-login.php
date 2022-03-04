<?php
declare(strict_types=1);

class OnecomCheckLogin extends OnecomHealthMonitor
{

    public $hm_data = [];
    public $login_masking_key = 'onecom_login_masking';

    public function __construct()
    {

        parent::__construct();
        $this->hm_data = get_option($this->option_key);
    }

    public function init()
    {
        add_action('wp_login_failed', [$this, 'log_failed_login']);
        if (
            $this->hm_data
            && isset(
                $this->hm_data['login_recaptcha'],
                $this->hm_data['recaptcha_keys'],
                $this->hm_data['recaptcha_keys']['oc_hm_site_key'],
                $this->hm_data['recaptcha_keys']['oc_hm_site_secret']
            )
            && $this->hm_data['login_recaptcha']
            && $this->hm_data['recaptcha_keys']
            && $this->hm_data['recaptcha_keys']['oc_hm_site_key']
            && $this->hm_data['recaptcha_keys']['oc_hm_site_secret']
        ) {
            add_action('login_form', [$this, 'login_form']);
            add_filter('wp_authenticate_user', [$this, 'verify_login_form'], 10, 3);
        }

        $expiry = get_option('oc_modified_cookie_expiry', false);
        if (!$expiry) {
            return;
        }
        add_filter('auth_cookie_expiration', [$this, 'modify_logout_duration'], 10, 3);
    }

    public function check_logout_time()
    {
        $logout_time = get_option('oc_modified_cookie_expiry', false);
        if (!$logout_time) {
            return $this->format_result($this->flag_open, __('You are using the default login expiration.', $this->text_domain), __('Current settings logout users after 48 hours. Consider shortening this duration.', $this->text_domain));
        }

        return $this->format_result($this->flag_resolved, __('You are using optimal logout duration.', $this->text_domain));
    }

    public function fix_check_logout_time()
    {
        if (update_option('oc_modified_cookie_expiry', true)) {
            return $this->format_result(
                $this->flag_resolved,
                $this->text['logout_duration'][$this->fix_confirmation],
                $this->text['logout_duration'][$this->status_desc][$this->status_resolved]
            );
        } else {
            return $this->format_result($this->flag_open, __('Failed to fix logout duration', $this->text_domain));
        }
    }

    public function undo_check_logout_time()
    {
        if (update_option('oc_modified_cookie_expiry', false)) {
            $check = 'logout_duration';

            return [
                $this->status_key => $this->flag_resolved,
                $this->fix_button_text => $this->text[$check][$this->fix_button_text],
                $this->desc_key => $this->text[$check][$this->status_desc][$this->status_open],
                $this->how_to_fix => $this->text[$check][$this->how_to_fix],
                'ignore_text' => $this->ignore_text
            ];
        } else {
            return $this->format_result($this->flag_open, __('Failed to rollback', $this->text_domain));
        }
    }

    function modify_logout_duration($expiry, $user_id, $remember)
    {
        $expiry;
        $user_id;
        $remember;

        return 28800;
    }

    /**
     * store usernames and emails for which login attempts failed.
     *
     * @param $username
     */
    function log_failed_login($username)
    {
        if (!(username_exists($username) || email_exists($username))) {
            return;
        }
        $failed_hm_login = get_option($this->option_key, []);
        if (!isset($failed_hm_login['failed_logins'])) {
            $failed_hm_login['failed_logins'] = [];
        }

        $failed_hm_login['failed_logins'][$username] = date('Y-m-d H:i');
        update_option($this->option_key, $failed_hm_login);
    }

    /**
     * check if there was any failed login attempt for any username that matches
     * any of the existing users
     */
    function check_failed_login(): array
    {
        $logins = get_option($this->option_key);
        if (!isset($logins['failed_logins'])) {
            return $this->format_result($this->flag_resolved, __('There were no failed login attempts', $this->text_domain));
        }
        $result = $this->format_result($this->flag_open, __('There were some failed login attempts.', $this->text_domain), __('There were some of the failed login attempts for existing users. Consider changing the username for following users.', $this->text_domain));
        if ($logins['failed_logins']) {
            $result['file-list'] = $logins['failed_logins'];
        }

        return $result;
    }

    /**
     * Reset the log of failed login attempts.
     * @return array
     */
    public function reset_failed_login_data(): array
    {
        $hm_data_obj = get_option($this->option_key);
        unset($hm_data_obj['failed_logins']);
        if (update_option($this->option_key, $hm_data_obj)) {
            return $this->format_result($this->flag_resolved, __('Failed login data reset', $this->text_domain));
        }

        return $this->format_result($this->flag_open, __('Unable to reset login attempts', $this->text_domain));
    }

    public function login_recaptcha(): array
    {
        $hm_data_obj = get_option($this->option_key);
        if (isset($hm_data_obj['login_recaptcha']) && $hm_data_obj['login_recaptcha']) {
            return $this->format_result($this->flag_resolved);
        }

        return $this->format_result($this->flag_open);
    }

    public function fix_login_recaptcha($data)
    {
        $hm_data_obj = get_option($this->option_key);
        $hm_data_obj['recaptcha_keys'] = $data['inputs'];
        $hm_data_obj['login_recaptcha'] = true;
        update_option($this->option_key, $hm_data_obj);

        return $this->format_result(
            $this->flag_resolved,
            $this->text['login_recaptcha'][$this->fix_confirmation],
            $this->text['login_recaptcha'][$this->status_desc][$this->status_resolved]
        );
    }

    public function undo_login_recaptcha()
    {
        $hm_data_obj = get_option($this->option_key);
        $hm_data_obj['login_recaptcha'] = false;

        if (update_option($this->option_key, $hm_data_obj)) {
            $check = 'login_recaptcha';

            return [
                $this->status_key => $this->flag_resolved,
                $this->fix_button_text => $this->text[$check][$this->fix_button_text],
                $this->desc_key => $this->text[$check][$this->status_desc][$this->status_open],
                $this->how_to_fix => $this->text[$check][$this->how_to_fix],
                'ignore_text' => $this->ignore_text
            ];

        } else {
            return $this->format_result($this->status_open);
        }

    }

    public function login_form()
    {
        wp_enqueue_script('oc-google-recaptcha', 'https://www.google.com/recaptcha/api.js');
//        wp_enqueue_style('oc-google-recaptcha');
        ?>
        <style>
            .g-recaptcha {
                transform: scale(.9);
                -webkit-transform: scale(.9);
                transform-origin: 0 0;
                -webkit-transform-origin: 0 0;
            }
        </style>
        <p>
            <label for="recaptcha"><br/>
        <div class="g-recaptcha" data-sitekey="<?php echo $this->hm_data['recaptcha_keys']['oc_hm_site_key'] ?>"></div>
        </label>
        </p>
        <?php
    }

    function verify_login_form($user, $password)
    {
        $password;
        $secretkey = $this->hm_data['recaptcha_keys']['oc_hm_site_secret'];
        if (isset($_POST['g-recaptcha-response'])) {
            $response = wp_remote_get('https://www.google.com/recaptcha/api/siteverify?secret=' . $secretkey . '&response=' . $_POST['g-recaptcha-response']);
            $response = json_decode($response['body'], true);
            if ($response['success']) {
                return $user;
            } else {
                return new WP_Error('Captcha Invalid', '<strong>' . __('Invalid captcha value', $this->text_domain) . '</strong>');
            }
        } else {
            return new WP_Error('Captcha Invalid', '<strong>' . __('Invalid captcha value', $this->text_domain) . '</strong>');
        }
    }

    /**
     * Function check_login_protection
     * Check if a user has login protection enabled
     * @param null
     * @return array
     */

    public function check_login_protection(): array
    {
        $login_masking = (int)get_site_option($this->login_masking_key, 0);
        if (in_array($login_masking, [1, 2])) {
            return $this->format_result($this->flag_resolved);
        }
        return $this->format_result($this->flag_open);
    }
}