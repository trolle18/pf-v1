<?php
/**
 * Class OnecomTest
 *
 * @package Onecom_Themes_Plugins
 */

class OnecomXMLRpcTest extends WP_UnitTestCase {
	public $baseObj;

	public function setUp() {
		parent::setUp();

		$this->baseObj = new OnecomXmlRpc();
	}

	public function tearDown() {
		//code here

		parent::tearDown();
	}

	public function test_check_xmlrpc() {
		$result = $this->baseObj->check_xmlrpc();
		$this->assertArrayHasKey( 'status', $result );
		$this->assertArrayHasKey( 'title', $result );
		$this->assertArrayHasKey( 'desc', $result );
	}

	public function test_check_disabled_xmlrpc() {
		$result = $this->baseObj->check_xmlrpc();
		$this->assertArrayHasKey( 'status', $result );
		$this->assertArrayHasKey( 'title', $result );
		$this->assertArrayHasKey( 'desc', $result );
	}

	public function test_fix_check_xmlrpc() {

		$result = $this->baseObj->fix_check_xmlrpc();
		$this->assertArrayHasKey( 'status', $result );
		$this->assertArrayHasKey( 'title', $result );
		$this->assertArrayHasKey( 'desc', $result );

		$this->assertEquals( $result['status'], 0 );
		$this->assertEquals( $result['title'], 'XML RPC disabled' );
		$this->assertEquals( $result['desc'], '' );

	}

	/**
	 * @runTestsInSeparateProcesses
	 */
	public function test_undo_check_xmlrpc() {
		$result = $this->baseObj->undo_check_xmlrpc();
		$this->assertArrayHasKey( 'status', $result );
		$this->assertArrayHasKey( 'title', $result );
		$this->assertArrayHasKey( 'desc', $result );
	}
}