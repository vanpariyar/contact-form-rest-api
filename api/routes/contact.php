<?php
/**
 * Contact Form REST API Routes
 * 
 * @package ContactFormRestApi
 */

// Check if WP_REST_Controller is available
if ( ! class_exists( 'WP_REST_Controller' ) ) {
    return;
}

class Contact_Form_REST_Controller extends WP_REST_Controller {
 
  /**
   * Register the routes for the contact form API.
   */
  public function register_routes() {
    $version = '1';
    $namespace = 'contact-form/v' . $version;
    $base = 'submit';
    
    // Register the contact form submission endpoint
    register_rest_route( $namespace, '/' . $base, array(
      array(
        'methods'             => WP_REST_Server::CREATABLE,
        'callback'            => array( $this, 'submit_contact_form' ),
        'permission_callback' => array( $this, 'submit_contact_form_permissions_check' ),
        'args'                => $this->get_endpoint_args_for_item_schema( true ),
      ),
    ) );
    
    // Register endpoint to get all contact submissions (admin only)
    register_rest_route( $namespace, '/submissions', array(
      array(
        'methods'             => WP_REST_Server::READABLE,
        'callback'            => array( $this, 'get_contact_submissions' ),
        'permission_callback' => array( $this, 'get_submissions_permissions_check' ),
        'args'                => $this->get_collection_params(),
      ),
    ) );
    
    // Register endpoint to get a specific contact submission (admin only)
    register_rest_route( $namespace, '/submissions/(?P<id>[\d]+)', array(
      array(
        'methods'             => WP_REST_Server::READABLE,
        'callback'            => array( $this, 'get_contact_submission' ),
        'permission_callback' => array( $this, 'get_submission_permissions_check' ),
        'args'                => array(
          'context' => array(
            'default' => 'view',
          ),
        ),
      ),
    ) );
    
    // Register schema endpoint
    register_rest_route( $namespace, '/schema', array(
      'methods'             => WP_REST_Server::READABLE,
      'callback'            => array( $this, 'get_public_item_schema' ),
      'permission_callback' => '__return_true',
    ) );
  }
 
  /**
   * Submit a contact form
   *
   * @param WP_REST_Request $request Full data about the request.
   * @return WP_Error|WP_REST_Response
   */
  public function submit_contact_form( $request ) {
    $params = $request->get_params();
    
    // Validate required fields
    $required_fields = array( 'name', 'email', 'message' );
    foreach ( $required_fields as $field ) {
      if ( empty( $params[ $field ] ) ) {
        return new WP_Error( 
          'missing_field', 
          sprintf( __( 'Missing required field: %s', 'contact-form-rest-api' ), $field ), 
          array( 'status' => 400 ) 
        );
      }
    }
    
    // Sanitize and validate email
    $email = sanitize_email( $params['email'] );
    if ( ! is_email( $email ) ) {
      return new WP_Error( 
        'invalid_email', 
        __( 'Invalid email address', 'contact-form-rest-api' ), 
        array( 'status' => 400 ) 
      );
    }
    
    // Prepare contact data
    $contact_data = array(
      'post_title'    => sanitize_text_field( $params['name'] ),
      'post_content'  => sanitize_textarea_field( $params['message'] ),
      'post_status'   => 'publish',
      'post_type'     => 'contact',
      'post_author'   => 1, // Default admin user
    );
    
    // Insert the contact submission
    $post_id = wp_insert_post( $contact_data );
    
    if ( is_wp_error( $post_id ) ) {
      return new WP_Error( 
        'insert_failed', 
        __( 'Failed to save contact submission', 'contact-form-rest-api' ), 
        array( 'status' => 500 ) 
      );
    }
    
    // Save additional meta data
    update_post_meta( $post_id, 'contact_email', $email );
    update_post_meta( $post_id, 'contact_phone', isset( $params['phone'] ) ? sanitize_text_field( $params['phone'] ) : '' );
    update_post_meta( $post_id, 'contact_subject', isset( $params['subject'] ) ? sanitize_text_field( $params['subject'] ) : '' );
    update_post_meta( $post_id, 'submission_date', current_time( 'mysql' ) );
    
    // Send email notification if configured
    $this->send_contact_notification( $params, $post_id );
    
    // Prepare response data
    $response_data = array(
      'id'      => $post_id,
      'message' => __( 'Contact form submitted successfully', 'contact-form-rest-api' ),
      'status'  => 'success'
    );
    
    return new WP_REST_Response( $response_data, 201 );
  }
  
  /**
   * Get all contact submissions (admin only)
   *
   * @param WP_REST_Request $request Full data about the request.
   * @return WP_Error|WP_REST_Response
   */
  public function get_contact_submissions( $request ) {
    $args = array(
      'post_type'      => 'contact',
      'post_status'    => 'publish',
      'posts_per_page' => $request->get_param( 'per_page' ) ?: 10,
      'paged'          => $request->get_param( 'page' ) ?: 1,
      'orderby'        => 'date',
      'order'          => 'DESC',
    );
    
    $query = new WP_Query( $args );
    $submissions = array();
    
    if ( $query->have_posts() ) {
      while ( $query->have_posts() ) {
        $query->the_post();
        $submissions[] = $this->prepare_contact_submission_for_response( get_post() );
      }
    }
    
    wp_reset_postdata();
    
    return new WP_REST_Response( $submissions, 200 );
  }
  
