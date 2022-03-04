<?php
/**
 * Class OnecomTest
 *
 * @package Onecom_Themes_Plugins
 */

class OnecomTest extends WP_UnitTestCase {
	public $baseObj;

	public function setUp() {
		parent::setUp();
		//code here
		update_option( 'active_plugins', [ 'woocommerce/woocommerce.php' ] );
		update_option( 'onecom_hm_data', [
			'recaptcha_keys' => [
				'test_field_1' => 'test_field_1_value'
			]
		] );
		$this->baseObj = new Onecom();

	}

	public function tearDown() {
		//code here
		delete_option( 'active_plugins' );
		delete_option( 'onecom_hm_data' );
		parent::tearDown();
	}


	public function test_property_checks() {
		$is_array     = is_array( $this->baseObj->checks );
		$is_not_empty = ! empty( $this->baseObj->checks );
		$this->assertTrue( ( $is_array && $is_not_empty ) );
	}

	public function test_log_entry_with_debug_mode_disabled() {
		$this->assertTrue( ! $this->baseObj->log_entry( "Test message" ) );
	}

	public function test_log_entry_with_debug_mode_enabled() {
		$this->assertNotWPError( ! $this->baseObj->log_entry( "Test message", 1 ) );
	}

	public function test_save_result_with_finish_0() {
		$result = $this->baseObj->save_result( "some_stage", "healthy", 0 );
		$this->assertTrue( $result );
	}

	public function test_calculate_score_with_transient() {
		$transient_array = [
			'time'                => 1615290324,
			'debug_mode'          => 1,
			'php_updates'         => 0,
			'plugin_updates'      => 0,
			'theme_updates'       => 1,
			'core_updates'        => 0,
			'wp_connection'       => 0,
			'auto_updates'        => 0,
			'ssl_certificate'     => 1,
			'file_execution'      => 0,
			'file_permissions'    => 1,
			'db_security'         => 0,
			'admin_file_edit'     => 0,
			'common_usernames'    => 1,
			'discouraged_plugins' => 1
		];
		$result          = $this->baseObj->calculate_score( $transient_array );

		$this->assertTrue( is_array( $result ) );
		$this->assertArrayHasKey( 'time', $result );
		$this->assertArrayHasKey( 'score', $result );
	}

	public function test_calculate_score_without_transient() {
		$result = $this->baseObj->calculate_score( [] );
		$this->assertEquals( 0, $result );
	}

	public function test_get_html_with_simple_list() {
		$html = $this->baseObj->get_html( 'uploads_index', [
			'status' => 1,
			'title'  => 'Uploads is not okay',
			'desc'   => 'Uploads directory index is not okay',
			'list'   => [
				'file 1',
				'file 2'
			]

		] );

		$expected_html = '<li id="ocsh-uploads_index" class="ocsh-bullet ocsh-bullet-premium">
<span class="ocsh-error"></span> <h4 class="ocsh-scan-title">Uploads is not okay</h4><span class="oc-caret"></span><div class="ocsh-desc-wrap hidden"><div class="osch-desc">Uploads directory index is not okay</div><div class="ocsh-actions"><span class="ocsh-resolve-wrap"><a class="oc-mark-resolved" data-check="">Ignore in future scans</a></span></div></div></li>';

		$this->assertEquals( $html, $expected_html );
	}

	public function test_get_html_with_file_list() {
		$html = $this->baseObj->get_html( 'uploads_index', [
			'status'    => 1,
			'title'     => 'Uploads is not okay',
			'desc'      => 'Uploads directory index is not okay',
			'file-list' => [
				'key 1' => 'file 1',
				'key 1' => 'file 2'
			]

		] );

		$expected_html = '<li id="ocsh-uploads_index" class="ocsh-bullet ocsh-bullet-premium">
<h4 class="ocsh-scan-title">Uploads is not okay</h4>
<span class="ocsh-error"></span><div class="ocsh-desc-wrap hidden"><div class="osch-desc">Uploads directory index is not okay<ul class="ocsh-desc-li"><li>key 1 <span class="ocsh-file-list-item">(file 2)</span> </li></ul></div><div class="ocsh-actions"><span class="ocsh-resolve-wrap"><a class="oc-mark-resolved" data-check="uploads_index">Ignore in future scans</a></span></div></div></li>';

		$this->assertEquals( $html, $expected_html );
	}

	public function test_get_html_with_fix() {
		$html = $this->baseObj->get_html( 'uploads_index', [
			'status'   => 1,
			'title'    => 'Uploads is not okay',
			'desc'     => 'Uploads directory index is not okay',
			'fix'      => true,
			'fix_text' => 'Some fix',
			'fix_url'  => 'somefix.tld'

		] );

		$expected_html = '<li id="ocsh-uploads_index" class="ocsh-bullet ocsh-bullet-premium">
<h4 class="ocsh-scan-title">Uploads is not okay</h4>
<span class="ocsh-error"></span><div class="ocsh-desc-wrap hidden"><div class="osch-desc">Uploads directory index is not okay</div><div class="ocsh-actions"><span class="ocsh-resolve-wrap"><a class="oc-mark-resolved" data-check="uploads_index">Ignore in future scans</a></span><span class="ocsh-fix-wrap"><button data-url="somefix.tld" class="oc-fix-button button" data-check="uploads_index">Some fix</button></span></div></div></li>';

		$this->assertEquals( $html, $expected_html );
	}

	public function test_format_result() {
		$result = $this->baseObj->format_result( 0, 'success message', 'success description' );
		$this->assertArrayHasKey( 'status', $result );
		$this->assertArrayHasKey( 'title', $result );
		$this->assertArrayHasKey( 'desc', $result );
	}

	public function test_get_html_with_undo() {
		$result   = $this->baseObj->get_html( 'uploads_index', [
			'status' => 0,
			'title'  => 'Uploads is okay',
			'desc'   => 'Uploads directory index is okay',
			'undo'   => true,

		] );
		$expected = '<li id="ocsh-uploads_index" class="ocsh-bullet ocsh-bullet-premium">
<h4 class="ocsh-scan-title">Uploads is okay<span class="ocsh-undo" title="Undo" data-check="uploads_index"></span></h4>
<span class="ocsh-success"></span></li>';
		$this->assertEquals( $expected, $result );
	}

	public function test_input_fields() {
		$result = $this->baseObj->get_html( 'login_form', [
			'status'       => 1,
			'title'        => 'Login form is okay',
			'desc'         => 'Login form seems okay',
			'input_fields' => [
				[
					'name'  => 'test_field_1',
					'label' => 'test_field_label'
				]
			]
		] );

		$expected = '<li id="ocsh-login_form" class="ocsh-bullet ocsh-bullet-premium">
<h4 class="ocsh-scan-title">Login form is okay</h4>
<span class="ocsh-error"></span><div class="ocsh-desc-wrap hidden"><div class="osch-desc">Login form seems okay<div class="ocsh_input-fields">
<label>test_field_label</label>
<input value="test_field_1_value" type="text" name="test_field_1" id="test_field_1"><p class="oc-error-message"></p>
</div></div><div class="ocsh-actions"><span class="ocsh-resolve-wrap"><a class="oc-mark-resolved" data-check="login_form">Ignore in future scans</a></span></div></div></li>';
		$this->assertEquals( $expected, $result );
	}
}
