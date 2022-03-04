<?php
/**
 * Class OnecomDebugMode
 * De
 */
declare( strict_types=1 );

class OnecomDebugMode extends OnecomHealthMonitor {
	public function check_error_reporting() {
		$this->log_entry( 'Scanning debug mode' );
		$display_errors = isset( $_POST['err'] ) ? intval( $_POST['err'] ) : 0;
		if ( ( $display_errors && ( $display_errors === 1 ) ) || WP_DEBUG ) {

			$guide_link = sprintf( "<a href='https://help.one.com/hc/%s/articles/115005593705-How-do-I-enable-error-messages-for-PHP-' target='_blank'>", onecom_generic_locale_link( '', get_locale(), 1 ) );

			$guide_link2 = sprintf( "<a href='https://help.one.com/hc/%s/articles/115005594045-How-do-I-enable-debugging-in-WordPress-' target='_blank'>", onecom_generic_locale_link( '', get_locale(), 1 ) );

			$result = $this->format_result($this->flag_open);
		} else {
		    $result  = $this->format_result($this->flag_resolved);
		}
		return $result;
	}

}