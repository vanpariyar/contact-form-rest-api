<?php

/**
 * Class REST_API_Test
 *
 * @package My_Plugin
 */

class REST_API_Test extends WP_UnitTestCase {

    /**
     * Test that the custom REST API route is registered.
     */
    public function test_rest_api_route_registration() {
        $routes = rest_get_server()->get_routes();

        $this->assertArrayHasKey( '/my-plugin/v1/data', $routes );
    }

    /**
     * Test that the REST API endpoint returns the correct response.
     */
    public function test_rest_api_response() {
        $request = new WP_REST_Request( 'GET', '/my-plugin/v1/data' );
        $response = rest_do_request( $request );
        $data = $response->get_data();

        $this->assertEquals( 200, $response->get_status() );
        $this->assertArrayHasKey( 'message', $data );
        $this->assertEquals( 'Success', $data['message'] );
    }
}