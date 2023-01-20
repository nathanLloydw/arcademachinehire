<?php

    /* ==================================================

    Testimonials Post Type Functions

    ================================================== */


    /* TESTIMONIAL CATEGORY
    ================================================== */
    if ( !function_exists('sf_testimonial_category_register') ) {
        function sf_testimonial_category_register() {
            $args = array(
                "label"             => __( 'Testimonial Categories', 'neighborhood' ),
                "singular_label"    => __( 'Testimonial Category', 'neighborhood' ),
                'public'            => true,
                'hierarchical'      => true,
                'show_ui'           => true,
                'show_in_nav_menus' => false,
                'args'              => array( 'orderby' => 'term_order' ),
                'rewrite'           => false,
                'query_var'         => true
            );

            register_taxonomy( 'testimonials-category', 'testimonials', $args );
        }
        add_action( 'init', 'sf_testimonial_category_register' );
    }


    /* TESTIMONIAL POST TYPE
    ================================================== */
    if ( !function_exists('sf_testimonials_register') ) {
        function sf_testimonials_register() {

            $labels = array(
                'name'               => __( 'Testimonials', 'neighborhood' ),
                'singular_name'      => __( 'Testimonial', 'neighborhood' ),
                'add_new'            => __( 'Add New', 'neighborhood' ),
                'add_new_item'       => __( 'Add New Testimonial', 'neighborhood' ),
                'edit_item'          => __( 'Edit Testimonial', 'neighborhood' ),
                'new_item'           => __( 'New Testimonial', 'neighborhood' ),
                'view_item'          => __( 'View Testimonial', 'neighborhood' ),
                'search_items'       => __( 'Search Testimonials', 'neighborhood' ),
                'not_found'          => __( 'No testimonials have been added yet', 'neighborhood' ),
                'not_found_in_trash' => __( 'Nothing found in Trash', 'neighborhood' ),
                'parent_item_colon'  => ''
            );

            $args = array(
                'labels'            => $labels,
                'public'            => true,
                'show_ui'           => true,
                'show_in_menu'      => true,
                'show_in_nav_menus' => false,
                'menu_icon'         => 'dashicons-format-quote',
                'rewrite'           => false,
                'supports'          => array( 'title', 'editor', 'custom-fields', 'excerpt' ),
                'has_archive'       => true,
                'taxonomies'        => array( 'testimonials-category' )
            );

            register_post_type( 'testimonials', $args );
        }
        add_action( 'init', 'sf_testimonials_register' );
    }


    /* TESTIMONIAL POST TYPE COLUMNS
    ================================================== */
    if ( !function_exists('sf_testimonials_edit_columns') ) {
        function sf_testimonials_edit_columns( $columns ) {
            $columns = array(
                "cb"                    => "<input type=\"checkbox\" />",
                "title"                 => __( "Testimonial", 'neighborhood' ),
                "testimonials-category" => __( "Categories", 'neighborhood' )
            );

            return $columns;
        }
        add_filter( "manage_edit-testimonials_columns", "sf_testimonials_edit_columns" );
    }
?>