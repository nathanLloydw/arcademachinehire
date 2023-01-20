<?php

class SwiftPageBuilderShortcode_clients extends SwiftPageBuilderShortcode {

    protected function content($atts, $content = null) {

		   	$title = $width = $el_class = $output = $tax_terms = $filter = $items = $el_position = '';
		
	        extract(shortcode_atts(array(
	        	'title' => '',
	        	'item_count'	=> '-1',
	        	'category'		=> '',
	        	'random'		=> '',
	        	'pagination'	=> 'no',
	        	'el_position' => '',
	        	'width' => '1/1',
	        	'el_class' => ''
	        ), $atts));
	        
	        // CATEGORY SLUG MODIFICATION
	        if ($category == "All") {$category = "all";}
		    if ($category == "all") {$category = '';}
		    $category_slug = str_replace('_', '-', $category);
		    
		    // CLIENTS QUERY SETUP
    		
    		global $post, $wp_query;
    		
    		$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    		
    		$orderby = 'date';
    		
    		if ( $random == "yes" ) {
    			$orderby = 'rand';
    		}
    		    		
    		$client_args=array(
	    		'post_type' => 'clients',
	    		'post_status' => 'publish',
	    		'paged' => $paged,
	    		'clients-category' => $category_slug,
	    		'posts_per_page' => $item_count,
	    		'orderby' => $orderby
       		);
       		    		
    		$clients_items = new WP_Query( $client_args );
    					
			$items .= '<ul class="clients-items clearfix">';
			
			$client_width = 124;
			$client_height = 124;
			
			// CLIENTS LOOP
			
			while ( $clients_items->have_posts() ) : $clients_items->the_post();
			
				$client_image = get_post_thumbnail_id();
				$client_img_url = wp_get_attachment_url( $client_image, 'full' );
				$client_link_url = sf_get_post_meta($post->ID, 'sf_client_link', true);
				    				
				$items .= '<li class="clearfix client-item">';
				    				
				$items .= '<figure>';
				
                $image = '';
                if (function_exists('aq_resize')) {
				    $image = aq_resize( $client_img_url, $client_width, $client_height, true, false);
				}

				if($image) {
				
					if ($client_link_url) {
					$items .= '<a href="'.$client_link_url.'" target="_blank"><img src="'.$image[0].'" width="'.$image[1].'" height="'.$image[2].'" /></a>';
					} else {
					$items .= '<img itemprop="image" src="'.$image[0].'" width="'.$image[1].'" height="'.$image[2].'" />';
					}
					
				}
				
				$items .= '</figure>';
							
			endwhile;
			
			wp_reset_postdata();
					
			$items .= '</ul>';
			
			
			// PAGINATION
			
			if ($pagination == "yes") {
			
				$items .= '<div class="pagination-wrap">';
				
				$items .= pagenavi($clients_items);
									
				$items .= '</div>';
			
			}
			
			// PAGE BUILDER OUPUT
    		
    		$el_class = $this->getExtraClass($el_class);
            $width = spb_translateColumnWidthToSpan($width);
            
            $output .= "\n\t".'<div class="spb_clients_widget spb_content_element '.$width.$el_class.'">';
            $output .= "\n\t\t".'<div class="spb_wrapper clients-wrap">';
            $output .= ($title != '' ) ? "\n\t\t\t".'<h4 class="spb_heading spb_text_heading"><span>'.$title.'</span></h4>' : '';
            $output .= "\n\t\t\t\t".$items;
            $output .= "\n\t\t".'</div> '.$this->endBlockComment('.spb_wrapper');
            $output .= "\n\t".'</div> '.$this->endBlockComment($width);
    
            $output = $this->startRow($el_position) . $output . $this->endRow($el_position);
            return $output;
		
    }
}

