<?php
class OcSpShort
{
    public function execute(
        &$sp_options = array(), &$oc_post = array()
    )
    {
        if ( array_key_exists( 'email', $oc_post ) ) {
            $user_email = $oc_post['email'];
            if ( ! empty( $user_email ) ) {
                if ( strlen( $user_email ) < 5 ) {
                    return "Email Too Short: $user_email";
                }
            }
        }
        if ( array_key_exists( 'author', $oc_post ) ) {
            if ( ! empty( $oc_post['author'] ) ) {
                $user_name = $oc_post['author'];


                if ( strlen( $oc_post['author'] ) < 3 ) {
                    return "Author Too Short: $user_name";
                }
            }
        }
        return false;


    }

}