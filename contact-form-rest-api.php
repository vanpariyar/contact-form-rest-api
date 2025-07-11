<?php
/**
 * Contact Form Rest Api
 *
 * @package           ContactFormRestApi
 * @author            Ronak Vanpariya
 * @copyright         2020 Ronak Vanpariya
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Contact Form Rest Api
 * Plugin URI:        https://github.com/vanpariyar/contact-form-rest-api
 * Description:       This is the Contact Form Rest Api that allows us the send the contact form through rest Api.
 * Version:           1.0.0
 * Requires at least: 5.5
 * Requires PHP:      7.2
 * Author:            Ronak Vanpariya
 * Author URI:        https://github.com/vanpariyar/
 * Text Domain:       contact-form-rest-api
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'CONTACT_FORM_REST_API_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'CONTACT_FORM_REST_API_PLUGIN_URL', plugins_url( '', __FILE__ ) );

/**
 * Plugin Activation and Deactivation Hooks
 */
require_once( CONTACT_FORM_REST_API_PLUGIN_DIR . 'includes/plugin_helpers.php');
register_activation_hook( __FILE__ , array( 'Contact_Form_Rest_Api_Helpers', 'activation_hook' ) );
register_deactivation_hook( __FILE__ , array( 'Contact_Form_Rest_Api_Helpers', 'deactivation_hook' ) );

/**
 * Include required files
 */
require_once CONTACT_FORM_REST_API_PLUGIN_DIR . 'api/controllers/contact.php';

/**
 * Basic Auth for REST API (optional - can be removed if not needed)
 */
require_once CONTACT_FORM_REST_API_PLUGIN_DIR . 'api/basic-auth.php';

/**
 * Initialize the plugin
 */
function contact_form_rest_api_init() {
    // Initialize admin customizer
    if ( is_admin() ) {
        add_action( 'init', array( 'Wp_Contact_Form_Rest_Api_Admin_customiser', 'customizer' ) );
    }
    
    // Include routes only after WordPress is fully loaded
    if ( class_exists( 'WP_REST_Controller' ) ) {
        require_once CONTACT_FORM_REST_API_PLUGIN_DIR . 'api/routes/contact.php';
    }
}
add_action( 'init', 'contact_form_rest_api_init' );

/**
 * Plugin main class
 */
class Wp_Contact_Form_Rest_Api {
    
    public function __construct() {
        // Plugin initialization
        add_action( 'init', array( $this, 'init' ) );
    }
    
    public function init() {
        // Load text domain for translations
        load_plugin_textdomain( 'contact-form-rest-api', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
    }
}

// Initialize the plugin
$wp_contact_form_rest_api = new Wp_Contact_Form_Rest_Api();