<?php

/**
 * Class OnecomHealthMonitorAjaxTest
 * Important methods in WP_Ajax_UnitTestCase
 * 1. $this->_handleAjax( 'my_ajax_action' );
 *
 * -- set data for ajax request
 * $_POST['_nonce'] = wp_create_nonce( 'my_nonce' );
 * $_POST['other_data'] = 'something';
 *
 * 2. $this->setExpectedException( 'WPAjaxDieStopException' ); or
 * WPAjaxDieContinueException
 *
 * 3. Set user role
 * $this->_setRole( 'subscriber' );
 *
 * 4. Run ajax as logout user
 * $this->logout();
 */
class OnecomHealthMonitorAjaxTest extends WP_Ajax_UnitTestCase {
	public $obj;

	public function setUp() {
		parent::setUp();
		add_filter( 'http_request_args', [ $this, 'bal_http_request_args' ], 100, 1 );
		add_action( 'http_api_curl', [ $this, 'bal_http_api_curl' ], 100, 1 );


		$this->obj = new OnecomHealthMonitorAjax();
		$this->obj->init();
	}

	function bal_http_request_args( $r ) //called on line 237
	{
		$r['timeout'] = 15000;

		return $r;
	}


	function bal_http_api_curl( $handle ) //called on line 1315
	{
		curl_setopt( $handle, CURLOPT_CONNECTTIMEOUT, 15000 );
		curl_setopt( $handle, CURLOPT_TIMEOUT, 15000 );
	}

	public function tearDown() {
		//code here

		parent::tearDown();
	}

	/**
	 * @medium
	 */
	public function test_my_request() {
		try {
			$this->_handleAjax( 'ocsh_check_php_updates' );
		} catch ( WPAjaxDieContinueException $e ) {
			// We expected this, do nothing.
		}
		$response = json_decode( $this->_last_response, true );
		$this->assertIsArray( $response );
		$this->assertNotEmpty( $response );
	}

	/**
	 * @medium
	 */
	public function test_ocsh_check_plugin_updates() {
		try {
			$this->_handleAjax( 'ocsh_check_plugin_updates' );
		} catch ( WPAjaxDieContinueException $e ) {
			// We expected this, do nothing.
		} catch ( WPAjaxDieStopException $e ) {
			// We expected this, do nothing.
		}
		$response = json_decode( $this->_last_response, true );
		$this->assertIsArray( $response );
		$this->assertNotEmpty( $response );
	}

	/**
	 * @medium
	 */
	public function test_ocsh_check_theme_updates() {
		try {
			$this->_handleAjax( 'ocsh_check_theme_updates' );
		} catch ( WPAjaxDieContinueException $e ) {
			// We expected this, do nothing.
		} catch ( WPAjaxDieStopException $e ) {
			// We expected this, do nothing.
		}
		$response = json_decode( $this->_last_response, true );
		$this->assertIsArray( $response );
		$this->assertNotEmpty( $response );
	}

	/**
	 * @medium
	 */
	public function test_ocsh_check_wp_updates() {
		try {
			$this->_handleAjax( 'ocsh_check_wp_updates' );
		} catch ( WPAjaxDieContinueException $e ) {
			// We expected this, do nothing.
		} catch ( WPAjaxDieStopException $e ) {
			// We expected this, do nothing.
		}
		$response = json_decode( $this->_last_response, true );
		$this->assertIsArray( $response );
		$this->assertNotEmpty( $response );
	}

	/**
	 * @medium
	 */
	public function test_ocsh_wp_connection() {
		try {
			$this->_handleAjax( 'ocsh_wp_connection' );
		} catch ( WPAjaxDieContinueException $e ) {
			// We expected this, do nothing.
		} catch ( WPAjaxDieStopException $e ) {
			// We expected this, do nothing.
		}
		$response = json_decode( $this->_last_response, true );
		$this->assertIsArray( $response );
		$this->assertNotEmpty( $response );
	}

	/**
	 * @medium
	 */
	public function test_ocsh_check_core_updates() {
		try {
			$this->_handleAjax( 'ocsh_check_core_updates' );
		} catch ( WPAjaxDieContinueException $e ) {
			// We expected this, do nothing.
		} catch ( WPAjaxDieStopException $e ) {
			// We expected this, do nothing.
		}
		$response = json_decode( $this->_last_response, true );
		$this->assertIsArray( $response );
		$this->assertNotEmpty( $response );
	}

	/**
	 * @medium
	 */
	public function test_ocsh_check_ssl() {
		try {
			$this->_handleAjax( 'ocsh_check_ssl' );
		} catch ( WPAjaxDieContinueException $e ) {
			// We expected this, do nothing.
		} catch ( WPAjaxDieStopException $e ) {
			// We expected this, do nothing.
		}
		$response = json_decode( $this->_last_response, true );
		$this->assertIsArray( $response );
		$this->assertNotEmpty( $response );
	}

