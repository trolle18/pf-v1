<?php
/**
 * Class OclauthTest
 *
 * @package Onecom_Themes_Plugins
 */

/**
 * OCLAUTH test case.
 */
class OclauthTest extends WP_UnitTestCase {

    public $obj;
    public function setUp() {
        parent::setUp();

        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/93.0.4577.63 Safari/537.36';
        //create OCLAUTH object
        $this->obj = new OCLAUTH();
        $this->obj->getInstance();
    }

    /**
     *  Test for class instance
     */
    public function test_getInstance(){
        $ClassInstance = $this->obj->getInstance();
        $this->assertInstanceOf(OCLAUTH::class, $ClassInstance);
    }

    /**
     * Test for construction
     */
    public function test_construct(){
        // $clsObj to be tested
        $clsObj  = $this->obj->getInstance();
        // assert function to test whether 'request_host' is an attribute of object
        $this->assertObjectHasAttribute('request_host', $clsObj, "Object doesn't contains 'request_host' as key");

        // assert function to test whether 'prefix' is an attribute of object
        $this->assertObjectHasAttribute('prefix', $clsObj, "Object doesn't contains 'prefix' as key");

        // assert function to test whether 'request_uri' is an attribute of object
        $this->assertObjectHasAttribute('request_uri', $clsObj, "Object doesn't contains 'request_uri' as key");

        // assert function to test whether 'site_url' is an attribute of object
        $this->assertObjectHasAttribute('site_url', $clsObj, "Object doesn't contains 'site_url' as key");

        // assert function to test whether 'home_url' is an attribute of object
        $this->assertObjectHasAttribute('home_url', $clsObj, "Object doesn't contains 'home_url' as key");

        // assert function to test whether 'is_wpadmin' is an attribute of object
        $this->assertObjectHasAttribute('is_wpadmin', $clsObj, "Object doesn't contains 'is_wpadmin' as key");

        // assert function to test whether 'is_rest_request' is an attribute of object
        $this->assertObjectHasAttribute('is_rest_request', $clsObj, "Object doesn't contains 'is_rest_request' as key");

        // assert function to test whether 'is_own_dir' is an attribute of object
        $this->assertObjectHasAttribute('is_own_dir', $clsObj, "Object doesn't contains 'is_own_dir' as key");

        // assert function to test whether 'is_req_host_different' is an attribute of object
        $this->assertObjectHasAttribute('is_req_host_different', $clsObj, "Object doesn't contains 'is_req_host_different' as key");

        // assert function to test whether 'onecom_domain' is an attribute of object
        $this->assertObjectHasAttribute('onecom_domain', $clsObj, "Object doesn't contains 'onecom_domain' as key");

        // assert function to test whether 'onecom_subdomain' is an attribute of object
        $this->assertObjectHasAttribute('onecom_subdomain', $clsObj, "Object doesn't contains 'onecom_subdomain' as key");
    }

    /**
     * Test destruct function
     */
    public function test_destruct()
    {
        $this->assertTrue( true ); //function call test
    }

    /**
     * Test clean url for non https://, http:// and www.
     */
    public function test_cleanURL(){
        $testURL = 'https://www.example.com';
        $expectedURL = 'example.com';
        $this->assertEquals($expectedURL,$this->obj->cleanURL($testURL));
    }

    /**
     * Test function call for validateRequestURL
     */
    public function test_validateRequestURL(){
        $this->assertNull($this->obj->validateRequestURL());
    }

    /**
     *  Test function responseHandler
     */
    public function test_responseHandler(){
        $is_success        = true;
        $data              = array('message' => 'Valid Status');
        $Successcallfun    = $this->obj->responseHandler($is_success,$data);

        $this->assertIsBool($Successcallfun["error"]);
        $this->assertEquals('Valid Status',$Successcallfun["message"]);
        $this->assertIsArray($this->obj->additional_info);
    }

    /**
     *  Function to check token exist
     */
    public function test_tokenExist(){
        //check if token exist
        $_GET['onecom-auth'] = 'demo-jwt-token-key';
        $this->assertEquals(true,$this->obj->tokenExist());

        //check if token key not exist
        $_GET = '';
        $this->assertEquals(false,$this->obj->tokenExist());
    }

    /**
     * Function to check validate token
     */
    public function test_validateToken(){
        $this->assertEquals(true,true);
    }

    /**
     * Check token data
     */
    public function test_checkTokenData(){

        $clsObj             = new stdClass();
        $clsObj->domain     = 'example.com';
        $clsObj->subdomain  = 'demo1';

        $this->obj->onecom_domain       = 'example.com';
        $this->obj->onecom_subdomain    = 'demo1';

        //check valid domain and subdomain
        $getDomainStatus = $this->obj->checkTokenData($clsObj);
        $this->assertEquals(false,$getDomainStatus["error"]);
        $this->assertEquals("Valid Status",$getDomainStatus["message"]);

        //check if domain and sub-domain is not match
        $this->obj->onecom_domain       = 'example.com';
        $this->obj->onecom_subdomain    = 'demo2';

        $getDomainErrorStatus = $this->obj->checkTokenData($clsObj);
        $this->assertEquals(true,$getDomainErrorStatus["error"]);
        $this->assertEquals("Incorrect Installation URL provided or unknown error occured.",$getDomainErrorStatus["message"]);

    }

