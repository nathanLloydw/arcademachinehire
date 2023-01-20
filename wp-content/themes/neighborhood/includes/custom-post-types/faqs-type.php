<?php

	/* ==================================================

	FAQs Post Type Functions

	================================================== */

	/* FAQS CATEGORY
	================================================== */
    if ( !function_exists('sf_faqs_category_register') ) {
    	function sf_faqs_category_register() {

    		$faqs_permalinks = get_option( 'sf_faqs_permalinks' );

    	    $args = array(
    	        "label" 						=> __('Topics', 'neighborhood'),
    	        "singular_label" 				=> __('Topic', 'neighborhood'),
    	        'public'                        => true,
    	        'hierarchical'                  => true,
    	        'show_ui'                       => true,
    	        'show_in_nav_menus'             => false,
    	        'args'                          => array( 'orderby' => 'term_order' ),
    	        'rewrite'           => array(
                    'slug'       => empty( $faqs_permalinks['category_base'] ) ? __( 'faqs-category', 'neighborhood' ) : __( $faqs_permalinks['category_base']  , 'neighborhood' ),
                    'with_front' => false
                ),
                'query_var'         => true
    	    );

    	    register_taxonomy( 'faqs-category', 'faqs', $args );
    	}
    	add_action( 'init', 'sf_faqs_category_register' );
    }


	/* FAQS POST TYPE
    ================================================== */
    if ( !function_exists('sf_faqs_register') ) {
        function sf_faqs_register() {

    		$faqs_permalinks = get_option( 'sf_faqs_permalinks' );
            $faqs_permalink  = empty( $faqs_permalinks['faqs_base'] ) ? __( 'faqs', 'neighborhood' ) : __( $faqs_permalinks['faqs_base'] , 'neighborhood' );

            $labels = array(
                'name' => __('FAQs', 'neighborhood'),
                'singular_name' => __('Question', 'neighborhood'),
                'add_new' => __('Add New', 'neighborhood'),
                'add_new_item' => __('Add New Question', 'neighborhood'),
                'edit_item' => __('Edit Question', 'neighborhood'),
                'new_item' => __('New Question', 'neighborhood'),
                'view_item' => __('View Question', 'neighborhood'),
                'search_items' => __('Search Questions', 'neighborhood'),
                'not_found' =>  __('No questions have been added yet', 'neighborhood'),
                'not_found_in_trash' => __('Nothing found in Trash', 'neighborhood'),
                'parent_item_colon' => ''
            );

            $args = array(
                'labels'            => $labels,
                'public'            => true,
                'show_ui'           => true,
                'show_in_menu'      => true,
                'show_in_nav_menus' => true,
                'menu_icon'=> 'dashicons-editor-help',
                'rewrite'           => $faqs_permalink != "faqs" ? array(
                    'slug'       => untrailingslashit( $faqs_permalink ),
                    'with_front' => false,
                    'feeds'      => true
                )
                    : false,
                'supports' => array('title', 'editor'),
                'has_archive' => true,
                'taxonomies' => array('faqs-category', 'post_tag')
            );

            register_post_type( 'faqs', $args );
        }
        add_action( 'init', 'sf_faqs_register' );
    }


	/* FAQS POST TYPE COLUMNS
	================================================== */
    if ( !function_exists('faqs_edit_columns') ) {
    	function faqs_edit_columns($columns){
            $columns = array(
                "cb" => "<input type=\"checkbox\" />",
                "title" => __("Question", 'neighborhood'),
                "description" => __("Answer", 'neighborhood'),
                "faqs-category" => __("Topics", 'neighborhood')
            );

            return $columns;
    	}

    	add_filter("manage_edit-faqs_columns", "faqs_edit_columns");
    }
?>