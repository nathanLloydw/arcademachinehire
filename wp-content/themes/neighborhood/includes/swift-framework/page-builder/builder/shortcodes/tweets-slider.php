<?php

class SwiftPageBuilderShortcode_tweets_slider extends SwiftPageBuilderShortcode {

    public function content( $atts, $content = null ) {

        $title = $order = $text_size = $items = $el_class = $width = $el_position = '';

        extract(shortcode_atts(array(
        	'title' => '',
        	'twitter_username' => '',
        	'text_size' => 'normal',
           	'tweets_count'	=> '6',
           	'animation'		=> 'fade',
           	'autoplay'		=> 'yes',
            'el_class' => '',
            'alt_background'	=> 'none',
            'el_position' => '',
            'width' => '1/1'
        ), $atts));

        $output = '';
        
        if ($autoplay == "yes") {
        $items .= '<div class="flexslider tweets-slider content-slider" data-animation="'.$animation.'" data-autoplay="yes"><ul class="slides">';
        } else {
        $items .= '<div class="flexslider tweets-slider content-slider" data-animation="'.$animation.'" data-autoplay="no"><ul class="slides">';
        }
        
       	$items .= sf_get_tweets($twitter_username, $tweets_count);
        		
        $items .= '</ul></div>';
       	       				        
        $el_class = $this->getExtraClass($el_class);
        $width = spb_translateColumnWidthToSpan($width);

        $el_class .= ' testimonial';
        
        // Full width setup
        $fullwidth = false;
        if ($alt_background != "none") {
        $fullwidth = true;
		}
		
        $output .= "\n\t".'<div class="spb_tweets_slider_widget '.$width.$el_class.'">';            
        $output .= "\n\t\t".'<div class="spb_wrapper slider-wrap text-'.$text_size.'">';
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

SPBMap::map( 'tweets_slider', array(
    "name"		=> __("Tweets Slider", 'neighborhood'),
    "base"		=> "tweets_slider",
    "class"		=> "spb-tweets-slider",
    "icon"      => "spb-icon-tweets-slider",
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
            "type" => "textfield",
            "heading" => __("Twitter username", 'neighborhood'),
            "param_name" => "twitter_username",
            "value" => "",
            "description" => __("The twitter username you'd like to show the latest tweet for. Make sure to not include the @.", 'neighborhood')
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
            "heading" => __("Number of tweets", 'neighborhood'),
            "param_name" => "tweets_count",
            "value" => "6",
            "description" => __("The number of tweets to show.", 'neighborhood')
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
            "description" => __("Show an alternative background around the asset. These can all be set in Neighborhood Options > Asset Background Options.", 'neighborhood')
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