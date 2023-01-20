<?php

/* ==================================================

Jobs Post Type Functions

================================================== */
    
    
$args = array(
    "label" 						=> __("Job Categories", 'neighborhood'), 
    "singular_label" 				=> __("Job Category", 'neighborhood'), 
    'public'                        => true,
    'hierarchical'                  => true,
    'show_ui'                       => true,
    'show_in_nav_menus'             => false,
    'args'                          => array( 'orderby' => 'term_order' ),
    'rewrite'                       => false,
    'query_var'                     => true
);

register_taxonomy( 'jobs-category', 'jobs', $args );


add_action('init', 'jobs_register');  
  
function jobs_register() {  

    $labels = array(
        'name' => _x('Jobs', 'post type general name', 'neighborhood'),
        'singular_name' => _x('Job', 'post type singular name', 'neighborhood'),
        'add_new' => _x('Add New', 'job', 'neighborhood'),
        'add_new_item' => __('Add New Job', 'neighborhood'),
        'edit_item' => __('Edit Job', 'neighborhood'),
        'new_item' => __('New Job', 'neighborhood'),
        'view_item' => __('View Job', 'neighborhood'),
        'search_items' => __('Search Jobs', 'neighborhood'),
        'not_found' =>  __('No jobs have been added yet', 'neighborhood'),
        'not_found_in_trash' => __('Nothing found in Trash', 'neighborhood'),
        'parent_item_colon' => ''
    );

    $args = array(  
        'labels' => $labels,  
        'public' => true,  
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_nav_menus' => false,
        'menu_icon'=> 'dashicons-hammer',
        'rewrite' => false,
        'supports' => array('title', 'editor'),
        'has_archive' => true,
        'taxonomies' => array('jobs-category', 'post_tag')
       );  
  
    register_post_type( 'jobs' , $args );  
}  

add_filter("manage_edit-jobs_columns", "jobs_edit_columns");   
  
function jobs_edit_columns($columns){  
        $columns = array(  
            "cb" => "<input type=\"checkbox\" />",  
            "title" => "Job",
            "description" => "Description",
            "jobs-category" => "Categories"  
        );  
  
        return $columns;  
}  

add_action("manage_posts_custom_column",  "jobs_custom_columns"); 
  
function jobs_custom_columns($column){  
        global $post;  
        switch ($column)  
        {  
            case "description":  
                break;
            case "jobs-category":
                echo get_the_term_list($post->ID, 'jobs-category', '', ', ','');
                break;
        }  
}  

?>