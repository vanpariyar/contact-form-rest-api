<?php 
/**
 * @package ContactFormRestApi
 * @desc Use to register Activation and Deactivaion Hooks.
 * 
 */

require_once( CONTACT_FORM_REST_API_PLUGIN_DIR . 'admin/customiser.php');


class Wp_Contact_Form_Rest_Api {

    function __construct(){
        add_action( 'init', array( 'Wp_Contact_Form_Rest_Api_Admin_customiser','customizer' ));
    }
    /**
     * Activation Hook.
     */
    public static function activation_hook(){
        add_action( 'init', array( 'Wp_Contact_Form_Rest_Api_Admin_customiser','customizer' ));
        /**
         * Flush Rewrite Rules after register.
         */
        flush_rewrite_rules( );
    }


    /**
     * Deactivation Hook.
     */
    public static function deactivation_hook(){
        unregister_post_type( 'contact' );
        /**
         * Flush Rewrite Rules after register.
         */
        flush_rewrite_rules( );
        
    }

}
