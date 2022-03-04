<?php

class OnecomPingbackTest extends WP_UnitTestCase {
	public $baseObj;

	public function setUp() {
		parent::setUp();

		$this->baseObj = new OnecomPingback();
	}

	public function tearDown() {
		//code here

		parent::tearDown();
	}

	public function test_check_pingbacks_test() {
		$result = $this->baseObj->check_pingbacks();

		$this->assertArrayHasKey( 'status', $result );
		$this->assertArrayHasKey( 'title', $result );
		$this->assertArrayHasKey( 'desc', $result );

		$this->assertEquals( $result['status'], 1 );
		$this->assertEquals( $result['title'], 'Pingback is enabled.' );
		$this->assertEquals( $result['desc'], 'You have pingbacks enabled on your site.' );
	}

	public function test_check_pingbacks_disabled() {
		update_option( 'default_pingback_flag', 0 );
		update_option( 'default_ping_status', '' );
		$result = $this->baseObj->check_pingbacks();

		$this->assertArrayHasKey( 'status', $result );
		$this->assertArrayHasKey( 'title', $result );
		$this->assertArrayHasKey( 'desc', $result );
		$this->assertEquals( $result['status'], 0 );
		$this->assertEquals( $result['title'], 'Pingbacks are disabled.' );
	}

	public function test_fix_pingback() {
		$result = $this->baseObj->fix_pingback();
		$this->assertArrayHasKey( 'status', $result );
		$this->assertArrayHasKey( 'title', $result );
		$this->assertArrayHasKey( 'desc', $result );
	}

	public function test_fix_pingback_fail() {
		update_option( 'default_ping_status', '' );
		update_option( 'default_pingback_flag', '' );
		$result = $this->baseObj->fix_pingback();
		$this->assertArrayHasKey( 'status', $result );
		$this->assertArrayHasKey( 'title', $result );
		$this->assertArrayHasKey( 'desc', $result );
	}

	public function test_undo() {
		update_option( 'default_pingback_flag', 0 );
		update_option( 'default_ping_status', '' );
		$result = $this->baseObj->undo();
		$this->assertArrayHasKey( 'status', $result );
		$this->assertArrayHasKey( 'title', $result );
		$this->assertArrayHasKey( 'desc', $result );
		$this->assertEquals( $result['status'], 0 );
		$this->assertEquals( $result['title'], 'Pingback enabled' );
	}

	public function test_undo_fail() {
		update_option( 'default_pingback_flag', 1 );
		update_option( 'default_ping_status', 'open' );
		$result = $this->baseObj->undo();
		$this->assertArrayHasKey( 'status', $result );
		$this->assertArrayHasKey( 'title', $result );
		$this->assertArrayHasKey( 'desc', $result );
		$this->assertEquals( $result['status'], 1 );
		$this->assertEquals( $result['title'], 'Pingback could not be enabled' );
	}
}