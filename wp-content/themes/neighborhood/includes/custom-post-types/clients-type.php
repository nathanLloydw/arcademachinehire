<?php

    /* ==================================================

    Clients Post Type Functions

    ================================================== */


    /* CLIENTS CATEGORY
    ================================================== */
    if ( !function_exists('sf_clients_category_register') ) {
        function sf_clients_category_register() {
            $args = array(
                "label"             => __( 'Client Categories', 'neighborhood' ),
                "singular_label"    => __( 'Client Category', 'neighborhood' ),
                'public'            => true,
                'hierarchical'      => true,
                'show_ui'           => true,
                'show_in_nav_menus' => false,
                'args'              => array( 'orderby' => 'term_order' ),
                'rewrite'           => false,
                'query_var'         => true
            );

            register_taxonomy( 'clients-category', 'clients', $args );
        }
        add_action( 'init', 'sf_clients_category_register' );
    }


    /* CLIENTS POST TYPE
    ================================================== */
    if ( !function_exists('sf_clients_register') ) {
        function sf_clients_register() {

            $labels = array(
                'name'               => __( 'Clients', 'neighborhood' ),
                'singular_name'      => __( 'Client', 'neighborhood' ),
                'add_new'            => __( 'Add New', 'neighborhood' ),
                'add_new_item'       => __( 'Add New Client', 'neighborhood' ),
                'edit_item'          => __( 'Edit Client', 'neighborhood' ),
                'new_item'           => __( 'New Client', 'neighborhood' ),
                'view_item'          => __( 'View Client', 'neighborhood' ),
                'search_items'       => __( 'Search Clients', 'neighborhood' ),
                'not_found'          => __( 'No clients have been added yet', 'neighborhood' ),
                'not_found_in_trash' => __( 'Nothing found in Trash', 'neighborhood' ),
                'parent_item_colon'  => ''
            );

            $args = array(
                'labels'            => $labels,
                'public'            => true,
                'show_ui'           => true,
                'show_in_menu'      => true,
                'show_in_nav_menus' => false,
                'menu_icon'         => 'dashicons-businessman',
                'rewrite'           => false,
                'supports'          => array( 'title', 'thumbnail', 'excerpt', 'custom-fields', 'excerpt' ),
                'has_archive'       => true,
                'taxonomies'        => array( 'clients-category', 'post_tag' )
            );

            register_post_type( 'clients', $args );
        }
        add_action( 'init', 'sf_clients_register' );
    }


    /* CLIENTS POST TYPE COLUMNS
    ================================================== */
    if ( !function_exists('sf_clients_edit_columns') ) {
        function sf_clients_edit_columns( $columns ) {
            $columns = array(
                "cb"               => "<input type=\"checkbox\" />",
                "thumbnail"        => "",
                "title"            => __( "Client", 'neighborhood' ),
                "clients-category" => __( "Categories", 'neighborhood' )
            );

            return $columns;
        }
        add_filter( "manage_edit-clients_columns", "sf_clients_edit_columns" );
    }
?>