SPBMap::map( 'clients', array(
    "name"		=> __("Clients", 'neighborhood'),
    "base"		=> "clients",
    "class"		=> "clients",
    "icon"      => "spb-icon-clients",
    "params"	=> array(
    	array(
    	    "type" => "textfield",
    	    "heading" => __("Widget title", 'neighborhood'),
    	    "param_name" => "title",
    	    "value" => "",
    	    "description" => __("Heading text. Leave it empty if not needed.", 'neighborhood')
    	),
        array(
            "type" => "textfield",
            "class" => "",
            "heading" => __("Number of items", 'neighborhood'),
            "param_name" => "item_count",
            "value" => "12",
            "description" => __("The number of clients to show per page. Leave blank to show ALL clients.", 'neighborhood')
        ),
        array(
            "type" => "select-multiple",
            "heading" => __("Clients category", 'neighborhood'),
            "param_name" => "category",
            "value" => get_category_list('clients-category'),
            "description" => __("Choose the category for the client items.", 'neighborhood')
        ),
        array(
            "type" => "dropdown",
            "heading" => __("Random Order", 'neighborhood'),
            "param_name" => "random",
            "value" => array(__('No', 'neighborhood') => "no", __('Yes', 'neighborhood') => "yes"),
            "description" => __("Show clients pagination.", 'neighborhood')
        ),
        array(
            "type" => "dropdown",
            "heading" => __("Pagination", 'neighborhood'),
            "param_name" => "pagination",
            "value" => array(__('No', 'neighborhood') => "no", __('Yes', 'neighborhood') => "yes"),
            "description" => __("Show clients pagination.", 'neighborhood')
        ),
        array(
            "type" => "textfield",
            "heading" => __("Extra class name", 'neighborhood'),
            "param_name" => "el_class",
            "value" => "",
            "description" => __("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", 'neighborhood')
        )
    )
) );


class SwiftPageBuilderShortcode_clients_featured extends SwiftPageBuilderShortcode {

    protected function content($atts, $content = null) {

		   	$title = $width = $el_class = $output = $wrap_span = $tax_terms = $filter = $items = $el_position = '';
		
	        extract(shortcode_atts(array(
	        	'title' => '',
	        	'category'		=> '',
	        	'alt_background'	=> 'none',
	        	'el_position' => '',
	        	'width' => '1/1',
	        	'el_class' => ''
	        ), $atts));
	        
	        // CATEGORY SLUG MODIFICATION
	        if ($category == "All") {$category = "all";}
		    if ($category == "all") {$category = '';}
		    $category_slug = str_replace('_', '-', $category);
		    
		    // CLIENTS QUERY SETUP
    		
    		global $post, $wp_query;
    		
    		$sidebar_config = sf_get_post_meta(get_the_ID(), 'sf_sidebar_config', true);
    		
    		$sidebars = '';
    		if (($sidebar_config == "left-sidebar") || ($sidebar_config == "right-sidebar")) {
    		$wrap_span = "span6";
    		$sidebars = 'one-sidebar';
    		} else if ($sidebar_config == "both-sidebars") {
    		$wrap_span = "span4";
    		$sidebars = 'both-sidebars';
    		} else {
    		$wrap_span = "span10";
    		$sidebars = 'no-sidebars';
    		}
       		    		    		
    		$client_args=array(
	    		'post_type' => 'clients',
	    		'post_status' => 'publish',
	    		'clients-category' => $category_slug,
	    		'posts_per_page' => 5
       		);
       		    		
    		$clients_items = new WP_Query( $client_args );
    		
    		$items .= '<h4 class="span2">'.$title.'</h4>';
    				
    		$items .= '<div class="featured-clients-items-wrap '.$wrap_span.'">';
			$items .= '<ul class="featured-clients-items row clearfix">';
			
			$client_width = 300;
			$client_height = NULL;
			
			// CLIENTS LOOP
			
			while ( $clients_items->have_posts() ) : $clients_items->the_post();
			
				$client_image = get_post_thumbnail_id();
				$client_img_url = wp_get_attachment_url( $client_image, 'full' );
				$client_link_url = sf_get_post_meta($post->ID, 'sf_client_link', true);
				    				
				$items .= '<li class="clearfix span2">';
				    				
				$items .= '<figure>';
					
				$image = '';
                if (function_exists('aq_resize')) {
                    $image = aq_resize( $client_img_url, $client_width, $client_height, true, false);
                }

				if($image) {
				
					if ($client_link_url) {
					$items .= '<a href="'.$client_link_url.'" target="_blank"><img src="'.$image[0].'" width="'.$image[1].'" height="'.$image[2].'" /></a>';
					} else {
					$items .= '<img src="'.$image[0].'" width="'.$image[1].'" height="'.$image[2].'" />';
					}
					
				}
				
				$items .= '</figure>';
							
			endwhile;
			
			wp_reset_postdata();
					
			$items .= '</ul></div>';
			
			// Full width setup
			$fullwidth = false;
			if ($alt_background != "none" && $sidebars == "no-sidebars") {
			$fullwidth = true;
			}
			
			// PAGE BUILDER OUPUT
    		$el_class = $this->getExtraClass($el_class);
            $width = spb_translateColumnWidthToSpan($width);
            
            $output .= "\n\t".'<div class="spb_featured_clients_widget spb_content_element '.$width.$el_class.'">';
            $output .= "\n\t\t".'<div class="spb_wrapper clients-wrap row">';
            $output .= "\n\t\t\t\t".$items;
            $output .= "\n\t\t".'</div> '.$this->endBlockComment('.spb_wrapper');
            $output .= "\n\t".'</div> '.$this->endBlockComment($width);
    
            $output = $this->startRow($el_position, $width, $fullwidth, "", $alt_background) . $output . $this->endRow($el_position, $width, $fullwidth);
            
            return $output;
		
    }
}