  /**
   * Get a specific contact submission (admin only)
   *
   * @param WP_REST_Request $request Full data about the request.
   * @return WP_Error|WP_REST_Response
   */
  public function get_contact_submission( $request ) {
    $post_id = $request->get_param( 'id' );
    $post = get_post( $post_id );
    
    if ( ! $post || $post->post_type !== 'contact' ) {
      return new WP_Error( 
        'not_found', 
        __( 'Contact submission not found', 'contact-form-rest-api' ), 
        array( 'status' => 404 ) 
      );
    }
    
    $data = $this->prepare_contact_submission_for_response( $post );
    
    return new WP_REST_Response( $data, 200 );
  }
  
  /**
   * Send email notification for new contact submission
   *
   * @param array $params Contact form parameters
   * @param int $post_id Post ID of the contact submission
   */
  private function send_contact_notification( $params, $post_id ) {
    $admin_email = get_option( 'admin_email' );
    $site_name = get_bloginfo( 'name' );
    
    $subject = sprintf( __( 'New contact form submission from %s', 'contact-form-rest-api' ), $params['name'] );
    
    $message = sprintf(
      __( 'You have received a new contact form submission:

Name: %s
Email: %s
Phone: %s
Subject: %s
Message: %s

View submission: %s', 'contact-form-rest-api' ),
      $params['name'],
      $params['email'],
      isset( $params['phone'] ) ? $params['phone'] : 'N/A',
      isset( $params['subject'] ) ? $params['subject'] : 'N/A',
      $params['message'],
      admin_url( 'post.php?post=' . $post_id . '&action=edit' )
    );
    
    wp_mail( $admin_email, $subject, $message );
  }
  
  /**
   * Prepare contact submission for response
   *
   * @param WP_Post $post Contact submission post
   * @return array Prepared data
   */
  private function prepare_contact_submission_for_response( $post ) {
    return array(
      'id'      => $post->ID,
      'name'    => $post->post_title,
      'email'   => get_post_meta( $post->ID, 'contact_email', true ),
      'phone'   => get_post_meta( $post->ID, 'contact_phone', true ),
      'subject' => get_post_meta( $post->ID, 'contact_subject', true ),
      'message' => $post->post_content,
      'date'    => $post->post_date,
      'status'  => $post->post_status,
    );
  }
 
  /**
   * Check if a given request has access to submit contact form
   *
   * @param WP_REST_Request $request Full data about the request.
   * @return WP_Error|bool
   */
  public function submit_contact_form_permissions_check( $request ) {
    // Allow public access for contact form submission
    return true;
  }
  
  /**
   * Check if a given request has access to get contact submissions
   *
   * @param WP_REST_Request $request Full data about the request.
   * @return WP_Error|bool
   */
  public function get_submissions_permissions_check( $request ) {
    return current_user_can( 'edit_posts' );
  }
  
  /**
   * Check if a given request has access to get a specific contact submission
   *
   * @param WP_REST_Request $request Full data about the request.
   * @return WP_Error|bool
   */
  public function get_submission_permissions_check( $request ) {
    return current_user_can( 'edit_posts' );
  }
 
  /**
   * Get the query params for collections
   *
   * @return array
   */
  public function get_collection_params() {
    return array(
      'page'     => array(
        'description'       => __( 'Current page of the collection.', 'contact-form-rest-api' ),
        'type'              => 'integer',
        'default'           => 1,
        'sanitize_callback' => 'absint',
      ),
      'per_page' => array(
        'description'       => __( 'Maximum number of items to be returned in result set.', 'contact-form-rest-api' ),
        'type'              => 'integer',
        'default'           => 10,
        'sanitize_callback' => 'absint',
      ),
    );
  }
  
  /**
   * Get the contact form schema
   *
   * @return array
   */
  public function get_endpoint_args_for_item_schema( $create = false ) {
    $args = array(
      'name' => array(
        'description' => __( 'Contact name', 'contact-form-rest-api' ),
        'type'        => 'string',
        'required'    => false, // Handle validation in our custom logic
        'sanitize_callback' => 'sanitize_text_field',
      ),
      'email' => array(
        'description' => __( 'Contact email', 'contact-form-rest-api' ),
        'type'        => 'string',
        'required'    => false, // Handle validation in our custom logic
        'sanitize_callback' => 'sanitize_email',
      ),
      'phone' => array(
        'description' => __( 'Contact phone number', 'contact-form-rest-api' ),
        'type'        => 'string',
        'required'    => false,
        'sanitize_callback' => 'sanitize_text_field',
      ),
      'subject' => array(
        'description' => __( 'Contact subject', 'contact-form-rest-api' ),
        'type'        => 'string',
        'required'    => false,
        'sanitize_callback' => 'sanitize_text_field',
      ),
      'message' => array(
        'description' => __( 'Contact message', 'contact-form-rest-api' ),
        'type'        => 'string',
        'required'    => false, // Handle validation in our custom logic
        'sanitize_callback' => 'sanitize_textarea_field',
      ),
    );
    
    return $args;
  }
}

// Initialize and register the REST controller
add_action( 'rest_api_init', function () {
  $controller = new Contact_Form_REST_Controller();
  $controller->register_routes();
} );