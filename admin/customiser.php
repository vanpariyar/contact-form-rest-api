<?php
/**
 * Customize Admin For the Contact Request
 * @package ContactFormRestApi
 * @subpackage Customizer.
 * @desc To Customize Admin Experience.
 * 
 */

class Wp_Contact_Form_Rest_Api_Admin_customiser {

    public static function customizer(){
        /* Contacts custom post type - Start */
        $expense_labels = array(
            'name'                => _x( 'Contacts', 'Post Type General Name', 'contact-form-rest-api' ),
            'singular_name'       => _x( 'Contact', 'Post Type Singular Name', 'contact-form-rest-api' ),
            'menu_name'           => __( 'Contacts', 'contact-form-rest-api' ),
            'parent_item_colon'   => __( 'Parent Contact', 'contact-form-rest-api' ),
            'all_items'           => __( 'All Contacts', 'contact-form-rest-api' ),
            'view_item'           => __( 'View Contact', 'contact-form-rest-api' ),
            'add_new_item'        => __( 'Add New Contact', 'contact-form-rest-api' ),
            'add_new'             => __( 'Add New', 'contact-form-rest-api' ),
            'edit_item'           => __( 'Edit Contact', 'contact-form-rest-api' ),
            'update_item'         => __( 'Update Contact', 'contact-form-rest-api' ),
            'search_items'        => __( 'Search Contact', 'contact-form-rest-api' ),
            'not_found'           => __( 'Not Found', 'contact-form-rest-api' ),
            'not_found_in_trash'  => __( 'Not found in Trash', 'contact-form-rest-api' ),
        );
         
        $expense_args = array(
            'label'               => __( 'Contacts', 'contact-form-rest-api' ),
            'description'         => __( 'Contact form submissions', 'contact-form-rest-api' ),
            'labels'              => $expense_labels,        
            'supports'            => array( 'title', 'excerpt', 'author', 'thumbnail', 'comments', 'custom-fields','editor' ),        
            'taxonomies'          => array( 'contact-category' ),        
            'hierarchical'        => false,
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_nav_menus'   => true,
            'show_in_admin_bar'   => true,
            'menu_icon'           => 'dashicons-groups',
            'menu_position'       => 5,
            'can_export'          => true,
            'has_archive'         => true,
            'exclude_from_search' => false,
            'publicly_queryable'  => true,
            'capability_type'     => 'post',
            'show_in_rest'        => true,
        );
        register_post_type( 'contact', $expense_args );        
        
        /* Contacts custom post type - End */
    
        // Register Contact category Start
        $labels = array(
            'name'              => _x( 'Contact Categories', 'taxonomy general name', 'contact-form-rest-api' ),
            'singular_name'     => _x( 'Contact Category', 'taxonomy singular name', 'contact-form-rest-api' ),
            'search_items'      => __( 'Search Contact Categories', 'contact-form-rest-api' ),
            'all_items'         => __( 'All Contact Categories', 'contact-form-rest-api' ),
            'parent_item'       => __( 'Parent Contact Category', 'contact-form-rest-api' ),
            'parent_item_colon' => __( 'Parent Contact Category:', 'contact-form-rest-api' ),
            'edit_item'         => __( 'Edit Contact Category', 'contact-form-rest-api' ),
            'update_item'       => __( 'Update Contact Category', 'contact-form-rest-api' ),
            'add_new_item'      => __( 'Add New Contact Category', 'contact-form-rest-api' ),
            'new_item_name'     => __( 'New Contact Category Name', 'contact-form-rest-api' ),
            'menu_name'         => __( 'Contact Category', 'contact-form-rest-api' ),
        );
    
        $args = array(
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array( 'slug' => 'contact-category', 'with_front' => false),
            'show_in_rest'       => true,
            'publicly_queryable' => true,
    
        );
        register_taxonomy( 'contact-category', array( 'contact' ), $args );
        // End contact category register
    
    
        /**
         *  Customize Admin Columns
         */
        //add_filter( 'manage_contact_posts_columns', 'WPETA_customize_columns' );
        function WPETA_customize_columns( $columns ) {
        $columns['email'] = __( 'Email', 'contact-form-rest-api' );
        return $columns;
        }
    
        /**
         * Manage custom column
         */
        // add_action( 'manage_contact_posts_custom_column', 'WPETA_transaction_column', 10, 2);
        function WPETA_transaction_column( $column, $post_id ) {
        // Email column
            switch($column){
                case 'email':
                    echo(get_post_meta($post_id, 'contact_email', true));
                break;
            }
        }
    }
}