SPBMap::map( 'clients_featured', array(
    "name"		=> __("Clients (Featured)", 'neighborhood'),
    "base"		=> "clients_featured",
    "class"		=> "clients_featured",
    "icon"      => "spb-icon-clients-featured",
    "params"	=> array(
    	array(
    	    "type" => "textfield",
    	    "heading" => __("Widget title", 'neighborhood'),
    	    "param_name" => "title",
    	    "value" => "",
    	    "description" => __("Heading text. Leave it empty if not needed.", 'neighborhood')
    	),
        array(
            "type" => "select-multiple",
            "heading" => __("Clients category", 'neighborhood'),
            "param_name" => "category",
            "value" => get_category_list('clients-category'),
            "description" => __("Choose the category for the client items.", 'neighborhood')
        ),
        array(
            "type" => "dropdown",
            "heading" => __("Show alt background", 'neighborhood'),
            "param_name" => "alt_background",
            "value" => array(__("None", 'neighborhood') => "none", __("Alt 1", 'neighborhood') => "alt-one", __("Alt 2", 'neighborhood') => "alt-two", __("Alt 3", 'neighborhood') => "alt-three", __("Alt 4", 'neighborhood') => "alt-four", __("Alt 5", 'neighborhood') => "alt-five", __("Alt 6", 'neighborhood') => "alt-six", __("Alt 7", 'neighborhood') => "alt-seven", __("Alt 8", 'neighborhood') => "alt-eight", __("Alt 9", 'neighborhood') => "alt-nine", __("Alt 10", 'neighborhood') => "alt-ten"),
            "description" => __("Show an alternative background around the asset. These can all be set in Neighborhood Options > Asset Background Options. NOTE: This is only available on a page with the no sidebar setup.", 'neighborhood')
        ),
        array(
            "type" => "altbg_preview",
            "heading" => __("Alt Background Preview", 'neighborhood'),
            "param_name" => "altbg_preview",
            "value" => "",
            "description" => ""
        ),
        array(
            "type" => "textfield",
            "heading" => __("Extra class name", 'neighborhood'),
            "param_name" => "el_class",
            "value" => "",
            "description" => __("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", 'neighborhood')
        )
    )
) );

?>