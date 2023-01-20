<?php

    /* ==================================================

    Portfolio Post Type Functions

    ================================================== */


    /* PORTFOLIO CATEGORY
    ================================================== */
    if ( !function_exists('sf_portfolio_category_register') ) {
        function sf_portfolio_category_register() {

            $portfolio_permalinks = get_option( 'sf_portfolio_permalinks' );

            $args = array(
                "label"             => __( 'Portfolio Categories', 'neighborhood' ),
                "singular_label"    => __( 'Portfolio Category', 'neighborhood' ),
                'public'            => true,
                'hierarchical'      => true,
                'show_ui'           => true,
                'show_in_nav_menus' => false,
                'args'              => array( 'orderby' => 'term_order' ),
                'rewrite'           => array(
                    'slug'       => empty( $portfolio_permalinks['category_base'] ) ? __( 'portfolio-category', 'neighborhood' ) : __( $portfolio_permalinks['category_base']  , 'neighborhood' ),
                    'with_front' => false
                ),
                'query_var'         => true
            );
            register_taxonomy( 'portfolio-category', 'portfolio', $args );
        }

        add_action( 'init', 'sf_portfolio_category_register' );
    }


    /* PORTFOLIO POST TYPE
    ================================================== */
    if ( !function_exists('sf_portfolio_register') ) {
        function sf_portfolio_register() {

            $portfolio_permalinks = get_option( 'sf_portfolio_permalinks' );
            $portfolio_permalink  = empty( $portfolio_permalinks['portfolio_base'] ) ? __( 'portfolio', 'neighborhood' ) : __( $portfolio_permalinks['portfolio_base'] , 'neighborhood' );

            $labels = array(
                'name'               => __( 'Portfolio', 'neighborhood' ),
                'singular_name'      => __( 'Portfolio Item', 'neighborhood' ),
                'add_new'            => __( 'Add New', 'neighborhood' ),
                'add_new_item'       => __( 'Add New Portfolio Item', 'neighborhood' ),
                'edit_item'          => __( 'Edit Portfolio Item', 'neighborhood' ),
                'new_item'           => __( 'New Portfolio Item', 'neighborhood' ),
                'view_item'          => __( 'View Portfolio Item', 'neighborhood' ),
                'search_items'       => __( 'Search Portfolio', 'neighborhood' ),
                'not_found'          => __( 'No portfolio items have been added yet', 'neighborhood' ),
                'not_found_in_trash' => __( 'Nothing found in Trash', 'neighborhood' ),
                'parent_item_colon'  => ''
            );

            $args = array(
                'labels'            => $labels,
                'public'            => true,
                'show_ui'           => true,
                'show_in_menu'      => true,
                'show_in_nav_menus' => true,
                'menu_icon'         => 'dashicons-format-image',
                'hierarchical'      => false,
                'rewrite'           => $portfolio_permalink != "portfolio" ? array(
                    'slug'       => untrailingslashit( $portfolio_permalink ),
                    'with_front' => false,
                    'feeds'      => true
                )
                    : false,
                'supports'          => array( 'title', 'editor', 'thumbnail', 'custom-fields', 'excerpt', 'revisions' ),
                'has_archive'       => true,
                'taxonomies'        => array( 'portfolio-category' )
            );

            register_post_type( 'portfolio', $args );
        }

        add_action( 'init', 'sf_portfolio_register' );
    }


    /* PORTFOLIO POST TYPE COLUMNS
    ================================================== */
    if ( !function_exists('sf_portfolio_edit_columns') ) {
        function sf_portfolio_edit_columns( $columns ) {
            $columns = array(
                "cb"                 => "<input type=\"checkbox\" />",
                "thumbnail"          => "",
                "title"              => __( "Portfolio Item", 'neighborhood' ),
                "description"        => __( "Description", 'neighborhood' ),
                "portfolio-category" => __( "Categories", 'neighborhood' )
            );

            return $columns;
        }
        add_filter( "manage_edit-portfolio_columns", "sf_portfolio_edit_columns" );
    }

?>