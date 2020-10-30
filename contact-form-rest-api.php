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

// require_once( CONTACT_FORM_REST_API_PLUGIN_DIR . 'includes/plugin_helpers.php');

/**
 *  TODO :: Remove the WP-basic Auth Plugin From Code.
 * 
 */
require_once( CONTACT_FORM_REST_API_PLUGIN_DIR . 'api/basic-auth.php');


require_once( CONTACT_FORM_REST_API_PLUGIN_DIR . 'api/routes/contact.php');
