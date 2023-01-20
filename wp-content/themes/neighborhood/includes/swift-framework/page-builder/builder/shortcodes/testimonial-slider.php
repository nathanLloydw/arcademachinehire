<?php

class SwiftPageBuilderShortcode_testimonial_slider extends SwiftPageBuilderShortcode {

    public function content( $atts, $content = null ) {

        $title = $order = $text_size = $items = $el_class = $width = $el_position = '';

        extract(shortcode_atts(array(
        	'title' => '',
        	'text_size' => '',
           	'item_count'	=> '',
           	'order'	=> '',
        	'category'		=> 'all',
        	'animation'		=> 'fade',
        	'autoplay'		=> 'yes',
            'el_class' => '',
            'alt_background'	=> 'none',
            'el_position' => '',
            'width' => '1/1'
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
        	'posts_per_page' => $item_count,
        	'no_found_rows' => 1,
        	);
        	    		
        $testimonials = new WP_Query( $testimonials_args );
        
        if ($autoplay == "yes") {
        $items .= '<div class="flexslider testimonials-slider content-slider" data-animation="'.$animation.'" data-autoplay="yes"><ul class="slides">';
        } else {
        $items .= '<div class="flexslider testimonials-slider content-slider" data-animation="'.$animation.'" data-autoplay="no"><ul class="slides">';
        }
                  
        // TESTIMONIAL LOOP
        
        while ( $testimonials->have_posts() ) : $testimonials->the_post();
        	
        	$testimonial_text = get_the_content();
        	$testimonial_cite = sf_get_post_meta($post->ID, 'sf_testimonial_cite', true);
        	
        	$items .= '<li class="testimonial">';
        	$items .= '<div class="testimonial-text text-'.$text_size.'">'.do_shortcode($testimonial_text).'</div>'; 
        	$items .= '<cite>'.$testimonial_cite.'</cite>';
        	$items .= '</li>';
        	        
        endwhile;
        
        wp_reset_postdata();
        		
        $items .= '</ul></div>';
       				        
        $el_class = $this->getExtraClass($el_class);
        $width = spb_translateColumnWidthToSpan($width);
        
        $sidebar_config = sf_get_post_meta(get_the_ID(), 'sf_sidebar_config', true);
        
        $sidebars = '';
        if (($sidebar_config == "left-sidebar") || ($sidebar_config == "right-sidebar")) {
        $sidebars = 'one-sidebar';
        } else if ($sidebar_config == "both-sidebars") {
        $sidebars = 'both-sidebars';
        } else {
        $sidebars = 'no-sidebars';
        }

        $el_class .= ' testimonial';
        
        // Full width setup
        $fullwidth = false;
        if ($alt_background != "none") {
        $fullwidth = true;
        }
        
		$output .= "\n\t".'<div class="spb_testimonial_slider_widget spb_content_element '.$width.$el_class.'">';
        $output .= "\n\t\t".'<div class="spb_wrapper slider-wrap">';
        $output .= ($title != '' ) ? "\n\t\t\t".'<div class="heading-wrap"><h4 class="spb_heading spb_text_heading"><span>'.$title.'</span></h4></div>' : '';
        $output .= "\n\t\t\t".$items;
        $output .= "\n\t\t".'</div> '.$this->endBlockComment('.spb_wrapper');
        $output .= "\n\t".'</div> '.$this->endBlockComment($width);

        $output = $this->startRow($el_position, $width, $fullwidth, false, $alt_background) . $output . $this->endRow($el_position, $width, $fullwidth, false);
        
        global $include_carousel;
        $include_carousel = true;
        
        return $output;
    }
}

SPBMap::map( 'testimonial_slider', array(
    "name"		=> __("Testimonials Slider", 'neighborhood'),
    "base"		=> "testimonial_slider",
    "class"		=> "spb_testimonial_slider spb_slider",
    "icon"      => "spb-icon-testimonial_slider",
    "wrapper_class" => "clearfix",
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
            "description" => __("The number of testimonials to show. Leave blank to show ALL testimonials.", 'neighborhood')
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
            "heading" => __("Slider animation", 'neighborhood'),
            "param_name" => "animation",
            "value" => array(__('Fade', 'neighborhood') => "fade", __('Slide', 'neighborhood') => "slide"),
            "description" => __("Choose the animation for the slider.", 'neighborhood')
        ),
        array(
            "type" => "dropdown",
            "heading" => __("Slider autoplay", 'neighborhood'),
            "param_name" => "autoplay",
            "value" => array(__('Yes', 'neighborhood') => "yes", __('No', 'neighborhood') => "no"),
            "description" => __("Select if you want the slider to autoplay or not.", 'neighborhood')
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