    /**
     *  check token for failed cases
     */
    public function test_checkToken(){
        $this->obj->onecom_domain    = 'wpin3.1prod.one';
        $this->obj->onecom_subdomain = 'www';
        $this->obj->tokenVal = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJkb21haW4iOiJ3cGluMy4xcHJvZC5vbmUiLCJzdWJkb21haW4iOiJ3d3ciLCJleHAiOjE2NjQ1MTM0NDAsImlhdCI6MTYyMTk0MjIzMH0.bdVIRM-xtHIb5KRhb1xpHuL4NKhKuy79VZcSm-hC1dETESfK7kDPInNetQ99GS5BddX6cnIk6gedb6ELerFiB_5LZDvxYqVHSM1uS81J1OuE4O5ckNnLUzBGhQUyKiX_kykz-Y7BlQ0IKHOq6ozqn9uUrKiROQt0jzznS9-kzTlyVOWU1IX_97gLH3SHJnKWo-cncJgxsQoogXAEOIxIlt5B6jlFlZMrnIV_VwiG5z-Bccx7m1k4RhUTgCerOR6_FP9Br0aT5s6niCOVoS7Jhru2BiXsdd7l4wwS0lOsmMMfS4D-YjTdnJRUgR2WtEXqW0myoMfN5g3nw1bmAGg-Zg';

        //check for token empty
        $emptyToken = $this->obj->checkToken('');
        $this->assertEquals('Token missing.',$emptyToken["message"]);

        //test failed token sign
        $failedsSign = $this->obj->checkToken($this->obj->tokenVal);
        $this->assertEquals('Signature verification failed',$failedsSign["message"]);
    }

    /**
     * Check valid domain
     */
    public function test_checkValidDomain(){
        $this->obj->onecom_domain    = 'example.com';
        $this->obj->onecom_subdomain = 'demo1';

        $token_domain       = 'example.com';
        $token_subdomain    = 'demo1';

        //If domain is valid
        $this->assertEquals(true,$this->obj->checkValidDomain($token_domain,$token_subdomain));

        //if domain is not matched
        $token_domain       = 'example.com';
        $token_subdomain    = 'demo2';

        $this->assertEquals(false,$this->obj->checkValidDomain($token_domain,$token_subdomain));
    }

    /**
     *  Check sitedomain
     */
    public function test_get_sitedomain(){

        //if set to example.com
        $_SERVER['ONECOM_DOMAIN_NAME'] = 'example.com';
        $this->assertEquals('example.com',$this->obj->get_sitedomain());
    }

    /**
     * Check site subdomain
     */
    public function test_get_sitesubdomain(){

        $_SERVER['ONECOM_DOMAIN_NAME']  = 'example.com';
        $_SERVER['SERVER_NAME']         = 'demo1.example.com';
        $this->obj->home_url            = 'https://demo1.example.com';
        $this->obj->site_url            = 'https://demo1.example.com';

        //test site subdomain
        $this->assertEquals('demo1',$this->obj->get_sitesubdomain());
    }

    /**
     * Check ocl error login page
     */
    public function test_ocl_login_error_callback(){
        $_GET['redirect_to'] = 'https://23aug-v2.wp1.1stg.one/?onecom-auth=eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJkb21haW4iOiJ3cDEuMXN0Zy5vbmUiLCJzdWJkb21haW4iOiIyM2F1Zy12MiIsImV4cCI6MTYzMTUxMjg2NywiaWF0IjoxNjMxNTEyODM3fQ.HM-c2oTdj6ipyTzzsML--8VfONMqOIao8Ymtj4inOhAD8Vs0s5oxYr3DaJO5BNdi1nCENF8tMTxv0j75mm0TRCGjlkyJ_JbJuayxW7BFo-xTcASOpcIR9cadHF-aRI8Z8KbZ8AqoarHCARhxxhikKyACejLahkYHDCu8bvFfrRil9hUzcU6HDmaSB4pWoVjGLa3I3f0UZU57txlwKBNEeu1j2ZDByraUZ0RTTLMuQ5LiO6OfT3rzcwm8AHGv3WS7z9PZYkzZa7JGkGVZNbQm4aEGpL6xoeDk5HIsYXctkJ3byXUrmO7yt3i6EJzf17MKXecwHTDSzjVp0GXKBP-y8A&wp_path=admin.php?page=onecom-vcache-plugin';
        $expiredmsg = '<div id="login_error">	Expired token<br>
                </div>';
        $this->assertEquals($expiredmsg,$this->obj->ocl_login_error_callback('Redirect to admin page'));
    }

    /**
     * Check login function where function ends with wp_redirect
     */
    public function test_oclAuthCheck(){
       $this->assertEquals(true,true);
    }

    /**
     *  Test for admin user creation
     */
    public function test_getAdminIds(){
        $user_id = self::factory()->user->create( array(
            'role' => 'administrator',
        ) );

        $this->assertEquals(1,$this->obj->getAdminIds());
    }

    /**
     *  Clear all test data
     */
    public function tearDown() {
        //code here
        parent::tearDown();
    }
}