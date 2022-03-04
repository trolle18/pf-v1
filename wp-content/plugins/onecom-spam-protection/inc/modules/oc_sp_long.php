<?php
class OcSpLong
{
    public function execute(
         &$sp_options = array(), &$oc_post = array()
    ) {

        if ( array_key_exists( 'email', $oc_post ) ) {
            $user_email = $oc_post['email'];
            //@todo merge these if statements
            if ( ! empty( $user_email ) ) {
                if ( strlen( $user_email ) > 64 ) {
                    return "".__('Email exceeds the allowed character limit',OC_SP_TEXTDOMAIN)." : $user_email";
                }
            }
        }
        if ( array_key_exists( 'author', $oc_post ) ) {
            if ( ! empty( $oc_post['author'] ) ) {
                $username = $oc_post['author'];
                if ( strlen( $oc_post['author'] ) > 64 ) {
                    return "".__('Username exceeds the allowed character limit',OC_SP_TEXTDOMAIN)." : $username";
                }
            }
        }


        return false;


    }

}

?>