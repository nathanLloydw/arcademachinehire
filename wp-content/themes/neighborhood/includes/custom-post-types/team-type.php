<?php

    /* ==================================================

    Team Post Type Functions

    ================================================== */


    /* TEAM CATEGORY
    ================================================== */
    if ( !function_exists('sf_team_category_register') ) {
        function sf_team_category_register() {

            $team_permalinks = get_option( 'sf_team_permalinks' );

            $args = array(
                "label"             => __( 'Team Categories', 'neighborhood' ),
                "singular_label"    => __( 'Team Category', 'neighborhood' ),
                'public'            => true,
                'hierarchical'      => true,
                'show_ui'           => true,
                'show_in_nav_menus' => false,
                'args'              => array( 'orderby' => 'term_order' ),
                'rewrite'           => array(
                    'slug'       => empty( $team_permalinks['category_base'] ) ? __( 'team-category', 'neighborhood' ) : __( $team_permalinks['category_base']  , 'neighborhood' ),
                    'with_front' => false
                ),
                'query_var'         => true
            );

            register_taxonomy( 'team-category', 'team', $args );
        }
        add_action( 'init', 'sf_team_category_register' );
    }


    /* TEAM POST TYPE
    ================================================== */
    if ( !function_exists('sf_team_register') ) {
        function sf_team_register() {

            $team_permalinks = get_option( 'sf_team_permalinks' );
            $team_permalink  = empty( $team_permalinks['team_base'] ) ? __( 'team', 'neighborhood' ) : __( $team_permalinks['team_base']  , 'neighborhood' );

            $labels = array(
                'name'               => __( 'Team', 'neighborhood' ),
                'singular_name'      => __( 'Team Member', 'neighborhood' ),
                'add_new'            => __( 'Add New', 'neighborhood' ),
                'add_new_item'       => __( 'Add New Team Member', 'neighborhood' ),
                'edit_item'          => __( 'Edit Team Member', 'neighborhood' ),
                'new_item'           => __( 'New Team Member', 'neighborhood' ),
                'view_item'          => __( 'View Team Member', 'neighborhood' ),
                'search_items'       => __( 'Search Team Members', 'neighborhood' ),
                'not_found'          => __( 'No team members have been added yet', 'neighborhood' ),
                'not_found_in_trash' => __( 'Nothing found in Trash', 'neighborhood' ),
                'parent_item_colon'  => ''
            );

            $args = array(
                'labels'            => $labels,
                'public'            => true,
                'show_ui'           => true,
                'show_in_menu'      => true,
                'show_in_nav_menus' => true,
                'menu_icon'         => 'dashicons-groups',
                'rewrite'           => $team_permalink != "team" ? array(
                    'slug'       => untrailingslashit( $team_permalink ),
                    'with_front' => false,
                    'feeds'      => true
                )
                    : false,
                'supports'          => array( 'title', 'editor', 'thumbnail', 'custom-fields', 'excerpt' ),
                'has_archive'       => true,
                'taxonomies'        => array( 'team-category', 'post_tag' )
            );

            register_post_type( 'team', $args );
        }
        add_action( 'init', 'sf_team_register' );
    }


    /* TEAM POST TYPE COLUMNS
    ================================================== */
    if ( !function_exists('sf_team_edit_columns') ) {
        function sf_team_edit_columns( $columns ) {
            $columns = array(
                "cb"            => "<input type=\"checkbox\" />",
                "thumbnail"     => "",
                "title"         => __( "Team Member", 'neighborhood' ),
                "description"   => __( "Description", 'neighborhood' ),
                "team-category" => __( "Categories", 'neighborhood' )
            );

            return $columns;
        }
        add_filter( "manage_edit-team_columns", "sf_team_edit_columns" );
    }

?>