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

define( 'CONTACT_FORM_REST_API_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'CONTACT_FORM_REST_API_PLUGIN_URL', plugins_url( __FILE__ ) );

/**
 * Plugin Activation and Deactivation Hooks
 */

require_once( CONTACT_FORM_REST_API_PLUGIN_DIR . 'includes/plugin_helpers.php');
register_activation_hook( __FILE__ , array( 'Wp_Contact_Form_Rest_Api', 'activation_hook' ) );
register_deactivation_hook( __FILE__ , array( 'Wp_Contact_Form_Rest_Api', 'deactivation_hook' ) );

/**
 *  TODO :: Remove the WP-basic Auth Plugin From Code.
 * 
 */
require_once CONTACT_FORM_REST_API_PLUGIN_DIR . 'api/basic-auth.php';

/**
 * Registred Routes in the plugin
 */
require_once CONTACT_FORM_REST_API_PLUGIN_DIR . 'api/routes/contact.php';

/**
 * Finally Jump Start the Plugin.
 */
$wp_rest_api_init = new Wp_Contact_Form_Rest_Api();