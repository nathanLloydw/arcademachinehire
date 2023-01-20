<?php
	/*
	*
	*	Swift Page Builder - Button Shortcodes
	*	------------------------------------------------
	*	Swift Framework
	* 	Copyright Swift Ideas 2013 - http://www.swiftideas.net
	*
	*/

	/* IMPACT TEXT ASSET
	================================================== */ 
	class SwiftPageBuilderShortcode_impact_text extends SwiftPageBuilderShortcode {
	
	    protected function content( $atts, $content = null ) {
	        $color = $type = $size = $target = $href = $border_top = $include_button = $button_style = $border_bottom = $title = $width = $position = $el_class = '';
	        extract(shortcode_atts(array(
	            'color' => 'btn',
	            'include_button' => '',
	            'button_style' => '',
	            'size' => '',
	            'target' => '',
	            'type'	=> '',
	            'href' => '',
	            'shadow'		=> 'yes',
	            'title' => __('Text on the button', 'neighborhood'),
	            'position' => 'cta_align_right',
	            'alt_background'	=> 'none',
	            'width' => '1/1',
	            'el_class' => '',
	            'el_position' => '',
	        ), $atts));
	        $output = '';
	        
	        $border_class = '';
	        
	        if ($border_top == "yes") {
	        $border_class .= 'border-top ';
	        }
	        if ($border_bottom == "yes") {
	        $border_class .= 'border-bottom';
	        }
	
			$width = spb_translateColumnWidthToSpan($width);
	        $el_class = $this->getExtraClass($el_class);
	        
	        $sidebar_config = sf_get_post_meta(get_the_ID(), 'sf_sidebar_config', true);
	        
	        $sidebars = '';
	        if (($sidebar_config == "left-sidebar") || ($sidebar_config == "right-sidebar")) {
	        $sidebars = 'one-sidebar';
	        } else if ($sidebar_config == "both-sidebars") {
	        $sidebars = 'both-sidebars';
	        } else {
	        $sidebars = 'no-sidebars';
	        }
	
	        if ( $target == 'same' || $target == '_self' ) { $target = '_self'; }
	        if ( $target != '' ) { $target = $target; }
	
	        $size = ( $size != '' ) ? ' '.$size : '';
	
	        $a_class = '';
	        if ( $el_class != '' ) {
	            $tmp_class = explode(" ", $el_class);
	        }
	        
	        $button = '';
	        
	        if (($type == "squarearrow") || ($type == "slightlyroundedarrow") || ($type == "roundedarrow")) {
	        	$button = '<a class="spb_button sf-button'.$size.' '. $color .' '. $type .'" href="'.$href.'" target="'.$target.'"><span>' . $title . '</span><span class="arrow"></span></a>';
	        } else {
	        	$button = '<a class="spb_button sf-button'.$size.' '. $color .' '. $type .'" href="'.$href.'" target="'.$target.'"><span>' . $title . '</span></a>';
	        }
	        
	        if ($button_style == "arrow") {
	        
		        if ($position == "cta_align_left") {
		        	$button = '<a class="impact-text-arrow arrow-left" href="'.$href.'" target="'.$target.'"><i class="fas fa-angle-left"></i></a>';
		        } else { 
		        	$button = '<a class="impact-text-arrow arrow-right" href="'.$href.'" target="'.$target.'"><i class="fas fa-angle-right"></i></a>';
		        }
	        
	        }
	        
	        // Full width setup
	        $fullwidth = false;
	        if ($alt_background != "none" && $sidebars == "no-sidebars") {
	        $fullwidth = true;
	        }
	        
	        $output .= '<div class="spb_impact_text spb_content_element clearfix '.$width.' '.$position.$el_class.'">'. "\n";
	        $output .= '<div class="impact-text-wrap clearfix">'. "\n";
	        $output .= '<div class="spb_call_text">'. spb_js_remove_wpautop($content) . '</div>'. "\n";
	        if ($include_button == "yes") {
	        $output .= $button. "\n";
	        }
	        $output .= '</div>'. "\n";
	        $output .= '</div> ' . $this->endBlockComment('.spb_impact_text') . "\n";
			
			$output = $this->startRow($el_position, $width, $fullwidth, false, $alt_background) . $output . $this->endRow($el_position, $width, $fullwidth, false);
			
	        return $output;
	    }
	}
	
	$colors_arr = array(__("Accent", 'neighborhood') => "accent", __("Blue", 'neighborhood') => "blue", __("Grey", 'neighborhood') => "grey", __("Light grey", 'neighborhood') => "lightgrey", __("Purple", 'neighborhood') => "purple", __("Light Blue", 'neighborhood') => "lightblue", __("Green", 'neighborhood') => "green", __("Lime Green", 'neighborhood') => "limegreen", __("Turquoise", 'neighborhood') => "turquoise", __("Pink", 'neighborhood') => "pink", __("Orange", 'neighborhood') => "orange");
	
	$size_arr = array(__("Normal", 'neighborhood') => "normal", __("Large", 'neighborhood') => "large");
	
	$type_arr = array(__("Standard", 'neighborhood') => "standard", __("Square with arrow", 'neighborhood') => "squarearrow", __("Slightly rounded", 'neighborhood') => "slightlyrounded", __("Slightly rounded with arrow", 'neighborhood') => "slightlyroundedarrow", __("Rounded", 'neighborhood') => "rounded", __("Rounded with arrow", 'neighborhood') => "roundedarrow", __("Outer glow effect", 'neighborhood') => "outerglow", __("Drop shadow effect", 'neighborhood') => "dropshadow");
	
	
	$target_arr = array(__("Same window", 'neighborhood') => "_self", __("New window", 'neighborhood') => "_blank");
	
	SPBMap::map( 'impact_text', array(
	    "name"		=> __("Impact Text + Button", 'neighborhood'),
	    "base"		=> "impact_text",
	    "class"		=> "button_grey",
		"icon"		=> "spb-icon-impact-text",
	    "controls"	=> "edit_popup_delete",
	    "params"	=> array(
	    	array(
	    	    "type" => "dropdown",
	    	    "heading" => __("Include button", 'neighborhood'),
	    	    "param_name" => "include_button",
	    	    "value" => array(__("Yes", 'neighborhood') => "yes", __("No", 'neighborhood') => "no"),
	    	    "description" => __("Include a button in the asset.", 'neighborhood')
	    	),
	    	array(
	    	    "type" => "dropdown",
	    	    "heading" => __("Button Style", 'neighborhood'),
	    	    "param_name" => "button_style",
	    	    "value" => array(__("Standard", 'neighborhood') => "standard", __("Arrow", 'neighborhood') => "arrow"),
	    	),
	        array(
	            "type" => "textfield",
	            "heading" => __("Text on the button", 'neighborhood'),
	            "param_name" => "title",
	            "value" => __("Text on the button", 'neighborhood'),
	            "description" => __("Text on the button.", 'neighborhood')
	        ),
	        array(
	            "type" => "textfield",
	            "heading" => __("URL (Link)", 'neighborhood'),
	            "param_name" => "href",
	            "value" => "",
	            "description" => __("Button link.", 'neighborhood')
	        ),
	        array(
	            "type" => "dropdown",
	            "heading" => __("Color", 'neighborhood'),
	            "param_name" => "color",
	            "value" => $colors_arr,
	            "description" => __("Button color.", 'neighborhood')
	        ),
	        array(
	            "type" => "dropdown",
	            "heading" => __("Size", 'neighborhood'),
	            "param_name" => "size",
	            "value" => $size_arr,
	            "description" => __("Button size.", 'neighborhood')
	        ),
	        array(
	            "type" => "dropdown",
	            "heading" => __("Type", 'neighborhood'),
	            "param_name" => "type",
	            "value" => $type_arr
	        ),
	        array(
	            "type" => "dropdown",
	            "heading" => __("Target", 'neighborhood'),
	            "param_name" => "target",
	            "value" => $target_arr
	        ),
	        array(
	            "type" => "dropdown",
	            "heading" => __("Button position", 'neighborhood'),
	            "param_name" => "position",
	            "value" => array(__("Align right", 'neighborhood') => "cta_align_right", __("Align left", 'neighborhood') => "cta_align_left", __("Align bottom", 'neighborhood') => "cta_align_bottom"),
	            "description" => __("Select button alignment.", 'neighborhood')
	        ),
	        array(
	            "type" => "textarea_html",
	            "holder" => "div",
	            "class" => "",
	            "heading" => __("Text", 'neighborhood'),
	            "param_name" => "content",
	            "value" => __("click the edit button to change this text.", 'neighborhood'),
	            "description" => __("Enter your content.", 'neighborhood')
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
	    ),
	    "js_callback" => array("init" => "spbCallToActionInitCallBack", "save" => "spbCallToActionSaveCallBack")
	) );	
?>