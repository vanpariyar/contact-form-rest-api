<?php
/**
 * Contact Form REST API Tests
 *
 * @package Contact_Form_Rest_Api
 */

/**
 * Test contact form REST API endpoints
 */
class Contact_Form_REST_API_Test extends WP_UnitTestCase {

    /**
     * Test contact form submission endpoint
     */
    public function test_contact_form_submission() {
        // Create a test request
        $request = new WP_REST_Request( 'POST', '/contact-form/v1/submit' );
        $request->set_param( 'name', 'John Doe' );
        $request->set_param( 'email', 'john@example.com' );
        $request->set_param( 'message', 'This is a test message' );
        $request->set_param( 'subject', 'Test Subject' );
        $request->set_param( 'phone', '1234567890' );
        
        // Get the REST server
        $server = rest_get_server();
        
        // Process the request
        $response = $server->dispatch( $request );
        
        // Assert response
        $this->assertEquals( 201, $response->get_status() );
        
        $data = $response->get_data();
        $this->assertArrayHasKey( 'id', $data );
        $this->assertArrayHasKey( 'message', $data );
        $this->assertArrayHasKey( 'status', $data );
        $this->assertEquals( 'success', $data['status'] );
    }
    
    /**
     * Test contact form submission with missing required fields
     */
    public function test_contact_form_submission_missing_fields() {
        // Create a test request with missing email
        $request = new WP_REST_Request( 'POST', '/contact-form/v1/submit' );
        $request->set_param( 'name', 'John Doe' );
        $request->set_param( 'message', 'This is a test message' );
        
        // Get the REST server
        $server = rest_get_server();
        
        // Process the request
        $response = $server->dispatch( $request );
        
        // Assert response
        $this->assertEquals( 400, $response->get_status() );
        
        $data = $response->get_data();
        $this->assertArrayHasKey( 'code', $data );
        $this->assertEquals( 'missing_field', $data['code'] );
    }
    
    /**
     * Test contact form submission with invalid email
     */
    public function test_contact_form_submission_invalid_email() {
        // Create a test request with invalid email
        $request = new WP_REST_Request( 'POST', '/contact-form/v1/submit' );
        $request->set_param( 'name', 'John Doe' );
        $request->set_param( 'email', 'invalid email' );
        $request->set_param( 'message', 'This is a test messagev1 ' );
        
        // Get the REST server
        $server = rest_get_server();
        
        // Process the request
        $response = $server->dispatch( $request );
        
        // Assert response
        $this->assertEquals( 400, $response->get_status() );
        
        $data = $response->get_data();
        $this->assertArrayHasKey( 'code', $data );
        $this->assertEquals( 'missing_field', $data['code'] );
    }
    
    /**
     * Test getting contact submissions (admin only)
     */
    public function test_get_contact_submissions() {
        // Create a test user with edit_posts capability
        $user_id = $this->factory->user->create( array( 'role' => 'editor' ) );
        wp_set_current_user( $user_id );
        
        // Create a test request
        $request = new WP_REST_Request( 'GET', '/contact-form/v1/submissions' );
        
        // Get the REST server
        $server = rest_get_server();
        
        // Process the request
        $response = $server->dispatch( $request );
        
        // Assert response
        $this->assertEquals( 200, $response->get_status() );
        
        $data = $response->get_data();
        $this->assertIsArray( $data );
    }
    
    /**
     * Test getting contact submissions without permission
     */
    public function test_get_contact_submissions_no_permission() {
        // Create a test user without edit_posts capability
        $user_id = $this->factory->user->create( array( 'role' => 'subscriber' ) );
        wp_set_current_user( $user_id );
        
        // Create a test request
        $request = new WP_REST_Request( 'GET', '/contact-form/v1/submissions' );
        
        // Get the REST server
        $server = rest_get_server();
        
        // Process the request
        $response = $server->dispatch( $request );
        
        // Assert response
        $this->assertEquals( 403, $response->get_status() );
    }
    
    /**
     * Test getting a specific contact submission
     */
    public function test_get_contact_submission() {
        // Create a test user with edit_posts capability
        $user_id = $this->factory->user->create( array( 'role' => 'editor' ) );
        wp_set_current_user( $user_id );
        
        // Create a test contact submission
        $post_id = $this->factory->post->create( array(
            'post_type' => 'contact',
            'post_title' => 'Test Contact',
            'post_content' => 'Test message',
            'post_status' => 'publish'
        ) );
        
        update_post_meta( $post_id, 'contact_email', 'test@example.com' );
        
        // Create a test request
        $request = new WP_REST_Request( 'GET', '/contact-form/v1/submissions/' . $post_id );
        
        // Get the REST server
        $server = rest_get_server();
        
        // Process the request
        $response = $server->dispatch( $request );
        
        // Assert response
        $this->assertEquals( 200, $response->get_status() );
        
        $data = $response->get_data();
        $this->assertArrayHasKey( 'id', $data );
        $this->assertArrayHasKey( 'name', $data );
        $this->assertArrayHasKey( 'email', $data );
        $this->assertEquals( $post_id, $data['id'] );
    }
    
    /**
     * Test getting a non-existent contact submission
     */
    public function test_get_contact_submission_not_found() {
        // Create a test user with edit_posts capability
        $user_id = $this->factory->user->create( array( 'role' => 'editor' ) );
        wp_set_current_user( $user_id );
        
        // Create a test request for non-existent post
        $request = new WP_REST_Request( 'GET', '/contact-form/v1/submissions/99999' );
        
        // Get the REST server
        $server = rest_get_server();
        
        // Process the request
        $response = $server->dispatch( $request );
        
        // Assert response
        $this->assertEquals( 404, $response->get_status() );
        
        $data = $response->get_data();
        $this->assertArrayHasKey( 'code', $data );
        $this->assertEquals( 'not_found', $data['code'] );
    }
}
