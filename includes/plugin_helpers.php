<?php 
/**
 * @package ContactFormRestApi
 * @desc Use to register Activation and Deactivation Hooks.
 * 
 */

require_once( CONTACT_FORM_REST_API_PLUGIN_DIR . 'admin/customiser.php');

class Contact_Form_Rest_Api_Helpers {

    /**
     * Activation Hook.
     */
    public static function activation_hook(){
        // Register custom post type and taxonomy
        Wp_Contact_Form_Rest_Api_Admin_customiser::customizer();
        
        /**
         * Flush Rewrite Rules after register.
         */
        flush_rewrite_rules();
    }

    /**
     * Deactivation Hook.
     */
    public static function deactivation_hook(){
        // Unregister post type
        unregister_post_type( 'contact' );
        
        /**
         * Flush Rewrite Rules after unregister.
         */
        flush_rewrite_rules();
    }
}
