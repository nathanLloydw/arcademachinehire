<?php

class SwiftPageBuilderShortcode_codesnippet extends SwiftPageBuilderShortcode {

    public function content( $atts, $content = null ) {

        $title = $pb_margin_bottom = $el_class = $width = $el_position = '';

        extract(shortcode_atts(array(
        	'title' => '',
        	'pb_margin_bottom'	=> '',
            'el_class' => '',
            'el_position' => '',
            'width' => '1'
        ), $atts));

        $output = '';

        $el_class = $this->getExtraClass($el_class);
        $width = spb_translateColumnWidthToSpan($width);
                
        if ($pb_margin_bottom == "yes") {
        $el_class .= ' pb-margin-bottom';
        }

        $output .= "\n\t".'<div class="spb_codesnippet_element '.$width.$el_class.'">';
        $output .= "\n\t\t".'<div class="spb_wrapper">';
        $output .= ($title != '' ) ? "\n\t\t\t".'<h4 class="spb_heading spb_codesnippet_heading"><span>'.$title.'</span></h4>' : '';
        $output .= "\n\t\t\t<code>".spb_js_remove_wpautop($content)."</code>";
        $output .= "\n\t\t".'</div> ' . $this->endBlockComment('.spb_wrapper');
        $output .= "\n\t".'</div> ' . $this->endBlockComment($width);

        //
        $output = $this->startRow($el_position) . $output . $this->endRow($el_position);
        return $output;
    }
}

SPBMap::map( 'codesnippet', array(
    "name"		=> __("Code Snippet", 'neighborhood'),
    "base"		=> "codesnippet",
    "class"		=> "spb_codesnippet",
    "icon"      => "spb-icon-code-snippet",
    "params"	=> array(
	    array(
	        "type" => "textfield",
	        "heading" => __("Widget title", 'neighborhood'),
	        "param_name" => "title",
	        "value" => "",
	        "description" => __("Heading text. Leave it empty if not needed.", 'neighborhood')
	    ),
	    array(
	        "type" => "textarea_html",
	        "holder" => "div",
	        "class" => "",
	        "heading" => __("Text", 'neighborhood'),
	        "param_name" => "content",
	        "value" => __("<p>Add your code snippet here.</p>", 'neighborhood'),
	        "description" => __("Enter your code snippet.", 'neighborhood')
	    ),
	    array(
	        "type" => "dropdown",
	        "heading" => __("Margin below widget", 'neighborhood'),
	        "param_name" => "pb_margin_bottom",
	        "value" => array(__('Yes', 'neighborhood') => "yes", __('No', 'neighborhood') => "no"),
	        "description" => __("Add a bottom margin to the widget.", 'neighborhood')
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