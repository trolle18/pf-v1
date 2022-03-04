<?php

/**
 * Class OnecomCheckSsl
 */
class OnecomCheckSsl extends OnecomHealthMonitor {
	function oc_sh_check_ssl() {
		$this->log_entry( 'Checking if SSL enabled' );
		$url     = str_replace( 'http://', 'https://', get_site_url() );
		$headers = $this->get_curl_header( $url );
		if ( empty( $headers ) ) {
			$status     = $this->flag_open;
			$guide_link = sprintf( "<a href='https://www.one.com/%s/chat' target='_blank'>", onecom_generic_locale_link( '', get_locale(), 1 ) );
			$title      = __( 'SSL certificate', $this->text_domain );
			$desc       = sprintf( __( 'Your site isn’t using HTTPS. Visitors on your site might get a warning that your website isn’t secure and it can also have a negative effect on your SEO rating. %sContact support%s', $this->text_domain ), $guide_link, "</a>" );
		} else {
			$status = $this->flag_resolved;
			$desc   = '';
			$title  = __( 'Your site has valid SSL certificate!', $this->text_domain );
		}
		$this->log_entry( 'Finished checking for SSL' );

		//@todo oc_sh_save_result( 'ssl_certificate', $result[ $this->status_key ] );

		return $this->format_result( $status, $title, $desc );
	}
}