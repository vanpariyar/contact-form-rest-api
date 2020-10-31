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
            'name'                => _x( 'Contacts', 'Post Type General Name', 'ContactFormRestApi' ),
            'singular_name'       => _x( 'Contact', 'Post Type Singular Name', 'ContactFormRestApi' ),
            'menu_name'           => __( 'Contacts', 'ContactFormRestApi' ),
            'parent_item_colon'   => __( 'Parent Contact', 'ContactFormRestApi' ),
            'all_items'           => __( 'All Contacts', 'ContactFormRestApi' ),
            'view_item'           => __( 'View Contact', 'ContactFormRestApi' ),
            'add_new_item'        => __( 'Add New Contact', 'ContactFormRestApi' ),
            'add_new'             => __( 'Add New', 'ContactFormRestApi' ),
            'edit_item'           => __( 'Edit Contact', 'ContactFormRestApi' ),
            'update_item'         => __( 'Update Contact', 'ContactFormRestApi' ),
            'search_items'        => __( 'Search Contact', 'ContactFormRestApi' ),
            'not_found'           => __( 'Not Found', 'ContactFormRestApi' ),
            'not_found_in_trash'  => __( 'Not found in Trash', 'ContactFormRestApi' ),
        );
         
        $expense_args = array(
            'label'               => __( 'Contacts', 'ContactFormRestApi' ),
            'description'         => __( '', 'ContactFormRestApi' ),
            'labels'              => $expense_labels,        
            'supports'            => array( 'title', 'excerpt', 'author', 'thumbnail', 'comments', 'custom-fields', ),        
            'taxonomies'          => array( 'expense-category' ),        
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
            'name'              => _x( 'Contact Categories', 'taxonomy general name', 'ContactFormRestApi' ),
            'singular_name'     => _x( 'Contact Category', 'taxonomy singular name', 'ContactFormRestApi' ),
            'search_items'      => __( 'Search Contact Categories', 'ContactFormRestApi' ),
            'all_items'         => __( 'All Contact Categories', 'ContactFormRestApi' ),
            'parent_item'       => __( 'Parent Contact Category', 'ContactFormRestApi' ),
            'parent_item_colon' => __( 'Parent Contact Category:', 'ContactFormRestApi' ),
            'edit_item'         => __( 'Edit Contact Category', 'ContactFormRestApi' ),
            'update_item'       => __( 'Update Contact Category', 'ContactFormRestApi' ),
            'add_new_item'      => __( 'Add New Contact Category', 'ContactFormRestApi' ),
            'new_item_name'     => __( 'New Contact Category Name', 'ContactFormRestApi' ),
            'menu_name'         => __( 'Contact Category', 'ContactFormRestApi' ),
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
        // End expense category register
    
    
        /**
         *  Customize Admin Coloumn
         */
        //add_filter( 'manage_contact_posts_columns', 'WPETA_customize_coloumns' );
        function WPETA_customize_coloumns( $columns ) {
        $columns['amount'] = __( 'Amount', 'ContactFormRestApi' );
        return $columns;
        }
    
        /**
         * Manage custom coloumn
         */
        // add_action( 'manage_contact_posts_custom_column', 'WPETA_transaction_coloumn', 10, 2);
        function WPETA_transaction_coloumn( $column, $post_id ) {
        // Image column
            switch($column){
                case 'amount':
                    echo(get_post_meta($post_id, 'amount', true));
                break;
            }
        }
    }
}
