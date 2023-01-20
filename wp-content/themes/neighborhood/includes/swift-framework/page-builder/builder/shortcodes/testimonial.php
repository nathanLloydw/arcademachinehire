<?php

class SwiftPageBuilderShortcode_testimonial extends SwiftPageBuilderShortcode {

    public function content( $atts, $content = null ) {

        $title = $order = $page_link = $text_size = $items = $el_class = $width = $el_position = '';

        extract(shortcode_atts(array(
        	'title' => '',
        	'text_size' => '',
           	'item_count'	=> '-1',
           	'order'	=> '',
        	'category'		=> '',
        	'pagination'	=> 'no',
        	'page_link'	=> '',
            'el_class' => '',
            'el_position' => '',
            'width' => '1/2'
        ), $atts));

        $output = '';
        
        // CATEGORY SLUG MODIFICATION
        if ($category == "All") {$category = "all";}
        if ($category == "all") {$category = '';}
        $category_slug = str_replace('_', '-', $category);
        
        
        // TESTIMONIAL QUERY SETUP
        
        global $post, $wp_query;
        
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
            		
        $testimonials_args = array(
        	'orderby' => $order,
        	'post_type' => 'testimonials',
        	'post_status' => 'publish',
        	'paged' => $paged,
        	'testimonials-category' => $category_slug,
        	'posts_per_page' => $item_count
        	);
        	    		
        $testimonials = new WP_Query( $testimonials_args );
                
        $items .= '<ul class="testimonials clearfix">';
        
        // TESTIMONIAL LOOP
        
        while ( $testimonials->have_posts() ) : $testimonials->the_post();
        	
        	$testimonial_text = get_the_content_with_formatting();
        	$testimonial_cite = sf_get_post_meta($post->ID, 'sf_testimonial_cite', true);
        	
        	$items .= '<li class="testimonial">';
        	$items .= '<div class="testimonial-text">'.do_shortcode($testimonial_text).'</div>'; 
        	$items .= '<cite>'.$testimonial_cite.'</cite>';
        	$items .= '</li>';
        	        
        endwhile;
        
        wp_reset_postdata();
        		
        $items .= '</ul>';
       
   		if ($page_link == "yes") {
	        $options = get_option('sf_neighborhood_options');
	        $testimonials_page = __($options['testimonial_page'], 'neighborhood');
	        if ($testimonials_page) {
	        $testimonials_page_title = get_page_by_path( $testimonials_page );
	        	if (isset($testimonials_page_title)) {
	        		$testimonials_page_id = $testimonials_page_title->ID;   
	        	}
        	}
        
			if ($testimonials_page && isset($testimonials_page_title)) {
				$items .= '<a href="'.get_permalink($testimonials_page_id).'" class="read-more">'.__("More", 'neighborhood').'<i class="fas fa-angle-right"></i></a>';
			}
		}
        
        // PAGINATION
        
        if ($pagination == "yes") {
        
        $items .= '<div class="pagination-wrap">';
        
        if($testimonials->max_num_pages>1){
        	
        	$items .= '<ul>';
        
            for($i=1;$i<=$testimonials->max_num_pages;$i++) {
            	if ($i == $paged) {
            		$items .= '<li><span>'.$i.'</span></li>';
            	} else {
             		$items .= '<li><a href="'. get_permalink() .'page/'.$i.'">'.$i.'</a></li>';
            	}
            }
            
            $items .= '</ul>';
            
        }
        					
        $items .= '</div>';
        
        }       

        $el_class = $this->getExtraClass($el_class);
        $width = spb_translateColumnWidthToSpan($width);

        $el_class .= ' testimonial';

        $output .= "\n\t".'<div class="spb_content_element '.$width.$el_class.'">';
        $output .= "\n\t\t".'<div class="spb_wrapper testimonial-wrap '.$text_size.'">';
        $output .= ($title != '' ) ? "\n\t\t\t".'<h4 class="spb_heading spb_text_heading"><span>'.$title.'</span></h4>' : '';
        $output .= "\n\t\t\t". $items;
        $output .= "\n\t\t".'</div> ' . $this->endBlockComment('.spb_wrapper');
        $output .= "\n\t".'</div> ' . $this->endBlockComment($width);

        $output = $this->startRow($el_position) . $output . $this->endRow($el_position);
        return $output;
    }
}

SPBMap::map( 'testimonial', array(
    "name"		=> __("Testimonials", 'neighborhood'),
    "base"		=> "testimonial",
    "class"		=> "",
    "icon"      => "spb-icon-testimonial",
    "wrapper_class" => "clearfix",
    "controls"	=> "full",
    "params"	=> array(
    	array(
    	    "type" => "textfield",
    	    "heading" => __("Widget title", 'neighborhood'),
    	    "param_name" => "title",
    	    "value" => "",
    	    "description" => __("Heading text. Leave it empty if not needed.", 'neighborhood')
    	),
        array(
            "type" => "dropdown",
            "heading" => __("Text size", 'neighborhood'),
            "param_name" => "text_size",
            "value" => array(__('Normal', 'neighborhood') => "normal", __('Large', 'neighborhood') => "large"),
            "description" => __("Choose the size of the text.", 'neighborhood')
        ),
        array(
            "type" => "textfield",
            "class" => "",
            "heading" => __("Number of items", 'neighborhood'),
            "param_name" => "item_count",
            "value" => "6",
            "description" => __("The number of testimonials to show per page. Leave blank to show ALL testimonials.", 'neighborhood')
        ),
        array(
            "type" => "dropdown",
            "heading" => __("Testimonials Order", 'neighborhood'),
            "param_name" => "order",
            "value" => array(__('Random', 'neighborhood') => "rand", __('Latest', 'neighborhood') => "date"),
            "description" => __("Choose the order of the testimonials.", 'neighborhood')
        ),
        array(
            "type" => "select-multiple",
            "heading" => __("Testimonials category", 'neighborhood'),
            "param_name" => "category",
            "value" => get_category_list('testimonials-category'),
            "description" => __("Choose the category for the testimonials.", 'neighborhood')
        ),
        array(
            "type" => "dropdown",
            "heading" => __("Pagination", 'neighborhood'),
            "param_name" => "pagination",
            "value" => array(__('No', 'neighborhood') => "no", __('Yes', 'neighborhood') => "yes"),
            "description" => __("Show testimonial pagination (1/1 width element only).", 'neighborhood')
        ),
        array(
            "type" => "dropdown",
            "heading" => __("Testimonials page link", 'neighborhood'),
            "param_name" => "page_link",
            "value" => array(
                    __("No", 'neighborhood') => "no",
                    __("Yes", 'neighborhood') => "yes"
            ),
            "description" => __("Include a link to the testimonials page (which you must choose in the theme options).", 'neighborhood')
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