	/**
	 * @medium
	 */
	public function test_ocsh_check_file_execution() {
		try {
			$this->_handleAjax( 'ocsh_check_file_execution' );
		} catch ( WPAjaxDieContinueException $e ) {
			// We expected this, do nothing.
		} catch ( WPAjaxDieStopException $e ) {
			// We expected this, do nothing.
		}
		$response = json_decode( $this->_last_response, true );
		$this->assertIsArray( $response );
		$this->assertNotEmpty( $response );
	}

	/**
	 * @medium
	 */
	public function test_ocsh_check_file_permissions() {
		try {
			$this->_handleAjax( 'ocsh_check_file_permissions' );
		} catch ( WPAjaxDieContinueException $e ) {
			// We expected this, do nothing.
		} catch ( WPAjaxDieStopException $e ) {
			// We expected this, do nothing.
		}
		$response = json_decode( $this->_last_response, true );
		$this->assertIsArray( $response );
		$this->assertNotEmpty( $response );
	}

	/**
	 * @medium
	 */
	public function test_ocsh_DB() {
		try {
			$this->_handleAjax( 'ocsh_DB' );
		} catch ( WPAjaxDieContinueException $e ) {
			// We expected this, do nothing.
		} catch ( WPAjaxDieStopException $e ) {
			// We expected this, do nothing.
		}
		$response = json_decode( $this->_last_response, true );
		$this->assertIsArray( $response );
		$this->assertNotEmpty( $response );
	}

	/**
	 * @medium
	 */
	public function test_ocsh_check_file_edit() {
		try {
			$this->_handleAjax( 'ocsh_check_file_edit' );
		} catch ( WPAjaxDieContinueException $e ) {
			// We expected this, do nothing.
		} catch ( WPAjaxDieStopException $e ) {
			// We expected this, do nothing.
		}
		$response = json_decode( $this->_last_response, true );
		$this->assertIsArray( $response );
		$this->assertNotEmpty( $response );
	}

	/**
	 * @medium
	 */
	public function test_ocsh_check_usernames() {
		try {
			$this->_handleAjax( 'ocsh_check_usernames' );
		} catch ( WPAjaxDieContinueException $e ) {
			// We expected this, do nothing.
		} catch ( WPAjaxDieStopException $e ) {
			// We expected this, do nothing.
		}
		$response = json_decode( $this->_last_response, true );
		$this->assertIsArray( $response );
		$this->assertNotEmpty( $response );
	}

	/**
	 * @medium
	 */
	public function test_ocsh_check_dis_plugin() {
		try {
			$_POST['HTTP_USER_AGENT'] = 'PHPUNIT';
			$this->_handleAjax( 'ocsh_check_dis_plugin' );
		} catch ( WPAjaxDieContinueException $e ) {
			// We expected this, do nothing.
		} catch ( WPAjaxDieStopException $e ) {
			// We expected this, do nothing.
		}
		$response = json_decode( $this->_last_response, true );
		$this->assertIsArray( $response );
		$this->assertNotEmpty( $response );
	}

	/**
	 * @medium
	 */
	public function test_ocsh_check_uploads_index() {
		try {
			$this->_handleAjax( 'ocsh_check_uploads_index' );
		} catch ( WPAjaxDieContinueException $e ) {
			// We expected this, do nothing.
		} catch ( WPAjaxDieStopException $e ) {
			// We expected this, do nothing.
		}
		$response = json_decode( $this->_last_response, true );
		$this->assertIsArray( $response );
		$this->assertNotEmpty( $response );
		$this->arrayHasKey( 'status', $response );
		$this->arrayHasKey( 'title', $response );
		$this->arrayHasKey( 'desc', $response );

		if ( $response['status'] === 1 ) {
			$this->arrayHasKey( 'file-list', $response );
			$this->arrayHasKey( 'html', $response );
		}

	}

	/**
	 * @medium
	 */
	public function test_ocsh_check_woocommerce_sessions() {
		try {
			$this->_handleAjax( 'ocsh_check_woocommerce_sessions' );
		} catch ( WPAjaxDieContinueException $e ) {
			// We expected this, do nothing.
		} catch ( WPAjaxDieStopException $e ) {
			// We expected this, do nothing.
		}
		$response = json_decode( $this->_last_response, true );
		$this->assertIsArray( $response );
		$this->assertNotEmpty( $response );
		$this->arrayHasKey( 'status', $response );
		$this->arrayHasKey( 'title', $response );
		$this->arrayHasKey( 'desc', $response );

		if ( $response['status'] === 1 ) {
			$this->arrayHasKey( 'file-list', $response );
			$this->arrayHasKey( 'html', $response );
		}

	}

}