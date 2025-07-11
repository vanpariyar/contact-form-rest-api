<?php
/**
 * Contact Form Controller
 * 
 * @package ContactFormRestApi
 */

/**
 * Contact form processing functions
 */
class Contact_Form_Controller {
    
    /**
     * Process contact form submission
     *
     * @param array $data Contact form data
     * @return array|WP_Error Result or error
     */
    public static function process_contact_submission( $data ) {
        // Validate required fields
        $required_fields = array( 'name', 'email', 'message' );
        foreach ( $required_fields as $field ) {
            if ( empty( $data[ $field ] ) ) {
                return new WP_Error( 
                    'missing_field', 
                    sprintf( __( 'Missing required field: %s', 'contact-form-rest-api' ), $field )
                );
            }
        }
        
        // Validate email
        if ( ! is_email( $data['email'] ) ) {
            return new WP_Error( 
                'invalid_email', 
                __( 'Invalid email address', 'contact-form-rest-api' )
            );
        }
        
        // Sanitize data
        $sanitized_data = array(
            'name'    => sanitize_text_field( $data['name'] ),
            'email'   => sanitize_email( $data['email'] ),
            'phone'   => isset( $data['phone'] ) ? sanitize_text_field( $data['phone'] ) : '',
            'subject' => isset( $data['subject'] ) ? sanitize_text_field( $data['subject'] ) : '',
            'message' => sanitize_textarea_field( $data['message'] ),
        );
        
        // Create contact post
        $post_data = array(
            'post_title'    => $sanitized_data['name'],
            'post_content'  => $sanitized_data['message'],
            'post_status'   => 'publish',
            'post_type'     => 'contact',
            'post_author'   => 1,
        );
        
        $post_id = wp_insert_post( $post_data );
        
        if ( is_wp_error( $post_id ) ) {
            return $post_id;
        }
        
        // Save meta data
        update_post_meta( $post_id, 'contact_email', $sanitized_data['email'] );
        update_post_meta( $post_id, 'contact_phone', $sanitized_data['phone'] );
        update_post_meta( $post_id, 'contact_subject', $sanitized_data['subject'] );
        update_post_meta( $post_id, 'submission_date', current_time( 'mysql' ) );
        
        // Send notification email
        self::send_notification_email( $sanitized_data, $post_id );
        
        return array(
            'id'      => $post_id,
            'message' => __( 'Contact form submitted successfully', 'contact-form-rest-api' ),
            'status'  => 'success'
        );
    }
    
    /**
     * Send notification email to admin
     *
     * @param array $data Contact form data
     * @param int $post_id Post ID
     */
    private static function send_notification_email( $data, $post_id ) {
        $admin_email = get_option( 'admin_email' );
        $site_name = get_bloginfo( 'name' );
        
        $subject = sprintf( 
            __( 'New contact form submission from %s', 'contact-form-rest-api' ), 
            $data['name'] 
        );
        
        $message = sprintf(
            __( 'You have received a new contact form submission:

Name: %s
Email: %s
Phone: %s
Subject: %s
Message: %s

View submission: %s', 'contact-form-rest-api' ),
            $data['name'],
            $data['email'],
            $data['phone'] ?: 'N/A',
            $data['subject'] ?: 'N/A',
            $data['message'],
            admin_url( 'post.php?post=' . $post_id . '&action=edit' )
        );
        
        wp_mail( $admin_email, $subject, $message );
    }
    
    /**
     * Get contact submissions
     *
     * @param array $args Query arguments
     * @return array Contact submissions
     */
    public static function get_contact_submissions( $args = array() ) {
        $defaults = array(
            'post_type'      => 'contact',
            'post_status'    => 'publish',
            'posts_per_page' => 10,
            'paged'          => 1,
            'orderby'        => 'date',
            'order'          => 'DESC',
        );
        
        $args = wp_parse_args( $args, $defaults );
        $query = new WP_Query( $args );
        $submissions = array();
        
        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                $submissions[] = self::prepare_submission_data( get_post() );
            }
        }
        
        wp_reset_postdata();
        
        return array(
            'submissions' => $submissions,
            'total'       => $query->found_posts,
            'pages'       => $query->max_num_pages,
        );
    }
    
    /**
     * Get single contact submission
     *
     * @param int $post_id Post ID
     * @return array|WP_Error Submission data or error
     */
    public static function get_contact_submission( $post_id ) {
        $post = get_post( $post_id );
        
        if ( ! $post || $post->post_type !== 'contact' ) {
            return new WP_Error( 
                'not_found', 
                __( 'Contact submission not found', 'contact-form-rest-api' )
            );
        }
        
        return self::prepare_submission_data( $post );
    }
    
    /**
     * Prepare submission data for response
     *
     * @param WP_Post $post Contact submission post
     * @return array Prepared data
     */
    private static function prepare_submission_data( $post ) {
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
}